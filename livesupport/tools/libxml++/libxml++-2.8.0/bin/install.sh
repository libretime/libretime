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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/libxml++/libxml++-2.8.0/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install cppunit into the development system usr
# environment.
# For more information on libxml++, see http://libxmlplusplus.sourceforge.net/
#
# WARNING: This install script does NOT install related dependencies, like Glib.
# To have these dependencies installed, please install GTK-- first, which
# in fact installs all needed dependencies
#-------------------------------------------------------------------------------
product=libxml++-2.8.0

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
usrdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
sharedir=$usrdir/share
docdir=$sharedir/doc/libxml++
tar=$basedir/src/$product.tar.bz2
installdir=$usrdir

export CPPFLAGS="-I$usrdir/include"
export LDFLAGS="-L$usrdir/lib"
export PKG_CONFIG_PATH="$usrdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$usrdir/lib"


echo "installing $product from $basedir to $installdir"


mkdir -p $tmpdir
cd $tmpdir

tar xfj $tar
cd $product
./configure --prefix=$installdir
make install

# make the reference documentation and install that as well, as the
# autoconf thing doesn't :(
make -C docs/reference
mkdir -p $docdir
cp -a docs/reference/2.8 $docdir

cd $basedir
rm -rf tmp

