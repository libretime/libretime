from django.contrib.auth import get_user_model
from rest_framework import serializers


class UserSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = get_user_model()
        fields = [
            "item_url",
            "username",
            "type",
            "first_name",
            "last_name",
            "lastfail",
            "skype_contact",
            "jabber_contact",
            "email",
            "cell_phone",
            "login_attempts",
        ]
