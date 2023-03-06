from django.urls import path

from . import views

urlpatterns = [
    path("login", views.login_view, name="authentication.login"),
    path("logout", views.logout_view, name="authentication.logout"),
]
