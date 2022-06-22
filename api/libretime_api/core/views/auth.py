from rest_framework import viewsets

from ..models import LoginAttempt, UserToken
from ..serializers import LoginAttemptSerializer, UserTokenSerializer


class UserTokenViewSet(viewsets.ModelViewSet):
    queryset = UserToken.objects.all()
    serializer_class = UserTokenSerializer
    model_permission_name = "usertoken"


class LoginAttemptViewSet(viewsets.ModelViewSet):
    queryset = LoginAttempt.objects.all()
    serializer_class = LoginAttemptSerializer
    model_permission_name = "loginattempt"
