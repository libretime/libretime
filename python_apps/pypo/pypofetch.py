# -*- coding: utf-8 -*-

import os
import sys
import time
import logging.config
import json
import telnetlib
import copy
from threading import Thread

from Queue import Empty

from api_clients import api_client
from std_err_override import LogWriter

from configobj import ConfigObj

# configure logging
logging.config.fileConfig("logging.cfg")
logger = logging.getLogger()
LogWriter.override_std_err(logger)

#need to wait for Python 2.7 for this..
#logging.captureWarnings(True)

# loading config file
try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    LS_HOST = config['ls_host']
    LS_PORT = config['ls_port']
    #POLL_INTERVAL = int(config['poll_interval'])
    POLL_INTERVAL = 1800


except Exception, e:
    logger.error('Error loading config file: %s', e)
    sys.exit()

class PypoFetch(Thread):
    def __init__(self, pypoFetch_q, pypoPush_q, media_q, telnet_lock):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        self.fetch_queue = pypoFetch_q
        self.push_queue = pypoPush_q
        self.media_prepare_queue = media_q
        self.last_update_schedule_timestamp = time.time()
        self.listener_timeout = POLL_INTERVAL

        self.telnet_lock = telnet_lock

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

            m = json.loads(message)
            command = m['event_type']
            self.logger.info("Handling command: " + command)

            if command == 'update_schedule':
                self.schedule_data = m['schedule']
                self.process_schedule(self.schedule_data)
            elif command == 'update_stream_setting':
                self.logger.info("Updating stream setting...")
                self.regenerateLiquidsoapConf(m['setting'])
            elif command == 'update_stream_format':
                self.logger.info("Updating stream format...")
                self.update_liquidsoap_stream_format(m['stream_format'])
            elif command == 'update_station_name':
                self.logger.info("Updating station name...")
                self.update_liquidsoap_station_name(m['station_name'])
            elif command == 'update_transition_fade':
                self.logger.info("Updating transition_fade...")
                self.update_liquidsoap_transition_fade(m['transition_fade'])
            elif command == 'switch_source':
                self.logger.info("switch_on_source show command received...")
                self.switch_source(self.logger, self.telnet_lock, m['sourcename'], m['status'])
            elif command == 'disconnect_source':
                self.logger.info("disconnect_on_source show command received...")
                self.disconnect_source(self.logger, self.telnet_lock, m['sourcename'])

            # update timeout value
            if command == 'update_schedule':
                self.listener_timeout = POLL_INTERVAL
            else:
                self.listener_timeout = self.last_update_schedule_timestamp - time.time() + POLL_INTERVAL
                if self.listener_timeout < 0:
                    self.listener_timeout = 0
            self.logger.info("New timeout: %s" % self.listener_timeout)
        except Exception, e:
            import traceback
            top = traceback.format_exc()
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", top)
            self.logger.error("Exception in handling Message Handler message: %s", e)

    @staticmethod
    def disconnect_source(logger, lock, sourcename):
        logger.debug('Disconnecting source: %s', sourcename)
        command = ""
        if(sourcename == "master_dj"):
            command += "master_harbor.kick\n"
        elif(sourcename == "live_dj"):
            command += "live_dj_harbor.kick\n"

        lock.acquire()
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.error(str(e))
        finally:
            lock.release()

    @staticmethod
    def switch_source(logger, lock, sourcename, status):
        logger.debug('Switching source: %s to "%s" status', sourcename, status)
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

        lock.acquire()
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            logger.error(str(e))
        finally:
            lock.release()

    """
        grabs some information that are needed to be set on bootstrap time
        and configures them
    """
    def set_bootstrap_variables(self):
        self.logger.debug('Getting information needed on bootstrap from Airtime')
        info = self.api_client.get_bootstrap_info()
        if info == None:
            self.logger.error('Unable to get bootstrap info.. Exiting pypo...')
            sys.exit(1)
        else:
            self.logger.debug('info:%s', info)
            for k, v in info['switch_status'].iteritems():
                self.switch_source(self.logger, self.telnet_lock, k, v)
            self.update_liquidsoap_stream_format(info['stream_label'])
            self.update_liquidsoap_station_name(info['station_name'])
            self.update_liquidsoap_transition_fade(info['transition_fade'])

    def write_liquidsoap_config(self, setting):
        fh = open('/etc/airtime/liquidsoap.cfg', 'w')
        self.logger.info("Rewriting liquidsoap.cfg...")
        fh.write("################################################\n")
        fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
        fh.write("################################################\n")
        for k, d in setting:
            buffer_str = d[u'keyname'] + " = "
            if d[u'type'] == 'string':
                temp = d[u'value']
                buffer_str += '"%s"' % temp
            else:
                temp = d[u'value']
                if temp == "":
                    temp = "0"
                buffer_str += temp

            buffer_str += "\n"
            fh.write(api_client.encode_to(buffer_str))
        fh.write("log_file = \"/var/log/airtime/pypo-liquidsoap/<script>.log\"\n");
        fh.close()
        # restarting pypo.
        # we could just restart liquidsoap but it take more time somehow.
        self.logger.info("Restarting pypo...")
        sys.exit(0)

    def regenerateLiquidsoapConf(self, setting):
        existing = {}
        # create a temp file

        setting = sorted(setting.items())
        try:
            fh = open('/etc/airtime/liquidsoap.cfg', 'r')
        except IOError, e:
            #file does not exist
            self.write_liquidsoap_config(setting)

        self.logger.info("Reading existing config...")
        # read existing conf file and build dict
        while True:
            line = fh.readline()

            # empty line means EOF
            if not line:
                break

            line = line.strip()

            if line[0] == "#":
                continue

            try:
                key, value = line.split('=', 1)
            except ValueError:
                continue
            key = key.strip()
            value = value.strip()
            value = value.replace('"', '')
            if value == '' or value == "0":
                value = ''
            existing[key] = value
        fh.close()

        # dict flag for any change in cofig
        change = {}
        # this flag is to detect disable -> disable change
        # in that case, we don't want to restart even if there are chnges.
        state_change_restart = {}
        #restart flag
        restart = False

        self.logger.info("Looking for changes...")
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
                stream, dump = s[u'keyname'].split('_', 1)
                if "_output" in s[u'keyname']:
                    if (existing[s[u'keyname']] != s[u'value']):
                        self.logger.info("'Need-to-restart' state detected for %s...", s[u'keyname'])
                        restart = True;
                        state_change_restart[stream] = True
                    elif (s[u'value'] != 'disabled'):
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
            self.write_liquidsoap_config(setting)
        else:
            self.logger.info("No change detected in setting...")
            self.update_liquidsoap_connection_status()

    def update_liquidsoap_connection_status(self):
        """
        updates the status of liquidsoap connection to the streaming server
        This fucntion updates the bootup time variable in liquidsoap script
        """

        self.telnet_lock.acquire()
        try:
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            # update the boot up time of liquidsoap. Since liquidsoap is not restarting,
            # we are manually adjusting the bootup time variable so the status msg will get
            # updated.
            current_time = time.time()
            boot_up_time_command = "vars.bootup_time " + str(current_time) + "\n"
            tn.write(boot_up_time_command)
            tn.write("streams.connection_status\n")
            tn.write('exit\n')

            output = tn.read_all()
        except Exception, e:
            self.logger.error(str(e))
        finally:
            self.telnet_lock.release()

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
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.stream_metadata_type %s\n' % stream_format).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error("Exception %s", e)
        finally:
            self.telnet_lock.release()

    def update_liquidsoap_transition_fade(self, fade):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(LS_HOST, LS_PORT)
            command = ('vars.default_dj_fade %s\n' % fade).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n')
            tn.read_all()
        except Exception, e:
            self.logger.error("Exception %s", e)
        finally:
            self.telnet_lock.release()

    def update_liquidsoap_station_name(self, station_name):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.logger.info(LS_HOST)
            self.logger.info(LS_PORT)

            self.telnet_lock.acquire()
            try:
                tn = telnetlib.Telnet(LS_HOST, LS_PORT)
                command = ('vars.station_name %s\n' % station_name).encode('utf-8')
                self.logger.info(command)
                tn.write(command)
                tn.write('exit\n')
                tn.read_all()
            except Exception, e:
                self.logger.error(str(e))
            finally:
                self.telnet_lock.release()
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
    def process_schedule(self, schedule_data):
        self.last_update_schedule_timestamp = time.time()
        self.logger.debug(schedule_data)
        media = schedule_data["media"]
        media_filtered = {}

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
                """
                {u'end': u'2012-07-26-04-05-00', u'fade_out': 500, u'show_name': u'Untitled Show', u'uri': u'http://', 
 u'cue_in': 0, u'start': u'2012-07-26-04-00-00', u'replay_gain': u'0', u'row_id': 16, u'cue_out': 300, u'type': 
 u'stream', u'id': 1, u'fade_in': 500}
                """
                if(media_item['type'] == 'file'):
                    fileExt = os.path.splitext(media_item['uri'])[1]
                    dst = os.path.join(download_dir, media_item['id'] + fileExt)
                    media_item['dst'] = dst
                    media_item['file_ready'] = False
                    media_filtered[key] = media_item

            self.media_prepare_queue.put(copy.copy(media_filtered))
        except Exception, e: self.logger.error("%s", e)

        # Send the data to pypo-push
        self.logger.debug("Pushing to pypo-push")
        self.push_queue.put(media)


        # cleanup
        try: self.cache_cleanup(media)
        except Exception, e: self.logger.error("%s", e)

    def cache_cleanup(self, media):
        """
        Get list of all files in the cache dir and remove them if they aren't being used anymore.
        Input dict() media, lists all files that are scheduled or currently playing. Not being in this
        dict() means the file is safe to remove. 
        """
        cached_file_set = set(os.listdir(self.cache_dir))
        scheduled_file_set = set()

        for mkey in media:
            media_item = media[mkey]
            if media_item['type'] == 'file':
                fileExt = os.path.splitext(media_item['uri'])[1]
                scheduled_file_set.add(media_item["id"] + fileExt)

        expired_files = cached_file_set - scheduled_file_set

        self.logger.debug("Files to remove " + str(expired_files))
        for f in expired_files:
            try:
                self.logger.debug("Removing %s" % os.path.join(self.cache_dir, f))
                os.remove(os.path.join(self.cache_dir, f))
            except Exception, e:
                self.logger.error(e)

    def main(self):
        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we can just wait for updates. 
        success, self.schedule_data = self.api_client.get_schedule()
        if success:
            self.logger.info("Bootstrap schedule received: %s", self.schedule_data)
            self.process_schedule(self.schedule_data)
            self.set_bootstrap_variables()

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
                
                Currently we are checking every POLL_INTERVAL seconds
                """


                message = self.fetch_queue.get(block=True, timeout=self.listener_timeout)
                self.handle_message(message)
            except Empty, e:
                self.logger.info("Queue timeout. Fetching schedule manually")
                success, self.schedule_data = self.api_client.get_schedule()
                if success:
                    self.process_schedule(self.schedule_data)
            except Exception, e:
                import traceback
                top = traceback.format_exc()
                self.logger.error('Exception: %s', e)
                self.logger.error("traceback: %s", top)

            loops += 1

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
