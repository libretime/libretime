# -*- coding: utf-8 -*-

import os
import sys
import time
import logging.config
import json
import telnetlib
import copy
import subprocess
import signal
from datetime import datetime
import mimetypes
from . import pure
from queue import Empty
from threading import Thread, Timer
from subprocess import Popen, PIPE

from api_clients import api_client
from .timeout import ls_timeout


def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)
signal.signal(signal.SIGINT, keyboardInterruptHandler)

logging.captureWarnings(True)

POLL_INTERVAL = 480

class PypoFetch(Thread):

    def __init__(self, pypoFetch_q, pypoPush_q, media_q, telnet_lock, pypo_liquidsoap, config):
        Thread.__init__(self)

        #Hacky...
        PypoFetch.ref = self

        self.api_client = api_client.AirtimeApiClient()
        self.fetch_queue = pypoFetch_q
        self.push_queue = pypoPush_q
        self.media_prepare_queue = media_q
        self.last_update_schedule_timestamp = time.time()
        self.config = config
        self.listener_timeout = POLL_INTERVAL

        self.telnet_lock = telnet_lock

        self.logger = logging.getLogger()

        self.pypo_liquidsoap = pypo_liquidsoap

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
        except Exception as e:
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
            elif command == 'reset_liquidsoap_bootstrap':
                self.set_bootstrap_variables()
            elif command == 'update_stream_setting':
                self.logger.info("Updating stream setting...")
                self.regenerate_liquidsoap_conf(m['setting'])
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
                self.pypo_liquidsoap.\
                        get_telnet_dispatcher().\
                        switch_source(m['sourcename'], m['status'])
            elif command == 'disconnect_source':
                self.logger.info("disconnect_on_source show command received...")
                self.pypo_liquidsoap.get_telnet_dispatcher().\
                        disconnect_source(m['sourcename'])
            else:
                self.logger.info("Unknown command: %s" % command)

            # update timeout value
            if command == 'update_schedule':
                self.listener_timeout = POLL_INTERVAL
            else:
                self.listener_timeout = self.last_update_schedule_timestamp - time.time() + POLL_INTERVAL
                if self.listener_timeout < 0:
                    self.listener_timeout = 0
            self.logger.info("New timeout: %s" % self.listener_timeout)
        except Exception as e:
            self.logger.exception("Exception in handling Message Handler message")


    def switch_source_temp(self, sourcename, status):
        self.logger.debug('Switching source: %s to "%s" status', sourcename, status)
        command = "streams."
        if sourcename == "master_dj":
            command += "master_dj_"
        elif sourcename == "live_dj":
            command += "live_dj_"
        elif sourcename == "scheduled_play":
            command += "scheduled_play_"

        if status == "on":
            command += "start\n"
        else:
            command += "stop\n"

        return command

    """
    Initialize Liquidsoap environment
    """
    def set_bootstrap_variables(self):
        self.logger.debug('Getting information needed on bootstrap from Airtime')
        try:
            info = self.api_client.get_bootstrap_info()
        except Exception as e:
            self.logger.exception('Unable to get bootstrap info.. Exiting pypo...')

        self.logger.debug('info:%s', info)
        commands = []
        for k, v in info['switch_status'].items():
            commands.append(self.switch_source_temp(k, v))

        stream_format = info['stream_label']
        station_name = info['station_name']
        fade = info['transition_fade']

        commands.append(('vars.stream_metadata_type %s\n' % stream_format).encode('utf-8'))
        commands.append(('vars.station_name %s\n' % station_name).encode('utf-8'))
        commands.append(('vars.default_dj_fade %s\n' % fade).encode('utf-8'))
        self.pypo_liquidsoap.get_telnet_dispatcher().telnet_send(commands)

        self.pypo_liquidsoap.clear_all_queues()
        self.pypo_liquidsoap.clear_queue_tracker()

    def restart_liquidsoap(self):
        try:
            """do not block - if we receive the lock then good - no other thread
            will try communicating with Liquidsoap. If we don't receive, it may
            mean some thread blocked and is still holding the lock. Restarting
            Liquidsoap will cause that thread to release the lock as an Exception
            will be thrown."""
            self.telnet_lock.acquire(False)


            self.logger.info("Restarting Liquidsoap")
            subprocess.call('kill -9 `pidof airtime-liquidsoap`', shell=True, close_fds=True)

            #Wait here and poll Liquidsoap until it has started up
            self.logger.info("Waiting for Liquidsoap to start")
            while True:
                try:
                    tn = telnetlib.Telnet(self.config['ls_host'], self.config['ls_port'])
                    tn.write("exit\n")
                    tn.read_all()
                    self.logger.info("Liquidsoap is up and running")
                    break
                except Exception as e:
                    #sleep 0.5 seconds and try again
                    time.sleep(0.5)

        except Exception as e:
            self.logger.exception(e)
        finally:
            if self.telnet_lock.locked():
                self.telnet_lock.release()

    """
    NOTE: This function is quite short after it was refactored.
    """
    def regenerate_liquidsoap_conf(self, setting):
        self.restart_liquidsoap()
        self.update_liquidsoap_connection_status()


    @ls_timeout
    def update_liquidsoap_connection_status(self):
        """
        updates the status of Liquidsoap connection to the streaming server
        This function updates the bootup time variable in Liquidsoap script
        """

        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.config['ls_host'], self.config['ls_port'])
            # update the boot up time of Liquidsoap. Since Liquidsoap is not restarting,
            # we are manually adjusting the bootup time variable so the status msg will get
            # updated.
            current_time = time.time()
            boot_up_time_command = "vars.bootup_time " + str(current_time) + "\n"
            self.logger.info(boot_up_time_command)
            tn.write(boot_up_time_command)

            connection_status = "streams.connection_status\n"
            self.logger.info(connection_status)
            tn.write(connection_status)

            tn.write('exit\n'.encode('ascii'))

            output = tn.read_all()
        except Exception as e:
            self.logger.exception(e)
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


    @ls_timeout
    def update_liquidsoap_stream_format(self, stream_format):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.config['ls_host'], self.config['ls_port'])
            command = ('vars.stream_metadata_type %s\n' % stream_format).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n'.encode('ascii'))
            tn.read_all()
        except Exception as e:
            self.logger.exception(e)
        finally:
            self.telnet_lock.release()

    @ls_timeout
    def update_liquidsoap_transition_fade(self, fade):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(self.config['ls_host'], self.config['ls_port'])
            command = ('vars.default_dj_fade %s\n' % fade).encode('utf-8')
            self.logger.info(command)
            tn.write(command)
            tn.write('exit\n'.encode('ascii'))
            tn.read_all()
        except Exception as e:
            self.logger.exception(e)
        finally:
            self.telnet_lock.release()

    @ls_timeout
    def update_liquidsoap_station_name(self, station_name):
        # Push stream metadata to liquidsoap
        # TODO: THIS LIQUIDSOAP STUFF NEEDS TO BE MOVED TO PYPO-PUSH!!!
        try:
            try:
                self.telnet_lock.acquire()
                tn = telnetlib.Telnet(self.config['ls_host'], self.config['ls_port'])
                command = ('vars.station_name %s\n' % station_name).encode('utf-8')
                self.logger.info(command)
                tn.write(command)
                tn.write('exit\n'.encode('ascii'))
                tn.read_all()
            except Exception as e:
                self.logger.exception(e)
            finally:
                self.telnet_lock.release()
        except Exception as e:
            self.logger.exception(e)

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
            except Exception as e:
                pass

            media_copy = {}
            for key in media:
                media_item = media[key]
                if (media_item['type'] == 'file'):
                    fileExt = self.sanity_check_media_item(media_item)
                    dst = os.path.join(download_dir, "{}{}".format(media_item['id'], fileExt))
                    media_item['dst'] = dst
                    media_item['file_ready'] = False
                    media_filtered[key] = media_item

                media_item['start'] = datetime.strptime(media_item['start'],
                        "%Y-%m-%d-%H-%M-%S")
                media_item['end'] = datetime.strptime(media_item['end'],
                        "%Y-%m-%d-%H-%M-%S")
                media_copy[key] = media_item


            self.media_prepare_queue.put(copy.copy(media_filtered))
        except Exception as e:
            self.logger.exception(e)

        # Send the data to pypo-push
        self.logger.debug("Pushing to pypo-push")
        self.push_queue.put(media_copy)


        # cleanup
        try:
            self.cache_cleanup(media)
        except Exception as e:
            self.logger.exception(e)

    #do basic validation of file parameters. Useful for debugging
    #purposes
    def sanity_check_media_item(self, media_item):
        start = datetime.strptime(media_item['start'], "%Y-%m-%d-%H-%M-%S")
        end = datetime.strptime(media_item['end'], "%Y-%m-%d-%H-%M-%S")

        mime = media_item['metadata']['mime']
        mimetypes.init(["%s/mime.types" % os.path.dirname(os.path.realpath(__file__))])
        mime_ext = mimetypes.guess_extension(mime, strict=False)

        length1 = pure.date_interval_to_seconds(end - start)
        length2 = media_item['cue_out'] - media_item['cue_in']

        if abs(length2 - length1) > 1:
            self.logger.error("end - start length: %s", length1)
            self.logger.error("cue_out - cue_in length: %s", length2)
            self.logger.error("Two lengths are not equal!!!")

        media_item['file_ext'] = mime_ext

        return mime_ext

    def is_file_opened(self, path):
        #Capture stderr to avoid polluting py-interpreter.log
        proc = Popen(["lsof", path], stdout=PIPE, stderr=PIPE)
        out = proc.communicate()[0].strip()
        return bool(out)

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
                if "file_ext" not in media_item.keys():
                    media_item["file_ext"] = mimetypes.guess_extension(media_item['metadata']['mime'], strict=False)
                scheduled_file_set.add("{}{}".format(media_item["id"], media_item["file_ext"]))

        expired_files = cached_file_set - scheduled_file_set

        self.logger.debug("Files to remove " + str(expired_files))
        for f in expired_files:
            try:
                path = os.path.join(self.cache_dir, f)
                self.logger.debug("Removing %s" % path)

                #check if this file is opened (sometimes Liquidsoap is still
                #playing the file due to our knowledge of the track length
                #being incorrect!)
                if not self.is_file_opened(path):
                    os.remove(path)
                    self.logger.info("File '%s' removed" % path)
                else:
                    self.logger.info("File '%s' not removed. Still busy!" % path)
            except Exception as e:
                self.logger.exception("Problem removing file '%s'" % f)

    def manual_schedule_fetch(self):
        success, self.schedule_data = self.api_client.get_schedule()
        if success:
            self.process_schedule(self.schedule_data)
        return success

    def persistent_manual_schedule_fetch(self, max_attempts=1):
        success = False
        num_attempts = 0
        while not success and num_attempts < max_attempts:
            success = self.manual_schedule_fetch()
            num_attempts += 1

        return success

    # This function makes a request to Airtime to see if we need to
    # push metadata to TuneIn. We have to do this because TuneIn turns
    # off metadata if it does not receive a request every 5 minutes.
    def update_metadata_on_tunein(self):
        self.api_client.update_metadata_on_tunein()
        Timer(120, self.update_metadata_on_tunein).start()

    def main(self):
        #Make sure all Liquidsoap queues are empty. This is important in the
        #case where we've just restarted the pypo scheduler, but Liquidsoap still
        #is playing tracks. In this case let's just restart everything from scratch
        #so that we can repopulate our dictionary that keeps track of what 
        #Liquidsoap is playing much more easily.
        self.pypo_liquidsoap.clear_all_queues()

        self.set_bootstrap_variables()

        self.update_metadata_on_tunein()

        # Bootstrap: since we are just starting up, we need to grab the
        # most recent schedule.  After that we fetch the schedule every 8
        # minutes or wait for schedule updates to get pushed.
        success = self.persistent_manual_schedule_fetch(max_attempts=5)

        if success:
            self.logger.info("Bootstrap schedule received: %s", self.schedule_data)

        loops = 1
        while True:
            self.logger.info("Loop #%s", loops)
            manual_fetch_needed = False
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
                manual_fetch_needed = False
                self.handle_message(message)
            except Empty as e:
                self.logger.info("Queue timeout. Fetching schedule manually")
                manual_fetch_needed = True
            except Exception as e:
                self.logger.exception(e)

            try:
                if manual_fetch_needed:
                    self.persistent_manual_schedule_fetch(max_attempts=5)
            except Exception as e:
                self.logger.exception('Failed to manually fetch the schedule.')

            loops += 1

    def run(self):
        """
        Entry point of the thread
        """
        self.main()
        self.logger.info('PypoFetch thread exiting')
