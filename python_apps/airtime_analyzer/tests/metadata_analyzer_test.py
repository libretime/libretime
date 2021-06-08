import datetime

import mock
import mutagen
import pytest
from airtime_analyzer.metadata_analyzer import MetadataAnalyzer


@pytest.mark.parametrize(
    "params,exception",
    [
        ((42, dict()), TypeError),
        (("foo", 3), TypeError),
    ],
)
def test_analyze_wrong_params(params, exception):
    with pytest.raises(exception):
        MetadataAnalyzer.analyze(*params)


def default_metadata(metadata):
    return {
        "album_title": "Test Album",
        "artist_name": "Test Artist",
        "cuein": 0.0,
        "cueout": "0:00:03.839410",
        "ftype": "audioclip",
        "genre": "Test Genre",
        "hidden": False,
        "length_seconds": metadata["length_seconds"],
        "length": str(datetime.timedelta(seconds=metadata["length_seconds"])),
        "sample_rate": 44100,
        "track_number": "1",
        "track_title": "Test Title",
        "year": "1999",
    }


@pytest.mark.parametrize(
    "filepath,expected",
    [
        (
            "tests/test_data/44100Hz-16bit-mono.mp3",
            {
                "bit_rate": 63998,
                "channels": 1,
                "filesize": 32298,
                "md5": "a93c9503c85cd2fbe7658711a08c24b1",
                "mime": "audio/mp3",
                "track_total": "10",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-dualmono.mp3",
            {
                "bit_rate": 127998,
                "channels": 2,
                "filesize": 63436,
                "md5": "aee8bf340b484f921bca99390962f0d5",
                "mime": "audio/mp3",
                "track_total": "10",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-stereo.mp3",
            {
                "bit_rate": 127998,
                "channels": 2,
                "filesize": 63436,
                "md5": "063b20072f71a18b9d4f14434286fdc5",
                "mime": "audio/mp3",
                "track_total": "10",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-stereo-utf8.mp3",
            {
                "album_title": "Ä ä Ü ü ß",
                "artist_name": "てすと",
                "bit_rate": 127998,
                "channels": 2,
                "filesize": 63436,
                "genre": "Я Б Г Д Ж Й",
                "md5": "0bb41e7f65db3f31cf449de18f713fca",
                "mime": "audio/mp3",
                "track_title": "ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃ",
                "track_total": "10",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-simplestereo.mp3",
            {
                "bit_rate": 127998,
                "channels": 2,
                "filesize": 63436,
                "md5": "2330d4429bec7b35fa40185319069267",
                "mime": "audio/mp3",
                "track_total": "10",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-jointstereo.mp3",
            {
                "bit_rate": 127998,
                "channels": 2,
                "filesize": 63436,
                "md5": "063b20072f71a18b9d4f14434286fdc5",
                "mime": "audio/mp3",
                "track_total": "10",
            },
        ),
        # (
        #     "tests/test_data/44100Hz-16bit-mp3-missingid3header.mp3",
        #     {
        #         "bit_rate": 63998,
        #         "channels": 1,
        #         "filesize": 32298,
        #         "md5": "a93c9503c85cd2fbe7658711a08c24b1",
        #         "mime": "audio/mp3",
        #         "track_total": "10",
        #     },
        # ),
        (
            "tests/test_data/44100Hz-16bit-mono.ogg",
            {
                "bit_rate": 80000,
                "channels": 1,
                "filesize": 36326,
                "md5": "699e091994f3b69a77ed074951520e18",
                "mime": "audio/vorbis",
                "track_total": "10",
                "comment": "Test Comment",
            },
        ),
        (
            "tests/test_data/44100Hz-16bit-stereo.ogg",
            {
                "bit_rate": 112000,
                "channels": 2,
                "filesize": 41081,
                "md5": "185a3b9cd1bd2db4d168ff9c2c86046e",
                "mime": "audio/vorbis",
                "track_total": "10",
                "comment": "Test Comment",
            },
        ),
        # (
        #     "tests/test_data/44100Hz-16bit-stereo-invalid.wma",
        #     {
        #         "bit_rate": 63998,
        #         "channels": 1,
        #         "filesize": 32298,
        #         "md5": "a93c9503c85cd2fbe7658711a08c24b1",
        #         "mime": "audio/mp3",
        #         "track_total": "10",
        #     },
        # ),
        (
            "tests/test_data/44100Hz-16bit-stereo.m4a",
            {
                "bit_rate": 102619,
                "channels": 2,
                "cueout": "0:00:03.862630",
                "filesize": 51972,
                "md5": "c2c822e0cd6c03f3f6bd7158a6ed8c56",
                "mime": "audio/mp4",
                # "track_total": "10",
                "comment": "Test Comment",
            },
        ),
        # (
        #     "tests/test_data/44100Hz-16bit-stereo.wav",
        #     {
        #         "bit_rate": 112000,
        #         "channels": 2,
        #         "filesize": 677316,
        #         "md5": "6bd5df4f161375e4634cbd4968fb5c23",
        #         "mime": "audio/x-wav",
        #         "track_total": "10",
        #         "comment": "Test Comment",
        #     },
        # ),
    ],
)
def test_analyze(filepath, expected):
    metadata = MetadataAnalyzer.analyze(filepath, dict())
    assert abs(metadata["length_seconds"] - 3.9) < 0.1
    assert metadata == {**default_metadata(metadata), **expected}


def test_invalid_wma():
    metadata = MetadataAnalyzer.analyze(
        "tests/test_data/44100Hz-16bit-stereo-invalid.wma", dict()
    )
    assert metadata["mime"] == "audio/x-ms-wma"


def test_wav_stereo():
    metadata = MetadataAnalyzer.analyze(
        "tests/test_data/44100Hz-16bit-stereo.wav", dict()
    )
    assert metadata == {
        "sample_rate": 44100,
        "channels": 2,
        "filesize": 677316,
        "md5": "6bd5df4f161375e4634cbd4968fb5c23",
        "mime": "audio/x-wav",
        "cueout": "0:00:03.839410",
        "ftype": "audioclip",
        "hidden": False,
        "length": "0:00:03.839410",
        "length_seconds": 3.8394104308390022,
    }


def test_mp3_bad_channels():
    """
    Test an mp3 file where the number of channels is invalid or missing.
    """
    # It'd be a pain in the ass to construct a real MP3 with an invalid number
    # of channels by hand because that value is stored in every MP3 frame in the file
    filename = "tests/test_data/44100Hz-16bit-mono.mp3"
    audio_file = mutagen.File(filename, easy=True)
    audio_file.info.mode = 1777
    with mock.patch("airtime_analyzer.metadata_analyzer.mutagen") as mock_mutagen:
        mock_mutagen.File.return_value = audio_file

    metadata = MetadataAnalyzer.analyze(filename, dict())
    assert metadata == {
        **default_metadata(metadata),
        "bit_rate": 63998,
        "channels": 1,
        "filesize": 32298,
        "md5": "a93c9503c85cd2fbe7658711a08c24b1",
        "mime": "audio/mp3",
        "track_total": "10",
    }


def test_unparsable_file():
    metadata = MetadataAnalyzer.analyze("tests/test_data/unparsable.txt", dict())
    assert metadata == {
        "filesize": 10,
        "ftype": "audioclip",
        "hidden": False,
        "md5": "4d5e4b1c8e8febbd31fa9ce7f088beae",
        "mime": "text/plain",
    }
