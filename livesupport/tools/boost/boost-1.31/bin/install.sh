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
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/boost/boost-1.31/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install cppunit into the development system usr
# environment.
# For more information on Boost, see http://www.boost.org/
#-------------------------------------------------------------------------------
product=boost_1_31_0

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd; cd -`
installdir=`cd $basedir/../../../usr; pwd; cd -`
tmpdir=$basedir/tmp
etcdir=$basedir/etc
tar=$basedir/src/$product.tar.bz2

echo "installing $product from $basedir to $installdir"


mkdir -p $tmpdir
cd $tmpdir

tar xfj $tar
cd $product

# build BoostJam, their make enviornment
cd tools/build/jam_src/
sh build.sh gcc
cd ../../..

# now build and install the who thing
./tools/build/jam_src/bin.linuxx86/bjam "-sTOOLS=gcc" --prefix=$installdir install

cd $basedir
rm -rf tmp

