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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageAdmin/bin/backup.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script creates the tar archiv with backup of LS data
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir/var; pwd`
phpdir=`cd $reldir/var; pwd`
mkdir -p $reldir/tmp
tmpmaindir=`cd $reldir/tmp; pwd`
dbxml="db.xml"
datestr=`date '+%Y%m%d%H%M%S'`
tarfile0="xmls.tar"
tarfile="storage$datestr.tar"

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "This script creates the tgz archiv with backup of LS data.";
    echo "parameters:";
    echo "";
    echo "  -d, --destination   Destination directory [default:$tmpmaindir].";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}

#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o hd: -l help,destinantion: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -h|--help)
            printUsage;
            exit 0;;
        -d|--destinantion)
            destdir=$2
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

if [ "x$destdir" == "x" ]; then
    destdir=$tmpmaindir
fi
destdir=`cd $destdir; pwd`

#-------------------------------------------------------------------------------
#   Do backup
#-------------------------------------------------------------------------------

tmpdir=`mktemp -dp $tmpmaindir`

echo "Backuping to $destdir/$tarfile :"
echo "Dumping database  ..."
cd $phpdir
php -q backup.php > $tmpdir/$dbxml
echo "Packaging stored files ..."
cd $phpdir
storpath=`php -q getStorPath.php`
cd $storpath/..
find stor -name "*.xml" -print | tar cf $tmpdir/$tarfile0 -T -
find stor ! -name "*.xml" -a -type f -print | tar cf $tmpdir/$tarfile -T -
cd $tmpdir
tar rf $tarfile0 $dbxml --remove-files
echo "Compressing XML part ..."
bzip2 $tarfile0
tar rf $tarfile $tarfile0.bz2 --remove-files
mv $tarfile "$destdir"
rm -rf $tmpdir

#-------------------------------------------------------------------------------
#   Say goodbye
#-------------------------------------------------------------------------------
echo "done"
