import urllib
import logging
import logging.config
import json
import time
import datetime
import os
import sys
import shutil
import socket
import pytz
import signal
import math

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

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
except Exception, e:
    self.logger.error('Error loading config file: %s', e)
    sys.exit()

def getDateTimeObj(time):
    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")

    date = map(int, date)
    time = map(int, time)

    return datetime.datetime(date[0], date[1], date[2], time[0], time[1], time[2], 0, None)

PUSH_INTERVAL = 2

class ShowRecorder(Thread):

    def __init__ (self, show_instance, show_name, filelength, start_time):
        Thread.__init__(self)
        self.logger = logging.getLogger('recorder')
        self.api_client = api_client.api_client_factory(config, self.logger)
        self.filelength = filelength
        self.start_time = start_time
        self.show_instance = show_instance
        self.show_name = show_name
        self.p = None

    def record_show(self):
        length = str(self.filelength)+".0"
        filename = self.start_time
        filename = filename.replace(" ", "-")

        if config["record_file_type"] in ["mp3", "ogg"]:
            filetype = config["record_file_type"]
        else:
            filetype = "ogg";

        filepath = "%s%s.%s" % (config["base_recorded_files"], filename, filetype)

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
            self.p.send_signal(signal.SIGINT)

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
            except Exception, e:
                self.logger.error(e)
        else:
            self.logger.info("problem recording show")
            os.remove(filepath)

class Recorder(Thread):
    def __init__(self, q):
        Thread.__init__(self)
        self.logger = logging.getLogger('recorder')
        self.api_client = api_client.api_client_factory(config, self.logger)
        self.api_client.register_component("show-recorder")
        self.sr = None
        self.shows_to_record = {}
        self.server_timezone = ''
        self.queue = q
        self.logger.info("RecorderFetch: init complete")
        self.loops = 0

    def handle_message(self):
        if not self.queue.empty():
            message = self.queue.get()
            msg =  json.loads(message)
            command = msg["event_type"]
            self.logger.info("Received msg from Pypo Message Handler: %s", msg)
            if command == 'cancel_recording':
                if self.sr is not None and self.sr.is_recording():
                    self.sr.cancel_recording()
            else:
                self.process_recorder_schedule(msg)
                self.loops = 0
        
        if self.shows_to_record:
            self.start_record()
    
    def process_recorder_schedule(self, m):
        self.logger.info("Parsing recording show schedules...")
        temp_shows_to_record = {}
        shows = m['shows']
        for show in shows:
            show_starts = getDateTimeObj(show[u'starts'])
            show_end = getDateTimeObj(show[u'ends'])
            time_delta = show_end - show_starts

            temp_shows_to_record[show[u'starts']] = [time_delta, show[u'instance_id'], show[u'name'], m['server_timezone']]
        self.shows_to_record = temp_shows_to_record

    def get_time_till_next_show(self):
        if len(self.shows_to_record) != 0:
            tnow = datetime.datetime.utcnow()
            sorted_show_keys = sorted(self.shows_to_record.keys())

            start_time = sorted_show_keys[0]
            next_show = getDateTimeObj(start_time)

            delta = next_show - tnow
            out = delta.seconds
            
            if out < 5:
                self.logger.debug("Shows %s", self.shows_to_record)
                self.logger.debug("Next show %s", next_show)
                self.logger.debug("Now %s", tnow)
        return out

    def start_record(self):
        if len(self.shows_to_record) != 0:
            try:
                delta = self.get_time_till_next_show()
                if delta < 5:
                    self.logger.debug("sleeping %s seconds until show", delta)
                    time.sleep(delta)
    
                    sorted_show_keys = sorted(self.shows_to_record.keys())
                    start_time = sorted_show_keys[0]
                    show_length = self.shows_to_record[start_time][0]
                    show_instance = self.shows_to_record[start_time][1]
                    show_name = self.shows_to_record[start_time][2]
                    server_timezone = self.shows_to_record[start_time][3]
                    
                    T = pytz.timezone(server_timezone)
                    start_time_on_UTC = getDateTimeObj(start_time)
                    start_time_on_server = start_time_on_UTC.replace(tzinfo=pytz.utc).astimezone(T)
                    start_time_formatted = '%(year)d-%(month)02d-%(day)02d %(hour)02d:%(min)02d:%(sec)02d' % \
                        {'year': start_time_on_server.year, 'month': start_time_on_server.month, 'day': start_time_on_server.day,\
                         'hour': start_time_on_server.hour, 'min': start_time_on_server.minute, 'sec': start_time_on_server.second}
                    self.sr = ShowRecorder(show_instance, show_name, show_length.seconds, start_time_formatted)
                    self.sr.start()
                    #remove show from shows to record.
                    del self.shows_to_record[start_time]
                    #self.time_till_next_show = self.get_time_till_next_show()
            except Exception,e :
                import traceback
                top = traceback.format_exc()
                self.logger.error('Exception: %s', e)
                self.logger.error("traceback: %s", top)

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        try:
            self.logger.info("Started...")
            # Bootstrap: since we are just starting up, we need to grab the
            # most recent schedule.  After that we can just wait for updates.
            try:
                temp = self.api_client.get_shows_to_record()
                if temp is not None:
                    self.process_recorder_schedule(temp)
                self.logger.info("Bootstrap recorder schedule received: %s", temp)
            except Exception, e:
                self.logger.error(e)
                
            self.logger.info("Bootstrap complete: got initial copy of the schedule")
            
            recording = False
            
            self.loops = 0
            heartbeat_period = math.floor(30/PUSH_INTERVAL)
            
            while True:
                if self.loops % heartbeat_period == 0:
                    self.logger.info("heartbeat")
                if self.loops * PUSH_INTERVAL > 3600:
                    self.loops = 0
                    """
                    Fetch recorder schedule
                    """
                    try:
                        temp = self.api_client.get_shows_to_record()
                        if temp is not None:
                            self.process_recorder_schedule(temp)
                        self.logger.info("updated recorder schedule received: %s", temp)
                    except Exception, e:
                        self.logger.error(e)
                try: self.handle_message()
                except Exception, e:
                    self.logger.error('Pypo Recorder Exception: %s', e)
                time.sleep(PUSH_INTERVAL)
                self.loops += 1
        except Exception,e :
            import traceback
            top = traceback.format_exc()
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", top)

