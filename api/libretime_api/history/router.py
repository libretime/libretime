from rest_framework import routers

from .views import (
    ListenerCountViewSet,
    LiveLogViewSet,
    MountNameViewSet,
    PlayoutHistoryMetadataViewSet,
    PlayoutHistoryTemplateFieldViewSet,
    PlayoutHistoryTemplateViewSet,
    PlayoutHistoryViewSet,
    TimestampViewSet,
)

router = routers.DefaultRouter()
router.register("listener-counts", ListenerCountViewSet)
router.register("live-logs", LiveLogViewSet)
router.register("mount-names", MountNameViewSet)
router.register("playout-history", PlayoutHistoryViewSet)
router.register("playout-history-metadata", PlayoutHistoryMetadataViewSet)
router.register("playout-history-templates", PlayoutHistoryTemplateViewSet)
router.register("playout-history-template-fields", PlayoutHistoryTemplateFieldViewSet)
router.register("timestamps", TimestampViewSet)
