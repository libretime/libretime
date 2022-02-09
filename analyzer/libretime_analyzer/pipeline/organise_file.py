import errno
import os
import shutil
import time
import uuid

from loguru import logger

from .context import Context


def organise_file(ctx: Context) -> Context:
    """Move the file at audio_file_path over into the import_directory/import,
    renaming it to original_filename.

    This analyzer copies a file over from a temporary directory (stor/organize)
    into the Airtime library (stor/imported).

    If you import three copies of the same file, the behaviour is:
    - The filename is of the first file preserved.
    - The filename of the second file has the timestamp attached to it.
    - The filename of the third file has a UUID placed after the timestamp, but ONLY IF it's imported within 1 second of the second file (ie. if the timestamp is the same).

    Keyword arguments:
        audio_file_path: Path to the file to be imported.
        import_directory: Path to the "import" directory inside the Airtime stor directory.
                            (eg. /srv/airtime/stor/import)
        original_filename: The filename of the file when it was uploaded to Airtime.
        metadata: A dictionary where the "full_path" of where the file is moved to will be added.
    """

    # Import the file over to it's final location.
    # TODO: Also, handle the case where the move fails and write some code
    # to possibly move the file to problem_files.

    max_dir_len = 48
    max_file_len = 48
    final_file_path = ctx.storage_url
    orig_file_basename, orig_file_extension = os.path.splitext(ctx.original_filename)
    if "artist_name" in ctx.metadata:
        final_file_path += (
            "/" + ctx.metadata["artist_name"][0:max_dir_len]
        )  # truncating with array slicing
    if "album_title" in ctx.metadata:
        final_file_path += "/" + ctx.metadata["album_title"][0:max_dir_len]
    # Note that orig_file_extension includes the "." already
    final_file_path += "/" + orig_file_basename[0:max_file_len] + orig_file_extension

    # Ensure any redundant slashes are stripped
    final_file_path = os.path.normpath(final_file_path)

    # If a file with the same name already exists in the "import" directory, then
    # we add a unique string to the end of this one. We never overwrite a file on import
    # because if we did that, it would mean Airtime's database would have
    # the wrong information for the file we just overwrote (eg. the song length would be wrong!)
    # If the final file path is the same as the file we've been told to import (which
    # you often do when you're debugging), then don't move the file at all.

    if os.path.exists(final_file_path):
        if os.path.samefile(ctx.filepath, final_file_path):
            ctx.metadata["full_path"] = final_file_path
            return ctx
        base_file_path, file_extension = os.path.splitext(final_file_path)
        final_file_path = "{}_{}{}".format(
            base_file_path,
            time.strftime("%m-%d-%Y-%H-%M-%S", time.localtime()),
            file_extension,
        )

    # If THAT path exists, append a UUID instead:
    while os.path.exists(final_file_path):
        base_file_path, file_extension = os.path.splitext(final_file_path)
        final_file_path = "{}_{}{}".format(
            base_file_path,
            str(uuid.uuid4()),
            file_extension,
        )

    # Ensure the full path to the file exists
    mkdir_p(os.path.dirname(final_file_path))

    # Move the file into its final destination directory
    logger.debug(f"Moving {ctx.filepath} to {final_file_path}")
    shutil.move(ctx.filepath, final_file_path)

    ctx.metadata["full_path"] = final_file_path
    return ctx


def mkdir_p(path):
    """Make all directories in a tree (like mkdir -p)"""
    if path == "":
        return
    try:
        os.makedirs(path)
    except OSError as exc:  # Python >2.5
        if exc.errno == errno.EEXIST and os.path.isdir(path):
            pass
        else:
            raise
