from rest_framework import serializers

from ..models import ListenerCount, MountName, Timestamp


class MountNameSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = MountName
        fields = "__all__"


class TimestampSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Timestamp
        fields = "__all__"


class ListenerCountSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ListenerCount
        fields = "__all__"
