<?
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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/GreenBox.php,v $

------------------------------------------------------------------------------*/
define('GBERR_DENY', 40);
define('GBERR_FILEIO', 41);
define('GBERR_FILENEX', 42);
define('GBERR_FOBJNEX', 43);
define('GBERR_WRTYPE', 44);
define('GBERR_NONE', 45);
define('GBERR_AOBJNEX', 46);
define('GBERR_NOTF', 47);

define('GBERR_NOTIMPL', 50);

require_once '../../../alib/var/alib.php';
require_once '../StoredFile.php';

/**
 *  GreenBox class
 *
 *  LiveSupport file storage module
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.1 $
 *  @see Alib
 */
class GreenBox extends Alib{
    var $filesTable;
    var $mdataTable;
    var $accessTable;
    var $storageDir;
    var $accessDir;
    var $rootId;
    var $storId;
    var $doDebug = true;
    /**
     *  Constructor
     *
     *  @param dbc PEAR::db abstract class reference
     *  @param config config array from conf.php
     *  @return class instance
     */
    function GreenBox(&$dbc, $config)
    {
        parent::Alib(&$dbc, $config);
        $this->filesTable = $config['tblNamePrefix'].'files';
        $this->mdataTable = $config['tblNamePrefix'].'mdata';
        $this->accessTable= $config['tblNamePrefix'].'access';
        $this->storageDir = $config['storageDir'];
        $this->accessDir  = $config['accessDir'];
        $this->dbc->setErrorHandling(PEAR_ERROR_RETURN);
        $this->rootId = $this->getRootNode();
        $this->storId = $this->wd =
            $this->getObjId('StorageRoot', $this->rootId);
        $this->dbc->setErrorHandling();
    }

    
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
        return $this->addObj($folderName , 'Folder', $parid);
    }

    /**
     *  Store new file in the storage
     *
     *  @param parid int, parent id
     *  @param fileName string, name for new file
     *  @param mediaFileLP string, local path of media file
     *  @param mdataFileLP string, local path of metadata file
     *  @param sessid string, session id
     *  @return int
     *  @exception PEAR::error
     */
    function putFile($parid, $fileName,
         $mediaFileLP, $mdataFileLP, $sessid='') 
    {
        if(($res = $this->_authorize('write', $parid, $sessid)) !== TRUE)
            return $res;
        $name   = "$fileName";
        $id = $this->addObj($name , 'File', $parid);
        $ac =&  StoredFile::insert(
            &$this, $id, $name, $mediaFileLP, $mdataFileLP
        );
        if(PEAR::isError($ac)) return $ac;
        return $id;
    }

    /**
     *  Return raw media file.<br>
     *  <b>will be probably removed from API</b><br>
     *  see access() method
     *  
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return file
     */
    function getFile($id, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::getFile: probably obsolete API function');
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            $fname = $ac->accessRawMediaData();
#            readfile($fname);
            return join('', file($fname));
            $fname = $ac->releaseRawMediaData();
        }
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
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
#            echo"<pre>\n"; print_r($ac); exit;
            $ia = $ac->analyzeMediaFile();
            return $ia;
        }
    }
    
    /**
     *  Create and return access link to media file
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return filename as string or PEAR::error
     */
    function access($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            $fname = $ac->accessRawMediaData($sessid);
            return $fname;
        }
    }

    /**
     *  Release access link to media file
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function release($id, $sessid='')
    {
        if(($res = $this->_authorize('read', $id, $sessid)) !== TRUE)
            return $res;
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            $res = $ac->releaseRawMediaData($sessid);
            return $res;
        }
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
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            $res = $ac->rename($newName);
            if(PEAR::isError($res)) return $res;
            return $this->renameObj($id, $newName);
        }
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
        if($this->getObjType($did) !== 'Folder')
            return PEAR::raiseError(
                'GreenBox::moveFile: destination is not folder', GBERR_WRTYPE
            );
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        $this->_relocateSubtree($id, $did);
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
        if($this->getObjType($did)!=='Folder')
            return PEAR::raiseError(
                'GreenBox::copyFile: destination is not folder', GBERR_WRTYPE
            );
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        return $this->_copySubtree($id, $did);
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
        $res = $this->removeObj($id);
        if(PEAR::isError($res)) return $res;
        return TRUE;
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
        return PEAR::raiseError(
            'GreenBox::createVersion: not implemented', GBERR_NOTIMPL
        );
        // ---
        if($this->getObjType($did)!=='Folder')
            return PEAR::raiseError(
                'GreenBox::createReplica: dest is not folder', GBERR_WRTYPE
            );
        if(($res = $this->_authorize(
            array('read', 'write'), array($id, $did), $sessid
        )) !== TRUE) return $res;
        if($replicaName=='') $replicaName = $this->getObjName($id);
        while(($exid = $this->getObjId($replicaName, $did))<>'')
            { $replicaName.='_R'; }
        $rid = $this->addObj($replicaName , 'Replica', $did, 0, $id);
        if(PEAR::isError($rid)) return $rid;
#        $this->addMdata($this->_pathFromId($rid), 'isReplOf', $id, $sessid);
        return $rid;
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
        return PEAR::raiseError(
            'GreenBox::createVersion: not implemented', GBERR_NOTIMPL
        );
    }


    /* ------------------------------------------------------------- metadata */

    /**
     *  Update metadata tree
     *
     *  @param id int, virt.file's local id
     *  @param mdataFile string, local path of metadata XML file
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     */
    function updateMetadata($id, $mdataFile, $sessid='')
    {
        if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
            return $res;
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            return $ac->updateMetaData($mdataFile);
        }
    }
    
    /**
     *  Update object namespace and value of one metadata record
     *
     *  @param id int, virt.file's local id
     *  @param mdid int, metadata record id
     *  @param object string, object value, e.g. title string
     *  @param objns string, object namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     *  @see MetaData
     */
    function updateMetadataRecord($id, $mdid, $object, $objns='_L', $sessid='')
    {
        if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
            return $res;
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            return $ac->updateMetaDataRecord($mdid, $object, $objns);
        }
    }

    /**
     *  Add single metadata record.<br>
     *  <b>TODO: NOT FINISHED</b><br>
     *  Params could be changed!
     *
     *  @param id int, virt.file's local id
     *  @param propertyName string
     *  @param propertyValue string
     *  @param sessid string, session id
     *  @return boolean or PEAR::error
     *  @see MetaData
     */
    function addMetaDataRecord($id, $propertyName,
        $propertyValue, $sessid='')
    {
        //if(($res = $this->_authorize('write', $id, $sessid)) !== TRUE)
        //    return $res;
        return PEAR::raiseError(
            'GreenBox::addMetaDataRecord: not implemented', GBERR_NOTIMPL
        );
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
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        else{
            return $ac->getMetaData();
        }
    }

    /**
     *  Search in local metadata database.<br>
     *  <b>TODO: NOT FINISHED</b><br>
     *  Should support structured queries, e.g.:<br>
     *  XML file with the structure as metadata, but
     *  with SQL LIKE terms instead of metadata values.<br>
     *  Some standard query format would be better,
     *  but I've not found it yet.
     *
     *  @param searchData string, search query -
     *      only one SQL LIKE term supported now.
     *      It will be searched in all literal object values
     *      in metadata database
     *  @param sessid string, session id
     *  @return array of gunid strings
     */
    function localSearch($searchData, $sessid='')
    {
        $ftsrch = $searchData;
        $res = $this->dbc->getAll("SELECT md.gunid as gunid
            FROM {$this->filesTable} f, {$this->mdataTable} md
            WHERE f.gunid=md.gunid AND md.objns='_L' AND
                md.object like '$ftsrch'
            GROUP BY md.gunid
        ");
        if(!is_array($res)) $res = array();
/*
        if(!(count($res)>0))
            return PEAR::raiseError(
                'GreenBox::localSearch: no items found', GBERR_NONE
            );
*/
        return $res;
    }

    /* -------------------------------------------- remote repository methods */

    /**
     *  Upload file to remote repository
     *
     *  @param id int, virt.file's local id
     *  @param sessid string, session id
     *  @return string - transfer id or PEAR::error
     */
    function uploadFile($id, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::uploadFile: not implemented', GBERR_NOTIMPL
        );
    }

    /**
     *  Download file from remote repository
     *
     *  @param id int, virt.file's local id
     *  @param parid int, destination folder local id
     *  @param sessid string, session id
     *  @return string - transfer id or PEAR::error
     */
    function downloadFile($id, $parid, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::downloadFile: not implemented', GBERR_NOTIMPL
        );
    }

    /**
     *  Get status of asynchronous transfer
     *
     *  @param transferId int, id of asynchronous transfer
     *      returned by uploadFile or downloadFile methods
     *  @param sessid string, session id
     *  @return string or PEAR::error
     */
    function getTransferStatus($transferId, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::getTransferStatus: not implemented', GBERR_NOTIMPL
        );
    }

    /**
     *  Search in central metadata database
     *
     *  @param searchData string, search query - see localSearch method
     *  @param sessid string, session id
     *  @return string - job id or PEAR::error
     */
    function globalSearch($searchData, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::globalSearch: not implemented', GBERR_NOTIMPL
        );
    }

    /**
     *  Get results from asynchronous search
     *
     *  @param transferId int, transfer id returned by
     *  @param sessid string, session id
     *  @return array with results or PEAR::error
     */
    function getSearchResults($transferId, $sessid='')
    {
        return PEAR::raiseError(
            'GreenBox::getSearchResults: not implemented', GBERR_NOTIMPL
        );
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
        if($this->getObjType($id)!=='Folder')
            return PEAR::raiseError(
                'GreenBox::listFolder: not a folder', GBERR_NOTF
            );
        $a = $this->getDir($id, 'id, name, type, param as target', 'name');
        return $a;
    }
    
    /**
     *  List files in folder
     *
     *  @param id int, local id of object
     *  @param relPath string, relative path
     *  @return array
     */
    function getObjIdFromRelPath($id, $relPath='.')
    {
#        $a = $this->getDir($id, 'id, name, type, param as target', 'name');
        $a = split('/', $relPath);
        if($this->getObjType($id)!=='Folder') $nid = $this->getparent($id);
        else $nid = $id;
        foreach($a as $i=>$item){
            switch($item){
                case".":
                    break;
                case"..":
                    $nid = $this->getparent($nid);
                    break;
                case"":
                    break;
                default:
                    $nid = $this->getObjId($item, $nid);
            }
#            $a[$i] = array('o'=>$item, 'n'=>($nid==null ? 'NULL' : $nid));
        }
#        return $a;
        return $nid;
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
        $acfa = $this->dbc->getAll("SELECT * FROM {$this->accessTable}
            WHERE sessid='$sessid'");
        if(PEAR::isError($acfa)) return $acfa;
        foreach($acfa as $i=>$acf){
            $ac =& StoredFile::recallFromLink(&$this, $acf['tmplink'], $sessid);
            $ac->releaseRawMediaData($sessid);
        }
        parent::logout($sessid);
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
        if($this->getObjType($id)==='File'){
            $ac =& StoredFile::recall(&$this, $id);
            if(PEAR::isError($ac)){ return $ac; }
            $ac2 =& StoredFile::copyOf(&$ac, $nid);
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
        return PEAR::raiseError("GreenBox::$method: access denied", GBERR_DENY);
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
            "SELECT id FROM {$this->filesTable} WHERE gunid='$gunid'"
        );
    }

    /* =============================================== test and debug methods */
    /**
     *  dump
     *
     */
    function dump($id='', $indch='    ', $ind='', $format='{name}')
    {
        if($id=='') $id = $this->storId;
        return parent::dump($id, $indch, $ind, $format);
    }

    /**
     *
     *
     */
    function dumpDir($id='', $format='$o["name"]')
    {
        if($id=='') $id = $this->storId;
        $arr = $this->getDir($id, 'id,name');
//        if($this->doDebug){ $this->debug($arr); exit; }
        $arr = array_map(create_function('$o', 'return "'.$format.'";'), $arr);
        return join('', $arr);
    }

    /**
     *
     *
     */
    function debug($va)
    {
        echo"<pre>\n"; print_r($va); #exit;
    }

    /**
     *  deleteData
     *
     *  @return void
     */
    function deleteData()
    {
//        $this->dbc->query("DELETE FROM {$this->filesTable}");
        $ids = $this->dbc->getAll("SELECT id FROM {$this->filesTable}");
        if(is_array($ids)) foreach($ids as $i=>$item){
            $this->removeObj($item['id']);
        }
        parent::deleteData();
        $this->initData();
    }
    /**
     *  testData
     *
     */
    function testData($d='')
    {
        $exdir = dirname(getcwd()).'/tests';
        $s = $this->sessid;
        $o[] = $this->addSubj('test1', 'a');
        $o[] = $this->addSubj('test2', 'a');
        $o[] = $this->addSubj('test3', 'a');
        $o[] = $this->addSubj('test4', 'a');

        $o[] = $t1hd = $this->getObjId('test1', $this->storId);
        $o[] = $t1d1 = $this->createFolder($t1hd, 'test1_folder1', $s);
        $o[] = $this->createFolder($t1hd, 'test1_folder2', $s);
        $o[] = $this->createFolder($t1d1, 'test1_folder1_1', $s);
        $o[] = $t1d12 = $this->createFolder($t1d1, 'test1_folder1_2', $s);

        $o[] = $t2hd = $this->getObjId('test2', $this->storId);
        $o[] = $this->createFolder($t2hd, 'test2_folder1', $s);

        $o[] = $this->putFile($t1hd, 'file1.mp3', "$exdir/ex1.mp3", '', $s);
        $o[] = $this->putFile($t1d12 , 'file2.wav', "$exdir/ex2.wav", '', $s);
/*
*/
        $this->tdata['storage'] = $o;
    }

    /**
     *  test
     *
     */
    function test()
    {
//        if(PEAR::isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->login('root', $this->config['tmpRootPass']);
        $this->testData();
        $this->logout($this->sessid);
        $this->test_correct = "    StorageRoot
        root
        test1
            test1_folder1
                test1_folder1_1
                test1_folder1_2
                    file2.wav
            test1_folder2
            file1.mp3
        test2
            test2_folder1
        test3
        test4
";
        $this->test_dump = $this->dumpTree($this->storId);
        if($this->test_dump==$this->test_correct)
            { $this->test_log.="Storage: OK\n"; return true; }
        else PEAR::raiseError('GreenBox::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n.{$this->test_correct}.\n".
            "dump:\n.{$this->test_dump}.\n</pre>\n");
    }

    /**
     *  initData - initialize
     *
     */
    function initData()
    {
        $this->rootId = $this->getRootNode();
        $this->storId = $this->wd =
            $this->addObj('StorageRoot', 'Folder', $this->rootId);
        $rootUid = parent::addSubj('root', $this->config['tmpRootPass']);
        $this->login('root', $this->config['tmpRootPass']);
        $res = $this->addPerm($rootUid, '_all', $this->rootId, 'A');
        $fid = $this->createFolder($this->storId, 'root', $this->sessid);
#        $id = $this->dbc->nextId("{$this->mdataTable}_id_seq");
        $this->logout($this->sessid);
    }
    /**
     *  install - create tables
     *
     */
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->filesTable} (
            id int not null,
            gunid char(32) not null,
            name varchar(255) not null default'',
            type varchar(255) not null default'',
            currentlyAccessing int not null default 0
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->filesTable}_id_idx
            ON {$this->filesTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->filesTable}_gunid_idx
            ON {$this->filesTable} (gunid)");
        $this->dbc->query("CREATE INDEX {$this->filesTable}_name_idx
            ON {$this->filesTable} (name)");

        $this->dbc->createSequence("{$this->mdataTable}_id_seq");
        $this->dbc->query("CREATE TABLE {$this->mdataTable} (
            id int not null,
            gunid char(32),
            subjns varchar(255),             -- subject namespace shortcut/uri
            subject varchar(255) not null default '',
            predns varchar(255),             -- predicate namespace shortcut/uri
            predicate varchar(255) not null,
            predxml char(1) not null default 'T', -- Tag or Attribute
            objns varchar(255),              -- object namespace shortcut/uri
            object text
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->mdataTable}_id_idx
            ON {$this->mdataTable} (id)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_gunid_idx
            ON {$this->mdataTable} (gunid)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_subj_idx
            ON {$this->mdataTable} (subjns, subject)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_pred_idx
            ON {$this->mdataTable} (predns, predicate)");

        $this->dbc->query("CREATE TABLE {$this->accessTable} (
            gunid char(32) not null,
            sessid char(32) not null,
            tmpLink varchar(255) not null default'',
            ts timestamp
        )");
        $this->dbc->query("CREATE INDEX {$this->accessTable}_acc_idx
            ON {$this->accessTable} (tmpLink, sessid)");
        if(!file_exists("{$this->storageDir}/buffer")){
            mkdir("{$this->storageDir}/buffer", 0775);
        }
        $this->initData();
    }
    /**
     *  id  subjns  subject predns  predicate   objns   object
     *  y1  literal xmbf    NULL    namespace   literal http://www.sotf.org/xbmf
     *  x1  gunid   <gunid> xbmf    contributor NULL    NULL
     *  x2  mdid    x1      xbmf    role        literal Editor
     *
     *  predefined shortcuts:
     *      _L              = literal
     *      _G              = gunid (global id of media file)
     *      _I              = mdid (local id of metadata record)
     *      _nssshortcut    = namespace shortcut definition
     *      _blank          = blank node
     */

    /**
     *  uninstall
     *
     *  @return void
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->mdataTable}");
        $this->dbc->dropSequence("{$this->mdataTable}_id_seq");
        $this->dbc->query("DROP TABLE {$this->filesTable}");
        $this->dbc->query("DROP TABLE {$this->accessTable}");
        $d = dir($this->storageDir);
        while (is_object($d) && (false !== ($entry = $d->read()))){
            if(filetype("{$this->storageDir}/$entry")=='dir' &&
                    $entry!='CVS' && strlen($entry)==3)
            {
                $dd = dir("{$this->storageDir}/$entry");
                while (false !== ($ee = $dd->read())){
                    //if(substr($ee, -4)=='.mp3' || substr($ee, -4)=='.xml')
                    if(substr($ee, 0, 1)!=='.')
                        unlink("{$this->storageDir}/$entry/$ee");
                }
                $dd->close();
                rmdir("{$this->storageDir}/$entry");
            }
        }
        if(is_object($d)) $d->close();
        if(file_exists("{$this->storageDir}/buffer")){
            $d = dir("{$this->storageDir}/buffer");
            while (false !== ($entry = $d->read())) if(substr($entry,0,1)!='.')
                { unlink("{$this->storageDir}/buffer/$entry"); }
            $d->close();
            rmdir("{$this->storageDir}/buffer");
        }
        parent::uninstall();
    }

    /**
     *  Aux logging for debug
     *
     *  @param msg string - log message
     */
    function debugLog($msg)
    {
        $fp=fopen("{$this->storageDir}/log", "a") or die("Can't write to log\n");
        fputs($fp, date("H:i:s").">$msg<\n");
        fclose($fp);
    }
}
?>