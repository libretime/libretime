"""
Regression tests for the duplicate-push race behind dead air in #2471.

verify_correct_present_media reconciles the Liquidsoap queue tracker against
the items scheduled right now. It used to skip tracker entries whose
wall-clock end time had passed, which made an item that crossed its end
between payload capture and reconciliation invisible: still in the schedule,
absent from the tracker scan → "Need to add items to Liquidsoap *now*" → the
same schedule row handed over a SECOND time:

    19:00:00 Prepared ".../483.mp3" (RID 0)
    19:00:30 Prepared ".../483.mp3" (RID 1)   ← duplicate request

Duplicate requests double the blast radius of any later cache miss and make
the schedule play items twice ("the first item played again but in reality
neither was output"). Fix: an item that was handed to a queue stays known to
the reconciler — regardless of wall-clock end — until its row leaves the
schedule, at which point it is removed from the queue exactly once.
"""

from datetime import datetime, timedelta
from unittest.mock import MagicMock

from libretime_playout.player.events import EventKind, FileEvent
from libretime_playout.player.liquidsoap import Liquidsoap


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


def liquidsoap_holding_ended_item() -> tuple[Liquidsoap, FileEvent]:
    now = datetime.utcnow()
    # row 259048 / file 483: the 30-second station ID from the incident —
    # ended microseconds ago but still present in the schedule
    wedged = make_file_event(259048, 483, now - timedelta(seconds=31), 30.0)
    liq = Liquidsoap(MagicMock())
    liq.liq_queue_tracker[0] = wedged
    return liq, wedged


def test_no_duplicate_push_for_ended_scheduled_item():
    """An item already handed to Liquidsoap must never be pushed a second
    time even when the wall clock says it ended while it is still in the
    schedule — duplicate RIDs are what made the cache-delete race fatal in
    #2471."""
    liq, wedged = liquidsoap_holding_ended_item()
    fresh_copy = wedged.model_copy()
    fresh_copy.file_ready = True  # skip the not-ready wait path

    liq.verify_correct_present_media([fresh_copy])

    assert (
        not liq.liq_client.queue_push.called
    ), "item still tracked in a queue must not be handed to Liquidsoap again"
    assert not liq.liq_client.queues_remove.called


def test_ended_item_leaving_schedule_is_reconciled_out():
    """Stale tracked entries (including ended ones — the wedged-queue
    residents from #2471) are queues_remove'd once, exactly when their row
    leaves the schedule, instead of rotting unseen in the queue."""
    liq, _ = liquidsoap_holding_ended_item()

    liq.verify_correct_present_media([])  # row no longer in the schedule

    liq.liq_client.queues_remove.assert_called_once_with(0)
    assert liq.liq_queue_tracker[0] is None

    # idempotent: a second reconcile sees nothing left to remove
    liq.verify_correct_present_media([])
    liq.liq_client.queues_remove.assert_called_once_with(0)
