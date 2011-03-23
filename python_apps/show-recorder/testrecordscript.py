#!/usr/local/bin/python
import urllib
import logging
import json
import time
import datetime
import os

from configobj import ConfigObj

from poster.encode import multipart_encode
from poster.streaminghttp import register_openers
import urllib2

from subprocess import call
from threading import Thread

# loading config file
try:
    config = ConfigObj('config.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

shows_to_record = {}

class Recorder(Thread):

    def __init__ (self, show_instance, filelength, filename, filetype):
        Thread.__init__(self)
        self.filelength = filelength
        self.filename = filename
        self.filetype = filetype
        self.show_instance = show_instance

    def record_show(self):

        length = str(self.filelength)+".0"
        filename = self.filename.replace(" ", "-")
        filepath = "%s%s.%s" % (config["base_recorded_files"], filename, self.filetype)

        command = "ecasound -i alsa -o %s -t:%s" % (filepath, length)
        call(command, shell=True)

        return filepath

    def upload_file(self, filepath):

        filename = os.path.split(filepath)[1]

        # Register the streaming http handlers with urllib2
        register_openers()

        # headers contains the necessary Content-Type and Content-Length
        # datagen is a generator object that yields the encoded parameters
        datagen, headers = multipart_encode({"file": open(filepath, "rb"), 'name': filename, 'show_instance': self.show_instance})

        url = config["base_url"] + config["upload_file_url"]
       
        req = urllib2.Request(url, datagen, headers)
        response = urllib2.urlopen(req).read().strip()
        print response

    def run(self):
        filepath = self.record_show()
        self.upload_file(filepath)


def getDateTimeObj(time):

    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")

    return datetime.datetime(int(date[0]), int(date[1]), int(date[2]), int(time[0]), int(time[1]), int(time[2]))    

def process_shows(shows):

    global shows_to_record
    shows_to_record = {}
    
    for show in shows:
        show_starts = getDateTimeObj(show[u'starts'])
        show_end = getDateTimeObj(show[u'ends'])
        time_delta = show_end - show_starts
        
        shows_to_record[show[u'starts']] = [time_delta, show[u'instance_id']]


def check_record():
    
    tnow = datetime.datetime.now()
    sorted_show_keys = sorted(shows_to_record.keys())
    print sorted_show_keys
    start_time = sorted_show_keys[0]
    next_show = getDateTimeObj(start_time)

    print next_show
    print tnow
    delta = next_show - tnow
    print delta    

    if delta <= datetime.timedelta(seconds=60):
        print "sleeping %s seconds until show" % (delta.seconds)
        time.sleep(delta.seconds)
       
        show_length = shows_to_record[start_time][0]
        show_instance = shows_to_record[start_time][1]
        show = Recorder(show_instance, show_length.seconds, start_time, filetype="mp3")
        show.start()
     
        #remove show from shows to record.
        del shows_to_record[start_time]
    

def get_shows():

    url = config["base_url"] + config["show_schedule_url"]
    response = urllib.urlopen(url)
    data = response.read()
   
    response_json = json.loads(data)
    shows = response_json[u'shows']
    print shows

    if len(shows):
        process_shows(shows)
        check_record() 


if __name__ == '__main__':

    while True:
        get_shows()
        time.sleep(5)

    
    
   

