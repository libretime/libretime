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
    Version  : $Revision: 1.13 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/xmlrpc/XR_LocStor.php,v $

------------------------------------------------------------------------------*/

require_once '../../../storageServer/var/LocStor.php';

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
     *      <li> metadata  : string -  metadata XML string</li>
     *      <li> fname :  string - human readable mnemonic file name
     *                      with extension corresponding to filetype</li>
     *      <li> chsum :  string - md5 checksum of media file</li>
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
            $r['sessid'], $r['gunid'], $r['metadata'], $r['fname'], $r['chsum']
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
     *      <li> 850  -  wrong 1st parameter, struct expected.</li>
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
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_TOKEN ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
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
     *      <li> chsum : string - md5 checksum</li>
     *      <li> size : int - file size</li>
     *      <li> filename : string - human readable mnemonic file name</li>
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
     *      <li> 847  -  invalid gunid.</li>
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
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_NOTF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
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
     *      <li> chsum : string - md5 checksum</li>
     *      <li> filename : string - mnemonic filename</li>
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
        #$this->debugLog("{$r['sessid']}, {$r['gunid']}");
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
     *      <li> fname :  string - human readable menmonic file name</li>
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
        $res = $this->createPlaylist($r['sessid'], $r['plid'], $r['fname']);
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
     *      <li> chsum : string - md5 checksum</li>
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
     *      <li> plid : string - playlistId</li>
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
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
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
     *      <li> chsum : string - md5 checksum</li>
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
     *      <li> 847  -  invalid plid.</li>
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
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_NOTF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
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

    /* --------------------------------------------------------- info methods */
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

    /* ----------------------------------------------------- metadata methods */
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
     *      <li> criteria : struct, with following fields:<br>
     *   <ul>
     *     <li>filetype : string - type of searched files,
     *       meaningful values: 'audioclip', 'playlist', 'all'</li>
     *     <li>operator : string - type of conditions join
     *       (any condition matches / all conditions match), 
     *       meaningful values: 'and', 'or', ''
     *       (may be empty or ommited only with less then 2 items in
     *       &quot;conditions&quot; field)
     *     </li>
     *     <li>limit : int - limit for result arrays (0 means unlimited)</li>
     *     <li>offset : int - starting point (0 means without offset)</li>
     *     <li>conditions : array of struct with fields:
     *       <ul>
     *           <li>cat : string - metadata category name</li>
     *           <li>op : string - operator, meaningful values:
     *               'full', 'partial', 'prefix', '=', '&lt;', '&lt;=',
     *               '&gt;', '&gt;='</li>
     *           <li>val : string - search value</li>
     *       </ul>
     *     </li>
     *   </ul>
     *   </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with the following
     *  fields:
     *   <ul>
     *      <li>audioClipResults : array with gunid strings
     *          of audioClips have been found</li>
     *      <li>audioClipCnt : int - number of audioClips matching
     *          the criteria</li>
     *      <li>playlistResults : array with gunid strings
     *          of playlists have been found</li>
     *      <li>playlistCnt : int - number of playlists matching
     *          the criteria</li>
     *   </ul>
     *  (cnt values may be greater than size of arrays - see limit param)
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
     *  @see BasicStor::localSearch
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
#        return new XML_RPC_Response(XML_RPC_encode($res));
        $xv = new XML_RPC_Value;
        $xv->addStruct(array(
            'audioClipCnt'      => XML_RPC_encode($res['audioClipCnt']),
            'playlistCnt'       => XML_RPC_encode($res['playlistCnt']),
            'audioClipResults'  =>
                (count($res['audioClipResults'])==0
                    ? new XML_RPC_Value(array(), 'array')
                    : XML_RPC_encode($res['audioClipResults'])
                ),
            'playlistResults'   =>
                (count($res['playlistResults'])==0
                    ? new XML_RPC_Value(array(), 'array')
                    : XML_RPC_encode($res['playlistResults'])
                ),
        ));
        return new XML_RPC_Response($xv);
    }

    /**
     *  Return values of specified metadata category
     *
     *  The XML-RPC name of this method is "locstor.browseCategory".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> category : string - metadata category name
     *          with or without namespace prefix (dc:title, author) </li>
     *      <li> criteria : hash - see searchMetadata method </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> results : array with values having been found </li>
     *      <li> cnt : integer - number of matching values </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_browseCategory:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::browseCategory
     */
    function xr_browseCategory($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        $res = $this->browseCategory(
            $r['category'], $r['criteria'], $r['sessid']
        );
        if(PEAR::isError($res)){
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
                
            );
        }
        $xv = new XML_RPC_Value;
        $xv->addStruct(array(
            'cnt'      => XML_RPC_encode($res['cnt']),
            'results'  =>
                (count($res['results'])==0
                    ? new XML_RPC_Value(array(), 'array')
                    : XML_RPC_encode($res['results'])
                ),
        ));
        return new XML_RPC_Response($xv);
    }

    /* ---------------------------------------------- methods for preferences */
    /**
     *  Load user preference value
     *
     *  The XML-RPC name of this method is "locstor.loadPref".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_loadPref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 849  -  invalid preference key.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::loadPref
     */
    function xr_loadPref($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->loadPref($r['sessid'], $r['key']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == GBERR_PREF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
                
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('value'=>$res)));
    }

    /**
     *  Save user preference value
     *
     *  The XML-RPC name of this method is "locstor.savePref".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
     *      <li> value : string - preference value </li>
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
     *      <li> 805  -  xr_savePref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::savePref
     */
    function xr_savePref($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->savePref($r['sessid'], $r['key'], $r['value']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     *  Delete user preference record
     *
     *  The XML-RPC name of this method is "locstor.delPref".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
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
     *      <li> 805  -  xr_delPref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 849  -  invalid preference key.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::delPref
     */
    function xr_delPref($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->delPref($r['sessid'], $r['key']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == GBERR_PREF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     *  Read group preference record
     *
     *  The XML-RPC name of this method is "locstor.loadGroupPref".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> group : string - group name </li>
     *      <li> key : string - preference key </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_loadGroupPref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 820  -  invalid group name.</li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 849  -  invalid preference key.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::loadGroupPref
     */
    function xr_loadGroupPref($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->loadGroupPref($r['sessid'], $r['group'], $r['key']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = (
                $ec0 == GBERR_SESS || $ec0 == GBERR_PREF || $ec0==ALIBERR_NOTGR
                ? 800+$ec0 : 805 
            );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
                
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('value'=>$res)));
    }

    /**
     *  Save group preference record
     *
     *  The XML-RPC name of this method is "locstor.saveGroupPref".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> group : string - group name </li>
     *      <li> key : string - preference key </li>
     *      <li> value : string - preference value </li>
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
     *      <li> 805  -  xr_saveGroupPref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 820  -  invalid group name.</li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::saveGroupPref
     */
    function xr_saveGroupPref($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->saveGroupPref($r['sessid'], $r['group'], $r['key'], $r['value']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0==GBERR_SESS || $ec0==ALIBERR_NOTGR ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* -------------------------------------------- remote repository methods */
    /**
     *  Starts upload audioclip to remote archive
     *
     *  The XML-RPC name of this method is "locstor.uploadToArchive".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid : string - global unique id </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_uploadToArchive:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::uploadToArchive
     */
    function xr_uploadToArchive($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Transport.php';
        $tr =& new Transport($this->dbc, $this, $this->config);
        $res = $tr->uploadToArchive($r['gunid'], $r['sessid']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     *  Starts download audioclip from remote archive
     *
     *  The XML-RPC name of this method is "locstor.downloadFromArchive".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid : string - global unique id </li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadFromArchive:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::downloadFromArchive
     */
    function xr_downloadFromArchive($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Transport.php';
        $tr =& new Transport($this->dbc, $this, $this->config);
        $res = $tr->downloadFromArchive($r['gunid'], $r['sessid']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     *  Checking status of transported file
     *
     *  The XML-RPC name of this method is "locstor.getTransportInfo".
     *
     *  The input parameters are an XML-RPC struct with the following
     *  fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     *  On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *     <li>trtype: string - audioclip | playlist</li>
     *     <li>direction: string - up | down</li>
     *     <li>status: boolean - true if file have been
     *              succesfully transported</li>
     *     <li>expectedsize: int - expected size</li>
     *     <li>realsize: int - size of transported  file</li>
     *     <li>expectedsum: string - expected checksum</li>
     *     <li>realsum: string - checksum of transported file</li>
     *  </ul>
     *
     *  On errors, returns an XML-RPC error response.
     *  The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getTransportInfo:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     *  @param input XMLRPC struct
     *  @return XMLRPC struct
     *  @see Pref::getTransportInfo
     */
    function xr_getTransportInfo($input)
    {
        list($ok, $r) = $this->_xr_getPars($input);
        if(!$ok) return $r;
        require_once '../../../storageServer/var/Transport.php';
        $tr =& new Transport($this->dbc, $this, $this->config);
        $res = $tr->getTransportInfo($r['trtok'], $r['sessid']);
        if(PEAR::isError($res)){
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /* ------------------------------------------------ methods for debugging */
    /**
     *  Reset storageServer for debugging.
     *
     *  The XML-RPC name of this method is "locstor.resetStorage".
     *
     *  The input parameters are an empty XML-RPC struct.
     *
     *  On success, returns a XML-RPC struct with following
     *  fields:
     *  <ul>
     *      <li> audioclips : array -
     *              array with gunids of inserted audioclips </li>
     *      <li> playlists : array -
     *              array with gunids of inserted playlists </li>
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
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

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

?>
