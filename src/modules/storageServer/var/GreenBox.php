<?php
require_once("BasicStor.php");
require_once("LocStor.php");
require_once('Prefs.php');

/**
 * GreenBox class
 *
 * File storage module.
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
class GreenBox extends BasicStor {

    /* ====================================================== storage methods */

    /**
     * Create new folder
     *
     * @param int $parid
     * 		Parent id
     * @param string $folderName
     * 		Name for new folder
     * @param string $sessid
     * 		Session id
     * @return int
     * 		ID of new folder
     * @exception PEAR::error
     */
    public function createFolder($parid, $folderName, $sessid='')
    {
        if (($res = BasicStor::Authorize('write', $parid, $sessid)) !== TRUE) {
            return $res;
        }
        return BasicStor::bsCreateFolder($parid, $folderName);
    } // fn createFolder


    /**
     * Store new file in the storage
     *
     * @param int $parid, parent id
     * @param string $fileName
     * 		The name for the new file.
     * @param string $mediaFileLP
     * 		Local path of the media file
     * @param string $mdataFileLP
     * 		Local path of the metadata file
     * @param string $sessid
     * 		Session id
     * @param string $gunid
     * 		Global unique id
     * @param string $ftype
     * 		Internal file type
     * @return int
     *      ID of the StoredFile that was created.
     */
    public function putFile($p_parentId, $p_values, $p_sessionId='')
    {
        if (($res = BasicStor::Authorize('write', $p_parentId, $p_sessionId)) !== TRUE) {
            return $res;
        }
        $storedFile = $this->bsPutFile($p_parentId, $p_values);
        return $storedFile;
    } // fn putFile


    /**
     * Store new webstream
     *
     * @param int $parid
     * 		Parent id
     * @param string $fileName
     * 		Name for new file
     * @param string $mdataFileLP
     * 		Local path of metadata file
     * @param string $sessid
     * 		Session id
     * @param string $gunid
     * 		Global unique id
     * @param string $url
     * 		Webstream url
     * @return int
     *  @exception PEAR::error
     */
    public function storeWebstream($parid, $fileName, $mdataFileLP, $sessid='',
         $gunid=NULL, $url)
    {
        if (($res = BasicStor::Authorize('write', $parid, $sessid)) !== TRUE) {
            return $res;
        }
        if (!file_exists($mdataFileLP)) {
            $mdataFileLP = dirname(__FILE__).'/emptyWebstream.xml';
        }
        $values = array(
            "filename" => $fileName,
            "metadata" => $mdataFileLP,
            "gunid" => $gunid,
            "filetype" => "webstream"
        );
        $storedFile = $this->bsPutFile($parid, $values);
        if (PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $oid = $storedFile->getId();
        $r = $this->bsSetMetadataValue(
            $oid, 'ls:url', $url, NULL, NULL, 'metadata');
        if (PEAR::isError($r)) {
            return $r;
        }
        return $oid;
    } // fn storeWebstream


    /**
     * Access stored file - increase access counter
     *
     * @param int $id
     *      virt.file's local id
     * @param string $sessid
     *      session id
     * @return string access token
     */
//    function accessFile($id, $sessid='')
//    {
//        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
//            return $res;
//        }
//        $gunid = BasicStor::GunidFromId($id);
//        $r = BasicStor::bsAccess(NULL, '', $gunid, 'access');
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        $token = $r['token'];
//        return $token;
//    } // fn accessFile


    /**
     * Release stored file - decrease access counter
     *
     * @param string $token
     *      access token
     * @param string $sessid
     *      session id
     * @return boolean
     */
//    function releaseFile($token, $sessid='')
//    {
//        $r = BasicStor::bsRelease($token, 'access');
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        return FALSE;
//    } // fn releaseFile


    /**
     * Analyze media file for internal metadata information
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $sessid
     * 		Session id
     * @return array
     */
    public function analyzeFile($id, $sessid='')
    {
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsAnalyzeFile($id);
    } // fn analyzeFile


    /**
     * Rename file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $newName
     * @param string $sessid
     * 		Session id
     * @return boolean|PEAR_Error
     */
    public function renameFile($id, $newName, $sessid='')
    {
        $parid = M2tree::GetParent($id);
        if (($res = BasicStor::Authorize('write', $parid, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsRenameFile($id, $newName);
    } // fn renameFile


    /**
     * Move file
     *
     * @param int $id
     *      virt.file's local id
     * @param int $did
     *      destination folder local id
     * @param string $sessid
     *      session id
     * @return boolean|PEAR_Error
     */
    public function moveFile($id, $did, $sessid='')
    {
        $res = BasicStor::Authorize(array('read', 'write'), array($id, $did), $sessid);
        if ($res !== TRUE) {
            return $res;
        }
        return $this->bsMoveFile($id, $did);
    } // fn moveFile


    /**
     * Copy file
     *
     * @param int $id
     *      virt.file's local id
     * @param int $did
     *      destination folder local id
     * @param string $sessid
     *      session id
     * @return boolean|PEAR_Error
     */
    public function copyFile($id, $did, $sessid='')
    {
        $res = BasicStor::Authorize(array('read', 'write'), array($id, $did), $sessid);
        if($res !== TRUE) {
            return $res;
        }
        return $this->bsCopyFile($id, $did);
    } // fn copyFile


    /**
     * Replace file. Doesn't change filetype!
     *
     * @param int $id
     *      virt.file's local id
     * @param string $mediaFileLP
     *      local path of media file
     * @param string $mdataFileLP
     *      local path of metadata file
     * @param string $sessid
     *      session id
     * @return TRUE|PEAR_Error
     */
    public function replaceFile($id, $mediaFileLP, $mdataFileLP, $sessid='')
    {
        if (($res = BasicStor::Authorize('write', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsReplaceFile($id, $mediaFileLP, $mdataFileLP);
    } // fn replaceFile


    /**
     * Delete file
     *
     * @param int $id
     *      virt.file's local id
     * @param int $sessid
     * @param boolean $forced
     *      if true don't use trash
     * @return true|PEAR_Error
     */
    public function deleteFile($id, $sessid='', $forced=FALSE)
    {
        $parid = M2tree::GetParent($id);
        if (($res = BasicStor::Authorize('write', $parid, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsDeleteFile($id, $forced);
    } // fn deleteFile


    /* ------------------------------------------------------------- metadata */

    /**
     * Replace metadata with new XML file or string
     *
     * @param int $id
     *      Virtual file's local id
     * @param string $mdata
     *      XML string or local path of metadata XML file
     * @param string $mdataLoc
     *      metadata location: 'file'|'string'
     * @param  string $sessid
     *      session id
     * @return boolean|PEAR_Error
     */
//    public function replaceMetadata($id, $mdata, $mdataLoc='file', $sessid='')
//    {
//        if (($res = BasicStor::Authorize('write', $id, $sessid)) !== TRUE) {
//            return $res;
//        }
//        return $this->bsReplaceMetadata($id, $mdata, $mdataLoc);
//    } // fn replaceMetadata


    /**
     * Get metadata XML tree as string
     *
     * @param int $id
     *      Virtual file's local id
     * @param string $sessid
     *      session id
     * @return string|PEAR_Error
     * @todo rename this function to "getMetadata"
     */
    public function getMetadata($id, $sessid='')
    {
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsGetMetadata($id);
    }


    /**
     * Return metadata as hierarchical PHP hash-array
     *
     * If xml:lang attribute is specified in metadata category,
     * array of metadata values indexed by xml:lang values
     * is presented instead of one plain metadata value.
     *
     * @param int $id
     *      local object id
     * @param string $sessid
     *      session ID
     * @return array
     */
    public function getMetadataArray($id, $sessid)
    {
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        $storedFile = StoredFile::Recall($id);
        if (is_null($storedFile) || PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $arr = $storedFile->md->genPhpArray();
        $md = FALSE;
        foreach ($arr['children'] as $i=>$a) {
            if ($a['elementname'] == 'metadata'){
                $md = $arr['children'][$i];
                break;
            }
        }
        if ($md === FALSE) {
            return PEAR::raiseError(
                "GreenBox::getMetadataArray: no metadata container found"
            );
        }
        $res = array();
        foreach ($md['children'] as $el) {
            $lang = isset($el['attrs']['xml:lang']) ? $el['attrs']['xml:lang'] : "";
            $category = $el['elementname'];
            if ($lang == "") {
                $res[$category] = $el['content'];
            } else {
                $res[$category][$lang] = $el['content'];
            }
        }
        return $res;
    }


    /**
     * Get metadata element value
     *
     * @param int $id
     *      Virtual file's local id
     * @param string $category
     *      metadata element name
     * @param string $sessid
     *      session id
     * @param string $lang
     *      xml:lang value for select language version
     * @param string $deflang
     *      xml:lang for default language
     * @return array of matching records as hash with fields:
     *   <ul>
     *      <li>mid int, local metadata record id</li>
     *      <li>value string, element value</li>
     *      <li>attrs hasharray of element's attributes indexed by
     *          qualified name (e.g. xml:lang)</li>
     *   </ul>
     */
    public function getMetadataValue($id, $category, $sessid='',
        $lang=NULL, $deflang=NULL)
    {
        if (!is_numeric($id)) {
            return null;
        }
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsGetMetadataValue($id, $category);
    } // fn getMetadataValue


    /**
     * Set metadata element value
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $category
     * 		Metadata element identification (e.g. dc:title)
     * @param string $sessid
     * 		Session id
     * @param string $value
     * 		The value to store, if NULL then delete record
     * @param string $lang
     * 		xml:lang value for select language version
     * @param int $mid
     * 		(optional on unique elements) Metadata record id
     * @return boolean
     */
    public function setMetadataValue($id, $category, $sessid, $value, $lang=NULL, $mid=NULL)
    {
        if (($res = BasicStor::Authorize('write', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $this->bsSetMetadataValue($id, $category, $value, $lang, $mid);
    } // fn setMetadataValue


    /**
     * Search in local metadata database.
     *
     * @param array $criteria
     *      with following structure:<br>
     *   <ul>
     *     <li>filetype - string, type of searched files,
     *       meaningful values: 'audioclip', 'webstream', 'playlist', 'all'</li>
     *     <li>operator - string, type of conditions join
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
     *     <li>conditions - array of hashes with structure:
     *       <ul>
     *           <li>cat - string, metadata category name</li>
     *           <li>op - string, operator - meaningful values:
     *               'full', 'partial', 'prefix', '=', '&lt;',
     *               '&lt;=', '&gt;', '&gt;='</li>
     *           <li>val - string, search value</li>
     *       </ul>
     *     </li>
     *   </ul>
     * @param string $sessid
     *      session id
     * @return array of hashes, fields:
     *   <ul>
     *       <li>cnt : integer - number of matching gunids
     *              of files have been found</li>
     *       <li>results : array of hashes:
     *          <ul>
     *           <li>gunid: string</li>
     *           <li>type: string - audioclip | playlist | webstream</li>
     *           <li>title: string - dc:title from metadata</li>
     *           <li>creator: string - dc:creator from metadata</li>
     *           <li>length: string - dcterms:extent in extent format</li>
     *          </ul>
     *      </li>
     *   </ul>
     *  @see BasicStor::bsLocalSearch
     */
    public function localSearch($criteria, $sessid='')
    {
        $limit = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        return $this->bsLocalSearch($criteria, $limit, $offset);
    } // fn localSearch


    /**
     * Return values of specified metadata category
     *
     * @param string $category
     *      metadata category name
     *          with or without namespace prefix (dc:title, author)
     * @param array $criteria
     *      see localSearch method
     * @param string $sessid
     * @return array, fields:
     *       results : array with found values
     *       cnt : integer - number of matching values
     * @see BasicStor::bsBrowseCategory
     */
    public function browseCategory($category, $criteria = null, $sessid = '')
    {
        $limit = 0;
        $offset = 0;
        if (!is_null($criteria)) {
            $limit = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
            $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        }
        $res = $this->bsBrowseCategory($category, $limit, $offset, $criteria);
        return $res;
    } // fn browseCategory


    /*====================================================== playlist methods */
    /**
     * Create a new empty playlist.
     *
     * @param int $parid
     *      parent id
     * @param string $fname
     *      human readable menmonic file name
     * @param string $sessid
     *      session ID
     * @return int
     * 		local id of created playlist
     */
    public function createPlaylist($parid, $fname, $sessid='')
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = StoredFile::CreateGunid();
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        $gunid2 = $lc->createPlaylist($sessid, $gunid, $fname);
        if (PEAR::isError($gunid2)) {
            return $gunid2;
        }
        // get local id:
        $id = BasicStor::IdFromGunid($gunid2);
        if (PEAR::isError($id)) {
            return $id;
        }
        // get home dir id:
        $hdid = $this->_getHomeDirIdFromSess($sessid);
        if (PEAR::isError($hdid)) {
            return $hdid;
        }
        // optionally move it to the destination folder:
        if($parid != $hdid && !is_null($parid)){
            $r = $this->bsMoveFile($id, $parid);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $id;
    } // fn createPlaylist


    /**
     * Return playlist as XML string
     *
     * @param int $id
     *      local object id
     * @param string $sessid
     *      session ID
     * @return string
     * 		XML
     */
//    function getPlaylistXml($id, $sessid)
//    {
//        return $this->getMetadata($id, $sessid);
//    } // fn getPlaylistXml


    /**
     * Return playlist as hierarchical PHP hash-array
     *
     * @param int $id
     *      local object id
     * @param string $sessid
     *      session ID
     * @return array
     */
    public function getPlaylistArray($id, $sessid)
    {
        $gunid = BasicStor::GunidFromId($id);
        $pl = StoredFile::Recall($id);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $gunid = $pl->gunid;
        return $pl->md->genPhpArray();
    } // fn getPlaylistArray


    /**
     * Mark playlist as edited and return edit token
     *
     * @param int $id
     *      local object id
     * @param string $sessid
     *      session ID
     * @return string
     * 		playlist access token
     */
    public function lockPlaylistForEdit($id, $sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = BasicStor::GunidFromId($id);
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        $res = $lc->editPlaylist($sessid, $gunid);
        if (PEAR::isError($res)) {
            return $res;
        }
        return $res['token'];
    } // fn lockPlaylistForEdit


    /**
     * Release token, regenerate XML from DB and clear edit flag.
     *
     * @param string $token
     *      playlist access token
     * @param string $sessid
     *      session ID
     * @return string gunid
     */
    public function releaseLockedPlaylist($token, $sessid)
    {
        $gunid = $this->bsCloseDownload($token, 'metadata');
        if (PEAR::isError($gunid)) {
            return $gunid;
        }
        $storedFile = StoredFile::RecallByGunid($gunid);
        if (is_null($storedFile) || PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $r = $storedFile->md->regenerateXmlFile();
        if (PEAR::isError($r)) {
            return $r;
        }
        $this->setEditFlag($gunid, FALSE, $sessid);
        return $gunid;
    } // fn releaseLockedPlaylist


    /**
     * Add audioclip specified by local id to the playlist
     *
     * @param string $token
     *      playlist access token
     * @param string $acId
     *      local ID of added file
     * @param string $sessid
     *      session ID
     * @param string $fadeIn
     *      in time format hh:mm:ss.ssssss
     * @param string $fadeOut
     *      in time format hh:mm:ss.ssssss
     * @param string $length
     *      length in extent format -
     *          for webstream (or for overrule length of audioclip)
     * @param string $clipstart
     *      optional clipstart time format hh:mm:ss.ssssss - relative to begin
     * @param string $clipend
     *      optional $clipend time format hh:mm:ss.ssssss - relative to begin
     * @return string, generated playlistElement gunid
     */
    public function addAudioClipToPlaylist($token, $acId, $sessid,
        $fadeIn=NULL, $fadeOut=NULL, $length=NULL, $clipstart=NULL, $clipend=NULL)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByToken($token);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $acGunid = BasicStor::GunidFromId($acId);
        if ($pl->cyclicRecursion($acGunid)){
            return PEAR::raiseError(
                "GreenBox::addAudioClipToPlaylist: cyclic-recursion detected".
                " ($type)"
            );
        }
        $res = $pl->addAudioClip($acId, $fadeIn, $fadeOut, NULL, $length, $clipstart, $clipend);
        if (PEAR::isError($res)) {
            return $res;
        }
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if (PEAR::isError($r)) {
            return $r;
        }
        return $res;
    } // fn addAudioClipToPlaylist


    /**
     * Remove audioclip from playlist
     *
     * @param string $token
     *      playlist access token
     * @param string $plElGunid
     *      global id of deleted playlistElement
     * @param string $sessid
     *      session ID
     * @return boolean
     * @todo rename this function to "deleteAudioClipFromPlaylist"
     */
    public function delAudioClipFromPlaylist($token, $plElGunid, $sessid)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByToken($token);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $res = $pl->delAudioClip($plElGunid);
        if (PEAR::isError($res)) {
            return $res;
        }
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if (PEAR::isError($r)) {
            return $r;
        }
        return $res;
    } // fn delAudioClipFromPlaylist


    /**
     * Change fadeInfo values
     *
     * @param string $token
     *      playlist access token
     * @param string $plElGunid
     *      global id of deleted playlistElement
     * @param string $fadeIn
     *      in time format hh:mm:ss.ssssss
     * @param string $fadeOut
     *      in time format hh:mm:ss.ssssss
     * @param sessid $string
     *      session ID
     * @return boolean
     */
    public function changeFadeInfo($token, $plElGunid, $fadeIn, $fadeOut, $sessid)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByToken($token);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $res = $pl->changeFadeInfo($plElGunid, $fadeIn, $fadeOut);
        if (PEAR::isError($res)) {
            return $res;
        }
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn changeFadeInfo
    
    /**
     * Change cueIn/curOut values for playlist element
     *
     * @param string $token
     *      playlist access token
     * @param string $plElGunid
     *      global id of deleted playlistElement
     * @param string $clipStart
     *      in time format hh:mm:ss.ssssss
     * @param string $clipEnd
     *      in time format hh:mm:ss.ssssss
     *      relative to begin
     * @param sessid $string
     *      session ID
     * @return boolean or pear error object
     */
    public function changeClipLength($token, $plElGunid, $clipStart, $clipEnd, $sessid)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByToken($token);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $res = $pl->changeClipLength($plElGunid, $clipStart, $clipEnd);
        if (PEAR::isError($res)) {
            return $res;
        }
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn changeFadeInfo


    /**
     * Move audioClip to the new position in the playlist.
     *
     * This method may change id attributes of playlistElements and/or
     * fadeInfo.
     *
     * @param string $token
     *      playlist access token
     * @param string $plElGunid
     *      global id of deleted playlistElement
     * @param int $newPos
     *      new position in playlist
     * @param string $sessid
     *      session ID
     * @return boolean
     */
    public function moveAudioClipInPlaylist($token, $plElGunid, $newPos, $sessid)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByToken($token);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $res = $pl->moveAudioClip($plElGunid, $newPos);
        if (PEAR::isError($res)) {
            return $res;
        }
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn moveAudioClipInPlaylist


    /**
     * RollBack playlist changes to the locked state
     *
     * @param string $token
     *      playlist access token
     * @param string $sessid
     *      session ID
     * @return string
     * 		gunid of playlist
     */
    public function revertEditedPlaylist($token, $sessid='')
    {
        global $CC_CONFIG, $CC_DBC;
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        return $lc->revertEditedPlaylist($token, $sessid);
    } // fn revertEditedPlaylist


    /**
     * Delete a Playlist metafile.
     *
     * @param int $id
     *      local id
     * @param string $sessid
     *      session ID
     * @return boolean
     */
    public function deletePlaylist($id, $sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = BasicStor::GunidFromId($id);
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        return $lc->deletePlaylist($sessid, $gunid);
    } // fn deletePlaylist


    /**
     * Find info about clip at specified offset in playlist.
     *
     * @param string $sessid
     *      session id
     * @param string $plid
     *      playlist global unique id
     * @param string $offset
     *      current playtime (hh:mm:ss.ssssss)
     * @param int $distance
     *      0=current clip; 1=next clip ...
     * @param string $lang
     *      xml:lang value for select language version
     * @param string $deflang
     *      xml:lang for default language
     * @return array of matching clip info:
     *   <ul>
     *      <li>gunid string, global unique id of clip</li>
     *      <li>elapsed string, already played time of clip</li>
     *      <li>remaining string, remaining time of clip</li>
     *      <li>duration string, total playlength of clip </li>
     *   </ul>
     */
    public function displayPlaylistClipAtOffset($sessid, $plid, $offset, $distance=0,
        $lang=NULL, $deflang=NULL)
    {
        require_once("Playlist.php");
        $pl = StoredFile::RecallByGunid($plid);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $res = $pl->displayPlaylistClipAtOffset($offset, $distance);
        if (PEAR::isError($res)) {
            return $res;
        }
        $res['title'] = NULL;
        $id = BasicStor::IdFromGunid($res['gunid']);
        if (PEAR::isError($id)) {
            return $id;
        }
        if (!is_null($id)) {
            $res['title'] = $this->bsGetMetadataValue($id, "dc:title");
        }
        $res['playlist_title'] = NULL;
        $id = BasicStor::IdFromGunid($plid);
        if (PEAR::isError($id)) {
            return $id;
        }
        if (!is_null($id)) {
            $res['playlist'] = $this->bsGetMetadataValue($id, "dc:title");
        }
        return $res;
    } // fn displayPlaylistClipAtOffset


    /**
     * Create a tarfile with playlist export - playlist and all matching
     * sub-playlists and media files (if desired)
     *
     * @param string $sessid
     *      string, session ID
     * @param array $plids
     *      array of strings, playlist global unique IDs
     *          (one gunid is accepted too)
     * @param string $type
     *      playlist format, values: lspl | smil | m3u
     * @param boolean $standalone
     *       if only playlist should be exported or
     *          with all related files
     * @return hasharray with  fields:
     *      fname string: readable fname,
     *      token string: access token
     */
    public function exportPlaylistOpen($sessid, $plids, $type='lspl', $standalone=FALSE)
    {
        return $this->bsExportPlaylistOpen($plids, $type, !$standalone);
    } // fn exportPlaylistOpen


    /**
     * Close playlist export previously opened by the exportPlaylistOpen method
     *
     * @param string $token
     *      access token obtained from exportPlaylistOpen
     *            method call
     * @return TRUE|PEAR_Error
     */
    public function exportPlaylistClose($token)
    {
        return $this->bsExportPlaylistClose($token);
    } // fn exportPlaylistClose


    /**
     * Open writable handle for import playlist in LS Archive format
     *
     * @param string $sessid
     *      session id
     * @param string $chsum
     *      md5 checksum of imported file
     * @return hasharray with:
     *      fname string: writable local filename
     *      token string: put token
     */
    public function importPlaylistOpen($sessid, $chsum='')
    {
        $userid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($userid)) {
            return $userid;
        }
        $r = $this->bsOpenPut($chsum, NULL, $userid);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $r;
    } // fn importPlaylistOpen


    /**
     * Close import-handle and import playlist
     *
     * @param string $token
     *      import token obtained by importPlaylistOpen method
     * @return int
     * 		result file local id (or error object)
     */
    public function importPlaylistClose($token)
    {
        $arr = $this->bsClosePut($token);
        if (PEAR::isError($arr)) {
            return $arr;
        }
        $fname = $arr['fname'];
        $owner = $arr['owner'];
        $parid = $this->_getHomeDirId($owner);
        if (PEAR::isError($parid)) {
            if (file_exists($fname)) {
                @unlink($fname);
            }
            return $parid;
        }
        $res = $this->bsImportPlaylist($parid, $fname, $owner);
        if (file_exists($fname)) {
            @unlink($fname);
        }
        return $res;
    } // fn importPlaylistClose


    /**
     * Check whether a Playlist metafile with the given playlist ID exists.
     *
     * @param int $id
     *      local id
     * @param string $sessid
     *      session ID
     * @return boolean
     */
    public function existsPlaylist($id, $sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = BasicStor::GunidFromId($id);
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        return $lc->existsPlaylist($sessid, $gunid);
    } // fn existsPlaylist


    /**
     * Check whether a Playlist metafile with the given playlist ID
     * is available for editing, i.e., exists and is not marked as
     * beeing edited.
     *
     * @param int $id
     *      local id
     * @param string $sessid
     *      session ID
     * @return TRUE|int
     *      id of user editing it
     */
    public function playlistIsAvailable($id, $sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = BasicStor::GunidFromId($id);
        $lc = new LocStor($CC_DBC, $CC_CONFIG);
        return $lc->playlistIsAvailable($sessid, $gunid, TRUE);
    } // fn playlistIsAvailable


    /* ---------------------------------------------- time conversion methods */
    /**
     * Convert playlist time value to float seconds
     *
     * @param string $plt
     *      playlist time value (HH:mm:ss.dddddd)
     * @return int
     * 		seconds
     */
      public function playlistTimeToSeconds($plt)
      {
          require_once("Playlist.php");
          return Playlist::playlistTimeToSeconds($plt);
      }


    /**
     * Convert float seconds value to playlist time format
     *
     * @param int $s0
     *      seconds
     * @return string
     * 		time in playlist time format (HH:mm:ss.dddddd)
     */
      public static function secondsToPlaylistTime($s0)
      {
          require_once("Playlist.php");
          return Playlist::secondsToPlaylistTime($s0);
      } // fn secondsToPlaylistTime


    /* ------------------------------------------------------- render methods */
    /**
     * Render playlist to ogg file (open handle)
     *
     * @param string $sessid
     *      session id
     * @param string $plid
     *      playlist gunid
     * @return string $token
     *      render token
     */
    public function renderPlaylistToFileOpen($sessid, $plid)
    {
        require_once("Renderer.php");
        $r = Renderer::rnRender2FileOpen($this, $plid);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $r;
    } // fn renderPlaylistToFileOpen


    /**
     * Render playlist to ogg file (check results)
     *
     * @param string $token
     *       render token
     * @return hasharray:
     *      status : string - susccess | working | fault
     *      tmpfile : string - filepath to result temporary file
     */
    public function renderPlaylistToFileCheck($token)
    {
        require_once("Renderer.php");
        $r = Renderer::rnRender2FileCheck($this, $token);
        if (PEAR::isError($r)) {
            return $r;
        }
        return array('status'=>$r['status'], 'tmpfile'=>$r['tmpfile']);
    } // fn renderPlaylistToFileCheck


    /**
     * Render playlist to ogg file (list results)
     *
     * @param string $status
     *       success | working | fault
     *      if this parameter is not set, then return with all unclosed
     * @return array of hasharray:
     *      status : string - susccess | working | fault
     *      tmpfile : string - filepath to result temporary file
     */
    public function renderPlaylistToFileList($status='')
    {
        require_once("Renderer.php");
        return Renderer::rnRender2FileList($this, $status);
    } // fn renderPlaylistToFileList


    /**
     * Render playlist to ogg file (close handle)
     *
     * @param string $token
     *       render token
     * @return boolean
     *      status
     */
    public function renderPlaylistToFileClose($token)
    {
        require_once("Renderer.php");
        $r = Renderer::rnRender2FileClose($this, $token);
        if (PEAR::isError($r)) {
            return $r;
        }
        return array(TRUE);
    } // fn renderPlaylistToFileClose


    /**
     * Render playlist to storage media clip (open handle)
     *
     * @param string $sessid
     *       session id
     * @param string $plid
     *       playlist gunid
     * @return string
     *      render token
     */
    public function renderPlaylistToStorageOpen($sessid, $plid)
    {
        require_once("Renderer.php");
        $owner = GreenBox::getSessUserId($sessid);
        if (PEAR::isError($owner)) {
            return $owner;
        }
        $r = Renderer::rnRender2FileOpen($this, $plid, $owner);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $r;
    } // fn renderPlaylistToStorageOpen


    /**
     * Render playlist to storage media clip (check results)
     *
     * @param string $token
     *       render token
     * @return hasharray:
     *      status : string - susccess | working | fault
     *      gunid : string - gunid of result file
     */
    public function renderPlaylistToStorageCheck($token)
    {
        require_once("Renderer.php");
        $r = Renderer::rnRender2StorageCheck($this, $token);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $r;
    } // fn renderPlaylistToStorageCheck


    /**
     * Render playlist to RSS file (open handle)
     *
     * @param string $sessid
     *       session id
     * @param string $plid
     *       playlist gunid
     * @return string
     *      render token
     */
    public function renderPlaylistToRSSOpen($sessid, $plid)
    {
        $token = '123456789abcdeff';
        $fakeFile = $CC_CONFIG['accessDir']."/$token.rss";
        file_put_contents($fakeFile, "fake rendered file");
        return array('token'=>$token);
    } // fn renderPlaylistToRSSOpen


    /**
     * Render playlist to RSS file (check results)
     *
     * @param string $token
     *       render token
     * @return hasharray:
     *      status : string - susccess | working | fault
     *      tmpfile : string - filepath to result temporary file
     */
    public function renderPlaylistToRSSCheck($token)
    {
        $fakeFile = $CC_CONFIG['accessDir']."/$token.rss";
        if ($token != '123456789abcdeff' || !file_exists($fakeFile)){
            return PEAR::raiseError(
                "LocStor::renderPlaylistToRSSCheck: invalid token ($token)"
            );
        }
        return array(
            'status'=> 'success',
            'tmpfile'   => $fakeFile,
        );
    } // fn renderPlaylistToRSSCheck


    /**
     * Render playlist to RSS file (list results)
     *
     * @param string $status
     *      success | working | fault
     * @return array of hasharray:
     *      status : string - susccess | working | fault
     *      tmpfile : string - filepath to result temporary file
     */
//    function renderPlaylistToRSSList($status='')
//    {
//        $dummytokens = array ('123456789abcdeff');
//        foreach ($dummytokens as $token) {
//            $r[] = $this->renderPlaylistToRSSCheck($token);
//        }
//        return $r;
//    } // fn renderPlaylistToRSSList


    /**
     * Render playlist to RSS file (close handle)
     *
     * @param string $token
     *      render token
     * @return boolean
     *      status
     */
    public function renderPlaylistToRSSClose($token)
    {
        if ($token != '123456789abcdeff'){
            return PEAR::raiseError(
                "GreenBox::renderPlaylistToRSSClose: invalid token"
            );
        }
        $fakeFile = $CC_CONFIG['accessDir']."/$token.rss";
        unlink($fakeFile);
        return TRUE;
    } // fn renderPlaylistToRSSClose


    /*================================================= storage admin methods */
    /* ------------------------------------------------------- backup methods */
    /**
     * Create backup of storage (open handle)
     *
     * @param string $sessid
     *      session id
     * @param struct $criteria
     *      see search criteria
     * @return array
     *           token  : string - backup token
     */
    public function createBackupOpen($sessid, $criteria='')
    {
        require_once("Backup.php");
        $bu = new Backup($this);
        if (PEAR::isError($bu)) {
            return $bu;
        }
        return $bu->openBackup($sessid,$criteria);
    } // fn createBackupOpen


    /**
     * Create backup of storage (check results)
     *
     * @param string $token
     *      backup token
     * @return hasharray with field:
     *      status : string - susccess | working | fault
     *      faultString: string - description of fault
     *      token  : stirng - backup token
     *      url    : string - access url
     */
    public function createBackupCheck($token)
    {
        require_once("Backup.php");
        $bu = new Backup($this);
        if (PEAR::isError($bu)) {
            return $bu;
        }
        return $bu->checkBackup($token);
    } // fn createBackupCheck


    /**
     * Create backup of storage (list results)
     *
     * @param string $sessid
     *      session id
     * @param string $stat
     *      if this parameter is not set, then return with all unclosed backups
     * @return array of hasharray with field:
     *      status : string - susccess | working | fault
     *      token  : stirng - backup token
     *      url    : string - access url
     */
    public function createBackupList($sessid, $stat='')
    {
        require_once("Backup.php");
        $bu = new Backup($this);
        if (PEAR::isError($bu)) {
            return $bu;
        }
        return $bu->listBackups($stat);
    } // fn createBackupList


    /**
     * Create backup of storage (close handle)
     *
     * @param string $token
     *      backup token
     * @return boolean
     *      status
     */
    public function createBackupClose($token)
    {
        require_once("Backup.php");
        $bu = new Backup($this);
        if (PEAR::isError($bu)) {
            return $bu;
        }
        return $bu->closeBackup($token);
    } // fn createBackupClose


    /* ===================================================== restore funcitons*/
    /**
     * Restore a backup file
     *
     * @param  string $sessid
     *      session id
     * @param  string $filename
     *      backup file path
     * @return string
     *      restore token
     */
    public function backupRestoreOpen($sessid, $filename)
    {
        require_once('Restore.php');
        $rs = new Restore($this);
        if (PEAR::isError($rs)) {
            return $rs;
        }
        return $rs->openRestore($sessid,$filename);
    } // fn backupRestoreOpen


    /**
     * Check status of backup restore
     *
     * @param string $token
     *      restore token
     * @return hasharray
     *      fields:
     * 	            token:  string - restore token
     *              status: string - working | fault | success
     *              faultString: string - description of fault
     */
    public function backupRestoreCheck($token)
    {
        require_once('Restore.php');
        $rs = new Restore($this);
        if (PEAR::isError($rs)) {
            return $rs;
        }
        return $rs->checkRestore($token);
    } // fn backupRestoreCheck


    /**
     * Close a restore procedure
     *
     * @param string $token
     *      restore token
     * @return boolean
     *      is success
     */
    public function backupRestoreClose($token) {
    	require_once('Restore.php');
    	$rs = new Restore($this);
    	if (PEAR::isError($rs)) {
    	    return $rs;
    	}
    	return $rs->closeRestore($token);
    } // fn backupRestoreClose

    /* ============================================== methods for preferences */

    /**
     * Read preference record by session id
     *
     * @param string $sessid
     *      session id
     * @param string $key
     *      preference key
     * @return string
     * 		preference value
     */
    public function loadPref($sessid, $key)
    {
        $pr = new Prefs($this);
        $res = $pr->loadPref($sessid, $key);
        return $res;
    } // fn loadPref


    /**
     * Save preference record by session id
     *
     * @param string $sessid
     *      session id
     * @param string $key
     *      preference key
     * @param string $value
     *      preference value
     * @return boolean
     */
    public function savePref($sessid, $key, $value)
    {
        $pr = new Prefs($this);
        $res = $pr->savePref($sessid, $key, $value);
        return $res;
    } // fn savePref


    /**
     * Delete preference record by session id
     *
     * @param string $sessid
     *      session id
     * @param string $key
     *      preference key
     * @return boolean
     */
    public function delPref($sessid, $key)
    {
        $pr = new Prefs($this);
        $res = $pr->delPref($sessid, $key);
        return $res;
    } // fn delPref


    /**
     * Read group preference record
     *
     * @param string $sessid
     *      session id
     * @param string $group
     *      group name
     * @param string $key
     *      preference key
     * @return string
     * 		preference value
     */
    public function loadGroupPref($sessid, $group, $key)
    {
        $pr = new Prefs($this);
        $res = $pr->loadGroupPref($sessid, $group, $key);
        return $res;
    } // fn loadGroupPref


    /**
     * Save group preference record
     *
     * @param string $sessid
     *      session id
     * @param string $group
     *      group name
     * @param string $key
     *      preference key
     * @param string $value
     *      preference value
     * @return boolean
     */
    public function saveGroupPref($sessid, $group, $key, $value)
    {
        $pr = new Prefs($this);
        $res = $pr->saveGroupPref($sessid, $group, $key, $value);
        return $res;
    } // fn saveGroupPref


    /**
     * Delete group preference record
     *
     * @param string $sessid
     *      session id
     * @param string $group
     *      group name
     * @param string $key
     *      preference key
     * @return boolean
     */
    public function delGroupPref($sessid, $group, $key)
    {
        $pr = new Prefs($this);
        $res = $pr->delGroupPref($sessid, $group, $key);
        return $res;
    } // fn delGroupPref


    /* =================================================== networking methods */
    /* ------------------------------------------------------- common methods */
    /**
     * Common "check" method for transports
     *
     * @param string $trtok
     *      transport token
     * @return array with fields:
     *      trtype: string - audioclip | playlist | search | file
     *      state: string - transport state
     *      direction: string - up | down
     *      expectedsize: int - file size in bytes
     *      realsize: int - currently transported bytes
     *      expectedchsum: string - orginal file checksum
     *      realchsum: string - transported file checksum
     *      ... ?
     */
    public function getTransportInfo($trtok)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->getTransportInfo($trtok);
    } // fn getTransportInfo


    /**
     * Turn transports on/off, optionaly return current state.
     *
     * @param string $sessid
     * 		session id
     * @param boolean $onOff
     * 		optional (if not used, current state is returned)
     * @return boolean
     *      previous state
     */
    public function turnOnOffTransports($sessid, $onOff=NULL)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->turnOnOffTransports($sessid, $onOff);
    } // fn turnOnOffTransports


    /**
     * Pause, resume or cancel transport
     *
     * @param string $trtok
     *      transport token
     * @param string $action
     *      pause | resume | cancel
     * @return string
     *      resulting transport state
     */
    public function doTransportAction($trtok, $action)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        $res = $tr->doTransportAction($trtok, $action);
        return $res;
    } // fn doTransportAction


    /* ------------------------ methods for ls-archive-format file transports */
    /**
     * Open async file transfer from local storageServer to network hub,
     * file should be ls-archive-format file.
     *
     * @param string $filePath
     *      local path to uploaded file
     * @return string
     *      transport token
     */
    public function uploadFile2Hub($filePath)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->uploadFile2Hub($filePath);
    } // fn uploadFile2Hub


    /**
     * Get list of prepared transfers initiated by hub
     *
     * @return array of structs/hasharrays with fields:
     *      trtok: string transport token
     *      ... ?
     */
    public function getHubInitiatedTransfers()
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->getHubInitiatedTransfers();
    } // fn getHubInitiatedTransfers


    /**
     * Start of download initiated by hub
     *
     * @param string $trtok
     *      transport token obtained from
     *          the getHubInitiatedTransfers method
     * @return string
     *      transport token
     */
    public function startHubInitiatedTransfer($trtok)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->startHubInitiatedTransfer($trtok);
    } // fn startHubInitiatedTransfer


    /* ------------- special methods for audioClip/webstream object transport */

    /**
     * Start upload of audioClip/webstream/playlist from local storageServer
     * to hub
     *
     * @param string $gunid
     *      global unique id of object being transported
     * @param boolean $withContent
     *      if true, transport playlist content too
     * @return string
     *      transport token
     * @todo rename this function "uploadToHub"
     */
    public function upload2Hub($gunid, $withContent=FALSE)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->upload2Hub($gunid, $withContent);
    } // fn upload2Hub


    /**
     * Start download of audioClip/webstream/playlist from hub to local
     * storageServer
     *
     * @param string $sessid
     * 		session id
     * @param string $gunid
     *      global unique id of playlist being transported
     * @param boolean $withContent
     *      if true, transport playlist content too
     * @return string
     *      transport token
     */
    public function downloadFromHub($sessid, $gunid, $withContent=TRUE){
        $uid = GreenBox::getSessUserId($sessid);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->downloadFromHub($uid, $gunid, $withContent);
    } // fn downloadFromHub


    /* ------------------------------------------------ global-search methods */
    /**
     * Start search job on network hub
     *
     * @param array $criteria
     * 		criteria format (see localSearch)
     * @return string
     *      transport token
     */
    public function globalSearch($criteria)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->globalSearch($criteria);
    } // fn globalSearch


    /**
     * Get results from search job on network hub
     *
     * @param string $trtok
     *      transport token
     * @param boolean $andClose
     * 		if TRUE, close transport token
     * @return array
     * 		search result format (see localSearch)
     */
    public function getSearchResults($trtok, $andClose=TRUE)
    {
        require_once("Transport.php");
        $tr = new Transport($this);
        return $tr->getSearchResults($trtok, $andClose);
    } // fn getSearchResults


    /* ========================================================= info methods */
    /**
     * List files in folder
     *
     * @param int $id
     *      local id of folder
     * @param string $sessid
     *      session id
     * @return array
     */
    public function listFolder($id, $sessid='')
    {
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        $listArr = $this->bsListFolder($id);
        return $listArr;
    } // fn listFolder


    /**
     * Get type of stored file (by local id)
     *
     * @param int $id
     *      local id
     * @return string|PEAR_Error
     */
    public static function getFileType($id)
    {
        $type = BasicStor::GetObjType($id);
        return $type;
    } // fn getFileType


    /**
     * Check if file gunid exists in the storage and
     * user have permission to read it
     *
     * @param string $sessid
     *      session id
     * @param string $gunid
     * @param string $ftype
     *      internal file type
     * @return string|PEAR_Error
     */
    public function existsFile($sessid, $gunid, $ftype=NULL)
    {
        $id = BasicStor::IdFromGunid($gunid);
        $ex = $this->bsExistsFile($id, $ftype);
        if (($res = BasicStor::Authorize('read', $id, $sessid)) !== TRUE) {
            return $res;
        }
        return $ex;
    } // fn existsFile


    /* ==================================================== redefined methods */
    /**
     * Get file's path in virtual filesystem
     *
     * @param int $id
     * @return array
     */
    public static function GetPath($id)
    {
        $pa = M2tree::GetPath($id, 'id, name, type');
        array_shift($pa);
        return $pa;
    } // fn getPath


    /**
     * Get user id from session id
     *
     * This redefinition only simulate old (bad) behaviour - returns NULL
     * for wrong sessid (code ALIBERR_NOTEXISTS).
     * HtmlUI depends on it.
     *
     * @param string $sessid
     * @return int|null|PEAR_Error
     */
    public static function GetSessUserId($sessid)
    {
        $r = Alib::GetSessUserId($sessid);
        if (PEAR::isError($r)) {
            if ($r->getCode() == ALIBERR_NOTEXISTS) {
                return NULL;
            } else {
                return $r;
            }
        }
        return $r;
    } // fn getSessUserId


    /**
     * Change user password.
     *
     *   ('superuser mode'= superuser is changing some password without
     *    knowledge of the old password)
     *
     * @param string $login
     * @param string $oldpass
     *      old password
     *      (should be null or empty for 'superuser mode')
     * @param string $pass
     * @param string $sessid
     *      session id, required for 'superuser mode'
     * @return boolean/err
     */
    public function passwd($login, $oldpass=null, $pass='', $sessid='')
    {
        if (is_null($oldpass) || ($oldpass == '') ) {
            if (($res = BasicStor::Authorize('subjects', $this->rootId, $sessid)) !== TRUE) {
                sleep(2);
                return $res;
            } else {
                $oldpass = null;
            }
        } else {
            if (FALSE === Subjects::Authenticate($login, $oldpass)) {
                sleep(2);
                return PEAR::raiseError(
                    "GreenBox::passwd: access denied (oldpass)", GBERR_DENY);
            }
        }
        $res = Subjects::Passwd($login, $oldpass, $pass);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    } // fn passwd


    /**
     * Insert permission record
     *
     * @param int $sid
     *      local user/group id
     * @param string $action
     * @param int $oid
     *      local object id
     * @param char $type
     *      'A'|'D' (allow/deny)
     * @param string $sessid
     *      session id
     * @return int
     *      local permission id
     */
    public function addPerm($sid, $action, $oid, $type='A', $sessid='')
    {
        $parid = M2tree::GetParent($oid);
        if (($res = BasicStor::Authorize('editPerms', $parid, $sessid)) !== TRUE) {
            return $res;
        }
        return Alib::AddPerm($sid, $action, $oid, $type);
    } // fn addPerm


    /**
     * Remove permission record
     *
     * @param int $permid
     * 		local permission id
     * @param int $subj
     * 		local user/group id
     * @param int $obj
     * 		local object id
     * @param string $sessid
     * 		session id
     * @return boolean/error
     */
    public function removePerm($permid=NULL, $subj=NULL, $obj=NULL, $sessid='')
    {
        if (!is_null($permid)) {
            $oid = Alib::GetPermOid($permid);
            if (PEAR::isError($oid)) {
                return $oid;
            }
            if (!is_null($oid)) {
                $parid = M2tree::GetParent($oid);
                if (($res = BasicStor::Authorize('editPerms', $parid, $sessid)) !== TRUE) {
                    return $res;
                }
            }
        }
        $res = Alib::RemovePerm($permid, $subj, $obj);
        return $res;
    } // fn removePerm

} // class GreenBox
?>