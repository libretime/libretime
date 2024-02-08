from enum import Enum

from django.db import models
from pydantic import BaseModel


class SitePreferences(BaseModel):
    station_name: str


class MessageFormatKind(int, Enum):
    ARTIST_TITLE = 0
    SHOW_ARTIST_TITLE = 1
    RADIO_SHOW = 2


class StreamPreferences(BaseModel):
    input_fade_transition: float
    message_format: MessageFormatKind
    message_offline: str
    # input_auto_switch_off: bool
    # input_auto_switch_on: bool
    # input_main_user: str
    # input_main_password: str
    # replay_gain_enabled: bool
    # replay_gain_offset: float
    # track_fade_in: float
    # track_fade_out: float
    # track_fade_transition: float


class StreamState(BaseModel):
    input_main_connected: bool
    input_main_streaming: bool
    input_show_connected: bool
    input_show_streaming: bool
    schedule_streaming: bool


class SitePreferenceManager(models.Manager):
    def get_queryset(self):
        return super().get_queryset().filter(user__isnull=True)


class Preference(models.Model):
    user = models.ForeignKey(
        "core.User",
        on_delete=models.CASCADE,
        blank=True,
        null=True,
        db_column="subjid",
    )
    key = models.CharField(
        max_length=255,
        unique=True,
        blank=True,
        null=True,
        db_column="keystr",
    )
    value = models.TextField(
        blank=True,
        null=True,
        db_column="valstr",
    )

    objects = models.Manager()
    site = SitePreferenceManager()

    @classmethod
    def get_site_preferences(cls) -> SitePreferences:
        entries = dict(cls.site.values_list("key", "value"))
        return SitePreferences(
            station_name=entries.get("station_name", "LibreTime"),
        )

    @classmethod
    def get_stream_preferences(cls) -> StreamPreferences:
        entries = dict(cls.site.values_list("key", "value"))
        return StreamPreferences(
            input_fade_transition=float(entries.get("default_transition_fade", 0.0)),
            message_format=MessageFormatKind(
                int(entries.get("stream_label_format", 0))
            ),
            message_offline=entries.get("off_air_meta", "Offline"),
        )

    @classmethod
    def get_stream_state(cls) -> StreamState:
        entries = dict(cls.site.values_list("key", "value"))
        return StreamState(
            input_main_connected=entries.get("master_dj") == "true",
            input_main_streaming=entries.get("master_dj_switch") == "on",
            input_show_connected=entries.get("live_dj") == "true",
            input_show_streaming=entries.get("live_dj_switch") == "on",
            schedule_streaming=entries.get("scheduled_play_switch") == "on",
        )

    class Meta:
        managed = False
        db_table = "cc_pref"
        unique_together = (("user", "key"),)
