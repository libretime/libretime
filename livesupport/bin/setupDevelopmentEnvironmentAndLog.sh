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
#   Location : $URL$
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  A script to set up the development environment for LiveSupport
#
#  Invoke as:
#  ./bin/setupDevelopmentEnvironmentAndLog.sh
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

usrdir=`cd $basedir/usr; pwd;`

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport development environment setup script.";
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

if [ "x$apache_group" = "x" ]; then
    apache_group=apache;
fi


#------------------------------------------------------------------------------
#  All steps are being logged
#------------------------------------------------------------------------------
echo "";
echo "The compile process will be started. All steps are being logged in"; 
echo "$tmpdir ";
echo "";

#------------------------------------------------------------------------------
#  Cleaning the setup
#------------------------------------------------------------------------------
make -C $basedir modprod_distclean > $tmpdir/make_modprod_distclean_setup.log 2>&1
ls -l $tmpdir/make_modprod_distclean_setup.log >> $tmpdir/make_modprod_distclean_setup.log

#-------------------------------------------------------------------------------
#  Create the configure script, using setup parameters
#-------------------------------------------------------------------------------
# --prefix=$usrdir                 --with-www-docroot=$usrdir/var =/var/www
# --with-hostname=localhost        --with-apache-group=$apache_group
#
# --with-check-boost=no =yes       --with-check-gtk=yes =no
# --with-check-gtkmm=yes =no       --with-check-icu=yes =no
# --with-check-libxmlpp=yes =no
#
# --with-create-database=no =yes   --with-create-odbc-data-source=no =yes
# --with-init-database=no =yes     --with-configure-apache=no =yes
#
# --with-database=LiveSupport =LiveSupport-test
# --with-database-user=LiveSupport =test
# --with-database-password=LiveSupport =test
#
# --with-station-audio-out=default =default
# --with-studio-audio-out=default =default
# --with-studio-audio-cue=default =default

rm -rf $tmpdir/configure
echo "Now Configure ... ";
$bindir/autogen.sh > $tmpdir/configure_development_environment_autogen.log 2>&1
$basedir/configure --with-hostname=localhost --with-www-docroot=$usrdir/var \
                   --prefix=$usrdir --with-apache-group=$apache_group \
                   --with-check-boost=yes --with-check-gtk=yes \
                   --with-check-gtkmm=yes --with-check-icu=yes \
                   --with-check-libxmlpp=yes > $tmpdir/configure_development_environment.log 2>&1
echo "Configure is done, configure_development_environment.log is created";
echo "";


#-------------------------------------------------------------------------------
#  Compile step by step, including the tools
#-------------------------------------------------------------------------------
echo "Now Compiling ... Tools";
make -C $basedir tools_setup > $tmpdir/make_install_tools_setup.log 2>&1
ls -l $tmpdir/make_install_tools_setup.log >> $tmpdir/make_install_tools_setup.log
echo "Done Tools Setup, make_install_tools_setup.log is created";
echo "";
echo "Now Compiling ... Doxytag";
make -C $basedir doxytag_setup > $tmpdir/make_doxytag_setup.log 2>&1
ls -l $tmpdir/make_doxytag_setup.log >> $tmpdir/make_doxytag_setup.log
echo "Done Doxytag Setup, make_doxytag_setup.log is created";
echo "";
echo "Now Configure ... Modules ... Products";
make -C $basedir modules_setup > $tmpdir/make_configure_modules_setup.log 2>&1
ls -l $tmpdir/make_configure_modules_setup.log >> $tmpdir/make_configure_modules_setup.log
echo "Configure the Modules is done, make_configure_modules_setup.log is created";
make -C $basedir products_setup > $tmpdir/make_configure_products_setup.log 2>&1
ls -l $tmpdir/make_configure_products_setup.log >> $tmpdir/make_configure_products_setup.log
echo "Configure the Products is done, make_configure_products_setup.log is created";
echo "";
echo "Now Compiling ...";
make -C $basedir compile > $tmpdir/make_compile_setup.log 2>&1
ls -l $tmpdir/make_compile_setup.log >> $tmpdir/make_compile_setup.log
echo "Compiling is done, make_compile_setup.log is created";
echo "";

#-------------------------------------------------------------------------------
#  Checking what we have done
#-------------------------------------------------------------------------------
echo "Now Checking ...";
make -C $basedir check > $tmpdir/make_check_setup.log 2>&1
ls -l $tmpdir/make_check_setup.log >> $tmpdir/make_check_setup.log
echo "Checking is be done, make_check_setup.log is created";
echo "";

#-------------------------------------------------------------------------------
#  User setup
#-------------------------------------------------------------------------------
echo "Setting up user settings ..."

$bindir/user_setup.sh --apache-group=$apache_group || exit 1


#-------------------------------------------------------------------------------
#  We're done
#-------------------------------------------------------------------------------
echo "Done."

