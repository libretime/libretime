import json
import logging
from queue import Queue as ThreadQueue
from signal import SIGTERM, signal
from time import sleep
from typing import Any, Dict

# For RabbitMQ
from kombu.connection import Connection
from kombu.message import Message
from kombu.messaging import Exchange, Queue
from kombu.mixins import ConsumerMixin

from .config import Config

logger = logging.getLogger(__name__)


class MessageHandler(ConsumerMixin):
    def __init__(
        self,
        connection: Connection,
        fetch_queue: "ThreadQueue[Dict[str, Any]]",
        recorder_queue: ThreadQueue[Dict[str, Any]],
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

    def on_message(self, body, message: Message) -> None:
        logger.debug("received message: %s", body)
        try:
            try:
                body = body.decode()
            except (UnicodeDecodeError, AttributeError):
                pass

            payload: dict = json.loads(body)
            command = payload["event_type"]
            logger.info("handling event %s: %s", command, payload)

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
                self.fetch_queue.put(payload)

            elif command in (
                "update_recorder_schedule",
                "cancel_recording",
            ):
                self.recorder_queue.put(payload)                

            else:
                logger.warning("invalid command: %s", command)

        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)

        message.ack()


# pylint: disable=too-few-public-methods
class MessageListener:
    def __init__(
        self,
        config: Config,
        fetch_queue: "ThreadQueue[Dict[str, Any]]",
        recorder_queue: "ThreadQueue[Dict[str, Any]]",
    ) -> None:
        self.config = config
        self.fetch_queue = fetch_queue
        self.recorder_queue = recorder_queue

    def run_forever(self) -> None:
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