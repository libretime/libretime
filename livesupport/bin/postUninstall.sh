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
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/postUninstall.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script makes post-uninstallation steps for LiveSupport.
#
#  Invoke as:
#  ./bin/postUninstall.sh
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


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport post-uninstall script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -D, --database      The name of the LiveSupport database.";
    echo "                      [default: LiveSupport]";
    echo "  -r, --www-root      The root directory for web documents served";
    echo "                      by apache [default: /var/www]";
    echo "  -s, --dbserver      The name of the database server host.";
    echo "                      [default: localhost]";
    echo "  -u, --dbuser        The name of the database user to access the"
    echo "                      database. [default: livesupport]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:D:hr:s:u: -l database:,dbserver:,dbuser:,directory:,help,www-root: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            installdir=$2;
            shift; shift;;
        -D|--database)
            database=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -r|--www-root)
            www_root=$2;
            shift; shift;;
        -s|--dbserver)
            dbserver=$2;
            shift; shift;;
        -u|--dbuser)
            dbuser=$2;
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
    database=LiveSupport;
fi

if [ "x$dbuser" == "x" ]; then
    dbuser=livesupport;
fi

if [ "x$www_root" == "x" ]; then
    www_root=/var/www
fi


echo "Making post-uninstall steps for LiveSupport.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  installation directory: $installdir";
echo "  database server:        $dbserver";
echo "  database:               $database";
echo "  database user:          $dbuser";
echo "  apache document root:   $www_root";
echo ""

#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_dbserver=$dbserver
ls_dbuser=$dbuser
ls_database=$database


postgres_user=postgres

install_bin=$installdir/bin
install_etc=$installdir/etc
install_lib=$installdir/lib
install_tmp=$installdir/tmp
install_var=$installdir/var


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
#  Remove symlinks
#-------------------------------------------------------------------------------
echo "Removing symlinks...";

# remove symlink for the PHP pages in apache's document root
rm -f $www_root/livesupport


#-------------------------------------------------------------------------------
#  Delete data files
#-------------------------------------------------------------------------------
echo "Deleting data files...";

rm -rf $installdir/var/htmlUI/var/html/img/*
rm -rf $installdir/var/htmlUI/var/templates_c/*
rm -rf $installdir/var/storageServer/var/stor/*
rm -rf $installdir/var/storageServer/var/access/*
rm -rf $installdir/var/storageServer/var/trans/*
rm -rf $installdir/var/archiveServer/var/stor/*
rm -rf $installdir/var/archiveServer/var/access/*
rm -rf $installdir/var/archiveServer/var/trans/*


#-------------------------------------------------------------------------------
#  Remove the ODBC data source and driver
#-------------------------------------------------------------------------------
echo "Removing ODBC data source and driver...";

echo "Removing LiveSupport ODBC data source...";
odbcinst -u -s -l -n $ls_database || exit 1;

echo "De-registering ODBC PostgreSQL driver...";
odbcinst -u -d -v -n PostgreSQL || exit 1;


#-------------------------------------------------------------------------------
#  Remove the database user and the database itself
#-------------------------------------------------------------------------------
echo "Removing database and database user...";

if [ "x$ls_dbserver" == "xlocalhost" ]; then
    su - $postgres_user -c "echo \"DROP DATABASE \\\"$ls_database\\\" \"\
                            | psql template1" \
        || echo "Couldn't drop database $ls_database.";

    su - $postgres_user -c "echo \"DROP USER $ls_dbuser \"\
                            | psql template1" \
        || echo "Couldn't drop database user $ls_dbuser.";

else
    echo "Unable to automatically drop database user and table for";
    echo "remote database $ls_dbserver.";
    echo "Make sure to drop database user $ls_dbuser on database server";
    echo "at $ls_dbserver.";
    echo "Also drop the database called $ld_database, owned by this user.";
    echo "";
    echo "The easiest way to achieve this is by issuing the following SQL";
    echo "commands to PostgreSQL:";
    echo "DROP DATABASE \"$ls_database\";";
    echo "DROP USER $ls_dbuser;";
fi


# TODO: check for the success of these operations somehow


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

