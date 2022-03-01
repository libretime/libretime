# pylint: disable=unused-import
from os import getenv

from ._internal import (
    AUTH_PASSWORD_VALIDATORS,
    AUTH_USER_MODEL,
    DEBUG,
    INSTALLED_APPS,
    MIDDLEWARE,
    REST_FRAMEWORK,
    ROOT_URLCONF,
    TEMPLATES,
    TEST_RUNNER,
    WSGI_APPLICATION,
    setup_logger,
)
from ._schema import Config

API_VERSION = "2.0.0"

LIBRETIME_LOG_FILEPATH = getenv("LIBRETIME_LOG_FILEPATH")
LIBRETIME_CONFIG_FILEPATH = getenv("LIBRETIME_CONFIG_FILEPATH")

CONFIG = Config(filepath=LIBRETIME_CONFIG_FILEPATH)

SECRET_KEY = CONFIG.general.api_key
ALLOWED_HOSTS = ["*"]

DATABASES = {
    "default": {
        "ENGINE": "django.db.backends.postgresql",
        "HOST": CONFIG.database.host,
        "PORT": CONFIG.database.port,
        "NAME": CONFIG.database.name,
        "USER": CONFIG.database.user,
        "PASSWORD": CONFIG.database.password,
    }
}

LANGUAGE_CODE = "en-us"
TIME_ZONE = "UTC"
USE_I18N = True
USE_L10N = True
USE_TZ = True

LOGGING = setup_logger(LIBRETIME_LOG_FILEPATH)
