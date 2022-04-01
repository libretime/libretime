import os

from django.http import FileResponse
from django.shortcuts import get_object_or_404
from rest_framework import status, viewsets
from rest_framework.decorators import action
from rest_framework.response import Response

from ..models import File
from ..serializers import FileSerializer


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):
        if pk is None:
            return Response("No file requested", status=status.HTTP_400_BAD_REQUEST)
        try:
            pk = int(pk)
        except ValueError:
            return Response(
                "File ID should be an integer", status=status.HTTP_400_BAD_REQUEST
            )

        filename = get_object_or_404(File, pk=pk)
        directory = filename.directory
        path = os.path.join(directory.directory, filename.filepath)
        response = FileResponse(open(path, "rb"), content_type=filename.mime)
        return response
