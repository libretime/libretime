from django.db import models


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
    value = models.CharField(
        max_length=255,
        blank=True,
        null=True,
    )
    type = models.CharField(
        max_length=16,
    )

    class Meta:
        managed = False
        db_table = "cc_stream_setting"
