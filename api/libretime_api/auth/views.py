from dj_rest_auth.views import LoginView as BaseLoginView, LogoutView as BaseLogoutView
from django.conf import settings
from rest_framework.request import Request

from ..legacy.models import LegacySession


class LoginView(BaseLoginView):
    def post(self, request: Request, *args, **kwargs):
        response = super().post(request, *args, **kwargs)

        legacy_session_id = request.COOKIES.get("PHPSESSID")
        if legacy_session_id:
            legacy_session = LegacySession.login(
                legacy_session_id,
                request.user,  # type: ignore
            )
            if legacy_session is not None:
                response.set_cookie(
                    key="PHPSESSID",
                    value=legacy_session.id,
                    expires="Session",
                    samesite="Strict",
                    httponly=True,
                    secure=settings.CONFIG.general.public_url.startswith("https://"),
                )

        return response


class LogoutView(BaseLogoutView):
    # Fix schema generation
    serializer_class = None

    def post(self, request: Request, *args, **kwargs):
        response = super().post(request, *args, **kwargs)

        legacy_session_id = request.COOKIES.get("PHPSESSID")
        if legacy_session_id:
            LegacySession.logout(legacy_session_id)
            response.delete_cookie(key="PHPSESSID", samesite="Lax")

        return response
