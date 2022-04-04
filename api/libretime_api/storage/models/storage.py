from django.db import models


class MusicDir(models.Model):
    directory = models.TextField(unique=True, blank=True, null=True)
    type = models.CharField(max_length=255, blank=True, null=True)
    exists = models.BooleanField(blank=True, null=True)
    watched = models.BooleanField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = "cc_music_dirs"
