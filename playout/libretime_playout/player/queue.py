import logging
from collections import deque
from datetime import datetime
from queue import Empty, Queue
from threading import Thread
from time import sleep
from typing import Any, Dict

from ..utils import seconds_between
from .events import AnyEvent
from .liquidsoap import Liquidsoap

logger = logging.getLogger(__name__)


class PypoLiqQueue(Thread):
    name = "liquidsoap_queue"
    daemon = True

    def __init__(
        self,
        future_queue: "Queue[Dict[str, Any]]",
        liquidsoap: Liquidsoap,
    ):
        Thread.__init__(self)
        self.queue = future_queue
        self.liquidsoap = liquidsoap

    def main(self) -> None:
        time_until_next_play = None
        schedule_deque: deque[AnyEvent] = deque()
        media_schedule = None

        while True:
            try:
                if time_until_next_play is None:
                    logger.info("waiting indefinitely for schedule")
                    media_schedule = self.queue.get(block=True)
                else:
                    logger.info(
                        "waiting %ss until next scheduled item", time_until_next_play
                    )
                    media_schedule = self.queue.get(
                        block=True, timeout=time_until_next_play
                    )
            except Empty:
                # Time to push a scheduled item.
                media_item = schedule_deque.popleft()
                self.liquidsoap.play(media_item)
                if len(schedule_deque):
                    time_until_next_play = seconds_between(
                        datetime.utcnow(),
                        schedule_deque[0].start,
                    )
                else:
                    time_until_next_play = None
            else:
                logger.info("New schedule received")

                # Preserve pending future ActionEvents (e.g. kick_out) that are
                # not represented in the incoming schedule. A pure-live show has
                # no cc_schedule rows so its kick_out is never rebuilt on refresh.
                now = datetime.utcnow()
                from .events import ActionEvent

                new_action_starts = {
                    item.start
                    for item in media_schedule.values()
                    if isinstance(item, ActionEvent)
                }
                preserved = [
                    item
                    for item in schedule_deque
                    if isinstance(item, ActionEvent)
                    and item.start > now
                    and item.start not in new_action_starts
                ]

                schedule_deque.clear()
                keys = sorted(media_schedule.keys())
                for i in keys:
                    schedule_deque.append(media_schedule[i])

                if preserved:
                    logger.info(
                        "Preserving %d pending action event(s) across schedule refresh",
                        len(preserved),
                    )
                    schedule_deque.extend(preserved)
                    sorted_items = sorted(schedule_deque, key=lambda x: x.start)
                    schedule_deque.clear()
                    schedule_deque.extend(sorted_items)

                if len(schedule_deque):
                    time_until_next_play = seconds_between(
                        datetime.utcnow(),
                        schedule_deque[0].start,
                    )
                else:
                    time_until_next_play = None

    def run(self) -> None:
        while True:
            try:
                self.main()
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(exception)
                sleep(1)
