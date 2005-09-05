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
    Version  : $Revision: 1.42 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/LocStor.php,v $

------------------------------------------------------------------------------*/

require_once "BasicStor.php";

/**
 *  LocStor class
 *
 *  Livesupport local storage interface
 */
class LocStor extends BasicStor{
    /* ---------------------------------------------------------------- store */
    /**
     *  Store or replace existing audio clip
     *
     *  @param sessid string, session id
     *  @param gunid string, global unique id
     *  @param metadata string, metadata XML string
     *  @param fname string, human readable menmonic file name
     *                      with extension corresponding to filetype
     *  @param chsum string, md5 checksum of media file
     *  @param ftype string audioclip | playlist | webstream
     *  @return struct {url:writable URL for HTTP PUT, token:access token
     */
    function storeAudioClipOpen(
        $sessid, $gunid, $metadata, $fname, $chsum, $ftype='audioclip'
    )
    {
        // test of gunid format:
        if(!$this->_checkGunid($gunid)){
            return PEAR::raiseError(
                "LocStor::storeAudioClipOpen: Wrong gunid ($gunid)"
            );
        }
        // test if specified gunid exists:
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(!PEAR::isError($ac)){
            // gunid exists - do replace
            $oid = $ac->getId();
            if(($res = $this->_authorize('write', $oid, $sessid)) !== TRUE)
                { return $res; }
            if($ac->isAccessed()){
                return PEAR::raiseError(
                    'LocStor::storeAudioClipOpen: is accessed'
                );
            }
            $res = $ac->replace(
                $oid, $ac->name, '', $metadata, 'string'
            );
            if(PEAR::isError($res)) return $res;
        }else{
            // gunid doesn't exists - do insert:
            $tmpFname = uniqid('');
            $parid = $this->_getHomeDirId($sessid);
            if(PEAR::isError($parid)) return $parid;
            if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
                { return $res; }
            $oid = $this->addObj($tmpFname , $ftype, $parid);
            if(PEAR::isError($oid)) return $oid;
            $ac =&  StoredFile::insert(
                $this, $oid, '', '', $metadata, 'string',
                $gunid, $ftype
            );
            if(PEAR::isError($ac)){
                $res = $this->removeObj($oid);
                return $ac;
            }
            if(PEAR::isError($res)) return $res;
        }
        $res = $ac->setState('incomplete');
        if(PEAR::isError($res)) return $res;
        if($fname == ''){
            $fname = "newFile";
        }
        $res = $this->bsRenameFile($oid, $fname);
        if(PEAR::isError($res)) return $res;
        return $this->bsOpenPut($chsum, $ac->gunid);
    }

    /**
     *  Store or replace existing audio clip
     *
     *  @param sessid string
     *  @param token string
     *  @return string gunid or PEAR::error
     */
    function storeAudioClipClose($sessid, $token)
    {
        $ac =& StoredFile::recallByToken($this, $token);
        if(PEAR::isError($ac)){ return $ac; }
        $tmpFname = $this->bsClosePut($token);
        if(PEAR::isError($tmpFname)){ $ac->delete(); return $tmpFname; }
        $res = $ac->replaceRawMediaData($tmpFname);
        if(PEAR::isError($res)){ return $res; }
        if(file_exists($tmpFname)) @unlink($tmpFname);
        $res = $ac->setState('ready');
        if(PEAR::isError($res)) return $res;
        return $ac->gunid;
    }

    /**
     *  Check uploaded file
     *
     *  @param token string, put token
     *  @return hash, (status: boolean, size: int - filesize)
     */
    function uploadCheck($token)
    {
        return $this->bsCheckPut($token);
    }

    /**
     *  Store webstream
     *
     *  @param sessid string, session id
     *  @param gunid string, global unique id
     *  @param metadata string, metadata XML string
     *  @param fname string, human readable menmonic file name
     *                      with extension corresponding to filetype
     *  @param url string, wewbstream url
     *  @return
     */
    function storeWebstream($sessid, $gunid, $metadata, $fname, $url)
    {
        $a = $this->storeAudioClipOpen(
            $sessid, $gunid, $metadata, $fname, md5(''), 'webstream');
        if(PEAR::isError($a)) return $a;
        $gunid = $this->storeAudioClipClose($sessid, $a['token']);
        if(PEAR::isError($gunid)) return $gunid;
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        $oid = $ac->getId();
        $r = $this-> bsSetMetadataValue(
            $oid, 'ls:url', $url, NULL, NULL, 'metadata');
        if(PEAR::isError($r)) return $r;
        return $gunid;
    }

    /* --------------------------------------------------------------- access */
    /**
     *  Make access to audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @param parent int parent token
     *  @return array with: seekable filehandle, access token
     */
    function accessRawAudioData($sessid, $gunid, $parent='0')
    {
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->accessRawMediaData($parent);
    }

    /**
     *  Release access to audio clip
     *
     *  @param sessid string
     *  @param token string, access token
     *  @return boolean or PEAR::error
     */
    function releaseRawAudioData($sessid, $token)
    {
        $ac =& StoredFile::recallByToken($this, $token);
        if(PEAR::isError($ac)) return $ac;
        return $ac->releaseRawMediaData($token);
    }

    /* ------------------------------------------------------------- download */
    /**
     *  Create and return downloadable URL for audio file
     *
     *  @param sessid string, session id
     *  @param gunid string, global unique id
     *  @return array with strings:
     *      downloadable URL, download token, chsum, size, filename
     */
    function downloadRawAudioDataOpen($sessid, $gunid)
    {
        $ex = $this->existsAudioClip($sessid, $gunid);
        if(PEAR::isError($ex)) return $ex;
        $id = $this->_idFromGunid($gunid);
        if(is_null($id) || !$ex){
            return PEAR::raiseError(
                "LocStor::downloadRawAudioDataOpen: gunid not found ($gunid)",
                GBERR_NOTF
            );
        }
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsOpenDownload($id);
    }

    /**
     *  Discard downloadable URL for audio file
     *
     *  @param token string, download token
     *  @return string, gunid
     */
    function downloadRawAudioDataClose($token)
    {
        return $this->bsCloseDownload($token);
    }

    /**
     *  Create and return downloadable URL for metadata
     *
     *  @param sessid string, session id
     *  @param gunid string, global unique id
     *  @return array with strings:
     *      downloadable URL, download token, chsum, filename
     */
    function downloadMetadataOpen($sessid, $gunid)
    {
//        $res = $this->existsAudioClip($sessid, $gunid);
//        if(PEAR::isError($res)) return $res;
        $id = $this->_idFromGunid($gunid);
        if(is_null($id)){
            return PEAR::raiseError(
             "LocStor::downloadMetadataOpen: gunid not found ($gunid)"
            );
        }
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $res = $this->bsOpenDownload($id, 'metadata');
        #unset($res['filename']);
        return $res;
    }

    /**
     *  Discard downloadable URL for metadata
     *
     *  @param token string, download token
     *  @return string, gunid
     */
    function downloadMetadataClose($token)
    {
        return $this->bsCloseDownload($token, 'metadata');
    }

    /**
     *  Return metadata as XML
     *
     *  @param sessid string
     *  @param gunid string
     *  @return string or PEAR::error
     */
    function getAudioClip($sessid, $gunid)
    {
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $md = $this->bsGetMetadata($ac->getId());
        if(PEAR::isError($md)) return $md;
        return $md;
    }

    /* ------------------------------------------------------- search, browse */
    /**
     *  Search in metadata database
     *
     *  @param sessid string
     *  @param criteria hash, with following structure:<br>
     *   <ul>
     *     <li>filetype - string, type of searched files,
     *       meaningful values: 'audioclip', 'playlist', 'all'</li>
     *     <li>operator - string, type of conditions join
     *       (any condition matches / all conditions match), 
     *       meaningful values: 'and', 'or', ''
     *       (may be empty or ommited only with less then 2 items in
     *       &quot;conditions&quot; field)
     *     </li>
     *     <li>limit : int - limit for result arrays (0 means unlimited)</li>
     *     <li>offset : int - starting point (0 means without offset)</li>
     *     <li>orderby : string - metadata category for sorting (optional)</li>
     *     <li>desc : boolean - flag for descending order (optional)</li>
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
     *  @return hash, with fields:
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
     *  @see BasicStor::localSearch
      */
    function searchMetadata($sessid, $criteria)
    {
        if(($res = $this->_authorize('read', $this->storId, $sessid)) !== TRUE)
            return $res;
        $filetype = strtolower($criteria['filetype']);
        $limit  = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        if($filetype=='all'){
            $criteriaAC = $criteria;    $criteriaAC['filetype'] = 'audioclip';
            $criteriaPL = $criteria;    $criteriaPL['filetype'] = 'playlist';
            $resAC = $this->bsLocalSearch($criteriaAC, $limit, $offset);
            $resPL = $this->bsLocalSearch($criteriaPL, $limit, $offset);
            return array(
                'audioClipResults'  => $resAC['results'],
                'audioClipCnt'      => $resAC['cnt'],
                'playlistResults'   => $resPL['results'],
                'playlistCnt'       => $resPL['cnt'],
            );
        }
        $srchRes = $this->bsLocalSearch($criteria, $limit, $offset);
        $res = array(
            'audioClipResults'  => array(),
            'audioClipCnt'      => 0,
            'playlistResults'   => array(),
            'playlistCnt'       => 0,
        );
        switch($filetype){
        case"audioclip":
            $res['audioClipResults'] = $srchRes['results'];
            $res['audioClipCnt']     = $srchRes['cnt'];
            break;
        case"playlist":
            $res['playlistResults'] = $srchRes['results'];
            $res['playlistCnt']     = $srchRes['cnt'];
            break;
        }
        return $res;
    }

    /**
     *  Return values of specified metadata category
     *
     *  @param category string, metadata category name
     *          with or without namespace prefix (dc:title, author)
     *  @param criteria hash, see searchMetadata method
     *  @param sessid string
     *  @return hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching values 
     *  @see BasicStor::bsBrowseCategory
     */
    function browseCategory($category, $criteria=NULL, $sessid='')
    {
        $limit  = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        $res = $this->bsBrowseCategory($category, $limit, $offset, $criteria);
        return $res;
    }
    
    /* ----------------------------------------------------------------- etc. */
    /**
     *  Check if audio clip exists
     *
     *  @param sessid string
     *  @param gunid string
     *  @return boolean
     */
    function existsAudioClip($sessid, $gunid)
    {
        $ex = $this->existsFile($sessid, $gunid, 'audioclip');
        // webstreams are subset of audioclips - moved to BasicStor
        // if($ex === FALSE ){
        //    $ex = $this->existsFile($sessid, $gunid, 'webstream');
        // }
        if($ex === FALSE ) return FALSE;
        if(PEAR::isError($ex)){ return $ex; }
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)){ return $ac; }
        return $ac->exists();
    }

    /**
     *  Check if file exists in the storage
     *
     *  @param sessid string
     *  @param gunid string
     *  @param ftype string, internal file type
     *  @return boolean
     */
    function existsFile($sessid, $gunid, $ftype=NULL)
    {
        $id = $this->_idFromGunid($gunid);
        if(is_null($id)) return FALSE;
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $ex = $this->bsExistsFile($id, $ftype);
        return $ex;
    }

    /**
     *  Delete existing audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @return boolean or PEAR::error
     */
    function deleteAudioClip($sessid, $gunid)
    {
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $res = $this->bsDeleteFile($ac->getId());
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Update existing audio clip metadata
     *
     *  @param sessid string
     *  @param gunid string
     *  @param metadata string, metadata XML string
     *  @return boolean or PEAR::error
     */
    function updateAudioClipMetadata($sessid, $gunid, $metadata)
    {
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->replaceMetaData($metadata, 'string');
    }

    /*====================================================== playlist methods */
    /**
     *  Create a new empty playlist.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @param fname string, human readable mnemonic file name
     *  @return string, playlist global unique ID
     */
    function createPlaylist($sessid, $playlistId, $fname)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if($ex){
            return PEAR::raiseError(
                'LocStor::createPlaylist: already exists'
            );
        }
        $tmpFname = uniqid('');
        $parid = $this->_getHomeDirId($sessid);
        if(PEAR::isError($parid)) return $parid;
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        $oid = $this->addObj($tmpFname , 'playlist', $parid);
        if(PEAR::isError($oid)) return $oid;
        $ac =&  StoredFile::insert($this, $oid, '', '',
            dirname(__FILE__).'/emptyPlaylist.xml',
            'file', $playlistId, 'playlist'
        );
        if(PEAR::isError($ac)){
            $res = $this->removeObj($oid);
            return $ac;
        }
        if($fname == ''){
            $fname = "newFile.xml";
        }
        $res = $this->bsRenameFile($oid, $fname);
        if(PEAR::isError($res)) return $res;
        $res = $ac->setState('ready');
        if(PEAR::isError($res)) return $res;
        $res = $ac->setMime('application/smil');
        if(PEAR::isError($res)) return $res;
        return $ac->gunid;
    }

    /**
     *  Open a Playlist metafile for editing.
     *  Open readable URL and mark file as beeing edited.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @return struct
     *      {url:readable URL for HTTP GET, token:access token, chsum:checksum}
     */
    function editPlaylist($sessid, $playlistId)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor::editPlaylist: playlist not exists'
            );
        }
        if($this->_isEdited($playlistId) !== FALSE){
            return PEAR::raiseError(
                'LocStor::editPlaylist: playlist already edited'
            );
        }
        $ac =& StoredFile::recallByGunid($this, $playlistId);
        if(PEAR::isError($ac)){ return $ac; }
        $id = $ac->getId();
        if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
            return $res;
        $res = $this->bsOpenDownload($id, 'metadata');
        if(PEAR::isError($res)){ return $res; }
        $r = $this->_setEditFlag($playlistId, TRUE, $sessid);
        if(PEAR::isError($r)){ return $r; }
        unset($res['filename']);
        return $res;
    }

    /**
     *  Store a new Playlist metafile in place of the old one.
     *
     *  @param sessid string, session ID
     *  @param playlistToken string, playlist access token
     *  @param newPlaylist string, new playlist as XML string
     *  @return string, playlistId
     */
    function savePlaylist($sessid, $playlistToken, $newPlaylist)
    {
        $playlistId = $this->bsCloseDownload($playlistToken, 'metadata');
        if(PEAR::isError($playlistId)){ return $playlistId; }
        $ac =& StoredFile::recallByGunid($this, $playlistId);
        if(PEAR::isError($ac)){ return $ac; }
        $res = $ac->replaceMetaData($newPlaylist, 'string', 'playlist');
        if(PEAR::isError($res)){ return $res; }
        $r = $this->_setEditFlag($playlistId, FALSE, $sessid);
        if(PEAR::isError($r)){ return $r; }
        return $playlistId;
    }

    /**
     *  RollBack playlist changes to the locked state
     *
     *  @param playlistToken string, playlist access token
     *  @param sessid string, session ID
     *  @return string gunid of playlist
     */
    function revertEditedPlaylist($playlistToken, $sessid='')
    {
        $gunid = $this->bsCloseDownload($playlistToken, 'metadata');
        if(PEAR::isError($gunid)) return $gunid;
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)){ return $ac; }
        $id = $ac->getId();
        $mdata = $ac->getMetaData();
        if(PEAR::isError($mdata)){ return $mdata; }
        $res = $ac->replaceMetaData($mdata, 'string');
        if(PEAR::isError($res)){ return $res; }
        $this->_setEditFlag($gunid, FALSE, $sessid);
        return $gunid;
    }

    /**
     *  Delete a Playlist metafile.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @return boolean
     */
    function deletePlaylist($sessid, $playlistId)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor::deletePlaylist: playlist not exists'
            );
        }
        $ac =& StoredFile::recallByGunid($this, $playlistId);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $res = $this->bsDeleteFile($ac->getId());
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Access (read) a Playlist metafile.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @param recursive boolean, flag for recursive access content
     *                  inside playlist (optional, default: false)
     *  @param parent int parent token
     *  @return struct {
     *      url: readable URL for HTTP GET,
     *      token: access token,
     *      chsum: checksum,
     *      content: array of structs - recursive access (optional)
     *  }
     */
    function accessPlaylist($sessid, $playlistId, $recursive=FALSE, $parent='0')
    {
        if($recursive){
            require_once"AccessRecur.php";
            $r = AccessRecur::accessPlaylist($this, $sessid, $playlistId);
            if(PEAR::isError($r)){ return $r; }
            return $r;
        }
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                "LocStor::accessPlaylist: playlist not found ($playlistId)",
                GBERR_NOTF
            );
        }
        $id = $this->_idFromGunid($playlistId);
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $res = $this->bsOpenDownload($id, 'metadata', $parent);
        unset($res['filename']);
        return $res;
    }

    /**
     *  Release the resources obtained earlier by accessPlaylist().
     *
     *  @param sessid string, session ID
     *  @param playlistToken string, playlist access token
     *  @param recursive boolean, flag for recursive access content
     *                  inside playlist (optional, default: false)
     *  @return string, playlist ID
     */
    function releasePlaylist($sessid, $playlistToken, $recursive=FALSE)
    {
        if($recursive){
            require_once"AccessRecur.php";
            $r = AccessRecur::releasePlaylist($this, $sessid, $playlistToken);
            if(PEAR::isError($r)){ return $r; }
            return $r;
        }
        return $this->bsCloseDownload($playlistToken, 'metadata');
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID exists.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @return boolean
     */
    function existsPlaylist($sessid, $playlistId)
    {
        return $this->existsFile($sessid, $playlistId, 'playlist');
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID
     *  is available for editing, i.e., exists and is not marked as
     *  beeing edited.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @param getUid boolean, optional flag for returning editedby uid
     *  @return boolean
     */
    function playlistIsAvailable($sessid, $playlistId, $getUid=FALSE)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor::playlistIsAvailable: playlist not exists'
            );
        }
        $ie = $this->_isEdited($playlistId);
        if($ie === FALSE) return TRUE;
        if($getUid) return $ie;
        return FALSE;
    }

    /*===================================================== auxiliary methods */
    /**
     *  Dummy method - only returns livesupport version
     *
     *  @return string
     */
    function getVersion()
    {
        //return $this->config['version'];
        return LS_VERSION;
    }

}
?>