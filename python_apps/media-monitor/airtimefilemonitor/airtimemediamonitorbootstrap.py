import os

from subprocess import Popen, PIPE


class AirtimeMediaMonitorBootstrap():

    def __init__(self, logger):
        self.logger = logger
        
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
    
        if os.path.exists(airtime_tmp + '/.airtime_media_index'):
            #a previous index exists, we can do a diff between this
            #file and the current state to see whether anything has
            #changed.
            self.logger.info("Previous index file found.")
            
                
            #find files that have been modified since the last time
            #media-monitor process was running.
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable -mmin -30" % dir
            stdout = self.execCommandAndReturnStdOut(command)
            self.logger.info("Files modified since last checkin: \n%s\n", stdout)
            
            
            #find deleted files
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable > %s/.airtime_media_index.tmp" % (dir, airtime_tmp)
            self.execCommand(command)
            
            command = "diff %s/.airtime_media_index.tmp %s/.airtime_media_index" % (airtime_tmp, airtime_tmp)
            stdout = self.execCommandAndReturnStdOut(command)
            self.logger.info("Deleted files since last checkin:\n%s\n", stdout)
            
        else:
            #a previous index does not exist. Most likely means that 
            #media monitor has never seen this directory before. Let's
            #notify airtime server about each of these files
            self.logger.info("Previous index file does not exist. Creating a new one")
            
            #create a new index file.
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable > %s/.airtime_media_index" % (dir, airtime_tmp)
            self.execCommand(command)
                
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