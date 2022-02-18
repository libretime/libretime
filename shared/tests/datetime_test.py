from datetime import time

from libretime_shared.datetime import (
    time_in_milliseconds,
    time_in_seconds,
)
from pytest import approx


def test_time_in_seconds():
    value = time(hour=0, minute=3, second=34, microsecond=649600)
    assert time_in_seconds(value) == approx(214.65, abs=0.009)


def test_time_in_milliseconds():
    value = time(hour=0, minute=0, second=0, microsecond=500000)
    assert time_in_milliseconds(value) == 500
