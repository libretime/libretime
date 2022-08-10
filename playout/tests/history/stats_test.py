from datetime import datetime
from typing import List
from unittest.mock import Mock, call

import pytest
from libretime_shared.config import IcecastOutput, ShoutcastOutput
from lxml.etree import XMLSyntaxError
from requests.exceptions import HTTPError

from libretime_playout.history.stats import AnyOutput, Stats, StatsCollector

from ..fixtures import icecast_stats, shoutcast_admin


@pytest.fixture(name="outputs")
def outputs_fixture():
    default_output = {
        "enabled": True,
        "mount": "main.ogg",
        "source_password": "hackme",
        "admin_password": "hackme",
        "audio": {"format": "ogg", "bitrate": 256},
    }
    return [
        IcecastOutput(**default_output),
        IcecastOutput(
            **{
                **default_output,
                "mount": "main.mp3",
                "audio": {"format": "mp3", "bitrate": 256},
            }
        ),
    ]


def test_stats_collector_collect_server_stats(
    requests_mock,
    outputs: List[AnyOutput],
):
    requests_mock.get(
        "http://localhost:8000/admin/stats.xml",
        content=icecast_stats.read_bytes(),
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    result = collector.collect_output_stats(outputs[0])
    assert result == {
        "main.ogg": Stats(listeners=2),
        "main.mp3": Stats(listeners=3),
    }

    legacy_client.assert_not_called()


def test_stats_collector_collect_server_stats_unauthorized(
    requests_mock,
    outputs: List[AnyOutput],
):
    requests_mock.get(
        "http://localhost:8000/admin/stats.xml",
        status_code=401,
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    with pytest.raises(HTTPError):
        collector.collect_output_stats(outputs[0])


def test_stats_collector_collect_server_stats_invalid_xml(
    requests_mock,
    outputs: List[AnyOutput],
):
    requests_mock.get(
        "http://localhost:8000/admin/stats.xml",
        content=b"""<?xml version="1.0"?><icestats><host>localhost</icestats>""",
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    with pytest.raises(XMLSyntaxError):
        collector.collect_output_stats(outputs[0])


def test_stats_collector_collect(
    requests_mock,
    outputs: List[AnyOutput],
):
    requests_mock.get(
        "http://localhost:8000/admin/stats.xml",
        content=icecast_stats.read_bytes(),
    )
    requests_mock.get(
        "http://shoutcast.com:8000/admin.cgi?sid=1&mode=viewxml",
        content=shoutcast_admin.read_bytes(),
    )

    legacy_client = Mock()
    default_output = {
        "source_password": "hackme",
        "admin_password": "hackme",
    }
    outputs.extend(
        [
            IcecastOutput(
                **{
                    **default_output,
                    "enabled": False,
                    "host": "example.com",
                    "mount": "disabled.ogg",
                    "audio": {"format": "ogg", "bitrate": 256},
                }
            ),
            ShoutcastOutput(
                **{
                    **default_output,
                    "enabled": True,
                    "kind": "shoutcast",
                    "host": "shoutcast.com",
                    "audio": {"format": "mp3", "bitrate": 256},
                }
            ),
        ]
    )

    collector = StatsCollector(legacy_client)
    collector.collect(outputs, _timestamp=datetime(2022, 8, 9, 11, 19, 7))

    legacy_client.assert_has_calls(
        [
            call.push_stream_stats(
                [
                    {
                        "timestamp": "2022-08-09 11:19:07",
                        "num_listeners": 2,
                        "mount_name": "main.ogg",
                    },
                    {
                        "timestamp": "2022-08-09 11:19:07",
                        "num_listeners": 3,
                        "mount_name": "main.mp3",
                    },
                    {
                        "timestamp": "2022-08-09 11:19:07",
                        "num_listeners": 1,
                        "mount_name": "shoutcast",
                    },
                ]
            )
        ]
    )
