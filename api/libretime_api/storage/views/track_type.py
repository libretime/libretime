from rest_framework import viewsets

from ..models import TrackType
from ..serializers import TrackTypeSerializer


class TrackTypeViewSet(viewsets.ModelViewSet):
    queryset = TrackType.objects.all()
    serializer_class = TrackTypeSerializer
    model_permission_name = "tracktype"
