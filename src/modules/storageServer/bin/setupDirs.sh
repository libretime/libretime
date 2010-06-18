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
# This script does httpd writeable directories setup
#-------------------------------------------------------------------------------

WWW_ROOT=`cd var/install; php -q getWwwRoot.php` || exit $?
echo " *** StorageServer bin/setupDirs.sh BEGIN"
echo " *** Root URL: $WWW_ROOT"
PHP_PWD=`bin/getUrl.sh $WWW_ROOT/install/getPwd.php` || \
  {
    errno=$?
    if [ $errno -eq 22 ]
    then
        echo "root URL is not accessible - configure HTTP entry point, please"
    fi
    exit $errno
  }
echo " *** Webspace mapping test:"
echo " *** mod_php : $PHP_PWD"
INSTALL_DIR="$PWD/var/install"
echo " *** install : $INSTALL_DIR"
if [ $PHP_PWD == $INSTALL_DIR ]; then
    echo " *** Mapping OK"
else
    echo " *** WARNING: there was a problem with webspace mapping!!!"
fi

HTTP_GROUP=`bin/getUrl.sh $WWW_ROOT/install/getGname.php` || \
 {
  ERN=$?;
  echo $HTTP_GROUP;
  echo " -> Probably wrong setting in var/conf.php: URL configuration";
  exit $ERN;
 }
echo " *** The system group that is running the http daemon: '$HTTP_GROUP'"

for i in $*
do
  echo " *** chown :$HTTP_GROUP $i"
  if [ -G $i ]; then
    chown :$HTTP_GROUP $i || \
    {
      ERN=$?;
      echo "ERROR: chown :$HTTP_GROUP $i -> You should have permissions to set group owner to group '$HTTP_GROUP'";
      exit $ERN;
    }
    echo " *** chmod g+sw $i"
    chmod g+sw $i || exit $?
  fi
done

echo " *** StorageServer bin/setupDirs.sh END"
exit 0
