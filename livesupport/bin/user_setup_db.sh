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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/user_setup_db.sh,v $
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
tmpdir=$basedir/tmp
toolsdir=$basedir/tools
modules_dir=$basedir/modules
products_dir=$basedir/products

usrdir=`cd $basedir/usr; pwd;`


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport development environment setup script.";
    echo "parameters:";
    echo "";
    echo "  -g, --apache-group  The group the apache daemon runs as.";
    echo "                      [default: apache]";
    echo "  -u, --user          The user to set up the environment for.";
    echo "                      Required parameter.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o g:hu: -l apache-group:,help,user: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -g|--apache-group)
            apache_group=$2;
            shift; shift;;
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

if [ "x$apache_group" == "x" ]; then
    apache_group=apache;
fi

dbserver=localhost;
database=LiveSupport-$user;
dbuser=test;
dbpassword=test;


echo "Configuring LiveSupport development environment.";
echo "";
echo "Using the following parameters:";
echo "";
echo "  configuring for user:   $user";
echo "  database server:        $dbserver";
echo "  database:               $database";
echo "  database user:          $dbuser";
echo "  database user password: $dbpassword";
echo "  apache daemon group:    $apache_group";
echo ""

# check for the apache group to be a real group
group_tmp_file=/tmp/ls_group_check.$$
touch $group_tmp_file
test_result=`chgrp $apache_group $group_tmp_file 2> /dev/null`
if [ $? != 0 ]; then
    rm -f $group_tmp_file;
    echo "Unable to use apache deamon group $apache_group.";
    echo "Please check if $apache_group is a correct user group.";
    exit 1;
fi
rm -f $group_tmp_file;


#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_dbserver=$dbserver
ls_dbuser=$dbuser
ls_dbpassword=$dbpassword
ls_database=$database


postgres_user=postgres


replace_sed_string="s/ls_dbuser/$ls_dbuser/; \
                    s/ls_dbpassword/$ls_dbpassword/; \
                    s/ls_dbserver/$ls_dbserver/; \
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
check_exe "psql" || exit 1;
check_exe "odbcinst" || exit 1;


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
echo "Creating database and database user...";

# FIXME: the below might not work for remote databases

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

odbcinst_template=$products_dir/scheduler/etc/odbcinst_template
odbc_template=$products_dir/scheduler/etc/odbc_template
odbc_template_tmp=/tmp/odbc_template.$$

# check for an existing PostgreSQL ODBC driver, and only install if necessary
odbcinst_res=`odbcinst -q -d | grep "\[PostgreSQL\]"`
if [ "x$odbcinst_res" == "x" ]; then
    echo "Registering ODBC PostgreSQL driver...";
    odbcinst -i -d -v -f $odbcinst_template || exit 1;
fi

echo "Registering LiveSupport ODBC data source...";
cat $odbc_template | sed -e "$replace_sed_string" > $odbc_template_tmp
odbcinst -i -s -l -f $odbc_template_tmp || exit 1;
rm -f $odbc_template_tmp


#-------------------------------------------------------------------------------
#  Call the script that will do the user-specific setup.
#-------------------------------------------------------------------------------
su - $user -c "$bindir/user_setup.sh -g $apache_group"


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

