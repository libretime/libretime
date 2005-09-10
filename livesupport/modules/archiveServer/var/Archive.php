<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/Archive.php,v $

------------------------------------------------------------------------------*/
require_once dirname(__FILE__)."/../../storageServer/var/LocStor.php";

/**
 *  Extension to StorageServer to act as ArchiveServer
 */
class Archive extends LocStor{

    /**
     *  Check uploaded file
     *
     *  @param token string
     *  @return array(md5h string, size int, url string)
     */
    function uploadCheck($token)
    {
        return $this->bsCheckPut($token);
    }


    /* ==================================================== auxiliary methods */

}
?>