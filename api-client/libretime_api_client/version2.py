###############################################################################
# This file holds the implementations for all the API clients.
#
# If you want to develop a new client, here are some suggestions: Get the fetch
# methods working first, then the push, then the liquidsoap notifier.  You will
# probably want to create a script on your server side to automatically
# schedule a playlist one minute from the current time.
###############################################################################
import logging
from datetime import datetime, timedelta
from typing import Dict

from dateutil.parser import isoparse

from ._config import Config
from .utils import RequestProvider, fromisoformat, time_in_milliseconds, time_in_seconds

LIBRETIME_API_VERSION = "2.0"
EVENT_KEY_FORMAT = "%Y-%m-%d-%H-%M-%S"


def datetime_to_event_key(value: datetime) -> str:
    return value.strftime(EVENT_KEY_FORMAT)


api_endpoints = {}

api_endpoints["version_url"] = "version/"
api_endpoints["schedule_url"] = "schedule/"
api_endpoints["webstream_url"] = "webstreams/{id}/"
api_endpoints["show_instance_url"] = "show-instances/{id}/"
api_endpoints["show_url"] = "shows/{id}/"
api_endpoints["file_url"] = "files/{id}/"
api_endpoints["file_download_url"] = "files/{id}/download/"


class AirtimeApiClient:
    API_BASE = "/api/v2"

    def __init__(self, logger=None, config_path="/etc/libretime/config.yml"):
        self.logger = logger or logging

        config = Config(filepath=config_path)
        self.base_url = config.general.public_url
        self.api_key = config.general.api_key

        self.services = RequestProvider(
            base_url=self.base_url + self.API_BASE,
            api_key=self.api_key,
            endpoints=api_endpoints,
        )

    def get_schedule(self):
        current_time = datetime.utcnow()
        end_time = current_time + timedelta(days=1)

        current_time_str = current_time.isoformat(timespec="seconds")
        end_time_str = end_time.isoformat(timespec="seconds")

        schedule = self.services.schedule_url(
            params={
                "ends__range": (f"{current_time_str}Z,{end_time_str}Z"),
                "is_valid": True,
                "playout_status__gt": 0,
            }
        )

        events = {}
        for item in schedule:
            item["starts"] = isoparse(item["starts"])
            item["ends"] = isoparse(item["ends"])

            show_instance = self.services.show_instance_url(id=item["instance_id"])
            show = self.services.show_url(id=show_instance["show_id"])

            if item["file"]:
                file = self.services.file_url(id=item["file_id"])
                events.update(generate_file_events(item, file, show))

            elif item["stream"]:
                webstream = self.services.webstream_url(id=item["stream_id"])
                events.update(generate_webstream_events(item, webstream, show))

        return {"media": events}

    def update_file(self, file_id, payload):
        data = self.services.file_url(id=file_id)
        data.update(payload)
        return self.services.file_url(id=file_id, _put_data=data)


def generate_file_events(
    schedule: dict,
    file: dict,
    show: dict,
) -> Dict[str, dict]:
    """
    Generate events for a scheduled file.
    """
    events = {}

    schedule_start_event_key = datetime_to_event_key(schedule["starts"])
    schedule_end_event_key = datetime_to_event_key(schedule["ends"])

    events[schedule_start_event_key] = {
        "type": "file",
        "independent_event": False,
        "row_id": schedule["id"],
        "start": schedule_start_event_key,
        "end": schedule_end_event_key,
        "uri": file["url"],
        "id": file["id"],
        # Show data
        "show_name": show["name"],
        # Extra data
        "fade_in": time_in_milliseconds(fromisoformat(schedule["fade_in"])),
        "fade_out": time_in_milliseconds(fromisoformat(schedule["fade_out"])),
        "cue_in": time_in_seconds(fromisoformat(schedule["cue_in"])),
        "cue_out": time_in_seconds(fromisoformat(schedule["cue_out"])),
        "metadata": file,
        "replay_gain": file["replay_gain"],
        "filesize": file["filesize"],
    }

    return events


def generate_webstream_events(
    schedule: dict,
    webstream: dict,
    show: dict,
) -> Dict[str, dict]:
    """
    Generate events for a scheduled webstream.
    """
    events = {}

    schedule_start_event_key = datetime_to_event_key(schedule["starts"])
    schedule_end_event_key = datetime_to_event_key(schedule["ends"])

    events[schedule_start_event_key] = {
        "type": "stream_buffer_start",
        "independent_event": True,
        "row_id": schedule["id"],
        "start": datetime_to_event_key(schedule["starts"] - timedelta(seconds=5)),
        "end": datetime_to_event_key(schedule["starts"] - timedelta(seconds=5)),
        "uri": webstream["url"],
        "id": webstream["id"],
    }

    events[f"{schedule_start_event_key}_0"] = {
        "type": "stream_output_start",
        "independent_event": True,
        "row_id": schedule["id"],
        "start": schedule_start_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
        # Show data
        "show_name": show["name"],
    }

    # NOTE: stream_*_end were previously triggerered 1 second before the schedule end.
    events[schedule_end_event_key] = {
        "type": "stream_buffer_end",
        "independent_event": True,
        "row_id": schedule["id"],
        "start": schedule_end_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
    }

    events[f"{schedule_end_event_key}_0"] = {
        "type": "stream_output_end",
        "independent_event": True,
        "row_id": schedule["id"],
        "start": schedule_end_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
    }

    return events
