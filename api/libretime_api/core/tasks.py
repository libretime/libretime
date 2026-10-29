from datetime import timedelta

from celery import shared_task
from celery.utils.log import get_task_logger
from django.core import mail
from django.db.models import F
from django.utils import timezone

from .models import Session

logger = get_task_logger(__name__)


SEND_EMAIL_SOFT_TIME_LIMIT = timedelta(minutes=1).seconds
SEND_EMAIL_TIME_LIMIT = timedelta(minutes=2).seconds


@shared_task(
    acks_late=True,
    soft_time_limit=SEND_EMAIL_SOFT_TIME_LIMIT,
    time_limit=SEND_EMAIL_TIME_LIMIT,
)
def send_mail(
    subject: str,
    message: str,
    recipients: list[str],
) -> None:
    """
    Send a mail to the given recipients.

    Args:
        subject: Subject of the mail.
        message: Message of the mail.
        recipients: List of recipients for the mail.
    """
    mail.send_mail(
        subject,
        message,
        None,  # From
        recipients,
    )


@shared_task()
def clean_expired_sessions() -> int:
    """
    Delete expired php sessions.

    Returns:
        Number of deleted sessions.
    """
    now = timezone.now().timestamp()

    count, _ = (
        Session.objects.alias(expired_at=F("updated_at") + F("lifetime"))
        .filter(expired_at__lt=now)
        .delete()
    )

    logger.info("deleted %d expired sessions", count)
    return count
