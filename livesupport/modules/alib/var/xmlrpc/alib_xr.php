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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/xmlrpc/alib_xr.php,v $

------------------------------------------------------------------------------*/
include_once "xmlrpc.inc";
include_once "xmlrpcs.inc";
require_once "../example/alib_h.php";

function v2xr($var, $struct=true){
	if(is_array($var)){
		$r = array();
		foreach($var as $k=>$v) if($struct) $r[$k]=v2xr($v); else $r[]=v2xr($v);
		return new xmlrpcval($r, ($struct ? "struct":"array"));
	}else if(is_int($var)){
		return new xmlrpcval($var, "int");
	}else{
		return new xmlrpcval($var, "string");
	}
}

/**
 *  XMLRPC interface for Alib class<br>
 *  only for testing now (with example) - LiveSupport uses special interface
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.2 $
 *  @see Subjects
 *  @see GreenBox
 */
class XR_Alib extends Alib{
 function xr_test($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $s=$p1->scalarval();
    else return new xmlrpcresp(0, 801,
        "xr_login: wrong 1st parameter, string expected.");
    $p2=$input->getParam(1);
    if(isset($p2) && $p2->scalartyp()=="string") $sessid=$p2->scalarval();
    else return new xmlrpcresp(0, 801,
        "xr_login: wrong 2nd parameter, string expected.");
    return new xmlrpcresp(
        v2xr(strtoupper($s)."_".$this->getSessLogin($sessid)."_".$sessid, false)
    );
 }
 function xr_login($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $login=$p1->scalarval();
    else return new xmlrpcresp(0, 801,
        "xr_login: wrong 1st parameter, string expected.");
    $p2=$input->getParam(1);
    if(isset($p2) && $p2->scalartyp()=="string") $pass=$p2->scalarval();
    else return new xmlrpcresp(0, 801,
        "xr_login: wrong 2nd parameter, string expected.");
    if(!($res = $this->login($login, $pass)))
        return new xmlrpcresp(0, 802,
            "xr_login: login failed - incorrect username or password.");
    else
        return new xmlrpcresp(v2xr($res, false));
 }
 function xr_logout($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $sessid=$p1->scalarval();
    else return new xmlrpcresp(0, 801,
        "xr_login: wrong 2nd parameter, string expected.");
    $res = $this->logout($sessid);
    if(!PEAR::isError($res)) return new xmlrpcresp(v2xr('Bye', false));
    else return new xmlrpcresp(0, 803,
        "xr_logout: logout failed - not logged.");
 }
 function xr_getDir($input){
    $p1=$input->getParam(0);
    if(isset($p1) && ($p1->scalartyp()=="int") &&
        is_numeric($id=$p1->scalarval()));
    else return new xmlrpcresp(0, 801,
        "xr_getDir: wrong 1st parameter, int expected.");
    $res = $this->getDir($id, 'name');
    return new xmlrpcresp(v2xr($res, false));
 }
 function xr_getPath($input){
    $p1=$input->getParam(0);
    if(isset($p1) && ($p1->scalartyp()=="int") &&
        is_numeric($id=$p1->scalarval()));
    else return new xmlrpcresp(0, 801,
        "xr_getPath: wrong 1st parameter, int expected.");
    $res = $this->getPath($id, 'id, name');
    return new xmlrpcresp(v2xr($res, false));
 }
}

$alib = &new XR_Alib($dbc, $config);

$s=new xmlrpc_server( array(
	"alib.xrTest" => array(
		"function" => array(&$alib, 'xr_test'),
		"signature" => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString)),
		"docstring" => ""
	),
	"alib.login" => array(
		"function" => array(&$alib, 'xr_login'),
		"signature" => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString)),
		"docstring" => ""
	),
	"alib.logout" => array(
		"function" => array(&$alib, 'xr_logout'),
		"signature" => array(array($xmlrpcString, $xmlrpcString)),
		"docstring" => ""
	),
	"alib.getDir" => array(
		"function" => array(&$alib, 'xr_getDir'),
		"signature" => array(array($xmlrpcArray, $xmlrpcInt)),
		"docstring" => "returns directory listing of object with given id"
	),
	"alib.getPath" => array(
		"function" => array(&$alib, 'xr_getPath'),
		"signature" => array(array($xmlrpcArray, $xmlrpcInt)),
		"docstring" =>
		    "returns listing of object in path from rootnode to object with given id"
	)
));

require_once"../example/alib_f.php";
?>