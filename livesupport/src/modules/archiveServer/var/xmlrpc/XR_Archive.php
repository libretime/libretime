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
    Location : $URL$

------------------------------------------------------------------------------*/

require_once "../../../storageServer/var/xmlrpc/XR_LocStor.php";

/**
 *  XML-RPC interface for Archive
 *  
 */
class XR_Archive extends XR_LocStor{
    /**
     *  Simple ping method - return strtouppered string
     *
     *  @param input XMLRPC struct
     */
    function xr_ping($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        return new XML_RPC_Response(
            XML_RPC_encode(strtoupper($r['par']), "string")
        );
    }

    /**
     *  Check state of file upload
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadCheck($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadCheck($r['token']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

}

?>
