# pylint: disable=protected-access
from os import environ
from typing import List, Union
from unittest import mock

import pytest
from pydantic import BaseModel

from libretime_shared.config import BaseConfig
from libretime_shared.config._env import EnvLoader

ENV_SCHEMA_OBJ_WITH_STR = {
    "type": "object",
    "properties": {"a_str": {"type": "string"}},
}


@pytest.mark.parametrize(
    "env_parent, env, schema, expected",
    [
        (
            "PRE",
            {"PRE_A_STR": "found"},
            {"a_str": {"type": "string"}},
            {"a_str": "found"},
        ),
        (
            "PRE",
            {"PRE_OBJ_A_STR": "found"},
            {"obj": ENV_SCHEMA_OBJ_WITH_STR},
            {"obj": {"a_str": "found"}},
        ),
        (
            "PRE",
            {"PRE_ARR1": "one, two"},
            {"arr1": {"type": "array", "items": {"type": "string"}}},
            {"arr1": ["one", "two"]},
        ),
        (
            "PRE",
            {
                "PRE_ARR2_0_A_STR": "one",
                "PRE_ARR2_1_A_STR": "two",
                "PRE_ARR2_3_A_STR": "ten",
            },
            {"arr2": {"type": "array", "items": ENV_SCHEMA_OBJ_WITH_STR}},
            {
                "arr2": [
                    {"a_str": "one"},
                    {"a_str": "two"},
                    None,
                    {"a_str": "ten"},
                ]
            },
        ),
    ],
)
def test_env_config_loader_get_object(
    env_parent,
    env,
    schema,
    expected,
):
    with mock.patch.dict(environ, env):
        loader = EnvLoader(schema={}, env_prefix="PRE")
        result = loader._get_object(env_parent, {"properties": schema})
        assert result == expected


class FirstChildConfig(BaseModel):
    a_child_str: str


class SecondChildConfig(BaseModel):
    a_child_str: str
    a_child_int: int


# pylint: disable=too-few-public-methods
class FixtureConfig(BaseConfig):
    a_str: str
    a_list_of_str: List[str]
    a_obj: FirstChildConfig
    a_obj_with_default: FirstChildConfig = FirstChildConfig(a_child_str="default")
    a_list_of_obj: List[FirstChildConfig]
    a_union_str_or_int: Union[str, int]
    a_union_obj: Union[FirstChildConfig, SecondChildConfig]
    a_list_of_union_str_or_int: List[Union[str, int]]
    a_list_of_union_obj: List[Union[FirstChildConfig, SecondChildConfig]]


ENV_SCHEMA = FixtureConfig.model_json_schema()


@pytest.mark.parametrize(
    "env_name, env, schema, expected",
    [
        (
            "PRE_A_STR",
            {"PRE_A_STR": "found"},
            ENV_SCHEMA["properties"]["a_str"],
            "found",
        ),
        (
            "PRE_A_LIST_OF_STR",
            {"PRE_A_LIST_OF_STR": "one, two"},
            ENV_SCHEMA["properties"]["a_list_of_str"],
            ["one", "two"],
        ),
        (
            "PRE_A_OBJ",
            {"PRE_A_OBJ_A_CHILD_STR": "found"},
            ENV_SCHEMA["properties"]["a_obj"],
            {"a_child_str": "found"},
        ),
    ],
)
def test_env_config_loader_get(
    env_name,
    env,
    schema,
    expected,
):
    with mock.patch.dict(environ, env):
        loader = EnvLoader(schema=ENV_SCHEMA, env_prefix="PRE")
        result = loader._get(env_name, schema)
        assert result == expected


def test_env_config_loader_load_empty():
    with mock.patch.dict(environ, {}):
        loader = EnvLoader(schema=ENV_SCHEMA, env_prefix="PRE")
        result = loader.load()
        assert not result


def test_env_config_loader_load():
    with mock.patch.dict(
        environ,
        {
            "PRE_A_STR": "found",
            "PRE_A_LIST_OF_STR": "one, two",
            "PRE_A_OBJ": "invalid",
            "PRE_A_OBJ_A_CHILD_STR": "found",
            "PRE_A_OBJ_WITH_DEFAULT_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_OBJ": "invalid",
            "PRE_A_LIST_OF_OBJ_0_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_OBJ_1_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_OBJ_3_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_OBJ_INVALID": "invalid",
            "PRE_A_UNION_STR_OR_INT": "found",
            "PRE_A_UNION_OBJ_A_CHILD_STR": "found",
            "PRE_A_UNION_OBJ_A_CHILD_INT": "found",
            "PRE_A_LIST_OF_UNION_STR_OR_INT": "one, two, 3",
            "PRE_A_LIST_OF_UNION_STR_OR_INT_3": "4",
            "PRE_A_LIST_OF_UNION_OBJ": "invalid",
            "PRE_A_LIST_OF_UNION_OBJ_0_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_UNION_OBJ_1_A_CHILD_STR": "found",
            "PRE_A_LIST_OF_UNION_OBJ_1_A_CHILD_INT": "found",
            "PRE_A_LIST_OF_UNION_OBJ_3_A_CHILD_INT": "found",
            "PRE_A_LIST_OF_UNION_OBJ_INVALID": "invalid",
        },
    ):
        loader = EnvLoader(schema=ENV_SCHEMA, env_prefix="PRE")
        result = loader.load()
        assert result == {
            "a_str": "found",
            "a_list_of_str": ["one", "two"],
            "a_obj": {"a_child_str": "found"},
            "a_obj_with_default": {"a_child_str": "found"},
            "a_list_of_obj": [
                {"a_child_str": "found"},
                {"a_child_str": "found"},
                None,
                {"a_child_str": "found"},
            ],
            "a_union_str_or_int": "found",
            "a_union_obj": {
                "a_child_str": "found",
                "a_child_int": "found",
            },
            "a_list_of_union_str_or_int": ["one", "two", "3", "4"],
            "a_list_of_union_obj": [
                {"a_child_str": "found"},
                {"a_child_str": "found", "a_child_int": "found"},
                None,
                {"a_child_int": "found"},
            ],
        }
