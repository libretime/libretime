from rest_framework import routers

from .views import FileViewSet, LibraryViewSet

router = routers.DefaultRouter(trailing_slash=False)
router.register("files", FileViewSet)
router.register("libraries", LibraryViewSet)

urls = router.urls
