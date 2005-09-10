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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/bin/createDatabase.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script reates the database used by LiveSupport
#
#  Invoke as:
#  ./bin/createDatabase.sh
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
    echo "LiveSupport scheduler database creation script.";
    echo "parameters";
    echo "";
    echo "  -D, --database      The name of the LiveSupport database.";
    echo "                      [default: LiveSupport]";
    echo "  -s, --dbserver      The name of the database server host.";
    echo "                      [default: localhost]";
    echo "  -u, --dbuser        The name of the database user to access the"
    echo "                      database. [default: livesupport]";
    echo "  -w, --dbpassword    The database user password.";
    echo "                      [default: livesupport]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o D:hs:u:w: -l database:,dbserver:,dbuser:,dbpassword:,help, -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -D|--database)
            database=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -s|--dbserver)
            dbserver=$2;
            shift; shift;;
        -u|--dbuser)
            dbuser=$2;
            shift; shift;;
        -w|--dbpassword)
            dbpassword=$2;
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
    database=LiveSupport;
fi

if [ "x$dbuser" == "x" ]; then
    dbuser=livesupport;
fi

if [ "x$dbpassword" == "x" ]; then
    dbpassword=livesupport;
fi

echo "Creating database for LiveSupport scheduler.";
echo "";
echo "Using the following parameters:";
echo "";
echo "  database server:        $dbserver";
echo "  database:               $database";
echo "  database user:          $dbuser";
echo "  database user password: $dbpassword";
echo ""

#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_dbserver=$dbserver
ls_dbuser=$dbuser
ls_dbpassword=$dbpassword
ls_database=$database


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

check_exe "su" || exit 1;
check_exe "psql" || exit 1;


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
echo "Creating database and database user...";

# FIXME: the below might not work for remote databases

if [ "x$ls_dbserver" == "xlocalhost" ]; then
    su - $postgres_user -c "echo \"CREATE USER $ls_dbuser \
                                   ENCRYPTED PASSWORD '$ls_dbpassword' \
                                   CREATEDB NOCREATEUSER;\" \
                            | psql template1" \
        || echo "Couldn't create database user $ls_dbuser.";

    su - $postgres_user -c "echo \"CREATE DATABASE \\\"$ls_database\\\" \
                                    OWNER $ls_dbuser ENCODING 'utf-8';\" \
                            | psql template1" \
        || echo "Couldn't create database $ls_database.";
else
    echo "Unable to automatically create database user and table for";
    echo "remote database $ls_dbserver.";
    echo "Make sure to create database user $ls_dbuser with password";
    echo "$ls_dbpassword on database server at $ls_dbserver.";
    echo "Also create a database called $ld_database, owned by this user.";
    echo "";
    echo "The easiest way to achieve this is by issuing the following SQL";
    echo "commands to PostgreSQL:";
    echo "CREATE USER $ls_dbuser";
    echo "    ENCRYPTED PASSWORD '$ls_dbpassword'";
    echo "    CREATEDB NOCREATEUSER;";
    echo "CREATE DATABASE \"$ls_database\"";
    echo "    OWNER $ls_dbuser ENCODING 'utf-8';";
fi


# TODO: check for the success of these operations somehow
#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

