from os import getenv

# pylint: disable=unused-import
from ._internal import (
    API_VERSION,
    AUTH_PASSWORD_VALIDATORS,
    AUTH_USER_MODEL,
    DEBUG,
    DEFAULT_AUTO_FIELD,
    INSTALLED_APPS,
    MIDDLEWARE,
    REST_AUTH,
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

CONFIG = Config(LIBRETIME_CONFIG_FILEPATH)  # type: ignore[arg-type, misc]

SECRET_KEY = CONFIG.general.secret_key

ALLOWED_HOSTS = ["*"]

LOGGING = setup_logger(LIBRETIME_LOG_FILEPATH)

# CORS
# https://github.com/adamchainz/django-cors-headers

# Create an 'origin' by removing the public_url path
public_url_origin = (
    CONFIG.general.public_url[: -len(CONFIG.general.public_url.path)]
    if CONFIG.general.public_url.path
    else CONFIG.general.public_url
)

CORS_ALLOWED_ORIGINS = [public_url_origin] + CONFIG.general.allowed_cors_origins

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


# Internationalization
# https://docs.djangoproject.com/en/3.2/topics/i18n/

LANGUAGE_CODE = "en-us"
TIME_ZONE = "UTC"
USE_I18N = True
USE_TZ = True

# Email
# https://docs.djangoproject.com/en/4.2/topics/email/

EMAIL_BACKEND = "django.core.mail.backends.smtp.EmailBackend"

EMAIL_HOST = CONFIG.email.host
EMAIL_PORT = CONFIG.email.port
EMAIL_HOST_USER = CONFIG.email.user
EMAIL_HOST_PASSWORD = CONFIG.email.password
EMAIL_USE_SSL = CONFIG.email.encryption == "ssl/tls"  # implicit
EMAIL_USE_TLS = CONFIG.email.encryption == "starttls"  # explicit
EMAIL_TIMEOUT = CONFIG.email.timeout
EMAIL_SSL_KEYFILE = CONFIG.email.key_file
EMAIL_SSL_CERTFILE = CONFIG.email.cert_file

DEFAULT_FROM_EMAIL = CONFIG.email.from_email
