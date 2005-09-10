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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/bin/setupDirs.sh,v $
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# This script does httpd writeable directories setup
#-------------------------------------------------------------------------------

WWW_ROOT=`cd var/install; php -q getWwwRoot.php` || exit $?
echo "# archiveServer root URL: $WWW_ROOT"

HTTP_GROUP=`bin/getUrl.sh $WWW_ROOT/install/getGname.php` || \
 {
  ERN=$?;
  echo $HTTP_GROUP;
  echo " -> Probably wrong setting in var/conf.php: URL configuration";
  exit $ERN; 
 }
echo "# group running http daemon: $HTTP_GROUP"

for i in $*
do
 echo "mkdir $i"
  mkdir -p $i || exit $?
  chown :$HTTP_GROUP $i || \
   {
    ERN=$?;
    echo " -> You should have permissions to set group owner to group $HTTP_GROUP";
    exit $ERN;
   }
  chmod g+sw $i || exit $?
done

echo "# Directories setup finished OK"
exit 0
