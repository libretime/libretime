from rest_framework import serializers

from ..models import Show, ShowDays, ShowHost, ShowInstance, ShowRebroadcast


class ShowSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Show
        fields = [
            "item_url",
            "id",
            "name",
            "url",
            "genre",
            "description",
            "color",
            "background_color",
            "linked",
            "is_linkable",
            "image_path",
            "has_autoplaylist",
            "autoplaylist_repeat",
            "autoplaylist",
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
    file_id = serializers.IntegerField(source="file.id", read_only=True)

    class Meta:
        model = ShowInstance
        fields = [
            "item_url",
            "id",
            "description",
            "starts",
            "ends",
            "record",
            "rebroadcast",
            "time_filled",
            "created",
            "last_scheduled",
            "modified_instance",
            "autoplaylist_built",
            "show",
            "show_id",
            "instance",
            "file",
            "file_id",
        ]


class ShowRebroadcastSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = ShowRebroadcast
        fields = "__all__"
