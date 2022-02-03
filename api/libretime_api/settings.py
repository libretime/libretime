import os

from .utils import get_random_string, read_config_file

API_VERSION = "2.0.0"

LIBRETIME_LOG_FILEPATH = os.getenv("LIBRETIME_LOG_FILEPATH")
LIBRETIME_CONFIG_FILEPATH = os.getenv(
    "LIBRETIME_CONFIG_FILEPATH",
    "/etc/airtime/airtime.conf",
)
LIBRETIME_STATIC_ROOT = os.getenv(
    "LIBRETIME_STATIC_ROOT",
    "/usr/share/airtime/api",
)
CONFIG = read_config_file(LIBRETIME_CONFIG_FILEPATH)


# Quick-start development settings - unsuitable for production
# See https://docs.djangoproject.com/en/3.0/howto/deployment/checklist/

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = get_random_string(CONFIG.get("general", "api_key", fallback=""))

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = os.getenv("LIBRETIME_DEBUG", False)

ALLOWED_HOSTS = ["*"]


# Application definition

INSTALLED_APPS = [
    "libretime_api.apps.LibreTimeAPIConfig",
    "django.contrib.admin",
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


# Database
# https://docs.djangoproject.com/en/3.0/ref/settings/#databases

DATABASES = {
    "default": {
        "ENGINE": "django.db.backends.postgresql",
        "NAME": CONFIG.get("database", "name", fallback="libretime"),
        "USER": CONFIG.get("database", "user", fallback="libretime"),
        "PASSWORD": CONFIG.get("database", "password", fallback="libretime"),
        "HOST": CONFIG.get("database", "host", fallback="localhost"),
        "PORT": CONFIG.get("database", "port", fallback="5432"),
    }
}


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

REST_FRAMEWORK = {
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


# Internationalization
# https://docs.djangoproject.com/en/3.0/topics/i18n/

LANGUAGE_CODE = "en-us"

TIME_ZONE = "UTC"

USE_I18N = True

USE_L10N = True

USE_TZ = True


# Static files (CSS, JavaScript, Images)
# https://docs.djangoproject.com/en/3.0/howto/static-files/

STATIC_URL = "/api/static/"
if not DEBUG:
    STATIC_ROOT = LIBRETIME_STATIC_ROOT

AUTH_USER_MODEL = "libretime_api.User"

TEST_RUNNER = "libretime_api.tests.runners.ManagedModelTestRunner"


LOGGING_HANDLERS = {
    "console": {
        "level": "INFO",
        "class": "logging.StreamHandler",
        "formatter": "simple",
    },
}

if LIBRETIME_LOG_FILEPATH is not None:
    LOGGING_HANDLERS["file"] = {
        "level": "DEBUG",
        "class": "logging.FileHandler",
        "filename": LIBRETIME_LOG_FILEPATH,
        "formatter": "verbose",
    }

LOGGING = {
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
    "handlers": LOGGING_HANDLERS,
    "loggers": {
        "django": {
            "handlers": LOGGING_HANDLERS.keys(),
            "level": "INFO",
            "propagate": True,
        },
        "libretime_api": {
            "handlers": LOGGING_HANDLERS.keys(),
            "level": "INFO",
            "propagate": True,
        },
    },
}
