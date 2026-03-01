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
