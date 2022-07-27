from rest_framework import serializers

from ..models import (
    PlayoutHistory,
    PlayoutHistoryMetadata,
    PlayoutHistoryTemplate,
    PlayoutHistoryTemplateField,
)


class PlayoutHistorySerializer(serializers.ModelSerializer):
    class Meta:
        model = PlayoutHistory
        fields = "__all__"


class PlayoutHistoryMetadataSerializer(serializers.ModelSerializer):
    class Meta:
        model = PlayoutHistoryMetadata
        fields = "__all__"


class PlayoutHistoryTemplateSerializer(serializers.ModelSerializer):
    class Meta:
        model = PlayoutHistoryTemplate
        fields = "__all__"


class PlayoutHistoryTemplateFieldSerializer(serializers.ModelSerializer):
    class Meta:
        model = PlayoutHistoryTemplateField
        fields = "__all__"
