from collections import namedtuple
from datetime import timedelta
from pathlib import Path

from pytest import approx

here = Path(__file__).parent
fixtures_path = here

FILE_INVALID_DRM = here / "invalid.wma"
FILE_INVALID_TXT = here / "invalid.txt"

Fixture = namedtuple(
    "Fixture",
    ["path", "length", "cuein", "cueout", "replaygain"],
)

# length,cuein,cueout
s1 = [10.0, 2.3, 10.0]
s2 = [3.9, 0.0, 3.9]

FILES = [
    # Sample 1 MP3
    Fixture(here / "s1-jointstereo.mp3", *s1, -1.6),
    Fixture(here / "s1-mono.mp3", *s1, -0.7),
    Fixture(here / "s1-stereo.mp3", *s1, -1.6),
    # Sample 1 MP3 -12dB
    Fixture(here / "s1-mono-12.mp3", *s1, +8.3),
    Fixture(here / "s1-stereo-12.mp3", *s1, +10.0),
    # Sample 1 MP3 +12dB
    Fixture(here / "s1-mono+12.mp3", *s1, -13.6),
    Fixture(here / "s1-stereo+12.mp3", *s1, -12.0),
    # Sample 1 FLAC
    Fixture(here / "s1-mono.flac", *s1, -1.6),
    Fixture(here / "s1-stereo.flac", *s1, -2.3),
    # Sample 1 FLAC -12dB
    Fixture(here / "s1-mono-12.flac", *s1, +10.0),
    Fixture(here / "s1-stereo-12.flac", *s1, +9.3),
    # Sample 1 FLAC +12dB
    Fixture(here / "s1-mono+12.flac", *s1, -12.0),
    Fixture(here / "s1-stereo+12.flac", *s1, -12.0),
    # Sample 1 AAC
    Fixture(here / "s1-mono.m4a", *s1, -4.5),
    Fixture(here / "s1-stereo.m4a", *s1, -2.9),
    # Sample 1 Vorbis
    Fixture(here / "s1-mono.ogg", *s1, -4.3),
    Fixture(here / "s1-stereo.ogg", *s1, -2.3),
    # Sample 2 MP3
    Fixture(here / "s2-jointstereo.mp3", *s2, 6.1),
    Fixture(here / "s2-mono.mp3", *s2, 6.1),
    Fixture(here / "s2-stereo.mp3", *s2, 6.1),
    # Sample 2 FLAC
    Fixture(here / "s2-mono.flac", *s2, 5.2),
    Fixture(here / "s2-stereo.flac", *s2, 5.2),
    # Sample 2 AAC
    Fixture(here / "s2-mono.m4a", *s2, 2.6),
    Fixture(here / "s2-stereo.m4a", *s2, 6.1),
    # Sample 2 Vorbis
    Fixture(here / "s2-mono.ogg", *s2, 2.3),
    Fixture(here / "s2-stereo.ogg", *s2, 5.2),
]

FixtureMeta = namedtuple(
    "FixtureMeta",
    ["path", "metadata"],
)

meta = {
    "cuein": 0.0,
    "sample_rate": 48000,
    "length": str(timedelta(seconds=10)),
    "length_seconds": approx(10.0, abs=0.1),
    "ftype": "audioclip",
    "hidden": False,
    # Tags
    "album_title": "Test Album",
    "artist_name": "Test Artist",
    "track_title": "Test Title",
    "track_number": "1",
    "track_total": "10",
    "year": "1999",
    "genre": "Test Genre",
    "comment": "Test Comment",
}

FILES_TAGGED = [
    FixtureMeta(
        here / "s1-jointstereo-tagged.mp3",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "filesize": approx(161094, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.mp3",
        {
            **meta,
            "bit_rate": approx(64000, abs=1e2),
            "channels": 1,
            "filesize": approx(80646, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.mp3",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "filesize": approx(161094, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.flac",
        {
            **meta,
            "bit_rate": approx(454468, abs=1e2),
            "channels": 1,
            "filesize": approx(576516, abs=1e2),
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.flac",
        {
            **meta,
            "bit_rate": approx(687113, abs=1e2),
            "channels": 2,
            "filesize": approx(867323, abs=1e2),
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.m4a",
        {
            **meta,
            "bit_rate": approx(65000, abs=5e4),
            "channels": 2,  # Weird
            "filesize": approx(80000, abs=1e5),
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.m4a",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e5),
            "channels": 2,
            "filesize": approx(150000, abs=1e5),
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.ogg",
        {
            **meta,
            "bit_rate": approx(80000, abs=1e2),
            "channels": 1,
            "filesize": approx(81340, abs=1e2),
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.ogg",
        {
            **meta,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "filesize": approx(104036, abs=1e2),
            "mime": "audio/vorbis",
        },
    ),
]

meta = {
    **meta,
    "album_title": "Ä ä Ü ü ß",
    "artist_name": "てすと",
    "track_title": "ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃ",
    "track_number": "1",
    "track_total": "10",
    "year": "1999",
    "genre": "Я Б Г Д Ж Й",
    "comment": "Ł Ą Ż Ę Ć Ń Ś Ź",
}

FILES_TAGGED += [
    FixtureMeta(
        here / "s1-jointstereo-tagged-utf8.mp3",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "filesize": approx(161161, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.mp3",
        {
            **meta,
            "bit_rate": approx(64000, abs=1e2),
            "channels": 1,
            "filesize": approx(80713, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.mp3",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "filesize": approx(161161, abs=1e2),
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.flac",
        {
            **meta,
            "bit_rate": approx(454468, abs=1e2),
            "channels": 1,
            "filesize": approx(576583, abs=1e2),
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.flac",
        {
            **meta,
            "bit_rate": approx(687113, abs=1e2),
            "channels": 2,
            "filesize": approx(867390, abs=1e2),
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.m4a",
        {
            **meta,
            "bit_rate": approx(65000, abs=5e4),
            "channels": 2,  # Weird
            "filesize": approx(80000, abs=1e5),
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.m4a",
        {
            **meta,
            "bit_rate": approx(128000, abs=1e5),
            "channels": 2,
            "filesize": approx(150000, abs=1e5),
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.ogg",
        {
            **meta,
            "bit_rate": approx(80000, abs=1e2),
            "channels": 1,
            "filesize": approx(81408, abs=1e2),
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.ogg",
        {
            **meta,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "filesize": approx(104104, abs=1e2),
            "mime": "audio/vorbis",
        },
    ),
]
