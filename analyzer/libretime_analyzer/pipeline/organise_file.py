import logging
import shutil
from pathlib import Path
from uuid import uuid4

logger = logging.getLogger(__name__)

MAX_DIR_LEN = 48
MAX_FILE_LEN = 48


def organise_file(
    filepath_: str,
    storage_url: str,
    original_filename: str,
    metadata: dict,
) -> dict:
    """
    Move the incoming file into the storage, while preserving the original filename.

    If you import multiple copies of the same file, the behavior is:
    - The first filename is preserved.
    - The next filenames receive an uuid append to the name.
    """
    filepath = Path(filepath_)

    orig_filename = Path(original_filename)
    dest_path = Path(storage_url)

    # Building import path
    if "artist_name" in metadata:
        dest_path /= metadata["artist_name"][0:MAX_DIR_LEN]

    if "album_title" in metadata:
        dest_path /= metadata["album_title"][0:MAX_DIR_LEN]

    dest_path /= orig_filename.stem[0:MAX_FILE_LEN] + orig_filename.suffix

    # Handle when a file already exists
    if dest_path.is_file():
        if filepath.samefile(dest_path):
            metadata["full_path"] = str(dest_path)
            return metadata

        dest_path = dest_path.with_name(f"{dest_path.stem}_{uuid4()}{dest_path.suffix}")
        logger.warning(f"found existing file, using new filepath {dest_path}")

    # Import
    dest_path.parent.mkdir(parents=True, exist_ok=True)

    logger.debug(f"moving {filepath} to {dest_path}")
    shutil.move(filepath, dest_path)

    metadata["full_path"] = str(dest_path)
    return metadata
