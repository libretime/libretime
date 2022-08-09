from unittest.mock import MagicMock, patch

import pytest

from libretime_api_client._utils import (
    ApcUrl,
    ApiRequest,
    IncompleteUrl,
    RequestProvider,
    UrlBadParam,
)


@pytest.mark.parametrize(
    "url, params, expected",
    [
        ("one/two/three", {}, "one/two/three"),
        ("/testing/{key}", {"key": "aaa"}, "/testing/aaa"),
        (
            "/more/{key_a}/{key_b}/testing",
            {"key_a": "aaa", "key_b": "bbb"},
            "/more/aaa/bbb/testing",
        ),
    ],
)
def test_apc_url(url: str, params: dict, expected: str):
    found = ApcUrl(url)
    assert found.base_url == url
    assert found.params(**params).url() == expected


def test_apc_url_bad_param():
    url = ApcUrl("/testing/{key}")
    with pytest.raises(UrlBadParam):
        url.params(bad_key="testing")


def test_apc_url_incomplete():
    url = ApcUrl("/{one}/{two}/three").params(two="testing")
    with pytest.raises(IncompleteUrl):
        url.url()


def test_api_request_init():
    u = ApiRequest("request_name", ApcUrl("/test/ing"))
    assert u.name == "request_name"


def test_api_request_call_json():
    return_value = {"ok": "ok"}

    read = MagicMock()
    read.headers = {"content-type": "application/json"}
    read.json = MagicMock(return_value=return_value)

    with patch("requests.get") as mock_method:
        mock_method.return_value = read
        request = ApiRequest("mm", ApcUrl("http://localhost/testing"))()
        assert request == return_value


def test_api_request_call_html():
    return_value = "<html><head></head><body></body></html>"

    read = MagicMock()
    read.headers = {"content-type": "application/html"}
    read.text = MagicMock(return_value=return_value)

    with patch("requests.get") as mock_method:
        mock_method.return_value = read
        request = ApiRequest("mm", ApcUrl("http://localhost/testing"))()
        assert request.text() == return_value


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
    }
    request_provider = RequestProvider(
        base_url="http://localhost/test",
        api_key="test_key",
        endpoints=endpoints,
    )

    for endpoint in endpoints:
        assert endpoint in request_provider.requests
