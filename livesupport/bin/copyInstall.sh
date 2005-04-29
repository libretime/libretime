#!/bin/sh
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
#   Author   : $Author: maroy $
#   Version  : $Revision: 1.4 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/Attic/copyInstall.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script installs LiveSupport files to their installation location.
#
#  Invoke as:
#  ./bin/copyInstall.sh
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
docdir=$basedir/doc
tmpdir=$basedir/tmp
toolsdir=$basedir/tools
modules_dir=$basedir/modules
products_dir=$basedir/products

usrdir=`cd $basedir/usr; pwd;`


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport install copy script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:h -l directory:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            installdir=$2;
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

if [ "x$installdir" == "x" ]; then
    echo "Required parameter install directory not specified.";
    printUsage;
    exit 1;
fi


echo "Installing LiveSupport files.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  installation directory: $installdir";
echo ""


#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
install_bin=$installdir/bin
install_etc=$installdir/etc
install_lib=$installdir/lib
install_tmp=$installdir/tmp
install_var=$installdir/var


#-------------------------------------------------------------------------------
#  Create the installation directory structure
#-------------------------------------------------------------------------------
echo "Copying files..."

mkdir -p $installdir
mkdir -p $install_bin
mkdir -p $install_etc
mkdir -p $install_lib
mkdir -p $install_tmp
mkdir -p $install_var


#-------------------------------------------------------------------------------
#  Copy the PHP files
#-------------------------------------------------------------------------------
mkdir $install_var/getid3
cp -pPR $modules_dir/getid3/var $install_var/getid3

mkdir $install_var/alib
cp -pPR $modules_dir/alib/var $install_var/alib

mkdir $install_var/storageServer
cp -pPR $modules_dir/storageServer/var $install_var/storageServer

mkdir $install_var/archiveServer
cp -pPR $modules_dir/archiveServer/var $install_var/archiveServer

mkdir $install_var/htmlUI
cp -pPR $modules_dir/htmlUI/var $install_var/htmlUI


#-------------------------------------------------------------------------------
#  Copy libraries and related files
#-------------------------------------------------------------------------------
cp -pPR $usrdir/lib/* $install_lib
cp -pPR $usrdir/etc/* $install_etc


#-------------------------------------------------------------------------------
#  Copy scheduler related files
#-------------------------------------------------------------------------------
cp -pPR $products_dir/scheduler/tmp/scheduler $install_bin
cp -pPR $products_dir/scheduler/bin/scheduler.sh $install_bin


#-------------------------------------------------------------------------------
#  Copy gLiveSupport related files
#-------------------------------------------------------------------------------
cp -pPR $products_dir/gLiveSupport/tmp/gLiveSupport $install_bin
cp -pPR $products_dir/gLiveSupport/bin/gLiveSupport.sh $install_bin
cp -pPR $products_dir/gLiveSupport/var/widgets $install_var
cp -pPR $products_dir/gLiveSupport/tmp/gLiveSupport*.res $install_var


#-------------------------------------------------------------------------------
#  Copy post-installation configuration related files
#-------------------------------------------------------------------------------
cp -pPR $bindir/postInstallScheduler.sh $install_bin
cp -pPR $bindir/postInstallGLiveSupport.sh $install_bin
cp -pPR $products_dir/scheduler/etc/scheduler.xml.template $install_etc
cp -pPR $products_dir/scheduler/etc/odbcinst_template $install_etc
cp -pPR $products_dir/scheduler/etc/odbcinst_debian_template $install_etc
cp -pPR $products_dir/scheduler/etc/odbc_template $install_etc
cp -pPR $products_dir/gLiveSupport/etc/gLiveSupport.xml.template $install_etc


#-------------------------------------------------------------------------------
#  Clean up remnants of the CVS system
#-------------------------------------------------------------------------------
rm -rf `find $install_var -type d -name CVS`


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

