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
    Version  : $Revision: 1.10 $
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
     *  @param metadata string with metadata XML
     *  @param chsum string, md5 checksum of media file
     *  @return struct {url:writable URL for HTTP PUT, token:access token
     */
    function storeAudioClipOpen($sessid, $gunid, $metadata, $chsum)
    {
        // test if specified gunid exists:
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(!PEAR::isError($ac)){
            // gunid exists - do replace
            if(($res = $this->_authorize(
                'write', $ac->getId(), $sessid
            )) !== TRUE) return $res;
            if($ac->isAccessed()){
                return PEAR::raiseError(
                    'LocStor.php: storeAudioClip: is accessed'
                );
            }
            $res = $ac->replace(
                $ac->getId(), $ac->name, '', $metadata, 'string'
            );
            if(PEAR::isError($res)) return $res;
        }else{
            // gunid doesn't exists - do insert
            $tmpid = uniqid('');
            $parid = $this->_getHomeDirId($sessid);
            if(PEAR::isError($parid)) return $parid;
            if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
                return $res;
            $oid = $this->addObj($tmpid , 'File', $parid);
            if(PEAR::isError($oid)) return $oid;
            $ac =&  StoredFile::insert(
                &$this, $oid, '', '', $metadata, 'string', $gunid
            );
            if(PEAR::isError($ac)){
                $res = $this->removeObj($oid);
                return $ac;
            }
            $res = $this->renameFile($oid, $ac->gunid, $sessid);
            if(PEAR::isError($res)) return $res;
        }
        $res = $ac->setState('incomplete');
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
        $fname = $this->bsClosePut($token);
        if(PEAR::isError($ac)){ return $ac; }
        $res = $ac->replaceRawMediaData($fname);
        if(PEAR::isError($res)){ return $res; }
        @unlink($fname);
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
     *  @param criteria string
     *  @return array or PEAR::error
     *  @see GreenBox::localSearch
     */
    function searchMetadata($sessid, $criteria)
    {
        $res = $this->localSearch($criteria, $sessid);
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
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->exists();
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
        $this->login('root', $this->config['tmpRootPass']);
        $s = $this->sessid;
        include"../tests/sampleData.php";
        $res = array();
        foreach($sampleData as $k=>$it){
            list($media, $meta) = $it;
            $r = $this->putFile($rootHD, "file".($k+1), $media, $meta, $s);
            if(PEAR::isError($r)){ return $r; }
            $res[] = $this->_gunidFromId($r);
        }
        $this->logout($this->sessid);
        return $res;
    }
}
?>