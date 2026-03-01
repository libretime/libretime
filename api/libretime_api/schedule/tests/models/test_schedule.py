from datetime import datetime, timedelta

from django.test import TestCase

from ...models import Schedule, ShowInstance


class TestSchedule(TestCase):
    @classmethod
    def setUpTestData(cls):
        cls.show_instance = ShowInstance(
            created_at=datetime(year=2021, month=10, day=1, hour=12),
            starts_at=datetime(year=2021, month=10, day=2, hour=1),
            ends_at=datetime(year=2021, month=10, day=2, hour=2),
        )
        cls.length = timedelta(minutes=10)
        cls.cue_in = timedelta(seconds=1)
        cls.cue_out = cls.length - timedelta(seconds=4)

    def create_schedule(self, starts_at):
        return Schedule(
            starts_at=starts_at,
            ends_at=starts_at + self.length,
            cue_in=self.cue_in,
            cue_out=self.cue_out,
            instance=self.show_instance,
        )

    def test_get_cue_out(self):
        # No overlapping schedule datetimes, normal usecase:
        item1_starts = datetime(year=2021, month=10, day=2, hour=1, minute=30)
        item1 = self.create_schedule(item1_starts)
        self.assertEqual(item1.get_cue_out(), self.cue_out)
        self.assertEqual(item1.get_ends_at(), item1_starts + self.length)

        # Mixed overlapping schedule datetimes (only ends is overlapping):
        item_2_starts = datetime(year=2021, month=10, day=2, hour=1, minute=55)
        item_2 = self.create_schedule(item_2_starts)
        self.assertEqual(item_2.get_cue_out(), timedelta(minutes=5))
        self.assertEqual(item_2.get_ends_at(), self.show_instance.ends_at)

        # Fully overlapping schedule datetimes (starts and ends are overlapping):
        item3_starts = datetime(year=2021, month=10, day=2, hour=2, minute=1)
        item3 = self.create_schedule(item3_starts)
        self.assertEqual(item3.get_cue_out(), self.cue_out)
        self.assertEqual(item3.get_ends_at(), self.show_instance.ends_at)

    def test_overbooked(self):
        # Starts before the schedule ends
        item1_starts = datetime(year=2021, month=10, day=2, hour=1, minute=30)
        item1 = self.create_schedule(item1_starts)
        self.assertFalse(item1.overbooked)

        # Starts after the schedule ends
        item_2_starts = datetime(year=2021, month=10, day=2, hour=3)
        item_2 = self.create_schedule(item_2_starts)
        self.assertTrue(item_2.overbooked)
