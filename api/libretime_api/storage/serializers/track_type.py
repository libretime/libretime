from rest_framework import serializers

from ..models import TrackType


class TrackTypeSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = TrackType
        fields = "__all__"
