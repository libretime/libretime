<?php
// $Id: xrLocStor.php,v 1.1 2004/09/12 21:59:27 tomas Exp $
include_once "xmlrpc.inc";
include_once "xmlrpcs.inc";
require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';
require_once '../LocStor.php';

#PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
#PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallBack');
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
#PEAR::setErrorHandling(PEAR_ERROR_PRINT);

function errCallBack($err)
{
    echo "<pre>gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

function v2xr($var, $struct=true){
	if(is_array($var)){
		$r = array();
		foreach($var as $k=>$v) if($struct) $r[$k]=v2xr($v); else $r[]=v2xr($v);
		return new xmlrpcval($r, ($struct ? "struct" : "array"));
	}else if(is_int($var)){
		return new xmlrpcval($var, "int");
	}else if(is_bool($var)){
		return new xmlrpcval($var, "boolean");
	}else{
		return new xmlrpcval($var, "string");
	}
}

class XR_LocStor extends LocStor{
 function _xr_getPars($input)
 {
    $p = $input->getParam(0);
    if(isset($p) && $p->scalartyp()=="struct"){
        $p->structreset();  $r = array();
        while(list($k,$v) = $p->structeach()){   $r[$k] = $v->scalarval(); }
        return array(TRUE, $r);
    }
    else return array(FALSE, new xmlrpcresp(0, 801, "xr_login: wrong 1st parameter, struct expected."));
 }
 function xr_test($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    return new xmlrpcresp(v2xr(array(
        'str'=>strtoupper($r['teststring']),
        'login'=>$this->getSessLogin($r['sessid']),
        'sessid'=>$r['sessid']
    ), true));
 }
 function xr_authenticate($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->authenticate($r['login'], $r['pass']);
    return new xmlrpcresp(new xmlrpcval($res, "boolean"));
 }
 function xr_login($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    if(!($res = $this->login($r['login'], $r['pass'])))
        return new xmlrpcresp(0, 802, "xr_login: login failed - incorrect username or password.");
    else
        return new xmlrpcresp(v2xr($res, false));
 }
 function xr_logout($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->logout($r['sessid']);
    if(!PEAR::isError($res)) return new xmlrpcresp(v2xr('Bye', false));
    else return new xmlrpcresp(0, 803, "xr_logout: logout failed - not logged.");
 }
 function xr_existsAudioClip($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
#    $this->debugLog(join(', ', $r));
    $res = $this->existsAudioClip($r['sessid'], $r['gunid']);
#    $this->debugLog($res);
    if(PEAR::isError($res))
        return new xmlrpcresp(0, 803, "xr_existsAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
    return new xmlrpcresp(new xmlrpcval($res, "boolean"));
 }
 function xr_storeAudioClip($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->storeAudioClip($r['sessid'], $r['gunid'], $r['mediaFileLP'], $r['mdataFileLP']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "string"));
    else return new xmlrpcresp(0, 803, "xr_storeAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_deleteAudioClip($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->deleteAudioClip($r['sessid'], $r['gunid']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    else return new xmlrpcresp(0, 803, "xr_deleteAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_updateAudioClipMetadata($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->updateAudioClipMetadata($r['sessid'], $r['gunid'], $r['mdataFileLP']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    else return new xmlrpcresp(0, 803, "xr_updateAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_searchMetadata($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->searchMetadata($r['sessid'], $r['criteria']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    else return new xmlrpcresp(0, 803, "xr_searchAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_accessRawAudioData($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->accessRawAudioData($r['sessid'], $r['gunid']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "string"));
    else return new xmlrpcresp(0, 803, "xr_accessRawAudioData: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_releaseRawAudioData($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->releaseRawAudioData($r['sessid'], $r['tmpLink']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "boolean"));
    else return new xmlrpcresp(0, 803, "xr_releaseRawAudioData: ".$res->getMessage()." ".$res->getUserInfo());
 }
 function xr_getAudioClip($input)
 {
    list($ok, $r) = $this->_xr_getPars($input);
    if(!$ok) return $r;
    $res = $this->getAudioClip($r['sessid'], $r['gunid'], $r['metaData']);
    if(!PEAR::isError($res)) return new xmlrpcresp(new xmlrpcval($res, "string"));
    else return new xmlrpcresp(0, 803, "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo());
 }
}

$locStor = &new XR_LocStor(&$dbc, $config);

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: teststring, sessid.',
    'authenticate'            => 'Checks authentication.',
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'existsAudioClip'         => 'Checks if an Audio clip with the specified id is stored in local storage.',
    'storeAudioClip'          => 'Store a new audio clip or replace an existing one.',
    'deleteAudioClip'         => 'Delete an existing Audio clip.',
    'updateAudioClipMetadata' => 'Update the metadata of an Audio clip stored in Local storage.',
    'searchMetadata'          => 'Search through the metadata of stored AudioClips, and return all matching clip ids.',
    'accessRawAudioData'      => 'Get access to raw audio data of an AudioClip.',
    'releaseRawAudioData'     => 'Release access for raw audio data.',
    'getAudioClip'            => 'Return the contents of an Audio clip.'
);

$defs = array();
foreach($methods as $method=>$description){
    $defs["locstor.$method"] = array(
    		"function" => array(&$locStor, "xr_$method"),
    		"signature" => array(array($xmlrpcStruct, $xmlrpcStruct)),
    		"docstring" => $description
    );
}
$s=new xmlrpc_server( $defs );
?>