import pytest

from libretime_api_client.v2 import ApiClient


@pytest.mark.parametrize(
    "base_url",
    [
        ("http://localhost:8080"),
        ("http://localhost:8080/base"),
    ],
)
def test_api_client(requests_mock, base_url):
    api_client = ApiClient(base_url=base_url, api_key="test-key")

    requests_mock.get(
        f"{base_url}/api/v2/version",
        json={"api_version": "2.0.0"},
    )

    resp = api_client.get_version()
    assert resp.json() == {"api_version": "2.0.0"}
