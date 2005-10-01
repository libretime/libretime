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
installdir=$basedir/../../usr

package="PEAR packages needed by LiveSupport"

packages_required="
Archive_Tar
Console_Getopt
XML_RPC
PEAR
Calendar
DB
File
File_Find
HTML_Common
HTML_QuickForm
XML_Util
XML_Parser
XML_Beautifier
XML_Serializer
"

VERSION_Archive_Tar=Archive_Tar-1.3.1
VERSION_Console_Getopt=Console_Getopt-1.2
VERSION_XML_RPC=XML_RPC-1.4.3-ls1
VERSION_PEAR=PEAR-1.3.5
VERSION_Calendar=Calendar-0.5.2
VERSION_DB=DB-1.7.6
VERSION_File=File-1.2.0
VERSION_File_Find=File_Find-0.3.1
VERSION_HTML_Common=HTML_Common-1.2.1
VERSION_HTML_QuickForm=HTML_QuickForm-3.2.4pl1
VERSION_XML_Util=XML_Util-1.1.1
VERSION_XML_Parser=XML_Parser-1.2.6
VERSION_XML_Beautifier=XML_Beautifier-1.1
VERSION_XML_Serializer=XML_Serializer-0.15.0

#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "LiveSupport PEAR packages install script.";
    echo " parameters:";
    echo "";
    echo "  -d, --directory  The LiveSupport installation directory";
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

#-------------------------------------------------------------------------------
#  Customize the configuration file with the appropriate values
#-------------------------------------------------------------------------------
destdir=`cd $installdir; pwd;`
peardir=$destdir/lib/pear

configtemplate=$etcdir/pear.conf.template
configfile=$destdir/etc/pear.conf
pearcmd="pear -c $configfile"

echo "Configuring $package"
echo " (with destdir: $destdir)"

mkdir -p $destdir
mkdir -p $destdir/etc

cp -pP $configtemplate $configfile

$pearcmd config-set php_dir $peardir || exit 1
$pearcmd config-set bin_dir $destdir/bin || exit 1
$pearcmd config-set doc_dir $peardir/docs || exit 1
$pearcmd config-set data_dir $peardir/data || exit 1
$pearcmd config-set cache_dir $peardir/cache || exit 1
$pearcmd config-set test_dir $peardir/tests || exit 1
#$pearcmd config-show; exit

#-------------------------------------------------------------------------------
#  Function to check for a PEAR module
#
#  @param $1 the name of the PEAR module
#  @return 0 if the module is available, non-0 otherwise
#-------------------------------------------------------------------------------
check_pear_module() {
    test_result=`$pearcmd info $1`
    if [ $? = 0 ]; then
        #echo "OK"
        return 0;
    else
        #echo "NOT installed";
        return 1;
    fi
}

#-------------------------------------------------------------------------------
#   Install the packages
#-------------------------------------------------------------------------------
echo "Installing $package to directory:"
echo " $destdir"
cd $srcdir

for pkg in $packages_required
do echo -n " "
    echo -n "$pkg: "
    eval "pkgv=\$VERSION_$pkg"
    check_pear_module $pkg && (
        $pearcmd upgrade $pkgv.tgz >/dev/null && echo -n "upgrading to $pkgv"
        #|| echo -n "code: $?"
    ) || (
#        $pearcmd install $pkgv.tgz >/dev/null && echo -n "installing $pkgv" || exit 1
        $pearcmd install $pkgv.tgz  && echo -n "installing $pkgv" || exit 1
    )
    check_pear_module $pkg && echo " OK" || exit 1
done

#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Install of $package finished OK."
exit 0
