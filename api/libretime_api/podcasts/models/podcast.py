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
        "core.User", models.DO_NOTHING, db_column="owner", blank=True, null=True
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
    file = models.ForeignKey("storage.File", models.DO_NOTHING, blank=True, null=True)
    podcast = models.ForeignKey("Podcast", models.DO_NOTHING)
    publication_date = models.DateTimeField()
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
    podcast = models.ForeignKey("Podcast", models.DO_NOTHING)

    def get_owner(self):
        return self.podcast.owner

    class Meta:
        managed = False
        db_table = "station_podcast"


class ImportedPodcast(models.Model):
    auto_ingest = models.BooleanField()
    auto_ingest_timestamp = models.DateTimeField(blank=True, null=True)
    album_override = models.BooleanField()
    podcast = models.ForeignKey("Podcast", models.DO_NOTHING)

    def get_owner(self):
        return self.podcast.owner

    class Meta:
        managed = False
        db_table = "imported_podcast"
