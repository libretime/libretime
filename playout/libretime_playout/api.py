from datetime import datetime, timedelta, time
from dateutil.parser import isoparse

from libretime_shared.datetime import time_in_milliseconds, time_in_seconds
from libretime_api_client.v2 import ApiClient

DATETIME_FMT = "%Y-%m-%d-%H-%M-%S"


def get_schedule(api_client: ApiClient):
    current_time = datetime.utcnow()
    end_time = current_time + timedelta(days=1)

    str_current = current_time.isoformat(timespec="seconds")
    str_end = end_time.isoformat(timespec="seconds")
    data = api_client.list_schedule(
        params={
            "ends__range": (f"{str_current}Z,{str_end}Z"),
            "is_valid": True,
            "playout_status__gt": 0,
        }
    )

    result = {}
    for item in data:
        start = isoparse(item["starts"])
        start_key = start.strftime("%Y-%m-%d-%H-%M-%S")
        end = isoparse(item["ends"])
        end_key = end.strftime("%Y-%m-%d-%H-%M-%S")

        show_instance = api_client.get_show_instance(id=item["instance_id"])
        show = api_client.get_show(id=show_instance["show_id"])

        result[start_key] = {
            "start": start_key,
            "end": end_key,
            "row_id": item["id"],
            "show_name": show["name"],
        }
        current = result[start_key]
        if item["file"]:
            current["independent_event"] = False
            current["type"] = "file"
            current["id"] = item["file_id"]

            fade_in = time_in_milliseconds(time.fromisoformat(item["fade_in"]))
            fade_out = time_in_milliseconds(time.fromisoformat(item["fade_out"]))

            cue_in = time_in_seconds(time.fromisoformat(item["cue_in"]))
            cue_out = time_in_seconds(time.fromisoformat(item["cue_out"]))

            current["fade_in"] = fade_in
            current["fade_out"] = fade_out
            current["cue_in"] = cue_in
            current["cue_out"] = cue_out

            info = api_client.get_file(id=item["file_id"])
            current["metadata"] = info
            current["uri"] = item["file"]
            current["replay_gain"] = info["replay_gain"]
            current["filesize"] = info["filesize"]
        elif item["stream"]:
            current["independent_event"] = True
            current["id"] = item["stream_id"]
            info = api_client.get_webstream(id=item["stream_id"])
            current["uri"] = info["url"]
            current["type"] = "stream_buffer_start"
            # Stream events are instantaneous
            current["end"] = current["start"]

            result[f"{start_key}_0"] = {
                "id": current["id"],
                "type": "stream_output_start",
                "start": current["start"],
                "end": current["start"],
                "uri": current["uri"],
                "row_id": current["row_id"],
                "independent_event": current["independent_event"],
            }

            result[end_key] = {
                "type": "stream_buffer_end",
                "start": current["end"],
                "end": current["end"],
                "uri": current["uri"],
                "row_id": current["row_id"],
                "independent_event": current["independent_event"],
            }

            result[f"{end_key}_0"] = {
                "type": "stream_output_end",
                "start": current["end"],
                "end": current["end"],
                "uri": current["uri"],
                "row_id": current["row_id"],
                "independent_event": current["independent_event"],
            }

    return {"media": result}
