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
#   Author   : $Author: fgerlits $
#   Version  : $Revision: 2292 $
#   Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/bin/postInstallStation.sh $
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#  This script creates a distribution tarball for Campcaster network hub.
#  (campcaster-aserver-<version>.tar.bz2)
#
#  Invoke as:
#  ./bin/makeArchiveServerTar.sh -v <version.number>
#
#  To get usage help, try the -h option
#
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
#reldir=`dirname $0`/..
reldir=`pwd`
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
docdir=$basedir/doc
srcdir=$basedir/src
tmpdir=$basedir/tmp
toolsdir=$srcdir/tools
modules_dir=$srcdir/modules
products_dir=$srcdir/products

usrdir=`cd $basedir/usr; pwd;`

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster network hub tar package creator.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     Place the tarball in the specified directory.";
    echo "                      [default: current directory]";
    echo "  -v, --version       The version number of the created package.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:v:h -l lspath:,output:,version:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            directory=$2;
            shift; shift;;
        -v|--version)
            version=$2;
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

if [ "x$directory" == "x" ]; then
    directory=`pwd`;
fi

if [ "x$version" == "x" ]; then
    echo "Required parameter version not specified.";
    printUsage;
    exit 1;
fi

echo "Creating Campcaster network hub tar.gz package.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  LS directory:           $lspath";
echo "  output directory:       $directory";
echo ""

#-------------------------------------------------------------------------------
#   Check if there are generated files, and bail out if so
#-------------------------------------------------------------------------------
if [ -f $basedir/Makefile ]; then
    echo "ERROR: make sure to run this script on a freshly checked-out copy";
    echo "       of Campcaster, with NO generated files!";
    exit 1;
fi

#-------------------------------------------------------------------------------
#   More definitions
#-------------------------------------------------------------------------------
tarball=$directory/campcaster-aserver-$version.tar.bz2

ls_tmpdir=$tmpdir/campcaster-$version
src_tmpdir=$ls_tmpdir/src
tools_tmpdir=$src_tmpdir/tools
modules_tmpdir=$src_tmpdir/modules
products_tmpdir=$src_tmpdir/products
bin_tmpdir=$ls_tmpdir/bin
doc_tmpdir=$ls_tmpdir/doc
etc_tmpdir=$ls_tmpdir/etc
tmp_tmpdir=$ls_tmpdir/tmp

#-------------------------------------------------------------------------------
#  Function to check for the existence of an executable on the PATH
#
#  @param $1 the name of the exectuable
#  @return 0 if the executable exists on the PATH, non-0 otherwise
#-------------------------------------------------------------------------------
check_exe() {
    if [ -x "`which $1 2> /dev/null`" ]; then
        #echo "Executable $1 found...";
        return 0;
    else
        echo "Executable $1 not found...";
        return 1;
    fi
}

COMMENT='
#-------------------------------------------------------------------------------
#  Check to see if this script is being run as root
#-------------------------------------------------------------------------------
if [ `whoami` != "root" ]; then
    echo "Please run this script as root.";
    exit ;
fi
'

#-------------------------------------------------------------------------------
#  Check for required tools
#-------------------------------------------------------------------------------
echo "Checking for required tools..."

check_exe "tar" || exit 1;
check_exe "bzip2" || exit 1;

#-------------------------------------------------------------------------------
#  Create the directories again
#-------------------------------------------------------------------------------
echo "Creating tmp directories and copying files ..."

mkdir -p $ls_tmpdir
mkdir -p $src_tmpdir
mkdir -p $modules_tmpdir
mkdir -p $tools_tmpdir
mkdir -p $bin_tmpdir
mkdir -p $etc_tmpdir/apache

#-------------------------------------------------------------------------------
#  Copy the modules and tools
#-------------------------------------------------------------------------------
#cp -pPR $modules_dir/* $modules_tmpdir
for it in alib getid3 storageServer storageAdmin archiveServer; do
    cp -pPR $modules_dir/$it $modules_tmpdir
done
for it in pear; do
    cp -pPR $toolsdir/$it $tools_tmpdir
done
for it in preInstall.sh archiveServerSetup.sh; do
    cp -pPR $bindir/$it $bin_tmpdir
done
cp -pPR $etcdir/apache/* $etc_tmpdir/apache
for it in pg_hba.conf; do
    cp -pPR $etcdir/$it $etc_tmpdir
done

#-------------------------------------------------------------------------------
#  Copy additional files
#-------------------------------------------------------------------------------
#cp -pPR $bindir $ls_tmpdir
#cp -pPR $etcdir $ls_tmpdir
cp -pPR README INSTALL configure $ls_tmpdir

#-------------------------------------------------------------------------------
#  Get rid of the remnants of the subversion system
#-------------------------------------------------------------------------------
# Paul Baranowski: you dont need to do this when you export from SVN.
#rm -rf `find $ls_tmpdir -name .svn -type d`

#-------------------------------------------------------------------------------
#  Create the tarball
#-------------------------------------------------------------------------------
echo "Creating $tarball ...";
cd $tmpdir
tar cjf $tarball campcaster-$version
cd $basedir

#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

