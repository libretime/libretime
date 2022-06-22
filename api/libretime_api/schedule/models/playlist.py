from django.db import models


class Playlist(models.Model):
    name = models.CharField(max_length=255)
    mtime = models.DateTimeField(blank=True, null=True)
    utime = models.DateTimeField(blank=True, null=True)
    creator = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    description = models.CharField(max_length=512, blank=True, null=True)
    length = models.DurationField(blank=True, null=True)

    def get_owner(self):
        return self.creator

    class Meta:
        managed = False
        db_table = "cc_playlist"


class PlaylistContent(models.Model):
    playlist = models.ForeignKey(
        "Playlist",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    block = models.ForeignKey(
        "SmartBlock",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    stream_id = models.IntegerField(blank=True, null=True)
    type = models.SmallIntegerField()
    position = models.IntegerField(blank=True, null=True)
    trackoffset = models.FloatField()
    cliplength = models.DurationField(blank=True, null=True)
    cuein = models.DurationField(blank=True, null=True)
    cueout = models.DurationField(blank=True, null=True)
    fadein = models.TimeField(blank=True, null=True)
    fadeout = models.TimeField(blank=True, null=True)

    def get_owner(self):
        return self.playlist.get_owner()

    class Meta:
        managed = False
        db_table = "cc_playlistcontents"
