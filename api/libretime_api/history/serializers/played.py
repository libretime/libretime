from rest_framework import serializers

from ..models import (
    PlayoutHistory,
    PlayoutHistoryMetadata,
    PlayoutHistoryTemplate,
    PlayoutHistoryTemplateField,
)


class PlayoutHistorySerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = PlayoutHistory
        fields = "__all__"


class PlayoutHistoryMetadataSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = PlayoutHistoryMetadata
        fields = "__all__"


class PlayoutHistoryTemplateSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = PlayoutHistoryTemplate
        fields = "__all__"


class PlayoutHistoryTemplateFieldSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = PlayoutHistoryTemplateField
        fields = "__all__"
