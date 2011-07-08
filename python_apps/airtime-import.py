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
    dest = get_stor_dir()+"/organize/"
    copy_or_move_files_to(args.path, dest, 'copy')

def import_move(args):
    dest = get_stor_dir()+"/organize/"
    copy_or_move_files_to(args.path, dest, 'move')

def watch_add(args):
    if(os.path.isdir(args.path)):
        res = api_client.add_watched_dir(args.path)
        # sucess
        if(res == '[]'):
            print "%s added to watched folder list successfully" % args.path
        else:
            print "Adding %s to watched folder list failed.( path already exist in the list )" % args.path
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
        if(res == '[]'):
            print "%s removed from watched folder list successfully" % args.path
        else:
            print "Removing %s from watched folder list failed.( path doesn't exist in the list )" % args.path
    else:
        print "Given path is not a directory: %s" % args.path

def set_stor_dir(args):
    if(os.path.isdir(args.path)):
        res = api_client.set_storage_dir(args.path)
        print res
        # sucess
        if(res == '[]'):
            print "Successfully set storage folder to %s" % args.path
        else:
            print "Setting storage folder to  %s failed." % args.path
    else:
        print "Given path is not a directory: %s" % args.path
    
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
            
def get_stor_dir():
    res = api_client.list_all_watched_dirs()
    return res['dirs']['1']
    
parser = argparse.ArgumentParser(description="This script let you do following operations- imports files\n- add/remove/list watch folders\n- set default storage folder", formatter_class=RawTextHelpFormatter)
subparsers = parser.add_subparsers()

# for subcommand copy
parser_copy = subparsers.add_parser('copy', help='copy file')
parser_copy.add_argument('path', nargs='+', help='path to the file or directory')
parser_copy.set_defaults(func=import_copy)

# for subcommand move
parser_move = subparsers.add_parser('move', help='move file')
parser_move.add_argument('path', nargs='+', help='path to the file or directory')
parser_move.set_defaults(func=import_move)

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
parser_set_stor_dir = subparsers.add_parser('set-storage-dir', help='operations on watch directory')
parser_set_stor_dir.add_argument('-f', '--force', action='store_true', help='bypass confirmation')
parser_set_stor_dir.add_argument('path', help='path to the directory')
parser_set_stor_dir.set_defaults(func=set_stor_dir)

args = parser.parse_args()
#format args.path
if(hasattr(args,'path')):
    if(args.path[-1] != '/'):
        args.path = args.path + '/' 
args.func(args)



