from rest_framework.test import APIClient


# pylint: disable=invalid-name,unused-argument
def test_version_get(db, api_client: APIClient):
    response = api_client.get("/api/v2/version")
    assert response.status_code == 200
    assert response.json() == {
        "api_version": "2.0.0",
    }


# pylint: disable=invalid-name,unused-argument
def test_info_get(db, api_client: APIClient):
    response = api_client.get("/api/v2/info")
    assert response.status_code == 200
    assert response.json() == {
        "station_name": "LibreTime",
    }
