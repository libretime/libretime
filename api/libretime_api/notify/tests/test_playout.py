"""
Proof that the playout notifications are the ones the legacy service sent.

Every case is compared to the body captured from the legacy service on a real
broker, byte for byte, and to the exchange, routing key and message
properties it was captured with.
"""

import pytest

from .. import playout
from .conftest import legacy_capture

FIXTURE = legacy_capture("playout")

# The value the legacy publisher of tools/rabbitmq-equivalence sends, chosen
# to cover everything json_encode escapes: a quote, a backslash, a slash,
# accented characters, and an astral character that becomes a pair of
# surrogates.
STATION_NAME = 'Radio Zürich — "quote" \\ <>& / \U0001f600'

# The notification of the legacy service, per captured case.
CASES = {
    "update_schedule": playout.update_schedule,
    "reset_liquidsoap_bootstrap": playout.reset_liquidsoap_bootstrap,
    "switch_source_on": lambda: playout.switch_source("live_dj", "on"),
    "switch_source_off": lambda: playout.switch_source("master_dj", "off"),
    "disconnect_source": lambda: playout.disconnect_source("live_dj"),
    "update_station_name": lambda: playout.update_station_name(STATION_NAME),
    "update_transition_fade": lambda: playout.update_transition_fade("1.500"),
    "update_stream_format": lambda: playout.update_stream_format("1"),
    "update_message_offline": lambda: playout.update_message_offline(
        "LibreTime - offline"
    ),
}


def test_every_captured_case_is_covered():
    assert set(CASES) == set(FIXTURE["messages"])


@pytest.mark.parametrize("case", sorted(CASES))
def test_body_is_the_one_the_legacy_service_published(case, capture):
    CASES[case]()

    assert capture.single("playout").payload == FIXTURE["messages"][case]


@pytest.mark.parametrize("case", sorted(CASES))
def test_message_reaches_playout_like_the_legacy_one(case, capture):
    CASES[case]()

    message = capture.single("playout")

    assert message.delivery_info["exchange"] == FIXTURE["exchange"]
    assert message.delivery_info["routing_key"] == FIXTURE["routing_key"]

    # Playout refuses any other content type.
    assert message.content_type == FIXTURE["properties"]["content_type"]


def test_event_type_is_appended_last(capture):
    """The legacy model adds event_type to the payload it was given."""
    playout.send_message_to_pypo("update_station_name", {"station_name": "x"})

    assert capture.single("playout").payload == (
        '{"station_name":"x","event_type":"update_station_name"}'
    )


def test_empty_payload_stays_an_object(capture):
    """json_encode is called with JSON_FORCE_OBJECT, never with an array."""
    playout.update_schedule()

    assert capture.single("playout").payload == '{"event_type":"update_schedule"}'
