#!/usr/local/bin/python
import urllib
import logging
import json
import time
import datetime

from eci import *
from configobj import ConfigObj
import subprocess

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

    e = ECI()

    e("cs-add play_chainsetup")
    e("c-add 1st_chain")
    e("ai-add alsa")
    e("ao-add "+filepath)
    e("cs-set-length "+length)
    e("cop-select 1")
    e("cs-connect")
    e("start")

    while 1:
        time.sleep(1)

        if e("engine-status") != "running":
                break

    e("stop")
    e("cs-disconnect")

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

    #print tnow, next_show

    #tnow = getDateTimeObj("2011-03-04 16:00:00")
    #next_show = getDateTimeObj("2011-03-04 16:00:01")

    delta = next_show - tnow

    if delta <= datetime.timedelta(seconds=60):
        time.sleep(delta.seconds)
    
        show_length = shows_to_record[start_time]
        filepath = record_show(show_length.seconds, start_time)
        #filepath = record_show(10, "2011-03-04 16:00:00")
      
        command = "%s -c %s" %("../../utils/airtime-import", filepath)
        subprocess.call([command],shell=True)
    

def get_shows():

    url = config["base_url"] + config["show_schedule_url"]
    #url = url.replace("%%from%%", "2011-03-13 20:00:00")
    #url = url.replace("%%to%%", "2011-04-17 21:00:00")

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
        time.sleep(30)
    
   

