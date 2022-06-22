from django.db import models


class Show(models.Model):
    name = models.CharField(max_length=255)
    url = models.CharField(max_length=255, blank=True, null=True)
    genre = models.CharField(max_length=255, blank=True, null=True)
    description = models.CharField(max_length=8192, blank=True, null=True)
    color = models.CharField(max_length=6, blank=True, null=True)
    background_color = models.CharField(max_length=6, blank=True, null=True)
    live_stream_using_airtime_auth = models.BooleanField(blank=True, null=True)
    live_stream_using_custom_auth = models.BooleanField(blank=True, null=True)
    live_stream_user = models.CharField(max_length=255, blank=True, null=True)
    live_stream_pass = models.CharField(max_length=255, blank=True, null=True)
    linked = models.BooleanField()
    is_linkable = models.BooleanField()
    image_path = models.CharField(max_length=255, blank=True, null=True)
    has_autoplaylist = models.BooleanField()
    autoplaylist = models.ForeignKey(
        "Playlist",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )
    autoplaylist_repeat = models.BooleanField()

    def get_owner(self):
        return self.showhost_set.all()

    class Meta:
        managed = False
        db_table = "cc_show"


class ShowDays(models.Model):
    first_show = models.DateField()
    last_show = models.DateField(blank=True, null=True)
    start_time = models.TimeField()
    timezone = models.CharField(max_length=1024)
    duration = models.CharField(max_length=1024)
    day = models.SmallIntegerField(blank=True, null=True)
    repeat_type = models.SmallIntegerField()
    next_pop_date = models.DateField(blank=True, null=True)
    show = models.ForeignKey("Show", on_delete=models.DO_NOTHING)
    record = models.SmallIntegerField(blank=True, null=True)

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_days"


class ShowHost(models.Model):
    show = models.ForeignKey("Show", on_delete=models.DO_NOTHING)
    subjs = models.ForeignKey("core.User", on_delete=models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = "cc_show_hosts"


class ShowInstance(models.Model):
    description = models.CharField(max_length=8192, blank=True, null=True)
    starts = models.DateTimeField()
    ends = models.DateTimeField()
    show = models.ForeignKey("Show", on_delete=models.DO_NOTHING)
    record = models.SmallIntegerField(blank=True, null=True)
    rebroadcast = models.SmallIntegerField(blank=True, null=True)
    instance = models.ForeignKey(
        "self",
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
    time_filled = models.DurationField(blank=True, null=True)
    created = models.DateTimeField()
    last_scheduled = models.DateTimeField(blank=True, null=True)
    modified_instance = models.BooleanField()
    autoplaylist_built = models.BooleanField()

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_instances"


class ShowRebroadcast(models.Model):
    day_offset = models.CharField(max_length=1024)
    start_time = models.TimeField()
    show = models.ForeignKey("Show", on_delete=models.DO_NOTHING)

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_rebroadcast"
