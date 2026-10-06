from datetime import timedelta

from celery import shared_task
from celery.utils.log import get_task_logger
from django.db.models import F
from django.utils import timezone

from .models import Schedule

logger = get_task_logger(__name__)


@shared_task()
def clean_overbooked_schedule(batch_size: int = 10_000) -> int:
    """
    Clean unused overbooked schedule items.

    When the schedule items for a show instances are generated, the schedule generates
    more items that can fit the show instance duration. Once the show instance has aired,
    the is no need to keep the overbooked schedule items.

    Args:
        batch_size: Number of schedule items deleted per query.

    Returns:
        Number of deleted schedule items.
    """
    keep_since = timezone.now() - timedelta(days=90)

    items = (
        Schedule.objects.filter(
            # Keep the last 3 months regardless.
            instance__ends_at__lt=keep_since,
            ends_at__lt=keep_since,
            # Keep broadcasted or played schedules items.
            played=False,
            broadcasted=0,
            # Overbooked items start after the end of their show instance.
            starts_at__gt=F("instance__ends_at"),
            # Overbooked items are outside the boundary of their show instance.
            position_status=Schedule.PositionStatus.OUTSIDE,
        )
        .order_by()
        .values_list("pk", flat=True)
    )

    count = 0
    while True:
        batch_count, _ = Schedule.objects.filter(pk__in=items[:batch_size]).delete()
        count += batch_count
        if batch_count < batch_size:
            break

    logger.info("deleted %d overbooked schedule items", count)
    return count
