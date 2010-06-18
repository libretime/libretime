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
#  This script sets up the development environment for a user.
#
#  Invoke as:
#  ./bin/user_setup_root.sh
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
srcdir=$basedir/src
tmpdir=$basedir/tmp
toolsdir=$srcdir/tools
modules_dir=$srcdir/modules
products_dir=$srcdir/products

scheduler_dir=${products_dir}/scheduler
scheduler_bindir=${scheduler_dir}/bin

usrdir=`cd $basedir/usr; pwd;`


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster user database setup script.";
    echo "parameters:";
    echo "";
    echo "  -u, --user          The user to set up the environment for.";
    echo "                      Required parameter.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o hu: -l help,user: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -h|--help)
            printUsage;
            exit 0;;
        -u|--user)
            user=$2;
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

if [ "x$user" == "x" ]; then
    echo "Required parameter user missing.";
    printUsage;
    exit 1;
fi


echo "Creating the Campcaster user database";
echo "for user: $user.";
echo ""


#-------------------------------------------------------------------------------
#   The details of installation
#-------------------------------------------------------------------------------
postgres_user=postgres

ls_database=Campcaster-$user
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

