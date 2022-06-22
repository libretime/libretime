from django.db import models


class SmartBlock(models.Model):
    name = models.CharField(max_length=255)
    mtime = models.DateTimeField(blank=True, null=True)
    utime = models.DateTimeField(blank=True, null=True)
    creator = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    description = models.CharField(max_length=512, blank=True, null=True)
    length = models.DurationField(blank=True, null=True)
    type = models.CharField(max_length=7, blank=True, null=True)

    def get_owner(self):
        return self.creator

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
        "SmartBlock",
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
    trackoffset = models.FloatField()
    cliplength = models.DurationField(blank=True, null=True)
    cuein = models.DurationField(blank=True, null=True)
    cueout = models.DurationField(blank=True, null=True)
    fadein = models.TimeField(blank=True, null=True)
    fadeout = models.TimeField(blank=True, null=True)

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
    criteria = models.CharField(max_length=32)
    modifier = models.CharField(max_length=16)
    value = models.CharField(max_length=512)
    extra = models.CharField(max_length=512, blank=True, null=True)
    criteriagroup = models.IntegerField(blank=True, null=True)
    block = models.ForeignKey("SmartBlock", on_delete=models.DO_NOTHING)

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
