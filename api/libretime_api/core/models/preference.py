from django.db import models


class Preference(models.Model):
    user = models.ForeignKey(
        "User",
        on_delete=models.CASCADE,
        db_column="subjid",
        blank=True,
        null=True,
    )
    key = models.CharField(
        db_column="keystr",
        max_length=255,
        unique=True,
        blank=True,
        null=True,
    )
    value = models.TextField(
        db_column="valstr",
        blank=True,
        null=True,
    )

    class Meta:
        managed = False
        db_table = "cc_pref"
        unique_together = (("user", "key"),)


class StreamSetting(models.Model):
    key = models.CharField(
        db_column="keyname",
        primary_key=True,
        max_length=64,
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
