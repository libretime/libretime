from rest_framework import serializers

from ..models import LiveLog


class LiveLogSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = LiveLog
        fields = "__all__"
