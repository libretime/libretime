from django.db import models


class Preference(models.Model):
    subjid = models.ForeignKey(
        "User", models.DO_NOTHING, db_column="subjid", blank=True, null=True
    )
    keystr = models.CharField(unique=True, max_length=255, blank=True, null=True)
    valstr = models.TextField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_pref"
        unique_together = (("subjid", "keystr"),)


class MountName(models.Model):
    mount_name = models.CharField(max_length=1024)

    class Meta:
        managed = False
        db_table = "cc_mount_name"


class StreamSetting(models.Model):
    keyname = models.CharField(primary_key=True, max_length=64)
    value = models.CharField(max_length=255, blank=True, null=True)
    type = models.CharField(max_length=16)

    class Meta:
        managed = False
        db_table = "cc_stream_setting"
