from rest_framework import serializers

from ..models import Schedule


class ScheduleSerializer(serializers.HyperlinkedModelSerializer):
    file_id = serializers.IntegerField(source="file.id", read_only=True)
    stream_id = serializers.IntegerField(source="stream.id", read_only=True)
    instance_id = serializers.IntegerField(source="instance.id", read_only=True)
    cue_out = serializers.DurationField(source="get_cue_out", read_only=True)
    ends_at = serializers.DateTimeField(source="get_ends_at", read_only=True)

    class Meta:
        model = Schedule
        fields = [
            "item_url",
            "id",
            "starts_at",
            "ends_at",
            "instance",
            "instance_id",
            "file",
            "file_id",
            "stream",
            "stream_id",
            "length",
            "fade_in",
            "fade_out",
            "cue_in",
            "cue_out",
            "position",
            "position_status",
            "broadcasted",
            "played",
            "overbooked",
        ]
