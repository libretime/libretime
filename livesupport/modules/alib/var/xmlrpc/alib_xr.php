<?php
// $Id: alib_xr.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
include_once"xmlrpc.inc";
include_once"xmlrpcs.inc";
require_once"../example/alib_h.php";

function v2xr($var, $struct=true){
	if(is_array($var)){
		$r = array();
		foreach($var as $k=>$v) if($struct) $r[$k]=v2xr($v); else $r[]=v2xr($v);
		return new xmlrpcval($r, ($struct?"struct":"array"));
	}else if(is_int($var)){
		return new xmlrpcval($var, "int");
	}else{
		return new xmlrpcval($var, "string");
	}
}

class XR_Alib extends alib{
 function xr_test($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $s=$p1->scalarval();
    else return new xmlrpcresp(0, 801, "xr_login: wrong 1st parameter, string expected.");
    $p2=$input->getParam(1);
    if(isset($p2) && $p2->scalartyp()=="string") $sessid=$p2->scalarval();
    else return new xmlrpcresp(0, 801, "xr_login: wrong 2nd parameter, string expected.");
    return new xmlrpcresp(v2xr(strtoupper($s)."_".$this->getSessLogin($sessid)."_".$sessid, false));
 }
 function xr_login($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $login=$p1->scalarval();
    else return new xmlrpcresp(0, 801, "xr_login: wrong 1st parameter, string expected.");
    $p2=$input->getParam(1);
    if(isset($p2) && $p2->scalartyp()=="string") $pass=$p2->scalarval();
    else return new xmlrpcresp(0, 801, "xr_login: wrong 2nd parameter, string expected.");
    if(!($res = $this->login($login, $pass)))
        return new xmlrpcresp(0, 802, "xr_login: login failed - incorrect username or password.");
    else
        return new xmlrpcresp(v2xr($res, false));
 }
 function xr_logout($input){
    $p1=$input->getParam(0);
    if(isset($p1) && $p1->scalartyp()=="string") $sessid=$p1->scalarval();
    else return new xmlrpcresp(0, 801, "xr_login: wrong 2nd parameter, string expected.");
    $res = $this->logout($sessid);
    if(!PEAR::isError($res)) return new xmlrpcresp(v2xr('Bye', false));
    else return new xmlrpcresp(0, 803, "xr_logout: logout failed - not logged.");
 }
 function xr_getDir($input){
    $p1=$input->getParam(0);
    if(isset($p1) && ($p1->scalartyp()=="int") && is_numeric($id=$p1->scalarval()));
    else return new xmlrpcresp(0, 801, "xr_getDir: wrong 1st parameter, int expected.");
    $res = $this->getDir($id, 'name');
    return new xmlrpcresp(v2xr($res, false));
 }
 function xr_getPath($input){
    $p1=$input->getParam(0);
    if(isset($p1) && ($p1->scalartyp()=="int") && is_numeric($id=$p1->scalarval()));
    else return new xmlrpcresp(0, 801, "xr_getPath: wrong 1st parameter, int expected.");
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
		"docstring" => "returns listing of object in path from rootnode to object with given id"
	)
));

#header("Content-type: text/plain");
#print_r($dirlist = getDir());

require_once"../example/alib_f.php";
?>