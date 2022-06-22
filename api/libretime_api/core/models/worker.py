from django.db import models


class ThirdPartyTrackReference(models.Model):
    service = models.CharField(max_length=256)
    foreign_id = models.CharField(unique=True, max_length=256, blank=True, null=True)
    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    upload_time = models.DateTimeField(blank=True, null=True)
    status = models.CharField(max_length=256, blank=True, null=True)

    class Meta:
        managed = False
        db_table = "third_party_track_references"


class CeleryTask(models.Model):
    task_id = models.CharField(max_length=256)
    track_reference = models.ForeignKey(
        "ThirdPartyTrackReference",
        on_delete=models.DO_NOTHING,
    )
    name = models.CharField(max_length=256, blank=True, null=True)
    dispatch_time = models.DateTimeField(blank=True, null=True)
    status = models.CharField(max_length=256)

    class Meta:
        managed = False
        db_table = "celery_tasks"
