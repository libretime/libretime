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
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/xmlrpc/xrArchive.php,v $

------------------------------------------------------------------------------*/
include_once "../../../storageServer/var/xmlrpc/xmlrpc.inc";
include_once "../../../storageServer/var/xmlrpc/xmlrpcs.inc";
require_once '../conf.php';
require_once 'DB.php';
require_once "../Archive.php";

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

/**
 *  XMLRPC layer for Archive module
 */
class XR_Archive extends Archive{

    /**
     *  Call LocStor::authenticate
     *
     *  @param input XMLRPC struct
     */
    function xr_authenticate($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->authenticate($r['login'], $r['pass']);
        return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    }

    /**
     *  Call LocStor::existsAudioClip
     *
     *  @param input XMLRPC struct
     */
    function xr_existsAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        #$this->debugLog(join(', ', $r));
        $res = $this->existsAudioClip($r['sessid'], $r['gunid']);
        #$this->debugLog($res);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_existsAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    }

    /**
     *  Call LocStor::deleteAudioClip
     *
     *  @param input XMLRPC struct
     */
    function xr_deleteAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->deleteAudioClip($r['sessid'], $r['gunid']);
        if(!PEAR::isError($res))
            return new xmlrpcresp(new xmlrpcval($res, "boolean"));
        else
            return new xmlrpcresp(0, 803,
                "xr_deleteAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /* ======================================================= upload methods */

    /**
     *  Open general file upload
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadOpen($r['sessid'], $r['trid'], $r['type']);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_uploadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(xmlrpc_encoder($res));
    }

    /**
     *  Check general file upload
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadCheck($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadCheck($r['sessid'], $r['url']);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_uploadCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(xmlrpc_encoder($res));
    }


    /**
     *  Close general file upload
     *
     *  @param input XMLRPC struct
     */
    function xr_uploadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->uploadClose($r['sessid'], $r['url'], $r['type'], $r['gunid']);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_uploadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(xmlrpc_encoder($res));
    }

    /* ===================================================== download methods */
    /**
     *  Open general file download 
     *
     *  @param input XMLRPC struct
     */
    function xr_downloadOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadOpen($r['sessid'], $r['type'], $r['par']);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_downloadOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(xmlrpc_encoder($res));
    }


    /**
     *  Close general file download
     *
     *  @param input XMLRPC struct
     */
    function xr_downloadClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadClose($r['sessid'], $r['url']);
        if(PEAR::isError($res))
            return new xmlrpcresp(0, 803,
                "xr_downloadClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new xmlrpcresp(xmlrpc_encoder($res));
    }

    /* =============================================== authentication methods */
    /**
     *  Call Archive::login
     *
     *  @param input XMLRPC struct
     */
    function xr_login($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        if(!($res = $this->login($r['login'], $r['pass'])))
            return new xmlrpcresp(0, 802,
                "xr_login: login failed - incorrect username or password ({$r['login']}/{$r['pass']})."
            );
        else
            return new xmlrpcresp($this->_v2xr($res, false));
    }

    /**
     *  Call Archive::logout
     *
     *  @param input XMLRPC struct
     */
    function xr_logout($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->logout($r['sessid']);
        if(!PEAR::isError($res))
            return new xmlrpcresp($this->_v2xr('Bye', false));
        else
            return new xmlrpcresp(0, 803,
                "xr_logout: logout failed - not logged."
            );
    }

    /* ==================================================== auxiliary methods */

    /**
     *  Simple ping method - return strtouppered string
     *
     *  @param input XMLRPC struct
     */
    function xr_ping($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        return new xmlrpcresp(new xmlrpcval(strtoupper($r['par']), "string"));
    }

    /**
     *  Convert PHP variables to XMLRPC objects
     *
     *  @param var mixed - PHP variable
     *  @param struct boolean - flag for using XMLRPC struct instead of array
     *  @return XMLRPC object
     */
    function _v2xr($var, $struct=true){
        if(is_array($var)){
            $r = array();
            foreach($var as $k=>$v){
                if($struct) $r[$k]=$this->_v2xr($v);
                else $r[]=$this->_v2xr($v);
            }
            return new xmlrpcval($r, ($struct ? "struct" : "array"));
        }else if(is_int($var)){
            return new xmlrpcval($var, "int");
        }else if(is_bool($var)){
            return new xmlrpcval($var, "boolean");
        }else{
            return new xmlrpcval($var, "string");
        }
    }

    /**
     *  Convert XMLRPC struct to PHP array
     *
     *  @param input XMLRPC struct
     */
    function _xr_getPars($input)
    {
        $p = $input->getParam(0);
        if(isset($p) && $p->scalartyp()=="struct"){
            $p->structreset();  $r = array();
            while(list($k,$v) = $p->structeach()){ $r[$k] = $v->scalarval(); }
            return array(TRUE, $r);
        }
        else return array(FALSE, new xmlrpcresp(0, 801,
            "xr_login: wrong 1st parameter, struct expected."
        ));
    }
}

$archive = &new XR_Archive(&$dbc, $config);

$methods = array(
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'ping'              =>'Echo request',

    'uploadOpen'      =>'Open file upload',
    'uploadCheck'     =>'Check size and md5 uploaded file',
    'uploadClose'     =>'Close file upload',
    'downloadOpen'    =>'Open file download',
    'downloadClose'   =>'Close file download',
);

$defs = array();
foreach($methods as $method=>$description){
    $defs["archive.$method"] = array(
            "function" => array(&$archive, "xr_$method"),
            "signature" => array(array($xmlrpcStruct, $xmlrpcStruct)),
            "docstring" => $description
    );
}
$s=new xmlrpc_server( $defs );
?>