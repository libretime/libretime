# pylint: disable=broad-except

from datetime import datetime, timedelta

from dateutil.parser import isoparse
from loguru import logger

from ..datetime import time_fromisoformat
from ..datetime import time_in_milliseconds as time_in_ms
from ..datetime import time_in_seconds as time_in_s
from .client import ApiClient

DATETIME_FMT = "%Y-%m-%d-%H-%M-%S"


class ApiClientCompat(ApiClient):
    """
    Compatibility layer class on top of ApiClient to provide additional logic.
    """

    def get_schedule(self):
        now = datetime.utcnow()
        end = now + timedelta(days=1)

        ends__range = (
            f"{now.isoformat(timespec='seconds')}Z,"
            f"{end.isoformat(timespec='seconds')}Z"
        )
        data = self.list_schedule(params={"ends__range": ends__range})

        result = {"media": {}}
        for item in data:
            start = isoparse(item["starts"])
            key = start.strftime(DATETIME_FMT)
            end = isoparse(item["ends"])

            show_instance = self.services.get_show_instance(id_=item["instance_id"])
            show = self.get_show(id_=show_instance["show_id"])

            result["media"][key] = {
                "start": start.strftime(DATETIME_FMT),
                "end": end.strftime(DATETIME_FMT),
                "row_id": item["id"],
                "show_name": show["name"],
            }

            current = result["media"][key]
            if item["file"]:
                info = self.services.file_url(id=item["file_id"])
                current = {
                    **current,
                    "independent_event": False,
                    "type": "file",
                    "id": item["file_id"],
                    "fade_in": time_in_ms(time_fromisoformat(item["fade_in"])),
                    "fade_out": time_in_ms(time_fromisoformat(item["fade_out"])),
                    "cue_in": time_in_s(time_fromisoformat(item["cue_in"])),
                    "cue_out": time_in_s(time_fromisoformat(item["cue_out"])),
                    "metadata": info,
                    "uri": item["file"],
                    "replay_gain": info["replay_gain"],
                    "filesize": info["filesize"],
                }

            elif item["stream"]:
                info = self.services.webstream_url(id=item["stream_id"])
                current = {
                    **current,
                    "independent_event": True,
                    "type": "stream_buffer_start",
                    "id": item["stream_id"],
                    "uri": info["url"],
                    "end": current["start"],  # Stream events are instantaneous
                }

                shared = {
                    "uri": current["uri"],
                    "row_id": current["row_id"],
                    "independent_event": current["independent_event"],
                }

                result[f"{key}_0"] = {
                    "id": current["id"],
                    "type": "stream_output_start",
                    "start": current["start"],
                    "end": current["start"],
                    **shared,
                }

                result[end.isoformat()] = {
                    "type": "stream_buffer_end",
                    "start": current["end"],
                    "end": current["end"],
                    **shared,
                }

                result[f"{end.isoformat()}_0"] = {
                    "type": "stream_output_end",
                    "start": current["end"],
                    "end": current["end"],
                    **shared,
                }

        return result
