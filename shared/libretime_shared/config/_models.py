from typing import TYPE_CHECKING, Any

# pylint: disable=no-name-in-module
from pydantic import AnyHttpUrl, BaseModel, validator

if TYPE_CHECKING:
    from pydantic.typing import AnyClassMethod


def no_trailing_slash_validator(key: str) -> "AnyClassMethod":
    # pylint: disable=unused-argument
    def strip_trailing_slash(cls: Any, value: Any) -> Any:
        if isinstance(value, str):
            return value.rstrip("/")
        return value

    return validator(key, pre=True, allow_reuse=True)(strip_trailing_slash)


# pylint: disable=too-few-public-methods
class GeneralConfig(BaseModel):
    public_url: AnyHttpUrl
    api_key: str

    # Validators
    _public_url_no_trailing_slash = no_trailing_slash_validator("public_url")


# pylint: disable=too-few-public-methods
class StorageConfig(BaseModel):
    path: str = "/srv/libretime"

    # Validators
    _path_no_trailing_slash = no_trailing_slash_validator("path")


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
