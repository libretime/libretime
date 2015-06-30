# -*- coding: utf-8 -*-

import logging
import traceback
import os
import sys
from threading import Thread
import time
# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue
from kombu.simple import SimpleQueue
from amqp.exceptions import AMQPError
import json

from std_err_override import LogWriter

#need to wait for Python 2.7 for this..
#logging.captureWarnings(True)


class PypoMessageHandler(Thread):
    def __init__(self, pq, rq, config):
        Thread.__init__(self)
        self.logger = logging.getLogger('message_h')
        self.pypo_queue = pq
        self.recorder_queue = rq
        self.config = config

    def init_rabbit_mq(self):
        self.logger.info("Initializing RabbitMQ stuff")
        simple_queue = None
        try:
            schedule_exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            connection = BrokerConnection(self.config["host"],
                                          self.config["user"],
                                          self.config["password"],
                                          self.config["vhost"])

            channel = connection.channel()
            simple_queue = SimpleQueue(channel, schedule_queue)
        except Exception, e:
            self.logger.error(e)

        return simple_queue

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
        except Exception, e:
            self.logger.error("Exception in handling RabbitMQ message: %s", e)

    def main(self):
        try:
            with self.init_rabbit_mq() as queue:
                while True:
                    message = queue.get(block=True)
                    self.handle_message(message.payload)
                    # ACK the message to take it off the queue
                    message.ack()
        except Exception, e:
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

