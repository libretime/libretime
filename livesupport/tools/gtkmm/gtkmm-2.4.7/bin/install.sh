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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/gtkmm/gtkmm-2.4.7/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install GTK-- 2.4 into the development system usr
# environment.
# For more information on GTK--, see http://gtkmm.sourceforge.net/
# This script will install GTK--, and its intermediate dependencies as well
#-------------------------------------------------------------------------------
sigc_product=libsigc++-2.0.6
glibmm_product=glibmm-2.4.5
gtkmm_product=gtkmm-2.4.7

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
installdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
etcdir=$basedir/etc

sigc_tar=$basedir/src/$sigc_product.tar.bz2
glibmm_tar=$basedir/src/$glibmm_product.tar.bz2
gtkmm_tar=$basedir/src/$gtkmm_product.tar.bz2

export CPPFLAGS="-I$installdir/include"
export LDFLAGS="-L$installdir/lib"
export PKG_CONFIG_PATH="$installdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$usrdir/lib"

mkdir -p $tmpdir

# compile & install sigc first
echo "installing $sigc_product from $basedir to $installdir"
cd $tmpdir
tar xfj $sigc_tar
cd $sigc_product
./configure --prefix=$installdir
make && make install

# compile & install glibmm
echo "installing $glibmm_product from $basedir to $installdir"
cd $tmpdir
tar xfj $glibmm_tar
cd $glibmm_product
./configure --prefix=$installdir
make && make install

# compile & install gtkmm
echo "installing $gtkmm_product from $basedir to $installdir"
cd $tmpdir
tar xfj $gtkmm_tar
cd $gtkmm_product
./configure --prefix=$installdir
make && make install


# clean up
cd $basedir
rm -rf $tmpdir

