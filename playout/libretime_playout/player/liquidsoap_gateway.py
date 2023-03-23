import logging
from typing import List, Optional

from ..liquidsoap.client import LiquidsoapClient
from .events import FileEvent, WebStreamEvent

logger = logging.getLogger(__name__)


def create_liquidsoap_annotation(file_event: FileEvent) -> str:
    # We need liq_start_next value in the annotate. That is the value that controls
    # overlap duration of crossfade.
    annotations = {
        "media_id": file_event.id,
        "schedule_table_id": file_event.row_id,
        "liq_start_next": "0",
        "liq_fade_in": file_event.fade_in / 1000,
        "liq_fade_out": file_event.fade_out / 1000,
        "liq_cue_in": file_event.cue_in,
        "liq_cue_out": file_event.cue_out,
    }

    if file_event.replay_gain is not None:
        annotations["replay_gain"] = f"{file_event.replay_gain} dB"

    # Override the the artist/title that Liquidsoap extracts from a file's metadata with
    # the metadata we get from Airtime. (You can modify metadata in Airtime's library,
    # which doesn't get saved back to the file.)
    if file_event.artist_name:
        annotations["artist"] = file_event.artist_name.replace('"', '\\"')

    if file_event.track_title:
        annotations["title"] = file_event.track_title.replace('"', '\\"')

    annotations_str = ",".join(f'{key}="{value}"' for key, value in annotations.items())

    return "annotate:" + annotations_str + ":" + str(file_event.local_filepath)


class TelnetLiquidsoap:
    current_prebuffering_stream_id: Optional[int] = None

    def __init__(
        self,
        liq_client: LiquidsoapClient,
        queues: List[int],
    ):
        self.liq_client = liq_client
        self.queues = queues

    def queue_clear_all(self):
        try:
            self.liq_client.queues_remove(*self.queues)
        except OSError as exception:
            logger.exception(exception)

    def queue_remove(self, queue_id: int):
        try:
            self.liq_client.queues_remove(queue_id)
        except OSError as exception:
            logger.exception(exception)

    def queue_push(self, queue_id: int, file_event: FileEvent):
        try:
            annotation = create_liquidsoap_annotation(file_event)
            self.liq_client.queue_push(queue_id, annotation, file_event.show_name)
        except OSError as exception:
            logger.exception(exception)

    def stop_web_stream_buffer(self):
        try:
            self.liq_client.web_stream_stop_buffer()
        except OSError as exception:
            logger.exception(exception)

    def stop_web_stream_output(self):
        try:
            self.liq_client.web_stream_stop()
        except OSError as exception:
            logger.exception(exception)

    def start_web_stream(self):
        try:
            self.liq_client.web_stream_start()
            self.current_prebuffering_stream_id = None
        except OSError as exception:
            logger.exception(exception)

    def start_web_stream_buffer(self, event: WebStreamEvent):
        try:
            self.liq_client.web_stream_start_buffer(event.row_id, event.uri)
            self.current_prebuffering_stream_id = event.row_id
        except OSError as exception:
            logger.exception(exception)

    def get_current_stream_id(self) -> str:
        try:
            return self.liq_client.web_stream_get_id()
        except OSError as exception:
            logger.exception(exception)
            return "-1"

    def disconnect_source(self, sourcename):
        if sourcename not in ("master_dj", "live_dj"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug("Disconnecting source: %s", sourcename)
            self.liq_client.source_disconnect(sourcename)
        except OSError as exception:
            logger.exception(exception)

    def switch_source(self, sourcename, status):
        if sourcename not in ("master_dj", "live_dj", "scheduled_play"):
            raise ValueError(f"invalid source name: {sourcename}")

        try:
            logger.debug('Switching source: %s to "%s" status', sourcename, status)
            self.liq_client.source_switch_status(sourcename, status == "on")
        except OSError as exception:
            logger.exception(exception)
