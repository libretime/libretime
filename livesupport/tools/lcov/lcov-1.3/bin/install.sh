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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/lcov/lcov-1.3/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install cppunit into the development system usr
# environment.
# For more information on lcov, see http://ltp.sourceforge.net/coverage/lcov.php
#-------------------------------------------------------------------------------
product=lcov-1.3

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd; cd -`
installdir=`cd $basedir/../../../usr; pwd; cd -`
tmpdir=$basedir/tmp
etcdir=$basedir/etc
tar=$basedir/src/$product.tar.gz

echo "installing $product from $basedir to $installdir"


mkdir -p $tmpdir
cd $tmpdir

tar xfz $tar
cd $product
patch -p1 < $etcdir/lcov-1.3-geninfo-regexp.patch
# lcov installs itself into PREFIX/usr/bin, so our normal installdir won't work
lcov_installdir=`cd $installdir/..; pwd; cd -`
PREFIX=$lcov_installdir make install

cd $basedir
rm -rf tmp

