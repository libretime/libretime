from rest_framework import serializers

from ..models import ImportedPodcast, Podcast, PodcastEpisode, StationPodcast


class PodcastSerializer(serializers.ModelSerializer):
    class Meta:
        model = Podcast
        fields = "__all__"


class PodcastEpisodeSerializer(serializers.ModelSerializer):
    class Meta:
        model = PodcastEpisode
        fields = "__all__"


class StationPodcastSerializer(serializers.ModelSerializer):
    class Meta:
        model = StationPodcast
        fields = "__all__"


class ImportedPodcastSerializer(serializers.ModelSerializer):
    class Meta:
        model = ImportedPodcast
        fields = "__all__"
