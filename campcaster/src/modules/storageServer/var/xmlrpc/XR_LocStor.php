<?php
require_once(dirname(__FILE__).'/../LocStor.php');

/**
 * XML-RPC interface for LocStor class
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class XR_LocStor extends LocStor {

    /* ----------------------------------------------------------- getVersion */
    /**
     * Dummy method - only returns Campcaster version
     *
     * The XML-RPC name of this method is "locstor.getVersion".
     *
     * Input parameters: XML-RPC struct with no fields.
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> version :  string </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getVersion:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Subjects::getVersion
     */
    public function xr_getVersion($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->getVersion();
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_getVersion: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(
            XML_RPC_encode(array('version'=>$res))
        );
    }


    /* ------------------------------------------------------- authentication */
    /**
     * Checks the login name and password of the user and return
     * true if login data are correct, othervise return false.
     *
     * The XML-RPC name of this method is "locstor.authenticate".
     *
     * Input parameters: XML-RPC struct with the following fields:
     *  <ul>
     *      <li> login  :  string  -  login name </li>
     *      <li> pass   :  string  -  password </li>
     *  </ul>
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> authenticate :  boolean </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 804  -  xr_authenticate: database error </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Subjects::authenticate
     */
    public function xr_authenticate($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $r;
        }
        $res = $this->authenticate($r['login'], $r['pass']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 804,
                "xr_authenticate: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        $retval = ($res !== FALSE);
        return new XML_RPC_Response(
            XML_RPC_encode(array('authenticate'=>$retval))
        );
    }


    /**
     * Checks the login name and password of the user.  If the login is
     * correct, a new session ID string is generated, to be used in all
     * subsequent XML-RPC calls as the "sessid" field of the
     * parameters.
     *
     * The XML-RPC name of this method is "locstor.login".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> login  :  string  -  login name </li>
     *      <li> pass   :  string  -  password </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> sessid : string  -  the newly generated session ID </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 802  -  xr_login: login failed -
     *                      incorrect username or password. </li>
     *      <li> 804  -  xr_login:: database error </li>
     * </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Alib::login
     */
    public function xr_login($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = Alib::Login($r['login'], $r['pass']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 804,
                "xr_login: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        if ($res === FALSE) {
            return new XML_RPC_Response(0, 802,
                "xr_login: login failed - incorrect username or password."
            );
        } else {
            return new XML_RPC_Response(XML_RPC_encode(array('sessid'=>$res)));
        }
    }

    /**
     * Logout, destroy session and return status.
     * If session is not valid error message is returned.
     *
     * The XML-RPC name of this method is "locstor.logout".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     * <ul>
     *      <li> sessid  :  string  -  session id </li>
     * </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     * <ul>
     *      <li> status : boolean  -  TRUE </li>
     * </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     * <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 803  -  xr_logout: logout failed - not logged. </li>
     * </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     */
    public function xr_logout($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = Alib::Logout($r['sessid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 803,
                "xr_logout: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* ---------------------------------------------------------------- store */
    /**
     * Open writable URL for store new AudioClip or replace existing one.
     * Writing to returned URL is possible using HTTP PUT method
     * (as e.g. curl -T &lt;filename&gt; command does)
     *
     * The XML-RPC name of this method is "locstor.storeAudioClipOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     * <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip,
     *          if gunid is empty string, new one is generated
     *          (returned by subsequent storeAudioClipClose call)
     *      </li>
     *      <li> metadata  : string -  metadata XML string
     *          (as defined in Campcaster::Core::AudioClip Class Reference,
     *          examples are in storageServer/var/tests/*.xml)
     *      </li>
     *      <li> fname :  string - human readable mnemonic file name
     *                      with extension corresponding to filetype</li>
     *      <li> chsum :  string - md5 checksum of media file</li>
     * </ul>
     *
     * On success, returns a XML-RPC struct:
     * <ul>
     *      <li> url : string - writable URL for HTTP PUT</li>
     *      <li> token : string - access token</li>
     * </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     * <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeAudioClipOpen:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 888  -  If the file being uploaded is a duplicate of
     *          a file already in the system.</li>
     * </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::storeAudioClipOpen
     */
    public function xr_storeAudioClipOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->storeAudioClipOpen($r['sessid'], $r['gunid'],
            $r['metadata'], $r['fname'], $r['chsum']);
        if (PEAR::isError($res)) {
            $code = 805;
            if ($res->getCode() == 888) {
                $code = 888;
            }
            return new XML_RPC_Response(0, $code,
                "xr_storeAudioClipOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Close writable URL for store new AudioClip or replace existing one.
     *
     * The XML-RPC name of this method is "locstor.storeAudioClipClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     * <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token  :  string  -  access token</li>
     * </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     * <ul>
     *      <li> gunid : string - gunid of stored file</li>
     * </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     * <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeAudioClipClose:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 850  -  wrong 1st parameter, struct expected.</li>
     * </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::storeAudioClipClose
     */
    public function xr_storeAudioClipClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->storeAudioClipClose($r['sessid'], $r['token']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_TOKEN ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_storeAudioClipClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /**
     * Store audio stream identified by URL - no raw audio data
     *
     * The XML-RPC name of this method is "locstor.storeWebstream".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *      <li> metadata  : string -  metadata XML string</li>
     *      <li> fname :  string - human readable mnemonic file name
     *                      with extension corresponding to filetype</li>
     *      <li> url :  string - URL of the webstrea,</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - gunid of stored file</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_storeWebstream:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::storeWebstream
     */
    public function xr_storeWebstream($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->storeWebstream(
            $r['sessid'], $r['gunid'], $r['metadata'], $r['fname'], $r['url']
        );
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_storeWebstream: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* ------------------------------------------------ access raw audio data */
    /**
     * Make access to audio clip.
     *
     * The XML-RPC name of this method is "locstor.accessRawAudioData".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - local access url</li>
     *      <li> token : string - access token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessRawAudioData:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::accessRawAudioData
     */
    public function xr_accessRawAudioData($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->accessRawAudioData($r['sessid'], $r['gunid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_accessRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Release access to audio clip
     *
     * The XML-RPC name of this method is "locstor.releaseRawAudioData".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token   :  string  -  access token
     *              returned by locstor.accessRawAudioData</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releaseRawAudioData:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::releaseRawAudioData
     */
    public function xr_releaseRawAudioData($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->releaseRawAudioData(NULL, $r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_releaseRawAudioData: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* ---------------------------------------------- download raw audio data */
    /**
     * Create downlodable URL for stored file
     *
     * The XML-RPC name of this method is "locstor.downloadRawAudioDataOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - downloadable url</li>
     *      <li> token : string - download token</li>
     *      <li> chsum : string - md5 checksum</li>
     *      <li> size : int - file size</li>
     *      <li> filename : string - human readable mnemonic file name</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessRawAudioDataOpen:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 847  -  invalid gunid.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::downloadRawAudioDataOpen
     */
    public function xr_downloadRawAudioDataOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->downloadRawAudioDataOpen($r['sessid'], $r['gunid']);
        if (PEAR::isError($res)) {
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
     * Delete downlodable URL with media file.
     *
     * The XML-RPC name of this method is "locstor.downloadRawAudioDataClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  download token
     *              returned by locstor.downloadRawAudioDataOpen</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - global unique ID</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releaseRawAudioDataClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::downloadRawAudioDataClose
     */
    public function xr_downloadRawAudioDataClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->downloadRawAudioDataClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_downloadRawAudioDataClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* ---------------------------------------------------- download metadata */
    /**
     * Create downlodable URL for metadata part of stored file
     *
     * The XML-RPC name of this method is "locstor.downloadMetadataOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioClip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct:
     *  <ul>
     *      <li> url : string - downloadable url</li>
     *      <li> token : string - download token</li>
     *      <li> chsum : string - md5 checksum</li>
     *      <li> filename : string - mnemonic filename</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadMetadataOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::downloadRawAudioDataOpen
     */
    public function xr_downloadMetadataOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        #$this->debugLog("{$r['sessid']}, {$r['gunid']}");
        $res = $this->downloadMetadataOpen($r['sessid'], $r['gunid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_downloadMetadataOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Delete downlodable URL with metadata.
     *
     * The XML-RPC name of this method is "locstor.downloadMetadataClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  download token
     *              returned by locstor.downloadRawAudioDataOpen</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> gunid : string - global unique ID</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadMetadataClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::downloadRawAudioDataClose
     */
    public function xr_downloadMetadataClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->downloadMetadataClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_downloadMetadataClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('gunid'=>$res)));
    }

    /* --------------------------------------------------------------- delete */
    /**
     * Delete existing audio clip - DISABLED now!
     *
     * The XML-RPC name of this method is "locstor.deleteAudioClip".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_deleteAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::deleteAudioClip
     */
    public function xr_deleteAudioClip($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        if (!isset($r['forced'])) {
            $r['forced']=FALSE;
        }
        $res = $this->deleteAudioClip($r['sessid'], $r['gunid'], $r['forced']);
        if (!$r['forced']) {
            return new XML_RPC_Response(0, 805, "xr_deleteAudioClip: method disabled");
        }
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_deleteAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /*====================================================== playlist methods */
    /**
     * Create a new Playlist metafile.
     *
     * The XML-RPC name of this method is "locstor.createPlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *      <li> fname :  string - human readable menmonic file name</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::createPlaylist
     */
    public function xr_createPlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->createPlaylist($r['sessid'], $r['plid'], $r['fname']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_createPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /**
     * Open a Playlist metafile for editing.
     * Open readable URL and mark file as beeing edited.
     *
     * The XML-RPC name of this method is "locstor.editPlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> url : string - readable url</li>
     *      <li> token : string - playlist token</li>
     *      <li> chsum : string - md5 checksum</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_editPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::editPlaylist
     */
    public function xr_editPlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->editPlaylist($r['sessid'], $r['plid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_editPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Store a new Playlist metafile in place of the old one.
     *
     * The XML-RPC name of this method is "locstor.savePlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  playlist token
     *              returned by locstor.editPlaylist</li>
     *      <li> newPlaylist  :  string  -  new Playlist in XML string </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string - playlistId</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_savePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::savePlaylist
     */
    public function xr_savePlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->savePlaylist($r['sessid'], $r['token'], $r['newPlaylist']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_savePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /**
     * RollBack playlist changes to the locked state
     *
     * The XML-RPC name of this method is "locstor.revertEditedPlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> token   :  string  -  playlist token
     *              returned by locstor.editPlaylist</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string - playlistId</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_revertEditedPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::revertEditedPlaylist
     */
    public function xr_revertEditedPlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->revertEditedPlaylist($r['token'], $r['sessid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_revertEditedPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /* ------------------------------------------------------- delete playlist*/
    /**
     * Delete a Playlist metafile - DISABLED now!
     *
     * The XML-RPC name of this method is "locstor.deletePlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_deletePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::deletePlaylist
     */
    public function xr_deletePlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        if (!isset($r['forced'])) {
            $r['forced']=FALSE;
        }
        $res = $this->deletePlaylist($r['sessid'], $r['plid'], $r['forced']);
        if (! $r['forced']) {
            return new XML_RPC_Response(0, 805,"xr_deletePlaylist: method disabled");
        }
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_FILENEX ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_deletePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* ------------------------------------------------------- access playlist*/
    /**
     * Access (read) a Playlist metafile.
     *
     * The XML-RPC name of this method is "locstor.accessPlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *      <li> recursive : boolean - flag for recursive access content
     *                  inside playlist (default: false)</li>
     *  </ul>
     *
     * On success, returns an XML-RPC struct with the following fields:
     *  <ul>
     *      <li> url : string - readable url of accessed playlist in
     *          XML format</li>
     *      <li> token : string - playlist token</li>
     *      <li> chsum : string - md5 checksum</li>
     *      <li> content: array of structs - recursive access (optional)</li>
     *  </ul>
     *
     * The <code>content</code> field contains a struct for each playlist
     * element contained in the playlist.  For audio clips, this struct is
     * of type <code>{url, token}</code>; for sub-playlists, it is of type
     *  <code>{url, token, chsum, content}</code>.
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_accessPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 847  -  invalid plid.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::accessPlaylist
     */
    public function xr_accessPlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        if (!isset($r['recursive']) || is_null($r['recursive'])) {
            $r['recursive']=FALSE;
        }
        $res = $this->accessPlaylist($r['sessid'], $r['plid'], (boolean)$r['recursive']);
        if (PEAR::isError($res)) {
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
     * Release the resources obtained earlier by accessPlaylist().
     *
     * The XML-RPC name of this method is "locstor.releasePlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token   :  string  -  playlist token
     *              returned by locstor.accessPlaylist</li>
     *      <li> recursive : boolean - flag for recursive release content
     *              accessed by recursive accessPlaylist
     *              (ignored now - true forced)</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> plid : string - playlist ID</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_releasePlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::releasePlaylist
     */
    public function xr_releasePlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        //if (!isset($r['recursive']) || is_null($r['recursive'])) $r['recursive']=FALSE;
        $res = $this->releasePlaylist(NULL, $r['token'], TRUE);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_releasePlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('plid'=>$res)));
    }

    /* -------------------------------------------------------- playlist info */
    /**
     * Check whether a Playlist metafile with the given playlist ID exists.
     *
     * The XML-RPC name of this method is "locstor.existsPlaylist".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> exists : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_existsPlaylist:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::existsPlaylist
     */
    public function xr_existsPlaylist($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->existsPlaylist($r['sessid'], $r['plid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_existsPlaylist: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('exists'=>$res)));
    }

    /**
     * Check whether a Playlist metafile with the given playlist ID
     * is available for editing, i.e., exists and is not marked as
     * beeing edited.
     *
     * The XML-RPC name of this method is "locstor.playlistIsAvailable".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  global unique id of Playlist</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> available : boolean</li>
     *      <li> ownerid : int - local user id</li>
     *      <li> ownerlogin : string - local username</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_playlistIsAvailable:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::playlistIsAvailable
     */
    public function xr_playlistIsAvailable($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->playlistIsAvailable($r['sessid'], $r['plid'], TRUE);
        $ownerId = ($res === TRUE ? NULL : $res);
        $ownerLogin = (is_null($ownerId) ? NULL : Subjects::GetSubjName($ownerId));
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_playlistIsAvailable: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'available' => ($res === TRUE),
            'ownerid'   => $ownerId,
            'ownerlogin'   => $ownerLogin,
        )));
    }

    /* ------------------------------------------------------ export playlist */
    /**
     * Create a tarfile with playlist export - playlist and all matching
     * sub-playlists and media files (if desired)
     *
     * The XML-RPC name of this method is "locstor.exportPlaylistOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plids : array of strings  -  global unique IDs of Playlists</li>
     *      <li> type  :  string  -  playlist format, values: lspl | smil </li>
     *      <li> standalone  :  boolean  - if only playlist should be exported or
     *          with all related files  </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> url : string - readable url</li>
     *      <li> token : string - access token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_exportPlaylistOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::exportPlaylistOpen
     */
    public function xr_exportPlaylistOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        if (!isset($r['standalone']) || empty($r['standalone'])) {
            $r['standalone']=FALSE;
        }
        $res = $this->exportPlaylistOpen(
            $r['sessid'], $r['plids'], $r['type'], $r['standalone']
        );
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_exportPlaylistOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'url'   => $res['url'],
            'token'   => $res['token'],
        )));
    }

    /**
     * Close playlist export previously opened by the exportPlaylistOpen method
     *
     * The XML-RPC name of this method is "locstor.exportPlaylistClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  access token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - status/li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_exportPlaylistClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::exportPlaylistClose
     */
    public function xr_exportPlaylistClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->exportPlaylistClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_exportPlaylistClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>TRUE)));
    }

    /* ------------------------------------------------------ import playlist */
    /**
     * Open writable URL for import playlist in LS Archive format
     *
     * The XML-RPC name of this method is "locstor.importPlaylistOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> chsum : string  -  md5 checksum of imported file</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> url : string - writable url</li>
     *      <li> token : string - PUT token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_importPlaylistOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::importPlaylistOpen
     */
    public function xr_importPlaylistOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->importPlaylistOpen($r['sessid'], $r['chsum']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_importPlaylistOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'url'=>$res['url'],
            'token'=>$res['token'],
        )));
    }

    /**
     * Open writable URL for import playlist in LS Archive format
     *
     * The XML-RPC name of this method is "locstor.importPlaylistClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  access token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> gunid : string - global id</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_importPlaylistClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::importPlaylistClose
     */
    public function xr_importPlaylistClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->importPlaylistClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_importPlaylistClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'gunid'=>$res,
        )));
    }

    /* ---------------------------------------------- render playlist to file */
    /**
     * Render playlist to ogg file (open handle)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToFileOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  playlist gunid </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> token : string - render token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToFileOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToFileOpen
     */
    public function xr_renderPlaylistToFileOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToFileOpen($r['sessid'], $r['plid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToFileOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'token'=>$res['token'],
        )));
    }

    /**
     * Render playlist to ogg file (check results)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToFileCheck".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  render token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> url : string - readable url</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToFileCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToFileCheck
     */
    public function xr_renderPlaylistToFileCheck($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToFileCheck($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToFileCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'url'=>$res['url'],
            'status'=>$res['status'],
        )));
    }

    /**
     * Render playlist to ogg file (close handle)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToFileClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  render token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToFileClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToFileClose
     */
    public function xr_renderPlaylistToFileClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToFileClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToFileClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'status'=>$res['status'],
        )));
    }

    /* ------------------------------------------- render playlist to storage */
    /**
     * Render playlist to storage media clip (open handle)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToStorageOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  playlist gunid </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> token : string - render token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToStorageOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToStorageOpen
     */
    public function xr_renderPlaylistToStorageOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToStorageOpen($r['sessid'], $r['plid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToStorageOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'token'=>$res['token'],
        )));
    }

    /**
     * Render playlist to storage media clip (check results)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToStorageCheck".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  render token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> gunid : string - gunid of result file</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToStorageCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToStorageCheck
     */
    public function xr_renderPlaylistToStorageCheck($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToStorageCheck($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToStorageCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'status'=>$res['status'],
            'gunid'=>$res['gunid'],
        )));
    }

    /* ----------------------------------------------- render playlist to RSS */
    /**
     * Render playlist to RSS file (open handle)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToRSSOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> plid : string  -  playlist gunid </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> token : string - render token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToRSSOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToRSSOpen
     */
    public function xr_renderPlaylistToRSSOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToRSSOpen($r['sessid'], $r['plid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToRSSOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'token'=>$res['token'],
        )));
    }

    /**
     * Render playlist to RSS file (check results)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToRSSCheck".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  render token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> url : string - readable url</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToRSSCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToRSSCheck
     */
    public function xr_renderPlaylistToRSSCheck($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToRSSCheck($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToRSSCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'url'=>$res['url'],
            'status'=>$res['status'],
        )));
    }

    /**
     * Render playlist to RSS file (close handle)
     *
     * The XML-RPC name of this method is "locstor.renderPlaylistToRSSClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  render token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_renderPlaylistToRSSClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::renderPlaylistToRSSClose
     */
    public function xr_renderPlaylistToRSSClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->renderPlaylistToRSSClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_renderPlaylistToRSSClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'status'=>$res['status'],
        )));
    }

    /*==================================================storage admin methods */
    /* ------------------------------------------------------- backup methods */
    /**
     * Create backup of storage (open handle)
     *
     * The XML-RPC name of this method is "locstor.createBackupOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> criteria : struct - see search criteria </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> token : string - backup token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createBackupOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::createBackupOpen
     */
    public function xr_createBackupOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }

#        return new XML_RPC_Response(XML_RPC_encode(var_export($this, TRUE)));

        $res = $this->createBackupOpen($r['sessid'], $r['criteria']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_createBackupOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'token'=>$res['token'],
        )));
    }

    /**
     * Create backup of storage (check results)
     *
     * The XML-RPC name of this method is "locstor.createBackupCheck".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  backup token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> url : string - readable url</li>
     *      <li> metafile : string - archive metafile in XML format</li>
     *      <li> faultString : string - error message
     *                  (use only if status==fault) </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createBackupCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::createBackupCheck
     */
     //      <li> 854  -  backup process fault</li>
    public function xr_createBackupCheck($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->createBackupCheck($r['token']);

        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_BGERR ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_createBackupCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Create backup of storage (list results)
     *
     * The XML-RPC name of this method is "locstor.createBackupList".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> stat  :  string  -  backup status </li>
     *  </ul>
     *
     * On success, returns a XML-RPC array of struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> url : string - readable url</li>
     *      <li> metafile : string - archive metafile in XML format</li>
     *      <li> faultString : string - error message
     *                  (use only if status==fault) </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createBackupCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::createBackupCheck
     */
     //      <li> 854  -  backup process fault</li>
    public function xr_createBackupList($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        if (!isset($r['stat']) || is_null($r['stat'])) {
            $r['stat']='';
        }
        $res = $this->createBackupList($r['stat']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_BGERR ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_createBackupCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Create backup of storage (close handle)
     *
     * The XML-RPC name of this method is "locstor.createBackupClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  backup token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_createBackupClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::createBackupClose
     */
    public function xr_createBackupClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->createBackupClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_createBackupClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'status'=>$res['status'],
        )));
    }
    /* ------------------------------------------------------ restore methods */
    /**
     * Open restore a backup file
     *
     * The XML-RPC name of this method is "locstor.restoreBackupOpen".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  : string  -  session id </li>
     *      <li> chsum :  string - md5 checksum of restore file</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> url : string - writable URL for HTTP PUT</li>
     *      <li> token : string - PUT token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_restoreBackupOpen:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::restoreBackupOpen
     */
    public function xr_restoreBackupOpen($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->restoreBackupOpen($r['sessid'], $r['chsum']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_restoreBackupOpen: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        unset($res['fname']);
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Close writable URL for restore a backup file and start the restore
     * process
     *
     * The XML-RPC name of this method is "locstor.restoreBackupClosePut".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  : string  -  session id </li>
     *      <li> token  :  string  -  PUT token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> token : string - restore token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_restoreBackupClosePut:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::restoreBackupClosePut
     */
    public function xr_restoreBackupClosePut($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->restoreBackupClosePut($r['sessid'], $r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_restoreBackupClosePut: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Check the state of restore procedure
     *
     * The XML-RPC name of this method is "locstor.restoreBackupCheck".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  restore token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - success | working | fault</li>
     *      <li> faultString: string - description of fault</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_restoreBackupCheck:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::restoreBackupCheck
     */
    public function xr_restoreBackupCheck($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->restoreBackupCheck($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_restoreBackupCheck: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
#        return new XML_RPC_Response(XML_RPC_encode(array(
#            'status'=>$res,
#        )));
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Close the restore process
     *
     * The XML-RPC name of this method is "locstor.restoreBackupClose".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> token  :  string  -  restore token </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with following fields:
     *  <ul>
     *      <li> status : string - status</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_restoreBackupClose:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::restoreBackupClose
     */
    public function xr_restoreBackupClose($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->restoreBackupClose($r['token']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_restoreBackupClose: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
#        return new XML_RPC_Response(XML_RPC_encode(array(
#            'gunid'=>$res,
#        )));
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /*========================================================== info methods */
    /**
     * Check if audio clip exists and return TRUE/FALSE
     *
     * The XML-RPC name of this method is "locstor.existsAudioClip".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> exists : boolean  </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_existsAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::existsAudioClip
     */
    public function xr_existsAudioClip($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        #$this->debugLog(join(', ', $r));
        $res = $this->existsAudioClip($r['sessid'], $r['gunid']);
        #$this->debugLog($res);
        if (PEAR::isError($res))
            return new XML_RPC_Response(0, 805,
                "xr_existsAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        return new XML_RPC_Response(XML_RPC_encode(array('exists'=>$res)));
    }

    /*====================================================== metadata methods */
    /**
     * Return all file's metadata as XML string
     *
     * The XML-RPC name of this method is "locstor.getAudioClip".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> metadata : string - metadata as XML</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getAudioClip:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::getAudioClip
     */
    public function xr_getAudioClip($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->getAudioClip($r['sessid'], $r['gunid']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('metadata'=>$res)));
    }

    /**
     * Update existing audio clip metadata
     *
     * The XML-RPC name of this method is "locstor.updateAudioClipMetadata".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid  :  string  -  global unique id of AudioCLip</li>
     *      <li> metadata  :  metadata XML string</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean - TRUE</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_updateAudioClipMetadata:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::updateAudioClipMetadata
     */
    public function xr_updateAudioClipMetadata($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->updateAudioClipMetadata(
            $r['sessid'], $r['gunid'], $r['metadata']
        );
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_updateAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     * Search in local metadata database
     *
     * The XML-RPC name of this method is "locstor.searchMetadata".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> criteria : struct, with following fields:<br>
     *   <ul>
     *     <li>filetype : string - type of searched files,
     *       meaningful values: 'audioclip', 'webstream', 'playlist', 'all'</li>
     *     <li>operator : string - type of conditions join
     *       (any condition matches / all conditions match),
     *       meaningful values: 'and', 'or', ''
     *       (may be empty or ommited only with less then 2 items in
     *       &quot;conditions&quot; field)
     *     </li>
     *     <li>limit : int - limit for result arrays (0 means unlimited)</li>
     *     <li>offset : int - starting point (0 means without offset)</li>
     *     <li>orderby : string - metadata category for sorting (optional)
     *          or array of strings for multicolumn orderby
     *          [default: dc:creator, dc:source, dc:title]
     *     </li>
     *     <li>desc : boolean - flag for descending order (optional)
     *          or array of boolean for multicolumn orderby
     *          (it corresponds to elements of orderby field)
     *          [default: all ascending]
     *     </li>
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
     * On success, returns a XML-RPC array of structs with fields:
     *   <ul>
     *       <li>cnt : integer - number of matching gunids
     *              of files have been found</li>
     *       <li>results : array of hashes:
     *          <ul>
     *           <li>gunid: string</li>
     *           <li>type: string - audioclip | playlist | webstream</li>
     *           <li>title: string - dc:title from metadata</li>
     *           <li>creator: string - dc:creator from metadata</li>
     *           <li>source: string - dc:source from metadata</li>
     *           <li>length: string - dcterms:extent in extent format</li>
     *          </ul>
     *      </li>
     *   </ul>
     * (cnt value may be greater than size of result array - see limit param)
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_searchMetadata:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::searchMetadata
     * @see BasicStor::localSearch
     */
    public function xr_searchMetadata($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->searchMetadata($r['sessid'], $r['criteria']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_searchAudioClip: ".$res->getMessage().
                " ".$res->getUserInfo()
            );
        }
#        return new XML_RPC_Response(XML_RPC_encode($res));
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

    /**
     * Return values of specified metadata category
     *
     * The XML-RPC name of this method is "locstor.browseCategory".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> category : string - metadata category name
     *          with or without namespace prefix (dc:title, author) </li>
     *      <li> criteria : hash - see searchMetadata method </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> results : array with found values </li>
     *      <li> cnt : integer - number of matching values </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_browseCategory:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::browseCategory
     */
    public function xr_browseCategory($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->browseCategory(
            $r['category'], $r['criteria'], $r['sessid']
        );
        if (PEAR::isError($res)) {
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

    /* ============================================== methods for preferences */
    /**
     * Load user preference value
     *
     * The XML-RPC name of this method is "locstor.loadPref".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
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
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Pref::loadPref
     */
    public function xr_loadPref($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Prefs.php');
        $pr = new Prefs($this);
        $res = $pr->loadPref($r['sessid'], $r['key']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == GBERR_PREF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()

            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('value'=>$res)));
    }

    /**
     * Save user preference value
     *
     * The XML-RPC name of this method is "locstor.savePref".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_savePref:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Pref::savePref
     */
    public function xr_savePref($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Prefs.php');
        $pr = new Prefs($this);
        $res = $pr->savePref($r['sessid'], $r['key'], $r['value']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     * Delete user preference record
     *
     * The XML-RPC name of this method is "locstor.delPref".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> key : string - preference key </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
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
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Pref::delPref
     */
    public function xr_delPref($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Prefs.php');
        $pr = new Prefs($this);
        $res = $pr->delPref($r['sessid'], $r['key']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == GBERR_PREF ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /**
     * Read group preference record
     *
     * The XML-RPC name of this method is "locstor.loadGroupPref".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> group : string - group name </li>
     *      <li> key : string - preference key </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
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
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Pref::loadGroupPref
     */
    public function xr_loadGroupPref($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Prefs.php');
        $pr = new Prefs($this);
        $res = $pr->loadGroupPref($r['sessid'], $r['group'], $r['key']);
        if (PEAR::isError($res)) {
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
     * Save group preference record
     *
     * The XML-RPC name of this method is "locstor.saveGroupPref".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> group : string - group name </li>
     *      <li> key : string - preference key </li>
     *      <li> value : string - preference value </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> status : boolean</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
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
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Pref::saveGroupPref
     */
    public function xr_saveGroupPref($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Prefs.php');
        $pr = new Prefs($this);
        $res = $pr->saveGroupPref($r['sessid'], $r['group'], $r['key'], $r['value']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0==GBERR_SESS || $ec0==ALIBERR_NOTGR ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('status'=>$res)));
    }

    /* =============================== remote repository (networking) methods */
    /* ------------------------------------------------------- common methods */
    /**
     * Common "check" method for transports
     *
     * The XML-RPC name of this method is "locstor.getTransportInfo".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *     <li>trtype: string - audioclip | playlist | search | file</li>
     *     <li>direction: string - up | down</li>
     *     <li>state: string - transport state</li>
     *     <li>expectedsize: int - expected size</li>
     *     <li>realsize: int - size of transported  file</li>
     *     <li>expectedchsum: string - expected checksum</li>
     *     <li>realchsum: string - checksum of transported file</li>
     *     <li>title: string - file title</li>
     *     <li>errmsg: string - error message from failed transports</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
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
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::getTransportInfo
     */
    public function xr_getTransportInfo($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->getTransportInfo($r['trtok']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getTransportInfo: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Turn transports on/off, optionaly return current state.
     *
     * The XML-RPC name of this method is "locstor.turnOnOffTransports".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> onOff: boolean optional
     *              (if not used, current state is returned)</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> state : boolean - previous state</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_turnOnOffTransports:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::turnOnOffTransports
     */
    public function xr_turnOnOffTransports($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->turnOnOffTransports($r['onOff']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_turnOnOffTransports: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('state'=>$res)));
    }

    /**
     * Pause, resume or cancel transport
     *
     * The XML-RPC name of this method is "locstor.doTransportAction".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> trtok : string - transport token</li>
     *      <li> action: string - pause | resume | cancel
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> state : string - resulting transport state</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_doTransportAction:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::doTransportAction
     */
    public function xr_doTransportAction($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->doTransportAction($r['trtok'], $r['action']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_doTransportAction: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('state'=>$res)));
    }

    /* ------------------------ methods for ls-archive-format file transports */
    /**
     * Open async file transfer from local storageServer to network hub,
     * file should be ls-archive-format file.
     *
     * The XML-RPC name of this method is "locstor.uploadFile2Hub".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> filePath string - local path to uploaded file</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_uploadFile2Hub:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::uploadFile2Hub
     */
    public function xr_uploadFile2Hub($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->uploadFile2Hub($r['filePath']); // local files on XML-RPC :(
                        // there should be something as uploadFile2storageServer
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_uploadFile2Hub: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     * Get list of prepared transfers initiated by hub
     *
     * The XML-RPC name of this method is "locstor.getHubInitiatedTransfers".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> results : array of structs with fields:
     *          <ul>
     *              <li> trtok : string - transport token</li>
     *              <li> ... ? </li>
     *          </ul>
     *      </li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getHubInitiatedTransfers:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::getHubInitiatedTransfers
     */
    public function xr_getHubInitiatedTransfers($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->getHubInitiatedTransfers();
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getHubInitiatedTransfers: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Start of download initiated by hub
     *
     * The XML-RPC name of this method is "locstor.startHubInitiatedTransfer".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> trtok : string - transport token obtained from
     *          the getHubInitiatedTransfers method</li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_startHubInitiatedTransfer:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::startHubInitiatedTransfer
     */
    public function xr_startHubInitiatedTransfer($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->startHubInitiatedTransfer($r['trtok']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_startHubInitiatedTransfer: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /* ------------- special methods for audioClip/webstream object transport */

    /**
     * Start upload of audioclip or playlist from local storageServer to hub
     *
     * The XML-RPC name of this method is "locstor.upload2Hub".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid: string - global unique id of object being transported
     *      </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_upload2Hub:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::upload2Hub
     */
    public function xr_upload2Hub($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->upload2Hub($r['gunid']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_upload2Hub: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     * Start download of audioclip or playlist from hub to local storageServer
     *
     * The XML-RPC name of this method is "locstor.downloadFromHub".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid: string - global unique id of object being transported
     *      </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadFromHub:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::downloadFromHub
     */
    public function xr_downloadFromHub($input)
    {
        list($ok, $par) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $par;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $uid = Alib::GetSessUserId($par['sessid']);
        $res = $tr->downloadFromHub($uid, $par['gunid']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_downloadFromHub: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /* ------------------------------------------------ global-search methods */
    /**
     * Start search job on network hub
     *
     * The XML-RPC name of this method is "locstor.globalSearch".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> criteria : hash, LS criteria format - see searchMetadata method
     *      </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with the following fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_globalSearch:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 874  -  invalid hub connection configuration.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::globalSearch
     */
    public function xr_globalSearch($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->globalSearch($r['criteria']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec = ($ec0 == GBERR_SESS || $ec0 == TRERR_TOK ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_globalSearch: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     * Get results from search job on network hub.
     * (returns error if not finished)
     *
     * The XML-RPC name of this method is "locstor.getSearchResults".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On success, returns a XML-RPC array of structs with fields:
     *   <ul>
     *       <li>cnt : integer - number of matching gunids
     *              of files have been found</li>
     *       <li>results : array of hashes:
     *          <ul>
     *           <li>gunid: string</li>
     *           <li>type: string - audioclip | playlist | webstream</li>
     *           <li>title: string - dc:title from metadata</li>
     *           <li>creator: string - dc:creator from metadata</li>
     *           <li>source: string - dc:source from metadata</li>
     *           <li>length: string - dcterms:extent in extent format</li>
     *          </ul>
     *      </li>
     *   </ul>
     * (cnt value may be greater than size of result array - see limit param)
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_getSearchResults:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *      <li> 872  -  invalid tranport token.</li>
     *      <li> 873  -  not finished.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::getSearchResults
     */
    public function xr_getSearchResults($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $r;
        }
        require_once('../Transport.php');
        $tr = new Transport($this);
        $res = $tr->getSearchResults($r['trtok']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec = (
                $ec0 == GBERR_SESS || $ec0 == TRERR_TOK || $ec0 == TRERR_NOTFIN
                 ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getSearchResults: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * OBSOLETE
     * Starts upload audioclip to remote archive
     *
     * The XML-RPC name of this method is "locstor.uploadToArchive".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid : string - global unique id </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_uploadToArchive:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::uploadToArchive
     */
    public function xr_uploadToArchive($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
        	return $r;
        }
        require_once(dirname(__FILE__).'/../Transport.php');
        $tr = new Transport($this);
        $res = $tr->uploadToArchive($r['gunid'], $r['sessid']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /**
     * OBSOLETE
     * Starts download audioclip from remote archive
     *
     * The XML-RPC name of this method is "locstor.downloadFromArchive".
     *
     * The input parameters are an XML-RPC struct with the following
     * fields:
     *  <ul>
     *      <li> sessid  :  string  -  session id </li>
     *      <li> gunid : string - global unique id </li>
     *  </ul>
     *
     * On success, returns a XML-RPC struct with single field:
     *  <ul>
     *      <li> trtok : string - transport token</li>
     *  </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_downloadFromArchive:
     *                      &lt;message from lower layer&gt; </li>
     *      <li> 848  -  invalid session id.</li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see Transport::downloadFromArchive
     */
    public function xr_downloadFromArchive($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        require_once(dirname(__FILE__).'/../Transport.php');
        $tr = new Transport($this);
        $res = $tr->downloadFromArchive($r['gunid'], $r['sessid']);
        if (PEAR::isError($res)) {
            $ec0 = intval($res->getCode());
            $ec  = ($ec0 == GBERR_SESS ? 800+$ec0 : 805 );
            return new XML_RPC_Response(0, $ec,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array('trtok'=>$res)));
    }

    /* ================================================ methods for debugging */
    /**
     * Reset storageServer for debugging.
     *
     * The XML-RPC name of this method is "locstor.resetStorage".
     *
     * The input parameters are an empty XML-RPC struct,
     * or struct with the following <b>optional</b> fields:
     *  <ul>
     *      <li> loadSampleData : boolean - load sample data? (default: true)
     *      </li>
     *      <li> invalidateSessionIds : boolean - invalidate active session IDs?
     *                                            (default: false)
     *      </li>
     *  </ul>
     *
     * On success, returns the same result as searchMetadata with filetype
     *  'all' and no conditions, ordered by filetype and dc:title
     * i.e. XML-RPC array of structs with fields:
     *   <ul>
     *       <li>cnt : integer - number of inserted files</li>
     *       <li>results : array of hashes:
     *          <ul>
     *           <li>gunid: string</li>
     *           <li>type: string - audioclip | playlist | webstream</li>
     *           <li>title: string - dc:title from metadata</li>
     *           <li>creator: string - dc:creator from metadata</li>
     *           <li>source: string - dc:source from metadata</li>
     *           <li>length: string - dcterms:extent in extent format</li>
     *          </ul>
     *      </li>
     *   </ul>
     *
     * On errors, returns an XML-RPC error response.
     * The possible error codes and error message are:
     *  <ul>
     *      <li> 3    -  Incorrect parameters passed to method:
     *                      Wanted ... , got ... at param </li>
     *      <li> 801  -  wrong 1st parameter, struct expected.</li>
     *      <li> 805  -  xr_resetStorage:
     *                      &lt;message from lower layer&gt; </li>
     *  </ul>
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     * @see LocStor::getAudioClip
     */
    public function xr_resetStorage($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->resetStorage(
            isset($r['loadSampleData']) ? $r['loadSampleData'] : TRUE,
            !(isset($r['invalidateSessionIds']) ? $r['invalidateSessionIds'] : FALSE)
        );
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }

    /**
     * Test XMLRPC - strupper and return given string,
     * also return loginname of logged user
     *  - debug method only
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     */
    public function xr_test($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'str'=>strtoupper($r['teststring']),
            'login' => Alib::GetSessLogin($r['sessid']),
            'sessid'=>$r['sessid']
        )));
    }

    /**
     * Open writable URL for put method - debug method only
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     */
    public function xr_openPut($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->bsOpenPut();
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode($res));
    }


    /**
     * Close writable URL - debug method only
     *
     * @param XML_RPC_Message $input
     * @return XML_RPC_Response
     */
    public function xr_closePut($input)
    {
        list($ok, $r) = XR_LocStor::xr_getParams($input);
        if (!$ok) {
            return $r;
        }
        $res = $this->bsClosePut($r['token'], $r['chsum']);
        if (PEAR::isError($res)) {
            return new XML_RPC_Response(0, 805,
                "xr_getAudioClip: ".$res->getMessage()." ".$res->getUserInfo()
            );
        }
        return new XML_RPC_Response(XML_RPC_encode(array(
            'fname'=>$res['fname'],
            'owner'=>$res['owner'],
        )));
    }

    /* ==================================================== "private" methods */
    /**
     * Check and convert struct of parameters
     *
     * @param XML_RPC_Message $input
     * @return array
     *      Array of two items: first item is boolean, indicating
     *      successful decode.
     *      On success, the second param is an array of values.
     *      On failure, the second param is an XML_RPC_Response object.
     */
    protected static function xr_getParams($input)
    {
        $p = $input->getParam(0);
        if (isset($p) && ($p->scalartyp()=="struct")) {
            $r = XML_RPC_decode($p);
            return array(TRUE, $r);
        } else {
            return array(FALSE, new XML_RPC_Response(0, 801, "wrong 1st parameter, struct expected." ));
        }
    }

} // class XR_LocStor

?>