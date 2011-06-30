import os
import time

from subprocess import Popen, PIPE


class AirtimeMediaMonitorBootstrap():

    def __init__(self, logger, multi_queue, pe):
        self.logger = logger
        self.multi_queue = multi_queue
        self.pe = pe
        self.airtime_tmp = '/var/tmp/airtime'
        
    """
    on bootup we want to scan all directories and look for files that
    weren't there or files that changed before media-monitor process
    went offline. We can do this by doing a hash of the directory metadata.
    """
    def scan(self):
        directories = ['/srv/airtime/stor']
        
        for dir in directories:
            self.check_for_diff(dir)
            
    def check_for_diff(self, dir):        
        #set to hold new and/or modified files. We use a set to make it ok if files are added
        #twice. This is become some of the tests for new files return result sets that are not
        #mutually exclusive from each other.
        added_files = set()
        removed_files = set()
    
        if os.path.exists(self.airtime_tmp + '/.airtime_media_index'):

            #find files that have been modified since the last time
            #media-monitor process was running.
            time_diff_sec = time.time() - os.path.getmtime(self.airtime_tmp + '/.airtime_media_index')
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable -mmin -%d" % (dir, time_diff_sec/60+1)
            self.logger.debug(command)
            stdout = self.execCommandAndReturnStdOut(command)
            self.logger.info("Files modified since last checkin: \n%s\n", stdout)
            
            new_files = stdout.split('\n')
            
            for file_path in new_files:
                added_files.add(file_path)




            #a previous index exists, we can do a diff between this
            #file and the current state to see whether anything has
            #changed.
            self.logger.info("Previous index file found.")
                        
            #find deleted files
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable > %s/.airtime_media_index.tmp" % (dir, self.airtime_tmp)
            self.execCommand(command)
            
            command = "diff -u %s/.airtime_media_index %s/.airtime_media_index.tmp" % (self.airtime_tmp, self.airtime_tmp)
            stdout = self.execCommandAndReturnStdOut(command)
            
            #remove first 3 lines from the diff output.
            stdoutSplit = (stdout.split('\n'))[3:]
            
            self.logger.info("Changed files since last checkin:\n%s\n", "\n".join(stdoutSplit))

            for line in stdoutSplit:
                if len(line.strip(' ')) > 0:
                    if line[0] == '+':
                        added_files.add(line[1:])
                    elif line[0] == '-':
                        removed_files.add(line[1:])

            self.pe.write_index_file()
        else:
            #a previous index does not exist. Most likely means that 
            #media monitor has never seen this directory before. Let's
            #notify airtime server about each of these files
            self.logger.info("Previous index file does not exist. Creating a new one")
            
            #create a new index file.
            stdout = self.pe.write_index_file()
            
            new_files = stdout.split('\n')
            
            for file_path in new_files:
                added_files.add(file_path)
                
        for file_path in added_files:
            if os.path.exists(file_path):
                self.pe.handle_created_file(False, os.path.basename(file_path), file_path)
                
        for file_path in removed_files:
            self.pe.handle_removed_file(file_path)
                            
    def execCommand(self, command):
        p = Popen(command, shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        if sts != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)        
                
    def execCommandAndReturnStdOut(self, command):
        p = Popen(command, shell=True, stdout=PIPE)
        stdout = p.communicate()[0]
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
        return stdout
                       