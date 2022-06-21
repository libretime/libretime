"""
URL Configuration

For more information on this file, see
https://docs.djangoproject.com/en/3.2/topics/http/urls/
"""
from django.urls import include, path
from drf_spectacular.views import SpectacularAPIView, SpectacularSwaggerView
from rest_framework import routers

from .core.router import router as core_router
from .core.views import version
from .history.router import router as history_router
from .podcasts.router import router as podcasts_router
from .schedule.router import router as schedule_router
from .storage.router import router as storage_router

router = routers.DefaultRouter()

router.registry.extend(core_router.registry)
router.registry.extend(history_router.registry)
router.registry.extend(podcasts_router.registry)
router.registry.extend(schedule_router.registry)
router.registry.extend(storage_router.registry)


urlpatterns = [
    path("api/v2/", include(router.urls)),
    path("api/v2/version/", version),
    path(
        "api/v2/schema/",
        SpectacularAPIView.as_view(),
        name="schema",
    ),
    path(
        "api/v2/schema/swagger-ui/",
        SpectacularSwaggerView.as_view(url_name="schema"),
        name="swagger-ui",
    ),
    path("api-auth/", include("rest_framework.urls", namespace="rest_framework")),
]
