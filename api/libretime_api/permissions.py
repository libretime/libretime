from secrets import compare_digest

from django.conf import settings
from rest_framework.permissions import BasePermission
from rest_framework.request import Request

from .core.models import Role

REQUEST_PERMISSION_TYPE_MAP = {
    "GET": "view",
    "HEAD": "view",
    "OPTIONS": "view",
    "POST": "change",
    "PUT": "change",
    "DELETE": "delete",
    "PATCH": "change",
}


def get_own_obj(request, view):
    user = request.user
    if user is None or user.role != Role.HOST:
        return ""
    if request.method == "GET":
        return ""
    qs = view.queryset.all()
    try:
        model_owners = []
        for model in qs:
            owner = model.get_owner()
            if owner not in model_owners:
                model_owners.append(owner)
        if len(model_owners) == 1 and user in model_owners:
            return "own_"
    except AttributeError:
        return ""
    return ""


def get_permission_for_view(request, view):
    try:
        permission_type = REQUEST_PERMISSION_TYPE_MAP[request.method]
        if view.__class__.__name__ == "APIRootView":
            return f"{permission_type}_apiroot"
        model = view.model_permission_name
        own_obj = get_own_obj(request, view)
        return f"{permission_type}_{own_obj}{model}"
    except AttributeError:
        return None


def check_authorization_header(request: Request):
    auth_header = request.headers.get("authorization", "")
    if auth_header.startswith("Api-Key"):
        token = auth_header.split()[1]
        return compare_digest(token, settings.CONFIG.general.api_key)
    return False


class IsAdminOrOwnUser(BasePermission):
    """
    Implements Django Rest Framework permissions. This is separate from
    Django's standard permission system. For details see
    https://www.django-rest-framework.org/api-guide/permissions/#custom-permissions
    """

    def has_permission(self, request, view):
        if request.user.is_superuser():
            return True
        return False

    def has_object_permission(self, request, view, obj):
        if request.user.is_superuser():
            return True
        return obj.username == request.user


class IsSystemTokenOrUser(BasePermission):
    """
    Implements Django Rest Framework permissions. This is separate from
    Django's standard permission system. For details see
    https://www.django-rest-framework.org/api-guide/permissions/#custom-permissions

    This permission allows services (liquidsoap, 3rd-party, etc) to connect with
    an API-Key header. All standard-users (i.e. not using the API-Key) have their
    permissions checked against Django's standard permission system.
    """

    def has_permission(self, request, view):
        if request.user and request.user.is_authenticated:
            perm = get_permission_for_view(request, view)
            # Required as view_apiroot is a permission not linked to a specific
            # model. This use-case allows users to view the base of the API
            # explorer. Their assigned group permissions determine further access
            # into the explorer.
            if perm == "view_apiroot":
                return True
            return request.user.has_perm(perm)
        return check_authorization_header(request)

    def has_object_permission(self, request, view, obj):
        if request.user and request.user.is_authenticated:
            perm = get_permission_for_view(request, view)
            return request.user.has_perm(perm, obj)
        return check_authorization_header(request)
