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
#   Version  : $Revision: 1.4 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/startMakeRecompile.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# This script runs cvs to update the LiveSupport source code and starts
# the recompile process. 
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
docdir=$basedir/doc
tmpdir=$basedir/tmp
logdir=$basedir/tmp
export PATH=~/bin:$PATH

echo "";
echo "The source code will be updated and the recompile process"; 
echo "will be started. All steps are being logged within the log";
echo "directory $logdir";
echo "";
echo "Are you certainly of ran ./configure first !";
echo "";
echo "Now a update by cvs...";

cd $bindir/..
cvs update -dP >& $logdir/cvs_update_before_setup.log
ls -l $logdir/cvs_update_before_setup.log >> $logdir/cvs_update_before_setup.log
echo "Compare with cvs is be done, cvs_update_before_setup.log is created";
make distclean >& $logdir/make_distclean_setup.log
ls -l $logdir/make_distclean_setup.log >> $logdir/make_distclean_setup.log
echo "Cleaning the setup is be done, make_distclean_setup.log is created";
#make tools_setup >& $logdir/tools_setup.log
#ls -l $logdir/tools_setup.log >> $logdir/tools_setup.log
#echo "Done Tools Setup, tools_setup.log is created";
echo "Skipping Tools, remove # for recompiling tools";
echo "";
echo "Now Configure ...";
make modules_setup >& $logdir/modules_setup.log
ls -l $logdir/modules_setup.log >> $logdir/modules_setup.log
echo "Configure the Modules is be done, modules_setup.log is created";
make products_setup >& $logdir/products_setup.log
ls -l $logdir/products_setup.log >> $logdir/products_setup.log
echo "Configure the Products is be done, products_setup.log is created";
echo "";
echo "Now Recompiling ...";
make compile >& $logdir/make_compile_setup.log 
ls -l $logdir/make_compile_setup.log >> $logdir/make_compile_setup.log
echo "Compiling is be done, make_compile_setup.log is created";
echo "";
echo "Now checking ...";
make check >& $logdir/make_check_setup.log
ls -l $logdir/make_check_setup.log >> $logdir/make_check_setup.log
echo "Checking is be done, make_check_setup.log is created";
echo "";
