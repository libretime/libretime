#!/usr/local/bin/python
import sys
import os
import logging
from configobj import ConfigObj
import argparse
from argparse import RawTextHelpFormatter
from api_clients import api_client
import json
import shutil

# create logger
logger = logging.getLogger()

# no logging
ch = logging.NullHandler()

# add ch to logger
logger.addHandler(ch)


# loading config file
try:
    config = ConfigObj('/etc/airtime/media-monitor.cfg')
except Exception, e:
    print('Error loading config file: %s', e)
    sys.exit()

api_client = api_client.api_client_factory(config)

# action call back classes
class CopyAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        stor = helper_get_stor_dir()
        if(stor is None):
            exit("Unable to connect to the server.")
        dest = helper_get_stor_dir()+"organize/"
        copy_or_move_files_to(values, dest, 'copy')

class MoveAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        stor = helper_get_stor_dir()
        if(stor is None):
            exit("Unable to connect to the server.")
        dest = helper_get_stor_dir()+"organize/"
        copy_or_move_files_to(args.path, dest, 'move')

class WatchAddAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        if(os.path.isdir(values)):
            res = api_client.add_watched_dir(values)
            if(res is None):
                exit("Unable to connect to the server.")
            # sucess
            if(res['msg']['code'] == 0):
                print "%s added to watched folder list successfully" % values
            else:
                print "Adding a watched folder failed. : %s" % res['msg']['error']
        else:
            print "Given path is not a directory: %s" % values
        
class WatchListAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        res = api_client.list_all_watched_dirs()
        if(res is None):
            exit("Unable to connect to the server.")
        dirs = res["dirs"].items()
        # there will be always 1 which is storage folder
        if(len(dirs) == 1):
                print "No watch folders found"
        else:
            for key, value in dirs:
                if(key != '1'):
                    print value

class WatchRemoveAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        if(os.path.isdir(values)):
            res = api_client.remove_watched_dir(values)
            if(res is None):
                exit("Unable to connect to the server.")
            # sucess
            if(res['msg']['code'] == 0):
                print "%s removed from watched folder list successfully" % values
            else:
                print "Removing a watched folder failed. : %s" % res['msg']['error']
        else:
            print "Given path is not a directory: %s" % values
            
class StorageSetAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        if(os.path.isdir(values)):
            res = api_client.set_storage_dir(values)
            if(res is None):
                exit("Unable to connect to the server.")
            # sucess
            if(res['msg']['code'] == 0):
                print "Successfully set storage folder to %s" % values
            else:
                print "Setting storage folder to failed.: %s" % res['msg']['error']
        else:
            print "Given path is not a directory: %s" % values

class StorageGetAction(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        print helper_get_stor_dir()

#helper functions
# copy or move files
# flag should be 'copy' or 'move'
def copy_or_move_files_to(paths, dest, flag):
    for path in paths:
        if(os.path.exists(path)):
            if(os.path.isdir(path)):
                #construct full path
                sub_path = []
                for temp in os.listdir(path):
                    sub_path.append(path+temp)
                copy_or_move_files_to(sub_path, dest, flag)
            elif(os.path.isfile(path)):
                #copy file to dest
                ext = os.path.splitext(path)[1]
                if( 'mp3' in ext or 'ogg' in ext ):
                    destfile = dest+os.path.basename(path)
                    if(flag == 'copy'):
                        print "Copying %(src)s to %(dest)s....." % {'src':path, 'dest':destfile}
                        shutil.copy2(path, destfile)
                    elif(flag == 'move'):
                        print "Moving %(src)s to %(dest)s....." % {'src':path, 'dest':destfile}
                        shutil.move(path, destfile)
        else:
            print "Cannot find file or path: %s" % path
            
def helper_get_stor_dir():
    res = api_client.list_all_watched_dirs()
    if(res is None):
        return res
    else:
        return res['dirs']['1']

storage_dir = helper_get_stor_dir()
if(storage_dir is None):
    storage_dir = "Unknown" 
else:
    storage_dir += "imported/"
help_text = """
    ========================
    Airtime Import Script
    ========================
    There are two ways to import audio files into Airtime:

    1) Copy or move files into the storage folder

       Copied or moved files will be placed into the folder:
       %s
        
       Files will be automatically organized into the structure
       "Artist/Album/TrackNumber-TrackName-Bitrate.file_extension".

    2) Add a folder to the Airtime library("watch" a folder)
    
       All the files in the watched folder will be imported to Airtime and the
       folder will be monitored to automatically detect any changes. Hence any
       changes done in the folder(add, delete, edit a file) will trigger 
       updates in Airtime libarary.
""" % storage_dir
parser = argparse.ArgumentParser(description=help_text, formatter_class=RawTextHelpFormatter, epilog="  ")#"This script let you do following operations\n- import files\n- add/remove/list watch folders\n- set default storage folder", formatter_class=RawTextHelpFormatter)
# for subcommand move
parser.add_argument('-c','--copy', nargs='+', metavar='FILE', action=CopyAction, help='Copy FILE(s) into the storage directory.\nYou can specify multiple files or directories.')
parser.add_argument('-m','--move', nargs='+', metavar='FILE', action=MoveAction, help='Move FILE(s) into the storage directory.\nYou can specify multiple files or directories.')
parser.add_argument('--watch-add', metavar='DIR', action=WatchAddAction, help='Add DIR to the watched folders list.')
parser.add_argument('--watch-list', nargs=0, action=WatchListAction, help='Show the list of folders that are watched.')
parser.add_argument('--watch-remove', metavar='DIR', action=WatchRemoveAction, help='Remove DIR from the watched folders list.')
parser.add_argument('--storage-dir-set', metavar='DIR', action=StorageSetAction, help='Set storage dir to DIR.')
parser.add_argument('--storage-dir-get', nargs=0, metavar='DIR', action=StorageGetAction, help='Show the current storage dir.')

if('-l' in sys.argv or '--link' in sys.argv):
    print "\nThe [-l][--link] option is deprecated. Please use the --watch-add option.\nTry 'airtime-import -h' for more detail.\n"
else:
    args = parser.parse_args()




