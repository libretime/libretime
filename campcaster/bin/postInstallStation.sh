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
#  This script makes post-installation steps for the Campcaster Station.
#
#  Invoke as:
#  ./bin/postInstallStation.sh
#
#  To get usage help, try the -h option
#
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster Station post-install script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -D, --database      The name of the Campcaster database.";
    echo "                      [default: Campcaster]";
    echo "  -g, --apache-group  The group the apache daemon runs as.";
    echo "                      [default: apache]";
    echo "  -r, --www-root      The root directory for web documents served";
    echo "                      by apache [default: /var/www]";
    echo "  -s, --dbserver      The name of the database server host.";
    echo "                      [default: localhost]";
    echo "  -u, --dbuser        The name of the database user to access the"
    echo "                      database. [default: campcaster]";
    echo "  -w, --dbpassword    The database user password.";
    echo "                      [default: campcaster]";
    echo "  -p, --postgresql-dir    The postgresql data directory, containing";
    echo "                      pg_hba.conf [default: /etc/postgresql]";
    echo "  -i, --postgresql-init-script    The name of the postgresql init";
    echo "                      script [default: /etc/init.d/postgresql]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:D:g:hi:p:r:s:u:w: -l apache-group:,database:,dbserver:,dbuser:,dbpassword:,directory:,help,postgresql-dir:,postgresql-init-script:,www-root: -n $CMD -- "$@") || exit 1
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
        -h|--help)
            printUsage;
            exit 0;;
        -i|--postgresql-init-script)
            postgresql_init_script=$2;
            shift; shift;;
        -p|--postgresql-dir)
            postgresql_dir=$2;
            shift; shift;;
        -r|--www-root)
            www_root=$2;
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

if [ "x$dbserver" == "x" ]; then
    dbserver=localhost;
fi

if [ "x$database" == "x" ]; then
    database=Campcaster;
fi

if [ "x$dbuser" == "x" ]; then
    dbuser=campcaster;
fi

if [ "x$dbpassword" == "x" ]; then
    dbpassword=campcaster;
fi

if [ "x$apache_group" == "x" ]; then
    apache_group=apache;
fi

if [ "x$postgresql_dir" == "x" ]; then
    postgresql_dir=/etc/postgresql;
fi

if [ "x$postgresql_init_script" == "x" ]; then
    postgresql_init_script=/etc/init.d/postgresql;
fi

if [ "x$www_root" == "x" ]; then
    www_root=/var/www;
fi

echo "Making post-install steps for Campcaster Station.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  installation directory:     $installdir";
echo "  database server:            $dbserver";
echo "  database:                   $database";
echo "  database user:              $dbuser";
echo "  database user password:     $dbpassword";
echo "  apache daemon group:        $apache_group";
echo "  apache document root:       $www_root";
echo "  postgresql data directory:  $postgresql_dir";
echo "  postgresql init script:     $postgresql_init_script";
echo ""

#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_dbserver=$dbserver
ls_dbuser=$dbuser
ls_dbpassword=$dbpassword
ls_database=$database

postgres_user=postgres

install_bin=$installdir/bin
install_etc=$installdir/etc
install_lib=$installdir/lib
install_usr=$installdir/usr
install_var=$installdir/var
install_var_ls=$install_var/Campcaster


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
check_exe "php" || exit 1;
check_exe "odbcinst" || exit 1;


#-------------------------------------------------------------------------------
#   Check for the apache group to be a real group
#-------------------------------------------------------------------------------
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
#  Install the new pg_hba.conf file
#-------------------------------------------------------------------------------
echo "Modifying postgresql access permissions...";

pg_config_dir=$postgresql_dir
pg_config_file=pg_hba.conf
pg_config_file_saved=pg_hba.conf.before-campcaster

if [ -f $pg_config_dir/$pg_config_file ] ; then
    mv -f $pg_config_dir/$pg_config_file $pg_config_dir/$pg_config_file_saved ;
fi
cp $install_etc/$pg_config_file $pg_config_dir/$pg_config_file
chown root:$postgres_user $pg_config_dir/$pg_config_file

# don't use restart for the init script, as it might return prematurely
# and in the later call to psql we wouldn't be able to connect
${postgresql_init_script} stop
${postgresql_init_script} start


#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
${install_bin}/createDatabase.sh --database=${ls_database} \
                                 --dbuser=${ls_dbuser} \
                                 --dbpassword=${ls_dbpassword} \
                                 --dbserver=${ls_dbserver}


#-------------------------------------------------------------------------------
#  Create the ODBC data source and driver
#-------------------------------------------------------------------------------
${install_bin}/createOdbcDataSource.sh --database=${ls_database} \
                                       --dbserver=${ls_dbserver}


#-------------------------------------------------------------------------------
#  Check whether the storage server directory has been replaced with a mount
#  point for an NFS share.
#-------------------------------------------------------------------------------
storagedir=$installdir/var/storageServer
storage_is_local=yes
if [ "`mount | grep -o \"on $storagedir \"`" = "on $storagedir " ]; then
    storage_is_local=no
fi


#-------------------------------------------------------------------------------
#  Setup directory permissions
#-------------------------------------------------------------------------------
echo "Setting up directory permissions..."

chgrp $apache_group $install_var_ls/archiveServer/var/stor
chgrp $apache_group $install_var_ls/archiveServer/var/access
chgrp $apache_group $install_var_ls/archiveServer/var/trans
chgrp $apache_group $install_var_ls/archiveServer/var/stor/buffer

chmod g+sw $install_var_ls/archiveServer/var/stor
chmod g+sw $install_var_ls/archiveServer/var/access
chmod g+sw $install_var_ls/archiveServer/var/trans
chmod g+sw $install_var_ls/archiveServer/var/stor/buffer

if [ "$storage_is_local" = "yes" ]; then
    chgrp $apache_group $install_var_ls/storageServer/var/stor
    chgrp $apache_group $install_var_ls/storageServer/var/access
    chgrp $apache_group $install_var_ls/storageServer/var/trans
    chgrp $apache_group $install_var_ls/storageServer/var/stor/buffer

    chmod g+sw $install_var_ls/storageServer/var/stor
    chmod g+sw $install_var_ls/storageServer/var/access
    chmod g+sw $install_var_ls/storageServer/var/trans
    chmod g+sw $install_var_ls/storageServer/var/stor/buffer
fi

chgrp $apache_group $install_var_ls/htmlUI/var/templates_c
chgrp $apache_group $install_var_ls/htmlUI/var/html/img

chmod g+sw $install_var_ls/htmlUI/var/templates_c
chmod g+sw $install_var_ls/htmlUI/var/html/img

#-------------------------------------------------------------------------------
#  Configuring Apache
#-------------------------------------------------------------------------------
echo "Configuring apache ..."
CONFFILE=90_php_campcaster.conf
AP_DDIR_FOUND=no
for APACHE_DDIR in \
    /etc/apache/conf.d /etc/apache2/conf.d /etc/apache2/conf/modules.d \
    /etc/httpd/conf.d
do
    echo -n "$APACHE_DDIR "
    if [ -d $APACHE_DDIR ]; then
        echo "Y"
        AP_DDIR_FOUND=yes
        cp $basedir/etc/apache/$CONFFILE $APACHE_DDIR
        break
    else
        echo "N"
    fi
done
if [ "$AP_DDIR_FOUND" != "yes" ]; then
    echo "###############################"
    echo " Could not configure Apache"
    echo "  include following file into apache config manually:"
    echo "  $basedir/etc/apache/$CONFFILE"
    echo "###############################"
fi
echo "done"

echo "Restarting apache...";
AP_SCR_FOUND=no
for APACHE_SCRIPT in apache apache2 httpd ; do
    echo -n "$APACHE_SCRIPT "
    if [ -x /etc/init.d/$APACHE_SCRIPT ]; then
        echo "Y"
        AP_SCR_FOUND=yes
        /etc/init.d/$APACHE_SCRIPT restart
        break
    else
        echo "N"
    fi
done
if [ "$AP_SCR_FOUND" != "yes" ]; then
    echo "###############################"
    echo " Could not reload Apache"
    echo "  please reload apache manually"
    echo "###############################"
fi
echo "done"


#-------------------------------------------------------------------------------
#  Create symlinks
#-------------------------------------------------------------------------------
echo "Creating symlinks...";

# create symlink for the PHP pages in apache's document root
rm -f $www_root/campcaster
ln -s $install_var_ls $www_root/campcaster


#-------------------------------------------------------------------------------
#  Initialize the database
#-------------------------------------------------------------------------------
echo "Initializing database...";

if [ "$storage_is_local" = "yes" ]; then
    # create PHP-related database tables
    cd $install_var_ls/storageServer/var/install
    # workaround for #2059; restore to "exit 1" after the ticket is closed
    php -q install.php || exit 1;
    #php -q install.php || true
    cd -
fi

# create PHP-related database tables
cd $install_var_ls/archiveServer/var/install
# workaround for ticket #2059; restore to "exit 1" after the ticket is closed
php -q install.php || exit 1;
#php -q install.php || true
cd -

# create scheduler-related database tables
cd $installdir
./bin/campcaster-scheduler.sh install || exit 1;
cd -


#-------------------------------------------------------------------------------
#  Generate a random password for the scheduler's access to the storage
#-------------------------------------------------------------------------------
if [ "$storage_is_local" = "yes" ]; then
    grep -q 'ls_scheduler_storage_pass' $install_etc/campcaster-scheduler.xml
    if [ $? = 0 ]; then
        SCHEDULER_STORAGE_PASS=`pwgen -N1 -c -n -s`
        php -q $install_var_ls/storageServer/var/install/campcaster-user.php \
            --addupdate scheduler ${SCHEDULER_STORAGE_PASS}
        sed -i -e "s/ls_scheduler_storage_pass/${SCHEDULER_STORAGE_PASS}/" \
            $install_etc/campcaster-scheduler.xml
    fi
fi

#-------------------------------------------------------------------------------
#  Initialize the twitter cron
#-------------------------------------------------------------------------------
echo "Initializing twitter cron...";

cd $install_var_ls/htmlUI/var/install
# workaround for #2059; restore to "exit 1" after the ticket is closed
php -q install.php || exit 1;
#php -q install.php || true
cd -

# We need the scheduler password here too
sed -i -e "s/change_me/${SCHEDULER_STORAGE_PASS}/" \
            $install_var_ls/htmlUI/var/html/ui_twitterCron.php



#-------------------------------------------------------------------------------
#  Update the database, if necessary
#-------------------------------------------------------------------------------
if [ "$storage_is_local" = "yes" ]; then
    php -q $install_var_ls/storageServer/var/install/upgrade/upgrade.php
fi


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

