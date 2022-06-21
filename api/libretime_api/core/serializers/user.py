from django.contrib.auth import get_user_model
from rest_framework import serializers


class UserSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = get_user_model()
        fields = [
            "item_url",
            "role",
            "username",
            "email",
            "first_name",
            "last_name",
            "login_attempts",
            "last_login",
            "last_failed_login",
            "skype",
            "jabber",
            "phone",
        ]
