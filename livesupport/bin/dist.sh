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
#   Version  : $Revision: 1.3 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/dist.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script creates a distribution tarball for livesupport.
#  Creates two tarballs:
#  livesupport-<version>.tar.bz2            - the LiveSupport source files
#  livesupport-libraries-<version>.tar.bz2  - dependent libraries
#
#  Invoke as:
#  ./bin/dist.sh <version.number>
#-------------------------------------------------------------------------------

if [ "x$1" == "x" ]; then
    echo "Please provide a version number as the first paramter.";
    exit 0;
fi

version=$1
tarball=livesupport-$version.tar.bz2
tarball_libs=livesupport-libraries-$version.tar.bz2

echo "WARNING! make sure to run this script on a freshly checked-out copy";
echo "         of LiveSupport, with NO generated files!";
echo "";
echo "Creating $tarball and $tarball_libs";

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

ls_tmpdir=$tmpdir/livesupport-$version
tools_tmpdir=$ls_tmpdir/tools
modules_tmpdir=$ls_tmpdir/modules
products_tmpdir=$ls_tmpdir/products
doc_tmpdir=$ls_tmpdir/doc
etc_tmpdir=$ls_tmpdir/etc
tmp_tmpdir=$ls_tmpdir/tmp

boost_dir=$toolsdir/boost
boost_version=boost-1.31
boost_tmpdir=$tools_tmpdir/boost

libxmlxx_dir=$toolsdir/libxml++
libxmlxx_version=libxml++-2.8.1
libxmlxx_tmpdir=$tools_tmpdir/libxml++

cxxunit_dir=$toolsdir/cppunit
cxxunit_version=cppunit-1.10.2
cxxunit_tmpdir=$tools_tmpdir/cppunit

libodbcxx_dir=$toolsdir/libodbc++
libodbcxx_version=libodbc++-0.2.3-20050404
libodbcxx_tmpdir=$tools_tmpdir/libodbc++

xmlrpcxx_dir=$toolsdir/xmlrpc++
xmlrpcxx_version=xmlrpc++-20040713
xmlrpcxx_tmpdir=$tools_tmpdir/xmlrpc++

lcov_dir=$toolsdir/lcov
lcov_version=lcov-1.3
lcov_tmpdir=$tools_tmpdir/lcov

helix_dir=$toolsdir/helix
helix_version=hxclient_1_3_0_neptunex-2004-12-15
helix_tmpdir=$tools_tmpdir/helix

gtk_dir=$toolsdir/gtk+
gtk_version=gtk+-2.6.1
gtk_tmpdir=$tools_tmpdir/gtk+

gtkmm_dir=$toolsdir/gtkmm
gtkmm_version=gtkmm-2.5.5
gtkmm_tmpdir=$tools_tmpdir/gtkmm

icu_dir=$toolsdir/icu
icu_version=icu-3.0
icu_tmpdir=$tools_tmpdir/icu

curl_dir=$toolsdir/curl
curl_version=curl-7.12.3
curl_tmpdir=$tools_tmpdir/curl

taglib_dir=$toolsdir/taglib
taglib_version=taglib-1.3.1
taglib_tmpdir=$tools_tmpdir/taglib


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
mkdir -p $modules_tmpdir
mkdir -p $products_tmpdir


#-------------------------------------------------------------------------------
#  Copy the modules and products
#-------------------------------------------------------------------------------
cp -a $modules_dir/* $modules_tmpdir
cp -a $products_dir/* $products_tmpdir


#-------------------------------------------------------------------------------
#  Copy additional files
#-------------------------------------------------------------------------------
cp -a $docdir $ls_tmpdir
cp -a $etcdir $ls_tmpdir
cp -a README Makefile $ls_tmpdir


#-------------------------------------------------------------------------------
#  Get rid of the remnants of the CVS system
#-------------------------------------------------------------------------------
rm -rf `find $ls_tmpdir -name CVS -type d`


#-------------------------------------------------------------------------------
#  Create the tarball
#-------------------------------------------------------------------------------
cd $tmpdir
tar cfj $basedir/$tarball livesupport-$version
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
#  Copy needed files to the temporary directory
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
#  Copy the tools sources
#-------------------------------------------------------------------------------
mkdir -p $boost_tmpdir
cp -a $boost_dir/$boost_version $boost_tmpdir

mkdir -p $libxmlxx_tmpdir
cp -a $libxmlxx_dir/$libxmlxx_version $libxmlxx_tmpdir

mkdir -p $cxxunit_tmpdir
cp -a $cxxunit_dir/$cxxunit_version $cxxunit_tmpdir

mkdir -p $libodbcxx_tmpdir
cp -a $libodbcxx_dir/$libodbcxx_version $libodbcxx_tmpdir

mkdir -p $xmlrpcxx_tmpdir
cp -a $xmlrpcxx_dir/$xmlrpcxx_version $xmlrpcxx_tmpdir

mkdir -p $lcov_tmpdir
cp -a $lcov_dir/$lcov_version $lcov_tmpdir

mkdir -p $helix_tmpdir
cp -a $helix_dir/$helix_version $helix_tmpdir

mkdir -p $gtk_tmpdir
cp -a $gtk_dir/$gtk_version $gtk_tmpdir

mkdir -p $gtkmm_tmpdir
cp -a $gtkmm_dir/$gtkmm_version $gtkmm_tmpdir

mkdir -p $icu_tmpdir
cp -a $icu_dir/$icu_version $icu_tmpdir

mkdir -p $curl_tmpdir
cp -a $curl_dir/$curl_version $curl_tmpdir

mkdir -p $taglib_tmpdir
cp -a $taglib_dir/$taglib_version $taglib_tmpdir


#-------------------------------------------------------------------------------
#  Get rid of the remnants of the CVS system
#-------------------------------------------------------------------------------
rm -rf `find $ls_tmpdir -name CVS -type d`


#-------------------------------------------------------------------------------
#  Create the libraries tarball
#-------------------------------------------------------------------------------
cd $tmpdir
tar cfj $basedir/$tarball_libs livesupport-$version
cd $basedir


#-------------------------------------------------------------------------------
#  Clean up
#-------------------------------------------------------------------------------
rm -rf $ls_tmpdir


#-------------------------------------------------------------------------------
#  We're done
#-------------------------------------------------------------------------------
echo "Done."

