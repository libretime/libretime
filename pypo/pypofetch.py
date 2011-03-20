import os
import sys
import time
import logging
import logging.config
import shutil
import pickle
import random
import string
import json
import telnetlib
import math
from threading import Thread

from api_clients import api_client
from util import CueFile

from configobj import ConfigObj

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('config.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    POLL_INTERVAL = 5

except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

class PypoFetch(Thread):
    def __init__(self, q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.cue_file = CueFile()
        self.set_export_source('scheduler')
        self.queue = q

    def set_export_source(self, export_source):
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
    
    """
    Fetching part of pypo
    - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" / "cache_for")
    - Saves a serialized file of the schedule
    - playlists are prepared. (brought to liquidsoap format) and, if not mounted via nsf, files are copied
      to the cache dir (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
    - runs the cleanup routine, to get rid of unused cashed files
    """
    def fetch(self, export_source):
        #wrapper script for fetching the whole schedule (in json)
        logger = logging.getLogger('fetch')

        try: os.mkdir(self.cache_dir)
        except Exception, e: pass

        # get schedule
        try:
            while self.get_schedule() != 1:
                logger.warning("failed to read from export url")
                time.sleep(1)

        except Exception, e: logger.error("%s", e)

        # prepare the playlists
        try:
            playlists = self.prepare_playlists()
        except Exception, e: logger.error("%s", e)


        scheduled_data = dict()
        scheduled_data['playlists'] = playlists
        scheduled_data['schedule'] = self.schedule
        self.queue.put(scheduled_data)

        # cleanup
        try: self.cleanup(self.export_source)
        except Exception, e: logger.error("%s", e)

    def get_schedule(self):
        logger = logging.getLogger('fetch')
        status, response = self.api_client.get_schedule()

        if status == 1:
            schedule = response['playlists']
            stream_metadata = response['stream_metadata']
            try:
                self.schedule = schedule
                tn = telnetlib.Telnet(LS_HOST, LS_PORT)

                #encode in latin-1 due to telnet protocol not supporting utf-8
                tn.write(('vars.stream_metadata_type %s\n' % stream_metadata['format']).encode('latin-1'))
                tn.write(('vars.station_name %s\n' % stream_metadata['station_name']).encode('latin-1'))

                tn.write('exit\n')
                tn.read_all()

            except Exception, e:
                logger.error("Exception %s", e)
                status = 0

        return status

    """
    Alternative version of playout preparation. Every playlist entry is
    pre-cued if neccessary (cue_in/cue_out != 0) and stored in the
    playlist folder.
    file is eg 2010-06-23-15-00-00/17_cue_10.132-123.321.mp3
    """
    def prepare_playlists(self):
        logger = logging.getLogger('fetch')

        schedule = self.schedule
        playlists = dict()

        # Dont do anything if schedule is empty
        if not schedule:
            logger.debug("Schedule is empty.")
            return playlists

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

                playlists[pkey] = ls_playlist
        except Exception, e:
            logger.info("%s", e)
        return playlists

    def handle_media_file(self, playlist, pkey):
        """
        This handles both remote and local files.
        Returns an updated ls_playlist string.
        """
        ls_playlist = []

        logger = logging.getLogger('fetch')
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
                        entry['show_name'] = playlist['show_name']
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
        logger = logging.getLogger('fetch')
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
        logger = logging.getLogger('fetch')

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

    def run(self):
        loops = 0
        heartbeat_period = math.floor(30/POLL_INTERVAL)
        logger = logging.getLogger('fetch')
        
        while True:
            if loops % heartbeat_period == 0:
                logger.info("heartbeat")
                loops = 0
            try: self.fetch('scheduler')
            except Exception, e:
                logger.error('Pypo Fetch Error, exiting: %s', e)
                sys.exit()
            time.sleep(POLL_INTERVAL)
            loops += 1
