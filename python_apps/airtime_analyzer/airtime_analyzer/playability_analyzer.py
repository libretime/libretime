__author__ = 'asantoni'

import subprocess
import logging
from .analyzer import Analyzer

class UnplayableFileError(Exception):
    pass

class PlayabilityAnalyzer(Analyzer):
    ''' This class checks if a file can actually be played with Liquidsoap. '''

    LIQUIDSOAP_EXECUTABLE = 'liquidsoap'

    @staticmethod
    def analyze(filename, metadata):
        ''' Checks if a file can be played by Liquidsoap.
        :param filename: The full path to the file to analyzer
        :param metadata: A metadata dictionary where the results will be put
        :return: The metadata dictionary
        '''
        command = [PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE, '-v', '-c', "output.dummy(audio_to_stereo(single(argv(1))))", '--', filename]
        try:
            subprocess.check_output(command, stderr=subprocess.STDOUT, close_fds=True)

        except OSError as e: # liquidsoap was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have liquidsoap installed?"))
        except (subprocess.CalledProcessError, Exception) as e: # liquidsoap returned an error code
            logging.warn(e)
            raise UnplayableFileError()

        return metadata
