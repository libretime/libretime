import os

os.environ.setdefault("LIBRETIME_DEBUG", "true")
os.environ.setdefault("LIBRETIME_GENERAL_API_KEY", "testing")

# pylint: disable=wrong-import-position,unused-import
from .prod import (
    ALLOWED_HOSTS,
    API_VERSION,
    AUTH_PASSWORD_VALIDATORS,
    AUTH_USER_MODEL,
    CONFIG,
    DATABASES,
    DEBUG,
    DEFAULT_AUTO_FIELD,
    INSTALLED_APPS,
    LANGUAGE_CODE,
    LOGGING,
    MIDDLEWARE,
    REST_FRAMEWORK,
    ROOT_URLCONF,
    SECRET_KEY,
    SPECTACULAR_SETTINGS,
    STATIC_URL,
    TEMPLATES,
    TIME_ZONE,
    USE_I18N,
    USE_L10N,
    USE_TZ,
    WSGI_APPLICATION,
)

# Testing
# https://docs.djangoproject.com/en/3.2/ref/settings/#test-runner

TEST_RUNNER = "libretime_api.tests.runner.ManagedModelTestRunner"
