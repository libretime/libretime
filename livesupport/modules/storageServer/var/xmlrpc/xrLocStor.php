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
    Version  : $Revision: 1.11 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/xrLocStor.php,v $

------------------------------------------------------------------------------*/

/* ====================================================== specific PHP config */
//error_reporting(0);
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

/* ================================================================= includes */
require_once 'DB.php';
require_once "XML/RPC/Server.php";
require_once '../conf.php';
require_once '../LocStor.php';

/* ============================================ setting default error handler */
function errHndl($errno, $errmsg, $filename, $linenum, $vars){
    if($errno == 8 /*E_NOTICE*/) return;
    $xr =& new XML_RPC_Response(0, 805,
        "ERROR:xrLocStor: $errno $errmsg ($filename:$linenum)");
    header("Content-type: text/xml");
    echo $xr->serialize();
    exit($errno);
}
$old_error_handler = set_error_handler("errHndl");

/* ====================================== XML-RPC interface class for LocStor */
/**
 *  XML-RPC interface for LocStor class 
 *  
 */
class XR_LocStor extends LocStor{

    /* ------------------------------------------------------- authentication */
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> authenticate :  boolean </li>
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
     *  @return XMLRPC struct
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
        return new XML_RPC_Response(
            XML_RPC_encode(array('authenticate'=>$retval))
        );
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> sessid : string  -  the newly generated session ID </li>
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
     *  @return XMLRPC struct
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
            return new XML_RPC_Response(XML_RPC_encode(array('sessid'=>$res)));
    }

    /**
     *  Logout, destroy session and return status.
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean  -  TRUE </li>
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
     *  @return XMLRPC struct
     *  @see GreenBox::logout
     */
    function xr_logout($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->logout($r['sessid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 803,
                "xr_logout: logout failed - not logged."
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* ---------------------------------------------------------------- store */
    /**
     *  Open writable URL for store new AudioClip or replace existing one.
     *  Writing to returned URL is possible using HTTP PUT method
     *  (as e.g. curl -T &lt;filename&gt; command does)
     *
     *  The XML-RPC name of this method is "locstor.storeAudioClipOpen".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *      <li> metadata  :  metadata XML string</li>
     *      <li> chsum :  md5 checksum of media file</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - writable URL for HTTP PUT</li>
     *      <li> token : string - access token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeAudioClipOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::storeAudioClipOpen
     */
    function xr_storeAudioClipOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->storeAudioClipOpen(
            $r['sessid'], $r['gunid'], $r['metadata'], $r['chsum']
        );
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_storeAudioClipOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Close writable URL for store new AudioClip or replace existing one.
     *
     *  The XML-RPC name of this method is "locstor.storeAudioClip".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token  :  string  -  access token</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - gunid of stored file</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeAudioClipClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::storeAudioClipClose
     */
    function xr_storeAudioClipClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->storeAudioClipClose($r['sessid'], $r['token']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_storeAudioClipClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* ------------------------------------------------ access raw audio data */
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
     *  On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - local access url</li>
     *      <li> token : string - access token</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::accessRawAudioData
     */
    function xr_accessRawAudioData($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->accessRawAudioData($r['sessid'], $r['gunid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_accessRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
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
     *      <li> token   :  string  -  access token
     *              returned by locstor.accessRawAudioData</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::releaseRawAudioData
     */
    function xr_releaseRawAudioData($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->releaseRawAudioData($r['sessid'], $r['token']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_releaseRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* ---------------------------------------------- download raw audio data */
    /**
     *  Create downlodable URL for stored file
     *
     *  The XML-RPC name of this method is "locstor.downloadRawAudioDataOpen".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - downloadable url</li>
     *      <li> token : string - download token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessRawAudioDataOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::downloadRawAudioDataOpen
     */
    function xr_downloadRawAudioDataOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadRawAudioDataOpen($r['sessid'], $r['gunid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_downloadRawAudioDataOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Delete downlodable URL with media file.
     *
     *  The XML-RPC name of this method is "locstor.downloadRawAudioDataClose".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  download token
     *              returned by locstor.downloadRawAudioDataOpen</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - global unique ID</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releaseRawAudioDataClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::downloadRawAudioDataClose
     */
    function xr_downloadRawAudioDataClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadRawAudioDataClose($r['token']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_downloadRawAudioDataClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* ---------------------------------------------------- download metadata */
    /**
     *  Create downlodable URL for metadata part of stored file
     *
     *  The XML-RPC name of this method is "locstor.downloadMetadataOpen".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - downloadable url</li>
     *      <li> token : string - download token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadMetadataOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::downloadRawAudioDataOpen
     */
    function xr_downloadMetadataOpen($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadMetadataOpen($r['sessid'], $r['gunid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_downloadMetadataOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Delete downlodable URL with metadata.
     *
     *  The XML-RPC name of this method is "locstor.downloadMetadataClose".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  download token
     *              returned by locstor.downloadRawAudioDataOpen</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - global unique ID</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadMetadataClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::downloadRawAudioDataClose
     */
    function xr_downloadMetadataClose($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->downloadMetadataClose($r['token']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_downloadMetadataClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* --------------------------------------------------------------- delete */
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::deleteAudioClip
     */
    function xr_deleteAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->deleteAudioClip($r['sessid'], $r['gunid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_deleteAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /*====================================================== playlist methods */
    /**
     *  Create a new Playlist metafile.
     *
     *  The XML-RPC name of this method is "locstor.createPlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::createPlaylist
     */
    function xr_createPlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->createPlaylist($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_createPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /**
     *  Open a Playlist metafile for editing.
     *  Open readable URL and mark file as beeing edited.
     *
     *  The XML-RPC name of this method is "locstor.editPlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> url : string - readable url</li>
     *      <li> token : string - playlist token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_editPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::editPlaylist
     */
    function xr_editPlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->editPlaylist($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_editPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Store a new Playlist metafile in place of the old one.
     *
     *  The XML-RPC name of this method is "locstor.savePlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  playlist token
     *              returned by locstor.editPlaylist</li>
     *      <li> newPlaylist  :  string  -  new Playlist in XML string </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_savePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::savePlaylist
     */
    function xr_savePlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->savePlaylist(
            $r['sessid'], $r['token'], $r['newPlaylist']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_savePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     *  Delete a Playlist metafile.
     *
     *  The XML-RPC name of this method is "locstor.deletePlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_deletePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::deletePlaylist
     */
    function xr_deletePlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->deletePlaylist($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_deletePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     *  Access (read) a Playlist metafile.
     *
     *  The XML-RPC name of this method is "locstor.accessPlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> url : string - readable url</li>
     *      <li> token : string - playlist token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::accessPlaylist
     */
    function xr_accessPlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->accessPlaylist($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_accessPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Release the resources obtained earlier by accessPlaylist().
     *
     *  The XML-RPC name of this method is "locstor.releasePlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  playlist token
     *              returned by locstor.accessPlaylist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string - playlist ID</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releasePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::releasePlaylist
     */
    function xr_releasePlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->releasePlaylist($r['sessid'], $r['token']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_releasePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID exists.
     *
     *  The XML-RPC name of this method is "locstor.existsPlaylist".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> exists : boolean</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_existsPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::existsPlaylist
     */
    function xr_existsPlaylist($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->existsPlaylist($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_existsPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('exists'=>$res)));
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID
     *  is available for editing, i.e., exists and is not marked as
     *  beeing edited.
     *
     *  The XML-RPC name of this method is "locstor.playlistIsAvailable".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> available : boolean</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_playlistIsAvailable:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::playlistIsAvailable
     */
    function xr_playlistIsAvailable($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->playlistIsAvailable($r['sessid'], $r['plid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_playlistIsAvailable: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('available'=>$res)));
    }

    /* ----------------------------------------------------------------- etc. */
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> exists : boolean  </li>
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
     *  @return XMLRPC struct
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
        return new XML_RPC_Response(XML_RPC_encode(array('exists'=>$res)));
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
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> metadata : string - metadata as XML</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::getAudioClip
     */
    function xr_getAudioClip($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->getAudioClip($r['sessid'], $r['gunid']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('metadata'=>$res)));
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
     *      <li> metadata  :  metadata XML string</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::updateAudioClipMetadata
     */
    function xr_updateAudioClipMetadata($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->updateAudioClipMetadata(
            $r['sessid'], $r['gunid'], $r['metadata']
        );
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_updateAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
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
     *              queries will be supported in the near future</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> results: array - array of gunids have founded</li>
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
     *  @return XMLRPC struct
     *  @see LocStor::searchMetadata
     *  @see GreenBox::localSearch
     */
    function xr_searchMetadata($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->searchMetadata($r['sessid'], $r['criteria']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 803,
                "xr_searchAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('results'=>$res)));
    }

    /**
     *  Reset storageServer for debugging.
     *
     *  The XML-RPC name of this method is "locstor.resetStorage".
     *
     *  The input parameters are an empty XML-RPC struct.
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunids : array - array with gunids of inserted files </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_resetStorage:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see LocStor::getAudioClip
     */
    function xr_resetStorage($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->resetStorage();
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunids'=>$res)));
    }


    /* ------------------------------------------- test methods for debugging */
    /**
     *  Test XMLRPC - strupper and return given string,
     *  also return loginname of logged user
     *  - debug method only
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     */
    function xr_test($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        return new XML_RPC_Response(XML_RPC_encode(array(
            'str'=>strtoupper($r['teststring']),
            'login'=>$this->getSessLogin($r['sessid']),
            'sessid'=>$r['sessid']
        )));
    }

    /**
     *  Open writable URL for put method - debug method only
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     */
    function xr_openPut($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->bsOpenPut();
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     *  Close writable URL - debug method only
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     */
    function xr_closePut($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->bsClosePut($r['token'], $r['chsum']);
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('fname'=>$res)));
    }

    /* ---------------------------------------------------- "private" methods */
    /**
     *  Check and convert struct of parameters
     *
     *  @param input XMLRPC parameters
     *  @return array
     */
    function _xr_getPars($input)
    {
        $p = $input->getParam(0);
        if(isset($p) && $p->scalartyp()=="struct"){
            $r = XML_RPC_decode($p);
            return array(TRUE, $r);
        }
        else return array(FALSE, new XML_RPC_Response(0, 801,
            "wrong 1st parameter, struct expected."
        ));
    }

}   // end of class definition

/* ============================================================= runable code */
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$locStor = &new XR_LocStor(&$dbc, $config);

$methods = array(
    'test'                    => 'Tests toupper and checks sessid, params: '.
                                  'teststring, sessid.',
    'authenticate'            => 'Checks authentication.',
    'login'                   => 'Login to storage.',
    'logout'                  => 'Logout from storage.',
    'existsAudioClip'         => 'Checks if an Audio clip with the specified '.
                                  'id is stored in local storage.',
    'storeAudioClipOpen'      => 'Open channel for store a new audio clip '.
                                    'or replace an existing one.',
    'storeAudioClipClose'     => 'Close channel for store a new audio clip'.
                                    ' or replace an existing one.',
    'downloadRawAudioDataOpen'=> 'Create and return downloadable URL'.
                                    'for audio file',
    'downloadRawAudioDataClose'=>'Discard downloadable URL for audio file',
    'downloadMetadataOpen'    => 'Create and return downloadable URL'.
                                    'for metadata',
    'downloadMetadataClose'   => 'Discard downloadable URL for metadata',
    'openPut'                 => 'openPut',
    'closePut'                => 'closePut',
    'deleteAudioClip'         => 'Delete an existing Audio clip.',
    'updateAudioClipMetadata' => 'Update the metadata of an Audio clip '.
                                  'stored in Local storage.',
    'searchMetadata'          => 'Search through the metadata of stored '.
                                  'AudioClips, return all matching clip ids.',
    'accessRawAudioData'      => 'Get access to raw audio data.',
    'releaseRawAudioData'     => 'Release access to raw audio data.',
    'getAudioClip'            => 'Return the contents of an Audio clip.',
    'resetStorage'            => 'Reset storageServer for debugging.',
    'createPlaylist'          => 'Create a new Playlist metafile.',
    'editPlaylist'            => 'Open a Playlist metafile for editing.',
    'savePlaylist'            => 'Save a Playlist metafile.',
    'deletePlaylist'          => 'Delete a Playlist metafile.',
    'accessPlaylist'          => 'Open readable URL to a Playlist metafile.',
    'releasePlaylist'         => 'Release readable URL from accessPlaylist.',
    'existsPlaylist'          => 'Check whether a Playlist exists.',
    'playlistIsAvailable'     => 'Check whether a Playlist is available '.
                                    'for editing.',
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

?>
