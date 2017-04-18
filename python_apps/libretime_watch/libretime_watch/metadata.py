#!/usr/bin/python
# -*- coding: utf-8 -*-

##
## Handling of all metadata stuff
##
import codecs
import datetime
import json
import logging
import os
import select
import signal
import subprocess
import sys
import time
import types

import libretime_watch


# create empty dictionary 
#database = {}

#
# analysing the file
#
def replay_gain (filename):
   """Getting the replaygain via python-rplay"""

   EXE="replaygain"

   command = [EXE, '-d', filename]
   try:
            results = subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)
            filename_token = "%s: " % filename
            rg_pos = results.find(filename_token, results.find("Calculating Replay Gain information")) + len(filename_token)
            db_pos = results.find(" dB", rg_pos)
            replaygain = results[rg_pos:db_pos]

   except OSError as e: # replaygain was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have python-rgain installed?"))
   except subprocess.CalledProcessError as e: # replaygain returned an error code
            logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
   except Exception as e:
            logging.warn(e)

   return replaygain

def cue_points (filename, cue_in, cue_out):
   """Analyse file cue using silan
      return cue_in, cue_out
   """

   EXE="silan"

   command = [EXE, '-q', '-b', '-F', '0.99', '-f', 'JSON', '-t', '1.0', filename]
   try:
            results_json = subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)
            silan_results = json.loads(results_json)
            # Defensive coding against Silan wildly miscalculating the cue in and out times:
            silan_cuein = float(format(silan_results['sound'][0][0], 'f'))
            silan_cueout = float (format(silan_results['sound'][0][1], 'f'))
            # get cue_out(coming from mutagen) as seconds
            x = datetime.datetime.strptime(cue_out, '%H:%M:%S.%f') - datetime.datetime(1900,1,1)
            cue_out_sec= x.total_seconds()
            # trust silan only, if the calculated value is within 95%..102% of the mutagen cue_out
            if silan_cueout > cue_out_sec * 0.95 and silan_cueout < cue_out_sec * 1.02:
               cue_out =  datetime.timedelta(seconds=silan_cueout)
               logging.info ("Silan defined a new cue_out: " + str(cue_out))
            cue_in = datetime.timedelta(seconds=silan_cuein)
            logging.info("Silan: "+str(silan_cuein)+" "+str(silan_cueout)+" "+str(cue_out_sec))

   except OSError as e: # silan was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have silan installed?"))
   except subprocess.CalledProcessError as e: # silan returned an error code
            logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
   except Exception as e:
            logging.warn(e)

   return cue_in, cue_out


def analyse_file (filename,database):
   """This method analyses the file and returns analyse_ok 
      It's filling the database dictionary with metadata read from
      the file
   """
   import hashlib
   import magic
   # test
   from mimetypes import MimeTypes
   from mutagen.easyid3 import EasyID3
   from mutagen.mp3 import MP3
   import mutagen

   analyse_ok=False
   logging.info ("analyse Filename: "+filename)
   #try to determin the filetype 
   mime_check = magic.from_file(filename, mime=True)
   database["mime"] = mime_check
   # test
   #f = MP3(filename)
   #f= mutagen.FileType(filename)
   #mime_mutagen = f.mime[0]
   #logging.info (" mutagen: " +mime_mutagen )
   
   mime = MimeTypes()
   type, a = mime.guess_type(filename)
   logging.info ("mime_check :"+database["mime"]+ " mime: "+type)
   #+" mutagen: " +mime_mutagen )
   #
   database["ftype"] = "audioclip"
   database["filesize"] = os.path.getsize(filename) 
   database["import_status"]=0
   #md5
   with open(filename, 'rb') as fh:
       m = hashlib.md5()
       while True:
           data = fh.read(8192)
           if not data:
              break
           m.update(data)
       database["md5"] = m.hexdigest()
   # MP3 file ?
   if database["mime"] in ['audio/mpeg','audio/mp3','application/octet-stream']:
     try:
       audio = EasyID3(filename)
       database["track_title"]=audio['title'][0]
       try:
         database["artist_name"]=audio['artist'][0]
       except StandardError, err:
         logging.warning('no artist ID3 for '+filename) 
         database["artist_name"]= ""       
       try:
         database["genre"]=audio['genre'][0]
       except StandardError, err:
         logging.debug('no genre ID3 for '+filename) 
         database["genre"]= ""
       try:
         database["album_title"]=audio['album'][0]
       except StandardError, err:
         logging.debug('no album title for '+filename) 
         database["album_title"]= ""
       try:
         database["track_number"]=audio['tracknumber'][0]
       except StandardError, err:
         logging.debug('no track_number for '+filename) 
         database["track_number"]= 0
       # get data encoded into file
       f = MP3(filename)
       database["bit_rate"]=f.info.bitrate
       database["sample_rate"]=f.info.sample_rate
       if hasattr(f.info, "length"):
         #Converting the length in seconds (float) to a formatted time string
         track_length = datetime.timedelta(seconds=f.info.length)
         database["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
         # Other fields for Airtime
         database["cueout"] = database["length"]
         database["replay_gain"]=float(replay_gain(filename))
       database["cuein"]= "00:00:00.0"
       # get better (?) cuein, cueout using silan
       database["cuein"], database["cueout"] = cue_points (filename, database["cuein"], database["cueout"])
       # mark as silan checked
       database["silan_check"] = "t"
       # use mutage to get better mime 
       if  f.mime:
            database["mime"] = f.mime[0]
       if database["mime"] in ["audio/mpeg", 'audio/mp3']:
          if f.info.mode == 3:
                database["channels"] = 1
          else:
                database["channels"] = 2
       else:
            database["channels"] = f.info.channels
       analyse_ok=True

     except StandardError, err:
          logging.error('Error ',str(err),filename) 
          #print "Error: ",str(err),filename
   return analyse_ok

