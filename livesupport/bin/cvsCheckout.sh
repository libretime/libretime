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
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/cvsCheckout.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# This script runs cvs to update the LiveSupport source code.
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
docdir=$basedir/doc
tmpdir=$basedir/tmp
logdir=$basedir/tmp

echo "";
echo "The LiveSupport source code will be updated now. The update will be";
echo "loged within cvs_checkout.log and printed to the console";
echo "";

cd $bindir/../..
cvs -z3 -d :pserver:anonymous@netfinity-5.mdlf.org:/home/cvs checkout livesupport >& $logdir/cvs_checkout.log
ls -l $logdir/cvs_checkout.log >> $logdir/cvs_checkout.log
cat $logdir/cvs_checkout.log
