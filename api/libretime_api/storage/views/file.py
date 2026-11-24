import logging
import os

from django.http import HttpResponse
from django.utils import timezone
from django.utils.encoding import filepath_to_uri
from django_filters import rest_framework as filters
from rest_framework import status, viewsets
from rest_framework.decorators import action
from rest_framework.exceptions import APIException

from ...schedule.models import PlaylistContent, Schedule, SmartBlockContent
from ..models import File
from ..serializers import FileSerializer
from ..services import dispose_of_file_bytes

logger = logging.getLogger(__name__)


class FileInUse(APIException):
    status_code = status.HTTP_409_CONFLICT
    default_detail = "The file is currently used"
    default_code = "file_in_use"


class FileViewSet(viewsets.ModelViewSet):
    queryset = File.objects.all()
    serializer_class = FileSerializer
    model_permission_name = "file"
    filter_backends = (filters.DjangoFilterBackend,)
    filterset_fields = ("md5", "genre")

    def get_queryset(self):
        # Soft-deleted files (#1163) leave the library — including md5
        # lookups, so re-importing a previously deleted file is not
        # blocked by its ghost — but stay addressable by id: calendar
        # rows and playout history must keep resolving their metadata.
        if self.action == "list":
            return File.objects.filter(deleted_at__isnull=True)
        return File.objects.all()

    # pylint: disable=invalid-name,unused-argument
    @action(detail=True, methods=["GET"])
    def download(self, request, pk=None):
        instance: File = self.get_object()

        if instance.deleted_at is not None:
            # the bytes were deliberately disposed of; the record lives on
            return HttpResponse(status=status.HTTP_410_GONE)

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
            dispose_of_file_bytes(instance)
        except OSError as exception:
            raise APIException("could not delete file from storage") from exception

        # Soft delete: the row survives so playout history, calendar rows
        # and playlists remain resolvable (#1163). ``deleted_at`` is the
        # human-intent marker nothing ever re-flips; ``exists`` keeps
        # storage truth honest so the legacy library queries (which filter
        # on file_exists) hide the row without learning about deletion.
        # ``deleted_by`` records who — NULL for API-key/system callers.
        File.objects.filter(pk=instance.pk).update(
            deleted_at=timezone.now(),
            deleted_by=getattr(self.request.user, "pk", None),
            exists=False,
        )

        # Playlist and smart-block rows are forward-looking templates: the
        # old hard delete pruned them via ON DELETE CASCADE, and volunteers
        # rely on that ("deleted track vanishes from my playlist"). With
        # soft delete the cascade never fires, so the same pruning happens
        # deliberately here — otherwise an autoloaded playlist would keep
        # scheduling the deleted file and playout would punch a hole in the
        # show on the 410. Aired grids (cc_playlists) and playout history
        # reference the file row and are deliberately left untouched
        # (#1163).
        PlaylistContent.objects.filter(file=instance).delete()
        SmartBlockContent.objects.filter(file=instance).delete()
