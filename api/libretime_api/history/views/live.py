from rest_framework import viewsets

from ..models import LiveLog
from ..serializers import LiveLogSerializer


class LiveLogViewSet(viewsets.ModelViewSet):
    queryset = LiveLog.objects.all()
    serializer_class = LiveLogSerializer
    model_permission_name = "livelog"
