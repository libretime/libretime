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
#   Author   : $Author: tomash $
#   Version  : $Revision: 1847 $
#   Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/src/modules/storageAdmin/bin/backup.sh $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  Playlist-to-file renderer caller. DUMMY VERSION.
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Playlist-to-file renderer caller. DUMMY VERSION.";
    echo "parameters:";
    echo "";
    echo "  -p, --playlist      URL of SMIL playlist to be rendered.";
    echo "  -s, --statusfile    Status file name.";
    echo "  -o, --output        File name where the output will be written.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}

#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o hp:s:o: -l help,playlist:,statusfile:,output: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -h|--help)
            printUsage;
            exit 0;;
        -p|--playlist)
            playlist=$2
            shift; shift;;
        -s|--statusfile)
            statusfile=$2
            shift; shift;;
        -o|--output)
            output=$2
            shift; shift;;
        --)
            shift;
            break;;
        *)
            echo "Unrecognized option $1.";
            printUsage;
            exit 1;
    esac
done

if [ "x$playlist" == "x" ]; then
    echo "Error in playlist parameter";
    printUsage;
    exit 1;
fi
if [ "x$statusfile" == "x" ]; then
    echo "Error in statusfile parameter";
    printUsage;
    exit 1;
fi
if [ "x$output" == "x" ]; then
    echo "Error in output parameter";
    printUsage;
    exit 1;
fi

#-------------------------------------------------------------------------------
#   Do it
#-------------------------------------------------------------------------------
echo "renderer.sh: rendering $playlist to $output"
echo "working" > $statusfile;
touch $output || { echo "fail" > $statusfile; exit 1; }
#sleep 4
#sleep 2
echo -e "$playlist\n$output" >> $output || { echo "fail" > $statusfile; exit 1; }

echo "success" > $statusfile

#-------------------------------------------------------------------------------
#   Say goodbye
#-------------------------------------------------------------------------------
echo "done"
exit 0
