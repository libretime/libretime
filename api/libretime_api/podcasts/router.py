from rest_framework import routers

from .views import (
    ImportedPodcastViewSet,
    PodcastEpisodeViewSet,
    PodcastViewSet,
    StationPodcastViewSet,
)

router = routers.DefaultRouter()
router.register("podcast-episodes", PodcastEpisodeViewSet)
router.register("podcasts", PodcastViewSet)
router.register("station-podcasts", StationPodcastViewSet)
router.register("imported-podcasts", ImportedPodcastViewSet)
