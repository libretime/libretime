"""
Notifications for the playout service.

Playout listens on the ``playout`` fanout exchange and reloads what it caches
in liquidsoap whenever one of these events arrives. The event names and the
payload fields are the ones of the legacy service, and playout ignores every
event it does not know, so both producers can run side by side.
"""

from collections.abc import Mapping
from enum import StrEnum
from typing import Any, Literal

from ._amqp import ExchangeSpec, publish

# Declared by the legacy service as durable with auto delete, which is what
# the playout consumer declares as well.
PLAYOUT_EXCHANGE = ExchangeSpec(name="playout", type="fanout", auto_delete=True)

# Sources playout can switch, validated by libretime_playout.player.liquidsoap.
SourceName = Literal["master_dj", "live_dj", "scheduled_play"]
SourceStatus = Literal["on", "off"]


class PlayoutEvent(StrEnum):
    """The event types playout handles."""

    UPDATE_SCHEDULE = "update_schedule"
    RESET_LIQUIDSOAP_BOOTSTRAP = "reset_liquidsoap_bootstrap"
    SWITCH_SOURCE = "switch_source"
    DISCONNECT_SOURCE = "disconnect_source"
    UPDATE_STATION_NAME = "update_station_name"
    UPDATE_TRANSITION_FADE = "update_transition_fade"
    UPDATE_STREAM_FORMAT = "update_stream_format"
    UPDATE_MESSAGE_OFFLINE = "update_message_offline"


def send_message_to_pypo(
    event_type: PlayoutEvent | str,
    data: Mapping[str, Any] | None = None,
) -> None:
    """Notify playout of an event, with the optional payload fields attached.

    Equivalent of ``Application_Model_RabbitMq::SendMessageToPypo``, which
    appends ``event_type`` to the fields it receives, so the field order of
    the message is the payload first and the event type last.
    """
    payload: dict[str, Any] = dict(data or {})
    payload["event_type"] = str(event_type)

    publish(PLAYOUT_EXCHANGE, payload)


def update_schedule() -> None:
    """Tell playout to fetch the schedule again.

    Callers have to build the schedule first, which the legacy service did in
    a post dispatch hook of every request changing it.
    """
    send_message_to_pypo(PlayoutEvent.UPDATE_SCHEDULE)


def reset_liquidsoap_bootstrap() -> None:
    """Tell playout to restart liquidsoap with a fresh bootstrap."""
    send_message_to_pypo(PlayoutEvent.RESET_LIQUIDSOAP_BOOTSTRAP)


def switch_source(sourcename: SourceName, status: SourceStatus) -> None:
    """Turn one of the playout sources on or off."""
    send_message_to_pypo(
        PlayoutEvent.SWITCH_SOURCE,
        {"sourcename": sourcename, "status": status},
    )


def disconnect_source(sourcename: SourceName) -> None:
    """Kick a live source out of the stream."""
    send_message_to_pypo(PlayoutEvent.DISCONNECT_SOURCE, {"sourcename": sourcename})


def update_station_name(station_name: str) -> None:
    """Update the station name playout sends as stream metadata."""
    send_message_to_pypo(
        PlayoutEvent.UPDATE_STATION_NAME,
        {"station_name": station_name},
    )


def update_transition_fade(transition_fade: str | float) -> None:
    """Update the crossfade duration between shows, in seconds.

    The legacy service forwards the value the form posted, a string like
    ``"1.500"``, and playout converts it to a float. Passing the stored value
    keeps the message byte identical.
    """
    send_message_to_pypo(
        PlayoutEvent.UPDATE_TRANSITION_FADE,
        {"transition_fade": transition_fade},
    )


def update_stream_format(stream_format: str | int) -> None:
    """Update the metadata format playout sends, see ``MessageFormatKind``."""
    send_message_to_pypo(
        PlayoutEvent.UPDATE_STREAM_FORMAT,
        {"stream_format": stream_format},
    )


def update_message_offline(message_offline: str) -> None:
    """Update the metadata playout sends while the station is off air."""
    send_message_to_pypo(
        PlayoutEvent.UPDATE_MESSAGE_OFFLINE,
        {"message_offline": message_offline},
    )
