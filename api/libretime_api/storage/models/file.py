from django.db import models


class File(models.Model):
    library = models.ForeignKey(
        "storage.Library",
        models.DO_NOTHING,
        blank=True,
        null=True,
        db_column="track_type_id",
    )

    owner = models.ForeignKey(
        "core.User",
        models.DO_NOTHING,
        blank=True,
        null=True,
    )

    class ImportStatus(models.IntegerChoices):
        SUCCESS = 0, "Success"
        PENDING = 1, "Pending"
        FAILED = 2, "Failed"

    import_status = models.IntegerField(
        choices=ImportStatus.choices,
        default=ImportStatus.PENDING,
    )

    filepath = models.TextField(blank=True, null=True)
    size = models.IntegerField(db_column="filesize")
    exists = models.BooleanField(blank=True, null=True, db_column="file_exists")
    mime = models.CharField(max_length=255)
    md5 = models.CharField(max_length=32, blank=True, null=True)

    hidden = models.BooleanField(blank=True, null=True)
    accessed = models.IntegerField(db_column="currentlyaccessing")
    scheduled = models.BooleanField(blank=True, null=True, db_column="is_scheduled")
    part_of_list = models.BooleanField(blank=True, null=True, db_column="is_playlist")

    created_at = models.DateTimeField(blank=True, null=True, db_column="utime")
    updated_at = models.DateTimeField(blank=True, null=True, db_column="mtime")
    last_played_at = models.DateTimeField(blank=True, null=True, db_column="lptime")

    edited_by = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        related_name="edited_files",
        db_column="editedby",
    )

    # Audio
    bit_rate = models.IntegerField(blank=True, null=True)
    sample_rate = models.IntegerField(blank=True, null=True)
    format = models.CharField(max_length=128, blank=True, null=True)  # ?
    channels = models.IntegerField(blank=True, null=True)
    length = models.DurationField(blank=True, null=True)

    bpm = models.IntegerField(blank=True, null=True)  # ?
    replay_gain = models.DecimalField(
        max_digits=8,
        decimal_places=2,
        blank=True,
        null=True,
    )
    cue_in = models.DurationField(blank=True, null=True, db_column="cuein")
    cue_out = models.DurationField(blank=True, null=True, db_column="cueout")

    # Metadata
    name = models.CharField(max_length=255)  # ?
    description = models.CharField(max_length=512, blank=True, null=True)  # ?

    artwork = models.CharField(max_length=512, blank=True, null=True)

    artist_name = models.CharField(max_length=512, blank=True, null=True)
    artist_url = models.CharField(max_length=512, blank=True, null=True)  # ?
    original_artist = models.CharField(max_length=512, blank=True, null=True)  # ?
    album_title = models.CharField(max_length=512, blank=True, null=True)
    track_title = models.CharField(max_length=512, blank=True, null=True)
    genre = models.CharField(max_length=64, blank=True, null=True)
    mood = models.CharField(max_length=64, blank=True, null=True)
    date = models.CharField(max_length=16, blank=True, null=True, db_column="year")
    track_number = models.IntegerField(blank=True, null=True)
    disc_number = models.CharField(max_length=8, blank=True, null=True)  # ?
    comment = models.TextField(blank=True, null=True, db_column="comments")
    language = models.CharField(max_length=512, blank=True, null=True)
    label = models.CharField(max_length=512, blank=True, null=True)
    copyright = models.CharField(max_length=512, blank=True, null=True)
    composer = models.CharField(max_length=512, blank=True, null=True)
    conductor = models.CharField(max_length=512, blank=True, null=True)
    orchestra = models.CharField(max_length=512, blank=True, null=True)  # ?
    encoder = models.CharField(max_length=64, blank=True, null=True)
    encoded_by = models.CharField(max_length=255, blank=True, null=True)  # ?
    isrc = models.CharField(
        max_length=512,
        blank=True,
        null=True,
        db_column="isrc_number",
    )

    lyrics = models.TextField(blank=True, null=True)  # ?
    lyricist = models.CharField(max_length=512, blank=True, null=True)  # ?
    original_lyricist = models.CharField(max_length=512, blank=True, null=True)  # ?

    subject = models.CharField(max_length=512, blank=True, null=True)  # ?
    contributor = models.CharField(max_length=512, blank=True, null=True)  # ?
    rating = models.CharField(max_length=8, blank=True, null=True)  # ?
    url = models.CharField(max_length=1024, blank=True, null=True)  # ?
    info_url = models.CharField(max_length=512, blank=True, null=True)  # ?
    audio_source_url = models.CharField(max_length=512, blank=True, null=True)  # ?
    buy_this_url = models.CharField(max_length=512, blank=True, null=True)  # ?
    catalog_number = models.CharField(max_length=512, blank=True, null=True)  # ?

    radio_station_name = models.CharField(max_length=512, blank=True, null=True)  # ?
    radio_station_url = models.CharField(max_length=512, blank=True, null=True)  # ?

    report_datetime = models.CharField(max_length=32, blank=True, null=True)  # ?
    report_location = models.CharField(max_length=512, blank=True, null=True)  # ?
    report_organization = models.CharField(max_length=512, blank=True, null=True)  # ?

    def get_owner(self):
        return self.owner

    class Meta:
        managed = False
        db_table = "cc_files"
        permissions = [
            ("change_own_file", "Change the files where they are the owner"),
            ("delete_own_file", "Delete the files where they are the owner"),
        ]
