import os

from .._fixtures import fixture_path

os.environ.setdefault("LIBRETIME_DEBUG", "true")
os.environ.setdefault("LIBRETIME_GENERAL_PUBLIC_URL", "http://localhost")
os.environ.setdefault("LIBRETIME_GENERAL_API_KEY", "testing")
os.environ.setdefault("LIBRETIME_GENERAL_SECRET_KEY", "testing")
os.environ.setdefault("LIBRETIME_STORAGE_PATH", str(fixture_path))

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
    DEFAULT_FROM_EMAIL,
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
    USE_TZ,
    WSGI_APPLICATION,
)

# Email
# https://docs.djangoproject.com/en/4.2/topics/email/

EMAIL_BACKEND = "django.core.mail.backends.console.EmailBackend"

# Testing
# https://docs.djangoproject.com/en/3.2/ref/settings/#test-runner

TEST_RUNNER = "libretime_api.tests.runner.ManagedModelTestRunner"
