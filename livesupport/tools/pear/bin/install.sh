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
#   Author   : $Author: tomas $
#   Version  : $Revision: 1.4 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/pear/bin/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install PEAR packages needed by LiveSupport locally
# into the Livesupport usr environment.
# To read more about PEAR, see http://pear.php.net/
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
srcdir=$basedir/src

description="PEAR packages needed by LiveSupport"

packages4install="
Archive_Tar-1.3.1
Console_Getopt-1.2
XML_RPC-1.3.0RC1
PEAR-1.3.5
Calendar-0.5.2
DB-1.7.6
File-1.2.0
File_Find-0.3.1
HTML_Common-1.2.1
HTML_QuickForm-3.2.4pl1
XML_Util-1.1.1
XML_Parser-1.2.6
XML_Beautifier-1.1
XML_Serializer-0.15.0
"

packages_required="
DB
Calendar
File
File_Find
HTML_Common
HTML_QuickForm
XML_Beautifier
XML_Parser
XML_RPC
XML_Serializer
XML_Util
"

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport PEAR packages install script.";
    echo " parameters:";
    echo "";
    echo "  -d, --directory  The LiveSupport installation directory, required.";
    echo "  -h, --help       Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:h -l directory:,help -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            installdir=$2;
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

if [ "x$installdir" == "x" ]; then
    echo "Required parameter install directory not specified.";
    printUsage;
    exit 1;
fi

#-------------------------------------------------------------------------------
#  Customize the configuration file with the appropriate values
#-------------------------------------------------------------------------------
rootdir=`cd $installdir; pwd;`
destdir=$rootdir/usr/lib/php

configtemplate=$etcdir/pear.conf.template
configfile=$destdir/etc/pear.conf
pearcmd="pear -c $configtemplate"

echo "Configuring $description"
echo " (with destdir: $destdir)"

mkdir -p $destdir
mkdir -p $destdir/etc

cp $configtemplate $configfile

$pearcmd config-set php_dir $destdir/php || exit 1
$pearcmd config-set bin_dir $destdir/bin || exit 1
$pearcmd config-set doc_dir $destdir/php/docs || exit 1
$pearcmd config-set data_dir $destdir/php/data || exit 1
$pearcmd config-set cache_dir $destdir/php/cache || exit 1
$pearcmd config-set test_dir $destdir/php/tests || exit 1
#$pearcmd config-show; exit

#-------------------------------------------------------------------------------
#   Install the packages
#-------------------------------------------------------------------------------
echo "Installing $description to directory:"
echo " $destdir"
cd $srcdir
for i in $packages4install
do echo -n " "
    $pearcmd install $i.tgz
done

#-------------------------------------------------------------------------------
#  Function to check for a PEAR module
#
#  @param $1 the name of the PEAR module
#  @return 0 if the module is available, non-0 otherwise
#-------------------------------------------------------------------------------
check_pear_module() {
    test_result=`$pearcmd info $1`
#    test_result=`pear info $1`
    if [ $? = 0 ]; then
#        echo "PEAR module $1 found...";
        echo "OK"
        return 0;
    else
#        echo "PEAR module $1 not found...";
        echo "NOT found ...";
        return 1;
    fi
}

#-------------------------------------------------------------------------------
#  Check PEAR packages
#  (because pear install returns exicode 1 even if package already exists)
#-------------------------------------------------------------------------------

for i in $packages_required
do echo -n " checking PEAR module $i: "
    check_pear_module $i || exit 1;
done

#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Install of $description finished OK."
exit 0
