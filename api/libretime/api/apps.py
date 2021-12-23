from pathlib import Path

from django.apps import AppConfig
from django.db.models.signals import pre_save

here = Path(__file__).parent


class LibreTimeAPIConfig(AppConfig):
    name = "libretime.api"
    label = "libretime_api"
    path = here
    verbose_name = "LibreTime API"
    default_auto_field = "django.db.models.AutoField"
