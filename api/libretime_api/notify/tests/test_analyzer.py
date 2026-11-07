"""
Proof that the analyzer notifications are the ones the legacy service sent.
"""

import pytest

from .. import analyzer
from .conftest import legacy_capture

FIXTURE = legacy_capture("airtime-uploads")

# The arguments the legacy publisher of tools/rabbitmq-equivalence sends.
TMP_FILE_PATH = "/tmp/libretime-equivalence.mp3"
IMPORT_DIRECTORY = "/path/to/storage/imported/"
ORIGINAL_FILENAME = "Some Song.mp3"
FILE_ID = 42

CASES = {
    "analyze_file_cue_points_true": lambda: analyzer.send_message_to_analyzer(
        TMP_FILE_PATH,
        IMPORT_DIRECTORY,
        ORIGINAL_FILENAME,
        FILE_ID,
        analyze_cue_points=True,
    ),
    "analyze_file_cue_points_false": lambda: analyzer.send_message_to_analyzer(
        TMP_FILE_PATH,
        IMPORT_DIRECTORY,
        ORIGINAL_FILENAME,
        FILE_ID,
        analyze_cue_points=False,
    ),
    "analyze_file_without_track_type": lambda: analyzer.send_message_to_analyzer(
        TMP_FILE_PATH, IMPORT_DIRECTORY, ORIGINAL_FILENAME, FILE_ID
    ),
}


def test_every_captured_case_is_covered():
    assert set(CASES) == set(FIXTURE["messages"])


@pytest.mark.parametrize("case", sorted(CASES))
def test_body_is_the_one_the_legacy_service_published(case, capture):
    CASES[case]()

    assert capture.single("airtime-uploads").payload == FIXTURE["messages"][case]


@pytest.mark.parametrize("case", sorted(CASES))
def test_message_reaches_the_analyzer_like_the_legacy_one(case, capture):
    CASES[case]()

    message = capture.single("airtime-uploads")

    assert message.delivery_info["exchange"] == FIXTURE["exchange"]
    assert message.delivery_info["routing_key"] == FIXTURE["routing_key"]
    assert message.content_type == FIXTURE["properties"]["content_type"]
