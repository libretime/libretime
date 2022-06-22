from rest_framework import routers

from .views import (
    CeleryTaskViewSet,
    CountryViewSet,
    LoginAttemptViewSet,
    PreferenceViewSet,
    ServiceRegisterViewSet,
    StreamSettingViewSet,
    ThirdPartyTrackReferenceViewSet,
    UserTokenViewSet,
    UserViewSet,
)

router = routers.DefaultRouter()
router.register("countries", CountryViewSet)
router.register("login-attempts", LoginAttemptViewSet)
router.register("preferences", PreferenceViewSet)
router.register("service-registers", ServiceRegisterViewSet)
router.register("stream-settings", StreamSettingViewSet)
router.register("users", UserViewSet)
router.register("user-tokens", UserTokenViewSet)
router.register("celery-tasks", CeleryTaskViewSet)
router.register("third-party-track-references", ThirdPartyTrackReferenceViewSet)
