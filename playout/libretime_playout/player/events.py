from datetime import datetime
from enum import Enum
from typing import Dict, Literal, Optional, TypedDict, Union

from typing_extensions import NotRequired

EVENT_KEY_FORMAT = "%Y-%m-%d-%H-%M-%S"


def event_key_to_datetime(value: Union[str, datetime]) -> datetime:
    if isinstance(value, datetime):
        return value
    return datetime.strptime(value, EVENT_KEY_FORMAT)


def datetime_to_event_key(value: Union[str, datetime]) -> str:
    if isinstance(value, str):
        return value
    return value.strftime(EVENT_KEY_FORMAT)


class EventKind(str, Enum):
    FILE = "file"
    ACTION = "event"
    WEB_STREAM_BUFFER_START = "stream_buffer_start"
    WEB_STREAM_OUTPUT_START = "stream_output_start"
    WEB_STREAM_BUFFER_END = "stream_buffer_end"
    WEB_STREAM_OUTPUT_END = "stream_output_end"


class BaseEvent(TypedDict):
    # TODO: Only use datetime
    start: Union[str, datetime]
    end: Union[str, datetime]


class FileEventMetadata(TypedDict):
    track_title: str
    artist_name: str
    mime: str


class FileEvent(BaseEvent):
    type: Literal[EventKind.FILE]

    # Schedule
    row_id: int
    uri: Optional[str]
    id: int

    # Show data
    show_name: str

    # File
    fade_in: float
    fade_out: float
    cue_in: float
    cue_out: float

    # TODO: Flatten this metadata dict
    metadata: FileEventMetadata

    replay_gain: float
    filesize: int

    # Runtime
    dst: NotRequired[str]
    file_ready: NotRequired[bool]
    file_ext: NotRequired[str]


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
    show_name: NotRequired[str]


class ActionEventKind(str, Enum):
    SWITCH_OFF = "switch_off"
    KICK_OUT = "kick_out"


class ActionEvent(BaseEvent):
    type: Literal[EventKind.ACTION]
    # TODO: user ActionEventKind enum
    event_type: str


AnyEvent = Union[FileEvent, WebStreamEvent, ActionEvent]

FileEvents = Dict[str, FileEvent]
Events = Dict[str, AnyEvent]
