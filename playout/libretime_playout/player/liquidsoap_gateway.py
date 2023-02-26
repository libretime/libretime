import logging
from typing import List

from ..liquidsoap.client import LiquidsoapClient
from ..timeout import ls_timeout

logger = logging.getLogger(__name__)


def create_liquidsoap_annotation(media):
    # We need liq_start_next value in the annotate. That is the value that controls overlap duration of crossfade.

    filename = media["dst"]
    annotation = (
        'annotate:media_id="%s",liq_start_next="0",liq_fade_in="%s",'
        + 'liq_fade_out="%s",liq_cue_in="%s",liq_cue_out="%s",'
        + 'schedule_table_id="%s",replay_gain="%s dB"'
    ) % (
        media["id"],
        float(media["fade_in"]) / 1000,
        float(media["fade_out"]) / 1000,
        float(media["cue_in"]),
        float(media["cue_out"]),
        media["row_id"],
        media["replay_gain"],
    )

    # Override the the artist/title that Liquidsoap extracts from a file's metadata
    # with the metadata we get from Airtime. (You can modify metadata in Airtime's library,
    # which doesn't get saved back to the file.)
    if "metadata" in media:
        if "artist_name" in media["metadata"]:
            artist_name = media["metadata"]["artist_name"]
            if isinstance(artist_name, str):
                annotation += ',artist="%s"' % (artist_name.replace('"', '\\"'))
        if "track_title" in media["metadata"]:
            track_title = media["metadata"]["track_title"]
            if isinstance(track_title, str):
                annotation += ',title="%s"' % (track_title.replace('"', '\\"'))

    annotation += ":" + filename

    return annotation


class TelnetLiquidsoap:
    def __init__(
        self,
        liq_client: LiquidsoapClient,
        queues: List[str],
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
    def queue_remove(self, queue_id):
        try:
            self.liq_client.queues_remove(queue_id)
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

    @ls_timeout
    def queue_push(self, queue_id, media_item):
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
    def start_web_stream(self, media_item):
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
    def get_current_stream_id(self):
        try:
            return self.liq_client.web_stream_get_id()
        except (ConnectionError, TimeoutError) as exception:
            logger.exception(exception)

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
