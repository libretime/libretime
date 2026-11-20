"""
Regression tests for late-item cue handling (modify_cue_point).

History: modify_cue_point shifted cue_in forward by the lateness with no
upper bound. An item late by more than its own length got a cue_in past
end-of-file; Liquidsoap accepted the request and finished it instantly
("End of track before cue-out point"), which in issue #699 led to a
44-minute episode being accepted and replayed back-to-back three times
while the queue chased a wall-clock it could not catch.

Semantics pinned here (resync, not stretch):
  * a late item re-enters the file at cue_in + lateness,
  * cue_out stays at the scheduled end so the item ENDS on time —
    preserving duration instead would delay every following item and is
    literally how schedule creep is created,
  * if the lateness consumed the entire cue range the item is dropped:
    nothing is ever pushed with cue_in >= cue_out.
"""

from datetime import datetime, timedelta
from unittest.mock import MagicMock

import pytest

from libretime_playout.player.events import EventKind, FileEvent
from libretime_playout.player.liquidsoap import Liquidsoap


def make_late_file_event(
    row_id: int = 131272,
    file_id: int = 55895,
    late_by: float = 61 * 60,
    cue_in: float = 0.0,
    cue_out: float = 2671.019,
    end_pad: float = 0.0,
) -> FileEvent:
    """The #699 Jean Shepherd case by default: a 44:31 episode that should
    have started 61 minutes ago (cue range fully in the past)."""
    now = datetime.utcnow()
    start = now - timedelta(seconds=late_by)
    return FileEvent(
        type=EventKind.FILE,
        row_id=row_id,
        id=file_id,
        show_name="Old Time Radio",
        start=start,
        # end may legitimately sit past start+cue_out (grid padding/fades);
        # the drop decision must be made on the cue range, not on end
        end=start + timedelta(seconds=(cue_out - cue_in) + end_pad),
        fade_in=0.0,
        fade_out=0.0,
        cue_in=cue_in,
        cue_out=cue_out,
        mime="audio/mp3",
        replay_gain=3.0,
        filesize=10684622,
        file_ready=True,
    )


def test_late_reentry_shifts_cue_in_and_keeps_scheduled_end():
    """A late-but-still-usable item jumps cue_in forward and preserves
    cue_out, so playback ends on the scheduled grid time."""
    liq = Liquidsoap(MagicMock())
    item = make_late_file_event(late_by=30, cue_in=0.5, cue_out=100.0)

    assert liq.modify_cue_point(item) is True

    assert item.cue_in == pytest.approx(30.5, abs=0.5)
    assert item.cue_out == 100.0, "scheduled end must not move (no creep)"


def test_on_time_or_early_item_is_untouched():
    liq = Liquidsoap(MagicMock())
    item = make_late_file_event(late_by=-10, cue_in=0.888, cue_out=30.911)

    assert liq.modify_cue_point(item) is True
    assert item.cue_in == 0.888
    assert item.cue_out == 30.911


def test_lateness_beyond_cue_range_drops_item():
    """#699 case: 44:31 episode 61 minutes late — cue_in would land past EOF.
    The item must be reported as not-playable instead."""
    liq = Liquidsoap(MagicMock())
    item = make_late_file_event()  # 61 min late, 44.5 min cue range

    assert liq.modify_cue_point(item) is False

    # event left unmutated so a dropped item cannot carry a bogus cue range
    assert item.cue_in == 0.0
    assert item.cue_in < item.cue_out


def test_no_push_when_late_item_dropped_by_reconcile():
    """End-to-end through verify_correct_present_media: a schedule row that
    is fully past its cue range must NOT be handed to Liquidsoap (that push
    is what queued the replay storm)."""
    liq = Liquidsoap(MagicMock())
    # end_pad keeps the event 'present' (end still in the future) even though
    # the playable cue range is entirely in the past — the state #699 reached
    # via its timezone confusion
    item = make_late_file_event(end_pad=1200)

    liq.verify_correct_present_media([item])

    assert (
        not liq.liq_client.queue_push.called
    ), "item with no remaining cue range must never be pushed"
