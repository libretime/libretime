from rest_framework import serializers


# pylint: disable=abstract-method
class StreamPreferencesSerializer(serializers.Serializer):
    input_fade_transition = serializers.FloatField(read_only=True)
    message_format = serializers.IntegerField(read_only=True)
    message_offline = serializers.CharField(read_only=True)
    replay_gain_enabled = serializers.BooleanField(read_only=True)
    replay_gain_offset = serializers.FloatField(read_only=True)


# pylint: disable=abstract-method
class StreamStateSerializer(serializers.Serializer):
    input_main_connected = serializers.BooleanField(read_only=True)
    input_main_streaming = serializers.BooleanField(read_only=True)
    input_show_connected = serializers.BooleanField(read_only=True)
    input_show_streaming = serializers.BooleanField(read_only=True)
    schedule_streaming = serializers.BooleanField(read_only=True)
