#!/usr/bin/env python3

# Generate the configuration file schema.

from pathlib import Path
from sys import argv

from libretime_playout.config import PlayoutConfig
from libretime_shared.config import (
    BaseConfig,
    DatabaseConfig,
    GeneralConfig,
    RabbitMQConfig,
)


class Config(BaseConfig):
    """
    LibreTime configuration.
    """

    general: GeneralConfig
    database: DatabaseConfig = DatabaseConfig()
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
    playout: PlayoutConfig = PlayoutConfig()


schema_filepath = Path(argv[1])
schema_filepath.write_text(Config.schema_json(indent=2), encoding="utf-8")
