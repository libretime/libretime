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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/tools/xmlrpc++/xmlrpc++-20040713/bin/prepare.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to prepare xmlrpc++ to be configured and compiled.
# For more information on xmlrpc++, see http://xmlrpcpp.sourceforge.net/
#-------------------------------------------------------------------------------
product=xmlrpc++-20040713 

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
installdir=`cd ${basedir}/../../../usr; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

tar=$basedir/src/$product.tar.gz

mkdir -p ${tmpdir}

# copy over install-sh, as AC_CONFIG_SUBDIRS will be looking for it
cp -r $bindir/install-sh $tmpdir

cd ${tmpdir}
if [ ! -d xmlrpc++ ]; then
    tar xfz ${tar}
    cd xmlrpc++
    patch -p1 < $etcdir/xmlrpc++-automake.patch
    patch -p1 < $etcdir/uninitialised_XmlRpcSource_ssl_ssl.patch
    patch -p1 < $etcdir/incorrect_XmlRpcValue_struct_tm_conversion.patch
fi

