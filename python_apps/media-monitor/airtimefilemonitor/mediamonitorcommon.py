import os
import grp
import pwd
import logging

from subprocess import Popen, PIPE
from airtimemetadata import AirtimeMetadata

class MediaMonitorCommon:

    timestamp_file = "/var/tmp/airtime/last_index"
    
    def __init__(self, airtime_config):
        self.supported_file_formats = ['mp3', 'ogg']
        self.logger = logging.getLogger()
        self.config = airtime_config
        self.md_manager = AirtimeMetadata()

    def is_parent_directory(self, filepath, directory):
        filepath = os.path.normpath(filepath)
        directory = os.path.normpath(directory)
        return (directory == filepath[0:len(directory)])

    """
    def is_temp_file(self, filename):
        info = filename.split(".")

        if(info[-2] in self.supported_file_formats):
            return True
        else:
            return False
    """

    def is_audio_file(self, filename):
        info = filename.split(".")

        if(info[-1] in self.supported_file_formats):
            return True
        else:
            return False
        
    #check if file is readable by "nobody"
    def has_correct_permissions(self, filepath):
        #drop root permissions and become "nobody"
        os.seteuid(65534)
        
        try:
            open(filepath)
            readable = True
        except IOError:
            self.logger.warn("File does not have correct permissions: '%s'", filepath)
            readable = False
        except Exception, e:
            self.logger.error("Unexpected exception thrown: %s", e)
            readable = False
        finally:
            #reset effective user to root
            os.seteuid(0)
        
        return readable

    def set_needed_file_permissions(self, item, is_dir):
        try:
            omask = os.umask(0)

            uid = pwd.getpwnam('www-data')[2]
            gid = grp.getgrnam('www-data')[2]

            os.chown(item, uid, gid)

            if is_dir is True:
                os.chmod(item, 02777)
            else:
                os.chmod(item, 0666)

        except Exception, e:
            self.logger.error("Failed to change file's owner/group/permissions. %s", e)
        finally:
            os.umask(omask)

            
    #checks if path is a directory, and if it doesnt exist, then creates it.
    #Otherwise prints error to log file.
    def ensure_is_dir(self, directory):
        try:
            omask = os.umask(0)
            if not os.path.exists(directory):
                os.makedirs(directory, 02777)
            elif not os.path.isdir(directory):
                #path exists but it is a file not a directory!
                self.logger.error("path %s exists, but it is not a directory!!!")
        finally:
            os.umask(omask)

    #moves file from source to dest but also recursively removes the 
    #the source file's parent directories if they are now empty.
    def move_file(self, source, dest):

        try:
            omask = os.umask(0)
            os.rename(source, dest)
        except Exception, e:
            self.logger.error("failed to move file. %s", e)
        finally:
            os.umask(omask)
         
        dir = os.path.dirname(source)
        self.cleanup_empty_dirs(dir)
            
    #keep moving up the file hierarchy and deleting parent
    #directories until we hit a non-empty directory, or we
    #hit the organize dir.
    def cleanup_empty_dirs(self, dir):
        if os.path.normpath(dir) != self.config.organize_directory:
            if len(os.listdir(dir)) == 0:
                os.rmdir(dir)
                
                pdir = os.path.dirname(dir)
                self.cleanup_empty_dirs(pdir)
        

    #checks if path exists already in stor. If the path exists and the md5s are the 
    #same just overwrite.
    def create_unique_filename(self, filepath, old_filepath):

        try:
            if(os.path.exists(filepath)):
                self.logger.info("Path %s exists", filepath)

                self.logger.info("Checking if md5s are the same.")
                md5_fp = self.md_manager.get_md5(filepath)
                md5_ofp = self.md_manager.get_md5(old_filepath)

                if(md5_fp == md5_ofp):
                    self.logger.info("Md5s are the same, moving to same filepath.")
                    return filepath

                self.logger.info("Md5s aren't the same, appending to filepath.")
                file_dir = os.path.dirname(filepath)
                filename = os.path.basename(filepath).split(".")[0]
                #will be in the format .ext
                file_ext = os.path.splitext(filepath)[1]
                i = 1;
                while(True):
                    new_filepath = '%s/%s(%s)%s' % (file_dir, filename, i, file_ext)
                    self.logger.error("Trying %s", new_filepath)

                    if(os.path.exists(new_filepath)):
                        i = i+1;
                    else:
                        filepath = new_filepath
                        break

        except Exception, e:
             self.logger.error("Exception %s", e)

        return filepath

    #create path in /srv/airtime/stor/imported/[song-metadata]
    def create_file_path(self, original_path, orig_md):

        storage_directory = self.config.storage_directory

        is_recorded_show = False

        try:
            #will be in the format .ext
            file_ext = os.path.splitext(original_path)[1]
            file_ext = file_ext.encode('utf-8')

            path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE', 'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']

            md = {}
            for m in path_md:
                if m not in orig_md:
                    md[m] = u'unknown'.encode('utf-8')
                else:
                    #get rid of any "/" which will interfere with the filepath.
                    if isinstance(orig_md[m], basestring):
                        md[m] = orig_md[m].replace("/", "-")
                    else:
                        md[m] = orig_md[m]

            if 'MDATA_KEY_TRACKNUMBER' in orig_md:
                #make sure all track numbers are at least 2 digits long in the filepath.
                md['MDATA_KEY_TRACKNUMBER'] = "%02d" % (int(md['MDATA_KEY_TRACKNUMBER']))

            #format bitrate as 128kbps
            md['MDATA_KEY_BITRATE'] = str(md['MDATA_KEY_BITRATE']/1000)+"kbps"

            filepath = None
            #file is recorded by Airtime
            #/srv/airtime/stor/recorded/year/month/year-month-day-time-showname-bitrate.ext
            if(md['MDATA_KEY_CREATOR'] == "AIRTIMERECORDERSOURCEFABRIC".encode('utf-8')):
                #yyyy-mm-dd-hh-MM-ss
                y = orig_md['MDATA_KEY_YEAR'].split("-")
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "recorded".encode('utf-8'), y[0], y[1], orig_md['MDATA_KEY_YEAR'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            elif(md['MDATA_KEY_TRACKNUMBER'] == u'unknown'.encode('utf-8')):
                filepath = '%s/%s/%s/%s/%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            else:
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TRACKNUMBER'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)         

            filepath = self.create_unique_filename(filepath, original_path)
            self.logger.info('Unique filepath: %s', filepath)
            self.ensure_is_dir(os.path.dirname(filepath))

        except Exception, e:
            self.logger.error('Exception: %s', e)

        return filepath
        
    def execCommandAndReturnStdOut(self, command):
        p = Popen(command, shell=True, stdout=PIPE)
        stdout = p.communicate()[0]
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
        return stdout
                    
    def scan_dir_for_new_files(self, dir):
        command = 'find "%s" -type f -iname "*.ogg" -o -iname "*.mp3" -readable' % dir.replace('"', '\\"')
        self.logger.debug(command)
        stdout = self.execCommandAndReturnStdOut(command)
        stdout = unicode(stdout, "utf_8")

        return stdout.splitlines() 
        
    def touch_index_file(self):
        open(self.timestamp_file, "w")
        
    def organize_new_file(self, pathname):
        self.logger.info(u"Organizing new file: %s", pathname)
        file_md = self.md_manager.get_md_from_file(pathname)

        if file_md is not None:
            #is_recorded_show = 'MDATA_KEY_CREATOR' in file_md and \
            #    file_md['MDATA_KEY_CREATOR'] == "AIRTIMERECORDERSOURCEFABRIC".encode('utf-8')
            filepath = self.create_file_path(pathname, file_md)
            
            self.logger.debug(u"Moving from %s to %s", pathname, filepath)
            self.move_file(pathname, filepath)
        else:
            filepath = None
            self.logger.warn("File %s, has invalid metadata", pathname)
            
        return filepath
