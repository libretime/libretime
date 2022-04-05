import datetime

import pytest

from libretime_api_client import utils


def test_time_in_seconds():
    time = datetime.time(hour=0, minute=3, second=34, microsecond=649600)
    assert abs(utils.time_in_seconds(time) - 214.65) < 0.009


def test_time_in_milliseconds():
    time = datetime.time(hour=0, minute=0, second=0, microsecond=500000)
    assert utils.time_in_milliseconds(time) == 500


@pytest.mark.parametrize(
    "payload, expected",
    [
        ("00:00:00.500000", datetime.time(microsecond=500000)),
        ("00:04:30.092540", datetime.time(minute=4, second=30, microsecond=92540)),
        ("00:04:30", datetime.time(minute=4, second=30)),
    ],
)
def test_fromisoformat(payload, expected):
    assert utils.fromisoformat(payload) == expected
