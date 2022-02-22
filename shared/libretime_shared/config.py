import sys
from configparser import ConfigParser
from os import environ
from pathlib import Path
from typing import Any, Dict, Optional, Union
from urllib.parse import urlunsplit

from loguru import logger

# pylint: disable=no-name-in-module
from pydantic import BaseModel, ValidationError
from pydantic.fields import ModelField
from pydantic.utils import deep_update
from yaml import YAMLError, safe_load

DEFAULT_ENV_PREFIX = "LIBRETIME"
DEFAULT_CONFIG_FILEPATH = Path("/etc/libretime/config.yml")

# pylint: disable=too-few-public-methods
class BaseConfig(BaseModel):
    """
    Read and validate the configuration from 'filepath' and os environment.

    :param filepath: yaml configuration file to read from
    :param env_prefix: prefix for the environment variable names
    :returns: configuration class
    """

    def __init__(
        self,
        *,
        env_prefix: str = DEFAULT_ENV_PREFIX,
        env_delimiter: str = "_",
        filepath: Optional[Union[Path, str]] = None,
    ) -> None:
        if filepath is not None:
            filepath = Path(filepath)

        file_values = self._load_file_values(filepath)
        env_values = self._load_env_values(env_prefix, env_delimiter)

        try:
            super().__init__(**deep_update(file_values, env_values))
        except ValidationError as error:
            logger.critical(error)
            sys.exit(1)

    def _load_env_values(self, env_prefix: str, env_delimiter: str) -> Dict[str, Any]:
        return self._get_fields_from_env(env_prefix, env_delimiter, self.__fields__)

    def _get_fields_from_env(
        self,
        env_prefix: str,
        env_delimiter: str,
        fields: Dict[str, ModelField],
    ) -> Dict[str, Any]:
        result: Dict[str, Any] = {}

        if env_prefix != "":
            env_prefix += env_delimiter

        for field in fields.values():
            env_name = (env_prefix + field.name).upper()

            if field.is_complex():
                children = self._get_fields_from_env(
                    env_name,
                    env_delimiter,
                    field.type_.__fields__,
                )

                if len(children) != 0:
                    result[field.name] = children
            else:
                if env_name in environ:
                    result[field.name] = environ[env_name]

        return result

    # pylint: disable=no-self-use
    def _load_file_values(
        self,
        filepath: Optional[Path] = None,
    ) -> Dict[str, Any]:
        if filepath is None:
            return {}

        # pylint: disable=fixme
        # TODO: Remove ability to load ini files once yaml if fully supported.
        if filepath.suffix == ".conf":
            config = ConfigParser()
            config.read_string(filepath.read_text(encoding="utf-8"))
            return {s: dict(config.items(s)) for s in config.sections()}

        try:
            return safe_load(filepath.read_text(encoding="utf-8"))
        except YAMLError as error:
            logger.critical(error)
            sys.exit(1)


# pylint: disable=too-few-public-methods
class GeneralConfig(BaseModel):
    api_key: str

    protocol: str = "http"
    base_url: str = "localhost"
    base_port: Optional[int]
    base_dir: str = "/"
    force_ssl: bool = False

    @property
    def public_url(self) -> str:
        scheme = "https" if self.force_ssl else self.protocol

        location = self.base_url
        if self.base_port is not None:
            location += f":{self.base_port}"

        path = self.base_dir.rstrip("/")

        return urlunsplit((scheme, location, path, None, None))


# pylint: disable=too-few-public-methods
class DatabaseConfig(BaseModel):
    host: str = "localhost"
    port: int = 5432
    name: str = "libretime"
    user: str = "libretime"
    password: str = "libretime"

    @property
    def url(self) -> str:
        return (
            f"postgresql://{self.user}:{self.password}"
            f"@{self.host}:{self.port}/{self.name}"
        )


# pylint: disable=too-few-public-methods
class RabbitMQConfig(BaseModel):
    host: str = "localhost"
    port: int = 5672
    name: str = "libretime"
    user: str = "libretime"
    password: str = "libretime"
    vhost: str = "/libretime"

    @property
    def url(self) -> str:
        return (
            f"amqp://{self.user}:{self.password}"
            f"@{self.host}:{self.port}/{self.vhost}"
        )
