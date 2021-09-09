import pytest
from api_clients.utils import RequestProvider
from api_clients.version2 import AirtimeApiClient, api_config


@pytest.fixture()
def config():
    return {
        **api_config,
        "general": {
            "base_dir": "/test",
            "base_port": 80,
            "base_url": "localhost",
            "api_key": "TEST_KEY",
        },
        "api_base": "api",
    }


class MockRequestProvider:
    @staticmethod
    def schedule_url(_post_data=None, params=None, **kwargs):
        return [
            {
                "id": 1,
                "starts": "2021-07-05T11:00:00Z",
                "ends": "2021-07-05T11:01:00.5000Z",
                "instance_id": 2,
                "file": "http://localhost/api/v2/file/3",
                "file_id": 3,
                "fade_in": "00:00:00.500000",
                "fade_out": "00:00:01",
                "cue_in": "00:00:00.142404",
                "cue_out": "01:58:04.463583",
            },
        ]

    @staticmethod
    def show_instance_url(_post_data=None, params=None, **kwargs):
        return {
            "show_id": 4,
        }

    @staticmethod
    def show_url(_post_data=None, params=None, **kwargs):
        return {
            "name": "Test show",
        }

    @staticmethod
    def file_url(_post_data=None, params=None, **kwargs):
        return {
            "item_url": "http://localhost/api/v2/files/3/",
            "name": "",
            "mime": "audio/mp3",
            "ftype": "audioclip",
            "filepath": "imported/1/test.mp3",
            "import_status": 0,
            "currently_accessing": 0,
            "mtime": "2021-07-01T23:13:43Z",
            "utime": "2021-07-01T23:12:46Z",
            "md5": "202ae33a642ce475bd8b265ddb11c139",
            "track_title": "Test file.mp3",
            "bit_rate": 320000,
            "sample_rate": 44100,
            "length": "01:58:04.463600",
            "genre": "Test",
            "channels": 2,
            "file_exists": True,
            "replay_gain": "-5.68",
            "cuein": "00:00:00.142404",
            "cueout": "01:58:04.463583",
            "silan_check": False,
            "hidden": False,
            "is_scheduled": True,
            "is_playlist": False,
            "filesize": 283379568,
            "track_type": "MUS",
            "directory": "http://localhost/api/v2/music-dirs/1/",
            "owner": "http://localhost/api/v2/users/1/",
        }


def test_get_schedule(monkeypatch, config):
    client = AirtimeApiClient(None, config)
    client.services = MockRequestProvider()
    schedule = client.get_schedule()
    assert schedule == {
        "media": {
            "2021-07-05-11-00-00": {
                "id": 3,
                "type": "file",
                "metadata": MockRequestProvider.file_url(),
                "row_id": 1,
                "uri": "http://localhost/api/v2/file/3",
                "fade_in": 500.0,
                "fade_out": 1000.0,
                "cue_in": 0.142404,
                "cue_out": 7084.463583,
                "start": "2021-07-05-11-00-00",
                "end": "2021-07-05-11-01-00",
                "show_name": "Test show",
                "replay_gain": "-5.68",
                "independent_event": False,
                "filesize": 283379568,
            },
        },
    }
