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
#   Version  : $Revision: 1.5 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/user_setup.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script configures the environment for a developer.
#
#  Invoke as:
#  ./bin/user_setup.sh
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
    echo "LiveSupport install script.";
    echo "parameters";
    echo "";
    echo "  -g, --apache-group  The group the apache daemon runs as.";
    echo "                      [default: apache]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o g:h -l apache-group:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -g|--apache-group)
            apache_group=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        --)
            shift;
            break;;
        *)
            echo "Unrecognized option $1.";
            printUsage;
            exit 1;
    esac
done

if [ "x$apache_group" == "x" ]; then
    apache_group=apache;
fi

scheduler_base_port=3344

user=`whoami`
# force localhost always
hostname=localhost
http_port=80
scheduler_port=`expr $scheduler_base_port + $UID`
dbserver=localhost
database=LiveSupport-$user
dbuser=test
dbpassword=test
homedir=$HOME
configdir=$homedir/.livesupport
htmldir=$homedir/public_html
outputdsp=/dev/dsp



echo "Configuring LiveSupport development environment for user $user.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  host name:               $hostname";
echo "  web server port:         $http_port";
echo "  scheduler port:          $scheduler_port";
echo "  database server:         $dbserver";
echo "  database:                $database";
echo "  database user:           $dbuser";
echo "  database user password:  $dbpassword";
echo "  apache daemon group:     $apache_group";
echo "  home directory:          $homedir";
echo "  configuration directory: $configdir";
echo "  web base directory:      $htmldir";
echo "  output audio device:     $outputdsp";
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

installdir=$usrdir

ls_php_host=$hostname
ls_php_port=$http_port
ls_php_urlPrefix=~$user/livesupport

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

ls_output_dsp=$outputdsp


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
ls_output_dsp_s=`echo $ls_output_dsp | sed -e "s/\//\\\\\\\\\//g"`

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
              s/ls_scheduler_port/$ls_scheduler_port/; \
              s/ls_output_dsp/$ls_output_dsp_s/;"



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
#  Check for required tools
#-------------------------------------------------------------------------------
echo "Checking for required tools..."

check_exe "sed" || exit 1;
check_exe "php" || exit 1;
check_exe "pear" || exit 1;

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
#  Customize the configuration files with the appropriate values
#-------------------------------------------------------------------------------
echo "Customizing configuration files..."

mkdir -p $configdir

cat $modules_dir/storageServer/var/conf_only.php.template \
    | sed -e "$replace_sed_string" \
    > $configdir/storageServer.conf.php

cat $modules_dir/archiveServer/var/conf_only.php.template \
    | sed -e "$replace_sed_string" \
    > $configdir/archiveServer.conf.php

cat $modules_dir/authentication/etc/webAuthentication.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/webAuthentication.xml

cat $modules_dir/db/etc/connectionManagerFactory.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/connectionManagerFactory.xml

cat $modules_dir/db/etc/simpleConnectionManager.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/simpleConnectionManager.xml

cat $modules_dir/schedulerClient/etc/schedulerClientFactory.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/schedulerClientFactory.xml

cat $modules_dir/schedulerClient/etc/schedulerDaemonXmlRpcClient.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/schedulerDaemonXmlRpcClient.xml

cat $modules_dir/storage/etc/webAuthenticationClient.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/webAuthenticationClient.xml

cat $modules_dir/storage/etc/webStorage.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/webStorage.xml

cat $products_dir/scheduler/etc/scheduler.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/scheduler.xml

cat $products_dir/gLiveSupport/etc/gLiveSupport.xml.user-template \
    | sed -e "$replace_sed_string" \
    > $configdir/gLiveSupport.xml


#-------------------------------------------------------------------------------
#  Create the public html directory, and links to the PHP directories
#-------------------------------------------------------------------------------
echo "Creating public HTML directory and links to web interfaces..."

mkdir -p $htmldir

rm -f $htmldir/livesupport

ln -s $modules_dir $htmldir/livesupport


#-------------------------------------------------------------------------------
#  Setup directory permissions
#-------------------------------------------------------------------------------
echo "Setting up directory permissions..."

chgrp $apache_group $modules_dir/archiveServer/var/stor
chgrp $apache_group $modules_dir/archiveServer/var/access
chgrp $apache_group $modules_dir/archiveServer/var/trans
chgrp $apache_group $modules_dir/archiveServer/var/stor/buffer

chmod g+sw $modules_dir/archiveServer/var/stor
chmod g+sw $modules_dir/archiveServer/var/access
chmod g+sw $modules_dir/archiveServer/var/trans
chmod g+sw $modules_dir/archiveServer/var/stor/buffer

chgrp $apache_group $modules_dir/storageServer/var/stor
chgrp $apache_group $modules_dir/storageServer/var/access
chgrp $apache_group $modules_dir/storageServer/var/trans
chgrp $apache_group $modules_dir/storageServer/var/stor/buffer

chmod g+sw $modules_dir/storageServer/var/stor
chmod g+sw $modules_dir/storageServer/var/access
chmod g+sw $modules_dir/storageServer/var/trans
chmod g+sw $modules_dir/storageServer/var/stor/buffer

chgrp $apache_group $modules_dir/htmlUI/var/templates_c
chgrp $apache_group $modules_dir/htmlUI/var/html/img

chmod g+sw $modules_dir/htmlUI/var/templates_c
chmod g+sw $modules_dir/htmlUI/var/html/img


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "";
echo "The HTML user interface for the LiveSupport development environment";
echo "for user $user is available at:";
echo "http://$ls_php_host:$ls_php_port/$ls_php_urlPrefix/htmlUI/var";
echo "";
echo "Done."

