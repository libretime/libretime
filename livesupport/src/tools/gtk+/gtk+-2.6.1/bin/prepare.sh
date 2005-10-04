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
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to prepare GTK+ 2.6 to be configured and compiled.
# For more information on GTK+, see http://www.gtk.org/
# This script will install GTK+, and its intermediate dependencies as well
#-------------------------------------------------------------------------------
glib_product=glib-2.6.1
tiff_product=tiff-3.7.1
pango_product=pango-1.8.0
atk_product=atk-1.9.0
gtk_product=gtk+-2.6.1

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
installdir=`cd ${basedir}/../../../usr; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

glib_tar=$basedir/src/$glib_product.tar.bz2
tiff_tar=$basedir/src/$tiff_product.tar.gz
pango_tar=$basedir/src/$pango_product.tar.bz2
atk_tar=$basedir/src/$atk_product.tar.bz2
gtk_tar=$basedir/src/$gtk_product.tar.bz2

export CPPFLAGS="-I$installdir/include"
export LDFLAGS="-L$installdir/lib"
export PKG_CONFIG_PATH="$installdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$usrdir/lib"
export PATH="$PATH:$usrdir/bin"

mkdir -p ${tmpdir}

# copy over install-sh, as AC_CONFIG_SUBDIRS will be looking for it
cp -r $bindir/install-sh $tmpdir

cd ${tmpdir}
if [ ! -d $tiff_product ]; then
    tar xfz ${tiff_tar}
    cd ${tiff_product}
    # patch here
fi

cd ${tmpdir}
if [ ! -d $glib_product ]; then
    tar xfj ${glib_tar}
    cd ${glib_product}
    # patch here
fi

cd ${tmpdir}
if [ ! -d $pango_product ]; then
    tar xfj ${pango_tar}
    cd ${pango_product}
    # patch here
fi

cd ${tmpdir}
if [ ! -d $atk_product ]; then
    tar xfj ${atk_tar}
    cd ${atk_product}
    # patch here
fi

cd ${tmpdir}
if [ ! -d $gtk_product ]; then
    tar xfj ${gtk_tar}
    cd ${gtk_product}
    # patch here
fi

