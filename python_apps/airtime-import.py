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

# create console handler and set level to debug
ch = logging.StreamHandler()

# create formatter
formatter = logging.Formatter('%(asctime)s %(levelname)s - [%(filename)s : %(funcName)s() : line %(lineno)d] - %(message)s')

# add formatter to ch
ch.setFormatter(formatter)

# add ch to logger
logger.addHandler(ch)


# loading config file
try:
    config = ConfigObj('/etc/airtime/media-monitor.cfg')
except Exception, e:
    print('Error loading config file: %s', e)
    sys.exit()

api_client = api_client.api_client_factory(config)

def import_copy(args):
    dest = helper_get_stor_dir()+"/organize/"
    copy_or_move_files_to(args.path, dest, 'copy')

def import_move(args):
    dest = helper_get_stor_dir()+"/organize/"
    copy_or_move_files_to(args.path, dest, 'move')

def watch_add(args):
    if(os.path.isdir(args.path)):
        res = api_client.add_watched_dir(args.path)
        # sucess
        if(res['msg']['code'] == 0):
            print "%s added to watched folder list successfully" % args.path
        else:
            print "Adding a watched folder failed. : %s" % res['msg']['error']
    else:
        print "Given path is not a directory: %s" % args.path

def watch_list(args):
    res = api_client.list_all_watched_dirs()
    dirs = res["dirs"].items()
    # there will be always 1 which is storage folder
    if(len(dirs) == 1):
            print "No watch folders found"
    else:
        for key, value in dirs:
            if(key != '1'):
                print value


def watch_remove(args):
    if(os.path.isdir(args.path)):
        res = api_client.remove_watched_dir(args.path)
        # sucess
        if(res['msg']['code'] == 0):
            print "%s removed from watched folder list successfully" % args.path
        else:
            print "Removing a watched folder failed. : %s" % res['msg']['error']
    else:
        print "Given path is not a directory: %s" % args.path

def set_stor_dir(args):
    if(os.path.isdir(args.path)):
        res = api_client.set_storage_dir(args.path)
        # sucess
        if(res['msg']['code'] == 0):
            print "Successfully set storage folder to %s" % args.path
        else:
            print "Setting storage folder to failed.: %s" % res['msg']['error']
    else:
        print "Given path is not a directory: %s" % args.path
    
def get_stor_dir(args):
    print helper_get_stor_dir()
#helper functions

# copy or move files
# falg should be 'copy' or 'move'
def copy_or_move_files_to(paths, dest, flag):
    for path in paths:
        if(os.path.exists(path)):
            if(os.path.isdir(path)):
                #construc full path
                sub_path = []
                print path
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
    return res['dirs']['1']
    
parser = argparse.ArgumentParser(description="This script let you do following operations\n- import files\n- add/remove/list watch folders\n- set default storage folder", formatter_class=RawTextHelpFormatter)
# for subcommand move
parser.add_argument('-c','--copy', action='store_true', help='copy file(deprecated. Use "copy" sub-command)')
parser.add_argument('-l','--link', action='store_true', help='link file(deprecated. Use "watch" sub-command)')
subparsers = parser.add_subparsers(help='sub-command help')

# for subcommand copy
parser_copy = subparsers.add_parser('copy', help='copy file')
parser_copy.add_argument('path', nargs='+', help='path to the file or directory')
parser_copy.set_defaults(func=import_copy)

# for subcommand move
parser_move = subparsers.add_parser('move', help='move file')
parser_move.add_argument('path', nargs='+', help='path to the file or directory')
parser_move.set_defaults(func=import_move)

#parser_deprecated1 = subparsers.add_parser('-c', help='copy file')

# for subcommand watch
parser_watch = subparsers.add_parser('watch', help='operations on watch directory')
watch_subparsers = parser_watch.add_subparsers()
parser_add = watch_subparsers.add_parser('add', help='add a folder to the watch list')
parser_list = watch_subparsers.add_parser('list', help='list watch folders')
parser_remove = watch_subparsers.add_parser('remove', help='remove a folder from the watch list')
parser_add.add_argument('path', help='path to the directory')
parser_remove.add_argument('path', help='path to the directory')
parser_remove.set_defaults(func=watch_remove)
parser_add.set_defaults(func=watch_add)
parser_list.set_defaults(func=watch_list)

# for subcommand set-storage-dir
parser_stor_dir = subparsers.add_parser('storage-dir', help='operations on storage directory')
storage_subparsers = parser_stor_dir.add_subparsers()
parser_set = storage_subparsers.add_parser('set', help='set a storage directory')
parser_get = storage_subparsers.add_parser('get', help='get the current storage directory')
parser_set.add_argument('-f', '--force', action='store_true', help='bypass confirmation')
parser_set.add_argument('path', help='path to the directory')
parser_set.set_defaults(func=set_stor_dir)
parser_get.set_defaults(func=get_stor_dir)


if ("-c" in sys.argv or "-copy" in sys.argv or "-l" in sys.argv or "-link" in sys.argv):
    args = parser.parse_args(['-h'])
else:
    args = parser.parse_args()
print args
#format args.path
if(hasattr(args,'path')):
    if(args.path[-1] != '/'):
        args.path = args.path + '/' 
args.func(args)



