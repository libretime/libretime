import subprocess
import logging
import traceback
import json
import datetime
from .analyzer import Analyzer


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

            # Defensive coding against Silan wildly miscalculating the cue in and out times:
            silan_length_seconds = float(silan_results['file duration'])
            silan_cuein = format(silan_results['sound'][0][0], 'f')
            silan_cueout = format(silan_results['sound'][0][1], 'f')

            # Sanity check the results against any existing metadata passed to us (presumably extracted by Mutagen):
            if 'length_seconds' in metadata:
                # Silan has a rare bug where it can massively overestimate the length or cue out time sometimes.
                if (silan_length_seconds - metadata['length_seconds'] > 3) or (float(silan_cueout) - metadata['length_seconds'] > 2):
                    # Don't trust anything silan says then...
                    raise Exception("Silan cue out {0} or length {1} differs too much from the Mutagen length {2}. Ignoring Silan values."
                                    .format(silan_cueout, silan_length_seconds, metadata['length_seconds']))
                # Don't allow silan to trim more than the greater of 3 seconds or 5% off the start of a track
                if float(silan_cuein) > max(silan_length_seconds*0.05, 3):
                    raise Exception("Silan cue in time {0} too big, ignoring.".format(silan_cuein))
            else:
                # Only use the Silan track length in the worst case, where Mutagen didn't give us one for some reason.
                # (This is mostly to make the unit tests still pass.)
                # Convert the length into a formatted time string.
                metadata['length_seconds'] = silan_length_seconds #
                track_length = datetime.timedelta(seconds=metadata['length_seconds'])
                metadata["length"] = str(track_length)


            ''' XXX: I've commented out the track_length stuff below because Mutagen seems more accurate than silan
                     as of Mutagen version 1.31. We are always going to use Mutagen's length now because Silan's
                     length can be off by a few seconds reasonably often.
            '''

            metadata['cuein'] = silan_cuein
            metadata['cueout'] = silan_cueout

        except OSError as e: # silan was not found
            logging.warn("Failed to run: %s - %s. %s" % (command[0], e.strerror, "Do you have silan installed?"))
        except subprocess.CalledProcessError as e: # silan returned an error code
            logging.warn("%s %s %s", e.cmd, e.message, e.returncode)
        except Exception as e:
            logging.warn(e)

        return metadata
