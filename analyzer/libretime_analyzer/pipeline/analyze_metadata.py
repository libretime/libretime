import hashlib
from datetime import timedelta
from pathlib import Path

import mutagen
from loguru import logger

from .context import Context


def analyze_metadata(ctx: Context) -> Context:
    """
    Extract audio metadata from tags embedded in the file using mutagen.
    """
    # Airtime <= 2.5.x required fields
    ctx.metadata["ftype"] = "audioclip"
    ctx.metadata["hidden"] = False

    # Get file properties
    ctx.metadata["filesize"] = ctx.filepath.stat().st_size
    ctx.metadata["md5"] = compute_md5(ctx.filepath)

    # Get audio file metadata
    extracted = mutagen.File(ctx.filepath, easy=True)
    if extracted is None:
        logger.warning(f"no metadata were extracted for {ctx.filepath}")
        return ctx

    ctx.metadata["mime"] = extracted.mime[0]

    info = extracted.info
    if hasattr(info, "sample_rate"):
        ctx.metadata["sample_rate"] = info.sample_rate

    if hasattr(info, "bitrate"):
        ctx.metadata["bit_rate"] = info.bitrate

    if hasattr(info, "length"):
        ctx.metadata["length_seconds"] = info.length
        ctx.metadata["length"] = str(timedelta(seconds=info.length))

    try:
        # Special handling for the number of channels in mp3 files.
        # 0=stereo, 1=joint stereo, 2=dual channel, 3=mono
        if ctx.metadata["mime"] in ("audio/mpeg", "audio/mp3"):
            ctx.metadata["channels"] = 1 if info.mode == 3 else 2
        else:
            ctx.metadata["channels"] = info.channels
    except (AttributeError, KeyError):
        pass

    try:
        track_number = extracted["tracknumber"]

        if isinstance(track_number, list):
            track_number = track_number[0]

        track_number_tokens = track_number
        if "/" in track_number:
            track_number_tokens = track_number.split("/")
            track_number = track_number_tokens[0]
        elif "-" in track_number:
            track_number_tokens = track_number.split("-")
            track_number = track_number_tokens[0]
        ctx.metadata["track_number"] = track_number
        track_total = track_number_tokens[1]
        ctx.metadata["track_total"] = track_total
    except (AttributeError, KeyError, IndexError):
        pass

    extracted_tags_mapping = {
        "title": "track_title",
        "artist": "artist_name",
        "album": "album_title",
        "bpm": "bpm",
        "composer": "composer",
        "conductor": "conductor",
        "copyright": "copyright",
        "comment": "comment",
        "encoded_by": "encoder",
        "genre": "genre",
        "isrc": "isrc",
        "label": "label",
        "organization": "label",
        # "length": "length",
        "language": "language",
        "last_modified": "last_modified",
        "mood": "mood",
        "bit_rate": "bit_rate",
        "replay_gain": "replaygain",
        # "tracknumber": "track_number",
        # "track_total": "track_total",
        "website": "website",
        "date": "year",
        # "mime_type": "mime",
    }

    for extracted_key, metadata_key in extracted_tags_mapping.items():
        try:
            ctx.metadata[metadata_key] = extracted[extracted_key]
            if isinstance(ctx.metadata[metadata_key], list):
                if len(ctx.metadata[metadata_key]):
                    ctx.metadata[metadata_key] = ctx.metadata[metadata_key][0]
                else:
                    ctx.metadata[metadata_key] = ""
        except KeyError:
            continue

    return ctx


def compute_md5(filepath: Path) -> str:
    """
    Compute a file md5sum.
    """
    with filepath.open("rb") as file:
        buffer = hashlib.md5()  # nosec
        while True:
            blob = file.read(8192)
            if not blob:
                break
            buffer.update(blob)

        return buffer.hexdigest()
