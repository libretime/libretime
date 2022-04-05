from rest_framework import viewsets

from ..models import CeleryTask, ThirdPartyTrackReference
from ..serializers import CeleryTaskSerializer, ThirdPartyTrackReferenceSerializer


class ThirdPartyTrackReferenceViewSet(viewsets.ModelViewSet):
    queryset = ThirdPartyTrackReference.objects.all()
    serializer_class = ThirdPartyTrackReferenceSerializer
    model_permission_name = "thirdpartytrackreference"


class CeleryTaskViewSet(viewsets.ModelViewSet):
    queryset = CeleryTask.objects.all()
    serializer_class = CeleryTaskSerializer
    model_permission_name = "celerytask"
