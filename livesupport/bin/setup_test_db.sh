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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/setup_test_db.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script sets up the test database for LiveSupport
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
docdir=$basedir/doc
tmpdir=$basedir/tmp
toolsdir=$basedir/tools
modules_dir=$basedir/modules
products_dir=$basedir/products

usrdir=`cd $basedir/usr; pwd;`

installlog=/tmp/livesupport_install.log


#-------------------------------------------------------------------------------
#  The details of the setup
#-------------------------------------------------------------------------------
ls_dbserver=localhost
ls_dbuser=test
ls_dbpassword=test
ls_database=LiveSupport-test


postgres_user=postgres


#-------------------------------------------------------------------------------
#  Function to check for the existence of an executable on the PATH
#
#  @param $1 the name of the exectuable
#  @return 0 if the executable exists on the PATH, non-0 otherwise
#-------------------------------------------------------------------------------
check_exe() {
    if [ -x "`which $1 2> /dev/null`" ]; then
        echo "Executable $1 found...";
        return 0;
    else
        echo "Executable $1 not found...";
        return 1;
    fi
}


#-------------------------------------------------------------------------------
#  Check to see if this script is being run as root
#-------------------------------------------------------------------------------
if [ `whoami` != "root" ]; then
    echo "Please run this script as root.";
    exit ;
fi


#-------------------------------------------------------------------------------
#  Check for required tools
#-------------------------------------------------------------------------------
echo "Checking for required tools..."

check_exe "psql" || exit 1;
check_exe "odbcinst" || exit 1;


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
echo "Creating database and database user...";

su - $postgres_user -c "echo \"CREATE USER $ls_dbuser \
                               ENCRYPTED PASSWORD '$ls_dbpassword' \
                               CREATEDB NOCREATEUSER;\" \
                        | psql template1" \
    || echo "Couldn't create database user $ls_dbuser.";

su - $postgres_user -c "echo \"CREATE DATABASE \\\"$ls_database\\\" \
                                OWNER $ls_dbuser ENCODING 'utf-8';\" \
                        | psql template1" \
    || echo "Couldn't create database $ls_database.";


# TODO: check for the success of these operations somehow


#-------------------------------------------------------------------------------
#  Create the ODBC data source and driver
#-------------------------------------------------------------------------------
echo "Creating ODBC data source and driver...";

odbcinst_template=$products_dir/scheduler/etc/odbcinst_test_template
odbc_template=$products_dir/scheduler/etc/odbc_test_template

# check for an existing PostgreSQL ODBC driver, and only install if necessary
odbcinst_res=`odbcinst -q -d | grep "\[PostgreSQL\]"`
if [ "x$odbcinst_res" == "x" ]; then
    echo "Registering ODBC PostgreSQL driver...";
    odbcinst -i -d -v -f $odbcinst_template || exit 1;
fi

echo "Registering LiveSupport ODBC data source...";
odbcinst -i -s -l -f $odbc_template || exit 1;


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

