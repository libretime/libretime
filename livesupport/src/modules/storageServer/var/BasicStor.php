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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
define('GBERR_DENY', 40);
define('GBERR_FILEIO', 41);
define('GBERR_FILENEX', 42);
define('GBERR_FOBJNEX', 43);
define('GBERR_WRTYPE', 44);
define('GBERR_NONE', 45);
define('GBERR_AOBJNEX', 46);
define('GBERR_NOTF', 47);
define('GBERR_SESS', 48);
define('GBERR_PREF', 49);
define('GBERR_TOKEN', 50);
define('GBERR_PUT', 51);
define('GBERR_LOCK', 52);
define('GBERR_GUNID', 53);
define('GBERR_BGERR', 54);

define('GBERR_NOTIMPL', 69);

require_once dirname(__FILE__)."/../../alib/var/alib.php";
require_once "StoredFile.php";
require_once "Transport.php";

/**
 *  BasicStor class
 *
 *  Core of LiveSupport file storage module
 *
 *  @author  $Author$
 *  @version $Revision$
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
        parent::Alib($dbc, $config);
        $this->config = $config;
        $this->filesTable = $config['tblNamePrefix'].'files';
        $this->mdataTable = $config['tblNamePrefix'].'mdata';
        $this->accessTable= $config['tblNamePrefix'].'access';
        $this->storageDir = realpath($config['storageDir']);
        $this->bufferDir  = realpath($config['bufferDir']);
        $this->transDir  = realpath($config['transDir']);
        $this->accessDir  = realpath($config['accessDir']);
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
     *  @param ftype string, internal file type
     *  @param mdataLoc string 'file'|'string' (optional)
     *  @return int
     *  @exception PEAR::error
     */
    function bsPutFile($parid, $fileName, $mediaFileLP, $mdataFileLP,
        $gunid=NULL, $ftype='unKnown', $mdataLoc='file')
    {
        $name   = addslashes("$fileName");
        $ftype  = strtolower($ftype);
        $id = $this->addObj($name , $ftype, $parid);
        if($this->dbc->isError($id)) return $id;
        $ac =  StoredFile::insert(
            $this, $id, $name, $mediaFileLP, $mdataFileLP, $mdataLoc,
            $gunid, $ftype
        );
        if($this->dbc->isError($ac)){
            $res = $this->removeObj($id);
            // catch constraint violations
            switch($ac->getCode()){
                case -3:
                    return PEAR::raiseError(
                        "BasicStor::bsPutFile: gunid duplication",
                        GBERR_GUNID);
                default:
                    return $ac;
            }
        }
        if($ftype == 'playlist') $ac->setMime('application/smil');
        return $id;
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
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
                $ac = StoredFile::recall($this, $id);
                if($this->dbc->isError($ac)){
                    // catch nonerror exception:
                    //if($ac->getCode() != GBERR_FOBJNEX)
                    return $ac;
                }
                $res = $ac->rename($newName);
                if($this->dbc->isError($res)) return $res;
                break;
            case"File":
            default:
        }
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
        $parid = $this->getParent($id);
        if($this->getObjType($did) !== 'Folder')
            return PEAR::raiseError(
                "BasicStor::moveFile: destination is not folder ($did)",
                GBERR_WRTYPE
            );
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
            case"File":
            case"Folder":
                return $this->moveObj($id, $did);
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::moveFile: unsupported object to move, sorry.",
                    GBERR_WRTYPE
                );
        }
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
        $parid = $this->getParent($id);
        if($this->getObjType($did) !== 'Folder'){
            return PEAR::raiseError(
                'BasicStor::bsCopyFile: destination is not folder',
                GBERR_WRTYPE
            );
        }
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
            case"File":
            case"Folder":
                return $this->copyObj($id, $did);
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::moveFile: unsupported object to copy, sorry.",
                    GBERR_WRTYPE
                );
        }
    }

    /**
     *  Replace file. Doesn't change filetype!
     *
     *  @param id int, virt.file's local id
     *  @param mediaFileLP string, local path of media file
     *  @param mdataFileLP string, local path of metadata file
     *  @param mdataLoc string 'file'|'string' (optional)
     *  @return true or PEAR::error
     *  @exception PEAR::error
     */
    function bsReplaceFile($id, $mediaFileLP, $mdataFileLP, $mdataLoc='file')
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        if(!empty($mdataFileLP) &&
                ($mdataLoc!='file' || file_exists($mdataFileLP))){
            $r = $ac->replaceMetaData($mdataFileLP, $mdataLoc);
            if($this->dbc->isError($r)) return $r;
        }
        if(!empty($mediaFileLP) && file_exists($mediaFileLP)){
            $r = $ac->replaceRawMediaData($mediaFileLP);
            if($this->dbc->isError($r)) return $r;
        }
        return TRUE;
    }

    /**
     *  Delete file
     *
     *  @param id int, virt.file's local id
     *  @param forced boolean, if true don't use trash
     *  @return true or PEAR::error
     */
    function bsDeleteFile($id, $forced=FALSE)
    {
        // full delete:
        if(!$this->config['useTrash'] || $forced){
            $res = $this->removeObj($id, $forced);
            return $res;
        }
        // move to trash:
        $did = $this->getObjId($this->config['TrashName'], $this->storId);
        if($this->dbc->isError($did)) return $did;
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
                $ac = StoredFile::recall($this, $id);
                if($this->dbc->isError($ac)) return $ac;
                if(is_null($did)){
                    return PEAR::raiseError("BasicStor::bsDeleteFile: ".
                        "trash not found", GBERR_NOTF);
                }
                $res = $ac->setState('deleted');
                if($this->dbc->isError($res)) return $res;
                break;
            default:
        }
        $res = $this->bsMoveFile($id, $did);
        return $res;
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
            WHERE token=x'{$token}'::bigint AND type='$type'
        ");
        if($this->dbc->isError($cnt)){ return FALSE; }
        return ($cnt == 1);
    }
    
    /**
     *  Get gunid from token
     *
     *  @param token string, access/put token
     *  @param type string 'put'|'access'|'download'
     *  @return string
     */
    function _gunidFromToken($token, $type='put')
    {
        $acc = $this->dbc->getRow("
            SELECT to_hex(gunid)as gunid, ext FROM {$this->accessTable}
            WHERE token=x'{$token}'::bigint AND type='$type'
        ");
        if($this->dbc->isError($acc)){ return $acc; }
        $gunid = StoredFile::_normalizeGunid($acc['gunid']);
        if($this->dbc->isError($gunid)){ return $gunid; }
        return $gunid;
    }
    
    /**
     *  Create and return access link to real file
     *
     *  @param realFname string, local filepath to accessed file
     *      (NULL for only increase access counter, no symlink)
     *  @param ext string, useful filename extension for accessed file
     *  @param gunid int, global unique id
     *      (NULL for special files such exported playlists)
     *  @param type string 'access'|'download'
     *  @param parent int parent token (recursive access/release)
     *  @param owner int, local user id - owner of token
     *  @return array with: seekable filehandle, access token
     */
    function bsAccess($realFname, $ext, $gunid, $type='access',
        $parent='0', $owner=NULL)
    {
        if(!is_null($gunid)){
            $gunid = StoredFile::_normalizeGunid($gunid);
        }
        foreach(array('ext', 'type') as $v) $$v = addslashes($$v);
        $token  = StoredFile::_createGunid();
        if(!is_null($realFname)){
            $linkFname = "{$this->accessDir}/$token.$ext";
            if(!file_exists($realFname)){
                return PEAR::raiseError(
                    "BasicStor::bsAccess: real file not found ($realFname)",
                    GBERR_FILEIO);
            }
            if(! @symlink($realFname, $linkFname)){
                return PEAR::raiseError(
                    "BasicStor::bsAccess: symlink create failed ($linkFname)",
                    GBERR_FILEIO);
            }
        }else $linkFname=NULL;
        $this->dbc->query("BEGIN");
        $gunidSql = (is_null($gunid) ? "NULL" : "x'{$gunid}'::bigint" );
        $ownerSql = (is_null($owner) ? "NULL" : "$owner" );
        $res = $this->dbc->query("
            INSERT INTO {$this->accessTable}
                (gunid, token, ext, type, parent, owner, ts)
            VALUES
                ($gunidSql, x'$token'::bigint,
                '$ext', '$type', x'{$parent}'::bigint, $ownerSql, now())
        ");
        if($this->dbc->isError($res)){
            $this->dbc->query("ROLLBACK"); return $res; }
        if(!is_null($gunid)){
            $res = $this->dbc->query("
                UPDATE {$this->filesTable}
                SET currentlyAccessing=currentlyAccessing+1, mtime=now()
                WHERE gunid=x'{$gunid}'::bigint
            ");
        }
        if($this->dbc->isError($res)){
            $this->dbc->query("ROLLBACK"); return $res; }
        $res = $this->dbc->query("COMMIT");
        if($this->dbc->isError($res)){ return $res; }
        return array('fname'=>$linkFname, 'token'=>$token);
    }

    /**
     *  Release access link to real file
     *
     *  @param token string, access token
     *  @param type string 'access'|'download'
     *  @return hasharray
     *      gunid: string, global unique ID or real pathname of special file
     *      owner: int, local subject id of token owner
     *      realFname: string, real local pathname of accessed file
     */
    function bsRelease($token, $type='access')
    {
        if(!$this->bsCheckToken($token, $type)){
            return PEAR::raiseError(
             "BasicStor::bsRelease: invalid token ($token)"
            );
        }
        $acc = $this->dbc->getRow("
            SELECT to_hex(gunid)as gunid, ext, owner FROM {$this->accessTable}
            WHERE token=x'{$token}'::bigint AND type='$type'
        ");
        if($this->dbc->isError($acc)){ return $acc; }
        $ext = $acc['ext'];
        $owner = $acc['owner'];
        $linkFname = "{$this->accessDir}/$token.$ext";
        $realFname = readlink($linkFname);
        if(file_exists($linkFname)) if(! @unlink($linkFname)){
            return PEAR::raiseError(
                "BasicStor::bsRelease: unlink failed ($linkFname)",
                GBERR_FILEIO);
        }
        $this->dbc->query("BEGIN");
        if(!is_null($acc['gunid'])){
            $gunid = StoredFile::_normalizeGunid($acc['gunid']);
            $res = $this->dbc->query("
                UPDATE {$this->filesTable}
                SET currentlyAccessing=currentlyAccessing-1, mtime=now()
                WHERE gunid=x'{$gunid}'::bigint AND currentlyAccessing>0
            ");
            if($this->dbc->isError($res)){
                $this->dbc->query("ROLLBACK"); return $res; }
        }
        $res = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE token=x'$token'::bigint
        ");
        if($this->dbc->isError($res)){
            $this->dbc->query("ROLLBACK"); return $res; }
        $res = $this->dbc->query("COMMIT");
        if($this->dbc->isError($res)){ return $res; }
        $res = array(
            'gunid' => (isset($gunid) ? $gunid : NULL ),
            'realFname' => $realFname,
            'owner' => $owner,
        );
        return $res;
    }

    /**
     *  Create and return downloadable URL for file
     *
     *  @param id int, virt.file's local id
     *  @param part string, 'media'|'metadata'
     *  @param parent int parent token (recursive access/release)
     *  @return array with strings:
     *      downloadable URL, download token, chsum, size, filename
     */
    function bsOpenDownload($id, $part='media', $parent='0')
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        $gunid      = $ac->gunid;
        switch($part){
            case"media":
                $realfile  = $ac->_getRealRADFname();
                $ext       = $ac->_getExt();
                $filename  = $ac->_getFileName();
                break;
            case"metadata":
                $realfile  = $ac->_getRealMDFname();
                $ext       = "xml";
                $filename  = $ac->_getFileName();
                break;
            default:
                return PEAR::raiseError(
                 "BasicStor::bsOpenDownload: unknown part ($part)"
                );
        }
        $acc = $this->bsAccess($realfile, $ext, $gunid, 'download', $parent);
        if($this->dbc->isError($acc)){ return $acc; }
        $url = $this->getUrlPart()."access/".basename($acc['fname']);
        $chsum = md5_file($realfile);
        $size = filesize($realfile);
        return array(
            'url'=>$url, 'token'=>$acc['token'],
            'chsum'=>$chsum, 'size'=>$size,
            'filename'=>$filename
        );
    }

    /**
     *  Discard downloadable URL
     *
     *  @param token string, download token
     *  @param part string, 'media'|'metadata'
     *  @return string, gunid
     */
    function bsCloseDownload($token, $part='media')
    {
        if(!$this->bsCheckToken($token, 'download')){
            return PEAR::raiseError(
             "BasicStor::bsCloseDownload: invalid token ($token)"
            );
        }
        $r = $this->bsRelease($token, 'download');
        if($this->dbc->isError($r)){ return $r; }
        return (is_null($r['gunid']) ? $r['realFname'] : $r['gunid']);
    }

    /**
     *  Create writable URL for HTTP PUT method file insert
     *
     *  @param chsum string, md5sum of the file having been put
     *  @param gunid string, global unique id
     *      (NULL for special files such imported playlists)
     *  @param owner int, local user id - owner of token
     *  @return hasharray with:
     *      url string: writable URL
     *      fname string: writable local filename
     *      token string: PUT token
     */
    function bsOpenPut($chsum, $gunid, $owner=NULL)
    {
        if(!is_null($gunid)){
            $gunid = StoredFile::_normalizeGunid($gunid);
        }
        foreach(array('chsum') as $v) $$v = addslashes($$v);
        $ext    = '';
        $token  = StoredFile::_createGunid();
        $res    = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE token=x'$token'::bigint
        ");
        if($this->dbc->isError($res)){ return $res; }
        $gunidSql = (is_null($gunid) ? "NULL" : "x'{$gunid}'::bigint" );
        $ownerSql = (is_null($owner) ? "NULL" : "$owner" );
        $res    = $this->dbc->query("
            INSERT INTO {$this->accessTable}
                (gunid, token, ext, chsum, type, owner, ts)
            VALUES
                ($gunidSql, x'$token'::bigint,
                    '$ext', '$chsum', 'put', $ownerSql, now())
        ");
        if($this->dbc->isError($res)){ return $res; }
        $fname = "{$this->accessDir}/$token";
        touch($fname);      // is it needed?
        $url = $this->getUrlPart()."xmlrpc/put.php?token=$token";
        return array('url'=>$url, 'fname'=>$fname, 'token'=>$token);
    }

    /**
     *  Get file from writable URL and return local filename.
     *  Caller should move or unlink this file.
     *
     *  @param token string, PUT token
     *  @return hash with fields:
     *      fname string, local path of the file having been put
     *      owner int, local subject id - owner of token
     */
    function bsClosePut($token)
    {
        $token = StoredFile::_normalizeGunid($token);
        if(!$this->bsCheckToken($token, 'put')){
            return PEAR::raiseError(
             "BasicStor::bsClosePut: invalid token ($token)",
             GBERR_TOKEN
            );
        }
        $row = $this->dbc->getRow("
            SELECT chsum, owner FROM {$this->accessTable}
            WHERE token=x'{$token}'::bigint
        ");
        if($this->dbc->isError($row)){ return $row; }
        $chsum = $row['chsum'];
        $owner = $row['owner'];
        $res = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE token=x'$token'::bigint
        ");
        if($this->dbc->isError($res)){ return $res; }
        $fname = "{$this->accessDir}/$token";
        $md5sum = md5_file($fname);
        if(trim($chsum) !='' && $chsum != $md5sum){
            if(file_exists($fname)) @unlink($fname);
            return PEAR::raiseError(
             "BasicStor::bsClosePut: md5sum does not match (token=$token)".
             " [$chsum/$md5sum]",
             GBERR_PUT
            );
        }
        return array('fname'=>$fname, 'owner'=>$owner);
    }

    /**
     *  Check uploaded file
     *
     *  @param token string, put token
     *  @return hash, (
     *      status: boolean,
     *      size: int - filesize
     *      expectedsum: string - expected checksum
     *      realsum: string - checksum of uploaded file
     *   )
     */
    function bsCheckPut($token)
    {
        if(!$this->bsCheckToken($token, 'put')){
            return PEAR::raiseError(
             "BasicStor::bsCheckPut: invalid token ($token)"
            );
        }
        $chsum = $this->dbc->getOne("
            SELECT chsum FROM {$this->accessTable}
            WHERE token=x'{$token}'::bigint
        ");
        if($this->dbc->isError($chsum)){ return $chsum; }
        $fname = "{$this->accessDir}/$token";
        $md5sum = md5_file($fname);
        $size = filesize($fname);
        $status = ($chsum == $md5sum);
        return array(
            'status'=>$status, 'size'=>$size,
            'expectedsum'=>$chsum,
            'realsum'=>$md5sum,
        );
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
    
    /**
     *  Return local subject id of token owner
     *
     *  @param token: string - access/put/render etc. token
     *  @return int - local subject id
     */
    function getTokenOwner($token)
    {
        $row = $this->dbc->getOne("
            SELECT owner FROM {$this->accessTable}
            WHERE token=x'{$token}'::bigint
        ");
        if($this->dbc->isError($row)){ return $row; }
        $owner = $row;
    }
    
    /** 
     *  Get tokens by type
     *
     *  @param type: string - access|put|render etc.
     *  @return array - array of tokens
     */
    function getTokensByType($type)
    {
        $res = $this->dbc->query(
            "SELECT TO_HEX(token) AS token FROM {$this->accessTable} WHERE type=?",
            array($type));
        while ($row = $res->fetchRow()) {
             $r[] = $row['token'];
        }
        return $r;
    }
    
    /* ----------------------------------------------------- metadata methods */

    /**
     *  Replace metadata with new XML file or string
     *
     *  @param id int, virt.file's local id
     *  @param mdata string, local path of metadata XML file
     *  @param mdataLoc string 'file'|'string'
     *  @return boolean or PEAR::error
     */
    function bsReplaceMetadata($id, $mdata, $mdataLoc='file')
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        return $ac->replaceMetaData($mdata, $mdataLoc);
    }
    
    /**
     *  Get metadata as XML string
     *
     *  @param id int, virt.file's local id
     *  @return string or PEAR::error
     */
    function bsGetMetadata($id)
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        return $ac->getMetaData();
    }

    /**
     *  Get dc:title (if exists)
     *
     *  @param id int, virt.file's local id
     *  @param gunid string, virt.file's gunid, optional, used only if not
     *          null, id is then ignored
     *  @param lang string, optional xml:lang value for select language version
     *  @param deflang string, optional xml:lang for default language
     *  @return string or PEAR::error
     */
    function bsGetTitle($id, $gunid=NULL, $lang=NULL, $deflang=NULL)
    {
        if(is_null($gunid)) $ac = StoredFile::recall($this, $id);
        else $ac = $r = StoredFile::recallByGunid($this, $gunid);
        if($this->dbc->isError($r)) return $r;
        $r = $ac->md->getMetadataValue('dc:title', $lang, $deflang);
        if($this->dbc->isError($r)) return $r;
        $title = (isset($r[0]['value']) ? $r[0]['value'] : 'unknown');
        return $title;
    }

    /**
     *  Get metadata element value
     *
     *  @param id int, virt.file's local id
     *  @param category string, metadata element name
     *  @param lang string, optional xml:lang value for select language version
     *  @param deflang string, optional xml:lang for default language
     *  @return array of matching records (as hash {id, value, attrs})
     *  @see Metadata::getMetadataValue
     */
    function bsGetMetadataValue($id, $category, $lang=NULL, $deflang=NULL)
    {   
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        return $ac->md->getMetadataValue($category, $lang, $deflang);
    }
    
    /**
     *  Set metadata element value
     *
     *  @param id int, virt.file's local id
     *  @param category string, metadata element identification (e.g. dc:title)
     *  @param value string/NULL value to store, if NULL then delete record
     *  @param lang string, optional xml:lang value for select language version
     *  @param mid int, metadata record id (OPTIONAL on unique elements)
     *  @param container string, container element name for insert
     *  @param regen boolean, optional flag, if true, regenerate XML file
     *  @return boolean
     */
    function bsSetMetadataValue($id, $category, $value,
        $lang=NULL, $mid=NULL, $container='metadata', $regen=TRUE)
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        /* disabled - html ui change only nonimportant categories
        if($ac->isEdited()){
            return PEAR::raiseError(
                'BasicStor::bsSetMetadataValue: is edited', GBERR_LOCK
            );
        }
        */
        if($category == 'dcterms:extent'){
            $value = $this->normalizeExtent($value);
        }
        $res = $ac->md->setMetadataValue(
            $category, $value, $lang, $mid, $container);
        if($this->dbc->isError($res)) return $res;
        if($regen){
            $r = $ac->md->regenerateXmlFile();
            if($this->dbc->isError($r)) return $r;
        }
        return $res;
    }

    /**
     *  Normalize time value to hh:mm:ss:dddddd format
     *
     *  @param v mixed, value to normalize
     *  @return string
     */
    function normalizeExtent($v)
    {
        if(!preg_match("|^\d{2}:\d{2}:\d{2}.\d{6}$|", $v)){
            require_once"Playlist.php";
            $s = Playlist::_plTimeToSecs($v);
            $t = Playlist::_secsToPlTime($s);
            return $t;
        }
        return $v;
    }
    
    /**
     *  Set metadata values in 'batch' mode
     *
     *  @param id int, virt.file's local id
     *  @param values hasharray, array of key/value pairs
     *      (e.g. 'dc:title'=>'New title')
     *  @param lang string, optional xml:lang value for select language version
     *  @param container string, container element name for insert
     *  @param regen boolean, optional flag, if true, regenerate XML file
     *  @return boolean
     */
    function bsSetMetadataBatch(
        $id, $values, $lang=NULL, $container='metadata', $regen=TRUE)
    {
        if(!is_array($values)) $values = array($values);
        foreach($values as $category=>$oneValue){
            $res = $this->bsSetMetadataValue($id, $category, $oneValue,
                $lang, NULL, $container, FALSE);
            if($this->dbc->isError($res)) return $res;
        }
        if($regen){
            $ac = StoredFile::recall($this, $id);
            if($this->dbc->isError($ac)) return $ac;
            $r = $ac->md->regenerateXmlFile();
            if($this->dbc->isError($r)) return $r;
        }
        return TRUE;
    }

    /**
     *  Search in local metadata database.
     *
     *  @param criteria hash, with following structure:<br>
     *   <ul>
     *     <li>filetype - string, type of searched files,
     *       meaningful values: 'audioclip', 'webstream', 'playlist', 'all'</li>
     *     <li>operator - string, type of conditions join
     *       (any condition matches / all conditions match), 
     *       meaningful values: 'and', 'or', ''
     *       (may be empty or ommited only with less then 2 items in
     *       &quot;conditions&quot; field)
     *     </li>
     *     <li>orderby : string - metadata category for sorting (optional)
     *         default sorting by dc:title (+ primary sorting by filetype -
     *         audioclips, playlists, webstreams ...)
     *     </li>
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
     *  @param limit int, limit for result arrays (0 means unlimited)
     *  @param offset int, starting point (0 means without offset)
     *  @return array of hashes, fields:
     *       cnt : integer - number of matching gunids 
     *              of files have been found
     *       results : array of hashes:
     *          gunid: string
     *          type: string - audioclip | playlist | webstream
     *          title: string - dc:title from metadata
     *          creator: string - dc:creator from metadata
     *          length: string - dcterms:extent in extent format
     *  @see DataEngine
     */
    function bsLocalSearch($criteria, $limit=0, $offset=0)
    {
        require_once "DataEngine.php";
        $de =& new DataEngine($this);
        $res = $r = $de->localSearch($criteria, $limit, $offset);
        if(PEAR::isError($r)){ return $r; }
        return $res;
    }

    /**
     *  Return values of specified metadata category
     *
     *  @param category string, metadata category name
     *          with or without namespace prefix (dc:title, author)
     *  @param limit int, limit for result arrays (0 means unlimited)
     *  @param offset int, starting point (0 means without offset)
     *  @param criteria hash, see bsLocalSearch method
     *  @return hash, fields:
     *       results : array with found values
     *       cnt : integer - number of matching values
     *  @see DataEngine
     */
    function bsBrowseCategory($category, $limit=0, $offset=0, $criteria=NULL)
    {
        require_once "DataEngine.php";
        $de =& new DataEngine($this);
        return $de->browseCategory($category, $limit, $offset, $criteria);
    }

    /* ---------------------------------------------------- methods4playlists */

    /**
     *  Create a tarfile with playlist export - playlist and all matching
     *  sub-playlists and media files (if desired)
     *
     *  @param plids - array of strings, playlist global unique IDs
     *          (one gunid is accepted too)
     *  @param type - string, playlist format,
     *          possible values: lspl | smil | m3u
     *  @param withContent - boolean, if true export related files too
     *  @return hasharray with  fields:
     *      fname string: readable fname,
     *      token string: access token
     */
    function bsExportPlaylistOpen($plids, $type='lspl', $withContent=TRUE)
    {
        require_once"Playlist.php";
        if(!is_array($plids)) $plids=array($plids);
        $gunids = array();
        foreach($plids as $plid){
            $pl = $r = Playlist::recallByGunid($this, $plid);
            if(PEAR::isError($r)) return $r;
            if($withContent){
                $gunidsX = $r = $pl->export();
                if(PEAR::isError($r)) return $r;
            }else{
                $gunidsX = array(array('gunid'=>$plid, 'type'=>'playlist'));
            }
            $gunids = array_merge($gunids, $gunidsX);
        }
        $plExts = array('lspl'=>"lspl", 'smil'=>"smil", 'm3u'=>"m3u");
        $plExt = (isset($plExts[$type]) ? $plExts[$type] : "xml" );
        $res = array();
        $tmpn = tempnam($this->bufferDir, 'plExport_');
        $tmpf = "$tmpn.tar";
        $tmpd = "$tmpn.dir";               mkdir($tmpd);
        $tmpdp = "$tmpn.dir/playlist";    mkdir($tmpdp);
        if($withContent){
            $tmpdc = "$tmpn.dir/audioClip";        mkdir($tmpdc);
        }
        foreach($gunids as $i=>$it){
            $ac = $r = StoredFile::recallByGunid($this, $it['gunid']);
            if(PEAR::isError($r)) return $r;
            $MDfname = $r = $ac->md->getFname();
            if(PEAR::isError($r)) return $r;
            if(file_exists($MDfname)){ switch($it['type']){
                case"playlist":
                    require_once"LsPlaylist.php";
                    $ac = $r = LsPlaylist::recallByGunid($this, $it['gunid']);
                    switch($type){
                        case"smil": $string = $r = $ac->output2Smil(); break;
                        case"m3u":  $string = $r = $ac->output2M3U();  break;
                        default:    $string = $r = $ac->md->genXmlDoc();
                    }
                    if(PEAR::isError($r)) return $r;
                    $r = $this->bsStr2File($string, "$tmpdp/{$it['gunid']}.$plExt");
                    if(PEAR::isError($r)) return $r;
                    break;
                default: copy($MDfname, "$tmpdc/{$it['gunid']}.xml"); break;
            }}
            $RADfname = $r =$ac->_getRealRADFname();
            if(PEAR::isError($r)) return $r;
            $RADext = $r = $ac->_getExt();
            if(PEAR::isError($r)) return $r;
            if(file_exists($RADfname)){
                copy($RADfname, "$tmpdc/{$it['gunid']}.$RADext");
            }
        }
        if(count($plids)==1){
            copy("$tmpdp/$plid.$plExt", "$tmpd/exportedPlaylist.$plExt");
        }
        $res = `cd $tmpd; tar cf $tmpf * --remove-files`;
        @rmdir($tmpdc);  @rmdir($tmpdp); @rmdir($tmpd);
        unlink($tmpn);
        $acc = $this->bsAccess($tmpf, 'tar', NULL/*gunid*/, 'access');
        if($this->dbc->isError($acc)){ return $acc; }
        return $acc;
    }

    /**
     *  Close playlist export previously opened by the bsExportPlaylistOpen
     *  method
     *
     *  @param token - string, access token obtained from bsExportPlaylistOpen
     *            method call
     *  @return boolean true or error object
     */
    function bsExportPlaylistClose($token)
    {
        $r = $this->bsRelease($token, 'access');
        if($this->dbc->isError($r)){ return $r; }
        $file = $r['realFname'];
        if(file_exists($file)) if(! @unlink($file)){
            return PEAR::raiseError(
                "BasicStor::bsExportPlaylistClose: unlink failed ($file)",
                GBERR_FILEIO);
        }
        return TRUE;
    }

    /**
     *  Import playlist in LS Archive format
     *
     *  @param parid int,  destination folder local id
     *  @param plid string,  playlist gunid
     *  @param aPath string, absolute path part of imported file
     *              (e.g. /home/user/livesupport)
     *  @param rPath string, relative path/filename part of imported file
     *              (e.g. playlists/playlist_1.smil)
     *  @param ext string, playlist extension (determines type of import)
     *  @param gunids hasharray, hash relation from filenames to gunids
     *  @param subjid int, local subject (user) id (id of user doing the import)
     *  @return int, result file local id (or error object)
     */
    function bsImportPlaylistRaw($parid, $plid, $aPath, $rPath, $ext, &$gunids, $subjid)
    {
        $id = $r = $this->_idFromGunid($plid);
        if(!is_null($r)) return $r;
        $path = realpath("$aPath/$rPath");
        if(FALSE === $path){
            return PEAR::raiseError(
                "BasicStor::bsImportPlaylistRaw: file doesn't exist ($aPath/$rPath)"
            );
        }
        switch($ext){
            case"xml":
            case"lspl":
                $fname = $plid;
                $res = $this->bsPutFile($parid, $fname,
                    NULL, $path, $plid, 'playlist'
                );
                break;
            case"smil":
                require_once "SmilPlaylist.php";
                $res = SmilPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $parid, $subjid);
                if(PEAR::isError($res)) break;
                $res = $res->getId();
                break;
            case"m3u":
                require_once "M3uPlaylist.php";
                $res = M3uPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $parid, $subjid);
                if(PEAR::isError($res)) break;
                $res = $res->getId();
                break;
            default:
                $res = PEAR::raiseError(
                    "BasicStor::importPlaylistRaw: unknown playlist format".
                    " (gunid:$plid, format:$ext)"
                );
                break;
        }
        if(!PEAR::isError($res)){ $gunids[basename($rPath)] = $plid; }
        return $res;
    }

    /**
     *  Import playlist in LS Archive format
     *
     *  @param parid int,  destination folder local id
     *  @param fpath string, imported file pathname
     *  @param subjid int, local subject (user) id (id of user doing the import)
     *  @return int, result file local id (or error object)
     */
    function bsImportPlaylist($parid, $fpath, $subjid)
    {
        // untar:
        $tmpn = tempnam($this->bufferDir, 'plImport_');
        $tmpd = "$tmpn.dir";
        $tmpdc = "$tmpd/audioClip";
        $tmpdp = "$tmpd/playlist";
        mkdir($tmpd);
        $res = `cd $tmpd; tar xf $fpath`;
        // clips:
        $d = @dir($tmpdc);   $entries=array(); $gunids=array();
        if($d !== false){
            while (false !== ($entry = $d->read())) {
                if(preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)){
                    list(,$gunid, $ext) = $va;
                    switch($ext){
                        case"xml":
                            $entries[$gunid]['metadata'] = $entry;
                            break;
                        default:
                            $entries[$gunid]['rawMedia'] = $entry;
                            $entries[$gunid]['rawMediaExt'] = $ext;
                            $gunids[$entry] = $gunid;
                            break;
                    }
                }
            }
            $d->close();
        }
        $res = TRUE;
        foreach($entries as $gunid=>$it){
            $rawMedia = "$tmpdc/{$it['rawMedia']}";
            if(!file_exists($rawMedia)) $rawMedia = NULL;
            $metadata = "$tmpdc/{$it['metadata']}";
            if(!file_exists($metadata)) $metadata = NULL;
            $r = $this->bsExistsFile($gunid, NULL, TRUE);
            if(!PEAR::isError($res) && !$r){
                $res = $this->bsPutFile($parid, $gunid, $rawMedia, $metadata,
                    $gunid, 'audioclip'
                );
            }
            @unlink("$tmpdc/{$it['rawMedia']}");
            @unlink("$tmpdc/{$it['metadata']}");
            if(PEAR::isError($res)) break;
        }
        // playlists:
        require_once"Playlist.php";
        $d = @dir($tmpdp);
        if($d !== false){
            while ((!PEAR::isError($res)) && false !== ($entry = $d->read())) {
                if(preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)){
                    list(,$gunid, $ext) = $va;
                    $res = $this->bsImportPlaylistRaw($parid, $gunid,
                        $tmpdp, $entry, $ext, $gunids, $subjid);
                    unlink("$tmpdp/$entry");
                    if(PEAR::isError($res)) break;
                }       
            }
            $d->close();
        }
        //@rmdir($tmpdc); @rmdir($tmpdp); @rmdir($tmpd);
        @system("rm -rf $tmpdc"); @system("rm -rf $tmpdp"); @system("rm -rf $tmpd");
        @unlink($tmpn);
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
        if($this->getObjType($id) !== 'Folder')
            return PEAR::raiseError(
                'BasicStor::bsListFolder: not a folder', GBERR_NOTF
            );
        $listArr = $this->getDir($id, 'id, name, type, param as target', 'name');
        if($this->dbc->isError($listArr)) return $listArr;
        foreach($listArr as $i=>$v){
            if($v['type'] == 'Folder')  break;
            $gunid = $this->_gunidFromId($v['id']);
            if($this->dbc->isError($gunid)) return $gunid;
            if(is_null($gunid)){ unset($listArr[$i]); break; }
            $listArr[$i]['type'] = $r = $this->_getType($gunid);
            if($this->dbc->isError($r)) return $r;
            $listArr[$i]['gunid'] = $gunid;
            if(StoredFIle::_getState($gunid) == 'incomplete')
                unset($listArr[$i]);
        }
        return $listArr;
    }
    
    /**
     *  Analyze media file for internal metadata information
     *
     *  @param id int, virt.file's local id
     *  @return array
     */
    function bsAnalyzeFile($id)
    {
        $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)) return $ac;
        $ia = $ac->analyzeMediaFile();
        return $ia;
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
        $relPath = trim(urldecode($relPath));
        //if($this->getObjType($id) !== 'Folder')
        $nid = $this->getParent($id);
        if($this->dbc->isError($nid)) return $nid;
        if(is_null($nid)){
            return PEAR::raiseError("null parent for id=$id"); }
        //else $nid = $id;
        if(substr($relPath, 0, 1)=='/'){ $nid=$this->storId; }
        $a = split('/', $relPath);
        foreach($a as $i=>$pathItem){
            switch($pathItem){
                case".":
                    break;
                case"..":
                    if($nid != $this->storId){
                        $nid = $this->getParent($nid);
                        if($this->dbc->isError($nid)) return $nid;
                        if(is_null($nid)){
                             return PEAR::raiseError(
                                "null parent for $nid");
                        }
                    }
                    break;
                case"":
                    break;
                default:
                    $nid = $this->getObjId($pathItem, $nid);
                    if($this->dbc->isError($nid)) return $nid;
                    if(is_null($nid)){
                         return PEAR::raiseError(
                            "Object $pathItem not found (from id=$id)");
                    }
            }
        }
        return $nid;
    }
    
    /**
     *  Check if file exists in the storage
     *
     *  @param id: int, local id
     *  @param ftype: string, internal file type
     *  @param byGunid: boolean, select file by gunid (id is then ignored)
     *  @return boolean
     */
    function bsExistsFile($id, $ftype=NULL, $byGunid=FALSE)
    {
        if($byGunid) $ac = StoredFile::recallByGunid($this, $id);
        else $ac = StoredFile::recall($this, $id);
        if($this->dbc->isError($ac)){
            // catch some exceptions
            switch($ac->getCode()){
                case GBERR_FILENEX:
                case GBERR_FOBJNEX:
                    return FALSE;
                    break;
                default: return $ac;
            }
        }
        $realFtype = $this->_getType($ac->gunid);
        if(!is_null($ftype) && (
            ($realFtype != $ftype)
            // webstreams are subset of audioclips
            && !($realFtype == 'webstream' && $ftype == 'audioclip')
        )) return FALSE;
        return TRUE;
    }

    /* ---------------------------------------------------- redefined methods */
    /**
     *  Get object type by id.
     *  (RootNode, Folder, File, )
     *
     *  @param oid int, local object id
     *  @return string/err
     */
    function getObjType($oid)
    {
        $type = parent::getObjType($oid);
        if($type == 'File'){
            $gunid = $this->_gunidFromId($oid);
            if($this->dbc->isError($gunid)) return $gunid;
            $ftype = $this->_getType($gunid);
            if($this->dbc->isError($ftype)) return $ftype;
            if(!is_null($ftype)) $type=$ftype;
        }
        return $type;
    }
    
    /**
     *  Add new user with home folder
     *
     *  @param login string
     *  @param pass string OPT
     *  @param realname string OPT
     *  @return int/err
     */
    function addSubj($login, $pass=NULL, $realname='')
    {
        $uid = parent::addSubj($login, $pass, $realname);
        if($this->dbc->isError($uid)) return $uid;
        if($this->isGroup($uid) === FALSE){
            $fid = $this->bsCreateFolder($this->storId, $login);
            if($this->dbc->isError($fid)) return $fid;
            $res = parent::addPerm($uid, '_all', $fid, 'A');
            if($this->dbc->isError($res)) return $res;
            if(!$this->config['isArchive']){
                $res =$this->addSubj2Gr($login, $this->config['StationPrefsGr']);
                if($this->dbc->isError($res)) return $res;
                $res =$this->addSubj2Gr($login, $this->config['AllGr']);
                if($this->dbc->isError($res)) return $res;
                $pfid = $this->bsCreateFolder($fid, 'public');
                if($this->dbc->isError($pfid)) return $pfid;
                $res = parent::addPerm($uid, '_all', $pfid, 'A');
                if($this->dbc->isError($res)) return $res;
                $allGrId =  $this->getSubjId($this->config['AllGr']);
                if($this->dbc->isError($allGrId)) return $allGrId;
                $res = parent::addPerm($allGrId, 'read', $pfid, 'A');
                if($this->dbc->isError($res)) return $res;
            }
        }
        return $uid;
    }
    /**
     *  Remove user by login and remove also his home folder
     *
     *  @param login string
     *  @return boolean/err
     */
    function removeSubj($login)
    {
        if(FALSE !== array_search($login, $this->config['sysSubjs'])){
            return $this->dbc->raiseError(
                "BasicStor::removeSubj: cannot remove system user/group");
        }
        $uid = $this->getSubjId($login);
        if($this->dbc->isError($uid)) return $uid;
        $res = $this->dbc->query("
            DELETE FROM {$this->accessTable} WHERE owner=$uid
        ");
        if($this->dbc->isError($res)) return $res;
        $res = parent::removeSubj($login);
        if($this->dbc->isError($res)) return $res;
        $id = $this->getObjId($login, $this->storId);
        if($this->dbc->isError($id)) return $id;
        if(!is_null($id)){
            // remove home folder:
            $res = $this->bsDeleteFile($id);
            if($this->dbc->isError($res)) return $res;
        }
        return TRUE;
    }  
    
    /**
     *   Authenticate and create session
     *
     *   @param login string
     *   @param pass string
     *   @return boolean/sessionId/err
     */
    function login($login, $pass)
    {
        $r = $this->upgradeDbStructure();
        if($this->dbc->isError($r)) return $r;
        $r = parent::login($login, $pass);
        if($this->dbc->isError($r)) return $r;
        return $r;
    }
    
    /* ================================================== "protected" methods */
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
        if($this->dbc->isError($userid)) return $userid;
        if(is_null($userid)){
            return PEAR::raiseError(
                "BasicStor::_authorize: invalid session", GBERR_DENY);
        }
        if(!is_array($pars)) $pars = array($pars);
        if(!is_array($acts)) $acts = array($acts);
        $perm = true;
        foreach($acts as $i=>$action){
            $res = $this->checkPerm($userid, $action, $pars[$i]);
            if($this->dbc->isError($res)) return $res;
            $perm = $perm && $res;
        }
        if($perm) return TRUE;
        $adesc = "[".join(',',$acts)."]";
        return PEAR::raiseError(
            "BasicStor::$adesc: access denied", GBERR_DENY);
    }

    /**
     *  Return users's home folder local ID
     *
     *  @param subjid string, local subject id
     *  @return local folder id
     */
    function _getHomeDirId($subjid)
    {
        $login = $this->getSubjName($subjid);
        if($this->dbc->isError($login)) return $login;
        $parid = $this->getObjId($login, $this->storId);
        if($this->dbc->isError($parid)) return $parid;
        if(is_null($parid)){
            return PEAR::raiseError("BasicStor::_getHomeDirId: ".
                "homedir not found ($subjid)", GBERR_NOTF);
        }
        return $parid;
    }
    
    /**
     *  Return users's home folder local ID
     *
     *  @param sessid string, session ID
     *  @return local folder id
     */
    function _getHomeDirIdFromSess($sessid)
    {
        $uid = $this->getSessUserId($sessid);
        if($this->dbc->isError($uid)) return $uid;
        return $this->_getHomeDirId($uid);
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
        if($this->dbc->isError($gunid)) return $gunid;
        if(is_null($gunid)) return NULL;
        return StoredFile::_normalizeGunid($gunid);
    }

    /**
     *  Get storage-internal file type
     *
     *  @param gunid string, global unique id of file
     *  @return string, see install()
     */
    function _getType($gunid)
    {
        $ftype = $this->dbc->getOne("
            SELECT ftype FROM {$this->filesTable}
            WHERE gunid=x'$gunid'::bigint
        ");
        return $ftype;
    }

    /**
     *  Check gunid format
     *
     *  @param gunid string, global unique ID
     *  @return boolean
     */
    function _checkGunid($gunid)
    {
        $res = preg_match("|^([0-9a-fA-F]{16})?$|", $gunid);
        return $res;
    }

    /**
     *  Returns if gunid is free
     *
     */
    function _gunidIsFree($gunid)
    {
        $cnt = $this->dbc->getOne("
            SELECT count(*) FROM {$this->filesTable}
            WHERE gunid=x'{$this->gunid}'::bigint
        ");
        if($this->dbc->isError($cnt)) return $cnt;
        if($cnt > 0) return FALSE;
        return TRUE;
    }    

    /**
     *  Set playlist edit flag
     *
     *  @param playlistId string, playlist global unique ID
     *  @param val boolean, set/clear of edit flag
     *  @param sessid string, session id
     *  @param subjid int, subject id (if sessid is not specified)
     *  @return boolean, previous state
     */
    function _setEditFlag($playlistId, $val=TRUE, $sessid=NULL, $subjid=NULL)
    {
        if(!is_null($sessid)){
            $subjid = $this->getSessUserId($sessid);
            if($this->dbc->isError($subjid)) return $subjid;
        }
        $ac = StoredFile::recallByGunid($this, $playlistId);
        if($this->dbc->isError($ac)) return $ac;
        $state = $ac->_getState();
        if($val){ $r = $ac->setState('edited', $subjid); }
        else{ $r = $ac->setState('ready', 'NULL'); }
        if($this->dbc->isError($r)) return $r;
        return ($state == 'edited');
    }

    /**
     *  Check if playlist is marked as edited
     *
     *  @param playlistId string, playlist global unique ID
     *  @return FALSE | int - id of user editing it
     */
    function _isEdited($playlistId)
    {
        $ac = StoredFile::recallByGunid($this, $playlistId);
        if($this->dbc->isError($ac)) return $ac;
        if(!$ac->isEdited($playlistId)) return FALSE;
        return $ac->isEditedBy($playlistId);
    }

    /* ---------------------------------------- redefined "protected" methods */
    /**
     *  Copy virtual file.<br>
     *  Redefined from parent class.
     *
     *  @return int, new object local id
     */
    function copyObj($id, $newParid, $after=NULL)
    {
        $parid = $this->getParent($id);
        $nid = parent::copyObj($id, $newParid, $after);
        if($this->dbc->isError($nid)) return $nid;
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
                $ac = StoredFile::recall($this, $id);
                if($this->dbc->isError($ac)){ return $ac; }
                $ac2 = StoredFile::copyOf($ac, $nid);
                $ac2->rename($this->getObjName($nid));
                break;
            case"File":
            default:
        }
        return $nid;
    }

    /**
     *  Move virtual file.<br>
     *  Redefined from parent class.
     *
     *  @return boolean
     */
    function moveObj($id, $newParid, $after=NULL)
    {
        $parid = $this->getParent($id);
        switch($this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
                $ac = StoredFile::recall($this, $id);
                if($this->dbc->isError($ac)){ return $ac; }
                if($ac->isEdited())
                    return PEAR::raiseError(
                        'BasicStor::moveObj: is edited');
                if($ac->isAccessed())
                    return PEAR::raiseError(
                        'BasicStor::moveObj: is accessed');
                break;
            default:
        }
        $nid = parent::moveObj($id, $newParid, $after);
        if($this->dbc->isError($nid)) return $nid;
        return TRUE;
    }
    /**
     *  Optionaly remove virtual file with the same name and add new one.<br>
     *  Redefined from parent class.
     *
     *  @return id
     */
    function addObj($name, $type, $parid=1, $aftid=NULL, $param='')
    {
        $exid = $this->getObjId($name, $parid);
        if($this->dbc->isError($exid)) return $exid;
        //if(!is_null($exid)){ $this->removeObj($exid); }
        $name2 = $name;
        for( ;
            $xid = $this->getObjId($name2, $parid),
                !is_null($xid) && !$this->dbc->isError($xid);
            $name2 .= "_"
        );
        if(!is_null($exid)){ $r = $this->renameObj($exid, $name2); }
        return parent::addObj($name, $type, $parid, $aftid, $param);
    }

    /**
     *  Remove virtual file.<br>
     *  Redefined from parent class.
     *
     *  @param id int, local id of removed object
     *  @param forced boolean, unconditional delete
     *  @return true or PEAR::error
     */
    function removeObj($id, $forced=FALSE)
    {
        switch($ot = $this->getObjType($id)){
            case"audioclip":
            case"playlist":
            case"webstream":
                $ac = StoredFile::recall($this, $id);
                if($this->dbc->isError($ac)) return $ac;
                if($ac->isEdited() && !$forced){
                    return PEAR::raiseError(
                        'BasicStor.php: removeObj: is edited'
                    );
                }
                if($ac->isAccessed() && !$forced){
                    return PEAR::raiseError(
                        'BasicStor.php: removeObj: is accessed'
                    );
                }
                $ac->delete();
                break;
            case"File":
            case"Folder":
            case"Replica":
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::bsDeleteFile: unknown obj type ($ot)"
                );
        }
        $res = parent::removeObj($id);
        if($this->dbc->isError($res)) return $res;
        return TRUE;
    }

    /* ========================================================= misc methods */
    /**
     *  Write string to file
     *
     *  @param str - string to be written to file
     *  @param fname - string, pathname to file
     *  @return TRUE or raise error
     */
    function bsStr2File($str, $fname)
    {
        $fp = @fopen($fname, "w");
        if($fp === FALSE){
            return PEAR::raiseError(
                "BasicStor::bsStr2File: cannot open file ($fname)"
            );
        }
        fwrite($fp, $str);
        fclose($fp);
        return TRUE;
    }
    
    /**
     *  Check and optionally upgrade LS db structure.
     *  (add column suported only now)
     *
     *  items in array with db changes:
     *  <ul>
     *      <li>tbl - table name</li>
     *      <li>fld - field name</li>
     *      <li>type - type of field</li>
     *  </ul>
     *
     *  @return boolean TRUE or error
     */
    function upgradeDbStructure()
    {
        $chDb = array(
            '1.1 Leon' => array(
                array('tbl'=>$this->accessTable, 'fld'=>'owner',
                    'type'=>"int REFERENCES {$this->subjTable}"
                ),
            ),
            '1.1 Freetown' => array(
                array('tbl'=>$this->filesTable, 'fld'=>'mtime',
                    'type'=>'timestamp(6) with time zone'
                ),
            ),
        );
        foreach($chDb as $version=>$chArr){
            foreach($chArr as $change){
                extract($change);   // tbl, op, fld, type
                $r = $this->dbc->tableInfo($tbl, DB_TABLEINFO_ORDERTABLE);
                if($this->dbc->isError($r)) return $r;
                if(!isset($r['ordertable'][$tbl][$fld])){
                    $q = "ALTER table $tbl ADD $fld $type";
                    $r = $this->dbc->query($q);
                    if($this->dbc->isError($r)) return $r;
                }
            }
        }
        return TRUE;
    }
    
    /* =============================================== test and debug methods */
    /**
     *  Reset storageServer for debugging.
     *
     *  @param loadSampleData boolean - flag for allow sample data loading
     *  @param filesOnly boolean - flag for operate only on files in storage
     *  @return result of localSearch with filetype 'all' and no conditions,
     *      i.e. array of hashes, fields:
     *       cnt : integer - number of inserted files
     *       results : array of hashes:
     *          gunid: string
     *          type: string - audioclip | playlist | webstream
     *          title: string - dc:title from metadata
     *          creator: string - dc:creator from metadata
     *          length: string - dcterms:extent in extent format
     */
    function resetStorage($loadSampleData=TRUE, $filesOnly=FALSE)
    {
        if($filesOnly) $this->deleteFiles();
        else $this->deleteData();
        if(!$this->config['isArchive']){
            $tr =& new Transport($this);
            $tr->resetData();
        }
        $res = array(
            'cnt'=>0, 'results'=>array(),
        );
        if(!$loadSampleData) return $res;
        $rootHD = $this->getObjId('root', $this->storId);
        $samples = dirname(__FILE__)."/tests/sampleData.php";
        if(file_exists($samples)){
            include $samples;
        }else $sampleData = array();
        foreach($sampleData as $k=>$it){
            $type = $it['type'];
            $xml = $it['xml'];
            if(isset($it['gunid'])) $gunid = $it['gunid'];
            else $gunid = '';
            switch($type){
                case"audioclip":
                    $media = $it['media'];
                    $fname = basename($media);
                    break;
                case"playlist":
                case"webstream":
                    $media = '';
                    $fname = basename($xml);
                    break;
            }
            $r = $this->bsPutFile(
                $rootHD, $fname,
                $media, $xml, $gunid, $type
            );
            if(PEAR::isError($r)){ return $r; }
            #$gunid = $this->_gunidFromId($r);
            #$res['results'][] = array('gunid' => $gunid, 'type' => $type);
            #$res['cnt']++;
        }
        return $this->bsLocalSearch(
            array('filetype'=>'all', 'conditions'=>array())
        );
        #return $res;
    }

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
        $arr = array_map(create_function('$o', 'return "'.$format .'";'), $arr);
        return join('', $arr);
    }

    /**
     *
     *
     */
    function debug($va)
    {
        echo"<pre>\n"; print_r($va);
    }

    /**
     *  deleteFiles
     *
     *  @return void
     */
    function deleteFiles()
    {
        $ids = $this->dbc->getAll("SELECT id FROM {$this->filesTable}");
        if(is_array($ids)) foreach($ids as $i=>$item){
            $this->bsDeleteFile($item['id'], TRUE);
        }
    }
    /**
     *  deleteData
     *
     *  @return void
     */
    function deleteData()
    {
        $this->deleteFiles();
        parent::deleteData();
        $this->initData();
    }
    /**
     *  Create BasicStor object with temporarily changed configuration
     *  to prevent data changes in tests
     *
     */
    function createTestSpace(&$dbc, $config){
        $configBckp = $config;
        $config['tblNamePrefix'] .= '_test_';
        mkdir($config['storageDir'].'/tmp');
        $config['storageDir']    .=  '/tmp/stor';
        $config['bufferDir']      =  $config['storageDir'].'/buffer';
        $config['transDir']      .=  '/tmp/trans';
        $config['accessDir']     .=  '/tmp/access';
        mkdir($config['storageDir']);
        mkdir($config['bufferDir']);
        $bs =& new BasicStor($dbc, $config);
        $bs->configBckp = $configBckp;
        $r = $bs->install();
        if(PEAR::isError($r)){ return $r; }
        return $bs;
    }
    /**
     *  Clean up test space
     *
     */
    function releaseTestSpace(){
        $r = $this->uninstall();
        if(PEAR::isError($r)){ return $r; }
        // rmdir($this->config['bufferDir']);
        rmdir($this->config['storageDir']);
        $this->config = $this->configBckp;
        rmdir($this->config['storageDir'].'/tmp');
    }
    /**
     *  testData
     *
     */
    function testData($d='')
    {
        $exdir = dirname(__FILE__).'/tests';
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

        $o[] = $this->bsPutFile($t1hd, 'file1.mp3', "$exdir/ex1.mp3", '', NULL, 'audioclip');
        $o[] = $this->bsPutFile($t1d12, 'file2.wav', "$exdir/ex2.wav", '', NULL, 'audioclip');
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
        $this->test_log = '';
        // if($this->dbc->isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->testData();
        if($this->config['useTrash']){
            $trash = "\n        {$this->config['TrashName']}";
        }else{
            $trash = "";
        }
        if(!$this->config['isArchive']){
            $this->test_correct = "    StorageRoot
        root
        test1
            file1.mp3
            public
            test1_folder1
                test1_folder1_1
                test1_folder1_2
                    file2.wav
            test1_folder2
        test2
            public
            test2_folder1
        test3
            public
        test4
            public{$trash}
";
        }else{
            $this->test_correct = "    StorageRoot
        root
        test1
            file1.mp3
            test1_folder1
                test1_folder1_1
                test1_folder1_2
                    file2.wav
            test1_folder2
        test2
            test2_folder1
        test3
        test4{$trash}
";
        }
        $r = $this->dumpTree($this->storId, '    ', '    ', '{name}');
        if($this->dbc->isError($r)) return $r;
        $this->test_dump = $r;
        if($this->test_dump==$this->test_correct)
            { $this->test_log.="# BasicStor::test: OK\n"; return true; }
        else return PEAR::raiseError(
            "BasicStor::test:\ncorrect:\n.{$this->test_correct}.\n".
            "dump:\n.{$this->test_dump}.\n", 1, PEAR_ERROR_RETURN);
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
        $res = parent::addPerm($rootUid, '_all', $this->rootId, 'A');
        if($this->dbc->isError($res)) return $res;
        $res = parent::addPerm($rootUid, 'subjects', $this->rootId, 'A');
        if($this->dbc->isError($res)) return $res;
        $fid = $this->bsCreateFolder($this->storId, 'root');
        if($this->dbc->isError($fid)) return $fid;
        if($this->config['useTrash']){
            $tfid = $this->bsCreateFolder(
                $this->storId, $this->config["TrashName"]);
            if($this->dbc->isError($tfid)) return $tfid;
        }
        $allid = parent::addSubj($this->config['AllGr']);
        if($this->dbc->isError($allid)) return $allid;
        $r = $this->addSubj2Gr('root', $this->config['AllGr']);
        $r = $res = parent::addPerm($allid, 'read', $this->rootId, 'A');
        $admid = parent::addSubj($this->config['AdminsGr']);
        if($this->dbc->isError($admid)) return $admid;
        $r = $this->addSubj2Gr('root', $this->config['AdminsGr']);
        if($this->dbc->isError($r)) return $r;
        $res = parent::addPerm($admid, '_all', $this->rootId, 'A');
        if($this->dbc->isError($res)) return $res;
        if(!$this->config['isArchive']){
            $stPrefGr = parent::addSubj($this->config['StationPrefsGr']);
            if($this->dbc->isError($stPrefGr)) return $stPrefGr;
            $this->addSubj2Gr('root', $this->config['StationPrefsGr']);
        }
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
     *      <li>deleted</li>
     *  </ul>
     *  file types:
     *  <ul>
     *      <li>audioclip</li>
     *      <li>playlist</li>
     *      <li>webstream</li>
     *  </ul>
     *  access types:
     *  <ul>
     *      <li>access</li>
     *      <li>download</li>
     *  </ul>
     */
    function install()
    {
        parent::install();
        $r = $this->dbc->query("CREATE TABLE {$this->filesTable} (
            id int not null,
            gunid bigint not null,                      -- global unique ID
            name varchar(255) not null default'',       -- human file id ;)
            mime varchar(255) not null default'',       -- mime type
            ftype varchar(128) not null default'',      -- file type
            state varchar(128) not null default'empty', -- file state
            currentlyaccessing int not null default 0,  -- access counter
            editedby int REFERENCES {$this->subjTable}, -- who edits it
            mtime timestamp(6) with time zone           -- lst modif.time
        )");
        if($this->dbc->isError($r)) return $r;
        $this->dbc->query("CREATE UNIQUE INDEX {$this->filesTable}_id_idx
            ON {$this->filesTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->filesTable}_gunid_idx
            ON {$this->filesTable} (gunid)");
        $this->dbc->query("CREATE INDEX {$this->filesTable}_name_idx
            ON {$this->filesTable} (name)");

        $this->dbc->createSequence("{$this->mdataTable}_id_seq");
        $r = $this->dbc->query("CREATE TABLE {$this->mdataTable} (
            id int not null,
            gunid bigint,
            subjns varchar(255),             -- subject namespace shortcut/uri
            subject varchar(255) not null default '',
            predns varchar(255),             -- predicate namespace shortcut/uri
            predicate varchar(255) not null,
            predxml char(1) not null default 'T', -- Tag or Attribute
            objns varchar(255),              -- object namespace shortcut/uri
            object text
        )");
        if($this->dbc->isError($r)) return $r;
        $this->dbc->query("CREATE UNIQUE INDEX {$this->mdataTable}_id_idx
            ON {$this->mdataTable} (id)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_gunid_idx
            ON {$this->mdataTable} (gunid)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_subj_idx
            ON {$this->mdataTable} (subjns, subject)");
        $this->dbc->query("CREATE INDEX {$this->mdataTable}_pred_idx
            ON {$this->mdataTable} (predns, predicate)");

        $r = $this->dbc->query("CREATE TABLE {$this->accessTable} (
            gunid bigint,                             -- global unique id
            token bigint,                             -- access token
            chsum char(32) not null default'',        -- md5 checksum
            ext varchar(128) not null default'',      -- extension
            type varchar(20) not null default'',      -- access type
            parent bigint,                            -- parent token
            owner int REFERENCES {$this->subjTable},  -- subject have started it
            ts timestamp
        )");
        if($this->dbc->isError($r)) return $r;
        $this->dbc->query("CREATE INDEX {$this->accessTable}_token_idx
            ON {$this->accessTable} (token)");
        $this->dbc->query("CREATE INDEX {$this->accessTable}_gunid_idx
            ON {$this->accessTable} (gunid)");
        $this->dbc->query("CREATE INDEX {$this->accessTable}_parent_idx
            ON {$this->accessTable} (parent)");
        if(!file_exists($this->storageDir)){
            mkdir($this->storageDir, 02775);
        }
        if(!file_exists($this->bufferDir)){
            mkdir($this->bufferDir, 02775);
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
        $d = @dir($this->storageDir);
        while (is_object($d) && (false !== ($entry = $d->read()))){
            if(filetype("{$this->storageDir}/$entry")=='dir'){
                if($entry!='CVS' && $entry!='tmp' && strlen($entry)==3)
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
        }
        if(is_object($d)) $d->close();
        if(file_exists($this->bufferDir)){
            $d = dir($this->bufferDir);
            while (false !== ($entry = $d->read())) if(substr($entry,0,1)!='.')
                { unlink("{$this->bufferDir}/$entry"); }
            $d->close();
            @rmdir($this->bufferDir);
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