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
#   Version  : $Revision: 1.6 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script installs LiveSupport.
#
#  Invoke as:
#  ./bin/install.sh
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

installlog=/tmp/livesupport_install.log


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport install script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -D, --database      The name of the LiveSupport database.";
    echo "                      [default: LiveSupport]";
    echo "  -g, --apache-group  The group the apache daemon runs as.";
    echo "                      [default: apache]";
    echo "  -H, --host          The fully qualified host name of the system";
    echo "                      [default: guess].";
    echo "  -p, --port          The port of the apache web server [default: 80]"
    echo "  -P, --scheduler-port    The port of the scheduler daemon to install"
    echo "                          [default: 3344]";
    echo "  -s, --dbserver      The name of the database server host.";
    echo "                      [default: same as host]";
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

opts=$(getopt -o d:D:g:H:hp:P:s:u:w: -l apache-group:,database:,dbserver:,dbuser:,dbpassword:,directory:,host:,help,port:,scheduler-port -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            installdir=$2;
            shift; shift;;
        -D|--database)
            database=$2;
            shift; shift;;
        -g|--apache-group)
            apache_group=$2;
            shift; shift;;
        -H|--host)
            hostname=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -p|--port)
            http_port=$2;
            shift; shift;;
        -P|--scheduler-port)
            scheduler_port=$2;
            shift; shift;;
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

if [ "x$installdir" == "x" ]; then
    echo "Required parameter install directory not specified.";
    printUsage;
    exit 1;
fi

if [ "x$hostname" == "x" ]; then
    hostname=`hostname -f`;
fi

if [ "x$http_port" == "x" ]; then
    http_port=80;
fi

if [ "x$scheduler_port" == "x" ]; then
    scheduler_port=3344;
fi

if [ "x$dbserver" == "x" ]; then
    dbserver=$hostname;
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

if [ "x$apache_group" == "x" ]; then
    apache_group=apache;
fi


echo "Installing LiveSupport.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  installation directory: $installdir";
echo "  host name:              $hostname";
echo "  web server port:        $http_port";
echo "  scheduler port:         $scheduler_port";
echo "  database server:        $dbserver";
echo "  database:               $database";
echo "  database user:          $dbuser";
echo "  database user password: $dbpassword";
echo "  apache daemon group:    $apache_group";
echo ""


#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_php_host=$hostname
ls_php_port=$http_port
ls_php_urlPrefix=livesupport

ls_alib_xmlRpcPrefix="xmlrpc/xrLocStor.php"
ls_storage_xmlRpcPrefix="xmlrpc/xrLocStor.php"

ls_dbserver=$dbserver
ls_dbuser=$dbuser
ls_dbpassword=$dbpassword
ls_database=$database

ls_scheduler_host=$hostname
ls_scheduler_port=$scheduler_port
ls_scheduler_urlPrefix=
ls_scheduler_xmlRpcPrefix=RC2


postgres_user=postgres

install_bin=$installdir/bin
install_etc=$installdir/etc
install_lib=$installdir/lib
install_tmp=$installdir/tmp
install_var=$installdir/var


# replace / characters with a \/ sequence, for sed below
# the sed statement is really "s/\//\\\\\//g", but needs escaping because of
# bash, hence the extra '\' characters
installdir_s=`echo $installdir | sed -e "s/\//\\\\\\\\\//g"`
ls_storage_xmlRpcPrefix_s=`echo $ls_storage_xmlRpcPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`
ls_alib_xmlRpcPrefix_s=`echo $ls_alib_xmlRpcPrefix | sed -e "s/\//\\\\\\\\\//g"`
ls_php_urlPrefix_s=`echo $ls_php_urlPrefix | sed -e "s/\//\\\\\\\\\//g"`
ls_scheduler_urlPrefix_s=`echo $ls_scheduler_urlPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`
ls_scheduler_xmlRpcPrefix_s=`echo $ls_scheduler_xmlRpcPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`

replace_sed_string="s/ls_install_dir/$installdir_s/; \
              s/ls_dbuser/$ls_dbuser/; \
              s/ls_dbpassword/$ls_dbpassword/; \
              s/ls_dbserver/$ls_dbserver/; \
              s/ls_database/$ls_database/; \
              s/ls_storageUrlPath/\/$ls_php_urlPrefix_s\/storageServer\/var/; \
              s/ls_php_urlPrefix/$ls_php_urlPrefix_s/; \
              s/ls_storage_xmlRpcPrefix/$ls_storage_xmlRpcPrefix_s/; \
              s/ls_alib_xmlRpcPrefix/$ls_alib_xmlRpcPrefix_s/; \
              s/ls_php_host/$ls_php_host/; \
              s/ls_php_port/$ls_php_port/; \
              s/ls_archiveUrlPath/\/$ls_php_urlPrefix_s\/archiveServer\/var/; \
              s/ls_scheduler_urlPrefix/$ls_scheduler_urlPrefix_s/; \
              s/ls_scheduler_xmlRpcPrefix/$ls_scheduler_xmlRpcPrefix_s/; \
              s/ls_scheduler_host/$ls_scheduler_host/; \
              s/ls_scheduler_port/$ls_scheduler_port/;"



#-------------------------------------------------------------------------------
#  Function to check for the existence of an executable on the PATH
#
#  @param $1 the name of the exectuable
#  @return 0 if the executable exists on the PATH, non-0 otherwise
#-------------------------------------------------------------------------------
check_exe() {
    if [ -x "`which $1 2> /dev/null`" ]; then
        echo "Exectuable $1 found...";
        return 0;
    else
        echo "Exectuable $1 not found...";
        return 1;
    fi
}


#-------------------------------------------------------------------------------
#  Function to check for a PEAR module
#
#  @param $1 the name of the PEAR module
#  @return 0 if the module is available, non-0 otherwise
#-------------------------------------------------------------------------------
check_pear_module() {
    test_result=`pear info $1`
    if [ $? = 0 ]; then
        echo "PEAR module $1 found...";
        return 0;
    else
        echo "PEAR module $1 not found...";
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
check_exe "php" || exit 1;
check_exe "pear" || exit 1;
check_exe "odbcinst" || exit 1;

check_pear_module "DB" || exit 1;
check_pear_module "Calendar" || exit 1;
check_pear_module "File" || exit 1;
check_pear_module "File_Find" || exit 1;
check_pear_module "HTML_Common" || exit 1;
check_pear_module "HTML_QuickForm" || exit 1;
check_pear_module "XML_Beautifier" || exit 1;
check_pear_module "XML_Parser" || exit 1;
check_pear_module "XML_RPC" || exit 1;
check_pear_module "XML_Serializer" || exit 1;
check_pear_module "XML_Util" || exit 1;


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
echo "Creating database and database user...";

# FIXME: the below might not work for remote databases

su - $postgres_user -c "echo \"CREATE USER $ls_dbuser \
                               ENCRYPTED PASSWORD '$ls_dbpassword' \
                               CREATEDB NOCREATEUSER;\" \
                        | psql -h $ls_dbserver template1" \
    || echo "Couldn't create database user $ls_dbuser.";

su - $postgres_user -c "echo \"CREATE DATABASE \\\"$ls_database\\\" \
                                OWNER $ls_dbuser ENCODING 'utf-8';\" \
                        | psql -h $ls_dbserver template1" \
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
#  Create the installation directory structure
#-------------------------------------------------------------------------------
echo "Copying files..."

mkdir -p $installdir
mkdir -p $install_bin
mkdir -p $install_etc
mkdir -p $install_lib
mkdir -p $install_tmp
mkdir -p $install_var


#-------------------------------------------------------------------------------
#  Copy the PHP files
#-------------------------------------------------------------------------------
mkdir $install_var/getid3
cp -a $modules_dir/getid3/var $install_var/getid3

mkdir $install_var/alib
cp -a $modules_dir/alib/var $install_var/alib

mkdir $install_var/storageServer
cp -a $modules_dir/storageServer/var $install_var/storageServer

mkdir $install_var/archiveServer
cp -a $modules_dir/archiveServer/var $install_var/archiveServer

mkdir $install_var/htmlUI
cp -a $modules_dir/htmlUI/var $install_var/htmlUI


#-------------------------------------------------------------------------------
#  Copy scheduler related files
#-------------------------------------------------------------------------------
cp -a $usrdir/lib/* $install_lib
cp -a $products_dir/scheduler/tmp/scheduler $install_bin
cp -a $products_dir/scheduler/bin/scheduler.sh $install_bin


#-------------------------------------------------------------------------------
#  Clean up remnants of the CVS system
#-------------------------------------------------------------------------------
rm -rf `find $install_var -type d -name CVS`


#-------------------------------------------------------------------------------
#  Customize the configuration files with the appropriate values
#-------------------------------------------------------------------------------
echo "Customizing configuration files..."

cat $install_var/storageServer/var/conf.php.template \
    | sed -e "$replace_sed_string" \
    > $install_var/storageServer/var/conf.php

cat $install_var/archiveServer/var/conf.php.template \
    | sed -e "$replace_sed_string" \
    > $install_var/archiveServer/var/conf.php

cat $products_dir/scheduler/etc/scheduler.xml.template \
    | sed -e "$replace_sed_string" \
    > $install_etc/scheduler.xml


#-------------------------------------------------------------------------------
#  Setup directory permissions
#-------------------------------------------------------------------------------
echo "Setting up directory permissions..."

chgrp $apache_group $install_var/archiveServer/var/stor
chgrp $apache_group $install_var/archiveServer/var/access
chgrp $apache_group $install_var/archiveServer/var/trans
chgrp $apache_group $install_var/archiveServer/var/stor/buffer

chmod g+sw $install_var/archiveServer/var/stor
chmod g+sw $install_var/archiveServer/var/access
chmod g+sw $install_var/archiveServer/var/trans
chmod g+sw $install_var/archiveServer/var/stor/buffer

chgrp $apache_group $install_var/storageServer/var/stor
chgrp $apache_group $install_var/storageServer/var/access
chgrp $apache_group $install_var/storageServer/var/trans
chgrp $apache_group $install_var/storageServer/var/stor/buffer

chmod g+sw $install_var/storageServer/var/stor
chmod g+sw $install_var/storageServer/var/access
chmod g+sw $install_var/storageServer/var/trans
chmod g+sw $install_var/storageServer/var/stor/buffer

chgrp $apache_group $install_var/htmlUI/var/templates_c
chgrp $apache_group $install_var/htmlUI/var/html/img

chmod g+sw $install_var/htmlUI/var/templates_c
chmod g+sw $install_var/htmlUI/var/html/img


#-------------------------------------------------------------------------------
#  Initialize the database
#-------------------------------------------------------------------------------
echo "Initializing database...";

# create PHP-related database tables
cd $install_var/storageServer/var/install
php -q install.php || exit 1;
cd -

# create scheduler-related database tables
cd $installdir
./bin/scheduler.sh install || exit 1;
cd -


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

