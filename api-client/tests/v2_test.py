from libretime_api_client.v2 import ApiClient


def test_api_client(requests_mock):
    api_client = ApiClient(base_url="http://localhost:8080", api_key="test-key")

    requests_mock.get(
        "http://localhost:8080/api/v2/version",
        json={"api_version": "2.0.0"},
    )

    resp = api_client.get_version()
    assert resp.json() == {"api_version": "2.0.0"}
