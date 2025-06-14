from datetime import datetime
from enum import Enum
from pathlib import Path
from typing import Dict, Literal, Optional, Union

from dateutil.parser import isoparse
from pydantic import BaseModel, BeforeValidator, Field, parse_obj_as
from typing_extensions import Annotated

from ..config import CACHE_DIR
from ..utils import mime_guess_extension

EVENT_KEY_FORMAT = "%Y-%m-%d-%H-%M-%S"


def event_key_to_datetime(value: Union[str, datetime]) -> datetime:
    if isinstance(value, str):
        value = datetime.strptime(value, EVENT_KEY_FORMAT)
    return value


def datetime_to_event_key(value: Union[str, datetime]) -> str:
    if isinstance(value, datetime):
        value = value.strftime(EVENT_KEY_FORMAT)
    return value


def event_isoparse(value: str) -> datetime:
    return isoparse(value).replace(tzinfo=None).replace(microsecond=0)


class EventKind(str, Enum):
    FILE = "file"
    ACTION = "event"
    WEB_STREAM_BUFFER_START = "stream_buffer_start"
    WEB_STREAM_OUTPUT_START = "stream_output_start"
    WEB_STREAM_BUFFER_END = "stream_buffer_end"
    WEB_STREAM_OUTPUT_END = "stream_output_end"


EventKeyDatetime = Annotated[datetime, BeforeValidator(event_key_to_datetime)]


class BaseEvent(BaseModel):
    start: EventKeyDatetime
    end: EventKeyDatetime

    @property
    def start_key(self) -> str:
        return datetime_to_event_key(self.start)

    @property
    def end_key(self) -> str:
        return datetime_to_event_key(self.end)

    def ended(self) -> bool:
        return datetime.utcnow() > self.end


class FileEvent(BaseEvent):
    type: Literal[EventKind.FILE]

    # Schedule
    row_id: int
    uri: Optional[str] = None
    id: int

    # Show data
    show_name: str

    # File
    fade_in: float
    fade_out: float
    cue_in: float
    cue_out: float

    track_title: Optional[str] = None
    artist_name: Optional[str] = None

    mime: str
    replay_gain: Optional[float] = None
    filesize: int

    file_ready: bool = False

    @property
    def file_ext(self) -> str:
        return mime_guess_extension(self.mime)

    @property
    def local_filepath(self) -> Path:
        return CACHE_DIR / f"{self.id}{self.file_ext}"


class WebStreamEvent(BaseEvent):
    type: Literal[
        EventKind.WEB_STREAM_BUFFER_START,
        EventKind.WEB_STREAM_OUTPUT_START,
        EventKind.WEB_STREAM_BUFFER_END,
        EventKind.WEB_STREAM_OUTPUT_END,
    ]

    # Schedule
    row_id: int
    uri: str
    id: int

    # Show data
    show_name: str


class ActionEventKind(str, Enum):
    SWITCH_OFF = "switch_off"
    KICK_OUT = "kick_out"


class ActionEvent(BaseEvent):
    type: Literal[EventKind.ACTION]
    # TODO: user ActionEventKind enum
    event_type: str


AnyEvent = Annotated[
    Union[FileEvent, WebStreamEvent, ActionEvent],
    Field(discriminator="type"),
]


def parse_any_event(value: dict) -> AnyEvent:
    return parse_obj_as(AnyEvent, value)  # type: ignore


FileEvents = Dict[str, FileEvent]
Events = Dict[str, AnyEvent]
