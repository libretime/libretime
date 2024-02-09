from enum import Enum

from pydantic import BaseModel


class Info(BaseModel):
    station_name: str


class MessageFormatKind(int, Enum):
    ARTIST_TITLE = 0
    SHOW_ARTIST_TITLE = 1
    RADIO_SHOW = 2


class StreamPreferences(BaseModel):
    input_fade_transition: float
    message_format: MessageFormatKind
    message_offline: str
    replay_gain_enabled: bool
    replay_gain_offset: float
    master_me_preset: int
    master_me_lufs: int


class StreamState(BaseModel):
    input_main_connected: bool
    input_main_streaming: bool
    input_show_connected: bool
    input_show_streaming: bool
    schedule_streaming: bool
