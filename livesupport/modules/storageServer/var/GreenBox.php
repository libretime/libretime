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
    Version  : $Revision: 1.53 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/GreenBox.php,v $

------------------------------------------------------------------------------*/
require_once "BasicStor.php";

/**
 *  GreenBox class
 *
 *  LiveSupport file storage module
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.53 $
 *  @see BasicStor
 */
class GreenBox extends BasicStor{

    /* ======================================================= public methods */

    /**
     *  Create new folder
     *
     *  @param parid int, parent id
     *  @param folderName string, name for new folder
     *  @param sessid string, session id
     *  @return id of new folder
     *  @exception PEAR::error
     */
    function createFolder($parid, $folderName, $sessid='')
    {
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        return $this->bsCreateFolder($parid, $folderName);
    }

    /**
     *  Store new file in the storage
     *
     *  @param parid int, parent id
     *  @param fileName string, name for new file
     *  @param mediaFileLP string, local path of media file
     *  @param mdataFileLP string, local path of metadata file
     *  @param sessid string, session id
     *  @param gunid string, global unique id OPTIONAL
     *  @param ftype string, internal file type
     *  @return int
     *  @exception PEAR::error
     */
    function putFile($parid, $fileName,
         $mediaFileLP, $mdataFileLP, $sessid='',
         $gunid=NULL, $ftype='audioclip')
    {
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        return $this->bsPutFile(
            $parid, $fileName, $mediaFileLP, $mdataFileLP, $gunid, $ftype
        );
    }

    /**
     *  Store new webstream
     *
     *  @param parid int, parent id
     *  @param fileName string, name for new file
     *  @param mdataFileLP string, local path of metadata file
     *  @param sessid string, session id
     *  @param gunid string, global unique id OPTIONAL
     *  @param url string, wewbstream url
     *  @return int
     *  @exception PEAR::error
     */
    function storeWebstream($parid, $fileName, $mdataFileLP, $sessid='',
         $gunid=NULL, $url)
    {
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        if(!file_exists($mdataFileLP)){
            $mdataFileLP = dirname(__FILE__).'/emptyWebstream.xml';
        }
        $oid = $this->bsPutFile(
            $parid, $fileName, '', $mdataFileLP, $gunid, 'webstream'
        );
        if(PEAR::isError($oid)) return $oid;
        $r = $this-> bsSetMetadataValue(
            $oid, 'ls:url', $url, NULL, NULL, 'metadata');
        if(PEAR::isError($r)) return $r;
        return $oid;
    }


    /**
     *  Access stored file - increase access counter
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return string access token
     */
    function accessFile($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $gunid = $this->_gunidFromId($id);
        $r = $this->bsAccess(NULL, '', $gunid, 'access');
        if(PEAR::isError($r)){ return $r; }
        $token = $r['token'];
        return $token;
    }

    /**
     *  Release stored file - decrease access counter
     *
     *  @param token string, access token
     *  @param sessid string, session id
     *  @return boolean
     */
    function releaseFile($token, $sessid='')
    {
        $r = $this->bsRelease($token, 'access');
        if(PEAR::isError($r)){ return $r; }
        return FALSE;
    }

    /**
     *  Analyze media file for internal metadata information
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return array
     */
    function analyzeFile($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsAnalyzeFile($id);
    }

    /**
     *  Rename file
     *
     *  @param id int, virt.file's local id
     *  @param newName string
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function renameFile($id, $newName, $sessid='')
    {
        $parid = $this->getParent($id);
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        return $this->bsRenameFile($id, $newName);
    }

    /**
     *  Move file
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function moveFile($id, $did, $sessid='')
    {
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        return $this->bsMoveFile($id, $did);
    }

    /**
     *  Copy file
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function copyFile($id, $did, $sessid='')
    {
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        return $this->bsCopyFile($id, $did);
    }

    /**
     *  Delete file
     *
     *  @param id int, virt.file's local id
     *  @param sessid int
     *  @return true or PEAR::error
     */
    function deleteFile($id, $sessid='')
    {
        $parid = $this->getParent($id);
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        return $this->bsDeleteFile($id);
    }

    /* ---------------------------------------------- replicas, versions etc. */
    /**
     *  Create replica.<br>
     *  <b>TODO: NOT FINISHED</b>
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @param replicaName string, name of new replica
     *  @param sessid string, session id
     *  @return int, local id of new object
     */
    function createReplica($id, $did, $replicaName='', $sessid='')
    {
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        return $this->bsCreateReplica($id, $did, $replicaName);
    }

    /**
     *  Create version.<br>
     *  <b>TODO: NOT FINISHED</b>
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @param versionLabel string, name of new version
     *  @param sessid string, session id
     *  @return int, local id of new object
     */
    function createVersion($id, $did, $versionLabel, $sessid='')
    {
        return $this->bsCreateVersion($id, $did, $versionLabel);
    }


    /* ------------------------------------------------------------- metadata */

    /**
     *  Replace metadata with new XML file or string
     *
     *  @param id int, virt.file's local id
     *  @param mdata string, XML string or local path of metadata XML file
     *  @param mdataLoc string, metadata location: 'file'|'string'
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function replaceMetadata($id, $mdata, $mdataLoc='file', $sessid='')
    {
        if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsReplaceMetadata($id, $mdata, $mdataLoc);
    }

    /**
     *  Get metadata XML tree as string
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return string or PEAR::error
     */
    function getMdata($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsGetMetadata($id);
    }

    /**
     *  Get metadata element value
     *
     *  @param id int, virt.file's local id
     *  @param category string, metadata element name
     *  @param sessid string, session id
     *  @param lang string, optional xml:lang value for select language version
     *  @return array of matching records as hash with fields:
     *   <ul>
     *      <li>mid int, local metadata record id</li>
     *      <li>value string, element value</li>
     *      <li>attrs hasharray of element's attributes indexed by
     *          qualified name (e.g. xml:lang)</li>
     *   </ul>
     */
    function getMdataValue($id, $category, $sessid='', $lang=NULL)
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsGetMetadataValue($id, $category, $lang);
    }

    /**
     *  Set metadata element value
     *
     *  @param id int, virt.file's local id
     *  @param category string, metadata element identification (e.g. dc:title)
     *  @param sessid string, session id
     *  @param value string/NULL value to store, if NULL then delete record
     *  @param lang string, optional xml:lang value for select language version
     *  @param mid int, metadata record id (OPTIONAL on unique elements)
     *  @return boolean
     */
    function setMdataValue($id, $category, $sessid, $value, $lang=NULL, $mid=NULL)
    {
        if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
            return $res;
        return $this->bsSetMetadataValue($id, $category, $value, $lang, $mid);
    }

    /**
     *  Search in local metadata database.
     *
     *  @param criteria hash, with following structure:<br>
     *   <ul>
     *     <li>filetype - string, type of searched files,
     *       meaningful values: 'audioclip', 'playlist'</li>
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
     *  @param sessid string, session id
     *  @return hash, field 'results' is an array with gunid strings
     *  of files have been found
     *  @see BasicStor::bsLocalSearch
     */
    function localSearch($criteria, $sessid='')
    {
        $limit  = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        return $this->bsLocalSearch($criteria, $limit, $offset);
    }

    /**
     *  Return values of specified metadata category
     *
     *  @param category string, metadata category name
     *          with or without namespace prefix (dc:title, author)
     *  @param criteria hash, see localSearch method
     *  @param sessid string
     *  @return hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching values
     *  @see BasicStor::bsBrowseCategory
     */
    function browseCategory($category, $criteria, $sessid='')
    {
        $limit  = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        $res = $this->bsBrowseCategory($category, $limit, $offset, $criteria);
        return $res;
    }

    /*====================================================== playlist methods */
    /**
     *  Create a new empty playlist.
     *
     *  @param parid int, parent id
     *  @param fname string, human readable menmonic file name
     *  @param sessid string, session ID
     *  @return int, local id of created playlist
     */
    function createPlaylist($parid, $fname, $sessid='')
    {
        $gunid  = StoredFile::_createGunid();
        require_once"LocStor.php";
        $lc =& new LocStor($this->dbc, $this->config);
        $gunid2 = $lc->createPlaylist($sessid, $gunid, $fname);
        if(PEAR::isError($gunid2)) return $gunid2;
        // get local id:
        $id = $this->_idFromGunid($gunid2);
        if(PEAR::isError($id)) return $id;
        // get home dir id:
        $hdid = $this->_getHomeDirId($sessid);
        if(PEAR::isError($hdid)) return $hdid;
        // optionally move it to the destination folder:
        if($parid != $hdid && !is_null($parid)){
            $r = $this->bsMoveFile($id, $parid);
            if(PEAR::isError($r)){ return $r; }
        }
        return $id;
    }

    /**
     *  Return playlist as XML string
     *
     *  @param id int, local object id
     *  @param sessid string, session ID
     *  @return string, XML
     */
    function getPlaylistXml($id, $sessid)
    {
        return $this->getMdata($id, $sessid);
    }

    /**
     *  Return playlist as hierarchical PHP hash-array
     *
     *  @param id int, local object id
     *  @param sessid string, session ID
     *  @return array
     */
    function getPlaylistArray($id, $sessid)
    {
        $gunid = $this->_gunidFromId($id);
        $pl =& StoredFile::recall($this, $id);
        if(PEAR::isError($pl)){ return $pl; }
        $gunid = $pl->gunid;
        return $pl->md->genPhpArray();
    }

    /**
     *  Mark playlist as edited and return edit token
     *
     *  @param id int, local object id
     *  @param sessid string, session ID
     *  @return string, playlist access token
     */
    function lockPlaylistForEdit($id, $sessid)
    {
        $gunid = $this->_gunidFromId($id);
        require_once"LocStor.php";
        $lc =& new LocStor($this->dbc, $this->config);
        $res = $lc->editPlaylist($sessid, $gunid);
        if(PEAR::isError($res)) return $res;
        return $res['token'];
    }

    /**
     *  Release token, regenerate XML from DB and clear edit flag.
     *
     *  @param token string, playlist access token
     *  @param sessid string, session ID
     *  @return string gunid
     */
    function releaseLockedPlaylist($token, $sessid)
    {
        $gunid = $this->bsCloseDownload($token, 'metadata');
        if(PEAR::isError($gunid)) return $gunid;
        $ac =& StoredFile::recallByGunid($this, $gunid);
        if(PEAR::isError($ac)){ return $ac; }
        $r = $ac->md->regenerateXmlFile();
        if(PEAR::isError($r)) return $r;
        $this->_setEditFlag($gunid, FALSE, $sessid);
        return $gunid;
    }

    /**
     *  Add audioclip specified by local id to the playlist
     *
     *  @param token string, playlist access token
     *  @param acId string, local ID of added file
     *  @param sessid string, session ID
     *  @param fadeIn string, optional, in time format hh:mm:ss.ssssss
     *  @param fadeOut string, dtto
     *  @return string, generated playlistElement gunid
     */
    function addAudioClipToPlaylist($token, $acId, $sessid,
        $fadeIn=NULL, $fadeOut=NULL)
    {
        require_once"Playlist.php";
        $pl =& Playlist::recallByToken($this, $token);
        if(PEAR::isError($pl)) return $pl;
        if('playlist' == ($type = $this->getFileType($acId))){
            return PEAR::raiseError(
                "GreenBox::addAudioClipToPlaylist: object type not supported".
                " ($type)"
            );
        }
        $res = $pl->addAudioClip($acId, $fadeIn, $fadeOut);
        if(PEAR::isError($res)) return $res;
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return $res;
    }

    /**
     *  Remove audioclip from playlist
     *
     *  @param token string, playlist access token
     *  @param plElGunid string, global id of deleted playlistElement
     *  @param sessid string, session ID
     *  @return boolean
     */
    function delAudioClipFromPlaylist($token, $plElGunid, $sessid)
    {
        require_once"Playlist.php";
        $pl =& Playlist::recallByToken($this, $token);
        if(PEAR::isError($pl)) return $pl;
        $res = $pl->delAudioClip($plElGunid);
        if(PEAR::isError($res)) return $res;
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return $res;
    }

    /**
     *  Change fadeInfo values
     *
     *  @param token string, playlist access token
     *  @param plElGunid string, global id of deleted playlistElement
     *  @param fadeIn string, optional, in time format hh:mm:ss.ssssss
     *  @param fadeOut string, dtto
     *  @param sessid string, session ID
     *  @return boolean
     */
    function changeFadeInfo($token, $plElGunid, $fadeIn, $fadeOut, $sessid)
    {
        require_once"Playlist.php";
        $pl =& Playlist::recallByToken($this, $token);
        if(PEAR::isError($pl)) return $pl;
        $res = $pl->changeFadeInfo($plElGunid, $fadeIn, $fadeOut);
        if(PEAR::isError($res)) return $res;
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }

    /**
     *  <span style="color:green">Move audioClip to the new position in the playlist</span>
     *
     *  @param token string, playlist access token
     *  @param plElGunid string, global id of deleted playlistElement
     *  @param newPos int - new position in playlist
     *  @param sessid string, session ID
     *  @return boolean
     */
    function moveAudioClipInPlaylist($token, $plElGunid, $newPos, $sessid)
    {
        require_once"Playlist.php";
        $pl =& Playlist::recallByToken($this, $token);
        if(PEAR::isError($pl)) return $pl;
        $res = $pl->moveAudioClip($plElGunid, $newPos);
        if(PEAR::isError($res)) return $res;
        // recalculate offsets and total length:
        $r = $pl->recalculateTimes();
        if(PEAR::isError($r)){ return $r; }
        return TRUE;
    }

    /**
     *  RollBack playlist changes to the locked state
     *
     *  @param token string, playlist access token
     *  @param sessid string, session ID
     *  @return string gunid of playlist
     */
    function revertEditedPlaylist($token, $sessid)
    {
        $gunid = $this->bsCloseDownload($token, 'metadata');
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
     *  @param id int, local id
     *  @param sessid string, session ID
     *  @return boolean
     */
    function deletePlaylist($id, $sessid)
    {
        $gunid = $this->_gunidFromId($id);
        require_once"LocStor.php";
        $lc =& new LocStor($this->dbc, $this->config);
        return $lc->deletePlaylist($sessid, $gunid);
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID exists.
     *
     *  @param id int, local id
     *  @param sessid string, session ID
     *  @return boolean
     */
    function existsPlaylist($id, $sessid)
    {
        $gunid = $this->_gunidFromId($id);
        require_once"LocStor.php";
        $lc =& new LocStor($this->dbc, $this->config);
        return $lc->existsPlaylist($sessid, $gunid);
    }

    /**
     *  Check whether a Playlist metafile with the given playlist ID
     *  is available for editing, i.e., exists and is not marked as
     *  beeing edited.
     *
     *  @param id int, local id
     *  @param sessid string, session ID
     *  @return FALSE | int - id of user editing it
     */
    function playlistIsAvailable($id, $sessid)
    {
        $gunid = $this->_gunidFromId($id);
        require_once"LocStor.php";
        $lc =& new LocStor($this->dbc, $this->config);
        return $lc->playlistIsAvailable($sessid, $gunid, TRUE);
    }

    /**
     *  Convert playlist time value to float seconds
     *
     *  @param plt string, playlist time value (HH:mm:ss.dddddd)
     *  @return int, seconds
     */
    function _plTimeToSecs($plt)
    {
        require_once"Playlist.php";
        return Playlist::_plTimeToSecs($plt);
    }

    /**
     *  Convert float seconds value to playlist time format
     *
     *  @param s0 int, seconds
     *  @return string, time in playlist time format (HH:mm:ss.dddddd)
     */
    function _secsToPlTime($s0)
    {
        require_once"Playlist.php";
        return Playlist::_secsToPlTime($s0);
    }
    
    /* ============================================== methods for preferences */

    /**
     *  Read preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @return string, preference value
     */
    function loadPref($sessid, $key)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->loadPref($sessid, $key);
        return $res;
    }

    /**
     *  Save preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @param value string, preference value
     *  @return boolean
     */
    function savePref($sessid, $key, $value)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->savePref($sessid, $key, $value);
        return $res;
    }

    /**
     *  Delete preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @return boolean
     */
    function delPref($sessid, $key)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->delPref($sessid, $key);
        return $res;
    }

    /**
     *  Read group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @return string, preference value
     */
    function loadGroupPref($sessid, $group, $key)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->loadGroupPref($sessid, $group, $key);
        return $res;
    }

    /**
     *  Save group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @param value string, preference value
     *  @return boolean
     */
    function saveGroupPref($sessid, $group, $key, $value)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->saveGroupPref($sessid, $group, $key, $value);
        return $res;
    }

    /**
     *  Delete group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @return boolean
     */
    function delGroupPref($sessid, $group, $key)
    {
        require_once '../../../storageServer/var/Prefs.php';
        $pr =& new Prefs($this);
        $res = $pr->delGroupPref($sessid, $group, $key);
        return $res;
    }
    /* --------------------------------------------------------- info methods */

    /**
     *  List files in folder
     *
     *  @param id int, local id of folder
     *  @param sessid string, session id
     *  @return array
     */
    function listFolder($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $listArr = $this->bsListFolder($id);
        return $listArr;
    }

    /**
     *  Get type of stored file (by local id)
     *
     *  @param id int, local id
     *  @return string/err
     */
    function getFileType($id)
    {
        // $id = $this->_idFromGunid($gunid);
        $type = $this->getObjType($id);
        return $type;
    }

    /**
     *  Check if file gunid exists in the storage and
     *  user have permission to read it
     *
     *  @param sessid string, session id
     *  @param gunid string
     *  @param ftype string, internal file type, optional
     *  @return string/err
     */
    function existsFile($sessid, $gunid, $ftype=NULL)
    {
        $id = $this->_idFromGunid($gunid);
        $ex = $this->bsExistsFile($id, $ftype);
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $ex;
    }

    /* ---------------------------------------------------- redefined methods */

    /**
     *  Get file's path in virtual filesystem
     *
     *  @param id int
     *  @return array
     */
    function getPath($id)
    {
        $pa =  parent::getPath($id, 'id, name, type');
        array_shift($pa);
        return $pa;
    }

}
?>
