from dj_rest_auth.views import (
    PasswordChangeView,
    PasswordResetConfirmView,
    PasswordResetView,
    UserDetailsView,
)
from django.urls import path

from .views import LoginView, LogoutView

urls = [
    path("auth/login/", LoginView.as_view(), name="rest_login"),
    path("auth/logout/", LogoutView.as_view(), name="rest_logout"),
    path(
        "auth/password/change/",
        PasswordChangeView.as_view(),
        name="rest_password_change",
    ),
    path(
        "auth/password/reset/",
        PasswordResetView.as_view(),
        name="rest_password_reset",
    ),
    path(
        "auth/password/reset/confirm/",
        PasswordResetConfirmView.as_view(),
        name="rest_password_reset_confirm",
    ),
    path(
        "auth/user/",
        UserDetailsView.as_view(),
        name="rest_user_details",
    ),
]
