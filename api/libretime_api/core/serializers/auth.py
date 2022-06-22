from rest_framework import serializers

from ..models import LoginAttempt, UserToken


class UserTokenSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = UserToken
        fields = "__all__"


class LoginAttemptSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = LoginAttempt
        fields = "__all__"
