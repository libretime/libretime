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
    Version  : $Revision: 1.6 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/xrLocStor.php,v $

------------------------------------------------------------------------------*/
#error_reporting(0);
ini_set("error_prepend_string", "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<methodResponse>
<fault>
<value>
<struct>
<member>
<name>faultCode</name>
<value><int>804</int></value>
</member>
<member>
<name>faultString</name>
<value><string>");
ini_set("error_append_string", "</string></value>
</member>
</struct>
</value>
</fault>
</methodResponse>");
header("Content-type: text/xml");

require_once "XML/RPC/Server.php";
require_once '../conf.php';
require_once 'DB.php';
require_once '../LocStor.php';

function errHndl($errno, $errmsg, $filename, $linenum, $vars){
    if($errno == 8 /*E_NOTICE*/) return;
    $xr =& new XML_RPC_Response(0, 805,
        "ERROR:xrLoctor: $errno $errmsg ($filename:$linenum)");
    header("Content-type: text/xml");
    echo $xr->serialize();
    exit($errno);
}
$old_error_handler = set_error_handler("errHndl");

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);


class XR_LocStor extends LocStor{

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
            return new XML_RPC_Value($r, ($struct ? "struct" : "array"));
        }else if(is_int($var)){
            return new XML_RPC_Value($var, "int");
        }else if(is_bool($var)){
            return new XML_RPC_Value($var, "boolean");
        }else{
            return new XML_RPC_Value($var, "string");
        }
    }
    /**
     *  Convert XMLRPC struct to PHP array
     *
     *  @param input XMLRPC struct
     *  @return array
     */
    function _xr_getPars($input)
    {
        $p = $input->getParam(0);
        if(isset($p) && $p->scalartyp()=="struct"){
            $p->structreset();  $r = array();
            while(list($k,$v) = $p->structeach()){ $r[$k] = $v->scalarval(); }
            return array(TRUE, $r);
        }
        else return array(FALSE, new XML_RPC_Response(0, 801,
            "wrong 1st parameter, struct expected."
        ));
    }

    /**
     *  Test XMLRPC - strupper and return given string,
     *  also return loginname of logged user
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     */
    function xr_test($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        return new XML_RPC_Response($this->_v2xr(array(
            'str'=>strtoupper($r['teststring']),
            'login'=>$this->getSessLogin($r['sessid']),
            'sessid'=>$r['sessid']
        ), true));
    }


    /**
     *  Checks the login name and password of the user and return
     *  true if login data are correct, othervise return false.
     *
     *  The XML-RPC name of this method is "locstor.authenticate".
     *
     *  Input parameters: XML-RPC struct with the following fields:
     *  <ul>
     *      <li> login  :  string  -  login name </li>
     *      <li> pass   :  string  -  password </li>
     *  </ul>
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 804  -  xr_authenticate: database error </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see Subjects::authenticate
     */
    function xr_authenticate($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->authenticate($r['login'], $r['pass']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 804,"xr_authenticate: database error");
        }
        $retval = ($res !== FALSE);
        return new XML_RPC_Response(new XML_RPC_Value($retval, "boolean"));
    }

    /**
     *  Checks the login name and password of the user.  If the login is
     *  correct, a new session ID string is generated, to be used in all
     *  subsequent XML-RPC calls as the "sessid" field of the
     *  parameters.
     *
     *  The XML-RPC name of this method is "locstor.login".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> login  :  string  -  login name </li>
     *      <li> pass   :  string  -  password </li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> string  -  the newly generated session ID </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 802  -  xr_login: login failed -
     *                      incorrect username or password. </li>
     *      <li> 804  -  xr_login:: database error </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return string
     *  @see Alib::login
     */
    function xr_login($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->login($r['login'], $r['pass']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 804,"xr_login: database error");
        }
        if($res === FALSE)
            return new XML_RPC_Response(0, 802,
                "xr_login: login failed - incorrect username or password."
            );
        else
            return new XML_RPC_Response($this->_v2xr($res, false));
    }

    /**
     *  Logout, destroy session and return 'Bye'.
     *  If session is not valid error message is returned.
     *
     *  The XML-RPC name of this method is "locstor.logout".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean  -  TRUE </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 803  -  xr_logout: logout failed - not logged. </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return string
     *  @see GreenBox::logout
     */
    function xr_logout($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->logout($r['sessid']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response($this->_v2xr('Bye', false));
        else
            return new XML_RPC_Response(0, 803,
                "xr_logout: logout failed - not logged."
            );
    }

    /**
     *  Check if audio clip exists and return TRUE/FALSE
     *
     *  The XML-RPC name of this method is "locstor.existsAudioClip".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean  </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_existsAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see LocStor::existsAudioClip
     */
    function xr_existsAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        #$this->debugLog(join(', ', $r));
        $res = $this->existsAudioClip($r['sessid'], $r['gunid']);
        #$this->debugLog($res);
        if(PEAR::isError($res))
            return new XML_RPC_Response(0, 805,
                "xr_existsAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(new XML_RPC_Value($res, "boolean"));
    }

    /**
     *  Store new AudioClip or replace existing one.
     *
     *  The XML-RPC name of this method is "locstor.storeAudioClip".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *      <li> mediaFileLP  :  local path of raw audio file</li>
     *      <li> mdataFileLP  :  local path of metadata XML file</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> string - gunid of stored file</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return string
     *  @see LocStor::storeAudioClip
     */
    function xr_storeAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->storeAudioClip(
            $r['sessid'], $r['gunid'], $r['mediaFileLP'], $r['mdataFileLP']
        );
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "string"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_storeAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Delete existing audio clip
     *
     *  The XML-RPC name of this method is "locstor.deleteAudioClip".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean - TRUE</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_deleteAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see LocStor::deleteAudioClip
     */
    function xr_deleteAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->deleteAudioClip($r['sessid'], $r['gunid']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "boolean"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_deleteAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Update existing audio clip metadata
     *
     *  The XML-RPC name of this method is "locstor.updateAudioClipMetadata".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *      <li> mdataFileLP  :  local path of metadata XML file</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean - TRUE</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_updateAudioClipMetadata:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see LocStor::updateAudioClipMetadata
     */
    function xr_updateAudioClipMetadata($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->updateAudioClipMetadata(
            $r['sessid'], $r['gunid'], $r['mdataFileLP']
        );
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "boolean"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_updateAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Search in local metadata database
     *
     *  The XML-RPC name of this method is "locstor.searchMetadata".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> criteria  : search string
     *           - will be searched in object part of RDF tripples
     *           - <b>this parameter may be changed</b> structured
     *              queries will be supported in the future</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean - TRUE</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_searchMetadata:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see LocStor::searchMetadata
     *  @see GreenBox::localSearch
     */
    function xr_searchMetadata($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->searchMetadata($r['sessid'], $r['criteria']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "boolean"));
        else
            return new XML_RPC_Response(0, 803,
                "xr_searchAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Make access to audio clip.
     *
     *  The XML-RPC name of this method is "locstor.accessRawAudioData".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> string - access symlink filename</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessRawAudioData:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return string
     *  @see LocStor::accessRawAudioData
     */
    function xr_accessRawAudioData($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->accessRawAudioData($r['sessid'], $r['gunid']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "string"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_accessRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Release access to audio clip
     *
     *  The XML-RPC name of this method is "locstor.releaseRawAudioData".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> tmpLink  :  string  -  temporary access symlink
     *              returned by locstor.accessRawAudioData</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> boolean</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releaseRawAudioData:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return boolean
     *  @see LocStor::releaseRawAudioData
     */
    function xr_releaseRawAudioData($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->releaseRawAudioData($r['sessid'], $r['tmpLink']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "boolean"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_releaseRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
    }

    /**
     *  Return all file's metadata as XML string
     *
     *  The XML-RPC name of this method is "locstor.getAudioClip".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     *  On success, returns a single XML-RPC value:
     *  <ul>
     *      <li> string - metadata as XML</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return string
     *  @see LocStor::getAudioClip
     */
    function xr_getAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->getAudioClip($r['sessid'], $r['gunid']);
        if(!PEAR::isError($res))
            return new XML_RPC_Response(new XML_RPC_Value($res, "string"));
        else
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
    }
}

$locStor = &new XR_LocStor(&$dbc, $config);

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: '.
                                  'teststring, sessid.',
    'authenticate'            => 'Checks authentication.',
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'existsAudioClip'         => 'Checks if an Audio clip with the specified '.
                                  'id is stored in local storage.',
    'storeAudioClip'          => 'Store a new audio clip or replace '.
                                 'an existing one.',
    'deleteAudioClip'         => 'Delete an existing Audio clip.',
    'updateAudioClipMetadata' => 'Update the metadata of an Audio clip '.
                                  'stored in Local storage.',
    'searchMetadata'          => 'Search through the metadata of stored '.
                                  'AudioClips, return all matching clip ids.',
    'accessRawAudioData'      => 'Get access to raw audio data.',
    'releaseRawAudioData'     => 'Release access to raw audio data.',
    'getAudioClip'            => 'Return the contents of an Audio clip.'
);

$defs = array();
foreach($methods as $method=>$description){
    $defs["locstor.$method"] = array(
#            "function" => array(&$locStor, "xr_$method"),
            "function" => "\$GLOBALS['locStor']->xr_$method",
            "signature" => array(
                array($GLOBALS['XML_RPC_Struct'], $GLOBALS['XML_RPC_Struct'])
            ),
            "docstring" => $description
    );
}
$s=new XML_RPC_Server( $defs );

#$s=new XML_RPC_Server( $defs , 0 );
##var_dump($s);
#$r = $s->parseRequest();
#$r = $r[0];
#var_dump($r);
#echo ($cn = get_class($r))."\n";
#$a = get_class_methods($cn);
#var_dump($a);
#echo $r->serialize();
?>
