<?
/**
*   LocStor class
*
*   Livesupport local storage interface
*/
require_once '../GreenBox.php';

class LocStor extends GreenBox{
    /*
    * 
    *
    * @param gunid string
    * @param sessid string
    * @return boolean
    */
    function existsAudioClip($sessid, $gunid)
    {
        $ac =& StoredFile::recall(&$this, '', $gunid);
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
    function storeAudioClip($sessid, $gunid, $mediaFileLP, $mdataFileLP)
    {
        // test if specified gunid exists:
        $ac =& StoredFile::recall(&$this, '', $gunid);
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
                &$this, $oid, '', $mediaFileLP, $mdataFileLP
            );
            if(PEAR::isError($ac)) return $ac;
//          $this->debugLog("parid=$parid, gunid={$ac->gunid},
//                mediaFileLP=$mediaFileLP");
            $res = $this->renameFile($oid, $ac->gunid, $sessid);
            if(PEAR::isError($res)) return $res;
        }
        return $ac->gunid;
    }
    function deleteAudioClip($sessid, $gunid)
    {
        $ac =& StoredFile::recall(&$this, '', $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $res = $this->deleteFile($ac->getId(), $sessid);
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
    function updateAudioClipMetadata($sessid, $gunid, $mdataFileLP)
    {
        $ac =& StoredFile::recall(&$this, '', $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('write', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->replaceMetaData($mdataFileLP);
    }
    function accessRawAudioData($sessid, $gunid)
    {
        $ac =& StoredFile::recall(&$this, '', $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->accessRawMediaData($sessid);
    }
    function releaseRawAudioData($sessid, $tmpLink)
    {
        $ac =& StoredFile::recallFromLink(&$this, $tmpLink, $sessid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        return $ac->releaseRawMediaData($sessid);
    }
    function searchMetadata($sessid, $criteria)
    {
        $res = $this->localSearch($criteria, $sessid);
        return $res;
    }
    function getAudioClip($sessid, $gunid)
    {
        $ac =& StoredFile::recall(&$this, '', $gunid);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $ac->getId(), $sessid)) !== TRUE)
            return $res;
        $md = $this->getMdata($ac->getId(), $sessid);
        if(PEAR::isError($md)) return $md;
        return $md;
    }
}
?>