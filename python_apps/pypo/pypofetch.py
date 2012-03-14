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
import copy
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
    def __init__(self, pypoFetch_q, pypoPush_q, media_q):
        Thread.__init__(self)
        self.api_client = api_client.api_client_factory(config)
        self.fetch_queue = pypoFetch_q
        self.push_queue = pypoPush_q
        self.media_prepare_queue = media_q
        
        self.logger = logging.getLogger();
        
        self.cache_dir = os.path.join(config["cache_dir"], "scheduler")
        self.logger.debug("Cache dir %s", self.cache_dir)

        try:
            if not os.path.isdir(dir):
                """
                We get here if path does not exist, or path does exist but
                is a file. We are not handling the second case, but don't 
                think we actually care about handling it.
                """
                self.logger.debug("Cache dir does not exist. Creating...")
                os.makedirs(dir)
        except Exception, e:
            pass
        
        self.schedule_data = []
        self.logger.info("PypoFetch: init complete")
    
    """
    Handle a message from RabbitMQ, put it into our yucky global var.
    Hopefully there is a better way to do this.
    """
    def handle_message(self, message):
        try:        
            self.logger.info("Received event from Pypo Message Handler: %s" % message)
            
            m =  json.loads(message)
            command = m['event_type']
            self.logger.info("Handling command: " + command)
        
            if command == 'update_schedule':
                self.schedule_data  = m['schedule']
                self.process_schedule(self.schedule_data, False)
            elif command == 'update_stream_setting':
                self.logger.info("Updating stream setting...")
                self.regenerateLiquidsoapConf(m['setting'])
            elif command == 'update_stream_format':
                self.logger.info("Updating stream format...")
                self.update_liquidsoap_stream_format(m['stream_format'])
            elif command == 'update_station_name':
                self.logger.info("Updating station name...")
                self.update_liquidsoap_station_name(m['station_name'])
            elif command == 'cancel_current_show':
                self.logger.info("Cancel current show command received...")
                self.stop_current_show()
            elif command == 'switch_source':
                self.logger.info("switch_on_source show command received...")
                self.switch_source(m['sourcename'], m['status'])
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", top)
            self.logger.error("Exception in handling Message Handler message: %s", e)

    def switch_source(self, sourcename, status):
        self.logger.debug('Switching source: %s to "%s" status', sourcename, status)
        command = "streams."
        if(sourcename == "master_dj"):
            command += "master_dj_"
        elif(sourcename == "live_dj"):
            command += "live_dj_"
        elif(sourcename == "scheduled_play"):
            command += "scheduled_play_"
            
        if(status == "on"):
            command += "start\n"
        else:
            command += "stop\n"
            
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.debug(e)
            self.logger.debug('Could not connect to liquidsoap')
            
    """
        This check current switch status from Airtime and update the status
    """
    def check_switch_status(self):
        self.logger.debug('Checking current switch status with Airtime')
        switch_status = self.api_client.get_switch_status()
        self.logger.debug('switch_status:%s',switch_status)
        for k, v in switch_status['status'].iteritems():
            self.switch_source(k, v)
        
    def stop_current_show(self):
        self.logger.debug('Notifying Liquidsoap to stop playback.')
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write('source.skip\n')
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.debug(e)
            self.logger.debug('Could not connect to liquidsoap')
    
    def regenerateLiquidsoapConf(self, setting_p):
        existing = {}
        # create a temp file
        fh = open('/etc/airtime/liquidsoap.cfg', 'r')
        self.logger.info("Reading existing config...")
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
        
        self.logger.info("Looking for changes...")
        setting = sorted(setting_p.items())
        # look for changes
        for k, s in setting:
            if "output_sound_device" in s[u'keyname'] or "icecast_vorbis_metadata" in s[u'keyname']:
                dump, stream = s[u'keyname'].split('_', 1)
                state_change_restart[stream] = False
                # This is the case where restart is required no matter what
                if (existing[s[u'keyname']] != s[u'value']):
                    self.logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
                    restart = True;
            elif "master_live_stream_port" in s[u'keyname'] or "master_live_stream_mp" in s[u'keyname'] or "dj_live_stream_port" in s[u'keyname'] or "dj_live_stream_mp" in s[u'keyname']:
                if (existing[s[u'keyname']] != s[u'value']):
                    self.logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
                    restart = True;
            else:
                stream, dump = s[u'keyname'].split('_',1)
                if "_output" in s[u'keyname']:
                    if (existing[s[u'keyname']] != s[u'value']):
                        self.logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
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
                        self.logger.info("Keyname: %s, Curent value: %s, New Value: %s", s[u'keyname'], existing[s[u'keyname']], s[u'value'])
                        change[stream] = True
                        
        # set flag change for sound_device alway True
        self.logger.info("Change:%s, State_Change:%s...", change, state_change_restart)
        
        for k, v in state_change_restart.items():
            if k == "sound_device" and v:
                restart = True
            elif v and change[k]:
                self.logger.info("'Need-to-restart' state detected for %s...", k)
                restart = True
        # rewrite
        if restart:
            fh = open('/etc/airtime/liquidsoap.cfg', 'w')
            self.logger.info("Rewriting liquidsoap.cfg...")
            fh.write("################################################\n")
            fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
            fh.write("################################################\n")
            for k, d in setting:
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
            self.logger.info("Restarting pypo...")
            sys.exit(0)
        else:
            self.logger.info("No change detected in setting...")
            self.update_liquidsoap_connection_status()

    def update_liquidsoap_connection_status(self):
        """
        updates the status of liquidsoap connection to the streaming server
        This fucntion updates the bootup time variable in liquidsoap script
        """
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
        self.logger.info(streams)
        
        fake_time = current_time + 1
        for s in streams:
            info = s.split(':')
            stream_id = info[0]
            status = info[1]
            if(status == "true"):
                self.api_client.notify_liquidsoap_status("OK", stream_id, str(fake_time))
                
    def update_liquidsoap_stream_format(self, stream_format):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.logger.info(LS_HOST)
            self.logger.info(LS_PORT)
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.stream_metadata_type %s\n' % stream_format).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error("Exception %s", e)
    
    def update_liquidsoap_station_name(self, station_name):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.logger.info(LS_HOST)
            self.logger.info(LS_PORT)
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.station_name %s\n' % station_name).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error("Exception %s", e)

    """
    Process the schedule
     - Reads the scheduled entries of a given range (actual time +/- "prepare_ahead" / "cache_for")
     - Saves a serialized file of the schedule
     - playlists are prepared. (brought to liquidsoap format) and, if not mounted via nsf, files are copied
       to the cache dir (Folder-structure: cache/YYYY-MM-DD-hh-mm-ss)
     - runs the cleanup routine, to get rid of unused cached files
    """
    def process_schedule(self, schedule_data, bootstrapping):      
        self.logger.debug(schedule_data)
        media = schedule_data["media"]

        # Download all the media and put playlists in liquidsoap "annotate" format
        try:
            
            """
            Make sure cache_dir exists
            """
            download_dir = self.cache_dir
            try:
                os.makedirs(download_dir)
            except Exception, e:
                pass
            
            for key in media:
                media_item = media[key]
                
                fileExt = os.path.splitext(media_item['uri'])[1]
                dst = os.path.join(download_dir, media_item['id']+fileExt)
                media_item['dst'] = dst
             
            self.media_prepare_queue.put(copy.copy(media))
            self.prepare_media(media, bootstrapping)
        except Exception, e: self.logger.error("%s", e)

        # Send the data to pypo-push
        self.logger.debug("Pushing to pypo-push")
        self.push_queue.put(media)


        # cleanup
        try: self.cleanup(media)
        except Exception, e: self.logger.error("%s", e)

        

    def prepare_media(self, media, bootstrapping):
        """
        Iterate through the list of media items in "media" and 
        download them.
        """
        try:
            mediaKeys = sorted(media.iterkeys())
            for mkey in mediaKeys:
                self.logger.debug("Media item starting at %s", mkey)
                media_item = media[mkey]
                
                if bootstrapping:            
                    self.check_for_previous_crash(media_item)
               
                entry = self.create_liquidsoap_annotation(media_item)
                media_item['show_name'] = "TODO"
                media_item["annotation"] = entry
                
        except Exception, e:
            self.logger.error("%s", e)
                
        return media
    

    def create_liquidsoap_annotation(self, media):
        """entry = \
            'annotate:media_id="%s",liq_start_next="%s",liq_fade_in="%s",liq_fade_out="%s",liq_cue_in="%s",liq_cue_out="%s",schedule_table_id="%s":%s' \
            % (media['id'], 0, \
            float(media['fade_in']) / 1000, \
            float(media['fade_out']) / 1000, \
            float(media['cue_in']), \
            float(media['cue_out']), \
            media['row_id'], media['dst'])"""
        
        entry = \
            'annotate:media_id="%s",liq_start_next="%s",liq_cue_in="%s",liq_cue_out="%s",schedule_table_id="%s":%s' \
            % (media['id'], 0, \
            float(media['cue_in']), \
            float(media['cue_out']), \
            media['row_id'], media['dst'])
        
        return entry
        
    def check_for_previous_crash(self, media_item):
        start = media_item['start']
        end = media_item['end']
        
        dtnow = datetime.utcnow()
        str_tnow_s = dtnow.strftime('%Y-%m-%d-%H-%M-%S')
                        
        if start <= str_tnow_s and str_tnow_s < end:
            #song is currently playing and we just started pypo. Maybe there
            #was a power outage? Let's restart playback of this song.
            start_split = map(int, start.split('-'))
            media_start = datetime(start_split[0], start_split[1], start_split[2], start_split[3], start_split[4], start_split[5], 0, None)
            self.logger.debug("Found media item that started at %s.", media_start)
            
            delta = dtnow - media_start #we get a TimeDelta object from this operation
            self.logger.info("Starting media item  at %d second point", delta.seconds)
            
            """
            Set the cue_in. This is used by Liquidsoap to determine at what point in the media
            item it should start playing. If the cue_in happens to be > cue_out, then make cue_in = cue_out
            """
            media_item['cue_in'] = delta.seconds + 10 if delta.seconds + 10 < media_item['cue_out'] else media_item['cue_out']
            
            """
            Set the start time, which is used by pypo-push to determine when a media item is scheduled.
            Pushing the start time into the future will ensure pypo-push will push this to Liquidsoap.
            """
            td = timedelta(seconds=10)
            media_item['start'] = (dtnow + td).strftime('%Y-%m-%d-%H-%M-%S')
            self.logger.info("Crash detected, setting playlist to restart at %s", (dtnow + td).strftime('%Y-%m-%d-%H-%M-%S'))
    
    def handle_media_file(self, media_item, dst):
        """
        Download and cache the media item.
        """
        
        self.logger.debug("Processing track %s", media_item['uri'])

        try:
            #blocking function to download the media item
            #self.download_file(media_item, dst)
            self.copy_file(media_item, dst)
            
            if os.access(dst, os.R_OK):
                # check filesize (avoid zero-byte files)
                try: 
                    fsize = os.path.getsize(dst)
                    if fsize > 0:
                        return True
                except Exception, e:
                    self.logger.error("%s", e)
                    fsize = 0
            else:
                self.logger.warning("Cannot read file %s.", dst)

        except Exception, e: 
            self.logger.info("%s", e)
            
        return False


    def copy_file(self, media_item, dst):
        """
        Copy the file from local library directory.
        """
        if not os.path.isfile(dst):
            self.logger.debug("copying from %s to local cache %s" % (media_item['uri'], dst))
            try:
                shutil.copy(media_item['uri'], dst)
            except:
                self.logger.error("Could not copy from %s to %s" % (media_item['uri'], dst))
        else:
            #file already exists
            pass


    def download_file(self, media_item, dst):
        """
        Download a file from a remote server and store it in the cache.
        """
        if os.path.isfile(dst):
            pass
            #self.logger.debug("file already in cache: %s", dst)
        else:
            self.logger.debug("try to download %s", media_item['uri'])
            self.api_client.get_media(media_item['uri'], dst)
            
    def cleanup(self, media):
        """
        Get list of all files in the cache dir and remove them if they aren't being used anymore.
        Input dict() media, lists all files that are scheduled or currently playing. Not being in this
        dict() means the file is safe to remove. 
        """
        cached_file_set = set(os.listdir(self.cache_dir))
        scheduled_file_set = set()
        
        for mkey in media:
            media_item = media[mkey]
            fileExt = os.path.splitext(media_item['uri'])[1]
            scheduled_file_set.add(media_item["id"] + fileExt)
        
        unneeded_files = cached_file_set - scheduled_file_set
        
        self.logger.debug("Files to remove " + str(unneeded_files))
        for file in unneeded_files:
            self.logger.debug("Removing %s" % os.path.join(self.cache_dir, file))
            os.remove(os.path.join(self.cache_dir, file))

    def main(self):
        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we can just wait for updates. 
        success, self.schedule_data = self.api_client.get_schedule()
        if success:
            self.logger.info("Bootstrap schedule received: %s", self.schedule_data)
            self.process_schedule(self.schedule_data, True)
            self.check_switch_status()

        loops = 1        
        while True:
            self.logger.info("Loop #%s", loops)
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
            except Exception, e:
                self.logger.error("Exception, %s", e)
                
                success, self.schedule_data = self.api_client.get_schedule()
                if success:
                    self.process_schedule(self.schedule_data, False)

            loops += 1

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
