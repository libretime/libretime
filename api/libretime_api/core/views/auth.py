from rest_framework import viewsets

from ..models import LoginAttempt, Session, UserToken
from ..serializers import LoginAttemptSerializer, SessionSerializer, UserTokenSerializer


class UserTokenViewSet(viewsets.ModelViewSet):
    queryset = UserToken.objects.all()
    serializer_class = UserTokenSerializer
    model_permission_name = "usertoken"


class SessionViewSet(viewsets.ModelViewSet):
    queryset = Session.objects.all()
    serializer_class = SessionSerializer
    model_permission_name = "session"


class LoginAttemptViewSet(viewsets.ModelViewSet):
    queryset = LoginAttempt.objects.all()
    serializer_class = LoginAttemptSerializer
    model_permission_name = "loginattempt"
