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
#   Author   : $Author: fgerlits $
#   Version  : $Revision: 3360 $
#   Location : $URL: svn://source.campware.org/campcaster/trunk/campcaster/src/tools/libodbc++/libodbc++-0.2.4pre4/bin/prepare.sh $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to prepare libodbc++ 2.3 to be configured and compiled.
# To read more about libodbc++, see http://libodbcxx.sourceforge.net/
#-------------------------------------------------------------------------------
product=libodbc++-0.2.5

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

tar=$basedir/src/$product.tar.bz2

mkdir -p ${tmpdir}

cd ${tmpdir}
if [ ! -d $product ]; then
    tar xfj ${tar}
    cd $product

    # patch accepted, will be in the next release
    # http://libodbcxx.svn.sourceforge.net/viewvc/libodbcxx?view=rev&revision=154
    patch -p1 < $etcdir/libodbc++-add-cstdio-include.patch
fi

