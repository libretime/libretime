from rest_framework import serializers

from ..models import CloudFile


class CloudFileSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = CloudFile
        fields = "__all__"
