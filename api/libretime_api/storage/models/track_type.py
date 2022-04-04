from django.db import models


class TrackType(models.Model):
    code = models.CharField(max_length=16, unique=True)
    type_name = models.CharField(max_length=255, blank=True, null=True)
    description = models.CharField(max_length=255, blank=True, null=True)
    visibility = models.BooleanField(blank=True, default=True)

    class Meta:
        managed = False
        db_table = "cc_track_types"
