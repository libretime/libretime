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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/StoredFile.php,v $

------------------------------------------------------------------------------*/
require_once '../RawMediaData.php';
require_once '../MetaData.php';
require_once '../../../getid3/var/getid3.php';
 
/**
 *  StoredFile class
 *
 *  LiveSupport file storage support class.<br>
 *  Represents one virtual file in storage. Virtual file has up to two parts:
 *  <ul>
 *      <li>metada in database - represeted by MetaData class</li>
 *      <li>binary media data in real file
 *          - represented by RawMediaData class</li>
 *  </ul>
 *  @see GreenBox
 *  @see MetaData
 *  @see RawMediaData
 */
class StoredFile{
    /* ========================================================== constructor */
    /**
     *  Constructor, but shouldn't be externally called
     *
     *  @param gb reference to GreenBox object
     *  @param gunid string, optional, globally unique id of file
     *  @return this
     */
    function StoredFile(&$gb, $gunid=NULL)
    {
        $this->gb         =& $gb;
        $this->dbc        =& $gb->dbc;
        $this->filesTable =  $gb->filesTable;
        $this->accessTable= $gb->accessTable;
        $this->gunid      =  $gunid;
        if(is_null($this->gunid)) $this->gunid = $this->_createGunid();
        $this->resDir     =  $this->_getResDir($this->gunid);
        $this->accessDir  =  $this->gb->accessDir;
        $this->rmd        =& new RawMediaData($this->gunid, $this->resDir);
        $this->md         =& new MetaData(&$gb, $this->gunid);
        return $this->gunid;
    }
    /* ========= 'factory' methods - should be called to construct StoredFile */
    /**
     *   Create instace of StoreFile object and insert new file
     *
     *  @param gb reference to GreenBox object
     *  @param oid int, local object id in the tree
     *  @param name string, name of new file
     *  @param mediaFileLP string, local path to media file
     *  @param mdataFileLP string, local path to metadata XML file
     *  @return instace of StoredFile object
     */
    function insert(&$gb, $oid, $name, $mediaFileLP='', $mdataFileLP='')
    {
        $ac =& new StoredFile(&$gb);
        $ac->name = $name;
        $ac->id   = $oid;
        $ac->type = "unKnown";
        if($ac->name=='') $ac->name=$ac->gunid;
        $this->dbc->query("BEGIN");
        $res = $ac->dbc->query("INSERT INTO {$ac->filesTable}
                (id, name, gunid, type)
            VALUES
                ('$oid', '{$ac->name}', '{$ac->gunid}', '{$ac->type}')"
        );
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        if($mdataFileLP != ''){
            $res = $ac->md->insert($mdataFileLP);
            if(PEAR::isError($res)){
                $this->dbc->query("ROLLBACK"); return $res;
            }
        }
        if($mediaFileLP != ''){
            $res = $ac->rmd->insert($mediaFileLP);
            if(PEAR::isError($res)){
                $this->dbc->query("ROLLBACK"); return $res;
            }
            $mime = $ac->rmd->getMime();
//        return PEAR::raiseError("X1");
//        $gb->debugLog("gunid={$ac->gunid}, mime=$mime");
            if($mime !== FALSE){
                $res = $ac->dbc->query("UPDATE {$ac->filesTable}
                    SET type='$mime' WHERE id='$oid'");
                if(PEAR::isError($res)){
                    $ac->dbc->query("ROLLBACK"); return $res;
                }
            }
        }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        return $ac;
    }
    /**
     *  Create instace of StoreFile object and recall existing file.<br>
     *  Should be supplied oid XOR gunid - not both ;)
     *
     *  @param gb reference to GreenBox object
     *  @param oid int, optional, local object id in the tree
     *  @param gunid string, optional, global unique id of file
     *  @return instace of StoredFile object
     */
    function recall(&$gb, $oid='', $gunid='')
    {
        $cond = ($gunid=='' ? "id='$oid'" : "gunid='$gunid'" );
        $row = $gb->dbc->getRow("SELECT id, gunid, type, name
            FROM {$gb->filesTable} WHERE $cond");
        if(PEAR::isError($row)) return $row;
        if(is_null($row)){
            return PEAR::raiseError(
                "StoredFile::recall: fileobj not exist", GBERR_FOBJNEX);
        }
        $ac =& new StoredFile(&$gb, $row['gunid']);
        $ac->type = $row['type'];
        $ac->name = $row['name'];
        $ac->id   = $row['id'];
        return $ac;
    }
    /**
     *  Create instace of StoreFile object and recall existing file from tmpLink
     *
     *  @param gb reference to GreenBox object
     *  @param tmpLink string
     *  @param sessid string
     */
    function recallFromLink(&$gb, $tmpLink, $sessid)
    {
        $gunid = $gb->dbc->getOne("SELECT gunid FROM {$gb->accessTable}
            WHERE tmpLink='$tmpLink' AND sessid='$sessid'");
        if(PEAR::isError($gunid)) return $gunid;
        if(is_null($gunid)) return PEAR::raiseError(
            "StoredFile::recallFromLink: accessobj not exist", GBERR_AOBJNEX);
        return StoredFile::recall(&$gb, '', $gunid);
    }
    /**
     *  Create instace of StoreFile object and make copy of existing file
     *
     *  @param src reference to source object
     *  @param nid int, new local id
     */
    function copyOf(&$src, $nid)
    {
        $ac =& StoredFile::insert(&$src->gb, $nid, $src->name, $src->_getRealRADFname(), '');
        if(PEAR::isError($ac)) return $ac;
        $ac->md->replace($src->md->getMetaData(), 'xml');
        return $ac;
    }

    /* ======================================================= public methods */
    /**
     *  Replace existing file with new data
     *
     *  @param oid int, local id
     *  @param name string, name of file
     *  @param mediaFileLP string, local path to media file
     *  @param mdataFileLP string, local path to metadata XML file or XML string
     *  @param mdataLoc string 'file'|'string'
     */
    function replace($oid, $name, $mediaFileLP='', $mdataFileLP='',
        $mdataLoc='file')
    {
        $this->dbc->query("BEGIN");
        $res = $this->dbc->query("UPDATE {$this->filesTable}
            SET name='$name', type='{$this->type}' WHERE id='$oid'");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        if($mediaFileLP != ''){
            $res = $this->rmd->replace($mediaFileLP, $this->type);
            if(PEAR::isError($res)){
                $this->dbc->query("ROLLBACK"); return $res;
            }
            $mime = $this->rmd->getMime();
            if($mime !== FALSE){
                $res = $this->dbc->query("UPDATE {$this->filesTable}
                    SET type='$mime' WHERE id='$oid'");
                if(PEAR::isError($res)){
                    $this->dbc->query("ROLLBACK"); return $res;
                }
            }
        }
        if($mdataFileLP != ''){
            $res = $this->md->replace($mdataFileLP, $mdataLoc);
            if(PEAR::isError($res)){
                $this->dbc->query("ROLLBACK"); return $res;
            }
        }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)){
            $this->dbc->query("ROLLBACK"); return $res;
        }
        return TRUE;
    }
    /**
     *  Increase access counter, create access record,
     *  call access method of RawMediaData
     *
     *  @param sessid string
     */
    function accessRawMediaData($sessid)
    {
        $this->dbc->query("BEGIN");
        $res = $this->dbc->query("UPDATE {$this->filesTable}
            SET currentlyaccessing=currentlyaccessing+1
            WHERE gunid='{$this->gunid}'");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $accLinkName = $this->_getAccessFname($sessid, $this->_getExt());
        $res = $this->dbc->query("INSERT INTO {$this->accessTable}
                (gunid, sessid, tmplink, ts)
            VALUES
                ('{$this->gunid}', '$sessid', '$accLinkName', now())");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $acc = $this->rmd->access($accLinkName);
        if(PEAR::isError($acc)){ $this->dbc->query("ROLLBACK"); return $acc; }
        if($acc === FALSE){
             $this->dbc->query("ROLLBACK");
            return PEAR::raiseError(
             'StoredFile::accessRawMediaData: not exists'
            );
        }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        return $acc;
    }
    /**
     *  Decrease access couter, delete access record,
     *  call release method of RawMediaData
     *
     *  @param sessid string
     */
    function releaseRawMediaData($sessid)
    {
        $this->dbc->query("BEGIN");
        $res = $this->dbc->query("UPDATE {$this->filesTable}
            SET currentlyaccessing=currentlyaccessing-1
            WHERE gunid='{$this->gunid}' AND currentlyaccessing>0"
        );
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $ca = $this->dbc->getOne("SELECT currentlyaccessing
            FROM {$this->filesTable}
            WHERE gunid='{$this->gunid}'"
        );
        if(PEAR::isError($ca)){ $this->dbc->query("ROLLBACK"); return $ca; }
        $accLinkName = $this->_getAccessFname($sessid, $this->_getExt());
        $res = $this->dbc->query("DELETE FROM {$this->accessTable}
            WHERE gunid='{$this->gunid}' AND sessid='$sessid'
                AND tmplink='$accLinkName'");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        if(intval($ca)==0) return $this->rmd->release($accLinkName);
        return TRUE;
    }
    /**
     *  Replace metadata with new XML file
     *
     *  @param mdataFileLP string, local path to metadata XML file or XML string
     *  @param mdataLoc string 'file'|'string'
     */
    function replaceMetaData($mdataFileLP, $mdataLoc='file')
    {
        $this->dbc->query("BEGIN");
        $res = $this->md->replace($mdataFileLP, $mdataLoc);
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
    /**
     *  Update metadata with new XML file
     *
     *  @param mdataFileLP string, local path to metadata XML file or XML string
     *  @param mdataLoc string 'file'|'string'
     *  @return boolean or PEAR::error
     */
    function updateMetaData($mdataFileLP, $mdataLoc='file')
    {
        $this->dbc->query("BEGIN");
        $res = $this->md->update($mdataFileLP, $mdataLoc);
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
    /**
     *  Update object namespace and value of one metadata record
     *
     *  @param mdid int, metadata record id
     *  @param object string, object value, e.g. title string
     *  @param objns string, object namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @see MetaData
     *  @return boolean or PEAR::error
     */
    function updateMetaDataRecord($mdid, $object, $objns='_L')
    {
        return $this->md->updateRecord($mdid, $object, $objns='_L');
    }
    /**
     *  Get metadata as XML string
     *
     *  @return XML string
     *  @see MetaData
     */
    function getMetaData()
    {
        return $this->md->getMetaData();
    }
    /**
     *  Analyze file with getid3 module.<br>
     *  Obtain some metadata stored in media file.<br>
     *  This method should be used for prefilling metadata input form.
     *
     *  @return array
     *  @see MetaData
     */
    function analyzeMediaFile()
    {
        $ia = $this->rmd->analyze();
        return $ia;
    }
    /**
     *  Rename stored virtual file
     *
     *  @param newname string
     *  @return true or PEAR::error
     */
    function rename($newname)
    {
        $res = $this->dbc->query("UPDATE {$this->filesTable} SET name='$newname'
            WHERE gunid='{$this->gunid}'");
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
    /**
     *  Delete stored virtual file
     *
     *  @see RawMediaData
     *  @see MetaData
     */
    function delete()
    {
        $res = $this->rmd->delete();
        if(PEAR::isError($res)) return $res;
        $res = $this->md->delete();
        if(PEAR::isError($res)) return $res;
        $links = $this->dbc->getAll("SELECT tmplink FROM {$this->accessTable}
            WHERE gunid='{$this->gunid}'");
        if(is_array($links)) foreach($links as $i=>$item){
            @unlink($item['tmplink']);
        }
        $res = $this->dbc->query("DELETE FROM {$this->accessTable}
            WHERE gunid='{$this->gunid}'");
        if(PEAR::isError($res)) return $res;
        $res = $this->dbc->query("DELETE FROM {$this->filesTable}
            WHERE gunid='{$this->gunid}'");
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Returns true if virtual file is accessed.<br>
     *  Static or dynamic call is possible.
     *
     *  @param gunid string, optional (for static call), global unique id
     */
    function isAccessed($gunid=NULL)
    {
        if(is_null($gunid)) $gunid = $this->gunid;
        return (0 < $this->dbc->getOne("
            SELECT currentlyAccessing FROM {$this->filesTable}
            WHERE gunid='$gunid'
        "));
    }    
    /**
     *  Returns local id of virtual file
     *
     */
    function getId()
    {
        return $this->id;
    }    
    /**
     *  Returns true if raw media file exists
     *
     */
    function exists()
    {
        $indb = $this->dbc->getRow("SELECT gunid FROM {$this->filesTable}
            WHERE gunid='{$this->gunid}'");
        return (!is_null($indb) && $this->rmd->exists());
    }    
    
    /* ==================================================== "private" methods */
    /**
     *  Create new global unique id
     *
     */
    function _createGunid()
    {
        return md5(microtime().$_SERVER['SERVER_ADDR'].rand().
            "org.mdlf.livesupport");
    }
    /**
     *  Get local id from global id.
     *  Static or dynamic call is possible.
     *
     *  @param gunid string, optional (for static call),
     *      global unique id of file
     */
    function _idFromGunid($gunid=NULL)
    {
        if(is_null($gunid)) $gunid = $this->$gunid;
        $id = $this->dbc->getOne("SELECT id FROM {$this->filesTable}
            WHERE gunid='$gunid'");
        if(is_null($id)) return PEAR::raiseError(
            "StoredFile::_idFromGunid: no such global unique id ($gunid)"
        );
        return $id;
    }
    /**
     *  Return suitable extension.<br>
     *  <b>TODO: make it general - is any tool for it?</b>
     *
     *  @return string file extension without a dot
     */
    function _getExt()
    {
        switch($this->type){
            case"audio/mpeg": $ext="mp3"; break;
            case"audio/x-wave": $ext="wav"; break;
            case"application/x-ogg": $ext="ogg"; break;
            default: $ext="bin"; break;
        }
        return $ext;
    }
    /**
     *  Get filetype from global id
     *
     *  @param gunid string, optional, global unique id of file
     */
    function _getType($gunid)
    {
        return $this->dbc->getOne("SELECT type FROM {$this->filesTable}
            WHERE gunid='$gunid'");
    }
    /**
     *  Get and optionaly create subdirectory in real filesystem for storing
     *  raw media data
     *
     */
    function _getResDir()
    {
        $resDir="{$this->gb->storageDir}/".substr($this->gunid, 0, 3);
        if(!file_exists($resDir)){ mkdir($resDir, 02775); }
        return $resDir;
    }
    /**
     *  Get real filename of raw media data
     *
     *  @see RawMediaData
     */
    function _getRealRADFname()
    {
        return $this->rmd->getFname();
    }
    /**
     *  Create and return name for temporary symlink.<br>
     *  <b>TODO: Should be more unique</b>
     *
     */
    function _getAccessFname($sessid, $ext='EXT')
    {
        $spart = md5("{$sessid}_{$this->gunid}");
        return "{$this->accessDir}/$spart.$ext";
    }
}
?>