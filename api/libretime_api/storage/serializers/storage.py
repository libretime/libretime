from rest_framework import serializers

from ..models import MusicDir


class MusicDirSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = MusicDir
        fields = "__all__"
