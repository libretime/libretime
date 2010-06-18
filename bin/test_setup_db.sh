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
#  This script sets up the test database for Campcaster
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
docdir=$basedir/doc
srcdir=$basedir/src
tmpdir=$basedir/tmp
toolsdir=$srcdir/tools
modules_dir=$srcdir/modules
products_dir=$srcdir/products

scheduler_dir=${products_dir}/scheduler
scheduler_bindir=${scheduler_dir}/bin

usrdir=`cd $basedir/usr; pwd;`

installlog=/tmp/campcaster_install.log


#-------------------------------------------------------------------------------
#  The details of the setup
#-------------------------------------------------------------------------------
ls_database=Campcaster-test
ls_dbuser=test
ls_dbpassword=test
ls_dbserver=localhost


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
${scheduler_bindir}/createDatabase.sh --database=${ls_database} \
                                      --dbuser=${ls_dbuser} \
                                      --dbpassword=${ls_dbpassword} \
                                      --dbserver=${ls_dbserver}


#-------------------------------------------------------------------------------
#  Create the ODBC data source and driver
#-------------------------------------------------------------------------------
${scheduler_bindir}/createOdbcDataSource.sh --database=${ls_database} \
                                            --dbserver=${ls_dbserver}


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

