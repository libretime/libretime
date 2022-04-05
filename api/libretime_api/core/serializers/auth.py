from rest_framework import serializers

from ..models import LoginAttempt, Session, UserToken


class UserTokenSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = UserToken
        fields = "__all__"


class SessionSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Session
        fields = "__all__"


class LoginAttemptSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = LoginAttempt
        fields = "__all__"
