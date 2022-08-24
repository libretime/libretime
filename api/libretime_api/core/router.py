from django.urls import path
from rest_framework import routers

from .views import (
    CeleryTaskViewSet,
    InfoView,
    LoginAttemptViewSet,
    PreferenceViewSet,
    ServiceRegisterViewSet,
    StreamPreferencesView,
    StreamStateView,
    ThirdPartyTrackReferenceViewSet,
    UserTokenViewSet,
    UserViewSet,
    VersionView,
)

router = routers.DefaultRouter(trailing_slash=False)
router.register("login-attempts", LoginAttemptViewSet)
router.register("preferences", PreferenceViewSet)
router.register("service-registers", ServiceRegisterViewSet)
router.register("users", UserViewSet)
router.register("user-tokens", UserTokenViewSet)
router.register("celery-tasks", CeleryTaskViewSet)
router.register("third-party-track-references", ThirdPartyTrackReferenceViewSet)

urls = [
    *router.urls,
    path("info", InfoView.as_view()),
    path("version", VersionView.as_view()),
    path("stream/preferences", StreamPreferencesView.as_view()),
    path("stream/state", StreamStateView.as_view()),
]
