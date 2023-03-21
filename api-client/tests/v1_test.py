import pytest

from libretime_api_client.v1 import ApiClient


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
        f"{base_url}/api/version",
        json={"api_version": "1.0.0"},
    )

    assert api_client.version() == "1.0.0"
