import subprocess
import logging
from .analyzer import Analyzer
import re


class ReplayGainAnalyzer(Analyzer):
    ''' This class extracts the ReplayGain using a tool from the python-rgain package. '''

    REPLAYGAIN_EXECUTABLE = 'replaygain' # From the rgain3 python package

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
            results = subprocess.check_output(command, stderr=subprocess.STDOUT,
                                              close_fds=True, text=True)
            gain_match = r'Calculating Replay Gain information \.\.\.(?:\n|.)*?:([\d.-]*) dB'
            replaygain = re.search(gain_match, results).group(1)
            metadata['replay_gain'] = float(replaygain)

        except OSError as e: # replaygain was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have python-rgain installed?"))
        except subprocess.CalledProcessError as e: # replaygain returned an error code
            logging.warn("%s %s %s", e.cmd, e.output, e.returncode)
        except Exception as e:
            logging.warn(e)

        return metadata
