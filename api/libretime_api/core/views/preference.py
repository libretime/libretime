from rest_framework import viewsets

from ..models import Preference, StreamSetting
from ..serializers import PreferenceSerializer, StreamSettingSerializer


class PreferenceViewSet(viewsets.ModelViewSet):
    queryset = Preference.objects.all()
    serializer_class = PreferenceSerializer
    model_permission_name = "preference"


class StreamSettingViewSet(viewsets.ModelViewSet):
    queryset = StreamSetting.objects.all()
    serializer_class = StreamSettingSerializer
    model_permission_name = "streamsetting"
