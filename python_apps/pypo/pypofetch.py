import os
import sys
import time
import calendar
import logging
import logging.config
import shutil
import random
import string
import json
import telnetlib
import math
import socket
from threading import Thread
from subprocess import Popen, PIPE
from datetime import datetime
from datetime import timedelta
import filecmp

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

from api_clients import api_client

from configobj import ConfigObj

# configure logging
logging.config.fileConfig("logging.cfg")

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    POLL_INTERVAL = int(config['poll_interval'])

except Exception, e:
    logger = logging.getLogger()
    logger.error('Error loading config file: %s', e)
    sys.exit()

class PypoFetch(Thread):
    def __init__(self, q):
        Thread.__init__(self)
        logger = logging.getLogger('fetch')
        self.api_client = api_client.api_client_factory(config)
        self.set_export_source('scheduler')
        self.queue = q
        self.schedule_data = []
        logger.info("PypoFetch: init complete")

    def init_rabbit_mq(self):
        logger = logging.getLogger('fetch')
        logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-pypo", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("pypo-fetch", exchange=schedule_exchange, key="foo")
            self.connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], "/")
            channel = self.connection.channel()
            consumer = Consumer(channel, schedule_queue)
            consumer.register_callback(self.handle_message)
            consumer.consume()
        except Exception, e:
            logger.error(e)
            return False
            
        return True
    
    """
    Handle a message from RabbitMQ, put it into our yucky global var.
    Hopefully there is a better way to do this.
    """
    def handle_message(self, body, message):
        logger = logging.getLogger('fetch')
        logger.info("Received event from RabbitMQ: " + message.body)
        
        m =  json.loads(message.body)
        command = m['event_type']
        logger.info("Handling command: " + command)
    
        if(command == 'update_schedule'):
            self.schedule_data  = m['schedule']
            self.process_schedule(self.schedule_data, "scheduler", False)
        elif (command == 'update_stream_setting'):
            logger.info("Updating stream setting...")
            self.regenerateLiquidsoapConf(m['setting'])
        elif (command == 'cancel_current_show'):
            logger.info("Cancel current show command received...")
            self.stop_current_show()
        # ACK the message to take it off the queue
        message.ack()
        
    def stop_current_show(self):
        logger = logging.getLogger('fetch')
        logger.debug('Notifying Liquidsoap to stop playback.')
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write('source.skip\n')
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.debug(e)
            logger.debug('Could not connect to liquidsoap')
    
    def regenerateLiquidsoapConf(self, setting):
        logger = logging.getLogger('fetch')
        existing = {}
        # create a temp file
        fh = open('/etc/airtime/liquidsoap.cfg', 'r')
        logger.info("Reading existing config...")
        # read existing conf file and build dict
        while 1:
            line = fh.readline()
            if not line:
                break
            
            line = line.strip()
            if line.find('#') == 0:
                continue
            # if empty line
            if not line:
                continue
            key, value = line.split('=')
            key = key.strip()
            value = value.strip()
            value = value.replace('"', '')
            if value == "" or value == "0":
                value = ''
            existing[key] =  value
        fh.close()
        
        # dict flag for any change in cofig
        change = {}
        # this flag is to detect diable -> disable change
        # in that case, we don't want to restart even if there are chnges.
        state_change_restart = {}
        #restart flag
        restart = False
        
        logger.info("Looking for changes...")
        # look for changes
        for s in setting:
            if "output_sound_device" in s[u'keyname'] or "icecast_vorbis_metadata" in s[u'keyname']:
                dump, stream = s[u'keyname'].split('_', 1)
                state_change_restart[stream] = False
                # This is the case where restart is required no matter what
                if (existing[s[u'keyname']] != s[u'value']):
                    logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
                    restart = True;
            else:
                stream, dump = s[u'keyname'].split('_',1)
                if "_output" in s[u'keyname']:
                    if (existing[s[u'keyname']] != s[u'value']):
                        logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
                        restart = True;
                        state_change_restart[stream] = True
                    elif ( s[u'value'] != 'disabled'):
                        state_change_restart[stream] = True
                    else:
                        state_change_restart[stream] = False
                else:
                    # setting inital value
                    if stream not in change:
                        change[stream] = False
                    if not (s[u'value'] == existing[s[u'keyname']]):
                        logger.info("Keyname: %s, Curent value: %s, New Value: %s", s[u'keyname'], existing[s[u'keyname']], s[u'value'])
                        change[stream] = True
                        
        # set flag change for sound_device alway True
        logger.info("Change:%s, State_Change:%s...", change, state_change_restart)
        
        for k, v in state_change_restart.items():
            if k == "sound_device" and v:
                restart = True
            elif v and change[k]:
                logger.info("'Need-to-restart' state detected for %s...", k)
                restart = True
        # rewrite
        if restart:
            fh = open('/etc/airtime/liquidsoap.cfg', 'w')
            logger.info("Rewriting liquidsoap.cfg...")
            fh.write("################################################\n")
            fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
            fh.write("################################################\n")
            for d in setting:
                buffer = d[u'keyname'] + " = "
                if(d[u'type'] == 'string'):
                    temp = d[u'value']
                    if(temp == ""):
                        temp = ""
                    buffer += "\"" + temp + "\""
                else:
                    temp = d[u'value']
                    if(temp == ""):
                        temp = "0"
                    buffer += temp
                buffer += "\n"
                fh.write(buffer)
            fh.write("log_file = \"/var/log/airtime/pypo-liquidsoap/<script>.log\"\n");
            fh.close()
            # restarting pypo.
            # we could just restart liquidsoap but it take more time somehow.
            logger.info("Restarting pypo...")
            #p = Popen("/etc/init.d/airtime-playout restart >/dev/null 2>&1", shell=True)
            #sts = os.waitpid(p.pid, 0)[1]
            sys.exit()
            self.process_schedule(self.schedule_data, "scheduler", False)
        else:
            logger.info("No change detected in setting...")
        
    def set_export_source(self, export_source):
        logger = logging.getLogger('fetch')
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        logger.info("Creating cache directory at %s", self.cache_dir)

    """
    Process the schedule
     - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" / "cache_for")
     - Saves a serialized file of the schedule
     - playlists are prepared. (brought to liquidsoap format) and, if not mounted via nsf, files are copied
       to the cache dir (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
     - runs the cleanup routine, to get rid of unused cached files
    """
    def process_schedule(self, schedule_data, export_source, bootstrapping):
        logger = logging.getLogger('fetch')
        playlists = schedule_data["playlists"]

        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        stream_metadata = schedule_data['stream_metadata']
        try:
            logger.info(LS_HOST)
            logger.info(LS_PORT)
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            #encode in latin-1 due to telnet protocol not supporting utf-8
            tn.write(('vars.stream_metadata_type %s\n' % stream_metadata['format']).encode('latin-1'))
            tn.write(('vars.station_name %s\n' % stream_metadata['station_name']).encode('latin-1'))
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.error("Exception %s", e)
            status = 0

        # Download all the media and put playlists in liquidsoap "annotate" format
        try:
             liquidsoap_playlists = self.prepare_playlists(playlists, bootstrapping)
        except Exception, e: logger.error("%s", e)

        # Send the data to pypo-push
        scheduled_data = dict()
        scheduled_data['liquidsoap_playlists'] = liquidsoap_playlists
        scheduled_data['schedule'] = playlists
        scheduled_data['stream_metadata'] = schedule_data["stream_metadata"]
        self.queue.put(scheduled_data)

        # cleanup
        try: self.cleanup(self.export_source)
        except Exception, e: logger.error("%s", e)


    """
    In this function every audio file is cut as necessary (cue_in/cue_out != 0) 
    and stored in a playlist folder.
    file is e.g. 2010-06-23-15-00-00/17_cue_10.132-123.321.mp3
    """
    def prepare_playlists(self, playlists, bootstrapping):
        logger = logging.getLogger('fetch')

        liquidsoap_playlists = dict()

        # Dont do anything if playlists is empty
        if not playlists:
            logger.debug("Schedule is empty.")
            return liquidsoap_playlists

        scheduleKeys = sorted(playlists.iterkeys())

        try:
            for pkey in scheduleKeys:
                logger.info("Playlist starting at %s", pkey)
                playlist = playlists[pkey]

                # create playlist directory
                try:
                    os.mkdir(self.cache_dir + str(pkey))
                except Exception, e:
                    logger.error(e)

                #June 13, 2011: Commented this block out since we are not currently setting this to '1' 
                #on the server side. Currently using a different method to detect if already played - Martin
                #if int(playlist['played']) == 1:
                #    logger.info("playlist %s already played / sent to liquidsoap, so will ignore it", pkey)
                
                ls_playlist = self.handle_media_file(playlist, pkey, bootstrapping)

                liquidsoap_playlists[pkey] = ls_playlist
        except Exception, e:
            logger.error("%s", e)
        return liquidsoap_playlists


    """
    Download and cache the media files.
    This handles both remote and local files.
    Returns an updated ls_playlist string.
    """
    def handle_media_file(self, playlist, pkey, bootstrapping):
        logger = logging.getLogger('fetch')
        
        ls_playlist = []
        
        dtnow = datetime.today()
        str_tnow_s = dtnow.strftime('%Y-%m-%d-%H-%M-%S')

        sortedKeys = sorted(playlist['medias'].iterkeys())
        
        for key in sortedKeys:
            media = playlist['medias'][key]
            logger.debug("Processing track %s", media['uri'])
            
            if bootstrapping:              
                start = media['start']
                end = media['end']
                
                if end <= str_tnow_s:
                    continue
                elif start <= str_tnow_s and str_tnow_s < end:
                    #song is currently playing and we just started pypo. Maybe there
                    #was a power outage? Let's restart playback of this song.
                    start_split = map(int, start.split('-'))
                    media_start = datetime(start_split[0], start_split[1], start_split[2], start_split[3], start_split[4], start_split[5])
                    logger.debug("Found media item that started at %s.", media_start)
                    
                    delta = dtnow - media_start #we get a TimeDelta object from this operation
                    logger.info("Starting media item  at %d second point", delta.seconds)
                    media['cue_in'] = delta.seconds + 10
                    td = timedelta(seconds=10)
                    playlist['start'] = (dtnow + td).strftime('%Y-%m-%d-%H-%M-%S')
                    logger.info("Crash detected, setting playlist to restart at %s", (dtnow + td).strftime('%Y-%m-%d-%H-%M-%S'))
            

            fileExt = os.path.splitext(media['uri'])[1]
            try:
                dst = "%s%s/%s%s" % (self.cache_dir, pkey, media['id'], fileExt)

                # download media file
                self.handle_remote_file(media, dst)
                
                if True == os.access(dst, os.R_OK):
                    # check filesize (avoid zero-byte files)
                    try: fsize = os.path.getsize(dst)
                    except Exception, e:
                        logger.error("%s", e)
                        fsize = 0

                    if fsize > 0:
                        pl_entry = \
                        'annotate:export_source="%s",media_id="%s",liq_start_next="%s",liq_fade_in="%s",liq_fade_out="%s",liq_cue_in="%s",liq_cue_out="%s",schedule_table_id="%s":%s' \
                        % (media['export_source'], media['id'], 0, \
                            float(media['fade_in']) / 1000, \
                            float(media['fade_out']) / 1000, \
                            float(media['cue_in']), \
                            float(media['cue_out']), \
                            media['row_id'], dst)

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

                    else:
                        logger.warning("zero-size file - skipping %s. will not add it to playlist at %s", media['uri'], dst)

                else:
                    logger.warning("something went wrong. file %s not available. will not add it to playlist", dst)

            except Exception, e: logger.info("%s", e)
        return ls_playlist


    """
    Download a file from a remote server and store it in the cache.
    """
    def handle_remote_file(self, media, dst):
        logger = logging.getLogger('fetch')
        if os.path.isfile(dst):
            pass
            #logger.debug("file already in cache: %s", dst)
        else:
            logger.debug("try to download %s", media['uri'])
            self.api_client.get_media(media['uri'], dst)
    
    """
    Cleans up folders in cache_dir. Look for modification date older than "now - CACHE_FOR"
    and deletes them.
    """
    def cleanup(self, export_source):
        logger = logging.getLogger('fetch')

        offset = 3600 * int(config["cache_for"])
        now = time.time()

        for r, d, f in os.walk(self.cache_dir):
            for dir in d:
                try:
                    timestamp = calendar.timegm(time.strptime(dir, "%Y-%m-%d-%H-%M-%S"))
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
                    logger.error(e)


    def main(self):
        logger = logging.getLogger('fetch')

        while not self.init_rabbit_mq():
            logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

        try: os.mkdir(self.cache_dir)
        except Exception, e: pass

        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we can just wait for updates. 
        status, self.schedule_data = self.api_client.get_schedule()
        if status == 1:
            logger.info("Bootstrap schedule received: %s", self.schedule_data)
            self.process_schedule(self.schedule_data, "scheduler", True)
        logger.info("Bootstrap complete: got initial copy of the schedule")

        loops = 1        
        while True:
            logger.info("Loop #%s", loops)
            try:
                # Wait for messages from RabbitMQ.  Timeout if we
                # dont get any after POLL_INTERVAL.
                self.connection.drain_events(timeout=POLL_INTERVAL)
                # Hooray for globals!
                schedule_data = SCHEDULE_PUSH_MSG
                status = 1
            except socket.timeout, se:
                # We didnt get a message for a while, so poll the server
                # to get an updated schedule. 
                status, schedule_data = self.api_client.get_schedule()
            except Exception, e:
                """
                This Generic exception is thrown whenever the RabbitMQ
                Service is stopped. In this case let's check every few
                seconds to see if it has come back up
                """
                logger.info("Unknown exception")
                return

            #return based on the exception
            
            if status == 1:
                self.process_schedule(schedule_data, "scheduler", False)                
            loops += 1        

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        while True:
            self.main()
