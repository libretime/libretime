from datetime import datetime, timedelta, timezone

from django.conf import settings
from django.utils import dateparse
from model_bakery import baker
from rest_framework.test import APITestCase

from ...._fixtures import AUDIO_FILENAME


class TestScheduleViewSet(APITestCase):
    @classmethod
    def setUpTestData(cls):
        cls.path = "/api/v2/schedule/"
        cls.token = settings.CONFIG.general.api_key

    def test_schedule_item_full_length(self):
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "schedule.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(minutes=5),
        )
        schedule_item = baker.make(
            "schedule.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + file.length,
            cue_out=file.cueout,
            instance=show,
            file=file,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(
            dateparse.parse_datetime(result[0]["ends"]), schedule_item.ends
        )
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), file.cueout)

    def test_schedule_item_trunc(self):
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "schedule.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(seconds=20),
        )
        schedule_item = baker.make(
            "schedule.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + file.length,
            instance=show,
            file=file,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path)
        self.assertEqual(response.status_code, 200)
        result = response.json()
        self.assertEqual(dateparse.parse_datetime(result[0]["ends"]), show.ends)
        expected = show.ends - schedule_item.starts
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), expected)
        self.assertNotEqual(
            dateparse.parse_datetime(result[0]["ends"]), schedule_item.ends
        )

    def test_schedule_item_invalid(self):
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        show = baker.make(
            "schedule.ShowInstance",
            starts=datetime.now(tz=timezone.utc) - timedelta(minutes=5),
            ends=datetime.now(tz=timezone.utc) + timedelta(minutes=5),
        )
        schedule_item = baker.make(
            "schedule.Schedule",
            starts=datetime.now(tz=timezone.utc),
            ends=datetime.now(tz=timezone.utc) + file.length,
            cue_out=file.cueout,
            instance=show,
            file=file,
        )
        invalid_schedule_item = baker.make(  # pylint: disable=unused-variable
            "schedule.Schedule",
            starts=show.ends + timedelta(minutes=1),
            ends=show.ends + timedelta(minutes=1) + file.length,
            cue_out=file.cueout,
            instance=show,
            file=file,
        )
        self.client.credentials(HTTP_AUTHORIZATION=f"Api-Key {self.token}")
        response = self.client.get(self.path, {"is_valid": True})
        self.assertEqual(response.status_code, 200)
        result = response.json()
        # The invalid item should be filtered out and not returned
        self.assertEqual(len(result), 1)
        self.assertEqual(
            dateparse.parse_datetime(result[0]["ends"]), schedule_item.ends
        )
        self.assertEqual(dateparse.parse_duration(result[0]["cue_out"]), file.cueout)

    def test_schedule_item_range(self):
        file = baker.make(
            "storage.File",
            mime="audio/mp3",
            filepath=AUDIO_FILENAME,
            length=timedelta(seconds=40.86),
            cuein=timedelta(seconds=0),
            cueout=timedelta(seconds=40.8131),
        )
        filter_point = datetime.now(tz=timezone.utc)

        show = baker.make(
            "schedule.ShowInstance",
            starts=filter_point - timedelta(minutes=5),
            ends=filter_point + timedelta(minutes=5),
        )
        schedule_item = baker.make(
            "schedule.Schedule",
            starts=filter_point,
            ends=filter_point + file.length,
            cue_out=file.cueout,
            instance=show,
            file=file,
        )
        previous_item = baker.make(  # pylint: disable=unused-variable
            "schedule.Schedule",
            starts=filter_point - timedelta(minutes=5),
            ends=filter_point - timedelta(minutes=5) + file.length,
            cue_out=file.cueout,
            instance=show,
            file=file,
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
