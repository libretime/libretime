import hashlib
import os
import stat
import time
from queue import Empty, Queue
from threading import Thread
from typing import Any, Dict

from libretime_api_client.v2 import ApiClient
from loguru import logger
from requests.exceptions import ConnectionError, HTTPError, Timeout


class PypoFile(Thread):
    name = "file"
    daemon = True

    def __init__(
        self,
        file_queue: Queue[Dict[str, Any]],
        api_client: ApiClient,
    ):
        Thread.__init__(self)
        self.media_queue = file_queue
        self.media = None
        self.api_client = api_client

    def copy_file(self, media_item):
        """
        Copy media_item from local library directory to local cache directory.
        """
        file_id = media_item["id"]
        dst = media_item["dst"]

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

        media_item["file_ready"] = not do_copy

        if do_copy:
            logger.info("copying file %s to cache %s", file_id, dst)
            try:
                with open(dst, "wb") as handle:
                    logger.info(media_item)
                    try:
                        response = self.api_client.download_file(file_id, stream=True)
                        for chunk in response.iter_content(chunk_size=2048):
                            handle.write(chunk)

                    except HTTPError as exception:
                        raise RuntimeError(
                            f"could not download file {media_item['id']}"
                        ) from exception

                # make file world readable and owner writable
                os.chmod(dst, stat.S_IRUSR | stat.S_IWUSR | stat.S_IRGRP | stat.S_IROTH)

                if media_item["filesize"] == 0:
                    file_size = self.report_file_size_and_md5_to_api(
                        dst, media_item["id"]
                    )
                    media_item["filesize"] = file_size

                media_item["file_ready"] = True
            except Exception as exception:
                logger.exception(
                    "could not copy file %s to %s: %s",
                    file_id,
                    dst,
                    exception,
                )

    def report_file_size_and_md5_to_api(self, file_path, file_id):
        try:
            file_size = os.path.getsize(file_path)

            with open(file_path, "rb") as fh:
                m = hashlib.md5()
                while True:
                    data = fh.read(8192)
                    if not data:
                        break
                    m.update(data)
                md5_hash = m.hexdigest()
        except OSError as exception:
            file_size = 0
            logger.exception(
                f"Error getting file size and md5 hash for file id {file_id}: {exception}"
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

    def get_highest_priority_media_item(self, schedule):
        """
        Get highest priority media_item in the queue. Currently the highest
        priority is decided by how close the start time is to "now".
        """
        if schedule is None or len(schedule) == 0:
            return None

        sorted_keys = sorted(schedule.keys())

        if len(sorted_keys) == 0:
            return None

        highest_priority = sorted_keys[0]
        media_item = schedule[highest_priority]

        logger.debug("Highest priority item: %s" % highest_priority)

        # Remove this media_item from the dictionary. On the next iteration
        # (from the main function) we won't consider it for prioritization
        # anymore. If on the next iteration we have received a new schedule,
        # it is very possible we will have to deal with the same media_items
        # again. In this situation, the worst possible case is that we try to
        # copy the file again and realize we already have it (thus aborting the copy).
        del schedule[highest_priority]

        return media_item

    def main(self):
        while True:
            try:
                if self.media is None or len(self.media) == 0:
                    # We have no schedule, so we have nothing else to do. Let's
                    # do a blocked wait on the queue
                    self.media = self.media_queue.get(block=True)
                else:
                    # We have a schedule we need to process, but we also want
                    # to check if a newer schedule is available. In this case
                    # do a non-blocking queue.get and in either case (we get something
                    # or we don't), get back to work on preparing getting files.
                    try:
                        self.media = self.media_queue.get_nowait()
                    except Empty:
                        pass

                media_item = self.get_highest_priority_media_item(self.media)
                if media_item is not None:
                    self.copy_file(media_item)
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
