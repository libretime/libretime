import subprocess
import logging
from .analyzer import Analyzer


class ReplayGainAnalyzer(Analyzer):
    ''' This class extracts the ReplayGain using a tool from the python-rgain package. '''

    REPLAYGAIN_EXECUTABLE = 'replaygain' # From the python-rgain package

    @staticmethod
    def analyze(filename, metadata):
        ''' Extracts the Replaygain loudness normalization factor of a track.
        :param filename: The full path to the file to analyzer
        :param metadata: A metadata dictionary where the results will be put
        :return: The metadata dictionary
        '''
        ''' The -d flag means do a dry-run, ie. don't modify the file directly.
        '''
        command = [ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE, '-d', filename]
        try:
            results = subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)
            filename_token = "%s: " % filename
            rg_pos = results.find(filename_token, results.find("Calculating Replay Gain information")) + len(filename_token)
            db_pos = results.find(" dB", rg_pos)
            replaygain = results[rg_pos:db_pos]
            metadata['replay_gain'] = float(replaygain)

        except OSError as e: # replaygain was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have python-rgain installed?"))
        except subprocess.CalledProcessError as e: # replaygain returned an error code
            logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
        except Exception as e:
            logging.warn(e)

        return metadata
