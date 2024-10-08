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

# fmt: off
FILES = [
#               filename                length  cuein   cueout  replaygain
# sample 1
# 0s  -> 8s: silence and pink noise fade in
# 8s  -> 9s: silence
# 9s  -> 12s: musik
# 12s -> 15s: pink noise fade out
Fixture(here / "s1-jointstereo.mp3",    15.0,   1.4,    15.0,   -5.9    ),
Fixture(here / "s1-mono.mp3",           15.0,   1.5,    15.0,   -2.0    ),
Fixture(here / "s1-stereo.mp3",         15.0,   1.4,    15.0,   -5.9    ),
Fixture(here / "s1-mono-12.mp3",        15.0,   1.2,    15.0,   +7.0    ),
Fixture(here / "s1-stereo-12.mp3",      15.0,   1.2,    15.0,   +6.1    ),
Fixture(here / "s1-mono+12.mp3",        15.0,   1.2,    15.0,   -17.0   ),
Fixture(here / "s1-stereo+12.mp3",      15.0,   1.2,    15.0,   -17.8   ),
Fixture(here / "s1-mono.flac",          15.0,   1.4,    15.0,   -2.3    ),
Fixture(here / "s1-stereo.flac",        15.0,   1.4,    15.0,   -6.0    ),
Fixture(here / "s1-mono-12.flac",       15.0,   2.0,    15.0,   +10.0   ),
Fixture(here / "s1-stereo-12.flac",     15.0,   1.8,    15.0,   +5.9    ),
Fixture(here / "s1-mono+12.flac",       15.0,   0.0,    15.0,   -12.0   ),
Fixture(here / "s1-stereo+12.flac",     15.0,   0.0,    15.0,   -14.9   ),
Fixture(here / "s1-mono.m4a",           15.0,   1.4,    15.0,   -4.5    ),
Fixture(here / "s1-stereo.m4a",         15.0,   1.4,    15.0,   -5.8    ),
Fixture(here / "s1-mono.ogg",           15.0,   1.4,    15.0,   -4.9    ),
Fixture(here / "s1-stereo.ogg",         15.0,   1.4,    15.0,   -5.7    ),
Fixture(here / "s1-stereo",             15.0,   1.4,    15.0,   -5.7    ),
Fixture(here / "s1-mono.wav",           15.0,   1.5,    15.0,   -2.3    ),
Fixture(here / "s1-stereo.wav",         15.0,   1.4,    15.0,   -6.0    ),
# sample 1 large (looped for 2 hours)
Fixture(here / "s1-large.flac",         7200,   1.4,    7200,   -6.0    ),
# sample 2
# 0s   -> 1.8s: silence
# 1.8s        : noise
# 1.8s -> 3.86s: silence
Fixture(here / "s2-jointstereo.mp3",    3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-mono.mp3",           3.86,   0.0,    3.86,   8.6     ),
Fixture(here / "s2-stereo.mp3",         3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-mono.flac",          3.86,   0.0,    3.86,   8.2     ),
Fixture(here / "s2-stereo.flac",        3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-mono.m4a",           3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-stereo.m4a",         3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-mono.ogg",           3.86,   0.0,    3.86,   5.6     ),
Fixture(here / "s2-stereo.ogg",         3.86,   0.0,    3.86,   5.6     ),
# sample 3
# 0s    ->  1s: silence
# 1s    ->  3s: noise
# 3s    ->  5s: silence
# 5s    ->  7s: noise
# 7s    ->  9s: silence
# 9s    ->  11s: noise
Fixture(here / "s3-stereo.mp3",         11.0,   1.0,    11.0,   1.0     ),
Fixture(here / "s3-stereo.flac",        11.0,   1.0,    11.0,   1.0     ),
Fixture(here / "s3-stereo.m4a",         11.0,   1.0,    11.0,   1.0     ),
Fixture(here / "s3-stereo.ogg",         11.0,   1.0,    11.0,   1.0     ),
]
# fmt: on

FixtureMeta = namedtuple(
    "FixtureMeta",
    ["path", "metadata"],
)

meta = {
    "sample_rate": 48000,
    "length": str(timedelta(seconds=15)),
    "length_seconds": approx(15.0, abs=0.1),
    "ftype": "audioclip",
    "hidden": False,
}

tags = {
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
            **tags,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.mp3",
        {
            **meta,
            **tags,
            "bit_rate": approx(64000, abs=1e2),
            "channels": 1,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.mp3",
        {
            **meta,
            **tags,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.flac",
        {
            **meta,
            **tags,
            "bit_rate": approx(452802, abs=1e2),
            "channels": 1,
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.flac",
        {
            **meta,
            **tags,
            "bit_rate": approx(938593, abs=1e3),
            "channels": 2,
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.m4a",
        {
            **meta,
            **tags,
            "bit_rate": approx(65000, abs=5e4),
            "channels": 2,  # Weird
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.m4a",
        {
            **meta,
            **tags,
            "bit_rate": approx(128000, abs=1e5),
            "channels": 2,
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.ogg",
        {
            **meta,
            **tags,
            "bit_rate": approx(80000, abs=1e2),
            "channels": 1,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.ogg",
        {
            **meta,
            **tags,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged",
        {
            **meta,
            **tags,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged.wav",
        {
            **meta,
            "bit_rate": approx(768000, abs=1e2),
            "channels": 1,
            "mime": "audio/wav",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged.wav",
        {
            **meta,
            "bit_rate": approx(1536000, abs=1e2),
            "channels": 2,
            "mime": "audio/wav",
        },
    ),
]

tags = {
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
            **tags,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.mp3",
        {
            **meta,
            **tags,
            "bit_rate": approx(64000, abs=1e2),
            "channels": 1,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.mp3",
        {
            **meta,
            **tags,
            "bit_rate": approx(128000, abs=1e2),
            "channels": 2,
            "mime": "audio/mp3",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.flac",
        {
            **meta,
            **tags,
            "bit_rate": approx(452802, abs=1e2),
            "channels": 1,
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.flac",
        {
            **meta,
            **tags,
            "bit_rate": approx(938593, abs=1e2),
            "channels": 2,
            "mime": "audio/flac",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.m4a",
        {
            **meta,
            **tags,
            "bit_rate": approx(65000, abs=5e4),
            "channels": 2,  # Weird
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.m4a",
        {
            **meta,
            **tags,
            "bit_rate": approx(128000, abs=1e5),
            "channels": 2,
            "mime": "audio/mp4",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.ogg",
        {
            **meta,
            **tags,
            "bit_rate": approx(80000, abs=1e2),
            "channels": 1,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.ogg",
        {
            **meta,
            **tags,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8",
        {
            **meta,
            **tags,
            "bit_rate": approx(112000, abs=1e2),
            "channels": 2,
            "mime": "audio/vorbis",
        },
    ),
    FixtureMeta(
        here / "s1-mono-tagged-utf8.wav",
        {
            **meta,
            "bit_rate": approx(768000, abs=1e2),
            "channels": 1,
            "mime": "audio/wav",
        },
    ),
    FixtureMeta(
        here / "s1-stereo-tagged-utf8.wav",
        {
            **meta,
            "bit_rate": approx(1536000, abs=1e2),
            "channels": 2,
            "mime": "audio/wav",
        },
    ),
]
