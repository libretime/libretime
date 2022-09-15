import json
from queue import Queue as ThreadQueue
from signal import SIGTERM, signal
from time import sleep

# For RabbitMQ
from kombu.connection import Connection
from kombu.messaging import Exchange, Queue
from kombu.mixins import ConsumerMixin
from loguru import logger

from .config import Config


class MessageHandler(ConsumerMixin):
    def __init__(
        self,
        connection: Connection,
        fetch_queue: ThreadQueue,
        recorder_queue: ThreadQueue,
    ):
        self.connection = connection

        self.fetch_queue = fetch_queue
        self.recorder_queue = recorder_queue

    def get_consumers(self, Consumer, channel):
        exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
        queues = [Queue("pypo-fetch", exchange=exchange, key="foo")]

        return [
            Consumer(queues, callbacks=[self.on_message], accept=["text/plain"]),
        ]

    def on_message(self, body, message):
        logger.debug(f"received message: {body}")
        try:
            try:
                body = body.decode()
            except (UnicodeDecodeError, AttributeError):
                pass

            payload = json.loads(body)
            command = payload["event_type"]
            logger.info(f"handling command: {command}")

            if command in (
                "update_schedule",
                "reset_liquidsoap_bootstrap",
                "update_stream_format",
                "update_message_offline",
                "update_station_name",
                "switch_source",
                "update_transition_fade",
                "disconnect_source",
            ):
                self.fetch_queue.put(message.payload)

            elif command in (
                "update_recorder_schedule",
                "cancel_recording",
            ):
                self.recorder_queue.put(message.payload)

            else:
                logger.warning(f"invalid command: {command}")

        except Exception as exception:
            logger.exception(exception)

        message.ack()


class MessageListener:
    def __init__(
        self,
        config: Config,
        fetch_queue: ThreadQueue,
        recorder_queue: ThreadQueue,
    ) -> None:
        self.config = config

        self.fetch_queue = fetch_queue
        self.recorder_queue = recorder_queue

    def run_forever(self):
        while True:
            with Connection(
                self.config.rabbitmq.url,
                heartbeat=5,
                transport_options={"client_properties": {"connection_name": "playout"}},
            ) as connection:
                handler = MessageHandler(
                    connection=connection,
                    fetch_queue=self.fetch_queue,
                    recorder_queue=self.recorder_queue,
                )

                def shutdown(_signum, _frame):
                    raise SystemExit()

                signal(SIGTERM, shutdown)

                logger.info("starting message handler")
                handler.run()

            sleep(5)
