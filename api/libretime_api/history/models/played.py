from django.db import models


class PlayoutHistory(models.Model):
    file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    starts = models.DateTimeField()
    ends = models.DateTimeField(blank=True, null=True)
    instance = models.ForeignKey(
        "schedule.ShowInstance",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    class Meta:
        managed = False
        db_table = "cc_playout_history"


class PlayoutHistoryMetadata(models.Model):
    history = models.ForeignKey(
        "PlayoutHistory",
        on_delete=models.DO_NOTHING,
    )
    key = models.CharField(max_length=128)
    value = models.CharField(max_length=128)

    class Meta:
        managed = False
        db_table = "cc_playout_history_metadata"


class PlayoutHistoryTemplate(models.Model):
    name = models.CharField(max_length=128)
    type = models.CharField(max_length=35)

    class Meta:
        managed = False
        db_table = "cc_playout_history_template"


class PlayoutHistoryTemplateField(models.Model):
    template = models.ForeignKey("PlayoutHistoryTemplate", on_delete=models.DO_NOTHING)
    name = models.CharField(max_length=128)
    label = models.CharField(max_length=128)
    type = models.CharField(max_length=128)
    is_file_md = models.BooleanField()
    position = models.IntegerField()

    class Meta:
        managed = False
        db_table = "cc_playout_history_template_field"
