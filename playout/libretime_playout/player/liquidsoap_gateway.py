import logging
from typing import List

from ..liquidsoap.client import LiquidsoapClient
from ..timeout import ls_timeout
from .events import FileEvent

logger = logging.getLogger(__name__)


def create_liquidsoap_annotation(file_event: FileEvent) -> str:
    # We need liq_start_next value in the annotate. That is the value that controls
    # overlap duration of crossfade.
    annotations = {
        "media_id": file_event["id"],
        "liq_start_next": "0",
        "liq_fade_in": float(file_event["fade_in"]) / 1000,
        "liq_fade_out": float(file_event["fade_out"]) / 1000,
        "liq_cue_in": float(file_event["cue_in"]),
        "liq_cue_out": float(file_event["cue_out"]),
        "schedule_table_id": file_event["row_id"],
        "replay_gain": f"{file_event['replay_gain']} dB",
    }

    # Override the the artist/title that Liquidsoap extracts from a file's metadata with
    # the metadata we get from Airtime. (You can modify metadata in Airtime's library,
    # which doesn't get saved back to the file.)
    if "metadata" in file_event:
        if "artist_name" in file_event["metadata"]:
            artist_name = file_event["metadata"]["artist_name"]
            if artist_name:
                annotations["artist"] = artist_name.replace('"', '\\"')

        if "track_title" in file_event["metadata"]:
            track_title = file_event["metadata"]["track_title"]
            if track_title:
                annotations["title"] = track_title.replace('"', '\\"')

    annotations_str = ",".join(f'{key}="{value}"' for key, value in annotations.items())

    return "annotate:" + annotations_str + ":" + file_event["dst"]


class TelnetLiquidsoap:
    def __init__(
        self,
        liq_client: LiquidsoapClient,
        queues: List[int],
    ):
        self.liq_client = liq_client
        self.queues = queues
        self.current_prebuffering_stream_id = None

    @ls_timeout
    def queue_clear_all(self):
        try:
            self.liq_client.queues_remove(*self.queues)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def queue_remove(self, queue_id: int):
        try:
            self.liq_client.queues_remove(queue_id)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def queue_push(self, queue_id: int, media_item: FileEvent):
        try:
            annotation = create_liquidsoap_annotation(media_item)
            self.liq_client.queue_push(queue_id, annotation, media_item["show_name"])
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def stop_web_stream_buffer(self):
        try:
            self.liq_client.web_stream_stop_buffer()
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def stop_web_stream_output(self):
        try:
            self.liq_client.web_stream_stop()
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def start_web_stream(self):
        try:
            self.liq_client.web_stream_start()
            self.current_prebuffering_stream_id = None
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def start_web_stream_buffer(self, media_item):
        try:
            self.liq_client.web_stream_start_buffer(
                media_item["row_id"],
                media_item["uri"],
            )
            self.current_prebuffering_stream_id = media_item["row_id"]
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def get_current_stream_id(self) -> str:
        try:
            return self.liq_client.web_stream_get_id()
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)
            return "-1"

    @ls_timeout
    def disconnect_source(self, sourcename):
        if sourcename not in ("master_dj", "live_dj"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug("Disconnecting source: %s", sourcename)
            self.liq_client.source_disconnect(sourcename)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def switch_source(self, sourcename, status):
        if sourcename not in ("master_dj", "live_dj", "scheduled_play"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug('Switching source: %s to "%s" status', sourcename, status)
            self.liq_client.source_switch_status(sourcename, status == "on")
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)
