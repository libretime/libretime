from libretime_api_client.utils import RequestProvider


def test_request_provider_init():
    request_provider = RequestProvider(
        base_url="http://localhost/test",
        api_key="test_key",
        endpoints={},
    )
    assert len(request_provider.available_requests()) == 0


def test_request_provider_contains():
    endpoints = {
        "upload_recorded": "/1/",
        "update_media_url": "/2/",
        "list_all_db_files": "/3/",
    }
    request_provider = RequestProvider(
        base_url="http://localhost/test",
        api_key="test_key",
        endpoints=endpoints,
    )

    for endpoint in endpoints:
        assert endpoint in request_provider.requests
