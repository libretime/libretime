from enum import Enum


class EventKind(str, Enum):
    FILE = "file"
    ACTION = "event"
    WEB_STREAM_BUFFER_START = "stream_buffer_start"
    WEB_STREAM_OUTPUT_START = "stream_output_start"
    WEB_STREAM_BUFFER_END = "stream_buffer_end"
    WEB_STREAM_OUTPUT_END = "stream_output_end"
