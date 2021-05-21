from django.db import models
from .files import File


class ThirdPartyTrackReference(models.Model):
    service = models.CharField(max_length=256)
    foreign_id = models.CharField(unique=True, max_length=256, blank=True, null=True)
    file = models.ForeignKey(File, models.DO_NOTHING, blank=True, null=True)
    upload_time = models.DateTimeField(blank=True, null=True)
    status = models.CharField(max_length=256, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'third_party_track_references'

class TrackType(models.Model):
    code = models.CharField(max_length=16, unique=True)
    type_name = models.CharField(max_length=255, blank=True, null=True)
    description = models.CharField(max_length=255, blank=True, null=True)
    visibility = models.BooleanField(blank=True, default=True)

    class Meta:
        managed = False
        db_table = 'cc_track_types'

