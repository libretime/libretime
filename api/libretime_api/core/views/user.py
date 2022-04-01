from django.contrib.auth import get_user_model
from rest_framework import viewsets

from ...permissions import IsAdminOrOwnUser
from ..serializers import UserSerializer


class UserViewSet(viewsets.ModelViewSet):
    queryset = get_user_model().objects.all()
    serializer_class = UserSerializer
    permission_classes = [IsAdminOrOwnUser]
    model_permission_name = "user"
