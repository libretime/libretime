from rest_framework import serializers

from ..models import Show, ShowDays, ShowHost, ShowInstance, ShowRebroadcast


class ShowSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Show
        fields = [
            "item_url",
            "id",
            "name",
            "description",
            "genre",
            "url",
            "image",
            "foreground_color",
            "background_color",
            "linked",
            "linkable",
            "auto_playlist",
            "auto_playlist_enabled",
            "auto_playlist_repeat",
        ]


class ShowDaysSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ShowDays
        fields = "__all__"


class ShowHostSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ShowHost
        fields = "__all__"


class ShowInstanceSerializer(serializers.HyperlinkedModelSerializer):
    show_id = serializers.IntegerField(source="show.id", read_only=True)
    record_file_id = serializers.IntegerField(source="record_file.id", read_only=True)

    class Meta:
        model = ShowInstance
        fields = [
            "item_url",
            "id",
            "created_at",
            "show",
            "show_id",
            "instance",
            "starts_at",
            "ends_at",
            "filled_time",
            "last_scheduled_at",
            "description",
            "modified",
            "rebroadcast",
            "auto_playlist_built",
            "record_enabled",
            "record_file",
            "record_file_id",
        ]


class ShowRebroadcastSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ShowRebroadcast
        fields = "__all__"
