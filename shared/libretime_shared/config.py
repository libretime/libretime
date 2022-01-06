import sys
from os import environ
from pathlib import Path
from typing import Any, Dict, Optional

from loguru import logger

# pylint: disable=no-name-in-module
from pydantic import BaseModel, ValidationError
from pydantic.fields import ModelField
from yaml import YAMLError, safe_load

DEFAULT_ENV_PREFIX = "LIBRETIME"


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
        filepath: Optional[Path] = None,
    ) -> None:
        file_values = self._load_file_values(filepath)
        env_values = self._load_env_values(env_prefix)

        try:
            super().__init__(
                **{
                    **file_values,
                    **env_values,
                },
            )
        except ValidationError as error:
            logger.critical(error)
            sys.exit(1)

    def _load_env_values(self, env_prefix: str) -> Dict[str, Any]:
        return self._get_fields_from_env(env_prefix, self.__fields__)

    def _get_fields_from_env(
        self,
        env_prefix: str,
        fields: Dict[str, ModelField],
    ) -> Dict[str, Any]:
        result: Dict[str, Any] = {}

        for field in fields.values():
            env_name = (env_prefix + "_" + field.name).upper()

            if field.is_complex():
                result[field.name] = self._get_fields_from_env(
                    env_name,
                    field.type_.__fields__,
                )
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

        try:
            return safe_load(filepath.read_text(encoding="utf-8"))
        except YAMLError as error:
            logger.critical(error)
            sys.exit(1)


# pylint: disable=too-few-public-methods
class Database(BaseModel):
    host: str = "localhost"
    port: int = 5432
    name: str = "libretime"
    user: str = "libretime"
    password: str = "libretime"


# pylint: disable=too-few-public-methods
class RabbitMQ(BaseModel):
    host: str = "localhost"
    port: int = 5672
    name: str = "libretime"
    user: str = "libretime"
    password: str = "libretime"
    vhost: str = "/libretime"
