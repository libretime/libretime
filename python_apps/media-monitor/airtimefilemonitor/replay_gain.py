from subprocess import Popen, PIPE
import re
import os
import sys

def get_process_output(command):
    """
    Run subprocess and return stdout
    """
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

def calculate_replay_gain(file_path):
    """
    This function accepts files of type mp3/ogg/flac and returns a calculated ReplayGain value.
    If the value cannot be calculated for some reason, then we default to 1.
    
    TODO:
    Currently some of the subprocesses called will actually insert metadata into the file itself,
    which we do *not* want as this changes the file's hash. Need to make a copy of the file before
    we run this function.
    
    http://wiki.hydrogenaudio.org/index.php?title=ReplayGain_1.0_specification
    """

    search = None
    if re.search(r'mp3$', file_path, re.IGNORECASE) or get_mime_type(file_path) == "audio/mpeg":
        if run_process("which mp3gain > /dev/null") == 0:
            out = get_process_output('mp3gain -q "%s" 2> /dev/null' % file_path)
            search = re.search(r'Recommended "Track" dB change: (.*)', out)
        else:
            print "mp3gain not found"
            #Log warning
    elif re.search(r'ogg$', file_path, re.IGNORECASE) or get_mime_type(file_path) == "application/ogg":
        if run_process("which vorbisgain > /dev/null  && which ogginfo > /dev/null") == 0:
            run_process('vorbisgain -q -f "%s" 2>/dev/null >/dev/null' % file_path)
            out = get_process_output('ogginfo "%s"' % file_path)
            search = re.search(r'REPLAYGAIN_TRACK_GAIN=(.*) dB', out)
        else:
            print "vorbisgain/ogginfo not found"
            #Log warning
    elif re.search(r'flac$', file_path, re.IGNORECASE) or get_mime_type(file_path) == "audio/x-flac":
        if run_process("which metaflac > /dev/null") == 0:
            out = get_process_output('metaflac --show-tag=REPLAYGAIN_TRACK_GAIN "%s"' % file_path)
            search = re.search(r'REPLAYGAIN_TRACK_GAIN=(.*) dB', out)
        else:
            print "metaflac not found"
            #Log warning
    else:
        pass
        #Log unknown file type.

    replay_gain = 1
    if search:
        matches = search.groups()
        if len(matches) == 1:
            replay_gain = matches[0]

    return replay_gain


# Example of running from command line:
# python replay_gain.py /path/to/filename.mp3
if __name__ == "__main__":
    print calculate_replay_gain(sys.argv[1])
