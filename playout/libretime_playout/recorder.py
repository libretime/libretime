import datetime
import logging
import math
import os
import signal
import sys
import time
from datetime import timezone
from pathlib import Path
from queue import Queue
from threading import Thread
from typing import Any, Dict

import mutagen
import requests
from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_api_client.v2 import ApiClient

from libretime_playout.config import PUSH_INTERVAL, RECORD_DIR, Config

from .liquidsoap.client import LiquidsoapClient

if sys.version_info < (3, 9):
    from backports.zoneinfo import ZoneInfo
else:
    from zoneinfo import ZoneInfo

logger = logging.getLogger(__name__)


def getDateTimeObj(time_str: str) -> datetime.datetime:
    """
    Parse a space-separated date time string (e.g. YYYY-MM-DD HH:MM:SS) on UTC.
    """
    timeinfo = time_str.split(" ")
    date = [int(x) for x in timeinfo[0].split("-")]
    my_time = [int(x) for x in timeinfo[1].split(":")]
    return datetime.datetime(
        date[0], date[1], date[2], my_time[0], my_time[1], my_time[2], 0, timezone.utc
    )


class ShowRecorder(Thread):
    name = "show_recorder"

    def __init__(
        self,
        show_instance: int,
        show_name: str,
        filelength: float,
        start_time: str,
        config: Config,
        legacy_client: LegacyClient,
        liq_client: LiquidsoapClient,
    ):
        Thread.__init__(self)
        self.legacy_client = legacy_client
        self.liq_client = liq_client
        self.config = config
        self.filelength = filelength
        self.start_time = start_time
        self.show_instance = show_instance
        self.show_name = show_name

    def record_show(self):
        length = str(int(self.filelength))
        filename = self.start_time.replace(" ", "-").replace(":", "-")

        record_file_format = self.config.playout.record_file_format

        joined_path = os.path.join(RECORD_DIR, filename)
        filepath = f"{joined_path}.{record_file_format}"
        logger.info("Recording show %s instance %d to %s", self.show_name, self.show_instance, filepath)
        
        self.liq_client.start_recording(
            dict(format=record_file_format, filename=filepath, length=f"{length}")
        )
        
        # Wait until the recording finishes (duration + 10s grace period)
        delay = int(self.filelength) + 10
        time.sleep(delay)

        return 0, filepath

    def upload_file(self, filepath) -> requests.Response:
        filename = os.path.split(filepath)[1]
        try:
            resp = requests.post(
                f"{self.config.general.public_url}/rest/media",
                auth=(self.config.general.api_key, ""),
                files=[
                    ("file", (filename, open(filepath, "rb"))),
                    ("show_instance", str(self.show_instance)),
                ],
                timeout=120,
            )
            resp.raise_for_status()
            return resp
        except requests.exceptions.HTTPError as exception:
            raise RuntimeError(f"could not upload {filepath}") from exception

    def set_metadata_and_save(self, filepath):
        try:
            # mutagen easy mode simplifies tag names across MP3, Ogg Vorbis, FLAC, etc.
            recorded_file = mutagen.File(filepath, easy=True)
            if recorded_file is not None:
                recorded_file["artist"] = "Airtime Show Recorder"
                recorded_file["title"] = f"{self.show_name} - {self.start_time}"
                recorded_file["tracknumber"] = str(self.show_instance)
                recorded_file.save()
        except Exception as exception:
            logger.exception("Failed to write metadata tags: %s", exception)

    def run(self):
        code, filepath = self.record_show()

        if code == 0:
            try:
                logger.info("Preparing to upload %s", filepath)
                self.set_metadata_and_save(filepath)

                # Upload to library via REST endpoint
                resp = self.upload_file(filepath)
                file_id = resp.json().get("id")
                if file_id:
                    # Link uploaded file to show instance and trigger rebroadcast scheduling
                    logger.info("Linking file %s to show instance %s", file_id, self.show_instance)
                    self.legacy_client.upload_recorded_file(self.show_instance, file_id)
                else:
                    logger.error("Could not retrieve file ID from upload response: %s", resp.text)

                os.remove(filepath)
            except Exception as exception:
                logger.exception(exception)
                if os.path.exists(filepath):
                    os.remove(filepath)
        else:
            logger.info("problem recording show")
            if os.path.exists(filepath):
                os.remove(filepath)


class Recorder(Thread):
    name = "recorder"
    daemon = True

    def __init__(
        self,
        recorder_queue: "Queue[Dict[str, Any]]",
        config: Config,
        legacy_client: LegacyClient,
        api_client: ApiClient,
        liq_client: LiquidsoapClient,
    ):
        Thread.__init__(self)
        self.legacy_client = legacy_client
        self.api_client = api_client
        self.liq_client = liq_client
        self.config = config
        self.sr = None
        self.shows_to_record = {}
        self.queue = recorder_queue
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
            msg = self.queue.get()
            command = msg.get("event_type")
            logger.debug("handling event %s: %s", command, msg)
            if command == "cancel_recording":
                # Cancellation logic can be handled or logged here.
                # In modern Liquidsoap model, max_duration will terminate the output file,
                # while API will ignore uploads on deleted instances.
                logger.info("Show recording cancelled by user or calendar changes.")
            else:
                self.fetch_recorder_schedule()
                self.loops = 0

        if self.shows_to_record:
            self.start_record()

    def fetch_recorder_schedule(self):
        try:
            # Query Django API directly for show instances configured to record in the next 2 hours
            now = datetime.datetime.now(timezone.utc)
            starts_after = now.isoformat()
            starts_before = (now + datetime.timedelta(hours=2)).isoformat()
            
            resp = self.api_client.list_shows_to_record(starts_after, starts_before)
            shows = resp.json()
            logger.info("Fetched recorder schedule from Django API: %s", shows)
            self.process_recorder_schedule(shows)
        except Exception as exception:
            logger.exception("Failed to fetch schedule from Django API: %s", exception)

    def process_recorder_schedule(self, shows):
        logger.info("Parsing recording show schedules...")
        temp_shows_to_record = {}
        
        try:
            server_timezone = self.api_client.get_stream_preferences().json().get("timezone", "UTC")
        except Exception:
            server_timezone = "UTC"

        for show in shows:
            # Example starts_at: "2026-06-27T03:46:30Z"
            starts_str = show["starts_at"].replace("Z", "+00:00")
            ends_str = show["ends_at"].replace("Z", "+00:00")
            
            try:
                starts_dt = datetime.datetime.fromisoformat(starts_str)
                ends_dt = datetime.datetime.fromisoformat(ends_str)
            except Exception as exception:
                logger.error("Failed to parse show dates: %s", exception)
                continue

            time_delta = ends_dt - starts_dt
            
            # Use UTC formatted start string as key
            starts_utc_formatted = starts_dt.astimezone(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")

            temp_shows_to_record[starts_utc_formatted] = [
                time_delta,
                show["id"],
                show.get("show_name", "Recorded Show"),
                server_timezone,
            ]
        self.shows_to_record = temp_shows_to_record

    def get_time_till_next_show(self):
        if len(self.shows_to_record) != 0:
            tnow = datetime.datetime.now(timezone.utc)
            sorted_show_keys = sorted(self.shows_to_record.keys())

            start_time = sorted_show_keys[0]
            next_show = getDateTimeObj(start_time)

            delta = next_show - tnow
            s = f"{delta.seconds}.{delta.microseconds}"
            out = float(s)
            
            # If show has already started/is starting now
            if next_show <= tnow:
                out = 0.0

            if out < 5:
                logger.debug("Shows %s", self.shows_to_record)
                logger.debug("Next show %s", next_show)
                logger.debug("Now %s", tnow)
            return out
        return 999999.0

    def currently_recording(self):
        return self.sr is not None and self.sr.is_alive()

    def start_record(self):
        if len(self.shows_to_record) == 0:
            return None
        try:
            delta = self.get_time_till_next_show()
            if delta < 5:
                if delta > 0:
                    logger.debug("sleeping %s seconds until show start", delta)
                    time.sleep(delta)

                sorted_show_keys = sorted(self.shows_to_record.keys())
                start_time = sorted_show_keys[0]
                show_length = self.shows_to_record[start_time][0]
                show_instance = self.shows_to_record[start_time][1]
                show_name = self.shows_to_record[start_time][2]
                server_timezone = self.shows_to_record[start_time][3]

                server_tz = ZoneInfo(server_timezone)
                start_time_on_UTC = getDateTimeObj(start_time)
                start_time_on_server = start_time_on_UTC.astimezone(server_tz)

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

                while True:
                    if self.currently_recording():
                        logger.info("Previous record thread still active, sleeping 100ms")
                        seconds_waiting = seconds_waiting + 0.1
                        time.sleep(0.1)
                    else:
                        show_length_seconds = show_length.total_seconds() - seconds_waiting
                        if show_length_seconds <= 0:
                            logger.warning("Remaining show length is negative or zero, skipping recording.")
                            break

                        self.sr = ShowRecorder(
                            show_instance,
                            show_name,
                            show_length_seconds,
                            start_time_formatted,
                            self.config,
                            self.legacy_client,
                            self.liq_client,
                        )
                        self.sr.start()
                        break

                # remove show from shows to record
                del self.shows_to_record[start_time]
        except Exception as exception:
            logger.exception(exception)

    def run(self):
        try:
            logger.info("Started...")
            # Bootstrap schedule on startup
            self.fetch_recorder_schedule()

            self.loops = 0
            while True:
                # Fetch schedule periodically (every hour)
                if self.loops * PUSH_INTERVAL > 3600:
                    self.loops = 0
                    self.fetch_recorder_schedule()

                try:
                    self.handle_message()
                except Exception as exception:
                    logger.exception(exception)

                time.sleep(PUSH_INTERVAL)
                self.loops += 1

        except Exception as exception:
            logger.exception(exception)
