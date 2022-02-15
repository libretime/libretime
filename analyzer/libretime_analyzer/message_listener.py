import json
import signal
import time
from json import JSONDecodeError
from pathlib import Path
from threading import Event as ThreadEvent

from libretime_shared.config import RabbitMQConfig
from loguru import logger
from pika import BlockingConnection, URLParameters
from pika.channel import Channel
from pika.exceptions import (
    AMQPChannelError,
    AMQPConnectionError,
    ConnectionClosedByBroker,
)
from pydantic import ValidationError

from libretime_analyzer.pipeline.context import Context

from .pipeline import PipelineError, Status, run_pipeline
from .status_reporter import StatusReporter

EXCHANGE = "airtime-uploads"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = ""
QUEUE = "airtime-uploads"


class MessageListener:
    config: RabbitMQConfig
    stop: ThreadEvent

    _connection: BlockingConnection
    _channel: Channel

    def __init__(self, config: RabbitMQConfig, stop: ThreadEvent):
        """
        Start listening for file upload event messages from RabbitMQ.
        """
        self.config = config
        self.stop = stop

        while not self.stop.is_set():
            try:
                host = URLParameters(self.config.url)
                self._connection = BlockingConnection(host)
                self._channel = self._connection.channel()

                self._channel.exchange_declare(
                    exchange=EXCHANGE,
                    exchange_type=EXCHANGE_TYPE,
                    durable=True,
                )
                self._channel.queue_declare(
                    queue=QUEUE,
                    durable=True,
                )
                self._channel.queue_bind(
                    exchange=EXCHANGE,
                    queue=QUEUE,
                    routing_key=ROUTING_KEY,
                )

                logger.info("Listening for messages...")
                self._channel.basic_consume(
                    QUEUE,
                    self.handle_message,
                    auto_ack=False,
                )
                self._channel.start_consuming()

            except (KeyboardInterrupt, SystemExit):
                break  # Break out of the while loop and exit the application
            except OSError:
                pass
            except AMQPError as e:
                if self._shutdown:
                    break
                logger.error("Connection to message queue failed. ")
                logger.error(e)
                logger.info("Retrying in 5 seconds...")
                time.sleep(5)

        if not self._channel.is_closed:
            self._channel.stop_consuming()
        if not self._connection.is_closed:
            self._connection.close()
        logger.info("Exiting cleanly.")

    def handle_message(self, channel, method_frame, header_frame, body):
        """
        A callback method that runs when a RabbitMQ message is received.
        """
        logger.debug(f"received {body!r}")

        # Handle incoming message
        try:
            message = json.loads(body)
            ctx = Context(
                filepath=Path(message["tmp_file_path"]),
                original_filename=message["original_filename"],
                storage_url=message["import_directory"],
                callback_api_key=message["api_key"],
                callback_url=message["callback_url"],
            )
        except (
            JSONDecodeError,
            ValidationError,
            KeyError,
        ) as exception:  # Incoming message is not properly formed
            logger.exception(exception)
            channel.basic_nack(
                delivery_tag=method_frame.delivery_tag,
                multiple=False,
                requeue=False,
            )

        # Run the pipeline
        try:
            ctx = run_pipeline(ctx)
        except PipelineError as exception:  # An error in the pipeline should be fixed.
            logger.exception(exception)
            channel.basic_nack(
                delivery_tag=method_frame.delivery_tag,
                multiple=False,
                requeue=False,
            )
            return

        if ctx.status == Status.FAILED:
            if ctx.callback_url:
                StatusReporter.report_failure_to_callback_url(
                    ctx.callback_url,
                    ctx.callback_api_key,
                    import_status=Status.FAILED,
                    reason="An error occurred while importing this file",
                )
            channel.basic_nack(
                delivery_tag=method_frame.delivery_tag,
                multiple=False,
                requeue=False,
            )
            return

        StatusReporter.report_success_to_callback_url(
            ctx.callback_url,
            ctx.callback_api_key,
            ctx.metadata,
        )

        channel.basic_ack(delivery_tag=method_frame.delivery_tag)
