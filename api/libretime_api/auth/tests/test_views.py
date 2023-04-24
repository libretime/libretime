import pytest
from rest_framework.test import APIClient


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
