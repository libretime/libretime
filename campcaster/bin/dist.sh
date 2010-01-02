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
#  This script creates a distribution tarball for Campcaster.
#  Creates two tarballs:
#  campcaster-<version>.tar.bz2            - the Campcaster source files
#  campcaster-libraries-<version>.tar.bz2  - dependent libraries
#
#  Invoke as:
#  ./bin/dist.sh -v <version.number>
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
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
    echo "Campcaster install script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     Place the tarballs in the specified directory.";
    echo "                      [default: the parent of the current directory]";
    echo "  -h, --help          Print this message and exit.";
    echo "  -v, --version       The version number of the created packages.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:hv: -l directory:,help,version: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            directory=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -v|--version)
            version=$2;
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

if [ "x$version" == "x" ]; then
    echo "Required parameter version not specified.";
    printUsage;
    exit 1;
fi

if [ "x$directory" == "x" ]; then
    directory=`pwd`/..;
fi

d=`cd $directory; pwd`
directory=$d


echo "Creating tarballs for Campcaster.";
echo "";
echo "Using the following parameters:";
echo "";
echo "  output directory:       $directory";
echo "  package version number: $version";
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
tarball=$directory/campcaster-$version.tar.bz2
tarball_libs=$directory/campcaster-libraries-$version.tar.bz2

ls_tmpdir=$tmpdir/campcaster-$version
src_tmpdir=$ls_tmpdir/src
tools_tmpdir=$src_tmpdir/tools
modules_tmpdir=$src_tmpdir/modules
products_tmpdir=$src_tmpdir/products
doc_tmpdir=$ls_tmpdir/doc
etc_tmpdir=$ls_tmpdir/etc
tmp_tmpdir=$ls_tmpdir/tmp

libodbcxx_dir=$toolsdir/libodbc++
libodbcxx_version=libodbc++-0.2.5
libodbcxx_tmpdir=$tools_tmpdir/libodbc++

xmlrpcxx_dir=$toolsdir/xmlrpc++
xmlrpcxx_version=xmlrpc++-20040713
xmlrpcxx_tmpdir=$tools_tmpdir/xmlrpc++

taglib_dir=$toolsdir/taglib
taglib_version=taglib-1.5
taglib_tmpdir=$tools_tmpdir/taglib

pear_dir=$toolsdir/pear
pear_tmpdir=$tools_tmpdir/pear

#-------------------------------------------------------------------------------
#  Create the sources tarball first
#-------------------------------------------------------------------------------
echo "Creating $tarball...";


#-------------------------------------------------------------------------------
#  Create the directories again
#-------------------------------------------------------------------------------
mkdir -p $ls_tmpdir
mkdir -p $ls_tmpdir/usr
mkdir -p $tmp_tmpdir
mkdir -p $src_tmpdir
mkdir -p $modules_tmpdir
mkdir -p $products_tmpdir


#-------------------------------------------------------------------------------
#  Copy the modules and products
#-------------------------------------------------------------------------------
cp -pPR $modules_dir/* $modules_tmpdir
cp -pPR $products_dir/* $products_tmpdir


#-------------------------------------------------------------------------------
#  Copy additional files
#-------------------------------------------------------------------------------
cp -pPR $bindir $ls_tmpdir
cp -pPR $docdir $ls_tmpdir
cp -pPR $etcdir $ls_tmpdir
cp -pPR README INSTALL CREDITS configure $ls_tmpdir


#-------------------------------------------------------------------------------
#  Get rid of the remnants of the subversion system
#-------------------------------------------------------------------------------
rm -rf `find $ls_tmpdir -name .svn -type d`


#-------------------------------------------------------------------------------
#  Create the main configure script
#-------------------------------------------------------------------------------
cd $tmpdir/campcaster-$version
./bin/autogen.sh
cd $basedir


#-------------------------------------------------------------------------------
#  Create the tarball
#-------------------------------------------------------------------------------
cd $tmpdir
tar cfj $tarball campcaster-$version
cd $basedir


#-------------------------------------------------------------------------------
#  Create the libraries tarball second
#-------------------------------------------------------------------------------
echo "Creating $tarball_libs...";


#-------------------------------------------------------------------------------
#  Create temprorary directory structure again
#-------------------------------------------------------------------------------
rm -rf $ls_tmpdir
mkdir -p $ls_tmpdir
mkdir -p $tools_tmpdir


#-------------------------------------------------------------------------------
#  Copy the tools sources
#-------------------------------------------------------------------------------
mkdir -p $cxxunit_tmpdir
cp -pPR $cxxunit_dir/$cxxunit_version $cxxunit_tmpdir

mkdir -p $libodbcxx_tmpdir
cp -pPR $libodbcxx_dir/$libodbcxx_version $libodbcxx_tmpdir

mkdir -p $xmlrpcxx_tmpdir
cp -pPR $xmlrpcxx_dir/$xmlrpcxx_version $xmlrpcxx_tmpdir

mkdir -p $taglib_tmpdir
cp -pPR $taglib_dir/$taglib_version $taglib_tmpdir

mkdir -p $pear_tmpdir
cp -pPR $pear_dir/* $pear_tmpdir


#-------------------------------------------------------------------------------
#  Get rid of the remnants of the subversion system
#-------------------------------------------------------------------------------
rm -rf `find $ls_tmpdir -name .svn -type d`


#-------------------------------------------------------------------------------
#  Create the libraries tarball
#-------------------------------------------------------------------------------
cd $tmpdir
tar cfj $tarball_libs campcaster-$version
cd $basedir


#-------------------------------------------------------------------------------
#  Clean up
#-------------------------------------------------------------------------------
rm -rf $ls_tmpdir


#-------------------------------------------------------------------------------
#  We're done
#-------------------------------------------------------------------------------
echo "Done."

