from django.apps import AppConfig


class CoreConfig(AppConfig):
    default_auto_field = "django.db.models.BigAutoField"
    name = "libretime_api.core"
    verbose_name = "LibreTime Core API"
