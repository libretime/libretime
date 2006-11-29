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
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to prepare gstreamer to be configured and compiled.
# To read more about gstreamer, see http://gstreamer.freedesktop.org/
#-------------------------------------------------------------------------------
product=gstreamer-0.8.12
plugins=gst-plugins-0.8.12

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

gstreamer_tar=${basedir}/src/${product}.tar.bz2
plugins_tar=${basedir}/src/${plugins}.tar.bz2

mkdir -p ${tmpdir}

# copy over install-sh, as AC_CONFIG_SUBDIRS will be looking for it
cp -r $bindir/install-sh $tmpdir

cd ${tmpdir}
if [ ! -d $product ]; then
    tar xfj ${gstreamer_tar}
    cd ${product}
    # patch here
fi

cd ${tmpdir}
if [ ! -d $plugins ]; then
    tar xfj ${plugins_tar}
    cd ${plugins}
    # see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=314146
    # for details on the following patch
    patch -p1 < ${etcdir}/adder-caps-property.patch
    # see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=309218
    # for details on the following patch
    # the patch was applied to 0.8.12, but in a slightly different form;
    # this reverts it to Akos's original version
    # TODO: figure out if this is needed, and remove it if it isn't
    patch -p1 < ${etcdir}/adder-duration-fix-revert-to-original.patch
    # see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=308167
    # for details on the following patch
    patch -p1 < ${etcdir}/switch-fix.patch
    # see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=308619
    # for details on the following patch
    patch -p1 < ${etcdir}/id3demuxbin-pad-free-fix.patch
    # see bug report at http://bugzilla.gnome.org/show_bug.cgi?id=359237
    # for details on the following patch
    patch -p1 < ${etcdir}/xml-buffer-size.patch
fi

