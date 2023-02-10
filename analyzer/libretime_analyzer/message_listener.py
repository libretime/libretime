import json
import signal
import time
from queue import Queue

import pika
from loguru import logger

from .config import Config
from .pipeline import Pipeline, PipelineOptions, PipelineStatus
from .status_reporter import StatusReporter

EXCHANGE = "airtime-uploads"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = ""
QUEUE = "airtime-uploads"


class MessageListener:
    def __init__(self, config: Config):
        """
        Start listening for file upload event messages from RabbitMQ.
        """

        self.config = config
        self._shutdown = False

        # Set up a signal handler so we can shutdown gracefully
        # For some reason, this signal handler must be set up here. I'd rather
        # put it in AirtimeAnalyzerServer, but it doesn't work there (something to do
        # with pika's SIGTERM handler interfering with it, I think...)
        signal.signal(signal.SIGTERM, self.graceful_shutdown)

        while not self._shutdown:
            try:
                self.connect_to_messaging_server()
                self.wait_for_messages()
            except (KeyboardInterrupt, SystemExit):
                break  # Break out of the while loop and exit the application
            except OSError:
                pass
            except pika.exceptions.AMQPError as exception:
                if self._shutdown:
                    break
                logger.error("Connection to message queue failed. ")
                logger.error(exception)
                logger.info("Retrying in 5 seconds...")
                time.sleep(5)

        self.disconnect_from_messaging_server()
        logger.info("Exiting cleanly.")

    def connect_to_messaging_server(self):
        """Connect to the RabbitMQ server and start listening for messages."""
        self._connection = pika.BlockingConnection(
            pika.ConnectionParameters(
                host=self.config.rabbitmq.host,
                port=self.config.rabbitmq.port,
                virtual_host=self.config.rabbitmq.vhost,
                credentials=pika.credentials.PlainCredentials(
                    self.config.rabbitmq.user,
                    self.config.rabbitmq.password,
                ),
            )
        )
        self._channel = self._connection.channel()
        self._channel.exchange_declare(
            exchange=EXCHANGE, exchange_type=EXCHANGE_TYPE, durable=True
        )
        self._channel.queue_declare(queue=QUEUE, durable=True)

        self._channel.queue_bind(
            exchange=EXCHANGE, queue=QUEUE, routing_key=ROUTING_KEY
        )

        logger.info(" Listening for messages...")
        self._channel.basic_consume(QUEUE, self.msg_received_callback, auto_ack=False)

    def wait_for_messages(self):
        """Wait until we've received a RabbitMQ message."""
        self._channel.start_consuming()

    def disconnect_from_messaging_server(self):
        """Stop consuming RabbitMQ messages and disconnect"""
        if not self._channel.is_closed:
            self._channel.stop_consuming()
        if not self._connection.is_closed:
            self._connection.close()

    def graceful_shutdown(self, signum, frame):
        """Disconnect and break out of the message listening loop"""
        self._shutdown = True
        self.disconnect_from_messaging_server()

    def msg_received_callback(self, channel, method_frame, header_frame, body):
        """A callback method that runs when a RabbitMQ message is received.

        Here we parse the message, spin up an analyzer process, and report the
        metadata back to the Airtime web application (or report an error).
        """
        logger.info(f" - Received '{body}' on routing_key '{method_frame.routing_key}'")

        audio_file_path = ""
        # final_file_path = ""
        import_directory = ""
        original_filename = ""
        file_id = ""

        try:
            try:
                body = body.decode()
            except (UnicodeDecodeError, AttributeError):
                pass
            msg_dict: dict = json.loads(body)

            file_id = msg_dict["file_id"]
            audio_file_path = msg_dict["tmp_file_path"]
            original_filename = msg_dict["original_filename"]
            import_directory = msg_dict["import_directory"]
            options = msg_dict.get("options", {})

            metadata = MessageListener.spawn_analyzer_process(
                audio_file_path,
                import_directory,
                original_filename,
                options,
            )

            callback_url = f"{self.config.general.public_url}/rest/media/{file_id}"
            callback_api_key = self.config.general.api_key

            StatusReporter.report_success(callback_url, callback_api_key, metadata)

        except KeyError:
            logger.exception("A mandatory field was missing from the message.")
            channel.basic_nack(
                delivery_tag=method_frame.delivery_tag,
                multiple=False,
                requeue=False,
            )

        except Exception as exception:
            logger.exception(exception)
            channel.basic_nack(
                delivery_tag=method_frame.delivery_tag,
                multiple=False,
                requeue=False,
            )

            if file_id:
                StatusReporter.report_failure(
                    callback_url,
                    callback_api_key,
                    import_status=2,
                    reason="An error occurred while importing this file",
                )

        else:
            channel.basic_ack(delivery_tag=method_frame.delivery_tag)

    @staticmethod
    def spawn_analyzer_process(
        audio_file_path,
        import_directory,
        original_filename,
        options: dict,
    ):
        metadata = {}

        queue = Queue()
        try:
            Pipeline.run_analysis(
                queue,
                audio_file_path,
                import_directory,
                original_filename,
                PipelineOptions(**options),
            )
            metadata = queue.get()
        except Exception as exception:
            logger.exception(f"Analyzer pipeline exception: {exception}")
            metadata["import_status"] = PipelineStatus.FAILED

        # Ensure our queue doesn't fill up and block due to unexpected behavior. Defensive code.
        while not queue.empty():
            queue.get()

        return metadata
