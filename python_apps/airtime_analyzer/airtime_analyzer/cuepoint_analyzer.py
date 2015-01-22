import subprocess
import logging
import traceback
import json
import datetime
from analyzer import Analyzer


class CuePointAnalyzer(Analyzer):
    ''' This class extracts the cue-in time, cue-out time, and length of a track using silan. '''

    SILAN_EXECUTABLE = 'silan'

    @staticmethod
    def analyze(filename, metadata):
        ''' Extracts the cue-in and cue-out times along and sets the file duration based on that.
            The cue points are there to skip the silence at the start and end of a track, and are determined
            using "silan", which analyzes the loudness in a track.
        :param filename: The full path to the file to analyzer
        :param metadata: A metadata dictionary where the results will be put
        :return: The metadata dictionary
        '''
        ''' The silan -F 0.99 parameter tweaks the highpass filter. The default is 0.98, but at that setting,
            the unit test on the short m4a file fails. With the new setting, it gets the correct cue-in time and
            all the unit tests pass.
        '''
        command = [CuePointAnalyzer.SILAN_EXECUTABLE, '-b', '-F', '0.99', '-f', 'JSON', '-t', '1.0', filename]
        try:
            results_json = subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)
            silan_results = json.loads(results_json)
            metadata['length_seconds'] = float(silan_results['file duration'])
            # Conver the length into a formatted time string
            track_length = datetime.timedelta(seconds=metadata['length_seconds'])
            metadata["length"] = str(track_length)
            metadata['cuein'] = format(silan_results['sound'][0][0], 'f')
            metadata['cueout'] = format(silan_results['sound'][0][1], 'f')

        except OSError as e: # silan was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have silan installed?"))
        except subprocess.CalledProcessError as e: # silan returned an error code
            logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
        except Exception as e:
            logging.warn(e)

        return metadata
