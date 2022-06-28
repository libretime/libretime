from libretime_playout.schedule import get_schedule


class ApiClientServicesMock:
    @staticmethod
    def schedule_url(_post_data=None, params=None, **kwargs):
        return [
            {
                "item_url": "http://192.168.10.100:8081/api/v2/schedule/17/",
                "id": 17,
                "starts": "2022-03-04T15:30:00Z",
                "ends": "2022-03-04T15:33:50.674340Z",
                "file": "http://192.168.10.100:8081/api/v2/files/1/",
                "file_id": 1,
                "stream": None,
                "clip_length": "00:03:50.674340",
                "fade_in": "00:00:00.500000",
                "fade_out": "00:00:00.500000",
                "cue_in": "00:00:01.310660",
                "cue_out": "00:03:51.985000",
                "media_item_played": False,
                "instance": "http://192.168.10.100:8081/api/v2/show-instances/3/",
                "instance_id": 3,
                "playout_status": 1,
                "broadcasted": 0,
                "position": 0,
            },
            {
                "item_url": "http://192.168.10.100:8081/api/v2/schedule/18/",
                "id": 18,
                "starts": "2022-03-04T15:33:50.674340Z",
                "ends": "2022-03-04T16:03:50.674340Z",
                "file": None,
                "stream": "http://192.168.10.100:8081/api/v2/webstreams/1/",
                "stream_id": 1,
                "clip_length": "00:30:00",
                "fade_in": "00:00:00.500000",
                "fade_out": "00:00:00.500000",
                "cue_in": "00:00:00",
                "cue_out": "00:30:00",
                "media_item_played": False,
                "instance": "http://192.168.10.100:8081/api/v2/show-instances/3/",
                "instance_id": 3,
                "playout_status": 1,
                "broadcasted": 0,
                "position": 1,
            },
        ]

    @staticmethod
    def show_instance_url(_post_data=None, params=None, **kwargs):
        return {
            "item_url": "http://192.168.10.100:8081/api/v2/show-instances/3/",
            "id": 3,
            "description": "",
            "starts": "2022-03-04T15:30:00Z",
            "ends": "2022-03-04T16:30:00Z",
            "record": 0,
            "rebroadcast": 0,
            "time_filled": "00:33:50.674340",
            "created": "2022-03-04T15:05:36Z",
            "last_scheduled": "2022-03-04T15:05:46Z",
            "modified_instance": False,
            "autoplaylist_built": False,
            "show": "http://192.168.10.100:8081/api/v2/shows/3/",
            "show_id": 3,
            "instance": None,
            "file": None,
        }

    @staticmethod
    def show_url(_post_data=None, params=None, **kwargs):
        return {
            "item_url": "http://192.168.10.100:8081/api/v2/shows/3/",
            "id": 3,
            "name": "Test",
            "url": "",
            "genre": "",
            "description": "",
            "color": "",
            "background_color": "",
            "linked": False,
            "is_linkable": True,
            "image_path": "",
            "has_autoplaylist": False,
            "autoplaylist_repeat": False,
            "autoplaylist": None,
        }

    @staticmethod
    def file_url(_post_data=None, params=None, **kwargs):
        return {
            "id": 1,
            "url": None,
            "replay_gain": "-8.77",
            "size": 9505222,
        }

    @staticmethod
    def webstream_url(_post_data=None, params=None, **kwargs):
        return {
            "item_url": "http://192.168.10.100:8081/api/v2/webstreams/1/",
            "id": 1,
            "name": "Test",
            "description": "",
            "url": "http://some-other-radio:8800/main.ogg",
            "length": "00:30:00",
            "creator_id": 1,
            "mtime": "2022-03-04T13:11:20Z",
            "utime": "2022-03-04T13:11:20Z",
            "lptime": None,
            "mime": "application/ogg",
        }


class ApiClientMock:
    services = ApiClientServicesMock()


def test_get_schedule():
    api_client = ApiClientMock()
    assert get_schedule(api_client) == {
        "media": {
            "2022-03-04-15-30-00": {
                "type": "file",
                "independent_event": False,
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
                "metadata": ApiClientServicesMock.file_url(),
                "replay_gain": "-8.77",
                "filesize": 9505222,
            },
            "2022-03-04-15-33-50": {
                "type": "stream_buffer_start",
                "independent_event": True,
                "row_id": 18,
                "start": "2022-03-04-15-33-45",
                "end": "2022-03-04-15-33-45",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
            "2022-03-04-15-33-50_0": {
                "type": "stream_output_start",
                "independent_event": True,
                "row_id": 18,
                "start": "2022-03-04-15-33-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
                "show_name": "Test",
            },
            "2022-03-04-16-03-50": {
                "type": "stream_buffer_end",
                "independent_event": True,
                "row_id": 18,
                "start": "2022-03-04-16-03-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
            "2022-03-04-16-03-50_0": {
                "type": "stream_output_end",
                "independent_event": True,
                "row_id": 18,
                "start": "2022-03-04-16-03-50",
                "end": "2022-03-04-16-03-50",
                "uri": "http://some-other-radio:8800/main.ogg",
                "id": 1,
            },
        }
    }
