import logging
import os

from django.conf import settings
from django.http import HttpResponse
from django.shortcuts import get_object_or_404
from django.utils.encoding import filepath_to_uri
from rest_framework import viewsets
from rest_framework.decorators import action
from rest_framework.serializers import IntegerField

from ...schedule.models import Schedule
from ..models import File
from ..serializers import FileSerializer

logger = logging.getLogger(__name__)


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):  # pylint: disable=invalid-name
        instance: File = self.get_object()

        response = HttpResponse()
        # HTTP headers must be USASCII encoded, or Nginx might not find the file and
        # will return a 404.
        redirect_uri = filepath_to_uri(os.path.join("/api/_media", instance.filepath))
        response["X-Accel-Redirect"] = redirect_uri
        return response

    def destroy(self, request, *args, **kwargs):  # pylint: disable=invalid-name
        pk = kwargs.get("pk", None)
        pk = IntegerField().to_internal_value(data=pk)

        file = get_object_or_404(File, pk=pk)

        # Check if the file is scheduled to be played in the future
        if Schedule.is_file_scheduled_in_the_future(file_id=pk) is True:
            return HttpResponse(status=409)

        path = os.path.join(settings.CONFIG.storage.path, file.filepath)

        try:
            if os.path.isfile(path):
                os.remove(path)
        except OSError as exception:
            logger.error("Could not delete file from storage: %s", exception)
            return HttpResponse(status=500)
        self.perform_destroy(file)
        return HttpResponse(status=204)
