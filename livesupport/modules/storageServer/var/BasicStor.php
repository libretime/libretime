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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/BasicStor.php,v $

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

require_once "../../../alib/var/alib.php";
require_once "StoredFile.php";
require_once "Transport.php";

/**
 *  BasicStor class
 *
 *  Core of LiveSupport file storage module
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.1 $
 *  @see Alib
 */
class BasicStor extends Alib{
    var $filesTable;
    var $mdataTable;
    var $accessTable;
    var $storageDir;
    var $bufferDir;
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
    function BasicStor(&$dbc, $config)
    {
        parent::Alib(&$dbc, $config);
        $this->config = $config;
        $this->filesTable = $config['tblNamePrefix'].'files';
        $this->mdataTable = $config['tblNamePrefix'].'mdata';
        $this->accessTable= $config['tblNamePrefix'].'access';
        $this->storageDir = $config['storageDir'];
        $this->bufferDir  = $config['bufferDir'];
        $this->transDir  = $config['transDir'];
        $this->accessDir  = $config['accessDir'];
        $this->dbc->setErrorHandling(PEAR_ERROR_RETURN);
        $this->rootId = $this->getRootNode();
        $this->storId = $this->wd =
            $this->getObjId('StorageRoot', $this->rootId);
        $this->dbc->setErrorHandling();
    }

    /**
     *  Create new folder
     *
     *  @param parid int, parent id
     *  @param folderName string, name for new folder
     *  @return id of new folder
     *  @exception PEAR::error
     */
    function bsCreateFolder($parid, $folderName)
    {
        return $this->addObj($folderName , 'Folder', $parid);
    }

    /**
     *  Store new file in the storage
     *
     *  @param parid int, parent id
     *  @param fileName string, name for new file
     *  @param mediaFileLP string, local path of media file
     *  @param mdataFileLP string, local path of metadata file
     *  @param gunid string, global unique id OPTIONAL
     *  @return int
     *  @exception PEAR::error
     */
    function bsPutFile($parid, $fileName,
        $mediaFileLP, $mdataFileLP, $gunid=NULL)
    {
        $name   = "$fileName";
        $id = $this->addObj($name , 'File', $parid);
        $ac =&  StoredFile::insert(
            &$this, $id, $name, $mediaFileLP, $mdataFileLP, 'file', $gunid
        );
        if(PEAR::isError($ac)) return $ac;
        return $id;
    }

    /**
     *  Analyze media file for internal metadata information
     *
     *  @param id int, virt.file's local id
     *  @return array
     */
    function bsAnalyzeFile($id)
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        $ia = $ac->analyzeMediaFile();
        return $ia;
    }

    /**
     *  Rename file
     *
     *  @param id int, virt.file's local id
     *  @param newName string
     *  @return boolean or PEAR::error
     */
    function bsRenameFile($id, $newName)
    {
        $parid = $this->getParent($id);
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)){
            // catch nonerror exception:
            if($ac->getCode() != GBERR_FOBJNEX) return $ac;
        }
        $res = $ac->rename($newName);
        if(PEAR::isError($res)) return $res;
        return $this->renameObj($id, $newName);
    }

    /**
     *  Move file
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @return boolean or PEAR::error
     */
    function bsMoveFile($id, $did)
    {
        if($this->getObjType($did) !== 'Folder')
            return PEAR::raiseError(
                'BasicStor::moveFile: destination is not folder', GBERR_WRTYPE
            );
        $this->_relocateSubtree($id, $did);
    }

    /**
     *  Copy file
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @return boolean or PEAR::error
     */
    function bsCopyFile($id, $did)
    {
        if($this->getObjType($did)!=='Folder')
            return PEAR::raiseError(
                'GreenBox::copyFile: destination is not folder', GBERR_WRTYPE
            );
        return $this->_copySubtree($id, $did);
    }

    /**
     *  Delete file
     *
     *  @param id int, virt.file's local id
     *  @return true or PEAR::error
     */
    function bsDeleteFile($id)
    {
        $parid = $this->getParent($id);
        $res = $this->removeObj($id);
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /* ----------------------------------------------------- put, access etc. */
    /**
     *  Check validity of asscess/put token
     *
     *  @param token string, access/put token
     *  @param type string 'put'|'access'|'download'
     *  @return boolean
     */
    function bsCheckToken($token, $type='put')
    {
        $cnt = $this->dbc->getOne("
            SELECT count(token) FROM {$this->accessTable}
            WHERE token='{$token}' AND type='$type'
        ");
        if(PEAR::isError($cnt)){ return FALSE; }
        return ($cnt == 1);
    }
    
    /**
     *  Create and return access link to media file
     *
     *  @param realFname string, local filepath to accessed file
     *  @param ext string, useful filename extension for accessed file
     *  @param gunid int, global unique id
     *  @param sessid string, session id
     *  @param type string 'access'|'download'
     *  @return array with: seekable filehandle, access token
     */
    function bsAccess($realFname, $ext, $gunid, $sessid='', $type='access')
    {
        $token  = StoredFile::_createGunid();
        $res = $this->dbc->query("
            INSERT INTO {$this->accessTable}
                (gunid, sessid, token, ext, type, ts)
            VALUES
                ('{$gunid}', '$sessid', '$token', '$ext', '$type', now())
        ");
        if(PEAR::isError($res)){ return $res; }
        $linkFname = "{$this->accessDir}/$token.$ext";

        if(!file_exists($realFname)){
            return PEAR::raiseError(
                "BasicStor::bsAccess: symlink create failed ($accLinkName)",
                GBERR_FILEIO);
        }
        if(! @symlink($realFname, $linkFname)){
            return PEAR::raiseError(
                "BasicStor::bsAccess: symlink create failed ($linkFname)",
                GBERR_FILEIO);
        }
        return array('fname'=>$linkFname, 'token'=>$token);
    }

    /**
     *  Release access link to media file
     *
     *  @param token string, access token
     *  @param type string 'access'|'download'
     *  @return boolean
     */
    function bsRelease($token, $type='access')
    {
        if(!$this->bsCheckToken($token, $type)){
            return PEAR::raiseError(
             "BasicStor::bsRelease: invalid token ($token)"
            );
        }
        $ext = $this->dbc->getOne("
            SELECT ext FROM {$this->accessTable}
            WHERE token='{$token}' AND type='$type'
        ");
        if(PEAR::isError($ext)){ return $ext; }
        $linkFname = "{$this->accessDir}/$token.$ext";
        $res = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE token='$token'
        ");
        if(PEAR::isError($res)){ return $res; }
        if(! @unlink($linkFname)){
            return PEAR::raiseError(
                "BasicStor::bsRelease: unlink failed ($linkFname)",
                GBERR_FILEIO);
        }
        return TRUE;
    }

    /**
     *  Create and return downloadable URL for file
     *
     *  @param id int, virt.file's local id
     *  @param part string, 'media'|'metadata'
     *  @return array with: downloadable URL, download token
     */
    function bsOpenDownload($id, $part='media')
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        $gunid      = $ac->gunid;
        switch($part){
        case"media":
            $fname  = $ac->_getRealRADFname();
            $ext    = $ac->_getExt();
            break;
        case"metadata":
            $md = $this->bsGetMdata($id);
            $fname  =  "{$this->bufferDir}/$gunid";
            $e  = FALSE;
            if(!$fh = fopen($fname, "w")){ $e = TRUE; }
            elseif(fwrite($fh, $md) === FALSE){ $e = TRUE; }
            if($e){
                return PEAR::raiseError(
                    "BasicStor::bsOpenDownload: can't write ($fname)",
                    GBERR_FILEIO);
            }
            fclose($fh);
            $ext    = "xml";
            break;
        }
        $sessid = '';
        $acc = $this->bsAccess($fname, $ext, $gunid, $sessid, 'download');
        $url = $this->getUrlPart()."access/".basename($acc['fname']);
        return array('url'=>$url, 'token'=>$acc['token']);
    }

    /**
     *  Discard downloadable URL
     *
     *  @param token string, download token
     *  @param part string, 'media'|'metadata'
     *  @return boolean
     */
    function bsCloseDownload($token, $part='media')
    {
        if($part == 'metadata'){
            $gunid = $this->dbc->getOne("
                SELECT gunid FROM {$this->accessTable}
                WHERE token='{$token}' AND type='download'
            ");
            if(PEAR::isError($gunid)){ return $gunid; }
            $fname  =  "{$this->bufferDir}/$gunid";
            @unlink($fname);
        }
        return $this->bsRelease($token, 'download');
    }

    /**
     *  Create writable URL for HTTP PUT method file insert
     *
     *  @param chsum string, md5sum of the file having been put
     *  @param gunid string, global unique id
     *  @return array with: writable URL, PUT token
     */
    function bsOpenPut($chsum, $gunid)
    {
        $sessid = '';
        $ext    = '';
        $token  = StoredFile::_createGunid();
        $res    = $this->dbc->query("
            INSERT INTO {$this->accessTable}
                (gunid, sessid, token, ext, chsum, type, ts)
            VALUES
                ('{$gunid}', '$sessid', '$token',
                    '$ext', '$chsum', 'put', now())
        ");
        if(PEAR::isError($res)){ return $res; }
        $fname = "{$this->accessDir}/$token";
        touch($fname);      // is it needed?
        $url = $this->getUrlPart()."xmlrpc/put.php?token=$token";
        return array('url'=>$url, 'token'=>$token);
    }

    /**
     *  Get file from writable URL and insert it to the storage
     *
     *  @param token string, PUT token
     *  @return string, local path of the file having been put
     */
    function bsClosePut($token)
    {
        if(!$this->bsCheckToken($token, 'put')){
            return PEAR::raiseError(
             'BasicStor::bsClosePut: invalid token ($token)'
            );
        }
        $chsum = $this->dbc->getOne("
            SELECT chsum FROM {$this->accessTable}
            WHERE token='{$token}'
        ");
        $fname = "{$this->accessDir}/$token";
        $md5sum = md5_file($fname);
        if($chsum != $md5sum){
            return PEAR::raiseError(
             'BasicStor::bsClosePut: md5sum does not match (token=$token)'
            );
        }
        $res = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE token='$token'
        ");
        if(PEAR::isError($res)){ return $res; }
        return $fname;
    }

    /**
     *  Return starting part of storageServer URL
     *
     *  @return string, url
     */
    function getUrlPart()
    {
        $host  = $this->config['storageUrlHost'];
        $port  = $this->config['storageUrlPort'];
        $path  = $this->config['storageUrlPath'];
        return "http://$host:$port$path/";
    }
    
    /* ---------------------------------------------- replicas, versions etc. */
    /**
     *  Create replica.<br>
     *  <b>TODO: NOT FINISHED</b>
     *
     *  @param id int, virt.file's local id
     *  @param did int, destination folder local id
     *  @param replicaName string, name of new replica
     *  @return int, local id of new object
     */
    function bsCreateReplica($id, $did, $replicaName)
    {
        return PEAR::raiseError(
            'GreenBox::createVersion: not implemented', GBERR_NOTIMPL
        );
        // ---
        if($this->getObjType($did)!=='Folder')
            return PEAR::raiseError(
                'GreenBox::createReplica: dest is not folder', GBERR_WRTYPE
            );
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
     *  @return int, local id of new object
     */
    function bsCreateVersion($id, $did, $versionLabel)
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
     *  @return boolean or PEAR::error
     */
    function bsUpdateMetadata($id, $mdataFile)
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        return $ac->updateMetaData($mdataFile);
    }
    
    /**
     *  Update object namespace and value of one metadata record
     *
     *  @param id int, virt.file's local id
     *  @param mdid int, metadata record id
     *  @param object string, object value, e.g. title string
     *  @param objns string, object namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @return boolean or PEAR::error
     *  @see MetaData
     */
    function bsUpdateMetadataRecord($id, $mdid, $object, $objns='_L')
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        return $ac->updateMetaDataRecord($mdid, $object, $objns);
    }

    /**
     *  Add single metadata record.<br>
     *  <b>TODO: NOT FINISHED</b><br>
     *  Params could be changed!
     *
     *  @param id int, virt.file's local id
     *  @param propertyName string
     *  @param propertyValue string
     *  @return boolean or PEAR::error
     *  @see MetaData
     */
    function bsAddMetaDataRecord($id, $propertyName, $propertyValue)
    {
        return PEAR::raiseError(
            'GreenBox::addMetaDataRecord: not implemented', GBERR_NOTIMPL
        );
    }

    /**
     *  Get metadata XML tree as string
     *
     *  @param id int, virt.file's local id
     *  @return string or PEAR::error
     */
    function bsGetMdata($id)
    {
        $ac =& StoredFile::recall(&$this, $id);
        if(PEAR::isError($ac)) return $ac;
        return $ac->getMetaData();
    }

    /**
     *  Search in local metadata database.<br>
     *  <b>TODO: NOT FINISHED</b><br>
     *  It will support structured queries -  array of mode and query parts.
     *  Mode is &quot;match all&quot; or &quot;match any&quot;.
     *  Query parts is array of [fieldname, operator, value] entities.
     *
     *  @param searchData string, search query -
     *      only one SQL LIKE term supported now.
     *      It will be searched in all literal object values
     *      in metadata database
     *  @return array of gunid strings
     */
    function bsLocalSearch($searchData)
    {
        $ftsrch = $searchData;
        $res = $this->dbc->getCol("SELECT md.gunid as gunid
            FROM {$this->filesTable} f, {$this->mdataTable} md
            WHERE f.gunid=md.gunid AND md.objns='_L' AND
                md.object like '%$ftsrch%'
            GROUP BY md.gunid
        ");
        if(!is_array($res)) $res = array();
        return $res;
    }

    /* --------------------------------------------------------- info methods */

    /**
     *  List files in folder
     *
     *  @param id int, local id of folder
     *  @return array
     */
    function bsListFolder($id)
    {
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
        }
        return $nid;
    }
    



    /* -------------------------------------------- remote repository methods */

    /**
     *  Upload file to remote repository
     *
     *  @param id int, virt.file's local id
     *  @param gunid string, global id
     *  @param sessid string, session id
     *  @return string - transfer id or PEAR::error
     */
    function uploadFile($id, $gunid, $sessid='')
    {
        $res = $this->prepareForTransport($id, $gunid, $sessid);
        if(PEAR::isError($res)) return $res;
        list($mediaFile, $mdataFile, $gunid) = $res;
        $tr =& new Transport(&$this->dbc, $this->config);
        $res = $tr->uploadOpen($mediaFile, 'media', $sessid, $gunid);
        if(PEAR::isError($res)) return $res;
        $res2 = $tr->uploadOpen($mdataFile, 'metadata', $sessid, $gunid);
        if(PEAR::isError($res2)) return $res2;
        $res3 = $tr->getTransportStatus($res);
        $res4 = $tr->getTransportStatus($res2);
#        return $res;
        return array($res, $res2, $res3, $res4);
    }

    /**
     *  Download file from remote repository
     *
     *  @param gunid int, global unique id
     *  @param sessid string, session id
     *  @return string - transfer id or PEAR::error
     */
    function downloadFile($gunid, $sessid='')
    {
        $tr =& new Transport(&$this->dbc, $this->config);
        // get home dir if needed
        $res = $tr->downloadOpen($sessid, 'media', $gunid,
            $this->getSessUserId($sessid)
        );
        if(PEAR::isError($res)) return $res;
        $res2 = $tr->downloadOpen($sessid, 'metadata', $gunid,
            $this->getSessUserId($sessid)
        );
        if(PEAR::isError($res)) return $res;
        $res3 = $tr->getTransportStatus($res);
        $res4 = $tr->getTransportStatus($res2);
#        return $res;
        return array($res, $res2, $res3, $res4);
    }
    


    /**
     *  Method for handling interupted transports via cron 
     *
     */
     function cronJob()
     {
        $tr =& new Transport(&$this->dbc, $this->config);
        $ru = $tr->uploadCron();
        $rd = $tr->downloadCron(&$this);
        return array($ru, $rd);
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
     *  Prepare symlink to media file and create metadata file for transport
     *
     *  @param id
     *  @param gunid
     *  @param sessid
     *  @return array
     */
    function prepareForTransport($id, $gunid, $sessid='')
    {
        if(!$gunid) $gunid = $this->_gunidFromId($id);
        else $id = $this->_idFromGunid($gunid);
        $ac =& StoredFile::recallByGunid(&$this, $gunid);
        if(PEAR::isError($ac)) return $ac;
        $mediaTarget = $ac->_getRealRADFname();
        $mediaFile = "$gunid";
        $mdataFile = "$gunid.xml";
        @symlink($mediaTarget, $this->transDir."/$mediaFile");
        $mdata = $this->getMdata($id, $sessid);
        if(PEAR::isError($mdata)) return $mdata;
        if(!($fh = fopen($this->transDir."/$mdataFile", 'w'))) $res=FALSE;
        else{
            $res = fwrite($fh, $mdata);
            fclose($fh);
        }
        if($res === FALSE) return PEAR::raiseError(
            "GreenBox::prepareForTransport:".
            " can't write metadata tmp file ($mdataFile)"
        );
        return array($mediaFile, $mdataFile, $gunid);
    }
    
    /**
     *  Insert transported file and metadata into storage.<br>
     *  TODO: cals methods from LocStor - it's not good
     *
     *  @param sessid string - session id
     *  @param file string - local path to filr
     *  @param type string - media|metadata|search
     *  @param gunid string - global unique id
     */
    function processTransported($sessid, $file, $type, $gunid='X')
    {
        switch($type){
            case 'media':
                if(!file_exists($file)) break;
                $res = $this->storeAudioClip($sessid, $gunid,
                    $file, '');
                if(PEAR::isError($res)) return $res;
                @unlink($file);
                break;
            case 'metadata':
            case 'mdata':
                if(!file_exists($file)) break;
                $res = $this->updateAudioClipMetadata($sessid, $gunid,
                    $file);
                if(PEAR::isError($res)){
                    // catch valid exception
                    if($res->getCode() == GBERR_FOBJNEX){
                        $res2 = $this->storeAudioClip($sessid, $gunid,
                            '', $file);
                        if(PEAR::isError($res2)) return $res2;
                    }else return $res;
                }
                @unlink($file);
                break;
            case 'search':
                //$this->localSearch($criteria);
                return PEAR::raiseError("processTranferred: search not implemented");
                break;
            default:
                return PEAR::raiseError("processTranferred: unknown type ($type)");
                break;
        }
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
        /*
        $srchid = md5($sessid.mtime());
        $fh = fopen($this->transDir."/$srchid", "w");
        fwrite($fh, serialize($searchData));
        fclose($fh); 
        $res = $tr->uploadOpen($srchid, 'search', $sessid, $gunid);
        if(PEAR::isError($res)) return $res;
        return $res;
        */
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
        $exdir = '../../../storageServer/var/tests';
        $o[] = $this->addSubj('test1', 'a');
        $o[] = $this->addSubj('test2', 'a');
        $o[] = $this->addSubj('test3', 'a');
        $o[] = $this->addSubj('test4', 'a');

        $o[] = $t1hd = $this->getObjId('test1', $this->storId);
        $o[] = $t1d1 = $this->bsCreateFolder($t1hd, 'test1_folder1');
        $o[] = $this->bsCreateFolder($t1hd, 'test1_folder2');
        $o[] = $this->bsCreateFolder($t1d1, 'test1_folder1_1');
        $o[] = $t1d12 = $this->bsCreateFolder($t1d1, 'test1_folder1_2');

        $o[] = $t2hd = $this->getObjId('test2', $this->storId);
        $o[] = $this->bsCreateFolder($t2hd, 'test2_folder1');

        $o[] = $this->bsPutFile($t1hd, 'file1.mp3', "$exdir/ex1.mp3", '');
        $o[] = $this->bsPutFile($t1d12, 'file2.wav', "$exdir/ex2.wav", '');
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
        $this->testData();
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
            { $this->test_log.="storageServer: OK\n"; return true; }
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
        $res = $this->addPerm($rootUid, '_all', $this->rootId, 'A');
        $fid = $this->bsCreateFolder($this->storId, 'root');
    }
    /**
     *  install - create tables
     *
     *  file states:
     *  <ul>
     *      <li>empty</li>
     *      <li>incomplete</li>
     *      <li>ready</li>
     *      <li>edited</li>
     *  </ul>
     */
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->filesTable} (
            id int not null,
            gunid char(32) not null,                    -- global unique ID
            name varchar(255) not null default'',       -- human file id ;)
            type varchar(255) not null default'',       -- mime type
            state varchar(128) not null default'empty', -- file state
            currentlyAccessing int not null default 0   -- access counter
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
            gunid char(32) not null default'',
            sessid char(32) not null default'',
            token char(32) not null default'',
            chsum char(32) not null default'',
            ext varchar(128) not null default'',
            type varchar(20) not null default'',
            ts timestamp
        )");
        $this->dbc->query("CREATE INDEX {$this->accessTable}_token_idx
            ON {$this->accessTable} (token)");
        $this->dbc->query("CREATE INDEX {$this->accessTable}_gunid_idx
            ON {$this->accessTable} (gunid)");
        if(!file_exists($this->bufferDir)){
            mkdir($this->bufferDir, 02775);
            chmod($this->bufferDir, 02775); // may be obsolete
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
                    if(substr($ee, 0, 1)!=='.')
                        unlink("{$this->storageDir}/$entry/$ee");
                }
                $dd->close();
                rmdir("{$this->storageDir}/$entry");
            }
        }
        if(is_object($d)) $d->close();
        if(file_exists($this->bufferDir)){
            $d = dir($this->bufferDir);
            while (false !== ($entry = $d->read())) if(substr($entry,0,1)!='.')
                { unlink("{$this->bufferDir}/$entry"); }
            $d->close();
            rmdir($this->bufferDir);
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