from django.db import models


class Playlist(models.Model):
    created_at = models.DateTimeField(blank=True, null=True, db_column="utime")
    updated_at = models.DateTimeField(blank=True, null=True, db_column="mtime")

    name = models.CharField(max_length=255)
    description = models.CharField(max_length=512, blank=True, null=True)
    length = models.DurationField(blank=True, null=True)

    owner = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        db_column="creator_id",
    )

    def get_owner(self):
        return self.owner

    class Meta:
        managed = False
        db_table = "cc_playlist"


class PlaylistContent(models.Model):
    playlist = models.ForeignKey(
        "schedule.Playlist",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    class Kind(models.IntegerChoices):
        FILE = 0, "File"
        STREAM = 1, "Stream"
        BLOCK = 2, "Block"

    kind = models.SmallIntegerField(
        choices=Kind.choices,
        db_column="type",
    )

    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    stream = models.ForeignKey(
        "schedule.Webstream",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    block = models.ForeignKey(
        "schedule.SmartBlock",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    position = models.IntegerField(blank=True, null=True)
    offset = models.FloatField(db_column="trackoffset")
    length = models.DurationField(blank=True, null=True, db_column="cliplength")
    cue_in = models.DurationField(blank=True, null=True, db_column="cuein")
    cue_out = models.DurationField(blank=True, null=True, db_column="cueout")
    fade_in = models.TimeField(blank=True, null=True, db_column="fadein")
    fade_out = models.TimeField(blank=True, null=True, db_column="fadeout")

    def get_owner(self):
        return self.playlist.get_owner()

    class Meta:
        managed = False
        db_table = "cc_playlistcontents"
