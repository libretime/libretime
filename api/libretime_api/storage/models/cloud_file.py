from django.db import models


class CloudFile(models.Model):
    storage_backend = models.CharField(max_length=512)
    resource_id = models.TextField()
    filename = models.ForeignKey(
        "File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        db_column="cc_file_id",
    )

    class Meta:
        managed = False
        db_table = "cloud_file"
