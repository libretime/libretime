from datetime import datetime
from unittest.mock import Mock, call

import pytest

from libretime_playout.history.stats import Server, Source, Stats, StatsCollector

from ..fixtures import icecast_stats, shoutcast_admin


@pytest.fixture(name="server")
def _server_fixture():
    return Server(
        host="example.com",
        port=8000,
        auth=("admin", "hackme"),
        sources=[
            Source("s1", "main.ogg"),
        ],
    )


def test_stats_collector_collect_server_stats(requests_mock, server):
    requests_mock.get(
        "http://example.com:8000/admin/stats.xml",
        content=icecast_stats.read_bytes(),
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    assert collector.collect_server_stats(server) == {"main.ogg": Stats(listeners=2)}

    legacy_client.assert_not_called()


def test_stats_collector_collect_server_stats_unauthorized(requests_mock, server):
    requests_mock.get(
        "http://example.com:8000/admin/stats.xml",
        status_code=401,
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    assert not collector.collect_server_stats(server)

    legacy_client.assert_has_calls(
        [
            call.update_stream_setting_table(
                {
                    "s1": "401 Client Error: None for url: http://example.com:8000/admin/stats.xml",
                }
            )
        ]
    )


def test_stats_collector_collect_server_stats_invalid_xml(requests_mock, server):
    requests_mock.get(
        "http://example.com:8000/admin/stats.xml",
        content=b"""<?xml version="1.0"?>
<icestats>
    <host>localhost
</icestats>
    """,
    )

    legacy_client = Mock()

    collector = StatsCollector(legacy_client)
    assert not collector.collect_server_stats(server)

    legacy_client.assert_has_calls(
        [
            call.update_stream_setting_table(
                {
                    "s1": "Opening and ending tag mismatch: host line 3 and icestats, line 4, column 12 (<string>, line 4)",
                }
            )
        ]
    )


def test_stats_collector_collect(requests_mock):
    requests_mock.get(
        "http://example.com:8000/admin/stats.xml",
        content=icecast_stats.read_bytes(),
    )
    requests_mock.get(
        "http://shoutcast.com:8000/admin.cgi?sid=1&mode=viewxml",
        content=shoutcast_admin.read_bytes(),
    )

    legacy_client = Mock()
    default_stream = {
        "enable": "true",
        "output": "icecast",
        "host": "example.com",
        "port": 8000,
        "mount": "main.ogg",
        "admin_user": "admin",
        "admin_pass": "hackme",
    }
    legacy_client.get_stream_parameters.return_value = {
        "stream_params": {
            "s1": {**default_stream},
            "s2": {**default_stream, "enable": "false", "mount": "main.mp3"},
            "s3": {**default_stream, "mount": "unknown.mp3"},
            "s4": {
                **default_stream,
                "output": "shoutcast",
                "host": "shoutcast.com",
                "mount": "shout.mp3",
            },
        }
    }

    collector = StatsCollector(legacy_client)
    collector.collect(_timestamp=datetime(2022, 8, 9, 11, 19, 7))

    legacy_client.assert_has_calls(
        [
            call.get_stream_parameters(),
            call.push_stream_stats(
                [
                    {
                        "timestamp": "2022-08-09 11:19:07",
                        "num_listeners": 2,
                        "mount_name": "main.ogg",
                    },
                    {
                        "timestamp": "2022-08-09 11:19:07",
                        "num_listeners": 1,
                        "mount_name": "shoutcast",
                    },
                ]
            ),
        ]
    )
