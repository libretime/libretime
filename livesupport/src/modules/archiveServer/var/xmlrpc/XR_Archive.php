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

require_once "../Archive.php";

/**
 *  XML-RPC interface for Archive
 *  
 */
class XR_Archive extends Archive{
    /**
     *  Simple ping method - return strtouppered string
     *
     *  @param input XMLRPC struct
     */
    function xr_ping($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = date("Ymd-H:i:s")." Network hub answer: {$r['par']}";
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadOpen($r['sessid'], $r['chsum']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
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

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadClose($r['token'], $r['trtype'], $r['pars']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_uploadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_downloadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadOpen($r['sessid'], $r['trtype'], $r['pars']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_downloadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_downloadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadClose($r['token'], $r['trtype']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_downloadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_prepareHubInitiatedTransfer($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        foreach(
            array('trtype'=>NULL, 'direction'=>'up', 'pars'=>array())
        as $k=>$dv)
            { if(!isset($r[$k])) $r[$k]=$dv;    }
        $res = $this->prepareHubInitiatedTransfer(
            $r['target'], $r['trtype'], $r['direction'], $r['pars']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_prepareHubInitiatedTransfer: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_listHubInitiatedTransfers($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        foreach(
            array('target'=>NULL, 'direction'=>NULL, 'trtok'=>NULL)
        as $k=>$dv)
            { if(!isset($r[$k])) $r[$k]=$dv;    }
        $res = $this->listHubInitiatedTransfers(
            $r['target'], $r['direction'], $r['trtok']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_listHubInitiatedTransfers: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  
     *
     *  @param input XMLRPC struct
     */
    function xr_setHubInitiatedTransfer($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->setHubInitiatedTransfer(
            $r['target'], $r['trtok'], $r['state']);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 803,
                "xr_setHubInitiatedTransfer: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


}

?>
