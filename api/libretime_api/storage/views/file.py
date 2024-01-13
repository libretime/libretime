import logging
import os

from django.conf import settings
from django.http import HttpResponse
from django.shortcuts import get_object_or_404
from django.utils.encoding import filepath_to_uri
from rest_framework import viewsets
from rest_framework.decorators import action
from rest_framework.serializers import IntegerField

from ..models import File
from ..serializers import FileSerializer

logger = logging.getLogger(__name__)


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):  # pylint: disable=invalid-name
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)

        response = HttpResponse()

        # HTTP headers must be USASCII encoded, or Nginx might not find the file and
        # will return a 404.
        redirect_uri = filepath_to_uri(os.path.join("/api/_media", file.filepath))
        response["X-Accel-Redirect"] = redirect_uri
        return response

    @action(detail=True, methods=["DELETE"])
    def delete_file(self, request, pk=None):  # pylint: disable=invalid-name
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)
        path = os.path.join(settings.CONFIG.storage.path, file.filepath)

        try:
            if os.path.isfile(path):
                os.remove(path)
        except OSError as exception:
            logger.error(f"Could not delete file from storage: {exception}")
            return HttpResponse(status=500)
        file.delete()
        return HttpResponse(status=204)
