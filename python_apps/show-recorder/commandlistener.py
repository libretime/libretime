#!/usr/local/bin/python
import urllib
import logging
import logging.config
import json
import time
import datetime
import os
import sys
import shutil

from configobj import ConfigObj

from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2

from subprocess import Popen
from threading import Thread

import mutagen

from api_clients import api_client

# For RabbitMQ - to be implemented in the future
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()
    
POLL_INTERVAL=3600
# loading config file
try:
    config = ConfigObj('/etc/airtime/recorder.cfg')
except Exception, e:
    logger = logging.getLogger('root')
    logger.error('Error loading config file: %s', e)
    sys.exit()
    
def getDateTimeObj(time):

    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")

    return datetime.datetime(int(date[0]), int(date[1]), int(date[2]), int(time[0]), int(time[1]), int(time[2]))


class CommandHandler(Thread):
    def __init__(self, command, q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.logger = logging.getLogger('root')
        self.logger.info("Handling command: " + command)
        self.command = command
        self.queue = q
    
    def run(self):
        if(self.command == 'update_schedule'):
            self.queue.put(self.api_client.get_shows_to_record())

class CommandListener(Thread):
    def __init__(self, q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.logger = logging.getLogger('root')
        self.sr = None
        self.queue = q
        self.shows_to_record = []
        self.logger.info("RecorderFetch: init complete")

    def init_rabbit_mq(self):
        self.logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-show-recorder", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("recorder-fetch", exchange=schedule_exchange, key="foo")
            self.connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], "/")
            channel = self.connection.channel() 
            consumer = Consumer(channel, schedule_queue)
            consumer.register_callback(self.handle_message)
            consumer.consume()
        except Exception, e:
            self.logger.error(e)
            return False
            
        return True
    
    def handle_message(self, body, message):
        self.logger.info("Received command from RabbitMQ: " + message.body)
        ch = CommandHandler(message.body, self.queue)
        ch.start()
        # ACK the message to take it off the queue
        message.ack()
        
    """def process_shows(self, shows):
        self.logger.info("Processing shows...")
        self.shows_to_record = []
        self.logger.info(shows)
        shows = shows[u'shows']
        for show in shows:
            show_starts = getDateTimeObj(show[u'starts'])
            show_end = getDateTimeObj(show[u'ends'])
            time_delta = show_end - show_starts

            self.shows_to_record[show[u'starts']] = [time_delta, show[u'instance_id'], show[u'name']]"""
    
    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        self.logger.info("Started...")
        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we can just wait for updates. 
        try:
            self.queue.put(self.api_client.get_shows_to_record())
            self.logger.info("Bootstrap complete: got initial copy of the schedule")
        except Exception, e:
            self.logger.error(e)
                
        loops = 1        
        while True:
            self.logger.info("Loop #%s", loops)
            try:
                # Wait for messages from RabbitMQ.  Timeout if we
                # dont get any after POLL_INTERVAL.
                self.connection.drain_events(timeout=POLL_INTERVAL)
                status = 1
            except Exception, e:
                self.logger.info(e)    
                # We didnt get a message for a while, so poll the server
                # to get an updated schedule. 
                self.queue.put(self.api_client.get_shows_to_record())
            loops += 1