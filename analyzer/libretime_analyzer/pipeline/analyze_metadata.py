import logging
from datetime import timedelta
from pathlib import Path
from typing import Any, Dict

import mutagen
from libretime_shared.files import compute_md5

logger = logging.getLogger(__name__)


def analyze_metadata(filepath_: str, metadata: Dict[str, Any]):
    """
    Extract audio metadata from tags embedded in the file using mutagen.
    """
    filepath = Path(filepath_)

    # Airtime <= 2.5.x required fields
    metadata["ftype"] = "audioclip"
    metadata["hidden"] = False

    # Get file properties
    metadata["filesize"] = filepath.stat().st_size
    metadata["md5"] = compute_md5(filepath)

    # Get audio file metadata
    extracted = mutagen.File(filepath, easy=True)
    if extracted is None:
        logger.warning(f"no metadata were extracted for {filepath}")
        return metadata

    metadata["mime"] = extracted.mime[0]

    info = extracted.info
    if hasattr(info, "sample_rate"):
        metadata["sample_rate"] = info.sample_rate

    if hasattr(info, "bitrate"):
        metadata["bit_rate"] = info.bitrate

    if hasattr(info, "length"):
        metadata["length_seconds"] = info.length
        metadata["length"] = str(timedelta(seconds=info.length))

    try:
        # Special handling for the number of channels in mp3 files.
        # 0=stereo, 1=joint stereo, 2=dual channel, 3=mono
        if metadata["mime"] in ("audio/mpeg", "audio/mp3"):
            metadata["channels"] = 1 if info.mode == 3 else 2
        else:
            metadata["channels"] = info.channels
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
        metadata["track_number"] = track_number
        track_total = track_number_tokens[1]
        metadata["track_total"] = track_total
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
            metadata[metadata_key] = extracted[extracted_key]
            if isinstance(metadata[metadata_key], list):
                if len(metadata[metadata_key]):
                    metadata[metadata_key] = metadata[metadata_key][0]
                else:
                    metadata[metadata_key] = ""
        except KeyError:
            continue

    return metadata
