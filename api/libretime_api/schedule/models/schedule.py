from django.db import models


class Schedule(models.Model):
    starts = models.DateTimeField()
    ends = models.DateTimeField()
    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    stream = models.ForeignKey(
        "Webstream",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    clip_length = models.DurationField(blank=True, null=True)
    fade_in = models.TimeField(blank=True, null=True)
    fade_out = models.TimeField(blank=True, null=True)
    cue_in = models.DurationField()
    cue_out = models.DurationField()
    media_item_played = models.BooleanField(blank=True, null=True)
    instance = models.ForeignKey("ShowInstance", on_delete=models.DO_NOTHING)
    playout_status = models.SmallIntegerField()
    broadcasted = models.SmallIntegerField()
    position = models.IntegerField()

    def get_owner(self):
        return self.instance.get_owner()

    def get_cueout(self):
        """
        Returns a scheduled item cueout that is based on the current show instance.

        Cueout of a specific item can potentially overrun the show that it is
        scheduled in. In that case, the cueout should be the end of the show.
        This prevents the next show having overlapping items playing.

        Cases:
        - When the schedule ends before the end of the show instance,
        return the stored cueout.

        - When the schedule starts before the end of the show instance
        and ends after the show instance ends,
        return timedelta between schedule starts and show instance ends.

        - When the schedule starts after the end of the show instance,
        return the stored cue_out even if the schedule WILL NOT BE PLAYED.
        """
        if self.starts < self.instance.ends and self.instance.ends < self.ends:
            return self.instance.ends - self.starts
        return self.cue_out

    def get_ends(self):
        """
        Returns a scheduled item ends that is based on the current show instance.

        End of a specific item can potentially overrun the show that it is
        scheduled in. In that case, the end should be the end of the show.
        This prevents the next show having overlapping items playing.

        Cases:
        - When the schedule ends before the end of the show instance,
        return the scheduled item ends.

        - When the schedule starts before the end of the show instance
        and ends after the show instance ends,
        return the show instance ends.

        - When the schedule starts after the end of the show instance,
        return the show instance ends.
        """
        if self.instance.ends < self.ends:
            return self.instance.ends
        return self.ends

    @property
    def is_valid(self):
        """
        A schedule item is valid if it starts before the end of the show instance
        it is in
        """
        return self.starts < self.instance.ends

    class Meta:
        managed = False
        db_table = "cc_schedule"
        permissions = [
            ("change_own_schedule", "Change the content on their shows"),
            ("delete_own_schedule", "Delete the content on their shows"),
        ]
