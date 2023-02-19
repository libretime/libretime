from enum import Enum
from typing import Dict, Literal, TypedDict, Union

from typing_extensions import NotRequired


class EventKind(str, Enum):
    FILE = "file"
    ACTION = "event"
    WEB_STREAM_BUFFER_START = "stream_buffer_start"
    WEB_STREAM_OUTPUT_START = "stream_output_start"
    WEB_STREAM_BUFFER_END = "stream_buffer_end"
    WEB_STREAM_OUTPUT_END = "stream_output_end"


class BaseEvent(TypedDict):
    # TODO: Convert start/end to datetime
    start: str
    end: str


class FileEventMetadata(TypedDict):
    track_title: str
    artist_name: str
    mime: str


class FileEvent(BaseEvent):
    type: Literal[EventKind.FILE]

    # Schedule
    row_id: int
    uri: str
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
Events = Dict[str, AnyEvent]
