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
    Version  : $Revision: 1.18 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/GreenBox.php,v $

------------------------------------------------------------------------------*/
require_once "BasicStor.php";

/**
 *  GreenBox class
 *
 *  LiveSupport file storage module
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.18 $
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
        return $this->bsCreateFolder($parid, $folderName , 'Folder', $parid);
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

    /* ----------------------------------------------------- put, access etc. */
    /**
     *  Create and return access link to media file
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return array with: seekable filehandle, access token
     */
    function access($id, $sessid='')
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        return $ac->accessRawMediaData($sessid);
    }

    /**
     *  Release access link to media file
     *
     *  @param sessid string, session id
     *  @param token string, access token
     *  @return boolean or PEAR::error
     */
    function release($token, $sessid='')
    {
        $ac =& StoredFile::recallByToken(&$this, $token);
        if(PEAR::isError($ac)) return $ac;
        return $ac->releaseRawMediaData($sessid, $token);
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
     *  @param mdata string, local path of metadata XML file
     *  @param mdataLoc string 'file'|'string'
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
        return $this->bsLocalSearch($criteria);
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
        foreach($listArr as $i=>$v){
            if($v['type'] == 'File'){
                $gunid = $this->_gunidFromId($v['id']);
                $listArr[$i]['type'] =
                    StoredFile::_getType($gunid);
                $listArr[$i]['gunid'] = $gunid;
            }
        }
        return $listArr;
    }
    
    /* ---------------------------------------------------- redefined methods */

    /**
     *  Logout and destroy session
     *
     *  @param sessid string
     *  @return true/err
     */
    function logout($sessid)
    {
        /* release all accessed files on logout - probably not useful
        $acfa = $this->dbc->getAll("SELECT * FROM {$this->accessTable}
            WHERE sessid='$sessid'");
        if(PEAR::isError($acfa)) return $acfa;
        foreach($acfa as $i=>$acf){
            $ac =& StoredFile::recallByToken(&$this, $acf['token']);
            $ac->releaseRawMediaData($sessid, $acf['token']);
        }
        */
        return parent::logout($sessid);
    }

    /**
     *  Add new user with home folder
     *
     *  @param login string
     *  @param pass string OPT
     *  @return int/err
     */
    function addSubj($login, $pass=NULL)
    {
        $uid = parent::addSubj($login, $pass);
        if(PEAR::isError($uid)) return $uid;
        $fid = $this->addObj($login , 'Folder', $this->storId);
        if(PEAR::isError($fid)) return $fid;
        $res = $this->addPerm($uid, '_all', $fid, 'A');
        if(PEAR::isError($res)) return $res;
        return $uid;
    }
    /**
     *  Remove user and his home folder
     *
     *  @param login string
     *  @param uid int OPT
     *  @return boolean/err
     */
    function removeSubj($login, $uid=NULL)
    {
        $res = parent::removeSubj($login, $pass);
        if(PEAR::isError($res)) return $res;
        $id = $this->getObjId($login, $this->storId);
        if(PEAR::isError($id)) return $id;
        $res = $this->removeObj($id);
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }  
    
    /**
     *  Get file's path in virtual filesystem
     *
     *  @param id int
     *  @return array
     */
    function getPath($id)
    {
        $pa =  parent::getPath($id, 'id, name, type'); array_shift($pa);
        return $pa;
    }

    /**
     *  Get object type by id.
     *  (RootNode, Folder, File, )
     *
     *  @param oid int, local object id
     *  @return string/err
     */
    function getObjType($oid)
    {
        $type = $this->getObjName($oid, 'type');
        if($type == 'File'){
            $type =
                StoredFile::_getType($this->_gunidFromId($oid));
        }
        return $type;
    }
    
    /* ==================================================== "private" methods */

    /**
     *  Copy virtual file.<br>
     *  Redefined from parent class.
     *
     *  @return id
     */
    function copyObj($id, $newParid, $after='')
    {
        $nid = parent::copyObj($id, $newParid, $after='');
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"File":
                $ac =& StoredFile::recall(&$this, $id);
                if(PEAR::isError($ac)){ return $ac; }
                $ac2 =& StoredFile::copyOf(&$ac, $nid);
                break;
            default:
        }
        return $nid;
    }
    /**
     *  Optionaly remove virtual file with the same name and add new one.<br>
     *  Redefined from parent class.
     *
     *  @return id
     */
    function addObj($name, $type, $parid=1, $aftid=NULL, $param='')
    {
        if(!is_null($exid = $this->getObjId($name, $parid)))
            { $this->removeObj($exid); }
        return parent::addObj($name, $type, $parid, $aftid, $param);
    }

    /**
     *  Remove virtual file.<br>
     *  Redefined from parent class.
     *
     *  @param id int, local id of removed object
     *  @return true or PEAR::error
     */
    function removeObj($id)
    {
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"File":
                $ac =& StoredFile::recall(&$this, $id);
                if(!PEAR::isError($ac)){
                    $ac->delete();
                }
                parent::removeObj($id);
                break;
            case"Folder":
                parent::removeObj($id);
                break;
            case"Replica":
                parent::removeObj($id);
                break;
            default:
        }
        return TRUE;
    }
    
    /**
     *  Return users's home folder local ID
     *
     *  @param sessid string, session ID
     *  @return local folder id
     */
    function _getHomeDirId($sessid)
    {
        $parid = $this->getObjId(
            $this->getSessLogin($sessid), $this->storId
        );
        return $parid;
    }
    
    /**
     *  Check authorization - auxiliary method
     *
     *  @param acts array of actions
     *  @param pars array of parameters - e.g. ids
     *  @param sessid string, session id
     *  @return true or PEAR::error
     */
    function _authorize($acts, $pars, $sessid='')
    {
        $userid = $this->getSessUserId($sessid);
        if(!is_array($pars)) $pars = array($pars);
        if(!is_array($acts)) $acts = array($acts);
        $perm = true;
        foreach($acts as $i=>$action){
            $res = $this->checkPerm($userid, $action, $pars[$i]);
            if(PEAR::isError($res)) return $res;
            $perm = $perm && $res;
        }
        if($perm) return TRUE;
        $adesc = "[".join(',',$acts)."]";
        return PEAR::raiseError("GreenBox::$adesc: access denied", GBERR_DENY);
    }

    /**
     *  Create fake session for downloaded files
     *
     *  @param userid user id
     *  @return string sessid
     */
    function _fakeSession($userid)
    {
        $sessid = $this->_createSessid();
        if(PEAR::isError($sessid)) return $sessid;
        $login = $this->getSubjName($userid);
        $r = $this->dbc->query("INSERT INTO {$this->sessTable}
                (sessid, userid, login, ts)
            VALUES
                ('$sessid', '$userid', '$login', now())");
        if(PEAR::isError($r)) return $r;
        return $sessid;
    }

    /**
     *  Get local id from global id
     *
     *  @param gunid string global id
     *  @return int local id
     */
    function _idFromGunid($gunid)
    {
        return $this->dbc->getOne(
            "SELECT id FROM {$this->filesTable} WHERE gunid=x'$gunid'::bigint"
        );
    }

    /**
     *  Get global id from local id
     *
     *  @param id int local id
     *  @return string global id
     */
    function _gunidFromId($id)
    {
        if(!is_numeric($id)) return NULL;
        $gunid = $this->dbc->getOne("
            SELECT to_hex(gunid)as gunid FROM {$this->filesTable}
            WHERE id='$id'
        ");
        if(is_null($gunid)) return NULL;
        return StoredFile::_normalizeGunid($gunid);
    }

}
?>