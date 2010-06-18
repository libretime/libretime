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
#   Author   : $Author: fgerlits $
#   Version  : $Revision: 2292 $
#   Location : $URL: svn+ssh://tomash@code.campware.org/home/svn/repo/livesupport/trunk/livesupport/bin/postInstallStation.sh $
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#  This script makes installation steps for the Campcaster network hub.
#
#  Invoke as:
#  ./bin/hubSetup.sh
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
etcdir=$basedir/etc
srcdir=$basedir/src
tools_dir=$srcdir/tools
modules_dir=$srcdir/modules


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster network hub install script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -n, --hostname      The remotely accessible hostname [default `hostname -f`].";
    echo "  -D, --database      The name of the Campcaster database.";
    echo "                      [default: CampcasterHub]";
    echo "  -g, --apache-group  The group the apache daemon runs as.";
    echo "                      [default: www-data]";
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
    echo "  -P, --skip-postgresql    Don't modify posgresql configuration.";
    echo "  -A, --skip-apache    Don't modify apache configuration.";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o Ad:D:g:hi:n:p:Pr:s:u:w: -l apache-group:,database:,dbserver:,dbuser:,dbpassword:,directory:,help,hostname:,postgresql-dir:,postgresql-init-script:,skip-apache,skip-postgresql,www-root: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -S|--skip-apache)
            skip_apache="yes";
            shift;;
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
        -n|--hostname)
            hostname=$2;
            shift; shift;;
        -p|--postgresql-dir)
            postgresql_dir=$2;
            shift; shift;;
        -P|--skip-postgresql)
            skip_postgresql="yes";
            shift;;
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
    database=CampcasterHub;
fi

if [ "x$dbuser" == "x" ]; then
    dbuser=campcaster;
fi

if [ "x$dbpassword" == "x" ]; then
    dbpassword=campcaster;
fi

if [ "x$apache_group" == "x" ]; then
    apache_group=www-data;
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

if [ "x$hostname" == "x" ]; then
    hostname=`hostname -f`
fi

www_port=80

echo "Installing Campcaster network hub (archiveServer).";
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
echo "  hostname:                   $hostname";
echo "  www port:                   $www_port";
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
install_var_ls=$installdir/var/Campcaster

url_prefix=campcaster_hub

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
if [ "$skip_postgresql" != "yes" ]; then
    echo "Modifying postgresql access permissions...";

    pg_config_dir=$postgresql_dir
    pg_config_file=pg_hba.conf
    pg_config_file_saved=pg_hba.conf.before-campcaster

    if [ -f $pg_config_dir/$pg_config_file ] ; then
        mv -vf $pg_config_dir/$pg_config_file $pg_config_dir/$pg_config_file_saved ;
    fi
    cp -v $etcdir/$pg_config_file $pg_config_dir/$pg_config_file
    chown root:$postgres_user $pg_config_dir/$pg_config_file

    # don't use restart for the init script, as it might return prematurely
    # and in the later call to psql we wouldn't be able to connect
    ${postgresql_init_script} stop
    ${postgresql_init_script} start
fi

#-------------------------------------------------------------------------------
#  Configuring Apache
#-------------------------------------------------------------------------------
if [ "$skip_apache" != "yes" ]; then
    echo "Configuring apache ..."
    CONFFILE=90_php_campcaster.conf
    AP_DDIR_FOUND=no
    for APACHE_DDIR in \
        /etc/apache/conf.d /etc/apache2/conf.d /etc/apache2/conf/modules.d \
        /etc/httpd/conf.d /etc/apache2/modules.d
    do
        echo -n "$APACHE_DDIR "
        if [ -d $APACHE_DDIR ]; then
            echo "Y"
            AP_DDIR_FOUND=yes
            cp -v $basedir/etc/apache/$CONFFILE $APACHE_DDIR
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
    else
        echo "done"
        echo "Restarting apache...";
        AP_SCR_FOUND=no
        for APACHE_SCRIPT in apache apache2 httpd ; do
            echo -n "$APACHE_SCRIPT "
            if [ -x /etc/init.d/$APACHE_SCRIPT ]; then
                echo "Y"
                AP_SCR_FOUND=yes
                /etc/init.d/$APACHE_SCRIPT restart
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
    fi
fi

#-------------------------------------------------------------------------------
#  Create the necessary database user and database itself
#-------------------------------------------------------------------------------
echo "Creating database user '$ls_dbuser' and database '$ls_database' ...";

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
    echo "Also create a database called $ls_database, owned by this user.";
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
#  Configuring modules
#-------------------------------------------------------------------------------
echo "Configuring modules ...";

cd $tools_dir/pear && ./configure --prefix=$installdir
cd $modules_dir/alib && ./configure --prefix=$installdir
cd $modules_dir/archiveServer && \
        ./configure --prefix=$installdir \
                --with-hostname=$hostname \
                                --with-www-port=$www_port \
                                --with-database-server=$dbserver \
                                --with-database=$database \
                                --with-database-user=$dbuser \
                                --with-database-password=$dbpassword \
                                --with-url-prefix=$url_prefix
cd $modules_dir/getid3 && ./configure --prefix=$installdir
#cd $modules_dir/htmlUI && ./configure --prefix=$installdir \
#    --with-apache-group=$apache_group \
#    --with-www-docroot=$www_root \
#    --with-storage-server=$installdir/var/Campcaster/storageServer
cd $modules_dir/storageAdmin && ./configure --prefix=$installdir \
    --with-storage-server=$installdir/var/Campcaster/storageServer \
    --with-phppart-dir=$installdir/var/Campcaster/storageAdmin
cd $modules_dir/storageServer && \
        ./configure --prefix=$installdir \
                --with-apache-group=$apache_group \
                --with-hostname=$hostname \
            --with-www-docroot=$www_root \
                                --with-www-port=$www_port \
                                --with-database-server=$dbserver \
                                --with-database=$database \
                                --with-database-user=$dbuser \
                                --with-database-password=$dbpassword \
                                --with-init-database=no \
                                --with-url-prefix=$url_prefix


#-------------------------------------------------------------------------------
#   Install
#-------------------------------------------------------------------------------
echo "Installing modules ...";

mkdir -p $installdir
#$tools_dir/pear/bin/install.sh -d $installdir || exit 1
make -C $tools_dir/pear install
make -C $modules_dir/alib install
make -C $modules_dir/getid3 install
make -C $modules_dir/storageServer install
make -C $modules_dir/storageAdmin install
make -C $modules_dir/archiveServer install

mkdir -p $install_var_ls/storageServer/var/tests
for it in ex1.mp3 ex2.wav; do
    cp $modules_dir/storageServer/var/tests/$it \
        $install_var_ls/storageServer/var/tests
done

#-------------------------------------------------------------------------------
#  Create symlinks
#-------------------------------------------------------------------------------
echo "Creating symlinks...";

# create symlink for the PHP pages in apache's document root
webentry=$www_root/$url_prefix
rm -f $webentry
ln -vs $install_var_ls $webentry


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

#chgrp $apache_group $install_var_ls/storageServer/var/stor
#chgrp $apache_group $install_var_ls/storageServer/var/access
#chgrp $apache_group $install_var_ls/storageServer/var/trans
#chgrp $apache_group $install_var_ls/storageServer/var/stor/buffer

#chmod g+sw $install_var_ls/storageServer/var/stor
#chmod g+sw $install_var_ls/storageServer/var/access
#chmod g+sw $install_var_ls/storageServer/var/trans
#chmod g+sw $install_var_ls/storageServer/var/stor/buffer

#chgrp $apache_group $install_var_ls/htmlUI/var/templates_c
#chgrp $apache_group $install_var_ls/htmlUI/var/html/img

#chmod g+sw $install_var_ls/htmlUI/var/templates_c
#chmod g+sw $install_var_ls/htmlUI/var/html/img


#-------------------------------------------------------------------------------
#  Initialize the database
#-------------------------------------------------------------------------------
echo "Initializing database...";

# create PHP-related database tables
cd $install_var_ls/archiveServer/var/install
php -q install.php || exit 1;
cd -

#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."


exit

