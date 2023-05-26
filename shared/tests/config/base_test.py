from os import environ
from pathlib import Path
from typing import List, Union
from unittest import mock

from pydantic import AnyHttpUrl, BaseModel, Field
from pytest import mark, raises
from typing_extensions import Annotated

from libretime_shared.config import (
    BaseConfig,
    DatabaseConfig,
    IcecastOutput,
    RabbitMQConfig,
    ShoutcastOutput,
    no_trailing_slash_validator,
)

AnyOutput = Annotated[
    Union[IcecastOutput, ShoutcastOutput],
    Field(discriminator="kind"),
]


# pylint: disable=too-few-public-methods
class FixtureConfig(BaseConfig):
    public_url: AnyHttpUrl
    api_key: str
    allowed_hosts: List[str] = []
    database: DatabaseConfig
    rabbitmq: RabbitMQConfig = RabbitMQConfig()
    outputs: List[AnyOutput]

    # Validators
    _public_url_no_trailing_slash = no_trailing_slash_validator("public_url")


FIXTURE_CONFIG_RAW = """
public_url: http://libretime.example.org/
api_key: "f3bf04fc"
allowed_hosts:
  - example.com
  - sub.example.com

# Comment !
database:
  host: "localhost"
  port: 5432

ignored: "ignored"

outputs:
  - enabled: true
    kind: icecast
    host: localhost
    port: 8000
    mount: main.ogg
    source_password: hackme
    audio:
      format: ogg
      bitrate: 256
"""


def test_base_config(tmp_path: Path):
    config_filepath = tmp_path / "config.yml"
    config_filepath.write_text(FIXTURE_CONFIG_RAW)

    with mock.patch.dict(
        environ,
        {
            "LIBRETIME_API": "invalid",
            "LIBRETIME_DATABASE_PORT": "8888",
            "LIBRETIME_DATABASE": "invalid",
            "LIBRETIME_RABBITMQ": "invalid",
            "LIBRETIME_RABBITMQ_HOST": "changed",
            "LIBRETIME_OUTPUTS_0_ENABLED": "false",
            "LIBRETIME_OUTPUTS_0_HOST": "changed",
            "WRONGPREFIX_API_KEY": "invalid",
        },
    ):
        config = FixtureConfig(config_filepath)

        assert config.public_url == "http://libretime.example.org"
        assert config.api_key == "f3bf04fc"
        assert config.allowed_hosts == ["example.com", "sub.example.com"]
        assert config.database.host == "localhost"
        assert config.database.port == 8888
        assert config.rabbitmq.host == "changed"
        assert config.rabbitmq.port == 5672
        assert config.outputs[0].enabled is False
        assert config.outputs[0].kind == "icecast"
        assert config.outputs[0].host == "changed"
        assert config.outputs[0].audio.format == "ogg"

    # Optional model: loading default values (rabbitmq)
    with mock.patch.dict(environ, {}):
        config = FixtureConfig(config_filepath)
        assert config.allowed_hosts == ["example.com", "sub.example.com"]
        assert config.rabbitmq.host == "localhost"
        assert config.rabbitmq.port == 5672

    # Optional model: overriding using environment (rabbitmq)
    with mock.patch.dict(
        environ,
        {
            "LIBRETIME_RABBITMQ_HOST": "changed",
            "LIBRETIME_ALLOWED_HOSTS": "example.com, changed.example.com",
        },
    ):
        config = FixtureConfig(config_filepath)
        assert config.allowed_hosts == ["example.com", "changed.example.com"]
        assert config.rabbitmq.host == "changed"
        assert config.rabbitmq.port == 5672


# pylint: disable=too-few-public-methods
class RequiredModel(BaseModel):
    api_key: str
    with_default: str = "original"


# pylint: disable=too-few-public-methods
class FixtureWithRequiredSubmodelConfig(BaseConfig):
    required: RequiredModel


FIXTURE_WITH_REQUIRED_SUBMODEL_CONFIG_RAW = """
required:
    api_key: "test_key"
"""


def test_base_config_required_submodel(tmp_path: Path):
    config_filepath = tmp_path / "config.yml"
    config_filepath.write_text(FIXTURE_WITH_REQUIRED_SUBMODEL_CONFIG_RAW)

    # With config file
    with mock.patch.dict(environ, {}):
        config = FixtureWithRequiredSubmodelConfig(config_filepath)
        assert config.required.api_key == "test_key"
        assert config.required.with_default == "original"

    # With env variables
    with mock.patch.dict(environ, {"LIBRETIME_REQUIRED_API_KEY": "test_key"}):
        config = FixtureWithRequiredSubmodelConfig(None)
        assert config.required.api_key == "test_key"
        assert config.required.with_default == "original"

    # With env variables override
    with mock.patch.dict(environ, {"LIBRETIME_REQUIRED_API_KEY": "changed"}):
        config = FixtureWithRequiredSubmodelConfig(config_filepath)
        assert config.required.api_key == "changed"
        assert config.required.with_default == "original"

    # With env variables default override
    with mock.patch.dict(
        environ,
        {
            "LIBRETIME_REQUIRED_API_KEY": "changed",
            "LIBRETIME_REQUIRED_WITH_DEFAULT": "changed",
        },
    ):
        config = FixtureWithRequiredSubmodelConfig(config_filepath)
        assert config.required.api_key == "changed"
        assert config.required.with_default == "changed"

    # Raise validation error
    with mock.patch.dict(environ, {}):
        with raises(SystemExit):
            FixtureWithRequiredSubmodelConfig(None)


def test_base_config_from_init() -> None:
    class FromInitFixtureConfig(BaseConfig):
        found: str
        override: str

    with mock.patch.dict(environ, {"LIBRETIME_OVERRIDE": "changed"}):
        config = FromInitFixtureConfig(
            found="changed",
            override="invalid",
        )

    assert config.found == "changed"
    assert config.override == "changed"


FIXTURE_CONFIG_RAW_MISSING = """
database:
    host: "localhost"
"""

FIXTURE_CONFIG_RAW_INVALID = """
database
    host: "localhost"
"""


@mark.parametrize(
    "raw,exception",
    [
        (FIXTURE_CONFIG_RAW_INVALID, SystemExit),
        (FIXTURE_CONFIG_RAW_MISSING, SystemExit),
    ],
)
def test_load_config_error(tmp_path: Path, raw, exception):
    config_filepath = tmp_path / "config.yml"
    config_filepath.write_text(raw)

    with raises(exception):
        with mock.patch.dict(environ, {}):
            FixtureConfig(config_filepath)
