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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/gstreamer/gstreamer-0.8.10/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install gstreamer into the development system usr
# environment.
# To read more about gstreamer, see http://gstreamer.freedesktop.org/
#-------------------------------------------------------------------------------
product=gstreamer-0.8.10
plugins=gst-plugins-0.8.9

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
installdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
etcdir=$basedir/etc
gstreamer_tar=$basedir/src/$product.tar.bz2
plugins_tar=$basedir/src/$plugins.tar.bz2

export PATH=$installdir/bin:$PATH
export LD_LIBRARY_PATH=$installdir/lib:$LD_LIBRARY_PATH
export PKG_CONFIG_PATH=$installdir/lib/pkgconfig


mkdir -p $tmpdir

echo "installing $product from $basedir to $installdir"

cd $tmpdir
tar xfj $gstreamer_tar
cd $product
./configure --prefix=$installdir
make install


echo "installing $plugins from $basedir to $installdir"

cd $tmpdir
tar xfj $plugins_tar
cd $plugins
# see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=305658
# for details on the following patch
patch -p1 < $etcdir/adder-fix.diff
# see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=308167
# for details on the following patch
patch -p1 < $etcdir/switcher-fix.diff
# --disable-spc is a workaround for gst-plugins-0.8.9, as some APU.c file
# is missing from there. remove this when later versions come around
./configure --disable-spc --prefix=$installdir
make install


cd $basedir
rm -rf tmp

