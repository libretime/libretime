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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/gstreamerElements/bin/play.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Play an audio file by using gstreamer and autoplugging.
#-------------------------------------------------------------------------------

# assume we're in $basedir/bin
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
test -z "$basedir" && basedir=.
usrdir=`cd $basedir/../../usr; pwd;`

bindir=$basedir/bin
etcdir=$basedir/etc
libdir=$basedir/lib
tmpdir=$basedir/tmp

play=$tmpdir/play


export CPPFLAGS="-I$usrdir/include"
export LDFLAGS="-L$usrdir/lib"
export PKG_CONFIG_PATH="$usrdir/lib/pkgconfig"
export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$usrdir/lib"
export GST_PLUGIN_PATH="$libdir"


if [ ! -f $play ]; then
    echo "first build the play executable by issuing the following command:";
    echo "";
    echo "make all";

    exit -1;
fi


$play "$*"

