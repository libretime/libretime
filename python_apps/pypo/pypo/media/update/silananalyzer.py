from threading import Thread

import traceback
import time
import subprocess
import json


class SilanAnalyzer(Thread):
    """
    The purpose of the class is to query the server for a list of files which
    do not have a Silan value calculated. This class will iterate over the
    list calculate the values, update the server and repeat the process until
    the server reports there are no files left.
    """

    @staticmethod
    def start_silan(apc, logger):
        me = SilanAnalyzer(apc, logger)
        me.start()

    def __init__(self, apc, logger):
        Thread.__init__(self)
        self.api_client = apc
        self.logger = logger

    def main(self):
        while True:
            # keep getting few rows at a time for current music_dir (stor
            # or watched folder).
            total = 0

            # return a list of pairs where the first value is the
            # file's database row id and the second value is the
            # filepath
            files = self.api_client.get_files_without_silan_value()
            total_files = len(files)
            if total_files == 0: return
            processed_data = []
            for f in files:
                full_path = f['fp']
                # silence detect(set default queue in and out)
                try:
                    data = {}
                    command = ['nice', '-n', '19', 'silan', '-b', '-f', 'JSON', full_path]
                    try:
                        proc = subprocess.Popen(command, stdout=subprocess.PIPE)
                        comm = proc.communicate()
                        if len(comm):
                            out = comm[0].strip('\r\n')
                            info = json.loads(out)
                            try: data['length'] = str('{0:f}'.format(info['file duration']))
                            except: pass
                            try: data['cuein'] = str('{0:f}'.format(info['sound'][0][0]))
                            except: pass
                            try: data['cueout'] = str('{0:f}'.format(info['sound'][-1][1]))
                            except: pass
                    except Exception, e:
                        self.logger.warn(str(command))
                        self.logger.warn(e)
                    processed_data.append((f['id'], data))
                    total += 1
                    if total % 5 == 0:
                        self.logger.info("Total %s / %s files has been processed.." % (total, total_files))
                except Exception, e:
                    self.logger.error(e)
                    self.logger.error(traceback.format_exc())

            try:
                self.api_client.update_cue_values_by_silan(processed_data)
            except Exception ,e:
                self.logger.error(e)
                self.logger.error(traceback.format_exc())

            self.logger.info("Processed: %d songs" % total)

    def run(self):
        while True:
            try:
                self.logger.info("Running Silan analyzer")
                self.main()
            except Exception, e:
                self.logger.error('Silan Analyzer Exception: %s', traceback.format_exc())
                self.logger.error(e)
            self.logger.info("Sleeping for 5...")
            time.sleep(60 * 5)

if __name__ == "__main__":
    from api_clients import api_client
    import logging
    logging.basicConfig(level=logging.DEBUG)
    api_client = api_client.AirtimeApiClient()
    SilanAnalyzer.start_silan(api_client, logging)

