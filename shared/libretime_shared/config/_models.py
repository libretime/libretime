import sys
from enum import Enum
from typing import TYPE_CHECKING, Any, List, Optional, Sequence, Union

# pylint: disable=no-name-in-module
from pydantic import AnyHttpUrl, AnyUrl, BaseModel, Field, validator
from typing_extensions import Annotated, Literal

if sys.version_info < (3, 9):
    from backports.zoneinfo import ZoneInfo, ZoneInfoNotFoundError
else:
    from zoneinfo import ZoneInfo, ZoneInfoNotFoundError


if TYPE_CHECKING:
    from pydantic.typing import AnyClassMethod


def no_trailing_slash_validator(key: str) -> "AnyClassMethod":
    # pylint: disable=unused-argument
    def strip_trailing_slash(cls: Any, value: Any) -> Any:
        if isinstance(value, str):
            return value.rstrip("/")
        return value

    return validator(key, pre=True, allow_reuse=True)(strip_trailing_slash)


def no_leading_slash_validator(key: str) -> "AnyClassMethod":
    # pylint: disable=unused-argument
    def strip_leading_slash(cls: Any, value: Any) -> Any:
        if isinstance(value, str):
            return value.lstrip("/")
        return value

    return validator(key, pre=True, allow_reuse=True)(strip_leading_slash)


# GeneralConfig
########################################################################################


# pylint: disable=too-few-public-methods
class GeneralConfig(BaseModel):
    public_url: AnyHttpUrl
    api_key: str

    timezone: str = "UTC"

    # Validators
    _public_url_no_trailing_slash = no_trailing_slash_validator("public_url")

    @validator("timezone")
    @classmethod
    def _validate_timezone(cls, value: str) -> str:
        try:
            ZoneInfo(value)
        except ZoneInfoNotFoundError as exception:
            raise ValueError(f"invalid timezone '{value}'") from exception

        return value


# StorageConfig
########################################################################################

# pylint: disable=too-few-public-methods
class StorageConfig(BaseModel):
    path: str = "/srv/libretime"

    # Validators
    _path_no_trailing_slash = no_trailing_slash_validator("path")


# DatabaseConfig
########################################################################################

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


# RabbitMQConfig
########################################################################################

# pylint: disable=too-few-public-methods
class RabbitMQConfig(BaseModel):
    host: str = "localhost"
    port: int = 5672
    user: str = "libretime"
    password: str = "libretime"
    vhost: str = "/libretime"

    @property
    def url(self) -> str:
        return (
            f"amqp://{self.user}:{self.password}"
            f"@{self.host}:{self.port}/{self.vhost}"
        )


# StreamConfig
########################################################################################


class BaseInput(BaseModel):
    enabled: bool = True
    public_url: Optional[AnyUrl] = None


class InputKind(str, Enum):
    HARBOR = "harbor"


class HarborInput(BaseInput):
    kind: Literal[InputKind.HARBOR] = InputKind.HARBOR
    mount: str
    port: int

    _mount_no_leading_slash = no_leading_slash_validator("mount")


class MainHarborInput(HarborInput):
    mount: str = "main"
    port: int = 8001


class ShowHarborInput(HarborInput):
    mount: str = "show"
    port: int = 8002


class Inputs(BaseModel):
    main: HarborInput = MainHarborInput()
    show: HarborInput = ShowHarborInput()


class AudioChannels(str, Enum):
    STEREO = "stereo"
    MONO = "mono"


class BaseAudio(BaseModel):
    channels: AudioChannels = AudioChannels.STEREO
    bitrate: int

    @validator("bitrate")
    @classmethod
    def _validate_bitrate(cls, value: int) -> int:
        # Once the liquidsoap script generation supports it, fine tune
        # the bitrate validation for each format
        bitrates = (32, 48, 64, 96, 128, 160, 192, 224, 256, 320)
        if value not in bitrates:
            raise ValueError(f"invalid bitrate {value}, must be one of {bitrates}")
        return value


class AudioFormat(str, Enum):
    AAC = "aac"
    MP3 = "mp3"
    OGG = "ogg"
    OPUS = "opus"


class AudioAAC(BaseAudio):
    format: Literal[AudioFormat.AAC] = AudioFormat.AAC


class AudioMP3(BaseAudio):
    format: Literal[AudioFormat.MP3] = AudioFormat.MP3


class AudioOGG(BaseAudio):
    format: Literal[AudioFormat.OGG] = AudioFormat.OGG
    enable_metadata: Optional[bool] = False


class AudioOpus(BaseAudio):
    format: Literal[AudioFormat.OPUS] = AudioFormat.OPUS


class IcecastOutput(BaseModel):
    kind: Literal["icecast"] = "icecast"
    enabled: bool = False
    public_url: Optional[AnyUrl] = None

    host: str = "localhost"
    port: int = 8000
    mount: str
    source_user: str = "source"
    source_password: str
    admin_user: str = "admin"
    admin_password: Optional[str] = None

    audio: Annotated[
        Union[AudioAAC, AudioMP3, AudioOGG, AudioOpus],
        Field(discriminator="format"),
    ]

    name: Optional[str] = None
    description: Optional[str] = None
    website: Optional[str] = None
    genre: Optional[str] = None

    _mount_no_leading_slash = no_leading_slash_validator("mount")


class ShoutcastOutput(BaseModel):
    kind: Literal["shoutcast"] = "shoutcast"
    enabled: bool = False
    public_url: Optional[AnyUrl] = None

    host: str = "localhost"
    port: int = 8000
    source_user: str = "source"
    source_password: str
    admin_user: str = "admin"
    admin_password: Optional[str] = None

    audio: Annotated[
        Union[AudioAAC, AudioMP3],
        Field(discriminator="format"),
    ]

    name: Optional[str] = None
    website: Optional[str] = None
    genre: Optional[str] = None


class SystemOutputKind(str, Enum):
    ALSA = "alsa"
    AO = "ao"
    OSS = "oss"
    PORTAUDIO = "portaudio"
    PULSEAUDIO = "pulseaudio"


class SystemOutput(BaseModel):
    enabled: bool = False
    kind: SystemOutputKind = SystemOutputKind.ALSA


# pylint: disable=too-few-public-methods
class Outputs(BaseModel):
    icecast: List[IcecastOutput] = Field([], max_items=3)
    shoutcast: List[ShoutcastOutput] = Field([], max_items=1)
    system: List[SystemOutput] = Field([], max_items=1)

    @property
    def merged(self) -> Sequence[Union[IcecastOutput, ShoutcastOutput]]:
        return self.icecast + self.shoutcast  # type: ignore


# pylint: disable=too-few-public-methods
class StreamConfig(BaseModel):
    """Stream configuration model."""

    inputs: Inputs = Inputs()
    outputs: Outputs = Outputs()
