from rest_framework import routers

from .views import (
    PlaylistContentViewSet,
    PlaylistViewSet,
    ScheduleViewSet,
    ShowDaysViewSet,
    ShowHostViewSet,
    ShowInstanceViewSet,
    ShowRebroadcastViewSet,
    ShowViewSet,
    SmartBlockContentViewSet,
    SmartBlockCriteriaViewSet,
    SmartBlockViewSet,
    WebstreamMetadataViewSet,
    WebstreamViewSet,
)

router = routers.DefaultRouter()
router.register("playlist-contents", PlaylistContentViewSet)
router.register("playlists", PlaylistViewSet)
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
