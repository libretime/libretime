from rest_framework import serializers

from ..models import Show, ShowDays, ShowHost, ShowInstance, ShowRebroadcast


class ShowSerializer(serializers.ModelSerializer):
    class Meta:
        model = Show
        fields = [
            "id",
            "name",
            "description",
            "genre",
            "url",
            "image",
            "foreground_color",
            "background_color",
            "live_enabled",
            "linked",
            "linkable",
            "auto_playlist",
            "auto_playlist_enabled",
            "auto_playlist_repeat",
            "intro_playlist",
            "override_intro_playlist",
            "outro_playlist",
            "override_outro_playlist",
        ]


class ShowDaysSerializer(serializers.ModelSerializer):
    class Meta:
        model = ShowDays
        fields = "__all__"


class ShowHostSerializer(serializers.ModelSerializer):
    class Meta:
        model = ShowHost
        fields = "__all__"


class ShowInstanceSerializer(serializers.ModelSerializer):
    class Meta:
        model = ShowInstance
        fields = "__all__"


class ShowRebroadcastSerializer(serializers.ModelSerializer):
    class Meta:
        model = ShowRebroadcast
        fields = "__all__"
