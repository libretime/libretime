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
#  This script creates the ODBC data source needed for Campcaster scheduler
#
#  Invoke as:
#  ./bin/createOdbcDataSource.sh
#
#  To get usage help, try the -h option
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
usrdir=$basedir/usr


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster scheduler ODBC DataSource creating script.";
    echo "parameters";
    echo "";
    echo "  -D, --database      The name of the Campcaster database.";
    echo "                      [default: Campcaster]";
    echo "  -s, --dbserver      The name of the database server host.";
    echo "                      [default: localhost]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o D:hs: -l database:,dbserver:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -D|--database)
            database=$2;
            shift; shift;;
        -s|--dbserver)
            dbserver=$2;
            shift; shift;;
        --)
            shift;
            break;;
        *)
            echo "Unrecognized option $1.";
            printUsage;
            exit 1;
    esac
done

if [ "x$dbserver" == "x" ]; then
    dbserver=localhost;
fi

if [ "x$database" == "x" ]; then
    database=Campcaster;
fi


echo "Creating ODBC data source for Campcaster scheduler.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  database server:        $dbserver";
echo "  database:               $database";
echo ""

#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_dbserver=$dbserver
ls_database=$database


replace_sed_string="s/ls_dbserver/$ls_dbserver/; \
                    s/ls_database/$ls_database/;"


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

check_exe "sed" || exit 1;
check_exe "grep" || exit 1;
check_exe "odbcinst" || exit 1;


#-------------------------------------------------------------------------------
#  Create the ODBC data source and driver
#-------------------------------------------------------------------------------
# check for an existing PostgreSQL ODBC driver, and uninstall if necessary
odbcinst_res=`odbcinst -q -d | grep "\[PostgreSQL_Campcaster\]"`
if [ "x$odbcinst_res" != "x" ]; then
    echo "Removing old ODBC PostgreSQL driver...";
    odbcinst -u -d -n PostgreSQL_Campcaster || exit 1;
fi

echo "Registering ODBC PostgreSQL driver...";
# check where the odbc dirvers are for PostgreSQL
if [ -f /usr/lib/libodbcpsql.so ]; then
    odbcinst_template=$etcdir/odbcinst_template
elif [ -f /usr/lib/odbc/psqlodbc.so ]; then
    odbcinst_template=$etcdir/odbcinst_old_debian_template
elif [ -f /usr/lib/odbc/psqlodbcw.so ]; then
    odbcinst_template=$etcdir/odbcinst_new_debian_template
else
    echo "can't find ODBC driver for PostgreSQL neither at /usr/lib";
    echo "nor at /usr/lib/odbc. please install proper ODBC drivers";
    exit 1;
fi
odbcinst -i -d -f $odbcinst_template || exit 1;

echo "Registering Campcaster ODBC data source...";
odbc_template=$etcdir/odbc_template
odbc_template_tmp=/tmp/odbc_template.$$
cat $odbc_template | sed -e "$replace_sed_string" > $odbc_template_tmp
odbcinst -i -s -l -f $odbc_template_tmp || exit 1;
rm -f $odbc_template_tmp


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

