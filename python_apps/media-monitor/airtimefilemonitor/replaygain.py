from subprocess import Popen, PIPE
import re
import os
import sys
import shutil
import tempfile
import logging


logger = logging.getLogger()

def get_process_output(command):
    """
    Run subprocess and return stdout
    """
    #logger.debug(command)
    p = Popen(command, shell=True, stdout=PIPE)
    return p.communicate()[0].strip()

def run_process(command):
    """
    Run subprocess and return "return code"
    """
    p = Popen(command, shell=True)
    return os.waitpid(p.pid, 0)[1]

def get_mime_type(file_path):
    """
    Attempts to get the mime type but will return prematurely if the process
    takes longer than 5 seconds. Note that this function should only be called
    for files which do not have a mp3/ogg/flac extension.
    """

    return get_process_output("timeout 5 file -b --mime-type %s" % file_path)

def duplicate_file(file_path):
    """
    Makes a duplicate of the file and returns the path of this duplicate file.
    """
    fsrc = open(file_path, 'r')
    fdst = tempfile.NamedTemporaryFile(delete=False)

    #logger.info("Copying %s to %s" % (file_path, fdst.name))

    shutil.copyfileobj(fsrc, fdst)

    fsrc.close()
    fdst.close()

    return fdst.name

def get_file_type(file_path):
    file_type = None
    if re.search(r'mp3$', file_path, re.IGNORECASE):
        file_type = 'mp3'
    elif re.search(r'og(g|a)$', file_path, re.IGNORECASE):
        file_type = 'vorbis'
    elif re.search(r'flac$', file_path, re.IGNORECASE):
        file_type = 'flac'
    elif re.search(r'(mp4|m4a)$', file_path, re.IGNORECASE):
        file_type = 'mp4'
    else:
        mime_type = get_mime_type(file_path) == "audio/mpeg"
        if 'mpeg' in mime_type:
            file_type = 'mp3'
        elif 'ogg' in mime_type or "oga" in mime_type:
            file_type = 'vorbis'
        elif 'flac' in mime_type:
            file_type = 'flac'
        elif 'mp4' in mime_type or "m4a" in mime_type:
            file_type = 'mp4'

    return file_type


def calculate_replay_gain(file_path):
    """
    This function accepts files of type mp3/ogg/flac and returns a calculated
    ReplayGain value in dB.  If the value cannot be calculated for some reason,
    then we default to 0 (Unity Gain).

    http://wiki.hydrogenaudio.org/index.php?title=ReplayGain_1.0_specification
    """

    try:
        """
        Making a duplicate is required because the ReplayGain extraction
        utilities we use make unwanted modifications to the file.
        """

        search = None
        temp_file_path = duplicate_file(file_path)

        file_type = get_file_type(file_path)

        if file_type:
            if file_type == 'mp3':
                if run_process("which mp3gain > /dev/null") == 0:
                    out = get_process_output('mp3gain -q "%s" 2> /dev/null' % temp_file_path)
                    search = re.search(r'Recommended "Track" dB change: (.*)', out)
                else:
                    logger.warn("mp3gain not found")
            elif file_type == 'vorbis':
                if run_process("which vorbisgain > /dev/null  && which ogginfo > /dev/null") == 0:
                    run_process('vorbisgain -q -f "%s" 2>/dev/null >/dev/null' % temp_file_path)
                    out = get_process_output('ogginfo "%s"' % temp_file_path)
                    search = re.search(r'REPLAYGAIN_TRACK_GAIN=(.*) dB', out)
                else:
                    logger.warn("vorbisgain/ogginfo not found")
            elif file_type == 'flac':
                if run_process("which metaflac > /dev/null") == 0:
                    out = get_process_output('metaflac --show-tag=REPLAYGAIN_TRACK_GAIN "%s"' % temp_file_path)
                    search = re.search(r'REPLAYGAIN_TRACK_GAIN=(.*) dB', out)
                else:
                    logger.warn("metaflac not found")
            elif file_type == 'mp4':
                if run_process("which aacgain > /dev/null") == 0:
                    out = get_process_output('aacgain -q "%s" 2> /dev/null' % temp_file_path)
                    search = re.search(r'Recommended "Track" dB change: (.*)', out)
                else:
                    logger.warn("aacgain not found")
            else:
                pass

        #no longer need the temp, file simply remove it.
        os.remove(temp_file_path)
    except Exception, e:
        logger.error(str(e))

    replay_gain = 0
    if search:
        matches = search.groups()
        if len(matches) == 1:
            replay_gain = matches[0]

    return replay_gain


# Example of running from command line:
# python replay_gain.py /path/to/filename.mp3
if __name__ == "__main__":
    print calculate_replay_gain(sys.argv[1])
