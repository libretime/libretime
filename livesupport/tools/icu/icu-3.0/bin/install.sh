#!/bin/sh
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/icu/icu-3.0/bin/Attic/install.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to install ICU into the development system usr
# environment.
# For more information on ICU, see http://oss.software.ibm.com/icu/
#-------------------------------------------------------------------------------
product=icu-3.0

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
installdir=`cd $basedir/../../../usr; pwd;`
tmpdir=$basedir/tmp
tar=$basedir/src/$product.tgz
docdir=$installdir/share/doc/icu/3.0
doczip=$basedir/src/$product-docs.zip

echo "installing $product from $basedir to $installdir"


mkdir -p $tmpdir
cd $tmpdir

# compile and install the library

tar xfz $tar
cd icu/source
./runConfigureICU LinuxRedHat --prefix=$installdir
make install

# install the documentation as well

cd $tmpdir
mkdir -p $docdir
unzip $doczip -d $docdir 

# clean up

cd $basedir
rm -rf tmp

