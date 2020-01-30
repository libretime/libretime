from django.apps import AppConfig
from django.db.models.signals import pre_save

class LibreTimeAPIConfig(AppConfig):
    name = 'libretimeapi'
    verbose_name = 'LibreTime API'
    default_auto_field = 'django.db.models.AutoField'
