import logging
import sys
from itertools import zip_longest
from pathlib import Path
from typing import Any, Dict, List, Optional, Union

from pydantic import BaseModel, ValidationError
from yaml import YAMLError, safe_load

from ._env import EnvLoader

logger = logging.getLogger(__name__)

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

    # pylint: disable=no-self-argument
    def __init__(
        _self,
        _filepath: Optional[Union[Path, str]] = None,
        *,
        _env_prefix: str = DEFAULT_ENV_PREFIX,
        _env_delimiter: str = "_",
        **kwargs: Any,
    ) -> None:
        if _filepath is not None:
            _filepath = Path(_filepath)

        env_loader = EnvLoader(_self.schema(), _env_prefix, _env_delimiter)

        values = deep_merge_dict(
            kwargs,
            _self._load_file_values(_filepath),
            env_loader.load(),
        )

        try:
            super().__init__(**values)
        except ValidationError as error:
            logger.critical(error)
            sys.exit(1)

    def _load_file_values(
        self,
        filepath: Optional[Path] = None,
    ) -> Dict[str, Any]:
        if filepath is None:
            logger.debug("no config filepath is provided")
            return {}

        if not filepath.is_file():
            logger.warning("provided config filepath '%s' is not a file", filepath)
            return {}

        try:
            return safe_load(filepath.read_text(encoding="utf-8"))
        except YAMLError as exception:
            logger.error(
                "config file '%s' is not a valid yaml file: %s", filepath, exception
            )

        return {}


def deep_merge_dict(base: Dict[str, Any], *elements: Dict[str, Any]) -> Dict[str, Any]:
    result = base.copy()

    for element in elements:
        for key, value in element.items():
            if key in result:
                if isinstance(result[key], dict) and isinstance(value, dict):
                    result[key] = deep_merge_dict(result[key], value)
                    continue

                if isinstance(result[key], list) and isinstance(value, list):
                    result[key] = deep_merge_list(result[key], value)
                    continue

            if value:
                result[key] = value

    return result


def deep_merge_list(base: List[Any], *elements: List[Any]) -> List[Any]:
    result: List[Any] = []
    for element in elements:
        for base_item, next_item in zip_longest(base, element):
            if isinstance(base_item, list) and isinstance(next_item, list):
                result.append(deep_merge_list(base_item, next_item))
                continue

            if isinstance(base_item, dict) and isinstance(next_item, dict):
                result.append(deep_merge_dict(base_item, next_item))
                continue

            if next_item:
                result.append(next_item)

    return result
