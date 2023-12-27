import sys
from enum import Enum
from typing import List, Literal, Optional, Union

from pydantic import BaseModel, Field, field_validator
from typing_extensions import Annotated

from ._fields import AnyHttpUrlStr, AnyUrlStr, StrNoLeadingSlash, StrNoTrailingSlash

if sys.version_info < (3, 9):
    from backports.zoneinfo import ZoneInfo, ZoneInfoNotFoundError
else:
    from zoneinfo import ZoneInfo, ZoneInfoNotFoundError


# GeneralConfig
########################################################################################


# pylint: disable=too-few-public-methods
class GeneralConfig(BaseModel):
    public_url: AnyHttpUrlStr
    api_key: str
    secret_key: str

    timezone: str = "UTC"

    allowed_cors_origins: List[AnyHttpUrlStr] = []

    @field_validator("timezone")
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
    path: StrNoTrailingSlash = "/srv/libretime"


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
    public_url: Optional[AnyUrlStr] = None


class InputKind(str, Enum):
    HARBOR = "harbor"


class HarborInput(BaseInput):
    kind: Literal[InputKind.HARBOR] = InputKind.HARBOR
    mount: StrNoLeadingSlash
    port: int
    secure: bool = False


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

    @field_validator("bitrate")
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
    public_url: Optional[AnyUrlStr] = None

    host: str = "localhost"
    port: int = 8000
    mount: StrNoLeadingSlash
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

    mobile: bool = False


class ShoutcastOutput(BaseModel):
    kind: Literal["shoutcast"] = "shoutcast"
    enabled: bool = False
    public_url: Optional[AnyUrlStr] = None

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

    mobile: bool = False


class SystemOutputKind(str, Enum):
    ALSA = "alsa"
    AO = "ao"
    OSS = "oss"
    PORTAUDIO = "portaudio"
    PULSEAUDIO = "pulseaudio"


class SystemOutput(BaseModel):
    enabled: bool = False
    kind: SystemOutputKind = SystemOutputKind.PULSEAUDIO


# pylint: disable=too-few-public-methods
class Outputs(BaseModel):
    icecast: List[IcecastOutput] = Field([], max_length=3)
    shoutcast: List[ShoutcastOutput] = Field([], max_length=1)
    system: List[SystemOutput] = Field([], max_length=1)

    @property
    def merged(self) -> List[Union[IcecastOutput, ShoutcastOutput]]:
        return self.icecast + self.shoutcast


# pylint: disable=too-few-public-methods
class StreamConfig(BaseModel):
    """Stream configuration model."""

    inputs: Inputs = Inputs()
    outputs: Outputs = Outputs()  # type: ignore[call-arg]
