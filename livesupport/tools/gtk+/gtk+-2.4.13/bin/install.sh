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
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/gtk+/gtk+-2.4.13/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install GTK+ 2.4 into the development system usr
# environment.
# For more information on GTK+, see http://www.gtk.org/
# This script will install GTK+, and its intermediate dependencies as well
#-------------------------------------------------------------------------------
glib_product=glib-2.4.7
tiff_product=tiff-v3.6.1
pango_product=pango-1.4.1
atk_product=atk-1.6.1
gtk_product=gtk+-2.4.13

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
installdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
etcdir=$basedir/etc

glib_tar=$basedir/src/$glib_product.tar.bz2
tiff_tar=$basedir/src/$tiff_product.tar.gz
pango_tar=$basedir/src/$pango_product.tar.bz2
atk_tar=$basedir/src/$atk_product.tar.bz2
gtk_tar=$basedir/src/$gtk_product.tar.bz2

export CPPFLAGS="-I$installdir/include"
export LDFLAGS="-L$installdir/lib"
export PKG_CONFIG_PATH="$installdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$installdir/lib"

mkdir -p $tmpdir

# compile & install glib first
echo "installing $glib_product from $basedir to $installdir"
cd $tmpdir
tar xfj $glib_tar
cd $glib_product
./configure --prefix=$installdir
make && make install

# compile & install tiff
echo "installing $tiff_product from $basedir to $installdir"
cd $tmpdir
tar xfz $tiff_tar
cd $tiff_product
./configure --noninteractive --prefix=$installdir
make && make install

# compile & install pango
echo "installing $pango_product from $basedir to $installdir"
cd $tmpdir
tar xfj $pango_tar
cd $pango_product
./configure --prefix=$installdir
make && make install

# compile & install atk
echo "installing $atk_product from $basedir to $installdir"
cd $tmpdir
tar xfj $atk_tar
cd $atk_product
./configure --prefix=$installdir
make && make install

# compile & install gtk+
echo "installing $gtk_product from $basedir to $installdir"
cd $tmpdir
tar xfj $gtk_tar
cd $gtk_product
./configure --prefix=$installdir
make && make install


# clean up
cd $basedir
rm -rf $tmpdir

