from datetime import timedelta

from celery import shared_task
from celery.utils.log import get_task_logger
from django.core import mail

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
