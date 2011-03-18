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

# loading config file
try:
    config = ConfigObj('config.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

shows_to_record = {}


def record_show(filelength, filename, filetype="mp3"):

    length = str(filelength)+".0"
    filename = filename.replace(" ", "-")
    filepath = "%s%s.%s" % (config["base_recorded_files"], filename, filetype)

    command = "ecasound -i alsa -o %s -t:%s" % (filepath, filelength)

    call(command, shell=True)

    return filepath


def getDateTimeObj(time):

    timeinfo = time.split(" ")
    date = timeinfo[0].split("-")
    time = timeinfo[1].split(":")

    return datetime.datetime(int(date[0]), int(date[1]), int(date[2]), int(time[0]), int(time[1]), int(time[2]))    

def process_shows(shows):
    
    for show in shows:
        show_starts = getDateTimeObj(show[u'starts'])
        show_end = getDateTimeObj(show[u'ends'])
        time_delta = show_end - show_starts
        
        shows_to_record[show[u'starts']] = time_delta


def check_record():
    
    tnow = datetime.datetime.now()
    sorted_show_keys = sorted(shows_to_record.keys())
    start_time = sorted_show_keys[0]
    next_show = getDateTimeObj(start_time)

    delta = next_show - tnow

    if delta <= datetime.timedelta(seconds=60):
        time.sleep(delta.seconds)
    
        show_length = shows_to_record[start_time]
        filepath = record_show(show_length.seconds, start_time)
        upload_file(filepath)
    

def get_shows():

    url = config["base_url"] + config["show_schedule_url"]
    response = urllib.urlopen(url)
    data = response.read()
    print data

    response_json = json.loads(data)
    shows = response_json[u'shows']
    print shows

    if len(shows):
        process_shows(shows)
        check_record()

def upload_file(filepath):

    filename = os.path.split(filepath)[1]

    # Register the streaming http handlers with urllib2
    register_openers()

    # headers contains the necessary Content-Type and Content-Length
    # datagen is a generator object that yields the encoded parameters
    datagen, headers = multipart_encode({"file": open(filepath, "rb"), 'name': filename})

    url = config["base_url"] + config["upload_file_url"]
   
    req = urllib2.Request(url, datagen, headers)
    response = urllib2.urlopen(req).read().strip()
    print response 


if __name__ == '__main__':

    while True:
        get_shows()
        time.sleep(30)

    
    
   

