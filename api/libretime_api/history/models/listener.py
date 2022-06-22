from django.db import models


class MountName(models.Model):
    mount_name = models.CharField(max_length=1024)

    class Meta:
        managed = False
        db_table = "cc_mount_name"


class Timestamp(models.Model):
    timestamp = models.DateTimeField()

    class Meta:
        managed = False
        db_table = "cc_timestamp"


class ListenerCount(models.Model):
    timestamp = models.ForeignKey("Timestamp", on_delete=models.DO_NOTHING)
    mount_name = models.ForeignKey("MountName", on_delete=models.DO_NOTHING)
    listener_count = models.IntegerField()

    class Meta:
        managed = False
        db_table = "cc_listener_count"
