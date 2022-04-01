from unittest.mock import MagicMock, patch

from libretime_api_client.utils import ApcUrl, ApiRequest


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
