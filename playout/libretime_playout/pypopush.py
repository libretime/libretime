import math
import telnetlib
import time
import traceback
from datetime import datetime
from queue import Queue
from threading import Thread

from libretime_api_client import version1 as api_client
from loguru import logger

from .config import PUSH_INTERVAL, Config
from .pypoliqqueue import PypoLiqQueue
from .timeout import ls_timeout


def is_stream(media_item):
    return media_item["type"] == "stream_output_start"


def is_file(media_item):
    return media_item["type"] == "file"


class PypoPush(Thread):
    def __init__(self, q, telnet_lock, pypo_liquidsoap, config: Config):
        Thread.__init__(self)
        self.api_client = api_client.AirtimeApiClient()
        self.queue = q

        self.telnet_lock = telnet_lock
        self.config = config

        self.pushed_objects = {}
        self.current_prebuffering_stream_id = None
        self.queue_id = 0

        self.future_scheduled_queue = Queue()
        self.pypo_liquidsoap = pypo_liquidsoap

        self.plq = PypoLiqQueue(self.future_scheduled_queue, self.pypo_liquidsoap)
        self.plq.daemon = True
        self.plq.start()

    def main(self):
        loops = 0
        heartbeat_period = math.floor(30 / PUSH_INTERVAL)

        media_schedule = None

        while True:
            try:
                media_schedule = self.queue.get(block=True)
            except Exception as e:
                logger.error(str(e))
                raise
            else:
                logger.debug(media_schedule)
                # separate media_schedule list into currently_playing and
                # scheduled_for_future lists
                currently_playing, scheduled_for_future = self.separate_present_future(
                    media_schedule
                )

                self.pypo_liquidsoap.verify_correct_present_media(currently_playing)
                self.future_scheduled_queue.put(scheduled_for_future)

            if loops % heartbeat_period == 0:
                logger.info("heartbeat")
                loops = 0
            loops += 1

    def separate_present_future(self, media_schedule):
        tnow = datetime.utcnow()

        present = []
        future = {}

        sorted_keys = sorted(media_schedule.keys())
        for mkey in sorted_keys:
            media_item = media_schedule[mkey]

            # Ignore track that already ended
            if media_item["type"] == "file" and media_item["end"] < tnow:
                logger.debug(f"ignoring ended media_item: {media_item}")
                continue

            diff_td = tnow - media_item["start"]
            diff_sec = self.date_interval_to_seconds(diff_td)

            if diff_sec >= 0:
                logger.debug(f"adding media_item to present: {media_item}")
                present.append(media_item)
            else:
                logger.debug(f"adding media_item to future: {media_item}")
                future[mkey] = media_item

        return present, future

    def date_interval_to_seconds(self, interval):
        """
        Convert timedelta object into int representing the number of seconds. If
        number of seconds is less than 0, then return 0.
        """
        seconds = (
            interval.microseconds
            + (interval.seconds + interval.days * 24 * 3600) * 10**6
        ) / float(10**6)

        return seconds

    @ls_timeout
    def stop_web_stream_all(self):
        try:
            self.telnet_lock.acquire()
            tn = telnetlib.Telnet(
                self.config.playout.liquidsoap_host,
                self.config.playout.liquidsoap_port,
            )

            # msg = 'dynamic_source.read_stop_all xxx\n'
            msg = "http.stop\n"
            logger.debug(msg)
            tn.write(msg)

            msg = "dynamic_source.output_stop\n"
            logger.debug(msg)
            tn.write(msg)

            msg = "dynamic_source.id -1\n"
            logger.debug(msg)
            tn.write(msg)

            tn.write("exit\n")
            logger.debug(tn.read_all())

        except Exception as e:
            logger.error(str(e))
        finally:
            self.telnet_lock.release()

    def run(self):
        while True:
            try:
                self.main()
            except Exception as e:
                top = traceback.format_exc()
                logger.error("Pypo Push Exception: %s", top)
                time.sleep(5)
        logger.info("PypoPush thread exiting")
