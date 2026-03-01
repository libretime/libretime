from rest_framework import serializers

from ..models import Schedule


class ReadScheduleSerializer(serializers.ModelSerializer):
    cue_out = serializers.DurationField(source="get_cue_out", read_only=True)
    ends_at = serializers.DateTimeField(source="get_ends_at", read_only=True)

    class Meta:
        model = Schedule
        fields = "__all__"


class WriteScheduleSerializer(serializers.ModelSerializer):

    class Meta:
        model = Schedule
        fields = "__all__"
