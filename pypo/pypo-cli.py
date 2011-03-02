#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Python part of radio playout (pypo)

The main functions are "fetch" (./pypo_cli.py -f) and "push" (./pypo_cli.py -p)
"""

# python defaults (debian default)
import time
import calendar

import os
import traceback
from optparse import *
import sys
import time
import datetime
import logging
import logging.config
import shutil
import urllib
import urllib2
import pickle
import telnetlib
import random
import string
import operator
import inspect

# additional modules (should be checked)
from configobj import ConfigObj

# custom imports
from util import *
from api_clients import *

PYPO_VERSION = '0.2'

# Set up command-line options
parser = OptionParser()

# help screen / info
usage = "%prog [options]" + " - python playout system"
parser = OptionParser(usage=usage)

# Options
parser.add_option("-v", "--compat", help="Check compatibility with server API version", default=False, action="store_true", dest="check_compat")

parser.add_option("-t", "--test", help="Do a test to make sure everything is working properly.", default=False, action="store_true", dest="test")
parser.add_option("-f", "--fetch-scheduler", help="Fetch the schedule from server.  This is a polling process that runs forever.", default=False, action="store_true", dest="fetch_scheduler")
parser.add_option("-p", "--push-scheduler", help="Push the schedule to Liquidsoap. This is a polling process that runs forever.", default=False, action="store_true", dest="push_scheduler")

parser.add_option("-b", "--cleanup", help="Cleanup", default=False, action="store_true", dest="cleanup")
parser.add_option("-c", "--check", help="Check the cached schedule and exit", default=False, action="store_true", dest="check")

# parse options
(options, args) = parser.parse_args()

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    POLL_INTERVAL = float(config['poll_interval'])
    PUSH_INTERVAL = 0.5
    #PUSH_INTERVAL = float(config['push_interval'])
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

class Global:
    def __init__(self):
        print

    def selfcheck(self):
        self.api_client = api_client.api_client_factory(config)
        if (not self.api_client.is_server_compatible()):
            sys.exit()


class Playout:
    def __init__(self):
        self.api_client = api_client.api_client_factory(config)
        self.cue_file = CueFile()
        self.set_export_source('scheduler')

        """
        push_ahead2 MUST be < push_ahead. The difference in these two values
        gives the number of seconds of the window of opportunity for the scheduler
        to catch when a playlist is to be played.
        """
        self.push_ahead = 15
        self.push_ahead2 = 10

        self.range_updated = False


    def test_api(self):
        self.api_client.test()


    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        self.schedule_file = self.cache_dir + 'schedule.pickle'
        self.schedule_tracker_file = self.cache_dir + "schedule_tracker.pickle"


    """
    Fetching part of pypo
    - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" / "cache_for")
    - Saves a serialized file of the schedule
    - playlists are prepared. (brought to liquidsoap format) and, if not mounted via nsf, files are copied
      to the cache dir (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
    - runs the cleanup routine, to get rid of unused cashed files
    """
    def fetch(self, export_source):
        """
        wrapper script for fetching the whole schedule (in json)
        """
        logger = logging.getLogger()

        try: os.mkdir(self.cache_dir)
        except Exception, e: pass

        # get schedule
        try:
            while self.get_schedule() != 1:
                logger.warning("failed to read from export url")
                time.sleep(1)

        except Exception, e: logger.error("%s", e)

        # prepare the playlists
        if config["cue_style"] == 'pre':
            try: self.prepare_playlists_cue()
            except Exception, e: logger.error("%s", e)
        elif config["cue_style"] == 'otf':
            try: self.prepare_playlists(self.export_source)
            except Exception, e: logger.error("%s", e)

        # cleanup
        try: self.cleanup(self.export_source)
        except Exception, e: logger.error("%s", e)

    def get_schedule(self):
        logger = logging.getLogger()
        status, response = self.api_client.get_schedule()

        if status == 1:
            logger.info("dump serialized schedule to %s", self.schedule_file)
            schedule = response['playlists']
            try:
                schedule_file = open(self.schedule_file, "w")
                pickle.dump(schedule, schedule_file)
                schedule_file.close()

            except Exception, e:
                logger.critical("Exception %s", e)
                status = 0

        return status


    """
    Alternative version of playout preparation. Every playlist entry is
    pre-cued if neccessary (cue_in/cue_out != 0) and stored in the
    playlist folder.
    file is eg 2010-06-23-15-00-00/17_cue_10.132-123.321.mp3
    """
    def prepare_playlists_cue(self):
        logger = logging.getLogger()

        # Load schedule from disk
        schedule = self.load_schedule()

        # Dont do anything if schedule is empty
        if (not schedule):
            logger.debug("Schedule is empty.")
            return

        scheduleKeys = sorted(schedule.iterkeys())

        try:
            for pkey in scheduleKeys:
                logger.info("found playlist at %s", pkey)
                playlist = schedule[pkey]

                # create playlist directory
                try:
                    os.mkdir(self.cache_dir + str(pkey))
                except Exception, e:
                    pass

                logger.debug('*****************************************')
                logger.debug('pkey:        ' + str(pkey))
                logger.debug('cached at :  ' + self.cache_dir + str(pkey))
                logger.debug('subtype:     ' + str(playlist['subtype']))
                logger.debug('played:      ' + str(playlist['played']))
                logger.debug('schedule id: ' + str(playlist['schedule_id']))
                logger.debug('duration:    ' + str(playlist['duration']))
                logger.debug('source id:   ' + str(playlist['x_ident']))
                logger.debug('*****************************************')

                if int(playlist['played']) == 1:
                    logger.info("playlist %s already played / sent to liquidsoap, so will ignore it", pkey)

                elif int(playlist['subtype']) > 0 and int(playlist['subtype']) < 5:
                    ls_playlist = self.handle_media_file(playlist, pkey)

                # write playlist file
                plfile = open(self.cache_dir + str(pkey) + '/list.lsp', "w")
                plfile.write(json.dumps(ls_playlist))
                plfile.close()
                logger.info('ls playlist file written to %s', self.cache_dir + str(pkey) + '/list.lsp')

        except Exception, e:
            logger.info("%s", e)

    def handle_media_file(self, playlist, pkey):
        """
        This handles both remote and local files.
        Returns an updated ls_playlist string.
        """
        ls_playlist = []

        logger = logging.getLogger()
        for media in playlist['medias']:
            logger.debug("Processing track %s", media['uri'])

            fileExt = os.path.splitext(media['uri'])[1]
            try:
                if str(media['cue_in']) == '0' and str(media['cue_out']) == '0':
                    logger.debug('No cue in/out detected for this file')
                    dst = "%s%s/%s%s" % (self.cache_dir, str(pkey), str(media['id']), str(fileExt))
                    do_cue = False
                else:
                    logger.debug('Cue in/out detected')
                    dst = "%s%s/%s_cue_%s-%s%s" % \
                    (self.cache_dir, str(pkey), str(media['id']), str(float(media['cue_in']) / 1000), str(float(media['cue_out']) / 1000), str(fileExt))
                    do_cue = True

                # check if it is a remote file, if yes download
                if media['uri'][0:4] == 'http':
                    self.handle_remote_file(media, dst, do_cue)
                else:
                    logger.debug("invalid media uri: %s", media['uri'])

                
                if True == os.access(dst, os.R_OK):
                    # check filesize (avoid zero-byte files)
                    try: fsize = os.path.getsize(dst)
                    except Exception, e:
                        logger.error("%s", e)
                        fsize = 0

                    if fsize > 0:
                        pl_entry = \
                        'annotate:export_source="%s",media_id="%s",liq_start_next="%s",liq_fade_in="%s",liq_fade_out="%s",schedule_table_id="%s":%s'\
                        % (str(media['export_source']), media['id'], 0, str(float(media['fade_in']) / 1000), \
                            str(float(media['fade_out']) / 1000), media['row_id'],dst)

                        logger.debug(pl_entry)

                        """
                        Tracks are only added to the playlist if they are accessible
                        on the file system and larger than 0 bytes.
                        So this can lead to playlists shorter than expectet.
                        (there is a hardware silence detector for this cases...)
                        """
                        entry = dict()
                        entry['type'] = 'file'
                        entry['annotate'] = pl_entry
                        ls_playlist.append(entry)

                        logger.debug("everything ok, adding %s to playlist", pl_entry)
                    else:
                        print 'zero-file: ' + dst + ' from ' + media['uri']
                        logger.warning("zero-size file - skipping %s. will not add it to playlist", dst)

                else:
                    logger.warning("something went wrong. file %s not available. will not add it to playlist", dst)

            except Exception, e: logger.info("%s", e)
        return ls_playlist


    def handle_remote_file(self, media, dst, do_cue):
        logger = logging.getLogger()
        if do_cue == False:
            if os.path.isfile(dst):
                logger.debug("file already in cache: %s", dst)
            else:
                logger.debug("try to download %s", media['uri'])
                self.api_client.get_media(media['uri'], dst)

        else:
            if os.path.isfile(dst):
                logger.debug("file already in cache: %s", dst)

            else:
                logger.debug("try to download and cue %s", media['uri'])

                fileExt = os.path.splitext(media['uri'])[1]
                dst_tmp = config["tmp_dir"] + "".join([random.choice(string.letters) for i in xrange(10)]) + fileExt
                self.api_client.get_media(media['uri'], dst_tmp)

                # cue
                logger.debug("STARTING CUE")
                debugDst = self.cue_file.cue(dst_tmp, dst, float(media['cue_in']) / 1000, float(media['cue_out']) / 1000)
                logger.debug(debugDst)
                logger.debug("END CUE")

                if True == os.access(dst, os.R_OK):
                    try: fsize = os.path.getsize(dst)
                    except Exception, e:
                        logger.error("%s", e)
                        fsize = 0

                if fsize > 0:
                    logger.debug('try to remove temporary file: %s' + dst_tmp)
                    try: os.remove(dst_tmp)
                    except Exception, e:
                        logger.error("%s", e)

                else:
                    logger.warning('something went wrong cueing: %s - using uncued file' + dst)
                    try: os.rename(dst_tmp, dst)
                    except Exception, e:
                        logger.error("%s", e)

    
    def cleanup(self, export_source):
        """
        Cleans up folders in cache_dir. Look for modification date older than "now - CACHE_FOR"
        and deletes them.
        """
        logger = logging.getLogger()

        offset = 3600 * int(config["cache_for"])
        now = time.time()

        for r, d, f in os.walk(self.cache_dir):
            for dir in d:
                try:
                    timestamp = time.mktime(time.strptime(dir, "%Y-%m-%d-%H-%M-%S"))
                    if (now - timestamp) > offset:
                        try:
                            logger.debug('trying to remove  %s - timestamp: %s', os.path.join(r, dir), timestamp)
                            shutil.rmtree(os.path.join(r, dir))
                        except Exception, e:
                            logger.error("%s", e)
                            pass
                        else:
                            logger.info('sucessfully removed %s', os.path.join(r, dir))
                except Exception, e:
                    print e
                    logger.error("%s", e)


    """
    The Push Loop - the push loop periodically (minimal 1/2 of the playlist-grid)
    checks if there is a playlist that should be scheduled at the current time.
    If yes, the temporary liquidsoap playlist gets replaced with the corresponding one,
    then liquidsoap is asked (via telnet) to reload and immediately play it.
    """
    def push(self, export_source):
        logger = logging.getLogger()

        self.schedule = self.load_schedule()
        playedItems = self.load_schedule_tracker()

        tcoming = time.localtime(time.time() + self.push_ahead)
        tcoming2 = time.localtime(time.time() + self.push_ahead2)
        tnow = time.localtime(time.time())

        str_tcoming_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming[0], tcoming[1], tcoming[2], tcoming[3], tcoming[4], tcoming[5])
        str_tcoming2_s = "%04d-%02d-%02d-%02d-%02d-%02d" % (tcoming2[0], tcoming2[1], tcoming2[2], tcoming2[3], tcoming2[4], tcoming2[5])

        if self.schedule == None:
            logger.warn('Unable to loop schedule - maybe write in progress?')
            logger.warn('Will try again in next loop.')

        else:
            for pkey in self.schedule:
                playedFlag = (pkey in playedItems) and playedItems[pkey].get("played", 0)
                if pkey[0:19] == str_tcoming_s or (pkey[0:19] < str_tcoming_s and pkey[0:19] > str_tcoming2_s and not playedFlag):
                    logger.debug('Preparing to push playlist scheduled at: %s', pkey)
                    playlist = self.schedule[pkey]

                    ptype = playlist['subtype']

                    # We have a match, replace the current playlist and
                    # force liquidsoap to refresh.
                    if (self.push_liquidsoap(pkey, self.schedule, ptype) == 1):
                        logger.debug("Pushed to liquidsoap, updating 'played' status.")
                        # Marked the current playlist as 'played' in the schedule tracker
                        # so it is not called again in the next push loop.
                        # Write changes back to tracker file.
                        playedItems[pkey] = playlist
                        playedItems[pkey]['played'] = 1
                        schedule_tracker = open(self.schedule_tracker_file, "w")
                        pickle.dump(playedItems, schedule_tracker)
                        schedule_tracker.close()
                        logger.debug("Wrote schedule to disk: "+str(json.dumps(playedItems)))

                        # Call API to update schedule states
                        logger.debug("Doing callback to server to update 'played' status.")
                        self.api_client.notify_scheduled_item_start_playing(pkey, self.schedule)


    def load_schedule(self):
        logger = logging.getLogger()
        schedule = None

        # create the file if it doesnt exist
        if (not os.path.exists(self.schedule_file)):
            logger.debug('creating file ' + self.schedule_file)
            open(self.schedule_file, 'w').close()
        else:
            # load the schedule from cache
            #logger.debug('loading schedule file '+self.schedule_file)
            try:
                schedule_file = open(self.schedule_file, "r")
                schedule = pickle.load(schedule_file)
                schedule_file.close()

            except Exception, e:
                logger.error('%s', e)

        return schedule


    def load_schedule_tracker(self):
        logger = logging.getLogger()
        playedItems = dict()

        # create the file if it doesnt exist
        if (not os.path.exists(self.schedule_tracker_file)):
            logger.debug('creating file ' + self.schedule_tracker_file)
            schedule_tracker = open(self.schedule_tracker_file, 'w')
            pickle.dump(playedItems, schedule_tracker)
            schedule_tracker.close()
        else:
            try:
                schedule_tracker = open(self.schedule_tracker_file, "r")
                playedItems = pickle.load(schedule_tracker)
                schedule_tracker.close()
            except Exception, e:
                logger.error('Unable to load schedule tracker file: %s', e)

        return playedItems


    def push_liquidsoap(self, pkey, schedule, ptype):
        logger = logging.getLogger()
        src = self.cache_dir + str(pkey) + '/list.lsp'

        try:
            if True == os.access(src, os.R_OK):
                logger.debug('OK - Can read playlist file')

            pl_file = open(src, "r")
            file_content = pl_file.read()
            pl_file.close()
            logger.debug('file content: %s' % (file_content))
            playlist = json.loads(file_content)

            #strptime returns struct_time in local time
            #mktime takes a time_struct and returns a floating point
            #gmtime Convert a time expressed in seconds since the epoch to a struct_time in UTC
            #mktime: expresses the time in local time, not UTC. It returns a floating point number, for compatibility with time().
            epoch_start = calendar.timegm(time.gmtime(time.mktime(time.strptime(pkey, '%Y-%m-%d-%H-%M-%S'))))

            #Return the time as a floating point number expressed in seconds since the epoch, in UTC.
            epoch_now = time.time()

            logger.debug("Epoch start: " + str(epoch_start))
            logger.debug("Epoch now: " + str(epoch_now))

            sleep_time = epoch_start - epoch_now;

            if sleep_time < 0:
                sleep_time = 0

            logger.debug('sleeping for %s s' % (sleep_time))
            time.sleep(sleep_time)

            tn = telnetlib.Telnet(LS_HOST, 1234)

            #skip the currently playing song if any.
            logger.debug("source.skip\n")
            tn.write("source.skip\n")

            # Get any extra information for liquidsoap (which will be sent back to us)
            liquidsoap_data = self.api_client.get_liquidsoap_data(pkey, schedule)

            #Sending schedule table row id string.
            logger.debug("vars.pypo_data %s\n"%(str(liquidsoap_data["schedule_id"])))
            tn.write("vars.pypo_data %s\n"%(str(liquidsoap_data["schedule_id"])))

            for item in playlist:
                annotate = str(item['annotate'])
                logger.debug(annotate)
                tn.write('queue.push %s' % (annotate))
                tn.write("\n")

            tn.write("exit\n")
            logger.debug(tn.read_all())

            status = 1
        except Exception, e:
            logger.error('%s', e)
            status = 0

        return status

    def check_schedule(self, export_source):
        logger = logging.getLogger()

        try:
            schedule_file = open(self.schedule_file, "r")
            schedule = pickle.load(schedule_file)
            schedule_file.close()

        except Exception, e:
            logger.error("%s", e)
            schedule = None

        for pkey in sorted(schedule.iterkeys()):
            playlist = schedule[pkey]
            print '*****************************************'
            print '\033[0;32m%s %s\033[m' % ('scheduled at:', str(pkey))
            print 'cached at :   ' + self.cache_dir + str(pkey)
            print 'subtype:      ' + str(playlist['subtype'])
            print 'played:       ' + str(playlist['played'])
            print 'schedule id:  ' + str(playlist['schedule_id'])
            print 'duration:     ' + str(playlist['duration'])
            print 'source id:    ' + str(playlist['x_ident'])
            print '-----------------------------------------'

            for media in playlist['medias']:
                print media

            print


if __name__ == '__main__':
    print '###########################################'
    print '#             *** pypo  ***               #'
    print '#      Liquidsoap + External Scheduler    #'
    print '#            Playout System               #'
    print '###########################################'

    # initialize
    g = Global()
    g.selfcheck()
    po = Playout()

    while True:
        logger = logging.getLogger()
        loops = 0

        if options.test:
            po.test_api()
            sys.exit()

        while options.fetch_scheduler:
            try: po.fetch('scheduler')
            except Exception, e:
                print e
                sys.exit()

            if (loops%2 == 0):
                logger.info("heartbeat\n\n\n\n")
            loops += 1
            time.sleep(POLL_INTERVAL)

        while options.push_scheduler:
            po.push('scheduler')

            try: po.push('scheduler')
            except Exception, e:
                print 'PUSH ERROR!! WILL EXIT NOW:('
                print e
                sys.exit()

            if (loops%60 == 0):
                logger.info("heartbeat")

            loops += 1
            time.sleep(PUSH_INTERVAL)

        while options.check:
            try: po.check_schedule()
            except Exception, e:
                print e
            sys.exit()

        while options.cleanup:
            try: po.cleanup('scheduler')
            except Exception, e:
                print e
            sys.exit()
        sys.exit()
