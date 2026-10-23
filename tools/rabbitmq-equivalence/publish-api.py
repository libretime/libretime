#!/usr/bin/env python3
"""
Publish the same messages as publish-legacy.php, from the API.

Run it with the RabbitMQ host of the platform you captured the legacy messages
on, then compare the two captures with compare.py:

    python publish-api.py
"""

import os
import sys

os.environ.setdefault("DJANGO_SETTINGS_MODULE", "libretime_api.settings.testing")

import django  # noqa: E402

django.setup()

from libretime_api import notify  # noqa: E402

STATION_NAME = "Radio Zürich — \"quote\" \\ <>& / \U0001f600"


def cases() -> dict:
    return {
        # Playout notifications, one per call site of the legacy service.
        "update_schedule": lambda: notify.send_message_to_pypo("update_schedule", {}),
        "reset_liquidsoap_bootstrap": lambda: notify.send_message_to_pypo(
            "reset_liquidsoap_bootstrap", {}
        ),
        "switch_source_on": lambda: notify.switch_source("live_dj", "on"),
        "switch_source_off": lambda: notify.switch_source("master_dj", "off"),
        "disconnect_source": lambda: notify.disconnect_source("live_dj"),
        "update_station_name": lambda: notify.update_station_name(STATION_NAME),
        "update_transition_fade": lambda: notify.update_transition_fade("1.500"),
        "update_stream_format": lambda: notify.update_stream_format("1"),
        "update_message_offline": lambda: notify.update_message_offline("LibreTime - offline"),
        # Analyzer notifications, one per track type option.
        "analyze_file_cue_points_true": lambda: notify.send_message_to_analyzer(
            "/tmp/libretime-equivalence.mp3",
            "/path/to/storage/imported/",
            "Some Song.mp3",
            42,
            analyze_cue_points=True,
        ),
        "analyze_file_cue_points_false": lambda: notify.send_message_to_analyzer(
            "/tmp/libretime-equivalence.mp3",
            "/path/to/storage/imported/",
            "Some Song.mp3",
            42,
            analyze_cue_points=False,
        ),
        "analyze_file_without_track_type": lambda: notify.send_message_to_analyzer(
            "/tmp/libretime-equivalence.mp3",
            "/path/to/storage/imported/",
            "Some Song.mp3",
            42,
        ),
    }


def main() -> None:
    only = sys.argv[1] if len(sys.argv) > 1 else ""

    for name, publish in cases().items():
        if only and only != name:
            continue

        try:
            publish()
            print(f"OK   {name}")
        except Exception as exception:  # pylint: disable=broad-exception-caught
            print(f"FAIL {name} {type(exception).__name__}: {exception}")


if __name__ == "__main__":
    main()
