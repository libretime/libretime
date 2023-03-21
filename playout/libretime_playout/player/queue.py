import logging
from collections import deque
from datetime import datetime
from queue import Empty, Queue
from threading import Thread
from typing import Any, Dict

from ..utils import seconds_between
from .events import AnyEvent, event_key_to_datetime
from .liquidsoap import PypoLiquidsoap

logger = logging.getLogger(__name__)


class PypoLiqQueue(Thread):
    name = "liquidsoap_queue"
    daemon = True

    def __init__(
        self,
        future_queue: "Queue[Dict[str, Any]]",
        pypo_liquidsoap: PypoLiquidsoap,
    ):
        Thread.__init__(self)
        self.queue = future_queue
        self.pypo_liquidsoap = pypo_liquidsoap

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
                self.pypo_liquidsoap.play(media_item)
                if len(schedule_deque):
                    time_until_next_play = seconds_between(
                        datetime.utcnow(),
                        event_key_to_datetime(schedule_deque[0]["start"]),
                    )
                else:
                    time_until_next_play = None
            else:
                logger.info("New schedule received")

                # new schedule received. Replace old one with this.
                schedule_deque.clear()

                keys = sorted(media_schedule.keys())
                for i in keys:
                    schedule_deque.append(media_schedule[i])

                if len(keys):
                    time_until_next_play = seconds_between(
                        datetime.utcnow(),
                        media_schedule[keys[0]]["start"],
                    )

                else:
                    time_until_next_play = None

    def run(self) -> None:
        try:
            self.main()
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)
