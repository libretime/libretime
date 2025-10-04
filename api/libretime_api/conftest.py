import pytest
from django.conf import settings
from rest_framework.test import APIClient


@pytest.fixture()
def api_client():
    obj = APIClient()
    obj.credentials(
        HTTP_AUTHORIZATION=f"Api-Key {settings.CONFIG.general.api_key}",
    )
    return obj


@pytest.fixture()
def host_user():
    from .core.models import Role, User

    return User.objects.create_user(
        role=Role.HOST,
        username="test",
        password="test",
        email="test@example.com",
        first_name="test",
        last_name="user",
    )
