import os
from datetime import datetime, timedelta, timezone

from django.conf import settings
from django.contrib.auth.models import AnonymousUser
from django.utils import dateparse
from model_bakery import baker
from rest_framework.test import APIRequestFactory, APITestCase

from libretime_api.views import FileViewSet


class TestFileViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/files/{id}/download/"
        cls.token = settings.CONFIG.get("general", "api_key")

    def test_invalid(self):
        path = self.path.format(id="a")
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(path)
        self.assertEqual(response.status_code, 400)

    def test_does_not_exist(self):
        path = self.path.format(id="1")
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(path)
        self.assertEqual(response.status_code, 404)

    def test_exists(self):
        music_dir = baker.make(
            "libretime_api.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretime_api.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
        )
        path = self.path.format(id=str(f.pk))
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(path)
        self.assertEqual(response.status_code, 200)


class TestScheduleViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/schedule/"
        cls.token = settings.CONFIG.get("general", "api_key")

    def test_schedule_item_full_length(self):
        music_dir = baker.make(
            "libretime_api.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretime_api.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "libretime_api.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(minutes=5),
        )
        scheduleItem = baker.make(
            "libretime_api.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), scheduleItem.ends)
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), f.cueout)

    def test_schedule_item_trunc(self):
        music_dir = baker.make(
            "libretime_api.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretime_api.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "libretime_api.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(seconds=20),
        )
        scheduleItem = baker.make(
            "libretime_api.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + f.length,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), show.ends)
        expected = show.ends - scheduleItem.starts
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), expected)
        self.assertNotEqual(
            dateparse.parse_datetime(result[0]["ends"]), scheduleItem.ends
        )

    def test_schedule_item_invalid(self):
        music_dir = baker.make(
            "libretime_api.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretime_api.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "libretime_api.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(minutes=5),
        )
        scheduleItem = baker.make(
            "libretime_api.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        invalidScheduleItem = baker.make(
            "libretime_api.Schedule",
            starts=show.ends + timedelta(minutes=1),
            ends=show.ends + timedelta(minutes=1) + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path, {"is_valid": True})
        self.assertEqual(response.status_code, 200)
        result = response.json()
        # The invalid item should be filtered out and not returned
        self.assertEqual(len(result), 1)
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), scheduleItem.ends)
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), f.cueout)

    def test_schedule_item_range(self):
        music_dir = baker.make(
            "libretime_api.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretime_api.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        filter_point = datetime.now(tz=timezone.utc)

        show = baker.make(
            "libretime_api.ShowInstance",
            starts=filter_point - timedelta(minutes=5),
            ends=filter_point + timedelta(minutes=5),
        )
        schedule_item = baker.make(
            "libretime_api.Schedule",
            starts=filter_point,
            ends=filter_point + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        previous_item = baker.make(
            "libretime_api.Schedule",
            starts=filter_point - timedelta(minutes=5),
            ends=filter_point - timedelta(minutes=5) + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        range_start = (filter_point - timedelta(minutes=1)).isoformat(
            timespec="seconds"
        )
        range_end = (filter_point + timedelta(minutes=1)).isoformat(timespec="seconds")
        response = self.client.get(
            self.path, {"starts__range": f"{range_start},{range_end}"}
        )
        self.assertEqual(response.status_code, 200)
        result = response.json()
        # The previous_item should be filtered out and not returned
        self.assertEqual(len(result), 1)
        self.assertEqual(
            dateparse.parse_datetime(result[0]["starts"]), schedule_item.starts
        )
