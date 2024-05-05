import logging
import os
from os import remove

from django.conf import settings
from django.http import HttpResponse
from django.utils.encoding import filepath_to_uri
from rest_framework import status, viewsets
from rest_framework.decorators import action
from rest_framework.exceptions import APIException

from ...schedule.models import Schedule
from ..models import File
from ..serializers import FileSerializer

logger = logging.getLogger(__name__)


class FileInUse(APIException):
    status_code = status.HTTP_409_CONFLICT
    default_detail = "The file is currently used"
    default_code = "file_in_use"


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"

    # pylint: disable=invalid-name,unused-argument
    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):
        instance: File = self.get_object()

        response = HttpResponse()
        # HTTP headers must be USASCII encoded, or Nginx might not find the file and
        # will return a 404.
        redirect_uri = filepath_to_uri(os.path.join("/api/_media", instance.filepath))
        response["X-Accel-Redirect"] = redirect_uri
        return response

    def perform_destroy(self, instance: File):
        if Schedule.is_file_scheduled_in_the_future(file_id=instance.id):
            raise FileInUse("file is scheduled in the future")

        try:
            if instance.filepath is None:
                logger.warning("file does not have a filepath: %d", instance.id)
                return

            path = os.path.join(settings.CONFIG.storage.path, instance.filepath)

            if not os.path.isfile(path):
                logger.warning("file does not exist in storage: %d", instance.id)
                return

            remove(path)
        except OSError as exception:
            raise APIException("could not delete file from storage") from exception
