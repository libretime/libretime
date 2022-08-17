import json
import time
from queue import Queue as ThreadQueue
from threading import Thread

# For RabbitMQ
from kombu.connection import Connection
from kombu.messaging import Exchange, Queue
from kombu.mixins import ConsumerMixin
from libretime_shared.config import RabbitMQConfig
from loguru import logger


class RabbitConsumer(ConsumerMixin):
    def __init__(self, connection, queues, handler):
        self.connection = connection
        self.queues = queues
        self.handler = handler

    def get_consumers(self, Consumer, channel):
        return [
            Consumer(self.queues, callbacks=[self.on_message], accept=["text/plain"]),
        ]

    def on_message(self, body, message):
        self.handler.handle_message(message.payload)
        message.ack()


class PypoMessageHandler(Thread):
    name = "message_handler"

    def __init__(
        self,
        fetch_queue: ThreadQueue,
        recorder_queue: ThreadQueue,
        config: RabbitMQConfig,
    ):
        Thread.__init__(self)
        self.pypo_queue = fetch_queue
        self.recorder_queue = recorder_queue
        self.config = config

    def init_rabbit_mq(self):
        logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange(
                "airtime-pypo", "direct", durable=True, auto_delete=True
            )
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            with Connection(
                f"amqp://{self.config.user}:{self.config.password}"
                f"@{self.config.host}:{self.config.port}"
                f"/{self.config.vhost}",
                heartbeat=5,
            ) as connection:
                rabbit = RabbitConsumer(connection, [schedule_queue], self)
                rabbit.run()
        except Exception as exception:
            logger.exception(exception)

    # Handle a message from RabbitMQ, put it into our yucky global var.
    # Hopefully there is a better way to do this.

    def handle_message(self, message):
        try:
            logger.info("Received event from RabbitMQ: %s" % message)

            try:
                message = message.decode()
            except (UnicodeDecodeError, AttributeError):
                pass
            m = json.loads(message)
            command = m["event_type"]
            logger.info("Handling command: " + command)

            if command == "update_schedule":
                logger.info("Updating schedule...")
                self.pypo_queue.put(message)
            elif command == "reset_liquidsoap_bootstrap":
                logger.info("Resetting bootstrap vars...")
                self.pypo_queue.put(message)
            elif command == "update_stream_setting":
                logger.info("Updating stream setting...")
                self.pypo_queue.put(message)
            elif command == "update_stream_format":
                logger.info("Updating stream format...")
                self.pypo_queue.put(message)
            elif command == "update_message_offline":
                logger.info("Updating message offline...")
                self.pypo_queue.put(message)
            elif command == "update_station_name":
                logger.info("Updating station name...")
                self.pypo_queue.put(message)
            elif command == "switch_source":
                logger.info("switch_source command received...")
                self.pypo_queue.put(message)
            elif command == "update_transition_fade":
                logger.info("Updating trasition fade...")
                self.pypo_queue.put(message)
            elif command == "disconnect_source":
                logger.info("disconnect_source command received...")
                self.pypo_queue.put(message)
            elif command == "update_recorder_schedule":
                self.recorder_queue.put(message)
            elif command == "cancel_recording":
                self.recorder_queue.put(message)
            else:
                logger.info("Unknown command: %s" % command)
        except Exception as exception:
            logger.exception(exception)

    def main(self):
        try:
            self.init_rabbit_mq()
        except Exception as exception:
            logger.exception(exception)
        logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
        time.sleep(5)

    # Main loop of the thread:
    # Wait for schedule updates from RabbitMQ, but in case there aren't any,
    # poll the server to get the upcoming schedule.

    def run(self):
        while True:
            self.main()
