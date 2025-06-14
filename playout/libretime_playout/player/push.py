import logging
import math
import time
from datetime import datetime
from queue import Queue
from threading import Thread
from typing import List, Tuple

from ..config import PUSH_INTERVAL, Config
from .events import AnyEvent, Events, FileEvent
from .liquidsoap import Liquidsoap
from .queue import PypoLiqQueue

logger = logging.getLogger(__name__)


class PypoPush(Thread):
    name = "push"
    daemon = True

    def __init__(
        self,
        push_queue: "Queue[Events]",
        liquidsoap: Liquidsoap,
        config: Config,
    ):
        Thread.__init__(self)
        self.queue = push_queue

        self.config = config

        self.future_scheduled_queue: "Queue[Events]" = Queue()
        self.liquidsoap = liquidsoap

        self.plq = PypoLiqQueue(self.future_scheduled_queue, self.liquidsoap)
        self.plq.start()

    def main(self) -> None:
        loops = 0
        heartbeat_period = math.floor(30 / PUSH_INTERVAL)

        events = None

        while True:
            try:
                events = self.queue.get(block=True)
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(exception)
                raise exception

            logger.debug(events)
            # separate media_schedule list into currently_playing and
            # scheduled_for_future lists
            currently_playing, scheduled_for_future = self.separate_present_future(
                events
            )

            self.liquidsoap.verify_correct_present_media(currently_playing)
            self.future_scheduled_queue.put(scheduled_for_future)

            if loops % heartbeat_period == 0:
                logger.info("heartbeat")
                loops = 0
            loops += 1

    def separate_present_future(self, events: Events) -> Tuple[List[AnyEvent], Events]:
        now = datetime.utcnow()

        present: List[AnyEvent] = []
        future: Events = {}

        for key in sorted(events.keys()):
            item = events[key]

            # Ignore track that already ended
            if isinstance(item, FileEvent) and item.end < now:
                logger.debug("ignoring ended media_item: %s", item)
                continue

            diff_sec = (now - item.start).total_seconds()

            if diff_sec >= 0:
                logger.debug("adding media_item to present: %s", item)
                present.append(item)
            else:
                logger.debug("adding media_item to future: %s", item)
                future[key] = item

        return present, future

    def run(self) -> None:
        while True:
            try:
                self.main()
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(exception)
                time.sleep(5)
