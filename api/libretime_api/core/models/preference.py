import logging
from typing import Optional, Union

from django.db import models

logger = logging.getLogger(__name__)


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

    class Meta:
        managed = False
        db_table = "cc_pref"
        unique_together = (("user", "key"),)


class StreamSetting(models.Model):
    key = models.CharField(
        primary_key=True,
        max_length=64,
        db_column="keyname",
    )
    raw_value = models.CharField(
        max_length=255,
        blank=True,
        null=True,
        db_column="value",
    )
    type = models.CharField(
        max_length=16,
    )

    @property
    def value(self) -> Optional[Union[bool, int, str]]:
        # Ignore if value is an empty string
        if not self.raw_value:
            return None

        if self.type == "boolean":
            return self.raw_value.lower() == "true"
        if self.type == "integer":
            return int(self.raw_value)
        if self.type == "string":
            return self.raw_value

        logger.warning(f"StreamSetting {self.key} has invalid type {self.type}")
        return self.raw_value

    class Meta:
        managed = False
        db_table = "cc_stream_setting"
