from rest_framework import viewsets

from ..models import CloudFile
from ..serializers import CloudFileSerializer


class CloudFileViewSet(viewsets.ModelViewSet):
    queryset = CloudFile.objects.all()
    serializer_class = CloudFileSerializer
    model_permission_name = "cloudfile"
