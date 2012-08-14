from threading import Thread

import traceback
import os
import time

from media.update import replaygain
from media.monitor.log import Loggable


class ReplayGainUpdater(Thread, Loggable):
    """
    The purpose of the class is to query the server for a list of files which
    do not have a ReplayGain value calculated. This class will iterate over the
    list calculate the values, update the server and repeat the process until
    the server reports there are no files left.

    This class will see heavy activity right after a 2.1->2.2 upgrade since 2.2
    introduces ReplayGain normalization. A fresh install of Airtime 2.2 will
    see this class not used at all since a file imported in 2.2 will
    automatically have its ReplayGain value calculated.
    """

    @staticmethod
    def start_reply_gain(apc):
        me = ReplayGainUpdater(apc)
        me.daemon = True
        me.start()

    def __init__(self,apc):
        Thread.__init__(self)
        self.api_client = apc

    def main(self):
        raw_response = self.api_client.list_all_watched_dirs()
        if 'dirs' not in raw_response:
            self.logger.error("Could not get a list of watched directories \
                               with a dirs attribute. Printing full request:")
            self.logger.error( raw_response )
            return

        directories = raw_response['dirs']

        for dir_id, dir_path in directories.iteritems():
            try:
                # keep getting few rows at a time for current music_dir (stor
                # or watched folder).
                total = 0
                while True:
                    # return a list of pairs where the first value is the
                    # file's database row id and the second value is the
                    # filepath
                    files = self.api_client.get_files_without_replay_gain_value(dir_id)
                    processed_data = []
                    for f in files:
                        full_path = os.path.join(dir_path, f['fp'])
                        processed_data.append((f['id'], replaygain.calculate_replay_gain(full_path)))

                    self.api_client.update_replay_gain_values(processed_data)
                    if len(files) == 0: break
                self.logger.info("Processed: %d songs" % total)

            except Exception, e:
                self.logger.error(e)
                self.logger.debug(traceback.format_exc())
    def run(self):
        try:
            while True:
                self.logger.info("Runnning replaygain updater")
                self.main()
                # Sleep for 5 minutes in case new files have been added
                time.sleep(60 * 5)
        except Exception, e:
            self.logger.error('ReplayGainUpdater Exception: %s', traceback.format_exc())
            self.logger.error(e)

if __name__ == "__main__":
    rgu = ReplayGainUpdater()
    rgu.main()
