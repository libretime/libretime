from django.db import models


class CeleryTask(models.Model):
    task_id = models.CharField(max_length=256)
    track_reference = models.ForeignKey('ThirdPartyTrackReference', models.DO_NOTHING, db_column='track_reference')
    name = models.CharField(max_length=256, blank=True, null=True)
    dispatch_time = models.DateTimeField(blank=True, null=True)
    status = models.CharField(max_length=256)

    class Meta:
        managed = False
        db_table = 'celery_tasks'
