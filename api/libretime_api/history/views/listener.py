from rest_framework import viewsets

from ..models import ListenerCount, MountName, Timestamp
from ..serializers import (
    ListenerCountSerializer,
    MountNameSerializer,
    TimestampSerializer,
)


class MountNameViewSet(viewsets.ModelViewSet):
    queryset = MountName.objects.all()
    serializer_class = MountNameSerializer
    model_permission_name = "mountname"


class TimestampViewSet(viewsets.ModelViewSet):
    queryset = Timestamp.objects.all()
    serializer_class = TimestampSerializer
    model_permission_name = "timestamp"


class ListenerCountViewSet(viewsets.ModelViewSet):
    queryset = ListenerCount.objects.all()
    serializer_class = ListenerCountSerializer
    model_permission_name = "listenercount"
