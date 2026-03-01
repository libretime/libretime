import hashlib
import logging
import os
import time
from queue import Empty, Queue
from threading import Thread
from typing import Optional

import requests
from libretime_api_client.v2 import ApiClient

from .events import FileEvent, FileEvents

logger = logging.getLogger(__name__)


class PypoFile(Thread):
    name = "file"
    daemon = True

    file_events_queue: "Queue[FileEvents]"
    file_events: FileEvents

    def __init__(
        self,
        file_queue: "Queue[FileEvents]",
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
        if file_event.local_filepath.is_file():
            logger.debug(
                "found file %s in cache %s",
                file_event.id,
                file_event.local_filepath,
            )
            file_event.file_ready = True
            return

        logger.info(
            "copying file %s to cache %s",
            file_event.id,
            file_event.local_filepath,
        )
        try:
            try:
                with file_event.local_filepath.open("wb") as file_fd:
                    response = self.api_client.download_file(file_event.id, stream=True)
                    for chunk in response.iter_content(chunk_size=8192):
                        file_fd.write(chunk)

            except requests.exceptions.HTTPError as exception:
                file_event.local_filepath.unlink(missing_ok=True)

                raise RuntimeError(
                    f"could not download file {file_event.id}"
                ) from exception

            # make file world readable and owner writable
            file_event.local_filepath.chmod(0o644)

            if file_event.filesize == 0:
                file_event.filesize = self.report_file_size_and_md5_to_api(
                    str(file_event.local_filepath),
                    file_event.id,
                )

            file_event.file_ready = True
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(
                "could not copy file %s to %s: %s",
                file_event.id,
                file_event.local_filepath,
                exception,
            )

    def report_file_size_and_md5_to_api(self, file_path: str, file_id: int) -> int:
        try:
            file_size = os.path.getsize(file_path)

            with open(file_path, "rb") as file_fd:
                hasher = hashlib.new("md5", usedforsecurity=False)
                while True:
                    data = file_fd.read(8192)
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
        except (requests.exceptions.ConnectionError, requests.exceptions.Timeout):
            logger.exception(error_msg)
        except Exception as exception:  # pylint: disable=broad-exception-caught
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
            except Exception as exception:  # pylint: disable=broad-exception-caught
                logger.exception(exception)
                raise exception

    def run(self):
        """
        Entry point of the thread
        """
        try:
            self.main()
        except Exception as exception:  # pylint: disable=broad-exception-caught
            logger.exception(exception)
            time.sleep(5)

        logger.info("PypoFile thread exiting")
