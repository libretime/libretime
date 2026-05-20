from datetime import datetime
from http.cookies import SimpleCookie

import pytest
from rest_framework.test import APIClient

from ...legacy.models import (
    LEGACY_SESSION_LIFETIME,
    LegacySession,
    legacy_session_encode,
)
from ...legacy.tests.models_test import make_legacy_session_data


@pytest.mark.django_db
def test_auth_login(api_client: APIClient):
    response = api_client.post(
        "/api/v2/auth/login/",
        {"username": "admin", "password": "admin"},
        format="json",
    )
    assert response.status_code == 204
    assert "sessionid" in response.cookies
    assert response.cookies["sessionid"].value != ""
    assert "PHPSESSID" not in response.cookies


@pytest.mark.django_db
def test_auth_login_with_legacy(api_client: APIClient):
    legacy_session = LegacySession.objects.create(
        id="jikjr9dlrjl9b0jn9r6tnci47a",
        modified=datetime.now().timestamp(),
        lifetime=LEGACY_SESSION_LIFETIME,
        data=legacy_session_encode(make_legacy_session_data()),
    )

    api_client.cookies = SimpleCookie({"PHPSESSID": legacy_session.id})
    response = api_client.post(
        "/api/v2/auth/login/",
        {"username": "admin", "password": "admin"},
        format="json",
    )
    assert response.status_code == 204
    assert "sessionid" in response.cookies
    assert response.cookies["sessionid"].value != ""
    assert "PHPSESSID" in response.cookies
    assert response.cookies["PHPSESSID"].value != ""
    assert response.cookies["PHPSESSID"].value != legacy_session.id


@pytest.mark.django_db
def test_auth_login_invalid(api_client: APIClient):
    response = api_client.post(
        "/api/v2/auth/login/",
        {"username": "admin", "password": "invalid"},
        format="json",
    )
    assert response.status_code == 400
    assert "sessionid" not in response.cookies


@pytest.mark.django_db
def test_auth_logout(api_client: APIClient):
    api_client.login(username="admin", password="admin")

    response = api_client.post("/api/v2/auth/logout/")
    assert response.status_code == 200
    assert "sessionid" in response.cookies
    assert response.cookies["sessionid"].value == ""
    assert "PHPSESSID" not in response.cookies


@pytest.mark.django_db
def test_auth_logout_with_legacy(api_client: APIClient):
    api_client.login(username="admin", password="admin")

    legacy_session = LegacySession.objects.create(
        id="jikjr9dlrjl9b0jn9r6tnci47a",
        modified=datetime.now().timestamp(),
        lifetime=LEGACY_SESSION_LIFETIME,
        data=legacy_session_encode(make_legacy_session_data()),
    )
    api_client.cookies["PHPSESSID"] = legacy_session.id

    response = api_client.post("/api/v2/auth/logout/")
    assert response.status_code == 200
    assert "sessionid" in response.cookies
    assert response.cookies["sessionid"].value == ""
    assert "PHPSESSID" in response.cookies
    assert response.cookies["PHPSESSID"].value == ""
