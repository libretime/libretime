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
    Version  : $Revision: 1.17 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/LocStor.php,v $

------------------------------------------------------------------------------*/

require_once "GreenBox.php";

/**
 *  LocStor class
 *
 *  Livesupport local storage interface
 */
class LocStor extends GreenBox{
    /* ---------------------------------------------------------------- store */
    /**
     *  Store or replace existing audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @param metadata string, metadata XML string
     *  @param fname string, human readable menmonic file name
     *                      with extension corresponding to filetype
     *  @param chsum string, md5 checksum of media file
     *  @return struct {url:writable URL for HTTP PUT, token:access token
     */
    function storeAudioClipOpen($sessid, $gunid, $metadata, $fname, $chsum)
    {
        // test if specified gunid exists:
        if(!preg_match("|^([0-9a-fA-F]{16})?$|", $gunid)){
            return PEAR::raiseError(
                "LocStor.php: storeAudioClipOpen: Wrong gunid ($gunid)"
            );
        }
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(!PEAR::isError($ac)){
            // gunid exists - do replace
            if(($res = $this->_authorize(
                'write', $ac->getId(), $sessid
            )) !== TRUE) return $res;
            if($ac->isAccessed()){
                return PEAR::raiseError(
                    'LocStor.php: storeAudioClipOpen: is accessed'
                );
            }
            $res = $ac->replace(
                $ac->getId(), $ac->name, '', $metadata, 'string'
            );
            if(PEAR::isError($res)) return $res;
        }else{
            // gunid doesn't exists - do insert
            $tmpFname = uniqid('');
            $parid = $this->_getHomeDirId($sessid);
            if(PEAR::isError($parid)) return $parid;
            if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
                return $res;
            $oid = $this->addObj($tmpFname , 'File', $parid);
            if(PEAR::isError($oid)) return $oid;
            $ac =&  StoredFile::insert(
                &$this, $oid, '', '', $metadata, 'string',
                $gunid, 'audioclip'
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
        $res = $this->renameFile($oid, $fname, $sessid);
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
        $ac =& StoredFile::recallByToken(&$this, $token);
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

    /* --------------------------------------------------------------- access */
    /**
     *  Make access to audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @return array with: seekable filehandle, access token
     */
    function accessRawAudioData($sessid, $gunid)
    {
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->accessRawMediaData($sessid);
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
        $ac =& StoredFile::recallByToken(&$this, $token);
        if(PEAR::isError($ac)) return $ac;
        return $ac->releaseRawMediaData($token);
    }

    /* ------------------------------------------------------------- download */
    /**
     *  Create and return downloadable URL for audio file
     *
     *  @param sessid string, session id
     *  @param gunid string, global unique id
     *  @return array with: downloadable URL, download token
     */
    function downloadRawAudioDataOpen($sessid, $gunid)
    {
        $res = $this->existsAudioClip($sessid, $gunid);
        if(PEAR::isError($res)) return $res;
        $id = $this->_idFromGunid($gunid);
        if(is_null($id)){
            return PEAR::raiseError(
             "LocStor::downloadRawAudioDataOpen: gunid not found ($gunid)"
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
     *  @return array with: downloadable URL, download token
     */
    function downloadMetadataOpen($sessid, $gunid)
    {
        $res = $this->existsAudioClip($sessid, $gunid);
        if(PEAR::isError($res)) return $res;
        $id = $this->_idFromGunid($gunid);
        if(is_null($id)){
            return PEAR::raiseError(
             "LocStor::downloadMetadataOpen: gunid not found ($gunid)"
            );
        }
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsOpenDownload($id, 'metadata');
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
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $md = $this->getMdata($ac->getId(), $sessid);
        if(PEAR::isError($md)) return $md;
        return $md;
    }

    /* --------------------------------------------------------------- search */
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
     *      <li>playlistResults : array with gunid strings
     *          of playlists have been found</li>
     *   </ul>
     *  @see GreenBox::localSearch
     */
    function searchMetadata($sessid, $criteria)
    {
        $filetype   = strtolower($criteria['filetype']);
        if($filetype=='all'){
            $criteriaAC = $criteria;    $criteriaAC['filetype'] = 'audioclip';
            $criteriaPL = $criteria;    $criteriaPL['filetype'] = 'playlist';
            $resAC = $this->localSearch($criteriaAC);
            $resPL = $this->localSearch($criteriaPL);
            return array(
                'audioClipResults'  => $resAC['results'],
                'playlistResults'   => $resPL['results']
            );
        }
        $srchRes = $this->localSearch($criteria, $sessid);
        $res = array('audioClipResults'=>NULL, 'playlistResults'=>NULL);
        switch($filetype){
        case"audioclip":
            $res['audioClipResults'] = $srchRes['results'];
            break;
        case"playlist":
            $res['playlistResults'] = $srchRes['results'];
            break;
        }
        return $res;
    }

    /* ----------------------------------------------------------------- etc. */
    /**
     *  Check if audio clip exists
     *
     *  @param sessid string
     *  @param gunid string
     *  @return boolean
     *  @see GreenBox
     */
    function existsAudioClip($sessid, $gunid)
    {
        $ex = $this->existsFile($sessid, $gunid, 'audioclip');
        if(!$ex) return FALSE;
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
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
     *  @see GreenBox
     */
    function existsFile($sessid, $gunid, $ftype=NULL)
    {
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)){
            // catch some exceptions
            switch($ac->getCode()){
                case GBERR_FILENEX:
                case GBERR_FOBJNEX:
                    return FALSE;
                    break;
                default: return $ac;
            }
        }
        if(!is_null($ftype) && ($ac->_getType() != $ftype)) return FALSE;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return TRUE;
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
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        if($ac->isAccessed()){
            return PEAR::raiseError(
                'LocStor.php: deleteAudioClip: is accessed'
            );
        }
        $res = $this->deleteFile($ac->getId(), $sessid);
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
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->replaceMetaData($metadata, 'string');
    }

    /**
     *  Reset storageServer for debugging.
     *
     *  @param input string
     */
    function resetStorage($input='')
    {
        $this->deleteData();
        $rootHD = $this->getObjId('root', $this->storId);
#        $this->login('root', $this->config['tmpRootPass']);
#        $s = $this->sessid;
        include"../tests/sampleData.php";
        $res = array();
        foreach($sampleData as $k=>$it){
            list($media, $meta) = $it;
#            $r = $this->putFile($rootHD, "file".($k+1), $media, $meta, $s);
            $r = $this->bsPutFile(
                $rootHD, basename($media), $media, $meta, '', 'audioclip'
            );
            if(PEAR::isError($r)){ return $r; }
            $res[] = $this->_gunidFromId($r);
        }
#        $this->logout($this->sessid);
        return $res;
    }

    /*====================================================== playlist methods */

    /**
     *  Create a new Playlist metafile.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @param fname string, human readable menmonic file name
     *  @return string, playlist global unique ID
     */
    function createPlaylist($sessid, $playlistId, $fname)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if($ex){
            return PEAR::raiseError(
                'LocStor.php: createPlaylist: already exists'
            );
        }
        $tmpFname = uniqid('');
        $parid = $this->_getHomeDirId($sessid);
        if(PEAR::isError($parid)) return $parid;
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        $oid = $this->addObj($tmpFname , 'File', $parid);
        if(PEAR::isError($oid)) return $oid;
        $ac =&  StoredFile::insert(&$this, $oid, '', '',
            '<?xml version="1.0" encoding="UTF-8"?><smil><body/></smil>',
            'string', $playlistId, 'playlist'
        );
        if(PEAR::isError($ac)){
            $res = $this->removeObj($oid);
            return $ac;
        }
        if($fname == ''){
            $fname = "newFile.xml";
        }
        $res = $this->renameFile($oid, $fname, $sessid);
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
     *  @return struct {url:readable URL for HTTP GET, token:access token}
     */
    function editPlaylist($sessid, $playlistId)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor.php: editPlaylist: playlist not exists'
            );
        }
        if($this->_isEdited($playlistId)){
            return PEAR::raiseError(
                'LocStor.php: editPlaylist: playlist already edited'
            );
        }
        $ac =& StoredFile::recallByGunid(&$this, $playlistId);
        if(PEAR::isError($ac)){ return $ac; }
        $id = $ac->getId();
        $res = $this->bsOpenDownload($id, 'metadata');
        if(PEAR::isError($res)){ return $res; }
        $this->_setEditFlag($playlistId, TRUE);
        return $res;
    }

    /**
     *  Store a new Playlist metafile in place of the old one.
     *
     *  @param sessid string, session ID
     *  @param playlistToken string, playlist access token
     *  @param newPlaylist string, new playlist as XML string
     *  @return boolean
     */
    function savePlaylist($sessid, $playlistToken, $newPlaylist)
    {
        $playlistId = $this->bsCloseDownload($playlistToken, $part='metadata');
        $ac =& StoredFile::recallByGunid(&$this, $playlistId);
        if(PEAR::isError($ac)){ return $ac; }
        $res = $ac->replaceMetaData($newPlaylist, $mdataLoc='string');
        if(PEAR::isError($res)){ return $res; }
        $this->_setEditFlag($playlistId, FALSE);
        return TRUE;
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
                'LocStor.php: deletePlaylist: playlist not exists'
            );
        }
        $ac =& StoredFile::recallByGunid(&$this, $playlistId);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        if($this->_isEdited($playlistId)){
            return PEAR::raiseError(
                'LocStor.php: deletePlaylist: playlist is edited'
            );
        }
        $res = $this->deleteFile($ac->getId(), $sessid);
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Access (read) a Playlist metafile.
     *
     *  @param sessid string, session ID
     *  @param playlistId string, playlist global unique ID
     *  @return struct {url:readable URL for HTTP GET, token:access token
     */
    function accessPlaylist($sessid, $playlistId)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor.php: accessPlaylist: playlist not found'
            );
        }
        $id = $this->_idFromGunid($playlistId);
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsOpenDownload($id, 'metadata');
    }

    /**
     *  Release the resources obtained earlier by accessPlaylist().
     *
     *  @param sessid string, session ID
     *  @param playlistToken string, playlist access token
     *  @return string, playlist ID
     */
    function releasePlaylist($sessid, $playlistToken)
    {
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
     *  @return boolean
     */
    function playlistIsAvailable($sessid, $playlistId)
    {
        $ex = $this->existsPlaylist($sessid, $playlistId);
        if(PEAR::isError($ex)){ return $ex; }
        if(!$ex){
            return PEAR::raiseError(
                'LocStor.php: playlistIsAvailable: playlist not exists'
            );
        }
        return !$this->_isEdited($playlistId);
    }

    /* ---------------------------------------------------- "private" methods */
    /**
     *  Check if playlist is marked as edited
     *
     *  @param playlistId string, playlist global unique ID
     *  @return boolean
     */
    function _isEdited($playlistId)
    {
        $state = StoredFile::_getState($playlistId);
        if($state == 'edited'){ return TRUE; }
        return FALSE;
    }

    /**
     *  Set edit flag
     *
     *  @param playlistId string, playlist global unique ID
     *  @param val boolean, set/clear of edit flag
     *  @return boolean, previous state
     */
    function _setEditFlag($playlistId, $val=TRUE)
    {
        $ac =& StoredFile::recallByGunid(&$this, $playlistId);
        $state = $ac->_getState();
        if($val){ $ac->setState('edited'); }
        else{ $ac->setState('ready'); }
        return ($state == 'edited');
    }
}
?>