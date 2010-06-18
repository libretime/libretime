#!/bin/bash
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# Run this script to prepare xmlrpc++ to be configured and compiled.
# For more information on xmlrpc++, see http://xmlrpcpp.sourceforge.net/
#-------------------------------------------------------------------------------
product=xmlrpc++-20040713 

reldir=`dirname $0`/..
basedir=`cd ${reldir}; pwd;`
bindir=${basedir}/bin
etcdir=${basedir}/etc
tmpdir=${basedir}/tmp

tar=$basedir/src/$product.tar.gz

mkdir -p ${tmpdir}

cd ${tmpdir}
if [ ! -d xmlrpc++ ]; then
    tar xfz ${tar}
    cd xmlrpc++
    # see http://sourceforge.net/tracker/index.php?func=detail&aid=990356&group_id=70654&atid=528555
    patch -p1 < $etcdir/xmlrpc++-automake.patch
    # see http://sourceforge.net/tracker/index.php?func=detail&aid=990676&group_id=70654&atid=528555
    patch -p1 < $etcdir/uninitialised_XmlRpcSource_ssl_ssl.patch
    # see http://sourceforge.net/tracker/?group_id=70654&atid=528555&func=detail&aid=1085119
    patch -p1 < $etcdir/incorrect_XmlRpcValue_struct_tm_conversion.patch
fi

