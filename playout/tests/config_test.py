import pytest

from libretime_playout.config import Config


def test_general_config_cache_ahead_hours():
    config = {
        "general": {
            "public_url": "http://localhost:8080",
            "api_key": "api_key",
            "secret_key": "secret_key",
            "cache_ahead_hours": 1,
        }
    }
    with pytest.deprecated_call():
        Config(None, **config)
