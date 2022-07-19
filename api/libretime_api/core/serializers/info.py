from rest_framework import serializers


# pylint: disable=abstract-method
class VersionSerializer(serializers.Serializer):
    api_version = serializers.CharField()
