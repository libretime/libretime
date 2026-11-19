"""
Regression tests for the cache-cleanup race behind dead air in #2471.

Incident (2023-03-20 19:00:35 UTC, station logs attached to the issue):
Liquidsoap holds asynchronous requests for cache files. When an item left
the (narrow) update_schedule payload — even seconds after its request was
accepted, or *before* its request played — cache_cleanup deleted the file:

    19:00:30 [decoder] accepted ".../5531.mp3"      (request accepted)
    19:00:46 [pypo]    File '.../5531.mp3' removed  (no payload reference)
    19:01:00 [queue]   Failed to prepare track: request not ready.  → SILENCE

The `lsof` guard cannot cover the window between "request accepted" and
"decoder opened the file". Fix: files cached within a grace period are
never deleted, whatever the latest schedule payload says. Stale files must
still be cleaned (no disk leak).
"""

import os
from datetime import datetime, timedelta
from pathlib import Path
from queue import Queue
from unittest.mock import MagicMock

from libretime_playout.config import Config
from libretime_playout.player.events import EventKind, FileEvent
from libretime_playout.player.fetch import PypoFetch


def make_file_event(
    row_id: int, file_id: int, start: datetime, length: float
) -> FileEvent:
    return FileEvent(
        type=EventKind.FILE,
        row_id=row_id,
        id=file_id,
        show_name="trasitionally",
        start=start,
        end=start + timedelta(seconds=length),
        fade_in=0.0,
        fade_out=0.0,
        cue_in=0.0,
        cue_out=length,
        mime="audio/mp3",
        replay_gain=0.5,
        filesize=499078,
    )


def fetch_with_cache(tmp_path: Path) -> PypoFetch:
    fetch = PypoFetch(
        Queue(),
        Queue(),
        Queue(),
        MagicMock(),
        MagicMock(),
        Config(
            **{
                "general": {
                    "public_url": "http://localhost:8080",
                    "api_key": "k",
                    "secret_key": "s",
                },
                "stream": {"outputs": {}},
            }
        ),
        MagicMock(),
        MagicMock(),
    )
    fetch.cache_dir = tmp_path
    return fetch


def test_cleanup_keeps_files_within_grace_window(tmp_path):
    """Files cached moments ago must survive cleanup even when no schedule
    payload references them — Liquidsoap may hold outstanding requests for
    them (483.mp3 / 5531.mp3 at 19:00:35 / 19:00:46 in the incident)."""
    fetch = fetch_with_cache(tmp_path)
    outstanding_1 = tmp_path / "483.mp3"
    outstanding_2 = tmp_path / "5531.mp3"
    current = tmp_path / "6001.mp3"
    for f in (outstanding_1, outstanding_2, current):
        f.write_bytes(b"\xff" * 16)  # mtime = now == freshly cached

    # the lsof guard reports "not open" — the race window (request accepted,
    # decoder not yet opened), exactly as in the incident
    fetch.is_file_opened = lambda p: False

    now = datetime.utcnow()
    events = {
        # payload window only contains the current 57-minute program item
        "k1": make_file_event(259035, 6001, now - timedelta(seconds=1), 3456.0),
    }
    fetch.cache_cleanup(events)

    assert outstanding_1.exists(), (
        "file cached within the grace period must survive cleanup: "
        "Liquidsoap may hold a request for it (#2471 wedge)"
    )
    assert outstanding_2.exists()
    assert current.exists()


def test_cleanup_still_deletes_stale_files(tmp_path):
    """Counter-weight: the grace window must not break normal cleanup —
    files cached longer ago than the grace period are still removable."""
    fetch = fetch_with_cache(tmp_path)
    stale = tmp_path / "999.mp3"
    stale.write_bytes(b"\xff" * 16)
    old = (datetime.utcnow() - timedelta(hours=2)).timestamp()
    os.utime(stale, (old, old))

    fetch.is_file_opened = lambda p: False

    now = datetime.utcnow()
    events = {"k1": make_file_event(259035, 6001, now, 3456.0)}
    fetch.cache_cleanup(events)

    assert stale.exists() is False, "stale cache files must still be cleaned up"
