from rest_framework import serializers

from ..models import Webstream, WebstreamMetadata


class WebstreamSerializer(serializers.ModelSerializer):
    class Meta:
        model = Webstream
        fields = "__all__"


class WebstreamMetadataSerializer(serializers.ModelSerializer):
    class Meta:
        model = WebstreamMetadata
        fields = "__all__"
