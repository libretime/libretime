from rest_framework import serializers

from ..models import Schedule


class ScheduleSerializer(serializers.HyperlinkedModelSerializer):
    file_id = serializers.IntegerField(source="file.id", read_only=True)
    stream_id = serializers.IntegerField(source="stream.id", read_only=True)
    instance_id = serializers.IntegerField(source="instance.id", read_only=True)
    cue_out = serializers.DurationField(source="get_cueout", read_only=True)
    ends = serializers.DateTimeField(source="get_ends", read_only=True)

    class Meta:
        model = Schedule
        fields = [
            "item_url",
            "id",
            "starts",
            "ends",
            "file",
            "file_id",
            "stream",
            "stream_id",
            "clip_length",
            "fade_in",
            "fade_out",
            "cue_in",
            "cue_out",
            "media_item_played",
            "instance",
            "instance_id",
            "playout_status",
            "broadcasted",
            "position",
        ]
