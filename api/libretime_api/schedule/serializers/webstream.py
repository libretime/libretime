from rest_framework import serializers

from ..models import Webstream, WebstreamMetadata


class WebstreamSerializer(serializers.HyperlinkedModelSerializer):
    id = serializers.IntegerField(read_only=True)

    class Meta:
        model = Webstream
        fields = "__all__"


class WebstreamMetadataSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = WebstreamMetadata
        fields = "__all__"
