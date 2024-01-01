import os

from django.http import HttpResponse
from django.shortcuts import get_object_or_404
from django.utils.encoding import iri_to_uri
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
    def download(self, request, pk=None):  # pylint: disable=invalid-name
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)

        response = HttpResponse()
        response["Content-Type"] = file.mime

        # HTTP headers must be USASCII encoded, or Nginx might not find the file and
        # will return a 404.
        redirect_uri = iri_to_uri(os.path.join("/api/_media", file.filepath))
        response["X-Accel-Redirect"] = redirect_uri
        return response
