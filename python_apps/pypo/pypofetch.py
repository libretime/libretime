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
from threading import Thread
from subprocess import Popen, PIPE
from datetime import datetime
from datetime import timedelta
from Queue import Empty
import filecmp

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
    def __init__(self, pypoFetch_q, pypoPush_q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.set_export_source('scheduler')
        self.fetch_queue = pypoFetch_q
        self.push_queue = pypoPush_q
        self.schedule_data = []
        logger = logging.getLogger('fetch')
        logger.info("PypoFetch: init complete")
    
    """
    Handle a message from RabbitMQ, put it into our yucky global var.
    Hopefully there is a better way to do this.
    """
    def handle_message(self, message):
        try:        
            logger = logging.getLogger('fetch')
            logger.info("Received event from Pypo Message Handler: %s" % message)
            
            m =  json.loads(message)
            command = m['event_type']
            logger.info("Handling command: " + command)
        
            if command == 'update_schedule':
                self.schedule_data  = m['schedule']
                self.process_schedule(self.schedule_data, "scheduler", False)
            elif command == 'update_stream_setting':
                logger.info("Updating stream setting...")
                self.regenerateLiquidsoapConf(m['setting'])
            elif command == 'update_stream_format':
                logger.info("Updating stream format...")
                self.update_liquidsoap_stream_format(m['stream_format'])
            elif command == 'update_station_name':
                logger.info("Updating station name...")
                self.update_liquidsoap_station_name(m['station_name'])
            elif command == 'cancel_current_show':
                logger.info("Cancel current show command received...")
                self.stop_current_show()
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            logger.error('Exception: %s', e)
            logger.error("traceback: %s", top)
            logger.error("Exception in handling Message Handler message: %s", e)
        
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
            key, value = line.split(' = ')
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
                fh.write(api_client.encode_to(buffer))
            fh.write("log_file = \"/var/log/airtime/pypo-liquidsoap/<script>.log\"\n");
            fh.close()
            # restarting pypo.
            # we could just restart liquidsoap but it take more time somehow.
            logger.info("Restarting pypo...")
            sys.exit(0)
        else:
            logger.info("No change detected in setting...")
            self.update_liquidsoap_connection_status()
    """
        updates the status of liquidsoap connection to the streaming server
        This fucntion updates the bootup time variable in liquidsoap script
    """
    def update_liquidsoap_connection_status(self):
        logger = logging.getLogger('fetch')
        tn = telnetlib.Telnet(LS_HOST, LS_PORT)
        # update the boot up time of liquidsoap. Since liquidsoap is not restarting,
        # we are manually adjusting the bootup time variable so the status msg will get
        # updated.
        current_time = time.time()
        boot_up_time_command = "vars.bootup_time "+str(current_time)+"\n"
        tn.write(boot_up_time_command)
        tn.write("streams.connection_status\n")
        tn.write('exit\n')
        
        output = tn.read_all()
        output_list = output.split("\r\n")
        stream_info = output_list[2]
        
        # streamin info is in the form of:
        # eg. s1:true,2:true,3:false
        streams = stream_info.split(",")
        logger.info(streams)
        
        fake_time = current_time + 1
        for s in streams:
            info = s.split(':')
            stream_id = info[0]
            status = info[1]
            if(status == "true"):
                self.api_client.notify_liquidsoap_status("OK", stream_id, str(fake_time))
                
        
        
    def set_export_source(self, export_source):
        logger = logging.getLogger('fetch')
        self.export_source = export_source
        self.cache_dir = config["cache_dir"] + self.export_source + '/'
        logger.info("Creating cache directory at %s", self.cache_dir)


    def update_liquidsoap_stream_format(self, stream_format):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            logger = logging.getLogger('fetch')
            logger.info(LS_HOST)
            logger.info(LS_PORT)
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.stream_metadata_type %s\n' % stream_format).encode('utf-8')
            logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.error("Exception %s", e)
    
    def update_liquidsoap_station_name(self, station_name):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            logger = logging.getLogger('fetch')
            logger.info(LS_HOST)
            logger.info(LS_PORT)
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.station_name %s\n' % station_name).encode('utf-8')
            logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.error("Exception %s", e)

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

        # Download all the media and put playlists in liquidsoap "annotate" format
        try:
             liquidsoap_playlists = self.prepare_playlists(playlists, bootstrapping)
        except Exception, e: logger.error("%s", e)

        # Send the data to pypo-push
        scheduled_data = dict()
        scheduled_data['liquidsoap_playlists'] = liquidsoap_playlists
        scheduled_data['schedule'] = playlists
        self.push_queue.put(scheduled_data)

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
                    logger.warning(e)
                
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
        
        dtnow = datetime.utcnow()
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
                    media_start = datetime(start_split[0], start_split[1], start_split[2], start_split[3], start_split[4], start_split[5], 0, None)
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

        try: os.mkdir(self.cache_dir)
        except Exception, e: pass

        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we can just wait for updates. 
        status, self.schedule_data = self.api_client.get_schedule()
        if status == 1:
            logger.info("Bootstrap schedule received: %s", self.schedule_data)
            self.process_schedule(self.schedule_data, "scheduler", True)

        loops = 1
        while True:
            logger.info("Loop #%s", loops)
            try:               
                try:
                    """
                    our simple_queue.get() requires a timeout, in which case we
                    fetch the Airtime schedule manually. It is important to fetch
                    the schedule periodically because if we didn't, we would only 
                    get schedule updates via RabbitMq if the user was constantly 
                    using the Airtime interface. 
                    
                    If the user is not using the interface, RabbitMq messages are not
                    sent, and we will have very stale (or non-existent!) data about the 
                    schedule.
                    
                    Currently we are checking every 3600 seconds (1 hour)
                    """
                    message = self.fetch_queue.get(block=True, timeout=3600)
                    self.handle_message(message)
                except Empty, e:
                    """
                    Queue timeout. Fetching data manually
                    """
                    raise
                except Exception, e:
                    """
                    sleep 5 seconds so that we don't spin inside this
                    while loop and eat all the CPU
                    """
                    time.sleep(5)
                    
                    """
                    There is a problem with the RabbitMq messenger service. Let's
                    log the error and get the schedule via HTTP polling
                    """
                    logger.error("Exception, %s", e)
                    raise
            except Exception, e:
                """
                Fetch Airtime schedule manually
                """
                status, self.schedule_data = self.api_client.get_schedule()
                if status == 1:
                    self.process_schedule(self.schedule_data, "scheduler", False)

            loops += 1

    """
    Main loop of the thread:
    Wait for schedule updates from RabbitMQ, but in case there arent any,
    poll the server to get the upcoming schedule.
    """
    def run(self):
        while True:
            self.main()
