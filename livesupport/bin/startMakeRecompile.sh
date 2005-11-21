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
# This script starts the LiveSupport recompile process. 
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
tmpdir=$basedir/tmp
logdir=$basedir/tmp
export PATH=~/bin:$PATH

echo "";
echo "The recompile process will be started. All steps are being logged in"; 
echo "$logdir ";
echo "";
echo "Are you certainly of ran ./configure first !";


cd $bindir/..
make modprod_distclean >& $logdir/make_modprod_distclean_setup.log
ls -l $logdir/make_modprod_distclean_setup.log >> $logdir/make_modprod_distclean_setup.log
echo "";
echo "Cleaning the setup is done, make_modprod_distclean_setup.log is created";
#echo "";
#echo "Now Recompiling ... Tools";
#make tools_setup >& $logdir/make_install_tools_setup.log
#ls -l $logdir/make_install_tools_setup.log >> $logdir/make_install_tools_setup.log
#echo "Done Tools Setup, make_install_tools_setup.log is created";
echo "";
echo "Skipping Tools setup, remove # for recompiling tools";
echo "";
echo "Now Configure ... Modules ... Products";
make modules_setup >& $logdir/make_configure_modules_setup.log
ls -l $logdir/make_configure_modules_setup.log >> $logdir/make_configure_modules_setup.log
echo "Configure the Modules is done, make_configure_modules_setup.log is created";
make products_setup >& $logdir/make_configure_products_setup.log
ls -l $logdir/make_configure_products_setup.log >> $logdir/make_configure_products_setup.log
echo "Configure the Products is done, make_configure_products_setup.log is created";
echo "";
echo "Now Recompiling ... ";
make compile >& $logdir/make_compile_setup.log 
ls -l $logdir/make_compile_setup.log >> $logdir/make_compile_setup.log
echo "Compiling is done, make_compile_setup.log is created";
echo "";
echo "Now checking ...";
make check >& $logdir/make_check_setup.log
ls -l $logdir/make_check_setup.log >> $logdir/make_check_setup.log
echo "Checking is be done, make_check_setup.log is created";
echo "";
ls -l $logdir
