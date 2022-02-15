import shutil
from pathlib import Path
from uuid import uuid4

from loguru import logger

from .context import Context

MAX_DIR_LEN = 48
MAX_FILE_LEN = 48


def organise_file(ctx: Context) -> Context:
    """
    Move the incoming file into the storage, while preserving the original filename.

    If you import multiple copies of the same file, the behavior is:
    - The first filename is preserved.
    - The next filenames receive an uuid append to the name.
    """
    orig_filename = Path(ctx.original_filename)
    dest_path = Path(ctx.storage_url)

    # Building import path
    if "artist_name" in ctx.metadata:
        dest_path /= ctx.metadata["artist_name"][0:MAX_DIR_LEN]

    if "album_title" in ctx.metadata:
        dest_path /= ctx.metadata["album_title"][0:MAX_DIR_LEN]

    dest_path /= orig_filename.stem[0:MAX_FILE_LEN] + orig_filename.suffix

    # Handle when a file already exists
    if dest_path.is_file():
        if ctx.filepath.samefile(dest_path):
            ctx.metadata["full_path"] = str(dest_path)
            return ctx

        dest_path = dest_path.with_name(f"{dest_path.stem}_{uuid4()}{dest_path.suffix}")
        logger.warning(f"found existing file, using new filepath {dest_path}")

    # Import
    dest_path.parent.mkdir(parents=True, exist_ok=True)

    logger.debug(f"moving {ctx.filepath} to {dest_path}")
    shutil.move(ctx.filepath, dest_path)

    ctx.metadata["full_path"] = str(dest_path)
    return ctx
