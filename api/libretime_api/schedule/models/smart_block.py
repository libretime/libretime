from django.db import models


class SmartBlock(models.Model):
    created_at = models.DateTimeField(blank=True, null=True, db_column="utime")
    updated_at = models.DateTimeField(blank=True, null=True, db_column="mtime")

    name = models.CharField(max_length=255)
    description = models.CharField(max_length=512, blank=True, null=True)
    length = models.DurationField(blank=True, null=True)

    class Kind(models.TextChoices):
        STATIC = "static", "Static"
        DYNAMIC = "dynamic", "Dynamic"

    kind = models.CharField(
        choices=Kind.choices,
        default=Kind.DYNAMIC,
        max_length=7,
        blank=True,
        null=True,
        db_column="type",
    )

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
        db_table = "cc_block"
        permissions = [
            (
                "change_own_smartblock",
                "Change the smartblocks where they are the owner",
            ),
            (
                "delete_own_smartblock",
                "Delete the smartblocks where they are the owner",
            ),
        ]


class SmartBlockContent(models.Model):
    block = models.ForeignKey(
        "schedule.SmartBlock",
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

    position = models.IntegerField(blank=True, null=True)
    offset = models.FloatField(db_column="trackoffset")
    length = models.DurationField(blank=True, null=True, db_column="cliplength")
    cue_in = models.DurationField(blank=True, null=True, db_column="cuein")
    cue_out = models.DurationField(blank=True, null=True, db_column="cueout")
    fade_in = models.TimeField(blank=True, null=True, db_column="fadein")
    fade_out = models.TimeField(blank=True, null=True, db_column="fadeout")

    def get_owner(self):
        return self.block.get_owner()

    class Meta:
        managed = False
        db_table = "cc_blockcontents"
        permissions = [
            (
                "change_own_smartblockcontent",
                "Change the content of smartblocks where they are the owner",
            ),
            (
                "delete_own_smartblockcontent",
                "Delete the content of smartblocks where they are the owner",
            ),
        ]


class SmartBlockCriteria(models.Model):
    block = models.ForeignKey("schedule.SmartBlock", on_delete=models.DO_NOTHING)
    group = models.IntegerField(
        blank=True,
        null=True,
        db_column="criteriagroup",
    )

    criteria = models.CharField(max_length=32)
    condition = models.CharField(max_length=16, db_column="modifier")
    value = models.CharField(max_length=512)
    extra = models.CharField(max_length=512, blank=True, null=True)

    def get_owner(self):
        return self.block.get_owner()

    class Meta:
        managed = False
        db_table = "cc_blockcriteria"
        permissions = [
            (
                "change_own_smartblockcriteria",
                "Change the criteria of smartblocks where they are the owner",
            ),
            (
                "delete_own_smartblockcriteria",
                "Delete the criteria of smartblocks where they are the owner",
            ),
        ]
