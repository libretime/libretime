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
srcdir=$basedir/src
tmpdir=$basedir/tmp
toolsdir=$srcdir/tools
modules_dir=$srcdir/modules
products_dir=$srcdir/products
scheduler_dir=$products_dir/scheduler
scheduler_bin_dir=$scheduler_dir/bin

usrdir=`cd $basedir/usr; pwd;`


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster local user settings setup script.";
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
scheduler_storage_pass=change_me
dbserver=localhost
database=Campcaster-$user
dbuser=test
dbpassword=test
homedir=$HOME
configdir=$homedir/.campcaster
htmldir=$homedir/public_html
output_device=default
cue_device=default


echo "Configuring Campcaster development environment for user $user.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  host name:                              $hostname";
echo "  web server port:                        $http_port";
echo "  scheduler port:                         $scheduler_port";
echo "  storage password for the scheduler:     $scheduler_storage_pass";
echo "  database server:                        $dbserver";
echo "  database:                               $database";
echo "  database user:                          $dbuser";
echo "  database user password:                 $dbpassword";
echo "  apache daemon group:                    $apache_group";
echo "  home directory:                         $homedir";
echo "  configuration directory:                $configdir";
echo "  web base directory:                     $htmldir";
echo "  output audio device:                    $output_device";
echo ""


# check for the apache group to be a real group
group_tmp_file=/tmp/ls_group_check.$$
touch $group_tmp_file
test_result=`chgrp $apache_group $group_tmp_file 2> /dev/null`
if [ $? != 0 ]; then
    rm -f $group_tmp_file;
    echo "Unable to use apache deamon group $apache_group.";
    echo "Please check if $apache_group is a correct user group,";
    echo "and that the current user is a member of this group.";
    exit 1;
fi
rm -f $group_tmp_file;


#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------

installdir=$usrdir

ls_php_host=$hostname
ls_php_port=$http_port
ls_php_urlPrefix=~$user/campcaster

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
ls_tmp_dir=$installdir/tmp
ls_scheduler_daemon_command="$scheduler_bin_dir/campcaster-scheduler_devenv.sh"

ls_audio_output_device=$output_device
ls_audio_cue_device=$cue_device

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
ls_tmp_dir_s=`echo $ls_tmp_dir | sed -e "s/\//\\\\\\\\\//g"`
ls_scheduler_daemon_command_s=`echo $ls_scheduler_daemon_command | \
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
    s/ls_scheduler_port/$ls_scheduler_port/; \
    s/ls_audio_output_device/$ls_audio_output_device/; \
    s/ls_audio_cue_device/$ls_audio_cue_device/; \
    s/ls_tmp_dir/$ls_tmp_dir_s/; \
    s/ls_scheduler_daemon_command/$ls_scheduler_daemon_command_s/; \
    s/ls_scheduler_storage_pass/$scheduler_storage_pass/;"
echo
echo $replace_sed_string
echo


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
#  Check for required tools
#-------------------------------------------------------------------------------
echo "Checking for required tools..."

check_exe "sed" || exit 1;
check_exe "php" || exit 1;


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

cat $modules_dir/storageClient/etc/webAuthenticationClient.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/webAuthenticationClient.xml

cat $modules_dir/storageClient/etc/webStorage.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/webStorage.xml

cat $products_dir/scheduler/etc/campcaster-scheduler.xml.template \
    | sed -e "$replace_sed_string" \
    > $configdir/campcaster-scheduler.xml

cat $products_dir/gLiveSupport/etc/campcaster-studio.xml.user-template \
    | sed -e "$replace_sed_string" \
    > $configdir/campcaster-studio.xml

cat $products_dir/gLiveSupport/etc/authenticationClient.xml.user-template \
    | sed -e "$replace_sed_string" \
    > $configdir/authenticationClient.xml

cat $products_dir/gLiveSupport/etc/storageClient.xml.user-template \
    | sed -e "$replace_sed_string" \
    > $configdir/storageClient.xml


#-------------------------------------------------------------------------------
#  Create the public html directory, and links to the PHP directories
#-------------------------------------------------------------------------------
echo "Creating public HTML directory and links to web interfaces..."

mkdir -p $htmldir

rm -f $htmldir/campcaster

ln -s $modules_dir $htmldir/campcaster


#-------------------------------------------------------------------------------
#  Setup storageServer
#-------------------------------------------------------------------------------
echo "Setting up storageServer..."

make -C $modules_dir/storageServer storage || exit 1
make -C $modules_dir/archiveServer storage || exit 1


#-------------------------------------------------------------------------------
#  Setup the database tables for the scheduler
#-------------------------------------------------------------------------------
echo "Setting up database tables for the scheduler..."

make -C $products_dir/scheduler init || exit 1


#-------------------------------------------------------------------------------
#  Add "scheduler" user
#-------------------------------------------------------------------------------
echo "Adding the 'scheduler' user..."

php $modules_dir/storageServer/var/install/campcaster-user.php --addupdate scheduler $scheduler_storage_pass


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

cp $modules_dir/htmlUI/var/redirect.php $modules_dir/index.php
cp $modules_dir/htmlUI/var/redirect.php $modules_dir/htmlUI/index.php
cp $modules_dir/htmlUI/var/redirect.php $modules_dir/htmlUI/var/index.php
cp $modules_dir/htmlUI/var/redirect.php $modules_dir/htmlUI/var/html/index.php


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "";
echo "The HTML user interface for the Campcaster development environment";
echo "for user $user is available at:";
echo "http://$ls_php_host:$ls_php_port/$ls_php_urlPrefix/htmlUI/var";
echo "";
echo "Done."

