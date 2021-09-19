from datetime import datetime, timedelta

from django.test import SimpleTestCase
from libretimeapi.models import Schedule, ShowInstance


class TestSchedule(SimpleTestCase):
    def test_get_cueout(self):
        show_instance = ShowInstance(
            created=datetime(year=2021, month=10, day=1, hour=12),
            starts=datetime(year=2021, month=10, day=2, hour=1),
            ends=datetime(year=2021, month=10, day=2, hour=2),
        )

        length = timedelta(minutes=10)
        cue_in = timedelta(seconds=1)
        cue_out = length - timedelta(seconds=4)

        def create_schedule(starts):
            return Schedule(
                starts=starts,
                ends=starts + length,
                cue_in=cue_in,
                cue_out=cue_out,
                instance=show_instance,
            )

        # No overlapping schedule datetimes, normal usecase:
        s1_starts = datetime(year=2021, month=10, day=2, hour=1, minute=30)
        s1 = create_schedule(s1_starts)
        self.assertEqual(s1.get_cueout(), cue_out)
        self.assertEqual(s1.get_ends(), s1_starts + length)

        # Mixed overlapping schedule datetimes (only ends is overlapping):
        s2_starts = datetime(year=2021, month=10, day=2, hour=1, minute=55)
        s2 = create_schedule(s2_starts)
        self.assertEqual(s2.get_cueout(), timedelta(minutes=5))
        self.assertEqual(s2.get_ends(), show_instance.ends)

        # Fully overlapping schedule datetimes (starts and ends are overlapping):
        s3_starts = datetime(year=2021, month=10, day=2, hour=2, minute=1)
        s3 = create_schedule(s3_starts)
        self.assertEqual(s3.get_cueout(), cue_out)
        self.assertEqual(s3.get_ends(), show_instance.ends)
