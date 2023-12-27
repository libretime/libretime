from collections import ChainMap
from functools import reduce
from operator import getitem
from os import environ
from typing import Any, Dict, List, Optional, TypeVar

__all__ = [
    "EnvLoader",
]


def filter_env(env: Dict[str, str], prefix: str) -> Dict[str, str]:
    """
    Filter a environment variables dict by key prefix.

    Args:
        env: Environment variables dict.
        prefix: Environment variable key prefix.

    Returns:
        Environment variables dict.
    """
    return {k: v for k, v in env.items() if k.startswith(prefix)}


def guess_env_array_indexes(env: Dict[str, str], prefix: str) -> List[int]:
    """
    Guess environment variables indexes from the environment variables keys.

    Args:
        env: Environment variables dict.
        prefix: Environment variable key prefix for all indexes.

    Returns:
        A list of indexes.
    """
    prefix_len = len(prefix)

    result = []
    for env_name in filter_env(env, prefix):
        if not env_name[prefix_len].isdigit():
            continue

        index_str = env_name[prefix_len:]
        index_str = index_str.partition("_")[0]
        result.append(int(index_str))

    return result


T = TypeVar("T")


def index_dict_to_none_list(base: Dict[int, T]) -> List[Optional[T]]:
    """
    Convert a dict to a list by associating the dict keys to the list
    indexes and filling the missing indexes with None.

    Args:
        base: Dict to convert.

    Returns:
        Converted dict.
    """
    if not base:
        return []

    result: List[Optional[T]] = [None] * (max(base.keys()) + 1)

    for index, value in base.items():
        result[index] = value

    return result


# pylint: disable=too-few-public-methods
class EnvLoader:
    schema: dict

    env_prefix: str
    env_delimiter: str

    _env: Dict[str, str]

    def __init__(
        self,
        schema: dict,
        env_prefix: Optional[str] = None,
        env_delimiter: str = "_",
    ) -> None:
        self.schema = schema
        self.env_prefix = env_prefix or ""
        self.env_delimiter = env_delimiter

        self._env = environ.copy()
        if self.env_prefix:
            self._env = filter_env(self._env, self.env_prefix)

    def load(self) -> Dict[str, Any]:
        if not self._env:
            return {}

        return self._get(self.env_prefix, self.schema)

    def _resolve_ref(
        self,
        path: str,
    ) -> Dict[str, Any]:
        _, *parts = path.split("/")
        return reduce(getitem, parts, self.schema)

    def _get_mapping(
        self,
        env_name: str,
        *schemas: Dict[str, Any],
    ) -> Dict[str, Any]:
        """
        Get a mapping of each subtypes with the data.

        This helps resolve conflicts after we have all the data.

        Args:
            env_name: Environment variable name to get the data from.

        Returns:
            Mapping of each subtypes, with associated data as value.
        """
        mapping: Dict[str, Any] = {}

        for schema in schemas:
            if "$ref" in schema:
                schema = self._resolve_ref(schema["$ref"])

            value = self._get(env_name, schema)
            if not value:
                continue

            key = "title" if "title" in schema else "type"
            mapping[schema[key]] = value

        return mapping

    # pylint: disable=too-many-return-statements,too-many-branches
    def _get(
        self,
        env_name: str,
        schema: Dict[str, Any],
    ) -> Any:
        """
        Get a value from the environment.

        Args:
            env_name: Environment variable name.
            schema: Schema for the value we are retrieving.

        Returns:
            Value retrieved from the environment.
        """

        if "$ref" in schema:
            schema = self._resolve_ref(schema["$ref"])

        if "const" in schema:
            return self._env.get(env_name, None)

        if "type" in schema:
            if schema["type"] == "null":
                return None

            if schema["type"] in ("string", "integer", "boolean"):
                return self._env.get(env_name, None)

            if schema["type"] == "object":
                return self._get_object(env_name, schema)

            if schema["type"] == "array":
                return self._get_array(env_name, schema)

        # Get all the properties as we won't have typing conflicts
        if "allOf" in schema:
            all_of_mapping = self._get_mapping(env_name, *schema["allOf"])
            # Merging all subtypes data together
            return dict(ChainMap(*all_of_mapping.values()))

        # Get all the properties as we won't have typing conflicts
        if "oneOf" in schema:
            one_of_mapping = self._get_mapping(env_name, *schema["oneOf"])
            # Merging all subtypes data together
            return dict(ChainMap(*one_of_mapping.values()))

        # Get all the properties and resolve conflicts after
        if "anyOf" in schema:
            any_of_mapping = self._get_mapping(env_name, *schema["anyOf"])
            if any_of_mapping:
                any_of_values = list(any_of_mapping.values())

                # If all subtypes are primary types, return the first subtype data
                if all(isinstance(value, str) for value in any_of_values):
                    return any_of_values[0]

                # If all subtypes are dicts, merge the subtypes data in a single dict.
                # Do not worry if subtypes share a field name, as the value is from a
                # single environment variable and will have the same value.
                if all(isinstance(value, dict) for value in any_of_values):
                    return dict(ChainMap(*any_of_values))

            return None

        raise ValueError(f"{env_name}: unhandled schema {schema}")

    def _get_object(
        self,
        env_name: str,
        schema: Dict[str, Any],
    ) -> Dict[str, Any]:
        """
        Get an object from the environment.

        Args:
            env_name: Environment variable name.
            schema: Schema for the value we are retrieving.

        Returns:
            Value retrieved from the environment.
        """
        result: Dict[str, Any] = {}

        if env_name != "":
            env_name += self.env_delimiter

        for child_key, child_schema in schema["properties"].items():
            child_env_name = (env_name + child_key).upper()

            value = self._get(child_env_name, child_schema)
            if value:
                result[child_key] = value

        return result

    # pylint: disable=too-many-branches
    def _get_array(
        self,
        env_parent: str,
        schema: Dict[str, Any],
    ) -> Optional[List[Any]]:
        """
        Get an array from the environment.

        Args:
            env_name: Environment variable name.
            schema: Schema for the value we are retrieving.

        Returns:
            Value retrieved from the environment.
        """
        result: Dict[int, Any] = {}

        schema_items = schema["items"]
        if "$ref" in schema_items:
            schema_items = self._resolve_ref(schema_items["$ref"])

        # Found a environment variable without index suffix, try
        # to extract CSV formatted array
        if env_parent in self._env:
            values = self._get(env_parent, schema_items)
            if values:
                for index, value in enumerate(values.split(",")):
                    result[index] = value.strip()

        indexes = guess_env_array_indexes(self._env, env_parent + self.env_delimiter)
        if indexes:
            for index in indexes:
                env_name = env_parent + self.env_delimiter + str(index)
                value = self._get(env_name, schema_items)
                if value:
                    result[index] = value

        return index_dict_to_none_list(result)
