import hashlib
import logging
import os
import stat
import time
from queue import Empty, Queue
from threading import Thread
from typing import Optional

from libretime_api_client.v2 import ApiClient
from requests.exceptions import ConnectionError, HTTPError, Timeout

from .events import FileEvent, FileEvents

logger = logging.getLogger(__name__)


class PypoFile(Thread):
    name = "file"
    daemon = True

    file_events_queue: Queue[FileEvents]
    file_events: FileEvents

    def __init__(
        self,
        file_queue: Queue[FileEvents],
        api_client: ApiClient,
    ):
        Thread.__init__(self)
        self.file_events_queue = file_queue
        self.file_events = {}
        self.api_client = api_client

    def copy_file(self, file_event: FileEvent):
        """
        Copy file_event from local library directory to local cache directory.
        """
        file_id = file_event["id"]
        dst = file_event["dst"]

        dst_exists = True
        try:
            dst_size = os.path.getsize(dst)
            if dst_size == 0:
                dst_exists = False
        except Exception:
            dst_exists = False

        do_copy = False
        if dst_exists:
            # TODO: Check if the locally cached variant of the file is sane.
            # This used to be a filesize check that didn't end up working.
            # Once we have watched folders updated files from them might
            # become an issue here... This needs proper cache management.
            # https://github.com/libretime/libretime/issues/756#issuecomment-477853018
            # https://github.com/libretime/libretime/pull/845
            logger.debug("found file %s in cache %s, skipping copy...", file_id, dst)
        else:
            do_copy = True

        file_event["file_ready"] = not do_copy

        if do_copy:
            logger.info("copying file %s to cache %s", file_id, dst)
            try:
                with open(dst, "wb") as handle:
                    logger.info(file_event)
                    try:
                        response = self.api_client.download_file(file_id, stream=True)
                        for chunk in response.iter_content(chunk_size=2048):
                            handle.write(chunk)

                    except HTTPError as exception:
                        raise RuntimeError(
                            f"could not download file {file_event['id']}"
                        ) from exception

                # make file world readable and owner writable
                os.chmod(dst, stat.S_IRUSR | stat.S_IWUSR | stat.S_IRGRP | stat.S_IROTH)

                if file_event["filesize"] == 0:
                    file_size = self.report_file_size_and_md5_to_api(
                        dst, file_event["id"]
                    )
                    file_event["filesize"] = file_size

                file_event["file_ready"] = True
            except Exception as exception:
                logger.exception(
                    "could not copy file %s to %s: %s",
                    file_id,
                    dst,
                    exception,
                )

    def report_file_size_and_md5_to_api(self, file_path: str, file_id: int) -> int:
        try:
            file_size = os.path.getsize(file_path)

            with open(file_path, "rb") as fh:
                hasher = hashlib.md5(usedforsecurity=False)
                while True:
                    data = fh.read(8192)
                    if not data:
                        break
                    hasher.update(data)
                md5_hash = hasher.hexdigest()
        except OSError as exception:
            file_size = 0
            logger.exception(
                "Error getting file size and md5 hash for file id %s: %s",
                file_id,
                exception,
            )

        # Make PUT request to LibreTime to update the file size and hash
        error_msg = f"Could not update media file {file_id} with file size and md5 hash"
        try:
            self.api_client.update_file(
                file_id,
                json={"filesize": file_size, "md5": md5_hash},
            )
        except (ConnectionError, Timeout):
            logger.exception(error_msg)
        except Exception as exception:
            logger.exception("%s: %s", error_msg, exception)

        return file_size

    def get_highest_priority_file_event(
        self,
        file_events: FileEvents,
    ) -> Optional[FileEvent]:
        """
        Get highest priority file event in the queue. Currently the highest
        priority is decided by how close the start time is to "now".
        """
        if file_events is None or len(file_events) == 0:
            return None

        sorted_keys = sorted(file_events.keys())

        if len(sorted_keys) == 0:
            return None

        highest_priority = sorted_keys[0]
        file_event = file_events[highest_priority]

        logger.debug("Highest priority item: %s", highest_priority)

        # Remove this media_item from the dictionary. On the next iteration
        # (from the main function) we won't consider it for prioritization
        # anymore. If on the next iteration we have received a new schedule,
        # it is very possible we will have to deal with the same media_items
        # again. In this situation, the worst possible case is that we try to
        # copy the file again and realize we already have it (thus aborting the copy).
        del file_events[highest_priority]

        return file_event

    def main(self):
        while True:
            try:
                if self.file_events is None or len(self.file_events) == 0:
                    # We have no schedule, so we have nothing else to do. Let's
                    # do a blocked wait on the queue
                    self.file_events = self.file_events_queue.get(block=True)
                else:
                    # We have a schedule we need to process, but we also want
                    # to check if a newer schedule is available. In this case
                    # do a non-blocking queue.get and in either case (we get something
                    # or we don't), get back to work on preparing getting files.
                    try:
                        self.file_events = self.file_events_queue.get_nowait()
                    except Empty:
                        pass

                file_event = self.get_highest_priority_file_event(self.file_events)
                if file_event is not None:
                    self.copy_file(file_event)
            except Exception as exception:
                logger.exception(exception)
                raise exception

    def run(self):
        """
        Entry point of the thread
        """
        try:
            self.main()
        except Exception as exception:
            logger.exception(exception)
            time.sleep(5)

        logger.info("PypoFile thread exiting")
