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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/htmlUI/bin/configureApache.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script configures apache for the htmlUI
#
#  Invoke as:
#  ./bin/configureApache.sh
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
    echo "LiveSupport apache configuration script.";
    echo "parameters";
    echo "";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o h -l help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
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


echo "Configuring apache for LiveSupport HTML interface.";
echo ""


#-------------------------------------------------------------------------------
#  Check to see if this script is being run as root
#-------------------------------------------------------------------------------
if [ `whoami` != "root" ]; then
    echo "Please run this script as root.";
    exit ;
fi


#-------------------------------------------------------------------------------
#  Configuring Apache
#-------------------------------------------------------------------------------
echo "Configuring apache ..."
CONFFILE=90_php_livesupport.conf
AP_DDIR_FOUND=no
for APACHE_DDIR in \
    /etc/apache/conf.d /etc/apache2/conf/modules.d /etc/httpd/conf.d \
    /etc/apache2/conf.d
do
    echo -n "$APACHE_DDIR "
    if [ -d $APACHE_DDIR ]; then
        echo "Y"
        AP_DDIR_FOUND=yes
        cp $etcdir/$CONFFILE $APACHE_DDIR
    else
        echo "N"
    fi
done
if [ "$AP_DDIR_FOUND" != "yes" ]; then
    echo "###############################"
    echo " Could not configure Apache"
    echo "  include following file into apache config manually:"
    echo "  $etcdir/$CONFFILE"
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
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

