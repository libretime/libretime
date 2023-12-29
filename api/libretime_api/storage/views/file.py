import os

from django.conf import settings
from django.http import FileResponse, HttpResponse
from django.shortcuts import get_object_or_404
from rest_framework import status, viewsets
from rest_framework.decorators import action
from rest_framework.serializers import IntegerField

from ..models import File
from ..serializers import FileSerializer


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):  # pylint: disable=invalid-name
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)
        path = os.path.join(settings.CONFIG.storage.path, file.filepath)
        return FileResponse(open(path, "rb"), content_type=file.mime)

    @action(detail=True, methods=["DELETE"])
    def delete_file(self, request, pk=None):  # pylint: disable=invalid-name
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)
        path = os.path.join(settings.CONFIG.storage.path, file.filepath)
        if os.path.isfile(path):
            os.remove(path)
        file.delete()
        return HttpResponse(status=status.HTTP_204_NO_CONTENT)
