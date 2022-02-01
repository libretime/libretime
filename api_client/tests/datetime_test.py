from datetime import time

from libretime.api_client.datetime import (
    time_fromisoformat,
    time_in_milliseconds,
    time_in_seconds,
)
from pytest import approx, mark


def test_time_in_seconds():
    value = time(hour=0, minute=3, second=34, microsecond=649600)
    assert time_in_seconds(value) == approx(214.65, abs=0.009)


def test_time_in_milliseconds():
    value = time(hour=0, minute=0, second=0, microsecond=500000)
    assert time_in_milliseconds(value) == 500


@mark.parametrize(
    "payload, expected",
    [
        ("00:00:00.500000", time(microsecond=500000)),
        ("00:04:30.092540", time(minute=4, second=30, microsecond=92540)),
        ("00:04:30", time(minute=4, second=30)),
    ],
)
def test_time_fromisoformat(payload, expected):
    assert time_fromisoformat(payload) == expected
