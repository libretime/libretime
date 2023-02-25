from datetime import datetime, time, timedelta
from operator import itemgetter
from typing import Dict

from dateutil.parser import isoparse
from libretime_api_client.v2 import ApiClient
from libretime_shared.datetime import time_in_milliseconds, time_in_seconds

from ..liquidsoap.models import StreamPreferences
from .events import (
    ActionEvent,
    AnyEvent,
    EventKind,
    Events,
    FileEvent,
    WebStreamEvent,
    datetime_to_event_key,
)


def insert_event(events: Events, event_key: str, event: AnyEvent):
    key = event_key

    # Search for an empty slot
    index = 0
    while key in events:
        # Ignore duplicate event
        if event == events[key]:
            return

        key = f"{event_key}_{index}"
        index += 1

    events[key] = event


def get_schedule(api_client: ApiClient) -> Events:
    stream_preferences = StreamPreferences(**api_client.get_stream_preferences().json())

    current_time = datetime.utcnow()
    end_time = current_time + timedelta(days=1)

    current_time_str = current_time.isoformat(timespec="seconds")
    end_time_str = end_time.isoformat(timespec="seconds")

    schedule = api_client.list_schedule(
        params={
            "ends_after": f"{current_time_str}Z",
            "ends_before": f"{end_time_str}Z",
            "overbooked": False,
            "position_status__gt": 0,
        }
    ).json()

    events: Dict[str, AnyEvent] = {}
    for item in sorted(schedule, key=itemgetter("starts_at")):
        item["starts_at"] = isoparse(item["starts_at"])
        item["ends_at"] = isoparse(item["ends_at"])

        show_instance = api_client.get_show_instance(item["instance"]).json()
        show = api_client.get_show(show_instance["show"]).json()

        if show["live_enabled"]:
            show_instance["starts_at"] = isoparse(show_instance["starts_at"])
            show_instance["ends_at"] = isoparse(show_instance["ends_at"])
            generate_live_events(
                events,
                show_instance,
                stream_preferences.input_fade_transition,
            )

        if item["file"]:
            file = api_client.get_file(item["file"]).json()
            generate_file_events(events, item, file, show)

        elif item["stream"]:
            webstream = api_client.get_webstream(item["stream"]).json()
            generate_webstream_events(events, item, webstream, show)

    return dict(sorted(events.items()))


def generate_live_events(
    events: Events,
    show_instance: dict,
    input_fade_transition: float,
):
    transition = timedelta(seconds=input_fade_transition)

    switch_off_event_key = datetime_to_event_key(show_instance["ends_at"] - transition)
    kick_out_event_key = datetime_to_event_key(show_instance["ends_at"])

    # If enabled, fade the input source out
    if switch_off_event_key != kick_out_event_key:
        switch_off_event: ActionEvent = {
            "type": EventKind.ACTION,
            "event_type": "switch_off",
            "start": switch_off_event_key,
            "end": switch_off_event_key,
        }
        insert_event(events, switch_off_event_key, switch_off_event)

    # Then kick the source out
    kick_out_event: ActionEvent = {
        "type": EventKind.ACTION,
        "event_type": "kick_out",
        "start": kick_out_event_key,
        "end": kick_out_event_key,
    }
    insert_event(events, kick_out_event_key, kick_out_event)


def generate_file_events(
    events: Events,
    schedule: dict,
    file: dict,
    show: dict,
):
    """
    Generate events for a scheduled file.
    """
    schedule_start_event_key = datetime_to_event_key(schedule["starts_at"])
    schedule_end_event_key = datetime_to_event_key(schedule["ends_at"])

    event: FileEvent = {
        "type": EventKind.FILE,
        "row_id": schedule["id"],
        "start": schedule_start_event_key,
        "end": schedule_end_event_key,
        "uri": file["url"],
        "id": file["id"],
        # Show data
        "show_name": show["name"],
        # Extra data
        "fade_in": time_in_milliseconds(time.fromisoformat(schedule["fade_in"])),
        "fade_out": time_in_milliseconds(time.fromisoformat(schedule["fade_out"])),
        "cue_in": time_in_seconds(time.fromisoformat(schedule["cue_in"])),
        "cue_out": time_in_seconds(time.fromisoformat(schedule["cue_out"])),
        "metadata": {
            "track_title": file["track_title"],
            "artist_name": file["artist_name"],
            "mime": file["mime"],
        },
        "replay_gain": file["replay_gain"],
        "filesize": file["size"],
    }
    insert_event(events, schedule_start_event_key, event)


def generate_webstream_events(
    events: Events,
    schedule: dict,
    webstream: dict,
    show: dict,
):
    """
    Generate events for a scheduled webstream.
    """
    schedule_start_event_key = datetime_to_event_key(schedule["starts_at"])
    schedule_end_event_key = datetime_to_event_key(schedule["ends_at"])

    stream_buffer_start_event: WebStreamEvent = {
        "type": EventKind.WEB_STREAM_BUFFER_START,
        "row_id": schedule["id"],
        "start": datetime_to_event_key(schedule["starts_at"] - timedelta(seconds=5)),
        "end": datetime_to_event_key(schedule["starts_at"] - timedelta(seconds=5)),
        "uri": webstream["url"],
        "id": webstream["id"],
    }
    insert_event(events, schedule_start_event_key, stream_buffer_start_event)

    stream_output_start_event: WebStreamEvent = {
        "type": EventKind.WEB_STREAM_OUTPUT_START,
        "row_id": schedule["id"],
        "start": schedule_start_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
        # Show data
        "show_name": show["name"],
    }
    insert_event(events, schedule_start_event_key, stream_output_start_event)

    # NOTE: stream_*_end were previously triggered 1 second before
    # the schedule end.
    stream_buffer_end_event: WebStreamEvent = {
        "type": EventKind.WEB_STREAM_BUFFER_END,
        "row_id": schedule["id"],
        "start": schedule_end_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
    }
    insert_event(events, schedule_end_event_key, stream_buffer_end_event)

    stream_output_end_event: WebStreamEvent = {
        "type": EventKind.WEB_STREAM_OUTPUT_END,
        "row_id": schedule["id"],
        "start": schedule_end_event_key,
        "end": schedule_end_event_key,
        "uri": webstream["url"],
        "id": webstream["id"],
    }
    insert_event(events, schedule_end_event_key, stream_output_end_event)
