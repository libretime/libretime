import datetime
from configparser import ConfigParser

import pytest
from api_clients import utils


def test_time_in_seconds():
    time = datetime.time(hour=0, minute=3, second=34, microsecond=649600)
    assert abs(utils.time_in_seconds(time) - 214.65) < 0.009


def test_time_in_milliseconds():
    time = datetime.time(hour=0, minute=0, second=0, microsecond=500000)
    assert utils.time_in_milliseconds(time) == 500


@pytest.mark.parametrize(
    "payload, expected",
    [({}, "http"), ({"base_port": 80}, "http"), ({"base_port": 443}, "https")],
)
@pytest.mark.parametrize(
    "use_config",
    [False, True],
)
def test_get_protocol(payload, use_config, expected):
    config = ConfigParser() if use_config else {}
    config["general"] = {**payload}

    assert utils.get_protocol(config) == expected


@pytest.mark.parametrize("payload", [{}, {"base_port": 80}])
@pytest.mark.parametrize("use_config", [False, True])
@pytest.mark.parametrize(
    "values, expected",
    [
        (["yes", "Yes", "True", "true", True], "https"),
        (["no", "No", "False", "false", False], "http"),
    ],
)
def test_get_protocol_force_https(payload, use_config, values, expected):
    for value in values:
        config = ConfigParser() if use_config else {}
        config["general"] = {**payload, "force_ssl": value}
        assert utils.get_protocol(config) == expected


@pytest.mark.parametrize(
    "payload, expected",
    [
        ("00:00:00.500000", datetime.time(microsecond=500000)),
        ("00:04:30.092540", datetime.time(minute=4, second=30, microsecond=92540)),
    ],
)
def test_fromisoformat(payload, expected):
    assert utils.fromisoformat(payload) == expected
