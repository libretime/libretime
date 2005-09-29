#!/bin/bash
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
# This script call locstor.resetStorage XMLRPC method
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
WWW_ROOT=`cd $reldir/var/install; php -q getWwwRoot.php` || exit $?
echo "# storageServer root URL: $WWW_ROOT"

#$reldir/var/xmlrpc/xr_cli_test.py -s $WWW_ROOT/xmlrpc/xrLocStor.php \
# resetStorage || exit $?

cd $reldir/var/xmlrpc
php -q xr_cli_test.php -s $WWW_ROOT/xmlrpc/xrLocStor.php \
 resetStorage 1 0 || exit $?

echo "# resetStorage: OK"
exit 0
