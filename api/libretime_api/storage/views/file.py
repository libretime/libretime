import os

from django.http import FileResponse
from django.shortcuts import get_object_or_404
from rest_framework import viewsets
from rest_framework.decorators import action
from rest_framework.serializers import IntegerField

from ..models import File
from ..serializers import FileSerializer


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)
        storage = file.directory
        path = os.path.join(storage.directory, file.filepath)

        return FileResponse(open(path, "rb"), content_type=file.mime)
