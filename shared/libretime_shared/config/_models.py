from enum import Enum
from typing import Annotated, Literal, Union
from zoneinfo import ZoneInfo, ZoneInfoNotFoundError

from pydantic import BaseModel, Field, field_validator

from ._fields import AnyHttpUrlStr, AnyUrlStr, StrNoLeadingSlash, StrNoTrailingSlash

# GeneralConfig
########################################################################################


# pylint: disable=too-few-public-methods
class GeneralConfig(BaseModel):
    public_url: AnyHttpUrlStr
    api_key: str
    secret_key: str

    timezone: str = "UTC"

    allowed_cors_origins: list[AnyHttpUrlStr] = []

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
    public_url: AnyUrlStr | None = None


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
    enable_metadata: bool | None = False


class AudioOpus(BaseAudio):
    format: Literal[AudioFormat.OPUS] = AudioFormat.OPUS


class IcecastOutput(BaseModel):
    kind: Literal["icecast"] = "icecast"
    enabled: bool = False
    public_url: AnyUrlStr | None = None

    host: str = "localhost"
    port: int = 8000
    mount: StrNoLeadingSlash
    source_user: str = "source"
    source_password: str
    admin_user: str = "admin"
    admin_password: str | None = None

    audio: Annotated[
        AudioAAC | AudioMP3 | AudioOGG | AudioOpus,
        Field(discriminator="format"),
    ]

    name: str | None = None
    description: str | None = None
    website: str | None = None
    genre: str | None = None

    mobile: bool = False


class ShoutcastOutput(BaseModel):
    kind: Literal["shoutcast"] = "shoutcast"
    enabled: bool = False
    public_url: AnyUrlStr | None = None

    host: str = "localhost"
    port: int = 8000
    source_user: str = "source"
    source_password: str
    admin_user: str = "admin"
    admin_password: str | None = None

    audio: Annotated[
        AudioAAC | AudioMP3,
        Field(discriminator="format"),
    ]

    name: str | None = None
    website: str | None = None
    genre: str | None = None

    mobile: bool = False


class SystemOutput(str, Enum):
    ALSA = "alsa"
    AO = "ao"
    OSS = "oss"
    PORTAUDIO = "portaudio"
    PULSEAUDIO = "pulseaudio"


class BaseSystemOutput(BaseModel):
    enabled: bool = False


class ALSASystemOutput(BaseSystemOutput):
    kind: Literal[SystemOutput.ALSA] = SystemOutput.ALSA
    device: str | None = None


class AOSystemOutput(BaseSystemOutput):
    kind: Literal[SystemOutput.AO] = SystemOutput.AO


class OSSSystemOutput(BaseSystemOutput):
    kind: Literal[SystemOutput.OSS] = SystemOutput.OSS


class PortAudioSystemOutput(BaseSystemOutput):
    kind: Literal[SystemOutput.PORTAUDIO] = SystemOutput.PORTAUDIO


class PulseAudioSystemOutput(BaseSystemOutput):
    kind: Literal[SystemOutput.PULSEAUDIO] = SystemOutput.PULSEAUDIO
    device: str | None = None


AnySystemOutput = Annotated[
    Union[
        ALSASystemOutput,
        AOSystemOutput,
        OSSSystemOutput,
        PortAudioSystemOutput,
        PulseAudioSystemOutput,
    ],
    Field(discriminator="kind"),
]


# pylint: disable=too-few-public-methods
class Outputs(BaseModel):
    icecast: list[IcecastOutput] = Field([], max_length=3)
    shoutcast: list[ShoutcastOutput] = Field([], max_length=1)
    system: list[AnySystemOutput] = Field([], max_length=1)

    @property
    def merged(self) -> list[IcecastOutput | ShoutcastOutput]:
        return self.icecast + self.shoutcast


# pylint: disable=too-few-public-methods
class StreamConfig(BaseModel):
    """Stream configuration model."""

    inputs: Inputs = Inputs()
    outputs: Outputs = Outputs()  # type: ignore[call-arg]
