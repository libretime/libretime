from pathlib import Path

from libretime_shared.config import (
    BaseConfig,
    GeneralConfig,
    RabbitMQConfig,
    StreamConfig,
)
from pydantic import BaseModel
from typing_extensions import Literal

CACHE_DIR = Path.cwd() / "scheduler"
RECORD_DIR = Path.cwd() / "recorder"

PUSH_INTERVAL = 2
POLL_INTERVAL = 400


class PlayoutConfig(BaseModel):
    liquidsoap_host: str = "localhost"
    liquidsoap_port: int = 1234

    record_file_format: Literal["mp3", "ogg"] = "ogg"  # record_file_type
    record_bitrate: int = 256
    record_samplerate: int = 44100
    record_channels: int = 2
    record_sample_size: int = 16


class Config(BaseConfig):
    general: GeneralConfig
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
    playout: PlayoutConfig = PlayoutConfig()
    stream: StreamConfig = StreamConfig()
