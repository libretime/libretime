from django.db import models


class Show(models.Model):
    name = models.CharField(max_length=255)
    description = models.CharField(max_length=8192, blank=True, null=True)
    genre = models.CharField(max_length=255, blank=True, null=True)
    url = models.CharField(max_length=255, blank=True, null=True)

    image = models.CharField(
        max_length=255,
        blank=True,
        null=True,
        db_column="image_path",
    )
    foreground_color = models.CharField(
        max_length=6,
        blank=True,
        null=True,
        db_column="color",
    )
    background_color = models.CharField(
        max_length=6,
        blank=True,
        null=True,
    )

    live_auth_registered = models.BooleanField(
        default=False,
        blank=True,
        null=True,
        db_column="live_stream_using_airtime_auth",
    )
    live_auth_custom = models.BooleanField(
        default=False,
        blank=True,
        null=True,
        db_column="live_stream_using_custom_auth",
    )
    live_auth_custom_user = models.CharField(
        max_length=255,
        blank=True,
        null=True,
        db_column="live_stream_user",
    )
    live_auth_custom_password = models.CharField(
        max_length=255,
        blank=True,
        null=True,
        db_column="live_stream_pass",
    )

    @property
    def live_enabled(self) -> bool:
        return any((self.live_auth_registered, self.live_auth_custom))

    # A show is linkable if it has never been linked before. Once
    # a show becomes unlinked it can not be linked again.
    linked = models.BooleanField()
    linkable = models.BooleanField(db_column="is_linkable")

    auto_playlist = models.ForeignKey(
        "schedule.Playlist",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        db_column="autoplaylist_id",
    )
    auto_playlist_enabled = models.BooleanField(db_column="has_autoplaylist")
    auto_playlist_repeat = models.BooleanField(db_column="autoplaylist_repeat")

    hosts = models.ManyToManyField(
        "core.User",
        through="ShowHost",
    )

    def get_owner(self):
        return self.hosts.all()

    class Meta:
        managed = False
        db_table = "cc_show"


class ShowHost(models.Model):
    show = models.ForeignKey(
        "schedule.Show",
        on_delete=models.DO_NOTHING,
    )
    user = models.ForeignKey(
        "core.User",
        on_delete=models.DO_NOTHING,
        db_column="subjs_id",
    )

    class Meta:
        managed = False
        db_table = "cc_show_hosts"


# TODO: Replace record choices with a boolean
class Record(models.IntegerChoices):
    NO = 0, "No"
    YES = 1, "Yes"


class ShowDays(models.Model):
    show = models.ForeignKey("schedule.Show", on_delete=models.DO_NOTHING)

    first_show_on = models.DateField(
        db_column="first_show",
    )
    last_show_on = models.DateField(
        blank=True,
        null=True,
        db_column="last_show",
    )
    start_time = models.TimeField()

    timezone = models.CharField(max_length=1024)
    duration = models.CharField(max_length=1024)

    record_enabled = models.SmallIntegerField(
        choices=Record.choices,
        default=Record.NO,
        blank=True,
        null=True,
        db_column="record",
    )

    class WeekDay(models.IntegerChoices):
        MONDAY = 0, "Monday"
        TUESDAY = 1, "Tuesday"
        WEDNESDAY = 2, "Wednesday"
        THURSDAY = 3, "Thursday"
        FRIDAY = 4, "Friday"
        SATURDAY = 5, "Saturday"
        SUNDAY = 6, "Sunday"

    week_day = models.SmallIntegerField(
        choices=WeekDay.choices,
        blank=True,
        null=True,
        db_column="day",
    )

    class RepeatKind(models.IntegerChoices):
        WEEKLY = 0, "Every week"
        WEEKLY_2 = 1, "Every 2 weeks"
        WEEKLY_3 = 4, "Every 3 weeks"
        WEEKLY_4 = 5, "Every 4 weeks"
        MONTHLY = 2, "Every month"

    repeat_kind = models.SmallIntegerField(
        choices=RepeatKind.choices,
        db_column="repeat_type",
    )
    repeat_next_on = models.DateField(
        blank=True,
        null=True,
        db_column="next_pop_date",
    )

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_days"


class ShowInstance(models.Model):
    created_at = models.DateTimeField(db_column="created")

    show = models.ForeignKey("schedule.Show", on_delete=models.DO_NOTHING)
    instance = models.ForeignKey(
        "self",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
    )

    starts_at = models.DateTimeField(db_column="starts")
    ends_at = models.DateTimeField(db_column="ends")
    filled_time = models.DurationField(blank=True, null=True, db_column="time_filled")

    last_scheduled_at = models.DateTimeField(
        blank=True,
        null=True,
        db_column="last_scheduled",
    )

    description = models.CharField(max_length=8192, blank=True, null=True)
    modified = models.BooleanField(db_column="modified_instance")
    rebroadcast = models.SmallIntegerField(blank=True, null=True)

    auto_playlist_built = models.BooleanField(db_column="autoplaylist_built")

    record_enabled = models.SmallIntegerField(
        choices=Record.choices,
        default=Record.NO,
        blank=True,
        null=True,
        db_column="record",
    )
    record_file = models.ForeignKey(
        "storage.File",
        on_delete=models.DO_NOTHING,
        blank=True,
        null=True,
        db_column="file_id",
    )

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_instances"


class ShowRebroadcast(models.Model):
    show = models.ForeignKey("schedule.Show", on_delete=models.DO_NOTHING)
    day_offset = models.CharField(max_length=1024)
    start_time = models.TimeField()

    def get_owner(self):
        return self.show.get_owner()

    class Meta:
        managed = False
        db_table = "cc_show_rebroadcast"
