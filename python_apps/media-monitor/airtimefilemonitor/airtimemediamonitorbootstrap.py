import os

from subprocess import Popen, PIPE


class AirtimeMediaMonitorBootstrap():

    def __init__(self, logger, multi_queue, pe):
        self.logger = logger
        self.multi_queue = multi_queue
        self.pe = pe
        
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
        airtime_tmp = '/var/tmp/airtime'
        
        #set to hold new and/or modified files. We use a set to make it ok if files are added
        #twice. This is become some of the tests for new files return result sets that are not
        #mutually exclusive from each other.
        modified_files = set()

        #find files that have been modified since the last time
        #media-monitor process was running.
        command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable -mmin -30" % dir
        stdout = self.execCommandAndReturnStdOut(command)
        self.logger.info("Files modified since last checkin: \n%s\n", stdout)
        
        new_files = stdout.split('\n')
        
        for file_path in new_files:
            modified_files.add(file_path)
    
        if os.path.exists(airtime_tmp + '/.airtime_media_index') and False:
            #a previous index exists, we can do a diff between this
            #file and the current state to see whether anything has
            #changed.
            self.logger.info("Previous index file found.")
                        
            #find deleted files
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable > %s/.airtime_media_index.tmp" % (dir, airtime_tmp)
            self.execCommand(command)
            
            command = "diff %s/.airtime_media_index.tmp %s/.airtime_media_index" % (airtime_tmp, airtime_tmp)
            stdout = self.execCommandAndReturnStdOut(command)
            self.logger.info("Deleted files since last checkin:\n%s\n", stdout)

            #TODO: notify about deleted files and files moved here
            
        else:
            #a previous index does not exist. Most likely means that 
            #media monitor has never seen this directory before. Let's
            #notify airtime server about each of these files
            self.logger.info("Previous index file does not exist. Creating a new one")
            
            #create a new index file.
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable" % dir
            stdout = self.execCommandAndReturnStdOut(command)
            self.logger.info("New files found: \n%s\n", stdout)
            self.write_file(airtime_tmp + '/.airtime_media_index', stdout)
            
            new_files = stdout.split('\n')
            
            for file_path in new_files:
                modified_files.add(file_path)
                
        self.logger.debug("set size: %d", len(modified_files))

        for file_path in modified_files:
            if os.path.exists(file_path):
                self.pe.handle_created_file(False, os.path.basename(file_path), file_path)
            
    def write_file(self, file, string):
        f = open(file, 'w')
        f.write(string)
        f.close()
                
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
                       
        
if __name__ == '__main__':
    
        mmb = AirtimeMediaMonitorBootstrap()
        mmb.scan()
