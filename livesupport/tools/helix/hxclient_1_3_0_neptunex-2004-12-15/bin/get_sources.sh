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
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/helix/hxclient_1_3_0_neptunex-2004-12-15/bin/Attic/get_sources.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#   Run this script to get the sources from the Helix CVS server, for
#   branch hxclient_1_3_0_neptunex and for the date 2004-12-15
#
#   Before running this script, register at http://helixcommunity.org/
#   to have CVS access to the Helix DNA CVS server. This script expects
#   your Helix user id as the first parameter.
#-------------------------------------------------------------------------------

if [ "x$1" == "x" ]; then
    echo "specify your Helix CVS user name as the first parameter to this script";
    exit;
fi

helixUser=$1
helixProfile=helix-client-all-defines-nodist
branch=hxclient_1_3_0_neptunex
date=2004-12-15
product=$branch-$date
export SYSTEM_ID=linux-2.4-glibc23-i686

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
installdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
etcdir=$basedir/etc
srcdir=$basedir/src
tar=$srcdir/$product.tar.bz2

echo "getting Helix sources for user $helixUser";
echo "using branch $branch and date $date";


mkdir -p $tmpdir
cd $tmpdir


# create a directory with the name of the tarball
mkdir $product
cd $product

# check out the build tool
cvs -d :ext:$helixUser@cvs.helixcommunity.org:/cvsroot/ribosome co build

export BUILD_ROOT=`pwd`/build
build=$BUILD_ROOT/bin/build

# make the build tool update its BIF files
$build -m $branch -P $helixProfile -o -v splay

# get the sources
$build -m $branch -P $helixProfile -D $date -h -o -v splay

# remove remnants of the CVS system
rm -rf `find . -type d -name CVS`

# create the source tarball
cd $tmpdir
tar cfj $tar $product


# clean up
cd $basedir
rm -rf tmp

