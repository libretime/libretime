import pytest

from api_clients.utils import RequestProvider
from api_clients.version1 import api_config


@pytest.fixture()
def config():
    return {
        **api_config,
        "general": {
            "base_dir": "/test",
            "base_port": 80,
            "base_url": "localhost",
            "api_key": "TEST_KEY",
        },
        "api_base": "api",
    }


def test_request_provider_init(config):
    request_provider = RequestProvider(config, {})
    assert len(request_provider.available_requests()) == 0


def test_request_provider_contains(config):
    endpoints = {
        "upload_recorded": "/1/",
        "update_media_url": "/2/",
        "list_all_db_files": "/3/",
    }
    request_provider = RequestProvider(config, endpoints)
    for endpoint in endpoints:
        assert endpoint in request_provider.requests
