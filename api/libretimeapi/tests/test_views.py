import os
from datetime import datetime, timedelta

from django.conf import settings
from django.contrib.auth.models import AnonymousUser
from django.utils import dateparse
from libretimeapi.views import FileViewSet
from model_bakery import baker
from rest_framework.test import APIRequestFactory, APITestCase


class TestFileViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/files/{id}/download/"
        cls.token = settings.CONFIG.get("general", "api_key")

    def test_invalid(self):
        path = self.path.format(id="a")
        self.client.credentials(HTTP_AUTHORIZATION="Api-Key {}".format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 400)

    def test_does_not_exist(self):
        path = self.path.format(id="1")
        self.client.credentials(HTTP_AUTHORIZATION="Api-Key {}".format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 404)

    def test_exists(self):
        music_dir = baker.make(
            "libretimeapi.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretimeapi.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
        )
        path = self.path.format(id=str(f.pk))
        self.client.credentials(HTTP_AUTHORIZATION="Api-Key {}".format(self.token))
        response = self.client.get(path)
        self.assertEqual(response.status_code, 200)


class TestScheduleViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/schedule/"
        cls.token = settings.CONFIG.get("general", "api_key")

    def test_schedule_item_full_length(self):
        music_dir = baker.make(
            "libretimeapi.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretimeapi.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "libretimeapi.ShowInstance",
            starts=datetime.now(tz=datetime.timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=datetime.timezone.utc) + timedelta(minutes=5),
        )
        scheduleItem = baker.make(
            "libretimeapi.Schedule",
            starts=datetime.now(tz=datetime.timezone.utc),
            ends=datetime.now(tz=datetime.timezone.utc) + f.length,
            cue_out=f.cueout,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION="Api-Key {}".format(self.token))
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), scheduleItem.ends)
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), f.cueout)

    def test_schedule_item_trunc(self):
        music_dir = baker.make(
            "libretimeapi.MusicDir",
            directory=os.path.join(os.path.dirname(__file__), "resources"),
        )
        f = baker.make(
            "libretimeapi.File",
            directory=music_dir,
            mime="audio/mp3",
            filepath="song.mp3",
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "libretimeapi.ShowInstance",
            starts=datetime.now(tz=datetime.timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=datetime.timezone.utc) + timedelta(seconds=20),
        )
        scheduleItem = baker.make(
            "libretimeapi.Schedule",
            starts=datetime.now(tz=datetime.timezone.utc),
            ends=datetime.now(tz=datetime.timezone.utc) + f.length,
            instance=show,
            file=f,
        )
        self.client.credentials(HTTP_AUTHORIZATION="Api-Key {}".format(self.token))
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), show.ends)
        expected = show.ends - scheduleItem.starts
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), expected)
        self.assertNotEqual(
            dateparse.parse_datetime(result[0]["ends"]), scheduleItem.ends
        )
