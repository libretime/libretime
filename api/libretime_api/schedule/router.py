from rest_framework import routers

from .views import (
    ImportedPodcastViewSet,
    PlaylistContentViewSet,
    PlaylistViewSet,
    PodcastEpisodeViewSet,
    PodcastViewSet,
    ScheduleViewSet,
    ShowDaysViewSet,
    ShowHostViewSet,
    ShowInstanceViewSet,
    ShowRebroadcastViewSet,
    ShowViewSet,
    SmartBlockContentViewSet,
    SmartBlockCriteriaViewSet,
    SmartBlockViewSet,
    StationPodcastViewSet,
    WebstreamMetadataViewSet,
    WebstreamViewSet,
)

router = routers.DefaultRouter()
router.register("playlist-contents", PlaylistContentViewSet)
router.register("playlists", PlaylistViewSet)
router.register("podcast-episodes", PodcastEpisodeViewSet)
router.register("podcasts", PodcastViewSet)
router.register("station-podcasts", StationPodcastViewSet)
router.register("imported-podcasts", ImportedPodcastViewSet)
router.register("schedule", ScheduleViewSet)
router.register("show-days", ShowDaysViewSet)
router.register("show-hosts", ShowHostViewSet)
router.register("show-instances", ShowInstanceViewSet)
router.register("show-rebroadcasts", ShowRebroadcastViewSet)
router.register("shows", ShowViewSet)
router.register("smart-block-contents", SmartBlockContentViewSet)
router.register("smart-block-criteria", SmartBlockCriteriaViewSet)
router.register("smart-blocks", SmartBlockViewSet)
router.register("webstream-metadata", WebstreamMetadataViewSet)
router.register("webstreams", WebstreamViewSet)
