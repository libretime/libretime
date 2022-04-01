from rest_framework import serializers

from ..models import Playlist, PlaylistContent


class PlaylistSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Playlist
        fields = "__all__"


class PlaylistContentSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = PlaylistContent
        fields = "__all__"
