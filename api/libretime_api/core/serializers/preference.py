from rest_framework import serializers

from ..models import Preference, StreamSetting


class PreferenceSerializer(serializers.ModelSerializer):
    class Meta:
        model = Preference
        fields = "__all__"


class StreamSettingSerializer(serializers.ModelSerializer):
    class Meta:
        model = StreamSetting
        fields = "__all__"
