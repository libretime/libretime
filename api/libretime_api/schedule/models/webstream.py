from django.contrib.auth import get_user_model
from django.db import models


class Webstream(models.Model):
    name = models.CharField(max_length=255)
    description = models.CharField(max_length=255)
    url = models.CharField(max_length=512)
    length = models.DurationField()
    creator_id = models.IntegerField()
    mtime = models.DateTimeField()
    utime = models.DateTimeField()
    lptime = models.DateTimeField(blank=True, null=True)
    mime = models.CharField(max_length=1024, blank=True, null=True)

    def get_owner(self):
        return get_user_model().objects.get(pk=self.creator_id)

    class Meta:
        managed = False
        db_table = "cc_webstream"
        permissions = [
            ("change_own_webstream", "Change the webstreams where they are the owner"),
            ("delete_own_webstream", "Delete the webstreams where they are the owner"),
        ]


class WebstreamMetadata(models.Model):
    instance = models.ForeignKey("Schedule", on_delete=models.DO_NOTHING)
    start_time = models.DateTimeField()
    liquidsoap_data = models.CharField(max_length=1024)

    def get_owner(self):
        return self.instance.get_owner()

    class Meta:
        managed = False
        db_table = "cc_webstream_metadata"
