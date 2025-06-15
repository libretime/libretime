from os import environ, getenv
from typing import Optional

from .. import PACKAGE, VERSION

API_VERSION = "2.0.0"

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = getenv("LIBRETIME_DEBUG", "false").lower() == "true"

# Application definition

INSTALLED_APPS = [
    "libretime_api.legacy",
    "libretime_api.core",
    "libretime_api.history",
    "libretime_api.storage",
    "libretime_api.podcasts",
    "libretime_api.schedule",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    "django_celery_results",
    "rest_framework",
    "django_filters",
    "drf_spectacular",
    "corsheaders",
]

MIDDLEWARE = [
    "corsheaders.middleware.CorsMiddleware",
    "django.middleware.security.SecurityMiddleware",
    "django.contrib.sessions.middleware.SessionMiddleware",
    "django.middleware.common.CommonMiddleware",
    "django.middleware.csrf.CsrfViewMiddleware",
    "django.contrib.auth.middleware.AuthenticationMiddleware",
    "django.contrib.messages.middleware.MessageMiddleware",
    "django.middleware.clickjacking.XFrameOptionsMiddleware",
]

ROOT_URLCONF = "libretime_api.urls"

TEMPLATES = [
    {
        "BACKEND": "django.template.backends.django.DjangoTemplates",
        "DIRS": [],
        "APP_DIRS": True,
        "OPTIONS": {
            "context_processors": [
                "django.template.context_processors.debug",
                "django.template.context_processors.request",
                "django.contrib.auth.context_processors.auth",
                "django.contrib.messages.context_processors.messages",
            ],
        },
    },
]

WSGI_APPLICATION = "libretime_api.wsgi.application"

# Static files (CSS, JavaScript, Images)
# https://docs.djangoproject.com/en/3.2/howto/static-files/

STATIC_URL = "/api/v2/static/"

# Default primary key field type
# https://docs.djangoproject.com/en/3.2/ref/settings/#default-auto-field

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"


# Password validation
# https://docs.djangoproject.com/en/3.2/ref/settings/#auth-password-validators

AUTH_PASSWORD_VALIDATORS = [
    {
        "NAME": "django.contrib.auth.password_validation.UserAttributeSimilarityValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.MinimumLengthValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.CommonPasswordValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.NumericPasswordValidator",
    },
]


# Logging
# https://docs.djangoproject.com/en/3.2/topics/logging/#configuring-logging


def setup_logger(log_filepath: Optional[str]):
    logging_handlers = {
        "console": {
            "level": "INFO",
            "class": "logging.StreamHandler",
            "formatter": "simple",
        },
    }

    if log_filepath is not None:
        logging_handlers["file"] = {
            "level": "DEBUG",
            "class": "logging.FileHandler",
            "filename": log_filepath,
            "formatter": "verbose",
        }

    return {
        "version": 1,
        "disable_existing_loggers": False,
        "formatters": {
            "simple": {
                "format": "{levelname} {message}",
                "style": "{",
            },
            "verbose": {
                "format": "{asctime} {module} {levelname} {message}",
                "style": "{",
            },
        },
        "handlers": logging_handlers,
        "loggers": {
            "django": {
                "handlers": logging_handlers.keys(),
                "level": "INFO",
                "propagate": True,
            },
            "libretime_api": {
                "handlers": logging_handlers.keys(),
                "level": "INFO",
                "propagate": True,
            },
        },
    }


# Rest Framework
# https://www.django-rest-framework.org/api-guide/settings/

renderer_classes = ["rest_framework.renderers.JSONRenderer"]
if DEBUG:
    renderer_classes += ["rest_framework.renderers.BrowsableAPIRenderer"]

REST_FRAMEWORK = {
    "DEFAULT_RENDERER_CLASSES": renderer_classes,
    "DEFAULT_AUTHENTICATION_CLASSES": (
        "rest_framework.authentication.SessionAuthentication",
        "rest_framework.authentication.BasicAuthentication",
    ),
    "DEFAULT_PERMISSION_CLASSES": [
        "libretime_api.permissions.IsSystemTokenOrUser",
    ],
    "DEFAULT_FILTER_BACKENDS": [
        "django_filters.rest_framework.DjangoFilterBackend",
    ],
    "DEFAULT_SCHEMA_CLASS": "drf_spectacular.openapi.AutoSchema",
    "URL_FIELD_NAME": "item_url",
}

# Auth
# https://docs.djangoproject.com/en/3.2/topics/auth/customizing/#substituting-a-custom-user-model

AUTH_USER_MODEL = "core.User"

# Spectacular
# https://drf-spectacular.readthedocs.io/en/latest/settings.html

SPECTACULAR_ENUM_NAME_OVERRIDES = {
    "FileImportStatusEnum": "libretime_api.storage.models.File.ImportStatus",
    "PlaylistContentKindEnum": "libretime_api.schedule.models.PlaylistContent.Kind",
    "SmartBlockKindEnum": "libretime_api.schedule.models.SmartBlock.Kind",
}

SPECTACULAR_SETTINGS = {
    "TITLE": "LibreTime API",
    "DESCRIPTION": "Radio Broadcast & Automation Platform",
    "VERSION": API_VERSION,
    "ENUM_NAME_OVERRIDES": SPECTACULAR_ENUM_NAME_OVERRIDES,
}

# Sentry
# https://docs.sentry.io/platforms/python/guides/django/
if "SENTRY_DSN" in environ:
    # pylint: disable=import-outside-toplevel
    import sentry_sdk
    from sentry_sdk.integrations.django import DjangoIntegration

    sentry_sdk.init(
        traces_sample_rate=1.0,
        release=f"{PACKAGE}@{VERSION}",
        integrations=[
            DjangoIntegration(),
        ],
    )

# Celery
# https://docs.celeryq.dev/en/stable/userguide/configuration.html#configuration

# CELERY_BROKER_URL
CELERY_WORKER_CONCURRENCY = 2
CELERY_EVENT_QUEUE_EXPIRES = 900
CELERY_RESULT_BACKEND = "django-db"
CELERY_RESULT_PERSISTENT = True
CELERY_RESULT_EXPIRES = 24 * 60 * 60  # Clean task result from backend after 24 hours
CELERY_RESULT_EXTENDED = True
CELERY_TASK_TRACK_STARTED = True
