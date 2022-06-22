from django.db import models


class File(models.Model):
    name = models.CharField(max_length=255)
    mime = models.CharField(max_length=255)
    ftype = models.CharField(max_length=128)
    filepath = models.TextField(blank=True, null=True)
    import_status = models.IntegerField()
    currently_accessing = models.IntegerField(db_column="currentlyaccessing")
    edited_by = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        related_name="edited_files",
        db_column="editedby",
    )
    mtime = models.DateTimeField(blank=True, null=True)
    utime = models.DateTimeField(blank=True, null=True)
    lptime = models.DateTimeField(blank=True, null=True)
    md5 = models.CharField(max_length=32, blank=True, null=True)
    track_title = models.CharField(max_length=512, blank=True, null=True)
    artist_name = models.CharField(max_length=512, blank=True, null=True)
    bit_rate = models.IntegerField(blank=True, null=True)
    sample_rate = models.IntegerField(blank=True, null=True)
    format = models.CharField(max_length=128, blank=True, null=True)
    length = models.DurationField(blank=True, null=True)
    album_title = models.CharField(max_length=512, blank=True, null=True)
    genre = models.CharField(max_length=64, blank=True, null=True)
    comments = models.TextField(blank=True, null=True)
    year = models.CharField(max_length=16, blank=True, null=True)
    track_number = models.IntegerField(blank=True, null=True)
    channels = models.IntegerField(blank=True, null=True)
    url = models.CharField(max_length=1024, blank=True, null=True)
    bpm = models.IntegerField(blank=True, null=True)
    rating = models.CharField(max_length=8, blank=True, null=True)
    encoded_by = models.CharField(max_length=255, blank=True, null=True)
    disc_number = models.CharField(max_length=8, blank=True, null=True)
    mood = models.CharField(max_length=64, blank=True, null=True)
    label = models.CharField(max_length=512, blank=True, null=True)
    composer = models.CharField(max_length=512, blank=True, null=True)
    encoder = models.CharField(max_length=64, blank=True, null=True)
    checksum = models.CharField(max_length=256, blank=True, null=True)
    lyrics = models.TextField(blank=True, null=True)
    orchestra = models.CharField(max_length=512, blank=True, null=True)
    conductor = models.CharField(max_length=512, blank=True, null=True)
    lyricist = models.CharField(max_length=512, blank=True, null=True)
    original_lyricist = models.CharField(max_length=512, blank=True, null=True)
    radio_station_name = models.CharField(max_length=512, blank=True, null=True)
    info_url = models.CharField(max_length=512, blank=True, null=True)
    artist_url = models.CharField(max_length=512, blank=True, null=True)
    audio_source_url = models.CharField(max_length=512, blank=True, null=True)
    radio_station_url = models.CharField(max_length=512, blank=True, null=True)
    buy_this_url = models.CharField(max_length=512, blank=True, null=True)
    isrc_number = models.CharField(max_length=512, blank=True, null=True)
    catalog_number = models.CharField(max_length=512, blank=True, null=True)
    original_artist = models.CharField(max_length=512, blank=True, null=True)
    copyright = models.CharField(max_length=512, blank=True, null=True)
    report_datetime = models.CharField(max_length=32, blank=True, null=True)
    report_location = models.CharField(max_length=512, blank=True, null=True)
    report_organization = models.CharField(max_length=512, blank=True, null=True)
    subject = models.CharField(max_length=512, blank=True, null=True)
    contributor = models.CharField(max_length=512, blank=True, null=True)
    language = models.CharField(max_length=512, blank=True, null=True)
    file_exists = models.BooleanField(blank=True, null=True)
    replay_gain = models.DecimalField(
        max_digits=8, decimal_places=2, blank=True, null=True
    )
    owner = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    cuein = models.DurationField(blank=True, null=True)
    cueout = models.DurationField(blank=True, null=True)
    silan_check = models.BooleanField(blank=True, null=True)
    hidden = models.BooleanField(blank=True, null=True)
    is_scheduled = models.BooleanField(blank=True, null=True)
    is_playlist = models.BooleanField(blank=True, null=True)
    filesize = models.IntegerField()
    description = models.CharField(max_length=512, blank=True, null=True)
    artwork = models.CharField(max_length=512, blank=True, null=True)
    track_type = models.CharField(max_length=16, blank=True, null=True)

    def get_owner(self):
        return self.owner

    class Meta:
        managed = False
        db_table = "cc_files"
        permissions = [
            ("change_own_file", "Change the files where they are the owner"),
            ("delete_own_file", "Delete the files where they are the owner"),
        ]
