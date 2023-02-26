"""
Python part of radio playout (pypo)

This function acts as a gateway between liquidsoap and the server API.
Mainly used to tell the platform what pypo/liquidsoap does.

Main case:
 - whenever LS starts playing a new track, its on_metadata callback calls
   a function in ls (notify(m)) which then calls the python script here
   with the currently starting filename as parameter
 - this python script takes this parameter, tries to extract the actual
   media id from it, and then calls back to the API to tell about it about it.

"""
import logging
from pathlib import Path
from typing import Optional

import click
from libretime_api_client.v1 import ApiClient as LegacyClient
from libretime_shared.cli import cli_logging_options
from libretime_shared.config import DEFAULT_ENV_PREFIX
from libretime_shared.logging import setup_logger

logger = logging.getLogger(__name__)


def api_client():
    return LegacyClient()


@click.group(context_settings={"auto_envvar_prefix": DEFAULT_ENV_PREFIX})
@cli_logging_options()
def cli(log_level: str, log_filepath: Optional[Path]):
    """
    A gateway between Liquidsoap and the API.
    """
    setup_logger(log_level, log_filepath, rotate=False)


@cli.command()
@click.argument("media_id")
def media(media_id):
    """
    Notify currently playing media.

    Replaces: notify --media-id=#{m['schedule_table_id']}
    """
    logger.info(f"Sending currently playing media id '{media_id}'")
    api_client().notify_media_item_start_playing(media_id)


@cli.command()
@click.argument("media_id")
@click.argument("data")
def webstream(media_id, data):
    """
    Notify currently playing webstream.

    Replaces: notify --webstream='#{json_str}' --media-id=#{!current_dyn_id}
    """
    logger.info(f"Sending currently playing webstream '{media_id}' data '{data}'")
    api_client().notify_webstream_data(data, media_id)


@cli.command()
@click.argument("name")
@click.argument("status")
def live(name, status):
    """
    Notify currently playing live input.

    Replaces: notify --source-name=#{sourcename} --source-status=#{status}
    """
    logger.info(f"Sending currently playing live source '{name}' status '{status}'")
    api_client().notify_source_status(name, status)


@cli.command()
@click.argument("stream_id")
@click.argument("time")
@click.option("--error", help="Error message if any occurred.")
def stream(stream_id, time, error):
    """
    Notify about output stream status.

    Replaces: notify --error='#{msg}' --stream-id=#{stream} --time=#{!time}
    Replaces: notify --connect --stream-id=#{stream} --time=#{!time}
    """
    status = "OK"
    if error is not None:
        status = error

    logger.info(f"Sending output stream '{stream_id}' status '{status}'")
    api_client().notify_liquidsoap_status(status, stream_id, time)


@cli.command()
def started():
    """
    Notify liquidsoap startup status.

    Replaces: notify --liquidsoap-started
    """
    logger.debug("Notifying server that Liquidsoap has started")
    api_client().notify_liquidsoap_started()
