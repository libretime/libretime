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
# Run this script to prepare libodbc++ 2.3 to be configured and compiled.
# To read more about libodbc++, see http://libodbcxx.sourceforge.net/
#-------------------------------------------------------------------------------
product=libodbc++-0.2.3-20050404

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
installdir=`cd ${basedir}/../../../usr; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

tar=$basedir/src/$product.tar.bz2

mkdir -p ${tmpdir}

# copy over install-sh, as AC_CONFIG_SUBDIRS will be looking for it
cp -r $bindir/install-sh $tmpdir

cd ${tmpdir}
if [ ! -d $product ]; then
    tar xfj ${tar}
    cd $product
    # see http://sourceforge.net/tracker/index.php?func=detail&aid=1176652&group_id=19075&atid=319075
    patch -p1 < $etcdir/libodbc++-no-namespace-closing-colon.patch
    # see http://sourceforge.net/tracker/index.php?func=detail&aid=1176656&group_id=19075&atid=319075
    patch -p1 < $etcdir/libodbc++-no-thread-dmaccess-mutex-fix.patch
    # patch not submitted
    patch -p1 < $etcdir/libodbc++-dont-install-some-docs.patch
    # patch to fix bug #1545 (not submitted; already fixed in 0.2.4)
    patch -p1 < $etcdir/libodbc++-gcc-4.0-fix.patch
fi

