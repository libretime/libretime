import pytest
from libretime_api_client.v2 import ApiClient

from libretime_playout.player.schedule import get_schedule


@pytest.fixture(name="api_client_mock")
def _api_client_mock(requests_mock):
    base_url = "http://localhost"
    api_client = ApiClient(base_url=base_url, api_key="test_key")

    requests_mock.get(
        f"{base_url}/api/v2/schedule",
        json=[
            {
                "id": 17,
                "starts_at": "2022-03-04T15:30:00Z",
                "ends_at": "2022-03-04T15:33:50.674340Z",
                "file": 1,
                "stream": None,
                "fade_in": "00:00:00.500000",
                "fade_out": "00:00:00.500000",
                "cue_in": "00:00:01.310660",
                "cue_out": "00:03:51.985000",
                "instance": 3,
            },
            {
                "id": 18,
                "starts_at": "2022-03-04T15:33:50.674340Z",
                "ends_at": "2022-03-04T16:03:50.674340Z",
                "file": None,
                "stream": 1,
                "fade_in": "00:00:00.500000",
                "fade_out": "00:00:00.500000",
                "cue_in": "00:00:00",
                "cue_out": "00:30:00",
                "instance": 3,
            },
        ],
    )

    requests_mock.get(
        f"{base_url}/api/v2/show-instances/3",
        json={
            "show": 3,
        },
    )

    requests_mock.get(
        f"{base_url}/api/v2/shows/3",
        json={
            "name": "Test",
        },
    )

    requests_mock.get(
        f"{base_url}/api/v2/files/1",
        json={
            "id": 1,
            "url": None,
            "replay_gain": "-8.77",
            "size": 9505222,
            "artist_name": "Bag Raiders",
            "track_title": "Shooting Stars",
            "mime": "audio/mp3",
        },
    )

    requests_mock.get(
        f"{base_url}/api/v2/webstreams/1",
        json={
            "id": 1,
            "name": "Test",
            "url": "http://some-other-radio:8800/main.ogg",
        },
    )

    return api_client


def test_get_schedule(api_client_mock: ApiClient):
    assert get_schedule(api_client_mock) == {
        "media": {
            "2022-03-04-15-30-00": {
                "type": "file",
                "row_id": 17,
                "start": "2022-03-04-15-30-00",
                "end": "2022-03-04-15-33-50",
                # NOTE: The legacy schedule generator creates an url,
                # but playout download the file using the file id, so
                # we can safely ignore it here.
                "uri": None,
                "id": 1,
                "show_name": "Test",
                "fade_in": 500.0,
                "fade_out": 500.0,
                "cue_in": 1.31066,
                "cue_out": 231.985,
                "metadata": {
                    "artist_name": "Bag Raiders",
                    "track_title": "Shooting Stars",
                    "mime": "audio/mp3",
                },
                "replay_gain": "-8.77",
                "filesize": 9505222,
            },
            "2022-03-04-15-33-50": {
                "type": "stream_buffer_start",
                "row_id": 18,
                "start": "2022-03-04-15-33-45",
                "end": "2022-03-04-15-33-45",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
            "2022-03-04-15-33-50_0": {
                "type": "stream_output_start",
                "row_id": 18,
                "start": "2022-03-04-15-33-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
                "show_name": "Test",
            },
            "2022-03-04-16-03-50": {
                "type": "stream_buffer_end",
                "row_id": 18,
                "start": "2022-03-04-16-03-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
            "2022-03-04-16-03-50_0": {
                "type": "stream_output_end",
                "row_id": 18,
                "start": "2022-03-04-16-03-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
        }
    }
