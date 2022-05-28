from django.apps import AppConfig


class LegacyConfig(AppConfig):
    default_auto_field = "django.db.models.BigAutoField"
    name = "libretime_api.legacy"
    verbose_name = "LibreTime Legacy"
