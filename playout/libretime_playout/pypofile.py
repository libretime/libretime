import hashlib
import os
import stat
import time
import traceback
from queue import Empty
from threading import Thread

from libretime_api_client import version2 as api_client
from loguru import logger
from requests.exceptions import ConnectionError, Timeout


class PypoFile(Thread):
    def __init__(self, schedule_queue):
        Thread.__init__(self)
        self.media_queue = schedule_queue
        self.media = None
        self.api_client = api_client.AirtimeApiClient()

    def copy_file(self, media_item):
        """
        Copy media_item from local library directory to local cache directory.
        """
        src = media_item["uri"]
        dst = media_item["dst"]

        src_size = media_item["filesize"]

        dst_exists = True
        try:
            dst_size = os.path.getsize(dst)
            if dst_size == 0:
                dst_exists = False
        except Exception as e:
            dst_exists = False

        do_copy = False
        if dst_exists:
            # TODO: Check if the locally cached variant of the file is sane.
            # This used to be a filesize check that didn't end up working.
            # Once we have watched folders updated files from them might
            # become an issue here... This needs proper cache management.
            # https://github.com/libretime/libretime/issues/756#issuecomment-477853018
            # https://github.com/libretime/libretime/pull/845
            logger.debug(
                "file %s already exists in local cache as %s, skipping copying..."
                % (src, dst)
            )
        else:
            do_copy = True

        media_item["file_ready"] = not do_copy

        if do_copy:
            logger.info(f"copying from {src} to local cache {dst}")
            try:
                with open(dst, "wb") as handle:
                    logger.info(media_item)
                    response = self.api_client.services.file_download_url(
                        id=media_item["id"]
                    )

                    if not response.ok:
                        logger.error(response)
                        raise Exception(
                            "%s - Error occurred downloading file"
                            % response.status_code
                        )

                    for chunk in response.iter_content(chunk_size=1024):
                        handle.write(chunk)

                # make file world readable and owner writable
                os.chmod(dst, stat.S_IRUSR | stat.S_IWUSR | stat.S_IRGRP | stat.S_IROTH)

                if media_item["filesize"] == 0:
                    file_size = self.report_file_size_and_md5_to_api(
                        dst, media_item["id"]
                    )
                    media_item["filesize"] = file_size

                media_item["file_ready"] = True
            except Exception as e:
                logger.error(f"Could not copy from {src} to {dst}")
                logger.error(e)

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
        except OSError as e:
            file_size = 0
            logger.error(
                "Error getting file size and md5 hash for file id %s" % file_id
            )
            logger.error(e)

        # Make PUT request to LibreTime to update the file size and hash
        error_msg = (
            "Could not update media file %s with file size and md5 hash:" % file_id
        )
        try:
            payload = {"filesize": file_size, "md5": md5_hash}
            response = self.api_client.update_file(file_id, payload)
        except (ConnectionError, Timeout):
            logger.error(error_msg)
        except Exception as e:
            logger.error(error_msg)
            logger.error(e)

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

        """
        Remove this media_item from the dictionary. On the next iteration
        (from the main function) we won't consider it for prioritization
        anymore. If on the next iteration we have received a new schedule,
        it is very possible we will have to deal with the same media_items
        again. In this situation, the worst possible case is that we try to
        copy the file again and realize we already have it (thus aborting the copy).
        """
        del schedule[highest_priority]

        return media_item

    def main(self):
        while True:
            try:
                if self.media is None or len(self.media) == 0:
                    """
                    We have no schedule, so we have nothing else to do. Let's
                    do a blocked wait on the queue
                    """
                    self.media = self.media_queue.get(block=True)
                else:
                    """
                    We have a schedule we need to process, but we also want
                    to check if a newer schedule is available. In this case
                    do a non-blocking queue.get and in either case (we get something
                    or we don't), get back to work on preparing getting files.
                    """
                    try:
                        self.media = self.media_queue.get_nowait()
                    except Empty as e:
                        pass

                media_item = self.get_highest_priority_media_item(self.media)
                if media_item is not None:
                    self.copy_file(media_item)
            except Exception as e:
                import traceback

                top = traceback.format_exc()
                logger.error(str(e))
                logger.error(top)
                raise

    def run(self):
        """
        Entry point of the thread
        """
        try:
            self.main()
        except Exception as e:
            top = traceback.format_exc()
            logger.error("PypoFile Exception: %s", top)
            time.sleep(5)
        logger.info("PypoFile thread exiting")
