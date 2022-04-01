from os import getenv
from typing import Optional

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = getenv("LIBRETIME_DEBUG")

# Application definition

INSTALLED_APPS = [
    "libretime_api.apps.LibreTimeAPIConfig",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    "rest_framework",
    "django_filters",
    "drf_spectacular",
]

MIDDLEWARE = [
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

# Password validation
# https://docs.djangoproject.com/en/3.0/ref/settings/#auth-password-validators

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

# Rest Framework settings
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

# Static files (CSS, JavaScript, Images)
# https://docs.djangoproject.com/en/3.0/howto/static-files/

STATIC_URL = "/api/v2/static/"

AUTH_USER_MODEL = "libretime_api.User"

TEST_RUNNER = "libretime_api.tests.runners.ManagedModelTestRunner"

# Logging
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
