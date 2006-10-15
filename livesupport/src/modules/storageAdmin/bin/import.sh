#!/bin/bash
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script imports audio files to  Campcaster storageServer.
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
phpdir=ls_storageAdmin_phppart_dir
if [ "$phpdir" == "ls_storageAdmin_phppart_dir" ]
then
    phpdir=`cd $reldir/var; pwd`
fi
filelistpathname=.

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster import script.";
    echo "parameters:";
    echo "";
    echo "  -d, --directory     The source directory, required;";
    echo "                       will be readed recursively.";
    echo "  -l, --list          The filename with list of absolute filepaths";
    echo "                       (newline-separated).";
    echo "  -f, --file          The filename - import of one file";
    echo "  -t, --test          Test only - co not import, show analyze";
    echo "  -h, --help          Print this message and exit.";
    echo "";
    echo "Usage:";
    echo " $0 -d <directory>";
    echo " $0 -l <listfile>";
    echo " $0 -f <filename>";
    echo " $0 -h";
}

#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
unset srcabsdir
unset filelistpathname
unset filepathname
unset phpSw
CMD=${0##*/}

opts=$(getopt -o d:l:f:th -l directory:,list:,file:,test,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            srcdir=$2;
            test -d "$srcdir" || { echo "Directory not found ($srcdir)."; exit 1;  }
            srcabsdir=`cd "$srcdir"; pwd`
            shift; shift;;
        -l|--list)
            filelist=$2;
            test -f "$filelist" || { echo "File not found ($filelist)."; exit 1;  }
            filelistbasename=`basename "$filelist"`
            filelistdir=`dirname "$filelist"`
            filelistabsdir=`cd "$filelistdir"; pwd`
            filelistpathname="$filelistabsdir/$filelistbasename"
            shift; shift;;
        -f|--file)
            file=$2;
            test -f "$file" || { echo "File not found ($file)."; exit 1;  }
            filebasename=`basename "$file"`
            filedir=`dirname "$file"`
            fileabsdir=`cd "$filedir"; pwd`
            filepathname="$fileabsdir/$filebasename"
            shift; shift;;
        -t|--test)
            phpSw="-t"
            shift;;
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

if [ "x$srcabsdir" == "x" -a "x$filelist" == "x" -a "x$file" == "x" ]; then
    echo "Directory, filelist or file required.";
    printUsage;
    exit 1;
fi

#-------------------------------------------------------------------------------
#   Do recursive import
#-------------------------------------------------------------------------------

cd $phpdir

if [ -f "$filelistpathname" ]; then
    cat "$filelistpathname" | php -q import.php $phpSw || exit 1
elif [ -d "$srcabsdir" ]; then
    find "$srcabsdir" -type f | php -q import.php $phpSw || exit 1
elif [ -f "$filepathname" ]; then
    echo "$filepathname" | php -q import.php $phpSw || exit 1
else
    echo "Warning: not a directory: $srcabsdir"
fi

#-------------------------------------------------------------------------------
#   Say goodbye
#-------------------------------------------------------------------------------
echo "Import completed."
