import sys
from os import environ
from pathlib import Path
from typing import Any, Dict, List, Optional, Union

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
    :param env_delimiter: delimiter for the environment variable names
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
                children: Union[List[Any], Dict[str, Any]] = []

                if field.sub_fields:
                    if env_name in environ:
                        children = [v.strip() for v in environ[env_name].split(",")]

                else:
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

    def _load_file_values(
        self,
        filepath: Optional[Path] = None,
    ) -> Dict[str, Any]:
        if filepath is None:
            logger.debug("no config filepath is provided")
            return {}

        if not filepath.is_file():
            logger.warning(f"provided config filepath '{filepath}' is not a file")
            return {}

        try:
            return safe_load(filepath.read_text(encoding="utf-8"))
        except YAMLError as error:
            logger.error(f"config file '{filepath}' is not a valid yaml file: {error}")

        return {}
