from rest_framework import routers

from .views import CloudFileViewSet, FileViewSet, MusicDirViewSet, TrackTypeViewSet

router = routers.DefaultRouter()
router.register("files", FileViewSet)
router.register("music-dirs", MusicDirViewSet)
router.register("cloud-files", CloudFileViewSet)
router.register("track-types", TrackTypeViewSet)
