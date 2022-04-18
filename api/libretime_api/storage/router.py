from rest_framework import routers

from .views import CloudFileViewSet, FileViewSet, TrackTypeViewSet

router = routers.DefaultRouter()
router.register("files", FileViewSet)
router.register("cloud-files", CloudFileViewSet)
router.register("track-types", TrackTypeViewSet)
