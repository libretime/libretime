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
from amqplib.client_0_8.exceptions import AMQPConnectionException
import json

from std_err_override import LogWriter

# configure logging
logging_cfg = "/etc/airtime/pypo_logging.cfg"
logging.config.fileConfig(logging_cfg)
logger = logging.getLogger('message_h')
LogWriter.override_std_err(logger)

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
        try:
            schedule_exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            connection = BrokerConnection(self.config["host"], \
                    self.config["user"], \
                    self.config["password"], \
                    self.config["vhost"])

            channel = connection.channel()
            self.simple_queue = SimpleQueue(channel, schedule_queue)
        except Exception, e:
            self.logger.error(e)
            return False

        return True

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
        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

        loops = 1
        while True:
            self.logger.info("Loop #%s", loops)
            try:
                message = self.simple_queue.get(block=True)
                self.handle_message(message.payload)
                # ACK the message to take it off the queue
                message.ack()
            except (IOError, AttributeError, AMQPConnectionException), e:
                self.logger.error('Exception: %s', e)
                self.logger.error("traceback: %s", traceback.format_exc())
                while not self.init_rabbit_mq():
                    self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
                    time.sleep(5)
            except Exception, e:
                """
                sleep 5 seconds so that we don't spin inside this
                while loop and eat all the CPU
                """
                time.sleep(5)

                """
                There is a problem with the RabbitMq messenger service. Let's
                log the error and get the schedule via HTTP polling
                """
                self.logger.error('Exception: %s', e)
                self.logger.error("traceback: %s", traceback.format_exc())

            loops += 1

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        while True:
            self.main()

