from threading import Thread

import traceback
import os
import logging
import json

from api_clients import api_client
from media.update import replaygain


class ReplayGainUpdater(Thread):
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

    def __init__(self, logger):
        Thread.__init__(self)
        self.logger = logger
        self.api_client = api_client.AirtimeApiClient()

    def main(self):

        #TODO make sure object has 'dirs' attr
        directories = self.api_client.list_all_watched_dirs()['dirs']

        for dir_id, dir_path in directories.iteritems():
            try:
                processed_data = []

                #keep getting few rows at a time for current music_dir (stor or
                #watched folder).  #When we get a response with 0 rows, then we
                #will set 'finished' to True.
                finished = False

                while not finished:
                    # return a list of pairs where the first value is the file's database row id
                    # and the second value is the filepath
                    files = self.api_client.get_files_without_replay_gain_value(dir_id)

                    for f in files:
                        full_path = os.path.join(dir_path, f['fp'])
                        processed_data.append((f['id'], replaygain.calculate_replay_gain(full_path)))

                    self.api_client.update_replay_gain_values(processed_data)
                    finished = (len(files) == 0)

            except Exception, e:
                self.logger.error(e)
                self.logger.debug(traceback.format_exc())
    def run(self):
        try: self.main()
        except Exception, e:
            self.logger.error('ReplayGainUpdater Exception: %s', traceback.format_exc())
            self.logger.error(e)

if __name__ == "__main__":
    try:
        rgu = ReplayGainUpdater(logging)
        rgu.main()
    except Exception, e:
        print e
        print traceback.format_exc()
