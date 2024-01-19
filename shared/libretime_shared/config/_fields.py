from typing import Any, Optional

from pydantic import (
    AfterValidator,
    AnyHttpUrl,
    AnyUrl,
    GetCoreSchemaHandler,
    GetJsonSchemaHandler,
    TypeAdapter,
)
from pydantic.json_schema import JsonSchemaValue
from pydantic_core import Url
from pydantic_core.core_schema import CoreSchema, no_info_after_validator_function
from typing_extensions import Annotated

StrNoTrailingSlash = Annotated[str, AfterValidator(lambda x: str(x).rstrip("/"))]
StrNoLeadingSlash = Annotated[str, AfterValidator(lambda x: str(x).lstrip("/"))]


class AnyUrlStr(str):
    _type_adapter = TypeAdapter(AnyUrl)
    obj: Url

    @classmethod
    def __get_pydantic_core_schema__(
        cls,
        _: Any,
        handler: GetCoreSchemaHandler,
    ) -> CoreSchema:
        return no_info_after_validator_function(cls, handler(str))

    @classmethod
    def __get_pydantic_json_schema__(
        cls,
        core_schema: CoreSchema,
        handler: GetJsonSchemaHandler,
    ) -> JsonSchemaValue:
        field_schema = handler(core_schema)
        field_schema.update(format="uri")
        return field_schema

    def __new__(cls, value: str) -> "AnyUrlStr":
        url_obj = cls._type_adapter.validate_strings(value)
        self = str.__new__(cls, str(url_obj).rstrip("/"))
        self.obj = url_obj
        return self

    def __repr__(self) -> str:
        return f"{self.__class__.__name__}({super().__repr__()})"

    @property
    def scheme(self) -> str:
        return self.obj.scheme

    @property
    def host(self) -> Optional[str]:
        return self.obj.host

    @property
    def port(self) -> Optional[int]:
        return self.obj.port

    @property
    def path(self) -> Optional[str]:
        return self.obj.path


class AnyHttpUrlStr(AnyUrlStr):
    _type_adapter = TypeAdapter(AnyHttpUrl)
