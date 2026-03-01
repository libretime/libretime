from rest_framework import serializers

from ..models import LiveLog


class LiveLogSerializer(serializers.ModelSerializer):
    class Meta:
        model = LiveLog
        fields = "__all__"
