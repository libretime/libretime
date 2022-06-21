from rest_framework import viewsets

from ..models import ImportedPodcast, Podcast, PodcastEpisode, StationPodcast
from ..serializers import (
    ImportedPodcastSerializer,
    PodcastEpisodeSerializer,
    PodcastSerializer,
    StationPodcastSerializer,
)


class PodcastViewSet(viewsets.ModelViewSet):
    queryset = Podcast.objects.all()
    serializer_class = PodcastSerializer
    model_permission_name = "podcast"


class PodcastEpisodeViewSet(viewsets.ModelViewSet):
    queryset = PodcastEpisode.objects.all()
    serializer_class = PodcastEpisodeSerializer
    model_permission_name = "podcastepisode"


class StationPodcastViewSet(viewsets.ModelViewSet):
    queryset = StationPodcast.objects.all()
    serializer_class = StationPodcastSerializer
    model_permission_name = "station"


class ImportedPodcastViewSet(viewsets.ModelViewSet):
    queryset = ImportedPodcast.objects.all()
    serializer_class = ImportedPodcastSerializer
    model_permission_name = "importedpodcast"
