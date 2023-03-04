import random
from datetime import datetime

import pytest
from libretime_api_client.v2 import ApiClient

from libretime_playout.player.events import (
    ActionEvent,
    EventKind,
    FileEvent,
    WebStreamEvent,
    event_isoparse,
)
from libretime_playout.player.schedule import (
    generate_file_events,
    generate_live_events,
    generate_webstream_events,
    get_schedule,
)


@pytest.fixture(name="api_client")
def _api_client_fixture():
    base_url = "http://localhost"
    return ApiClient(base_url=base_url, api_key="test_key")


SHOW_1 = {"id": 1, "name": "Show 1", "live_enabled": False}
SHOW_2 = {"id": 2, "name": "Show 2", "live_enabled": False}
SHOW_3 = {"id": 3, "name": "Show 3", "live_enabled": True}
SHOW_4 = {"id": 4, "name": "Show 4", "live_enabled": False}

SHOW_INSTANCE_1 = {
    "id": 1,
    "starts_at": "2022-09-05T11:00:00Z",
    "ends_at": "2022-09-05T11:10:00Z",
    "show": 1,
}
SHOW_INSTANCE_2 = {
    "id": 2,
    "starts_at": "2022-09-05T11:10:00Z",
    "ends_at": "2022-09-05T12:10:00Z",
    "show": 2,
}
SHOW_INSTANCE_3 = {
    "id": 3,
    "starts_at": "2022-09-05T12:10:00Z",
    "ends_at": "2022-09-05T13:00:00Z",
    "show": 3,
}
SHOW_INSTANCE_4 = {
    "id": 4,
    "starts_at": "2022-09-05T13:00:00Z",
    "ends_at": "2022-09-05T14:10:00Z",
    "show": 4,
}

FILE_1 = {
    "id": 1,
    "mime": "audio/flac",
    "length": "00:03:41.041723",
    "replay_gain": "4.52",
    "cue_in": "00:00:08.252450",
    "cue_out": "00:03:27.208000",
    "artist_name": "Nils Frahm",
    "album_title": "Tripping with Nils Frahm",
    "track_title": "The Dane",
    "url": None,
    "size": 10000,
}
FILE_2 = {
    "id": 2,
    "mime": "audio/flac",
    "length": "00:06:08.668798",
    "replay_gain": "11.46",
    "cue_in": "00:00:13.700800",
    "cue_out": "00:05:15.845000",
    "artist_name": "Nils Frahm",
    "album_title": "Tripping with Nils Frahm",
    "track_title": "My Friend the Forest",
    "url": None,
    "size": 10000,
}
FILE_3 = {
    "id": 3,
    "mime": "audio/flac",
    "length": "00:14:18.400000",
    "replay_gain": "-2.13",
    "cue_in": "00:00:55.121100",
    "cue_out": "00:14:18.400000",
    "artist_name": "Nils Frahm",
    "album_title": "Tripping with Nils Frahm",
    "track_title": "All Melody",
    "url": None,
    "size": 10000,
}
FILE_4 = {
    "id": 4,
    "mime": "audio/flac",
    "length": "00:10:45.472200",
    "replay_gain": "-1.65",
    "cue_in": "00:00:00",
    "cue_out": "00:10:26.891000",
    "artist_name": "Nils Frahm",
    "album_title": "Tripping with Nils Frahm",
    "track_title": "#2",
    "url": None,
    "size": 10000,
}
FILE_5 = {
    "id": 5,
    "mime": "audio/mp3",
    "length": "00:59:04.989000",
    "replay_gain": "-1.39",
    "cue_in": "00:00:00",
    "cue_out": "00:58:59.130000",
    "artist_name": "Democracy Now! Audio",
    "album_title": "Democracy Now! Audio",
    "track_title": "Democracy Now! 2022-09-05 Monday",
    "url": None,
    "size": 10000,
}

WEBSTREAM_1 = {
    "id": 1,
    "name": "External radio",
    "url": "http://stream.radio.org/main.ogg",
}

SCHEDULE_1 = {
    "id": 1,
    "starts_at": "2022-09-05T11:00:00Z",
    "ends_at": "2022-09-05T11:05:02.144200Z",
    "cue_in": "00:00:13.700800",
    "cue_out": "00:05:15.845000",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 2,
    "instance": 1,
    "length": "00:05:02.144200",
    "stream": None,
}
SCHEDULE_2 = {
    "id": 2,
    "starts_at": "2022-09-05T11:05:02.144200Z",
    "ends_at": "2022-09-05T11:10:00Z",
    "cue_in": "00:00:00",
    "cue_out": "00:04:57.855800",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 4,
    "instance": 1,
    "length": "00:10:26.891000",
    "stream": None,
}
SCHEDULE_3 = {
    "id": 3,
    "starts_at": "2022-09-05T11:10:00Z",
    "ends_at": "2022-09-05T12:08:59Z",
    "cue_in": "00:00:00",
    "cue_out": "00:58:59.130000",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 5,
    "instance": 2,
    "length": "00:58:59.130000",
    "stream": None,
}
SCHEDULE_4 = {
    "id": 4,
    "starts_at": "2022-09-05T12:08:59Z",
    "ends_at": "2022-09-05T12:10:00Z",
    "cue_in": "00:00:00",
    "cue_out": "00:01:01",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 4,
    "instance": 2,
    "length": "00:10:26.891000",
    "stream": None,
}
SCHEDULE_5 = {
    "id": 5,
    "starts_at": "2022-09-05T12:10:00Z",
    "ends_at": "2022-09-05T12:40:00Z",
    "cue_in": "00:00:00",
    "cue_out": "00:30:00",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": None,
    "instance": 3,
    "length": "00:30:00",
    "stream": 1,
}
SCHEDULE_6 = {
    "id": 6,
    "starts_at": "2022-09-05T12:40:00Z",
    "ends_at": "2022-09-05T12:53:23Z",
    "cue_in": "00:00:55.121100",
    "cue_out": "00:14:18.400000",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 3,
    "instance": 3,
    "length": "00:13:23.278900",
    "stream": None,
}
SCHEDULE_7 = {
    "id": 7,
    "starts_at": "2022-09-05T12:53:23Z",
    "ends_at": "2022-09-05T12:58:25Z",
    "cue_in": "00:00:13.700800",
    "cue_out": "00:05:15.845000",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 2,
    "instance": 3,
    "length": "00:05:02.144200",
    "stream": None,
}
SCHEDULE_8 = {
    "id": 8,
    "starts_at": "2022-09-05T12:58:25Z",
    "ends_at": "2022-09-05T13:00:00Z",
    "cue_in": "00:00:08.252450",
    "cue_out": "00:01:35",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 1,
    "instance": 3,
    "length": "00:03:18.955550",
    "stream": None,
}
SCHEDULE_9 = {
    "id": 9,
    "starts_at": "2022-09-05T13:00:00Z",
    "ends_at": "2022-09-05T13:05:02.144200Z",
    "cue_in": "00:00:13.700800",
    "cue_out": "00:05:15.845000",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 2,
    "instance": 4,
    "length": "00:05:02.144200",
    "stream": None,
}
SCHEDULE_10 = {
    "id": 10,
    "starts_at": "2022-09-05T13:05:02.144200Z",
    "ends_at": "2022-09-05T13:10:00Z",
    "cue_in": "00:00:00",
    "cue_out": "00:04:57.855800",
    "fade_in": "00:00:00.500000",
    "fade_out": "00:00:00.500000",
    "file": 4,
    "instance": 4,
    "length": "00:10:26.891000",
    "stream": None,
}
SCHEDULE = [
    SCHEDULE_1,
    SCHEDULE_2,
    SCHEDULE_3,
    SCHEDULE_4,
    SCHEDULE_5,
    SCHEDULE_6,
    SCHEDULE_7,
    SCHEDULE_8,
    SCHEDULE_9,
    SCHEDULE_10,
]


def test_generate_live_events():
    show_instance_3 = SHOW_INSTANCE_3.copy()
    show_instance_3["starts_at"] = event_isoparse(show_instance_3["starts_at"])
    show_instance_3["ends_at"] = event_isoparse(show_instance_3["ends_at"])

    result = {}
    generate_live_events(result, show_instance_3, 0.0)
    assert result == {
        "2022-09-05-13-00-00": ActionEvent(
            start=datetime(2022, 9, 5, 13, 0),
            end=datetime(2022, 9, 5, 13, 0),
            type=EventKind.ACTION,
            event_type="kick_out",
        ),
    }

    result = {}
    generate_live_events(result, show_instance_3, 2.0)
    assert result == {
        "2022-09-05-12-59-58": ActionEvent(
            start=datetime(2022, 9, 5, 12, 59, 58),
            end=datetime(2022, 9, 5, 12, 59, 58),
            type=EventKind.ACTION,
            event_type="switch_off",
        ),
        "2022-09-05-13-00-00": ActionEvent(
            start=datetime(2022, 9, 5, 13, 0),
            end=datetime(2022, 9, 5, 13, 0),
            type=EventKind.ACTION,
            event_type="kick_out",
        ),
    }


def test_generate_file_events():
    schedule_1 = SCHEDULE_1.copy()
    schedule_1["starts_at"] = event_isoparse(schedule_1["starts_at"])
    schedule_1["ends_at"] = event_isoparse(schedule_1["ends_at"])

    result = {}
    generate_file_events(result, schedule_1, FILE_2, SHOW_1)
    assert result == {
        "2022-09-05-11-00-00": FileEvent(
            start=datetime(2022, 9, 5, 11, 0),
            end=datetime(2022, 9, 5, 11, 5, 2),
            type=EventKind.FILE,
            row_id=1,
            uri=None,
            id=2,
            show_name="Show 1",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=13.7008,
            cue_out=315.845,
            track_title="My Friend the Forest",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=11.46,
            filesize=10000,
            file_ready=False,
        )
    }


def test_generate_webstream_events():
    schedule_5 = SCHEDULE_5.copy()
    schedule_5["starts_at"] = event_isoparse(schedule_5["starts_at"])
    schedule_5["ends_at"] = event_isoparse(schedule_5["ends_at"])

    result = {}
    generate_webstream_events(result, schedule_5, WEBSTREAM_1, SHOW_3)
    assert result == {
        "2022-09-05-12-10-00": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 9, 55),
            end=datetime(2022, 9, 5, 12, 9, 55),
            type=EventKind.WEB_STREAM_BUFFER_START,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-10-00_0": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 10),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_OUTPUT_START,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-40-00": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 40),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_BUFFER_END,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-40-00_0": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 40),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_OUTPUT_END,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
    }


@pytest.mark.parametrize(
    "schedule",
    [
        (SCHEDULE),
        (random.sample(SCHEDULE, len(SCHEDULE))),
    ],
)
def test_get_schedule(schedule, requests_mock, api_client: ApiClient):
    base_url = api_client.base_url

    requests_mock.get(
        f"{base_url}/api/v2/stream/preferences",
        json={
            "input_fade_transition": 2.0,
            "message_format": 0,
            "message_offline": "",
        },
    )

    requests_mock.get(f"{base_url}/api/v2/schedule", json=schedule)

    requests_mock.get(f"{base_url}/api/v2/shows/1", json=SHOW_1)
    requests_mock.get(f"{base_url}/api/v2/shows/2", json=SHOW_2)
    requests_mock.get(f"{base_url}/api/v2/shows/3", json=SHOW_3)
    requests_mock.get(f"{base_url}/api/v2/shows/4", json=SHOW_4)
    requests_mock.get(f"{base_url}/api/v2/show-instances/1", json=SHOW_INSTANCE_1)
    requests_mock.get(f"{base_url}/api/v2/show-instances/2", json=SHOW_INSTANCE_2)
    requests_mock.get(f"{base_url}/api/v2/show-instances/3", json=SHOW_INSTANCE_3)
    requests_mock.get(f"{base_url}/api/v2/show-instances/4", json=SHOW_INSTANCE_4)
    requests_mock.get(f"{base_url}/api/v2/files/1", json=FILE_1)
    requests_mock.get(f"{base_url}/api/v2/files/2", json=FILE_2)
    requests_mock.get(f"{base_url}/api/v2/files/3", json=FILE_3)
    requests_mock.get(f"{base_url}/api/v2/files/4", json=FILE_4)
    requests_mock.get(f"{base_url}/api/v2/files/5", json=FILE_5)
    requests_mock.get(f"{base_url}/api/v2/webstreams/1", json=WEBSTREAM_1)

    assert get_schedule(api_client) == {
        "2022-09-05-11-00-00": FileEvent(
            start=datetime(2022, 9, 5, 11, 0),
            end=datetime(2022, 9, 5, 11, 5, 2),
            type=EventKind.FILE,
            row_id=1,
            uri=None,
            id=2,
            show_name="Show 1",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=13.7008,
            cue_out=315.845,
            track_title="My Friend the Forest",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=11.46,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-11-05-02": FileEvent(
            start=datetime(2022, 9, 5, 11, 5, 2),
            end=datetime(2022, 9, 5, 11, 10),
            type=EventKind.FILE,
            row_id=2,
            uri=None,
            id=4,
            show_name="Show 1",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=0.0,
            cue_out=297.8558,
            track_title="#2",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=-1.65,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-11-10-00": FileEvent(
            start=datetime(2022, 9, 5, 11, 10),
            end=datetime(2022, 9, 5, 12, 8, 59),
            type=EventKind.FILE,
            row_id=3,
            uri=None,
            id=5,
            show_name="Show 2",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=0.0,
            cue_out=3539.13,
            track_title="Democracy Now! 2022-09-05 Monday",
            artist_name="Democracy Now! Audio",
            mime="audio/mp3",
            replay_gain=-1.39,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-12-08-59": FileEvent(
            start=datetime(2022, 9, 5, 12, 8, 59),
            end=datetime(2022, 9, 5, 12, 10),
            type=EventKind.FILE,
            row_id=4,
            uri=None,
            id=4,
            show_name="Show 2",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=0.0,
            cue_out=61.0,
            track_title="#2",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=-1.65,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-12-10-00": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 9, 55),
            end=datetime(2022, 9, 5, 12, 9, 55),
            type=EventKind.WEB_STREAM_BUFFER_START,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-10-00_0": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 10),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_OUTPUT_START,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-40-00": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 40),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_BUFFER_END,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-40-00_0": WebStreamEvent(
            start=datetime(2022, 9, 5, 12, 40),
            end=datetime(2022, 9, 5, 12, 40),
            type=EventKind.WEB_STREAM_OUTPUT_END,
            row_id=5,
            uri="http://stream.radio.org/main.ogg",
            id=1,
            show_name="Show 3",
        ),
        "2022-09-05-12-40-00_1": FileEvent(
            start=datetime(2022, 9, 5, 12, 40),
            end=datetime(2022, 9, 5, 12, 53, 23),
            type=EventKind.FILE,
            row_id=6,
            uri=None,
            id=3,
            show_name="Show 3",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=55.1211,
            cue_out=858.4,
            track_title="All Melody",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=-2.13,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-12-53-23": FileEvent(
            start=datetime(2022, 9, 5, 12, 53, 23),
            end=datetime(2022, 9, 5, 12, 58, 25),
            type=EventKind.FILE,
            row_id=7,
            uri=None,
            id=2,
            show_name="Show 3",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=13.7008,
            cue_out=315.845,
            track_title="My Friend the Forest",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=11.46,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-12-58-25": FileEvent(
            start=datetime(2022, 9, 5, 12, 58, 25),
            end=datetime(2022, 9, 5, 13, 0),
            type=EventKind.FILE,
            row_id=8,
            uri=None,
            id=1,
            show_name="Show 3",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=8.25245,
            cue_out=95.0,
            track_title="The Dane",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=4.52,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-12-59-58": ActionEvent(
            start=datetime(2022, 9, 5, 12, 59, 58),
            end=datetime(2022, 9, 5, 12, 59, 58),
            type=EventKind.ACTION,
            event_type="switch_off",
        ),
        "2022-09-05-13-00-00": ActionEvent(
            start=datetime(2022, 9, 5, 13, 0),
            end=datetime(2022, 9, 5, 13, 0),
            type=EventKind.ACTION,
            event_type="kick_out",
        ),
        "2022-09-05-13-00-00_0": FileEvent(
            start=datetime(2022, 9, 5, 13, 0),
            end=datetime(2022, 9, 5, 13, 5, 2),
            type=EventKind.FILE,
            row_id=9,
            uri=None,
            id=2,
            show_name="Show 4",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=13.7008,
            cue_out=315.845,
            track_title="My Friend the Forest",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=11.46,
            filesize=10000,
            file_ready=False,
        ),
        "2022-09-05-13-05-02": FileEvent(
            start=datetime(2022, 9, 5, 13, 5, 2),
            end=datetime(2022, 9, 5, 13, 10),
            type=EventKind.FILE,
            row_id=10,
            uri=None,
            id=4,
            show_name="Show 4",
            fade_in=500.0,
            fade_out=500.0,
            cue_in=0.0,
            cue_out=297.8558,
            track_title="#2",
            artist_name="Nils Frahm",
            mime="audio/flac",
            replay_gain=-1.65,
            filesize=10000,
            file_ready=False,
        ),
    }
