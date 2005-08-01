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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/libodbc++/libodbc++-0.2.3-20050404/bin/autogen.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# Run this to set up the build system: configure, makefiles, etc.
# (based on the version in enlightenment's cvs)
#-------------------------------------------------------------------------------

package="libodbc++"

# assume we're in $basedir/bin
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
test -z "$basedir" && basedir=.
usrdir=`cd $basedir/../../../usr; pwd;`

bindir=$basedir/bin
etcdir=$basedir/etc
tmpdir=$basedir/tmp

cd "$tmpdir"
DIE=0

(autoheader --version) < /dev/null > /dev/null 2>&1 || {
    echo
    echo "You must have autoconf installed to compile $package."
    echo "Download the appropriate package for your distribution,"
    echo "or get the source tarball at ftp://ftp.gnu.org/pub/gnu/"
    DIE=1
}

(autoconf --version) < /dev/null > /dev/null 2>&1 || {
    echo
    echo "You must have autoconf installed to compile $package."
    echo "Download the appropriate package for your distribution,"
    echo "or get the source tarball at ftp://ftp.gnu.org/pub/gnu/"
    DIE=1
}

if test "$DIE" -eq 1; then
    exit 1
fi

if test -z "$*"; then
    echo "I am going to run ./configure with no arguments - if you wish "
    echo "to pass any to it, please specify them on the $0 command line."
fi

echo "Generating configuration files for $package, please wait...."

configure_ac=${etcdir}/configure.ac
configure=${tmpdir}/configure
aclocal_m4=${tmpdir}/aclocal.m4

# copy over configure.ac and acinlclude.m4 from etc to tmp,
# as aclocal >= 1.8 is sooo unbelivably stupid that it will simply try to
# look for configure.ac in the current directory, and include acinclude.m4
# in aclocal.m4 it without a directory path in front
#ACLOCAL_FLAGS="-I ${tmpdir} --acdir=${tmpdir} --output=${aclocal_m4}"
#echo "  aclocal $ACLOCAL_FLAGS"
#cp -f ${configure_ac} ${tmpdir}
#cp -f ${etcdir}/acinclude.m4 ${tmpdir}
#aclocal $ACLOCAL_FLAGS

#echo "  autoheader ${configure_ac}"
#autoheader ${configure_ac}

echo "  autoconf -I ${tmpdir} -o ${configure} ${configure_ac}"
autoconf -I ${tmpdir} -o ${configure} ${configure_ac}

export CPPFLAGS="-I$usrdir/include"
export LDFLAGS="-L$usrdir/lib"
export PKG_CONFIG_PATH="$usrdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$usrdir/lib"

${configure} "$@" && echo
