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
#   Version  : $Revision: 1.3 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/pear/bin/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install pear packages needed by LiveSupport into the
# development system usr environment.
# To read more about pear, see http://pear.php.net/
#-------------------------------------------------------------------------------
description="PEAR packages needed by LiveSupport"

#PEAR-1.4.0a11
packages="
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

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
etcdir=$basedir/etc
srcdir=$basedir/src

dest_usrdir=`cd $basedir/../../usr; pwd;`
destdir=$dest_usrdir/lib/php
mkdir -p $destdir
mkdir -p $destdir/etc

echo "configuring $description (with destdir: $destdir)"

configtemplate=$etcdir/pear.conf.template
configfile=$destdir/etc/pear.conf
pearcmd="pear -c $configtemplate"
cp $configtemplate $configfile

$pearcmd config-set php_dir $destdir/php || exit 1
$pearcmd config-set bin_dir $destdir/bin || exit 1
$pearcmd config-set doc_dir $destdir/php/docs || exit 1
$pearcmd config-set data_dir $destdir/php/data || exit 1
$pearcmd config-set cache_dir $destdir/php/cache || exit 1
$pearcmd config-set test_dir $destdir/php/tests || exit 1
#$pearcmd config-show; exit

echo "installing $description (with destdir: $destdir)"
cd $srcdir
for i in $packages
do echo $i
    $pearcmd install $i.tgz || \
        { echo "*** ERROR installing $i"; exit 1; }
done

echo "PEAR packages install finished OK"
exit 0
