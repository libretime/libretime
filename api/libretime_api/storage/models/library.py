from django.db import models


class Library(models.Model):
    name = models.CharField(
        max_length=255,
        blank=True,
        null=True,
        db_column="type_name",
    )
    code = models.CharField(max_length=16, unique=True)
    description = models.CharField(max_length=255, blank=True, null=True)
    enabled = models.BooleanField(
        blank=True,
        default=True,
        db_column="visibility",
    )

    analyze_cue_points = models.BooleanField(
        blank=True,
        default=True,
        db_column="analyze_cue_points",
    )
    
    id = models.AutoField(primary_key=True)

    class Meta:
        managed = False
        db_table = "cc_track_types"
