from django.db import models


class LiveLog(models.Model):
    state = models.CharField(max_length=32)
    start_time = models.DateTimeField()
    end_time = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_live_log"
