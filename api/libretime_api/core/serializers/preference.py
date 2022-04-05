from rest_framework import serializers

from ..models import Preference, StreamSetting


class PreferenceSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Preference
        fields = "__all__"


class StreamSettingSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = StreamSetting
        fields = "__all__"
