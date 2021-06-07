from django.db import models

from .files import File


class Schedule(models.Model):
    starts = models.DateTimeField()
    ends = models.DateTimeField()
    file = models.ForeignKey(File, models.DO_NOTHING, blank=True, null=True)
    stream = models.ForeignKey("Webstream", models.DO_NOTHING, blank=True, null=True)
    clip_length = models.DurationField(blank=True, null=True)
    fade_in = models.TimeField(blank=True, null=True)
    fade_out = models.TimeField(blank=True, null=True)
    cue_in = models.DurationField()
    cue_out = models.DurationField()
    media_item_played = models.BooleanField(blank=True, null=True)
    instance = models.ForeignKey("ShowInstance", models.DO_NOTHING)
    playout_status = models.SmallIntegerField()
    broadcasted = models.SmallIntegerField()
    position = models.IntegerField()

    def get_owner(self):
        return self.instance.get_owner()

    class Meta:
        managed = False
        db_table = "cc_schedule"
        permissions = [
            ("change_own_schedule", "Change the content on their shows"),
            ("delete_own_schedule", "Delete the content on their shows"),
        ]
