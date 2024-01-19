from typing import List

from libretime_playout.config import Config


def make_config(**kwargs) -> Config:
    return Config(
        **{
            "general": {
                "public_url": "http://localhost:8080",
                "api_key": "some_api_key",
                "secret_key": "some_secret_key",
            },
            **kwargs,
        }
    )


def make_config_with_stream(**kwargs) -> Config:
    return make_config(stream=kwargs)


TEST_STREAM_CONFIGS: List[Config] = [
    make_config(),
    make_config(
        liquidsoap={
            "harbor_ssl_certificate": "/fake/ssl.cert",
            "harbor_ssl_private_key": "/fake/ssl.key",
        },
        stream={
            "system": [{"enabled": True, "kind": "pulseaudio"}],
        },
    ),
    make_config_with_stream(
        outputs={
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
                    "enabled": False,
                    "mount": "second",
                    "source_password": "hackme",
                    "audio": {"format": "mp3", "bitrate": 256},
                },
            ],
        }
    ),
    make_config_with_stream(
        outputs={
            "shoutcast": [
                {
                    "enabled": True,
                    "source_password": "hackme",
                    "audio": {"format": "mp3", "bitrate": 256},
                    "name": "LibreTime!",
                    "description": "LibreTime Radio! Stream #1",
                    "website": "https://libretime.org",
                    "genre": "various",
                },
            ],
        }
    ),
    make_config_with_stream(
        outputs={
            "hls": [
                {
                    "enabled": True,
                    "manifest": "main",
                    "streams": [
                        {
                            "segments_prefix": "mp3low",
                            "format": "mpegts",
                            "codec": "libmp3lame",
                            "bitrate": "32k",
                            "sample_rate": "44100",
                        },
                        {
                            "segments_prefix": "mp3high",
                            "format": "mpegts",
                            "codec": "libmp3lame",
                            "bitrate": "128k",
                            "sample_rate": "44100",
                        },
                    ],
                }
            ],
        }
    ),
    make_config_with_stream(
        outputs={
            "icecast": [
                {
                    "enabled": True,
                    "mount": "main",
                    "source_password": "hackme",
                    "audio": {"format": "ogg", "bitrate": 256},
                },
            ],
            "shoutcast": [
                {
                    "enabled": True,
                    "source_password": "hackme",
                    "audio": {"format": "mp3", "bitrate": 256},
                },
            ],
            "hls": [
                {
                    "enabled": True,
                    "manifest": "main",
                    "streams": [
                        {
                            "segments_prefix": "mp3low",
                            "format": "mpegts",
                            "codec": "libmp3lame",
                            "bitrate": "32k",
                            "sample_rate": "44100",
                        },
                        {
                            "segments_prefix": "mp3high",
                            "format": "mpegts",
                            "codec": "libmp3lame",
                            "bitrate": "128k",
                            "sample_rate": "44100",
                        },
                    ],
                }
            ],
        }
    ),
    make_config_with_stream(
        outputs={
            "system": [{"enabled": True, "kind": "pulseaudio"}],
        }
    ),
    make_config_with_stream(
        outputs={
            "system": [
                {
                    "enabled": True,
                    "kind": "pulseaudio",
                    "device": "alsa_output.pci-0000_00_sink",
                }
            ],
        }
    ),
    make_config_with_stream(
        outputs={
            "system": [{"enabled": False, "kind": "alsa"}],
        }
    ),
]
