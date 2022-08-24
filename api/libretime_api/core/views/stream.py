from rest_framework import views
from rest_framework.response import Response

from ...permissions import IsSystemTokenOrUser
from ..models import Preference
from ..serializers import StreamPreferencesSerializer, StreamStateSerializer


class StreamPreferencesView(views.APIView):
    permission_classes = [IsSystemTokenOrUser]
    serializer_class = StreamPreferencesSerializer
    model_permission_name = "streamsetting"

    def get(self, request):
        data = Preference.get_stream_preferences()
        return Response(
            data.dict(
                include={
                    "input_fade_transition",
                    "message_format",
                    "message_offline",
                }
            )
        )


class StreamStateView(views.APIView):
    permission_classes = [IsSystemTokenOrUser]
    serializer_class = StreamStateSerializer
    model_permission_name = "streamsetting"

    def get(self, request):
        data = Preference.get_stream_state()
        return Response(
            data.dict(
                include={
                    "input_main_connected",
                    "input_main_streaming",
                    "input_show_connected",
                    "input_show_streaming",
                    "schedule_streaming",
                }
            )
        )
