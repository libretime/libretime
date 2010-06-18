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
# This script runs cvs to update the Campcaster source code.
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
tmpdir=$basedir/tmp
logdir=$basedir/tmp

echo "";
echo "The Campcaster source code will be updated now ... and logged in";
echo "$logdir";
echo "";

cd $bindir/..
svn update >& $logdir/svn_update.log
ls -l $logdir/svn_update.log >> $logdir/svn_update.log
cat $logdir/svn_update.log
echo "";
echo "The svn update is done, svn_update.log is created";
