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
    Version  : $Revision: 1.7 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/LocStor.php,v $

------------------------------------------------------------------------------*/

require_once "GreenBox.php";

/**
 *  LocStor class
 *
 *  Livesupport local storage interface
 */
class LocStor extends GreenBox{
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
     *  Store or replace existing audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @param mediaFileLP string, local path to media file
     *  @param mdataFileLP string, local path to metadata XML file
     *  @return string gunid or PEAR::error
     */
    function storeAudioClip($sessid, $gunid, $mediaFileLP, $mdataFileLP)
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
                $ac->getId(), $ac->name,$mediaFileLP, $mdataFileLP
            );
            if(PEAR::isError($res)) return $res;
        }else{
            // gunid doesn't exists - do insert
            $tmpid = uniqid('');
            $parid = $this->getObjId(
                $this->getSessLogin($sessid), $this->storId
            );
            if(PEAR::isError($parid)) return $parid;
            if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
                return $res;
            $oid = $this->addObj($tmpid , 'File', $parid);
            if(PEAR::isError($oid)) return $oid;
            $ac =&  StoredFile::insert(
                &$this, $oid, '', $mediaFileLP, $mdataFileLP, $gunid
            );
            if(PEAR::isError($ac)){
                $res = $this->removeObj($oid);
                return $ac;
            }
            $res = $this->renameFile($oid, $ac->gunid, $sessid);
            if(PEAR::isError($res)) return $res;
        }
        return $ac->gunid;
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
     *  @param mdataFileLP string, local path to metadata XML file
     *  @return boolean or PEAR::error
     */
    function updateAudioClipMetadata($sessid, $gunid, $mdataFileLP)
    {
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->replaceMetaData($mdataFileLP);
    }
    /**
     *  Make access to audio clip
     *
     *  @param sessid string
     *  @param gunid string
     *  @return string - access symlink path or PEAR::error
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
     *  @param tmpLink string
     *  @return boolean or PEAR::error
     */
    function releaseRawAudioData($sessid, $tmpLink)
    {
        $ac =& StoredFile::recallFromLink(&$this, $tmpLink, $sessid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->releaseRawMediaData($sessid);
    }
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
}
?>