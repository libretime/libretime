import pytest

from libretime_playout.config import Config
from libretime_playout.liquidsoap.models import StreamPreferences


@pytest.fixture()
def config():
    return Config(
        **{
            "general": {
                "public_url": "http://localhost:8080",
                "api_key": "some_api_key",
                "secret_key": "some_secret_key",
            },
            "stream": {
                "outputs": {
                    "icecast": [
                        {
                            "enabled": True,
                            "mount": "main",
                            "source_password": "hackme",
                            "audio": {"format": "ogg", "bitrate": 256},
                            "name": "LibreTime!",
                            "description": "LibreTime Radio! Stream #1",
                            "website": "https://libretime.org",
                            "genre": "various",
                        },
                        {
                            "enabled": True,
                            "mount": "second",
                            "source_password": "hackme",
                            "audio": {"format": "mp3", "bitrate": 256},
                        },
                    ]
                }
            },
        }
    )


@pytest.fixture()
def stream_preferences():
    return StreamPreferences(
        input_fade_transition=0.0,
        message_format=0,
        message_offline="LibreTime - offline",
        replay_gain_enabled=True,
        replay_gain_offset=-3.5,
    )
