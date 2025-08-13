import os

from celery import Celery, signals
from celery.schedules import crontab
from celery.utils.log import get_task_logger
from django.conf import settings
from libretime_api_client.v1 import ApiClient as LegacyClient

from . import PACKAGE, VERSION

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "libretime_api.settings.prod")
os.environ.setdefault("LIBRETIME_CONFIG_FILEPATH", "/etc/libretime/config.yml")

app = Celery("worker")

app.config_from_object("django.conf:settings", namespace="CELERY")
app.autodiscover_tasks(
    [
        "libretime_api.podcasts.tasks",
    ]
)

app.conf.beat_schedule = {
    "podcasts-delete-failed-download": {
        "task": "libretime_api.podcasts.tasks.delete_failed_download",
        "schedule": crontab(minute="*/5"),
    },
    "legacy-trigger-task-manager": {
        "task": "libretime_api.worker.legacy_trigger_task_manager",
        "schedule": crontab(minute="*/5"),
    },
}

logger = get_task_logger(__name__)


@signals.worker_init.connect
def init_sentry(**_kwargs):
    if "SENTRY_DSN" in os.environ:
        logger.info("installing sentry")
        # pylint: disable=import-outside-toplevel
        import sentry_sdk
        from sentry_sdk.integrations.celery import CeleryIntegration

        sentry_sdk.init(
            traces_sample_rate=1.0,
            release=f"{PACKAGE}@{VERSION}",
            integrations=[
                CeleryIntegration(),
            ],
        )


@app.task(ignore_result=True)
def legacy_trigger_task_manager():
    """
    Trigger the legacy task manager to perform background tasks.
    """
    legacy_client = LegacyClient(
        base_url=settings.CONFIG.general.public_url,
        api_key=settings.CONFIG.general.api_key,
    )
    legacy_client.trigger_task_manager()
