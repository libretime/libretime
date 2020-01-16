# -*- coding: utf-8 -*-

import logging
import traceback
import os
import sys
from threading import Thread
import time
# For RabbitMQ
from kombu.connection import Connection
from kombu.messaging import Exchange, Queue
from kombu.simple import SimpleQueue
from amqp.exceptions import AMQPError
import json

from kombu.mixins import ConsumerMixin

logging.captureWarnings(True)


class RabbitConsumer(ConsumerMixin):
    def __init__(self, connection, queues, handler):
        self.connection = connection
        self.queues = queues
        self.handler = handler

    def get_consumers(self, Consumer, channel):
        return [
            Consumer(self.queues, callbacks=[self.on_message], accept=['text/plain']),
        ]

    def on_message(self, body, message):
        self.handler.handle_message(message.payload)
        message.ack()

class PypoMessageHandler(Thread):
    def __init__(self, pq, rq, config):
        Thread.__init__(self)
        self.logger = logging.getLogger('message_h')
        self.pypo_queue = pq
        self.recorder_queue = rq
        self.config = config

    def init_rabbit_mq(self):
        self.logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            with Connection(self.config["host"], \
                            self.config["user"], \
                            self.config["password"], \
                            self.config["vhost"], \
                            heartbeat = 5) as connection:
                rabbit = RabbitConsumer(connection, [schedule_queue], self)
                rabbit.run()
        except Exception as e:
            self.logger.error(e)

    """
    Handle a message from RabbitMQ, put it into our yucky global var.
    Hopefully there is a better way to do this.
    """
    def handle_message(self, message):
        try:
            self.logger.info("Received event from RabbitMQ: %s" % message)

            m = json.loads(message)
            command = m['event_type']
            self.logger.info("Handling command: " + command)

            if command == 'update_schedule':
                self.logger.info("Updating schdule...")
                self.pypo_queue.put(message)
            elif command == 'reset_liquidsoap_bootstrap':
                self.logger.info("Resetting bootstrap vars...")
                self.pypo_queue.put(message)
            elif command == 'update_stream_setting':
                self.logger.info("Updating stream setting...")
                self.pypo_queue.put(message)
            elif command == 'update_stream_format':
                self.logger.info("Updating stream format...")
                self.pypo_queue.put(message)
            elif command == 'update_station_name':
                self.logger.info("Updating station name...")
                self.pypo_queue.put(message)
            elif command == 'switch_source':
                self.logger.info("switch_source command received...")
                self.pypo_queue.put(message)
            elif command == 'update_transition_fade':
                self.logger.info("Updating trasition fade...")
                self.pypo_queue.put(message)
            elif command == 'disconnect_source':
                self.logger.info("disconnect_source command received...")
                self.pypo_queue.put(message)
            elif command == 'update_recorder_schedule':
                self.recorder_queue.put(message)
            elif command == 'cancel_recording':
                self.recorder_queue.put(message)
            else:
                self.logger.info("Unknown command: %s" % command)
        except Exception as e:
            self.logger.error("Exception in handling RabbitMQ message: %s", e)

    def main(self):
        try:
            self.init_rabbit_mq()
        except Exception as e:
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", traceback.format_exc())
        self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
        time.sleep(5)

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        while True:
            self.main()

