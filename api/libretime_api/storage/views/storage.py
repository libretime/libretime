from rest_framework import viewsets

from ..models import MusicDir
from ..serializers import MusicDirSerializer


class MusicDirViewSet(viewsets.ModelViewSet):
    queryset = MusicDir.objects.all()
    serializer_class = MusicDirSerializer
    model_permission_name = "musicdir"
