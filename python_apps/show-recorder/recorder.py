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

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()

# loading config file
try:
    config = ConfigObj('/etc/airtime/recorder.cfg')
except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file: %s', e)
    sys.exit()

def getDateTimeObj(time):
    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")
    
    date = map(int, date)
    time = map(int, time)

    return datetime.datetime(date[0], date[1], date[2], time[0], time[1], time[2], 0, None)

class ShowRecorder(Thread):

    def __init__ (self, show_instance, show_name, filelength, start_time, filetype):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.filelength = filelength
        self.start_time = start_time
        self.filetype = filetype
        self.show_instance = show_instance
        self.show_name = show_name
        self.logger = logging.getLogger('root')
        self.p = None

    def record_show(self):
        length = str(self.filelength)+".0"
        filename = self.start_time
        filename = filename.replace(" ", "-")
        filepath = "%s%s.%s" % (config["base_recorded_files"], filename, self.filetype)

        br = config["record_bitrate"]
        sr = config["record_samplerate"]
        c = config["record_channels"]
        ss = config["record_sample_size"]

        #-f:16,2,44100
        #-b:256
        command = "ecasound -f:%s,%s,%s -i alsa -o %s,%s000 -t:%s" % (ss, c, sr, filepath, br, length)
        args = command.split(" ")

        self.logger.info("starting record")
        self.logger.info("command " + command)

        self.p = Popen(args)

        #blocks at the following line until the child process
        #quits
        code = self.p.wait()

        self.logger.info("finishing record, return code %s", self.p.returncode)
        code = self.p.returncode

        self.p = None

        return code, filepath

    def cancel_recording(self):
        #add 3 second delay before actually cancelling the show. The reason
        #for this is because it appears that ecasound starts 1 second later than
        #it should, and therefore this method is sometimes incorrectly called 1
        #second before the show ends.
        #time.sleep(3)

        #send signal interrupt (2)
        self.logger.info("Show manually cancelled!")
        if (self.p is not None):
            self.p.kill()

    #if self.p is defined, then the child process ecasound is recording
    def is_recording(self):
        return (self.p is not None)

    def upload_file(self, filepath):

        filename = os.path.split(filepath)[1]

        # Register the streaming http handlers with urllib2
        register_openers()

        # headers contains the necessary Content-Type and Content-Length
        # datagen is a generator object that yields the encoded parameters
        datagen, headers = multipart_encode({"file": open(filepath, "rb"), 'name': filename, 'show_instance': self.show_instance})

        self.api_client.upload_recorded_show(datagen, headers)

    def set_metadata_and_save(self, filepath):
        try:
            date = self.start_time
            md = date.split(" ")
            time = md[1].replace(":", "-")
            self.logger.info("time: %s" % time)

            name = time+"-"+self.show_name
            artist = api_client.encode_to("Airtime Show Recorder",'utf-8')

            #set some metadata for our file daemon
            recorded_file = mutagen.File(filepath, easy=True)
            recorded_file['title'] = name
            recorded_file['artist'] = artist
            recorded_file['date'] = md[0]
            recorded_file['tracknumber'] = self.show_instance
            recorded_file.save()

        except Exception, e:
            self.logger.error("Exception: %s", e)

    def run(self):
        code, filepath = self.record_show()

        if code == 0:
            try:
                self.logger.info("Preparing to upload %s" % filepath)

                self.set_metadata_and_save(filepath)

                self.upload_file(filepath)
                os.remove(filepath)
            except Exceptio, e:
                self.logger.error(e)
        else:
            self.logger.info("problem recording show")
            os.remove(filepath)

class CommandListener(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.logger = logging.getLogger('root')
        self.sr = None
        self.current_schedule = {}
        self.shows_to_record = {}
        self.time_till_next_show = 3600
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
        # ACK the message to take it off the queue
        message.ack()
        self.logger.info("Received command from RabbitMQ: " + message.body)
        m =  json.loads(message.body)
        command = m['event_type']
        self.logger.info("Handling command: " + command)

        if(command == 'update_schedule'):
            temp = m['shows']
            if temp is not None:
                self.parse_shows(temp)
        elif(command == 'cancel_recording'):
            if self.sr.is_recording():
                self.sr.cancel_recording()

    def parse_shows(self, shows):
        self.logger.info("Parsing show schedules...")
        self.shows_to_record = {}
        for show in shows:
            show_starts = getDateTimeObj(show[u'starts'])
            show_end = getDateTimeObj(show[u'ends'])
            time_delta = show_end - show_starts
            
            self.shows_to_record[show[u'starts']] = [time_delta, show[u'instance_id'], show[u'name']]
            delta = self.get_time_till_next_show()
            # awake at least 5 seconds prior to the show start
            self.time_till_next_show = delta - 5

        self.logger.info(self.shows_to_record)

    def get_time_till_next_show(self):
        if len(self.shows_to_record) != 0:
            tnow = datetime.datetime.utcnow()
            sorted_show_keys = sorted(self.shows_to_record.keys())

            start_time = sorted_show_keys[0]
            next_show = getDateTimeObj(start_time)

            delta = next_show - tnow
            out = delta.seconds

            self.logger.debug("Next show %s", next_show)
            self.logger.debug("Now %s", tnow)
        else:
            out = 3600
        return out

    def start_record(self):
        if len(self.shows_to_record) != 0:
            try:
                delta = self.get_time_till_next_show()

                self.logger.debug("sleeping %s seconds until show", delta)
                time.sleep(delta)

                sorted_show_keys = sorted(self.shows_to_record.keys())
                start_time = sorted_show_keys[0]
                show_length = self.shows_to_record[start_time][0]
                show_instance = self.shows_to_record[start_time][1]
                show_name = self.shows_to_record[start_time][2]

                self.sr = ShowRecorder(show_instance, show_name, show_length.seconds, start_time, filetype="mp3")
                self.sr.start()

                #remove show from shows to record.
                del self.shows_to_record[start_time]
                self.time_till_next_show = 3600
            except Exception,e :
                self.logger.error(e)
        else:
            self.logger.debug("No recording scheduled...")

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
            temp = self.api_client.get_shows_to_record()
            if temp is not None:
                shows = temp['shows']
                self.parse_shows(shows)
            self.logger.info("Bootstrap complete: got initial copy of the schedule")
        except Exception, e:
            self.logger.error(e)

        loops = 1
        while True:
            self.logger.info("Loop #%s", loops)
            try:
                # block until 5 seconds before the next show start
                self.connection.drain_events(timeout=self.time_till_next_show)
            except Exception, e:
                self.logger.info(e)
                # start recording
                self.start_record()

            loops += 1

if __name__ == '__main__':
    cl = CommandListener()
    cl.start()


