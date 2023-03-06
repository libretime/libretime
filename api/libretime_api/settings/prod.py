from os import getenv

from django.conf import settings

# pylint: disable=unused-import
from ._internal import (
    API_VERSION,
    AUTH_PASSWORD_VALIDATORS,
    AUTH_USER_MODEL,
    DEBUG,
    DEFAULT_AUTO_FIELD,
    INSTALLED_APPS,
    MIDDLEWARE,
    REST_FRAMEWORK,
    ROOT_URLCONF,
    SPECTACULAR_SETTINGS,
    STATIC_URL,
    TEMPLATES,
    WSGI_APPLICATION,
    setup_logger,
)
from ._schema import Config

LIBRETIME_LOG_FILEPATH = getenv("LIBRETIME_LOG_FILEPATH")
LIBRETIME_CONFIG_FILEPATH = getenv("LIBRETIME_CONFIG_FILEPATH")

CONFIG = Config(LIBRETIME_CONFIG_FILEPATH)

SECRET_KEY = CONFIG.general.api_key

ALLOWED_HOSTS = ["*"]

LOGGING = setup_logger(LIBRETIME_LOG_FILEPATH)

# Database
# https://docs.djangoproject.com/en/3.2/ref/settings/#databases

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

if DEBUG:
    # Print emails to stdout
    EMAIL_BACKEND = "django.core.mail.backends.console.EmailBackend"
else:
    EMAIL_BACKEND = "django.core.mail.backends.smtp.EmailBackend"
    DEFAULT_FROM_EMAIL = CONFIG.email.from_address
    EMAIL_USE_TLS = CONFIG.email.tls
    EMAIL_HOST = CONFIG.email.host
    EMAIL_PORT = CONFIG.email.port
    EMAIL_HOST_USER = CONFIG.email.user
    EMAIL_HOST_PASSWORD = CONFIG.email.password


# Internationalization
# https://docs.djangoproject.com/en/3.2/topics/i18n/

LANGUAGE_CODE = "en-us"
TIME_ZONE = "UTC"
USE_I18N = True
USE_TZ = True
