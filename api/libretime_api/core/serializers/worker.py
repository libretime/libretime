from rest_framework import serializers

from ..models import CeleryTask, ThirdPartyTrackReference


class ThirdPartyTrackReferenceSerializer(serializers.ModelSerializer):
    class Meta:
        model = ThirdPartyTrackReference
        fields = "__all__"


class CeleryTaskSerializer(serializers.ModelSerializer):
    class Meta:
        model = CeleryTask
        fields = "__all__"
