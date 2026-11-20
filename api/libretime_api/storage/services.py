import logging
import os

from django.conf import settings

from .models import File

logger = logging.getLogger(__name__)


def dispose_of_file_bytes(file: File) -> None:
    """
    Remove the stored audio bytes of a (soft-)deleted file.

    This is the single seam for byte disposal. A future retention policy —
    move to a trash/ directory, archive, N-day delayed deletion — belongs
    here, not in the view.

    The metadata row is never touched by this function: the row is only
    ever soft-deleted by the API (``deleted_at`` / ``deleted_by``). Row
    deletion, with its ``ON DELETE CASCADE`` fan-out that destroys playout
    history and schedule references (#1163), is intentionally reserved for
    an explicit admin purge, which does not exist yet.
    """
    if file.filepath is None:
        logger.warning("file does not have a filepath: %d", file.id)
        return

    path = os.path.join(settings.CONFIG.storage.path, file.filepath)

    if not os.path.isfile(path):
        # storage may be missing/unmounted; the deletion must still record
        # the human intent, so this is not fatal
        logger.warning("file does not exist in storage: %d", file.id)
        return

    os.remove(path)
