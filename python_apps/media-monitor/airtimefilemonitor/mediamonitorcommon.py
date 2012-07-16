# -*- coding: utf-8 -*-

import os
import grp
import pwd
import logging
import stat
import subprocess
import traceback

from subprocess import Popen, PIPE
from airtimemetadata import AirtimeMetadata
import pyinotify

class MediaMonitorCommon:

    timestamp_file = "/var/tmp/airtime/media-monitor/last_index"
    supported_file_formats = ['mp3', 'ogg']

    def __init__(self, airtime_config, wm=None):
        self.logger = logging.getLogger()
        self.config = airtime_config
        self.md_manager = AirtimeMetadata()
        self.wm = wm


    def clean_dirty_file_paths(self, dirty_files):
        """ clean dirty file paths by removing blanks and removing trailing/leading whitespace"""
        return filter(lambda e: len(e) > 0, [ f.strip(" \n") for f in dirty_files ])

    def find_command(self, directory, extra_arguments=""):
        """ Builds a find command that respects supported_file_formats list
        Note: Use single quotes to quote arguments """
        ext_globs = [ "-iname '*.%s'" % ext for ext in self.supported_file_formats ]
        find_glob = ' -o '.join(ext_globs)
        return "find '%s' %s %s" % (directory, find_glob, extra_arguments)

    def is_parent_directory(self, filepath, directory):
        filepath = os.path.normpath(filepath)
        directory = os.path.normpath(directory)
        return (directory == filepath[0:len(directory)])

    def is_temp_file(self, filename):
        info = filename.split(".")
        # if file doesn't have any extension, info[-2] throws exception
        # Hence, checking length of info before we do anything
        if(len(info) >= 2):
            return info[-2].lower() in self.supported_file_formats
        else:
            return False

    def is_audio_file(self, filename):
        info = filename.split(".")
        if len(info) < 2: return False # handle cases like filename="mp3"
        return info[-1].lower() in self.supported_file_formats

    #check if file is readable by "nobody"
    def is_user_readable(self, filepath, euid='nobody', egid='nogroup'):
        f = None
        try:
            uid = pwd.getpwnam(euid)[2]
            gid = grp.getgrnam(egid)[2]
            #drop root permissions and become "nobody"
            os.setegid(gid)
            os.seteuid(uid)
            f = open(filepath)
            readable = True
        except IOError:
            self.logger.warn("File does not have correct permissions: '%s'", filepath)
            readable = False
        except Exception, e:
            self.logger.error("Unexpected exception thrown: %s", e)
            readable = False
            self.logger.error("traceback: %s", traceback.format_exc())
        finally:
            #reset effective user to root
            if f: f.close()
            os.seteuid(0)
            os.setegid(0)
        return readable

    # the function only changes the permission if its not readable by www-data
    def is_readable(self, item, is_dir):
        try:
            return self.is_user_readable(item, 'www-data', 'www-data')
        except Exception:
            self.logger.warn(u"Failed to check owner/group/permissions for %s", item)
            return False

    def make_file_readable(self, pathname, is_dir):
        if is_dir:
            #set to 755
            os.chmod(pathname, stat.S_IRUSR | stat.S_IWUSR | stat.S_IXUSR | stat.S_IRGRP | stat.S_IXGRP | stat.S_IROTH | stat.S_IXOTH)
        else:
            #set to 644
            os.chmod(pathname, stat.S_IRUSR | stat.S_IWUSR | stat.S_IRGRP | stat.S_IROTH)

    def make_readable(self, pathname):
        """
        Should only call this function if is_readable() returns False. This function
        will attempt to make the file world readable by modifying the file's permission's
        as well as the file's parent directory permissions. We should only call this function
        on files in Airtime's stor directory, not watched directories!

        Returns True if we were able to make the file world readable. False otherwise.
        """
        original_file = pathname
        is_dir = False
        try:
            while not self.is_readable(original_file, is_dir):
                #Not readable. Make appropriate permission changes.
                self.make_file_readable(pathname, is_dir)

                dirname = os.path.dirname(pathname)
                if dirname == pathname:
                    #most likey reason for this is that we've hit '/'. Avoid infinite loop by terminating loop
                    raise Exception()
                else:
                    pathname = dirname
                    is_dir = True
        except Exception:
            #something went wrong while we were trying to make world readable.
            return False

        return True

    #checks if path is a directory, and if it doesnt exist, then creates it.
    #Otherwise prints error to log file.
    def ensure_is_dir(self, directory):
        try:
            omask = os.umask(0)
            if not os.path.exists(directory):
                os.makedirs(directory, 02777)
                self.wm.add_watch(directory, pyinotify.ALL_EVENTS, rec=True, auto_add=True)
            elif not os.path.isdir(directory):
                #path exists but it is a file not a directory!
                self.logger.error(u"path %s exists, but it is not a directory!!!", directory)
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
            self.logger.error("traceback: %s", traceback.format_exc())
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
                try:
                    os.rmdir(dir)
                    self.cleanup_empty_dirs(os.path.dirname(dir))
                except Exception:
                    #non-critical exception because we probably tried to delete a non-empty dir.
                    #Don't need to log this, let's just "return"
                    pass



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
                        i = i + 1;
                    else:
                        filepath = new_filepath
                        break

        except Exception, e:
            self.logger.error("Exception %s", e)

        return filepath

    #create path in /srv/airtime/stor/imported/[song-metadata]
    def create_file_path(self, original_path, orig_md):

        storage_directory = self.config.storage_directory
        try:
            #will be in the format .ext
            file_ext = os.path.splitext(original_path)[1].lower()
            path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE', 'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']

            md = {}
            for m in path_md:
                if m not in orig_md:
                    md[m] = u'unknown'
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
            md['MDATA_KEY_BITRATE'] = str(md['MDATA_KEY_BITRATE'] / 1000) + "kbps"

            filepath = None
            #file is recorded by Airtime
            #/srv/airtime/stor/recorded/year/month/year-month-day-time-showname-bitrate.ext
            if(md['MDATA_KEY_CREATOR'] == u"Airtime Show Recorder"):
                #yyyy-mm-dd-hh-MM-ss
                y = orig_md['MDATA_KEY_YEAR'].split("-")
                filepath = u'%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "recorded", y[0], y[1], orig_md['MDATA_KEY_YEAR'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)

                #"Show-Title-2011-03-28-17:15:00"
                title = md['MDATA_KEY_TITLE'].split("-")
                show_hour = title[0]
                show_min = title[1]
                show_sec = title[2]
                show_name = '-'.join(title[3:])

                new_md = {}
                new_md['MDATA_KEY_FILEPATH'] = os.path.normpath(original_path)
                new_md['MDATA_KEY_TITLE'] = '%s-%s-%s:%s:%s' % (show_name, orig_md['MDATA_KEY_YEAR'], show_hour, show_min, show_sec)
                self.md_manager.save_md_to_file(new_md)

            elif(md['MDATA_KEY_TRACKNUMBER'] == u'unknown'):
                filepath = u'%s/%s/%s/%s/%s-%s%s' % (storage_directory, "imported", md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            else:
                filepath = u'%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "imported", md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TRACKNUMBER'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)

            filepath = self.create_unique_filename(filepath, original_path)
            self.logger.info('Unique filepath: %s', filepath)
            self.ensure_is_dir(os.path.dirname(filepath))

        except Exception, e:
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", traceback.format_exc())

        return filepath

    def exec_command(self, command):
        p = Popen(command, shell=True, stdout=PIPE, stderr=PIPE)
        stdout, stderr = p.communicate()
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
            self.logger.error(stderr)

        try:
            """
            File name charset encoding is UTF-8.
            """
            stdout = stdout.decode("UTF-8")
        except Exception:
            stdout = None
            self.logger.error("Could not decode %s using UTF-8" % stdout)

        return stdout

    def scan_dir_for_new_files(self, dir):
        command = self.find_command(directory=dir, extra_arguments="-type f -readable")
        self.logger.debug(command)
        stdout = self.exec_command(command)

        if stdout is None:
            return []
        else:
            return stdout.splitlines()

    def touch_index_file(self):
        dirname = os.path.dirname(self.timestamp_file)
        try:
            if not os.path.exists(dirname):
                os.makedirs(dirname)
            open(self.timestamp_file, "w")
        except Exception, e:
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", traceback.format_exc())

    def organize_new_file(self, pathname, file_md):
        self.logger.info("Organizing new file: %s", pathname)

        filepath = self.create_file_path(pathname, file_md)

        self.logger.debug(u"Moving from %s to %s", pathname, filepath)
        self.move_file(pathname, filepath)
        self.make_readable(filepath)
        return filepath

    def test_file_playability(self, pathname):
        #when there is an single apostrophe inside of a string quoted by apostrophes, we can only escape it by replace that apostrophe
        #with '\''. This breaks the string into two, and inserts an escaped single quote in between them.
        #We run the command as pypo because otherwise the target file is opened with write permissions, and this causes an inotify ON_CLOSE_WRITE event
        #to be fired :/
        command = "sudo -u pypo airtime-liquidsoap -c 'output.dummy(audio_to_stereo(single(\"%s\")))' > /dev/null 2>&1" % pathname.replace("'", "'\\''")
        return_code = subprocess.call(command, shell=True)
        if return_code != 0:
            #print pathname for py-interpreter.log
            print pathname
        return (return_code == 0)

    def move_to_problem_dir(self, source):
        dest = os.path.join(self.config.problem_directory, os.path.basename(source))
        try:
            omask = os.umask(0)
            os.rename(source, dest)
        except Exception, e:
            self.logger.error("failed to move file. %s", e)
            self.logger.error("traceback: %s", traceback.format_exc())
        finally:
            os.umask(omask)

