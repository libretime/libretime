from rest_framework import routers

from .views import FileViewSet, LibraryViewSet

router = routers.DefaultRouter()
router.register("files", FileViewSet)
router.register("libraries", LibraryViewSet)
