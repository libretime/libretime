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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/helix/all_clients_nodist-helix-20041010/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install the helix libraries into the development system usr
# environment.
#-------------------------------------------------------------------------------
product=all_clients_nodist-helix-20041010

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd; cd -`
installdir=`cd $basedir/../../../usr; pwd; cd -`
tmpdir=$basedir/tmp
etcdir=$basedir/etc
tar=$basedir/src/$product-source.zip

echo "installing $product from $basedir to $installdir"


mkdir -p $tmpdir
cd $tmpdir

unzip $tar
cd $product

# usr the Helix build tool to compile the libraries
# for some reason, despite the -k flag, this script will try to connect
# to the Helix CVS server.
# see https://bugs.helixcommunity.org/show_bug.cgi?id=3309
export BUILD_ROOT=$tmpdir/$product/build
./build/bin/build -m helix -trelease -k -P helix-client-all-defines-nodist splay

# copy all the necessary files manually
helix_include_dir=$installdir/include/helix
mkdir -p $helix_include_dir
cp -rf ./common/runtime/pub/* $helix_include_dir
cp -rf ./common/include/* $helix_include_dir
cp -rf ./client/include/* $helix_include_dir
cp -rf ./common/container/pub/* $helix_include_dir
cp -rf ./datatype/rm/include/* $helix_include_dir
cp -rf ./common/system/pub/* $helix_include_dir
cp -rf ./common/dbgtool/pub/* $helix_include_dir
cp -rf ./common/util/pub/* $helix_include_dir

helix_lib_dir=$installdir/lib/helix
mkdir -p $helix_lib_dir
cp ./release/*.a $helix_lib_dir
cp ./release/*.so $helix_lib_dir

# clean up
cd $basedir
rm -rf tmp

