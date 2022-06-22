from django.db import models


class Podcast(models.Model):
    url = models.CharField(max_length=4096)
    title = models.CharField(max_length=4096)
    creator = models.CharField(max_length=4096, blank=True, null=True)
    description = models.CharField(max_length=4096, blank=True, null=True)
    language = models.CharField(max_length=4096, blank=True, null=True)
    copyright = models.CharField(max_length=4096, blank=True, null=True)
    link = models.CharField(max_length=4096, blank=True, null=True)

    itunes_author = models.CharField(max_length=4096, blank=True, null=True)
    itunes_keywords = models.CharField(max_length=4096, blank=True, null=True)
    itunes_summary = models.CharField(max_length=4096, blank=True, null=True)
    itunes_subtitle = models.CharField(max_length=4096, blank=True, null=True)
    itunes_category = models.CharField(max_length=4096, blank=True, null=True)
    itunes_explicit = models.CharField(max_length=4096, blank=True, null=True)

    owner = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    def get_owner(self):
        return self.owner

    class Meta:
        managed = False
        db_table = "podcast"
        permissions = [
            ("change_own_podcast", "Change the podcasts where they are the owner"),
            ("delete_own_podcast", "Delete the podcasts where they are the owner"),
        ]


class PodcastEpisode(models.Model):
    podcast = models.ForeignKey("Podcast", on_delete=models.DO_NOTHING)

    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    published_at = models.DateTimeField(db_column="publication_date")
    download_url = models.CharField(max_length=4096)
    episode_guid = models.CharField(max_length=4096)
    episode_title = models.CharField(max_length=4096)
    episode_description = models.TextField()

    def get_owner(self):
        return self.podcast.owner

    class Meta:
        managed = False
        db_table = "podcast_episodes"
        permissions = [
            (
                "change_own_podcastepisode",
                "Change the episodes of podcasts where they are the owner",
            ),
            (
                "delete_own_podcastepisode",
                "Delete the episodes of podcasts where they are the owner",
            ),
        ]


class StationPodcast(models.Model):
    podcast = models.ForeignKey("Podcast", on_delete=models.DO_NOTHING)

    def get_owner(self):
        return self.podcast.owner

    class Meta:
        managed = False
        db_table = "station_podcast"


class ImportedPodcast(models.Model):
    podcast = models.ForeignKey("Podcast", on_delete=models.DO_NOTHING)
    override_album = models.BooleanField(db_column="album_override")

    auto_ingest = models.BooleanField()
    auto_ingested_at = models.DateTimeField(
        blank=True,
        null=True,
        db_column="auto_ingest_timestamp",
    )

    def get_owner(self):
        return self.podcast.owner

    class Meta:
        managed = False
        db_table = "imported_podcast"
