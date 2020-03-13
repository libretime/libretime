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
import traceback
import re

import libretime_watch

import hashlib
# import magic

from mimetypes import MimeTypes
from mutagen.easyid3 import EasyID3
from mutagen.mp3 import MP3
from mutagen.oggvorbis import OggVorbis
from mutagen.flac import FLAC
import mutagen
from mutagen.id3 import ID3NoHeaderError
from mutagen.mp3 import HeaderNotFoundError
from mutagen.oggvorbis import OggVorbisHeaderError
from mutagen.flac import FLACNoHeaderError

##
## TO DO:
## Use code in airtime_analyzer for importing files rather than duplicating. 
##

#
# analysing the file
#
def replay_gain (filename):
    """Getting the replaygain via python-rplay"""

    EXE="replaygain"

    command = [EXE, '-d', filename]
    try:
        results = subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)
        exp = r'([+|-]?[0-9]+.[0-9]+)([ ]?dB)'
        m = re.search(exp, str(results))
        replaygain = float(m.groups()[0])
        logging.info("Replay gain: {0}".format(replaygain))
        return float(replaygain)
    except OSError as e: # replaygain was not found
        logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have python-rgain installed?"))
    except subprocess.CalledProcessError as e: # replaygain returned an error code
        logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
    except Exception as e:
        logging.warn(e)
        logging.warn("traceback: %s", traceback.format_exc())

    return None

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

def calculate_bpm(filename):
    # Attempt to calculate BPM
    cmd = [
        'ffmpeg','-i',filename,
        '-c:a', 'pcm_f32le', '-ar', '44100', '-ac', '1',
        '-f', 'f32le', '-v', 'quiet', '-']
    p = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    output = subprocess.check_output(['bpm'], stdin=p.stdout)
    p.wait()
    bpm = float(output)
    return int(bpm)

def md5_hash(filename):
    """encapsulate MD5 hashing into a function"""
    with open(filename, 'rb') as fh:
        m = hashlib.md5()
        while True:
            data = fh.read(8192)
            if not data:
               break
            m.update(data)
        return m.hexdigest()

def strim(varchar, length):
    """trim string to fit database varchar length."""
    return varchar[:length]

def analyse_file (filename, database):
    """This method analyses the file and returns analyse_ok 
      It's filling the database dictionary with metadata read from
      the file
    """
    analyse_ok=False
    logging.info ("analyse Filename: "+filename)

    #try to determine the filetype
    try:
        # database["mime"] = magic.from_file(filename, mime=True)
        #
        mime = MimeTypes()
        mtype, a = mime.guess_type(filename)
        database["mime"] = mtype
        #
        logging.info("mime_check: {0} | mime: {1}".format(database["mime"], mtype))
    except:
        logging.critical("Could not get mime type for {0}".format(filename))
        return False

    database["ftype"] = "audioclip"
    database["filesize"] = os.path.getsize(filename) 
    database["import_status"]=0

    #md5
    database["md5"] = md5_hash(filename)

    # Mp3
    if database["mime"] in ['audio/mpeg','audio/mp3', 'application/octet-stream']:
        try:
            audio = EasyID3(filename)
            f = MP3(filename)
        except (ID3NoHeaderError, HeaderNotFoundError) as e:
            logging.warning("MP3 without Metadata: {}".format(filename))
            return False
    # Ogg
    elif database["mime"] in ['audio/ogg', 'audio/vorbis', 'audio/x-vorbis', 'application/ogg', 'application/x-ogg']:
        try:
            audio = OggVorbis(filename)
            f = audio
        except OggVorbisHeaderError:
            logging.warning("OGG without Metadata: {}".format(filename))
            return False
    # flac
    elif database["mime"] in ['audio/flac', 'audio/flac-x']:
        try:
            audio = FLAC(filename)
            f = audio
        except FLACNoHeaderError:
            logging.warning("FLAC without Metadata: {}".format(filename))
            return False
    else:
        logging.warning("Unsupported mime type: {} -- for audio {}".format(database["mime"], filename))
        return False


    try:
        track_title = audio['title'][0]
    except:
        logging.warning("no title ID3 for {}".format(filename))
        # default title to filename
        track_title=filename.split("/")[-1]
    finally:
        track_title = strim(track_title, 512)
        database["track_title"]= track_title

    try:
        artist_name = audio['artist'][0]
        artist_name = strim(artist_name, 512)
        database["artist_name"] = artist_name
    except Exception as err:
        logging.warning('no artist ID3 for '+filename) 
        database["artist_name"]= ""

    for tag in [
            ('genre', 'genre', 64),
            ('label', 'label', 512),
            ('label', 'organization', 512),
            ('language', 'language', 512)]:

        try:
            if len(audio[tag[1]]) > 1:
                value = ', '.join(audio[tag[1]])
            else:
                value = audio[tag[1]][0]
            value = strim(value, tag[2])
            database[tag[0]] = value
        except Exception as err:
            logging.debug('no {0} ID3 for {1}'.format(tag, filename))
            database[tag[0]]= ""

    try:
        album_title = audio['album'][0]
        album_title = strim(album_title, 512)
        database["album_title"] = album_title
    except Exception as err:
        logging.debug('no album title for '+filename) 
        database["album_title"]= ""

    try:
        track_number = audio['tracknumber'][0]
        if "/" in track_number: 
        # TODO are slashes allowed in this format?
            track_number = track_number.split("/")[0]
        track_number = int(track_number)
        if track_number > 2147483647:
        # make sure it doesn't exceed Postgres maximum integer value
            track_number = 0
        database["track_number"]= track_number
    except Exception as err:
        logging.debug('no track_number for '+filename) 
        database["track_number"]= 0

    try:
        bpm = None
        bpm = calculate_bpm(filename)
    except ValueError:
        bpm = int(float(audio['bpm'][0]))
    except KeyError:
        pass
    finally:
        if bpm:
            database["bpm"] = int(bpm)
            logging.info("BPM: {0}".format(bpm))
        else:
            if "bpm" in database:
                del database["bpm"]

    database["bit_rate"] = f.info.bitrate
    database["sample_rate"] = f.info.sample_rate

    if hasattr(f.info, "length"):
        #Converting the length in seconds (float) to a formatted time string
        logging.info("TLEN: {0}".format(f.info.length))
        track_length = datetime.timedelta(seconds=f.info.length)
        database["length"] = str(track_length) #time.strftime("%H:%M:%S.%f", track_length)
        # Other fields for Airtime
        database["cueout"] = database["length"]
        replaygain = replay_gain(filename)
        if replaygain: #...
            database["replay_gain"] = replaygain

    database["cuein"]= "00:00:00.0"
    # get better (?) cuein, cueout using silan
    database["cuein"], database["cueout"] = cue_points (filename, database["cuein"], database["cueout"])
    # mark as silan checked
    database["silan_check"] = "t"

    # use mutage to get better mime
    if f.mime:
        database["mime"] = f.mime[0]
    if database["mime"] in ["audio/mpeg", 'audio/mp3', 'application/octet-stream']:
        if f.info.mode == 3:
            database["channels"] = 1
        else:
            database["channels"] = 2
    else:
        database["channels"] = f.info.channels

    return True
