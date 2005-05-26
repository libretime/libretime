#!/bin/bash
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
#   http://livesupport.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   LiveSupport is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   LiveSupport is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with LiveSupport; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author: tomas $
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageAdmin/bin/import.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script imports audio files to  LiveSupport storageServer.
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
phpdir=`cd $reldir/bin/php; pwd`
filelistpathname=.

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport import script.";
    echo "parameters:";
    echo "";
    echo "  -d, --directory     The source directory, required;";
    echo "                       will be readed recursively.";
    echo "  -l, --list          The filename with list of absolute filepaths";
    echo "                       (newline-separated).";
    echo "  -h, --help          Print this message and exit.";
    echo "";
    echo "Usage:";
    echo " $0 -d <directory>";
    echo " $0 -l <listfile>";
    echo " $0 -h";
}

#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:l:h -l directory:,list:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            srcdir=$2;
            srcabsdir=`cd "$srcdir"; pwd`
            shift; shift;;
        -l|--list)
            filelist=$2;
            filelistbasename=`basename "$filelist"`
            filelistdir=`dirname "$filelist"`
            filelistabsdir=`cd "$filelistdir"; pwd`
            filelistpathname=$filelistabsdir/$filelistbasename
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        --)
            shift;
            break;;
        *)
            echo "Unrecognized option $1.";
            printUsage;
            exit 1;
    esac
done

if [ "x$srcabsdir" == "x" -a "x$filelist" == "x" ]; then
    echo "Directory or filelist option required.";
    printUsage;
    exit 1;
fi

#-------------------------------------------------------------------------------
#   Do recursive import
#-------------------------------------------------------------------------------

cd $phpdir

if [ -f "$filelistpathname" ]; then
    cat "$filelistpathname" | php -q import.php || exit 1
fi

if [ -d "$srcabsdir" ]; then
    find "$srcabsdir" -type f | php -q import.php || exit 1
else
    echo "Warning: not a directory: $srcabsdir"
fi

#-------------------------------------------------------------------------------
#   Say goodbye
#-------------------------------------------------------------------------------
echo "Import completed."
