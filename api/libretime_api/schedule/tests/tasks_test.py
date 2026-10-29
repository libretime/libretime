from datetime import datetime, timedelta

from django.utils import timezone
from model_bakery import baker

from ..models import Schedule, ShowInstance
from ..tasks import clean_overbooked_schedule


def make_instance(ends_ago: timedelta) -> ShowInstance:
    ends_at = timezone.now() - ends_ago
    return baker.make(
        ShowInstance,
        starts_at=ends_at - timedelta(hours=1),
        ends_at=ends_at,
    )


def make_schedule(
    instance: ShowInstance,
    starts_at: datetime | None = None,
    position_status: int = Schedule.PositionStatus.OUTSIDE,
    played: bool = False,
    broadcasted: int = 0,
) -> Schedule:
    """
    Make a schedule item that is overbooked and eligible for cleanup by default.
    """
    if starts_at is None:
        starts_at = instance.ends_at + timedelta(minutes=5)

    return baker.make(
        Schedule,
        instance=instance,
        starts_at=starts_at,
        ends_at=starts_at + timedelta(minutes=5),
        position_status=position_status,
        played=played,
        broadcasted=broadcasted,
        cue_in=timedelta(0),
        cue_out=timedelta(minutes=5),
    )


# pylint: disable=invalid-name,unused-argument
def test_clean_overbooked_schedule(db) -> None:
    old = make_instance(ends_ago=timedelta(days=100))
    overbooked = make_schedule(old)

    assert clean_overbooked_schedule() == 1

    assert not Schedule.objects.filter(id=overbooked.id).exists()


# pylint: disable=invalid-name,unused-argument
def test_clean_overbooked_schedule_nothing_to_delete(db) -> None:
    assert clean_overbooked_schedule() == 0


# pylint: disable=invalid-name,unused-argument
def test_clean_overbooked_schedule_keeps_items(db) -> None:
    old = make_instance(ends_ago=timedelta(days=100))
    recent = make_instance(ends_ago=timedelta(days=10))

    kept = [
        # Show instance ended less than 90 days ago.
        make_schedule(recent),
        # Item ended less than 90 days ago, even if its show instance is older.
        make_schedule(old, starts_at=old.ends_at + timedelta(days=15)),
        # Item starts before, or exactly at, the end of the show instance.
        make_schedule(old, starts_at=old.ends_at - timedelta(minutes=1)),
        make_schedule(old, starts_at=old.ends_at),
        # Item is not outside of the show instance.
        make_schedule(old, position_status=Schedule.PositionStatus.INSIDE),
        make_schedule(old, position_status=Schedule.PositionStatus.BOUNDARY),
        make_schedule(old, position_status=Schedule.PositionStatus.FILLER),
        # Item was played or broadcasted.
        make_schedule(old, played=True),
        make_schedule(old, broadcasted=1),
    ]

    assert clean_overbooked_schedule() == 0

    assert Schedule.objects.count() == len(kept)


# pylint: disable=invalid-name,unused-argument
def test_clean_overbooked_schedule_batches(db) -> None:
    old = make_instance(ends_ago=timedelta(days=100))
    for _ in range(5):
        make_schedule(old)

    assert clean_overbooked_schedule(batch_size=2) == 5

    assert not Schedule.objects.exists()
