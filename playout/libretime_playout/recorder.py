import datetime
import json
import math
import os
import re
import signal
import time
from datetime import timezone
from subprocess import PIPE, Popen
from threading import Thread

import mutagen
from libretime_api_client.v1 import ApiClient as LegacyClient
from loguru import logger

from libretime_playout.config import PUSH_INTERVAL, RECORD_DIR, Config

try:
    from zoneinfo import ZoneInfo
except ImportError:
    from backports.zoneinfo import ZoneInfo


# TODO : add docstrings everywhere in this module


def getDateTimeObj(time):
    # TODO : clean up for this function later.
    # - use tuples to parse result from split (instead of indices)
    # - perhaps validate the input before doing dangerous casts?
    # - rename this function to follow the standard convention
    # - rename time to something else so that the module name does not get
    #   shadowed
    # - add docstring to document all behaviour of this function
    timeinfo = time.split(" ")
    date = [int(x) for x in timeinfo[0].split("-")]
    my_time = [int(x) for x in timeinfo[1].split(":")]
    return datetime.datetime(
        date[0], date[1], date[2], my_time[0], my_time[1], my_time[2], 0, None
    )


class ShowRecorder(Thread):
    name = "show_recorder"

    def __init__(
        self,
        show_instance,
        show_name,
        filelength,
        start_time,
        config: Config,
        legacy_client: LegacyClient,
    ):
        Thread.__init__(self)
        self.legacy_client = legacy_client
        self.config = config
        self.filelength = filelength
        self.start_time = start_time
        self.show_instance = show_instance
        self.show_name = show_name
        self.p = None

    def record_show(self):
        length = str(self.filelength)
        filename = self.start_time
        filename = filename.replace(" ", "-")

        joined_path = os.path.join(RECORD_DIR, filename)
        filepath = f"{joined_path}.{self.config.playout.record_file_format}"

        br = self.config.playout.record_bitrate
        sr = self.config.playout.record_samplerate
        c = self.config.playout.record_channels
        ss = self.config.playout.record_sample_size

        # -f:16,2,44100
        # -b:256
        command = "ecasound -f:{},{},{} -i alsa -o {},{}000 -t:{}".format(
            ss,
            c,
            sr,
            filepath,
            br,
            length,
        )
        args = command.split(" ")

        logger.info("starting record")
        logger.info("command " + command)

        self.p = Popen(args, stdout=PIPE, stderr=PIPE)

        # blocks at the following line until the child process
        # quits
        self.p.wait()
        outmsgs = self.p.stdout.readlines()
        for msg in outmsgs:
            m = re.search("^ERROR", msg)
            if not m == None:
                logger.info("Recording error is found: %s", outmsgs)
        logger.info("finishing record, return code %s", self.p.returncode)
        code = self.p.returncode

        self.p = None

        return code, filepath

    def cancel_recording(self):
        # send signal interrupt (2)
        logger.info("Show manually cancelled!")
        if self.p is not None:
            self.p.send_signal(signal.SIGINT)

    # if self.p is defined, then the child process ecasound is recording
    def is_recording(self):
        return self.p is not None

    def upload_file(self, filepath):

        filename = os.path.split(filepath)[1]

        # files is what requests actually expects
        files = {
            "file": open(filepath, "rb"),
            "name": filename,
            "show_instance": self.show_instance,
        }

        self.legacy_client.upload_recorded_show(files, self.show_instance)

    def set_metadata_and_save(self, filepath):
        """
        Writes song to 'filepath'. Uses metadata from:
            self.start_time, self.show_name, self.show_instance
        """
        try:
            full_date, full_time = self.start_time.split(" ", 1)
            # No idea why we translated - to : before
            # full_time = full_time.replace(":","-")
            logger.info("time: %s" % full_time)
            artist = "Airtime Show Recorder"
            # set some metadata for our file daemon
            recorded_file = mutagen.File(filepath, easy=True)
            recorded_file["artist"] = artist
            recorded_file["date"] = full_date
            recorded_file["title"] = "{}-{}-{}".format(
                self.show_name, full_date, full_time
            )
            # You cannot pass ints into the metadata of a file. Even tracknumber needs to be a string
            recorded_file["tracknumber"] = self.show_instance
            recorded_file.save()

        except Exception as exception:
            logger.exception(exception)

    def run(self):
        code, filepath = self.record_show()

        if code == 0:
            try:
                logger.info("Preparing to upload %s" % filepath)

                self.set_metadata_and_save(filepath)

                self.upload_file(filepath)
                os.remove(filepath)
            except Exception as exception:
                logger.exception(exception)
        else:
            logger.info("problem recording show")
            os.remove(filepath)


class Recorder(Thread):
    name = "recorder"
    daemon = True

    def __init__(self, q, config: Config, legacy_client: LegacyClient):
        Thread.__init__(self)
        self.legacy_client = legacy_client
        self.config = config
        self.sr = None
        self.shows_to_record = {}
        self.server_timezone = ""
        self.queue = q
        self.loops = 0
        logger.info("RecorderFetch: init complete")

        success = False
        while not success:
            try:
                self.legacy_client.register_component("show-recorder")
                success = True
            except Exception as exception:
                logger.exception(exception)
                time.sleep(10)

    def handle_message(self):
        if not self.queue.empty():
            message = self.queue.get()
            try:
                message = message.decode()
            except (UnicodeDecodeError, AttributeError):
                pass
            msg = json.loads(message)
            command = msg["event_type"]
            logger.info("Received msg from Pypo Message Handler: %s", msg)
            if command == "cancel_recording":
                if self.currently_recording():
                    self.cancel_recording()
            else:
                self.process_recorder_schedule(msg)
                self.loops = 0

        if self.shows_to_record:
            self.start_record()

    def process_recorder_schedule(self, m):
        logger.info("Parsing recording show schedules...")
        temp_shows_to_record = {}
        shows = m["shows"]
        for show in shows:
            show_starts = getDateTimeObj(show["starts"])
            show_end = getDateTimeObj(show["ends"])
            time_delta = show_end - show_starts

            temp_shows_to_record[show["starts"]] = [
                time_delta,
                show["instance_id"],
                show["name"],
                m["server_timezone"],
            ]
        self.shows_to_record = temp_shows_to_record

    def get_time_till_next_show(self):
        if len(self.shows_to_record) != 0:
            tnow = datetime.datetime.utcnow()
            sorted_show_keys = sorted(self.shows_to_record.keys())

            start_time = sorted_show_keys[0]
            next_show = getDateTimeObj(start_time)

            delta = next_show - tnow
            s = f"{delta.seconds}.{delta.microseconds}"
            out = float(s)

            if out < 5:
                logger.debug("Shows %s", self.shows_to_record)
                logger.debug("Next show %s", next_show)
                logger.debug("Now %s", tnow)
        return out

    def cancel_recording(self):
        self.sr.cancel_recording()
        self.sr = None

    def currently_recording(self):
        if self.sr is not None and self.sr.is_recording():
            return True
        else:
            return False

    def start_record(self):
        if len(self.shows_to_record) == 0:
            return None
        try:
            delta = self.get_time_till_next_show()
            if delta < 5:
                logger.debug("sleeping %s seconds until show", delta)
                time.sleep(delta)

                sorted_show_keys = sorted(self.shows_to_record.keys())
                start_time = sorted_show_keys[0]
                show_length = self.shows_to_record[start_time][0]
                show_instance = self.shows_to_record[start_time][1]
                show_name = self.shows_to_record[start_time][2]
                server_timezone = self.shows_to_record[start_time][3]

                server_tz = ZoneInfo(server_timezone)
                start_time_on_UTC = getDateTimeObj(start_time)
                start_time_on_server = start_time_on_UTC.replace(
                    tzinfo=timezone.utc
                ).astimezone(server_tz)

                start_time_formatted = (
                    "%(year)d-%(month)02d-%(day)02d %(hour)02d:%(min)02d:%(sec)02d"
                    % {
                        "year": start_time_on_server.year,
                        "month": start_time_on_server.month,
                        "day": start_time_on_server.day,
                        "hour": start_time_on_server.hour,
                        "min": start_time_on_server.minute,
                        "sec": start_time_on_server.second,
                    }
                )

                seconds_waiting = 0

                # avoiding CC-5299
                while True:
                    if self.currently_recording():
                        logger.info("Previous record not finished, sleeping 100ms")
                        seconds_waiting = seconds_waiting + 0.1
                        time.sleep(0.1)
                    else:
                        show_length_seconds = show_length.seconds - seconds_waiting

                        self.sr = ShowRecorder(
                            show_instance,
                            show_name,
                            show_length_seconds,
                            start_time_formatted,
                            self.config,
                            self.legacy_client,
                        )
                        self.sr.start()
                        break

                # remove show from shows to record.
                del self.shows_to_record[start_time]
                # self.time_till_next_show = self.get_time_till_next_show()
        except Exception as exception:
            logger.exception(exception)

    def run(self):
        """
        Main loop of the thread:
        Wait for schedule updates from RabbitMQ, but in case there aren't any,
        poll the server to get the upcoming schedule.
        """
        try:
            logger.info("Started...")
            # Bootstrap: since we are just starting up, we need to grab the
            # most recent schedule.  After that we can just wait for updates.
            try:
                temp = self.legacy_client.get_shows_to_record()
                if temp is not None:
                    self.process_recorder_schedule(temp)
                logger.info("Bootstrap recorder schedule received: %s", temp)
            except Exception as exception:
                logger.exception(exception)

            logger.info("Bootstrap complete: got initial copy of the schedule")

            self.loops = 0
            heartbeat_period = math.floor(30 / PUSH_INTERVAL)

            while True:
                if self.loops * PUSH_INTERVAL > 3600:
                    self.loops = 0
                    # Fetch recorder schedule
                    try:
                        temp = self.legacy_client.get_shows_to_record()
                        if temp is not None:
                            self.process_recorder_schedule(temp)
                        logger.info("updated recorder schedule received: %s", temp)
                    except Exception as exception:
                        logger.exception(exception)

                try:
                    self.handle_message()
                except Exception as exception:
                    logger.exception(exception)

                time.sleep(PUSH_INTERVAL)
                self.loops += 1

        except Exception as exception:
            logger.exception(exception)
