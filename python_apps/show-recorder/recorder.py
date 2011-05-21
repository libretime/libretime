#!/usr/local/bin/python
import urllib
import logging
import logging.config
import json
import time
import datetime
import os
import sys

from configobj import ConfigObj

from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2

from subprocess import Popen
from threading import Thread

# For RabbitMQ - to be implemented in the future
#from kombu.connection import BrokerConnection
#from kombu.messaging import Exchange, Queue, Consumer, Producer

from api_clients import api_client

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
    print 'Error loading config file: ', e
    sys.exit()

def getDateTimeObj(time):

    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")

    return datetime.datetime(int(date[0]), int(date[1]), int(date[2]), int(time[0]), int(time[1]), int(time[2]))

class ShowRecorder(Thread):

    def __init__ (self, show_instance, filelength, show_name, start_time, filetype):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.filelength = filelength
        self.show_name = show_name
        self.start_time = start_time
        self.filetype = filetype
        self.show_instance = show_instance
        self.logger = logging.getLogger('root')
        self.p = None

    def record_show(self):
        length = str(self.filelength)+".0"
        filename = self.show_name+" "+self.start_time
        filename = filename.replace(" ", "-")
        filepath = "%s%s.%s" % (config["base_recorded_files"], filename, self.filetype)

        command = "ecasound -i alsa -o %s -t:%s" % (filepath, length)
        #-ge:0.1,0.1,0,-1
        args = command.split(" ")

        self.logger.info("starting record")
        self.logger.info("command " + command)
        
        self.p = Popen(args)
        
        #blocks at the following line until the child process
        #quits
        code = self.p.wait()
        self.p = None
        
        self.logger.info("finishing record, return code %s", code)
        return code, filepath
        
    def cancel_recording(self):
        #send signal interrupt (2)
        self.logger.info("Show manually cancelled!")
        if (self.p is not None):
            self.p.terminate()
            self.p = None
            
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

    def run(self):
        code, filepath = self.record_show()

        if code == 0:
            self.logger.info("Preparing to upload %s" % filepath)
            self.upload_file(filepath)
        else:
            self.logger.info("problem recording show")


class Record():

    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        self.shows_to_record = {}
        self.logger = logging.getLogger('root')
        self.sr = None

    def process_shows(self, shows):

        self.shows_to_record = {}

        for show in shows:
            show_starts = getDateTimeObj(show[u'starts'])
            show_end = getDateTimeObj(show[u'ends'])
            time_delta = show_end - show_starts

            self.shows_to_record[show[u'starts']] = [time_delta, show[u'instance_id'], show[u'name']]

    def check_record(self):

        tnow = datetime.datetime.now()
        sorted_show_keys = sorted(self.shows_to_record.keys())

        start_time = sorted_show_keys[0]
        next_show = getDateTimeObj(start_time)

        self.logger.debug("Next show %s", next_show)
        self.logger.debug("Now %s", tnow)

        delta = next_show - tnow
        min_delta = datetime.timedelta(seconds=60)

        if delta <= min_delta:
            self.logger.debug("sleeping %s seconds until show", delta.seconds)
            time.sleep(delta.seconds)

            show_length = self.shows_to_record[start_time][0]
            show_instance = self.shows_to_record[start_time][1]
            show_name = self.shows_to_record[start_time][2]

            self.sr = ShowRecorder(show_instance, show_length.seconds, show_name, start_time, filetype="mp3")
            self.sr.start()

            #remove show from shows to record.
            del self.shows_to_record[start_time]


    def get_shows(self):

        shows = self.api_client.get_shows_to_record()
        
        if self.sr is not None:
            if not shows['is_recording'] and self.sr.is_recording():
                self.sr.cancel_recording()
            
        
        if shows is not None:
            shows = shows[u'shows']
        else:
            shows = []

        if len(shows):
            self.process_shows(shows)
            self.check_record()


if __name__ == '__main__':

    recorder = Record()

    while True:
        recorder.get_shows()
        time.sleep(5)

