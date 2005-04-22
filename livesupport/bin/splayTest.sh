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
#   Author   : $Author: fberckel $
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/Attic/splayTest.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# This script runs the Helix Simple player using test.mp3.
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
docdir=$basedir/doc
tmpdir=$basedir/tmp
usrbindir=$basedir/usr/bin
modules_dir=$basedir/modules
usrlibdir=$basedir/usr/lib

echo "";
echo "Now playing test.mp3 with the Helix Simple Player";
echo "";

$usrbindir/splay $modules_dir/playlistExecutor/var/test.mp3 -l $usrlibdir/helix
