<?php
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

require_once(dirname(__FILE__)."/../../alib/var/Alib.php");
require_once("StoredFile.php");
require_once("Transport.php");

/**
 * Core of Campcaster file storage module
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see Alib
 */
//class BasicStor extends Alib {
class BasicStor {
    protected $rootId;
    public $storId;


    public function __construct()
    {
        $this->rootId = M2tree::GetRootNode();        
        $this->storId = M2tree::GetObjId('StorageRoot', $this->rootId);
    }


    /**
     * Create new folder
     *
     * @param int $parid
     * 		Parent id
     * @param string $folderName
     * 		Name for new folder
     * @return unknown
     * 		id of new folder
     * @exception PEAR_Error
     */
    public function bsCreateFolder($parid, $folderName)
    {
        return BasicStor::AddObj($folderName , 'Folder', $parid);
    }


    /**
     * Store new file in the storage
     *
     * @param int $parid
     * 		Parent id
     * @param string $fileName
     * 		Name for new file
     * @param string $mediaFileLP
     * 		Local path of media file
     * @param string $mdataFileLP
     * 		Local path of metadata file
     * @param string $gunid
     * 		global unique id
     * @param string $ftype
     * 		Internal file type
     * @param string $mdataLoc
     * 		'file'|'string'
     * @return int
     * @exception PEAR::error
     */
    public function bsPutFile($parid, $fileName, $mediaFileLP, $mdataFileLP,
        $gunid=NULL, $ftype='unKnown', $mdataLoc='file')
    {
        $ftype = strtolower($ftype);
        $id = BasicStor::AddObj($fileName, $ftype, $parid);
        if (PEAR::isError($id)) {
            return $id;
        }
        $ac = StoredFile::insert(
            $this, $id, $fileName, $mediaFileLP, $mdataFileLP, $mdataLoc,
            $gunid, $ftype);
        if (PEAR::isError($ac)) {
            $res = $this->removeObj($id);
            // catch constraint violations
            switch ($ac->getCode()) {
                case -3:
                    return PEAR::raiseError(
                        "BasicStor::bsPutFile: gunid duplication",
                        GBERR_GUNID);
                default:
                    return $ac;
            }
        }
        if ($ftype == 'playlist') {
            $ac->setMime('application/smil');
        }
        return $id;
    } // fn bsPutFile


    /**
     * Rename file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $newName
     * @return boolean|PEAR_Error
     */
    public function bsRenameFile($id, $newName)
    {
        $parid = M2tree::GetParent($id);
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
                $ac = StoredFile::recall($this, $id);
                if (PEAR::isError($ac)) {
                    // catch nonerror exception:
                    //if($ac->getCode() != GBERR_FOBJNEX)
                    return $ac;
                }
                $res = $ac->rename($newName);
                if (PEAR::isError($res)) {
                    return $res;
                }
                break;
            case "File":
            default:
        }
        return M2tree::RenameObj($id, $newName);
    }


    /**
     * Move file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param int $did
     * 		Destination folder local id
     * @return boolean/PEAR_Error
     */
    public function bsMoveFile($id, $did)
    {
        $parid = M2tree::GetParent($id);
        if (BasicStor::GetObjType($did) !== 'Folder') {
            return PEAR::raiseError(
                "BasicStor::moveFile: destination is not folder ($did)",
                GBERR_WRTYPE
            );
        }
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
            case "File":
            case "Folder":
                return BasicStor::MoveObj($id, $did);
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::moveFile: unsupported object to move, sorry.",
                    GBERR_WRTYPE
                );
        }
    }


    /**
     * Copy file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param int $did
     * 		Destination folder local id
     * @return boolean|PEAR_Error
     */
    public function bsCopyFile($id, $did)
    {
        $parid = M2tree::GetParent($id);
        if (BasicStor::GetObjType($did) !== 'Folder') {
            return PEAR::raiseError(
                'BasicStor::bsCopyFile: destination is not folder',
                GBERR_WRTYPE
            );
        }
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
            case "File":
            case "Folder":
                return BasicStor::CopyObj($id, $did);
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::moveFile: unsupported object to copy, sorry.",
                    GBERR_WRTYPE
                );
        }
    }


    /**
     * Replace file. Doesn't change filetype!
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $mediaFileLP
     * 		Local path of media file
     * @param string $mdataFileLP
     * 		Local path of metadata file
     * @param string $mdataLoc
     * 		'file'|'string'
     * @return true|PEAR_Error
     * @exception PEAR::error
     */
    public function bsReplaceFile($id, $mediaFileLP, $mdataFileLP, $mdataLoc='file')
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        if (!empty($mdataFileLP) &&
                ($mdataLoc!='file' || file_exists($mdataFileLP))) {
            $r = $ac->replaceMetaData($mdataFileLP, $mdataLoc);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        if (!empty($mediaFileLP) && file_exists($mediaFileLP)) {
            $r = $ac->replaceRawMediaData($mediaFileLP);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return TRUE;
    }


    /**
     * Delete file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param boolean $forced
     * 		If true don't use trash
     * @return true|PEAR_Error
     */
    public function bsDeleteFile($id, $forced=FALSE)
    {
        global $CC_CONFIG;
        // full delete:
        if (!$CC_CONFIG['useTrash'] || $forced) {
            $res = $this->removeObj($id, $forced);
            return $res;
        }
        // move to trash:
        $did = M2tree::GetObjId($CC_CONFIG['TrashName'], $this->storId);
        if (PEAR::isError($did)) {
            return $did;
        }
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
                $ac = StoredFile::recall($this, $id);
                if (PEAR::isError($ac)) {
                    return $ac;
                }
                if (is_null($did)) {
                    return PEAR::raiseError("BasicStor::bsDeleteFile: ".
                        "trash not found", GBERR_NOTF);
                }
                $res = $ac->setState('deleted');
                if (PEAR::isError($res)) {
                    return $res;
                }
                break;
            default:
        }
        $res = $this->bsMoveFile($id, $did);
        return $res;
    }


    /* ----------------------------------------------------- put, access etc. */
    /**
     * Check validity of access/put token
     *
     * @param string $token
     * 		Access/put token
     * @param string $type
     * 		'put'|'access'|'download'
     * @return boolean
     */
    public function bsCheckToken($token, $type='put')
    {
        global $CC_CONFIG, $CC_DBC;
        $cnt = $CC_DBC->getOne("
            SELECT count(token) FROM ".$CC_CONFIG['accessTable']."
            WHERE token=x'{$token}'::bigint AND type='$type'
        ");
        if (PEAR::isError($cnt)) {
            return FALSE;
        }
        return ($cnt == 1);
    }


    /**
     *  Get gunid from token
     *
     * @param string $token
     * 		Access/put token
     * @param string $type
     * 		'put'|'access'|'download'
     * @return string
     */
//    function _gunidFromToken($token, $type='put')
//    {
//        $acc = $CC_DBC->getRow("
//            SELECT to_hex(gunid)as gunid, ext FROM {$this->accessTable}
//            WHERE token=x'{$token}'::bigint AND type='$type'
//        ");
//        if (PEAR::isError($acc)) {
//            return $acc;
//        }
//        $gunid = StoredFile::NormalizeGunid($acc['gunid']);
//        if (PEAR::isError($gunid)) {
//            return $gunid;
//        }
//        return $gunid;
//    }


    /**
     * Create and return access link to real file
     *
     * @param string $realFname
     * 		Local filepath to accessed file
     *      (NULL for only increase access counter, no symlink)
     * @param string $ext
     * 		Useful filename extension for accessed file
     * @param int $gunid
     * 		Global unique id
     *      (NULL for special files such exported playlists)
     * @param string $type
     * 		'access'|'download'
     * @param int $parent
     * 		parent token (recursive access/release)
     * @param int $owner
     * 		Local user id - owner of token
     * @return array
     * 		array with: seekable filehandle, access token
     */
    public function bsAccess($realFname, $ext, $gunid, $type='access',
        $parent='0', $owner=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_null($gunid)) {
            $gunid = StoredFile::NormalizeGunid($gunid);
        }
        $token = StoredFile::CreateGunid();
        if (!is_null($realFname)) {
            $linkFname = $CC_CONFIG['accessDir']."/$token.$ext";
            if (!file_exists($realFname)) {
                return PEAR::raiseError(
                    "BasicStor::bsAccess: real file not found ($realFname)",
                    GBERR_FILEIO);
            }
            if (! @symlink($realFname, $linkFname)) {
                return PEAR::raiseError(
                    "BasicStor::bsAccess: symlink create failed ($linkFname)",
                    GBERR_FILEIO);
            }
        } else {
            $linkFname = NULL;
        }
        $escapedExt = pg_escape_string($ext);
        $escapedType = pg_escape_string($type);
        $CC_DBC->query("BEGIN");
        $gunidSql = (is_null($gunid) ? "NULL" : "x'{$gunid}'::bigint" );
        $ownerSql = (is_null($owner) ? "NULL" : "$owner" );
        $res = $CC_DBC->query("
            INSERT INTO ".$CC_CONFIG['accessTable']."
                (gunid, token, ext, type, parent, owner, ts)
            VALUES
                ($gunidSql, x'$token'::bigint,
                '$escapedExt', '$escapedType', x'{$parent}'::bigint, $ownerSql, now())
        ");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        if (!is_null($gunid)) {
            $res = $CC_DBC->query("
                UPDATE ".$CC_CONFIG['filesTable']."
                SET currentlyAccessing=currentlyAccessing+1, mtime=now()
                WHERE gunid=x'{$gunid}'::bigint
            ");
        }
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            return $res;
        }
        return array('fname'=>$linkFname, 'token'=>$token);
    }


    /**
     * Release access link to real file
     *
     * @param string $token
     * 		Access token
     * @param string $type
     * 		'access'|'download'
     * @return array
     *      gunid: string, global unique ID or real pathname of special file
     *      owner: int, local subject id of token owner
     *      realFname: string, real local pathname of accessed file
     */
    public function bsRelease($token, $type='access')
    {
        global $CC_CONFIG, $CC_DBC;
        if (!$this->bsCheckToken($token, $type)) {
            return PEAR::raiseError(
             "BasicStor::bsRelease: invalid token ($token)"
            );
        }
        $acc = $CC_DBC->getRow("
            SELECT to_hex(gunid)as gunid, ext, owner FROM ".$CC_CONFIG['accessTable']."
            WHERE token=x'{$token}'::bigint AND type='$type'
        ");
        if (PEAR::isError($acc)) {
            return $acc;
        }
        $ext = $acc['ext'];
        $owner = $acc['owner'];
        $linkFname = $CC_CONFIG['accessDir']."/$token.$ext";
        $realFname = readlink($linkFname);
        if (file_exists($linkFname)) {
            if(! @unlink($linkFname)){
                return PEAR::raiseError(
                    "BasicStor::bsRelease: unlink failed ($linkFname)",
                    GBERR_FILEIO);
            }
        }
        $CC_DBC->query("BEGIN");
        if (!is_null($acc['gunid'])) {
            $gunid = StoredFile::NormalizeGunid($acc['gunid']);
            $res = $CC_DBC->query("
                UPDATE ".$CC_CONFIG['filesTable']."
                SET currentlyAccessing=currentlyAccessing-1, mtime=now()
                WHERE gunid=x'{$gunid}'::bigint AND currentlyAccessing>0
            ");
            if (PEAR::isError($res)) {
                $CC_DBC->query("ROLLBACK");
                return $res;
            }
        }
        $res = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['accessTable']." WHERE token=x'$token'::bigint
        ");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = array(
            'gunid' => (isset($gunid) ? $gunid : NULL ),
            'realFname' => $realFname,
            'owner' => $owner,
        );
        return $res;
    }


    /**
     * Create and return downloadable URL for file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $part
     * 		'media'|'metadata'
     * @param int $parent
     * 		parent token (recursive access/release)
     * @return array
     * 		array with strings:
     *      downloadable URL, download token, chsum, size, filename
     */
    public function bsOpenDownload($id, $part='media', $parent='0')
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        $gunid = $ac->gunid;
        switch ($part) {
            case "media":
                $realfile = $ac->_getRealRADFname();
                $ext = $ac->_getExt();
                $filename = $ac->_getFileName();
                break;
            case "metadata":
                $realfile = $ac->_getRealMDFname();
                $ext = "xml";
                $filename = $ac->_getFileName();
                break;
            default:
                return PEAR::raiseError(
                 "BasicStor::bsOpenDownload: unknown part ($part)"
                );
        }
        $acc = $this->bsAccess($realfile, $ext, $gunid, 'download', $parent);
        if (PEAR::isError($acc)) {
            return $acc;
        }
        $url = BasicStor::GetUrlPart()."access/".basename($acc['fname']);
        $chsum = md5_file($realfile);
        $size = filesize($realfile);
        return array(
            'url'=>$url, 'token'=>$acc['token'],
            'chsum'=>$chsum, 'size'=>$size,
            'filename'=>$filename
        );
    }


    /**
     * Discard downloadable URL
     *
     * @param string $token
     * 		Download token
     * @param string $part
     * 		'media'|'metadata'
     * @return string
     * 		gunid
     */
    public function bsCloseDownload($token, $part='media')
    {
        if (!$this->bsCheckToken($token, 'download')) {
            return PEAR::raiseError(
             "BasicStor::bsCloseDownload: invalid token ($token)"
            );
        }
        $r = $this->bsRelease($token, 'download');
        if (PEAR::isError($r)){
            return $r;
        }
        return (is_null($r['gunid']) ? $r['realFname'] : $r['gunid']);
    }


    /**
     * Create writable URL for HTTP PUT method file insert
     *
     * @param string $chsum
     * 		md5sum of the file having been put
     * @param string $gunid
     * 		global unique id
     *      (NULL for special files such imported playlists)
     * @param int $owner
     * 		local user id - owner of token
     * @return array
     * 		array with:
     *      url string: writable URL
     *      fname string: writable local filename
     *      token string: PUT token
     */
    public function bsOpenPut($chsum, $gunid, $owner=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_null($gunid)) {
            $gunid = StoredFile::NormalizeGunid($gunid);
        }
        $escapedChsum = pg_escape_string($chsum);
        $token = StoredFile::CreateGunid();
        $res = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['accessTable']." WHERE token=x'$token'::bigint
        ");
        if (PEAR::isError($res)) {
            return $res;
        }
        $gunidSql = (is_null($gunid) ? "NULL" : "x'{$gunid}'::bigint" );
        $ownerSql = (is_null($owner) ? "NULL" : "$owner" );
        $res = $CC_DBC->query("
            INSERT INTO ".$CC_CONFIG['accessTable']."
                (gunid, token, ext, chsum, type, owner, ts)
            VALUES
                ($gunidSql, x'$token'::bigint,
                    '', '$escapedChsum', 'put', $ownerSql, now())
        ");
        if (PEAR::isError($res)) {
            return $res;
        }
        $fname = $CC_CONFIG['accessDir']."/$token";
        touch($fname);      // is it needed?
        $url = BasicStor::GetUrlPart()."xmlrpc/put.php?token=$token";
        return array('url'=>$url, 'fname'=>$fname, 'token'=>$token);
    }


    /**
     * Get file from writable URL and return local filename.
     * Caller should move or unlink this file.
     *
     * @param string $token
     * 		PUT token
     * @return array
     * 		hash with fields:
     *      fname string, local path of the file having been put
     *      owner int, local subject id - owner of token
     */
    public function bsClosePut($token)
    {
        global $CC_CONFIG, $CC_DBC;
        $token = StoredFile::NormalizeGunid($token);
        if (!$this->bsCheckToken($token, 'put')) {
            return PEAR::raiseError(
             "BasicStor::bsClosePut: invalid token ($token)",
             GBERR_TOKEN
            );
        }
        $row = $CC_DBC->getRow("
            SELECT chsum, owner FROM ".$CC_CONFIG['accessTable']."
            WHERE token=x'{$token}'::bigint
        ");
        if (PEAR::isError($row)) {
            return $row;
        }
        $chsum = $row['chsum'];
        $owner = $row['owner'];
        $res = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['accessTable']." WHERE token=x'$token'::bigint
        ");
        if (PEAR::isError($res)) {
            return $res;
        }
        $fname = $CC_CONFIG['accessDir']."/$token";
        $md5sum = md5_file($fname);
        if (trim($chsum) !='' && $chsum != $md5sum) {
            if (file_exists($fname)) {
                @unlink($fname);
            }
            return PEAR::raiseError(
                 "BasicStor::bsClosePut: md5sum does not match (token=$token)".
                 " [$chsum/$md5sum]",
                 GBERR_PUT
                );
        }
        return array('fname'=>$fname, 'owner'=>$owner);
    }


    /**
     * Check uploaded file
     *
     * @param string $token
     * 		"Put" token
     * @return array
     * 		hash, (
     *      status: boolean,
     *      size: int - filesize
     *      expectedsum: string - expected checksum
     *      realsum: string - checksum of uploaded file
     *   	)
     */
    public function bsCheckPut($token)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!$this->bsCheckToken($token, 'put')) {
            return PEAR::raiseError(
             "BasicStor::bsCheckPut: invalid token ($token)"
            );
        }
        $chsum = $CC_DBC->getOne("
            SELECT chsum FROM ".$CC_CONFIG['accessTable']."
            WHERE token=x'{$token}'::bigint
            ");
        if (PEAR::isError($chsum)) {
            return $chsum;
        }
        $fname = $CC_CONFIG['accessDir']."/$token";
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
     * Return starting part of storageServer URL
     *
     * @return string
     * 		URL
     */
    public static function GetUrlPart()
    {
        global $CC_CONFIG;
        $host = $CC_CONFIG['storageUrlHost'];
        $port = $CC_CONFIG['storageUrlPort'];
        $path = $CC_CONFIG['storageUrlPath'];
        return "http://$host:$port$path/";
    }


    /**
     * Return local subject id of token owner
     *
     * @param string $token
     * 		access/put/render etc. token
     * @return int
     * 		local subject id
     */
//    function getTokenOwner($token)
//    {
//        $row = $CC_DBC->getOne("
//            SELECT owner FROM {$this->accessTable}
//            WHERE token=x'{$token}'::bigint
//        ");
//        if (PEAR::isError($row)) {
//            return $row;
//        }
//        $owner = $row;
//    }


    /**
     * Get tokens by type
     *
     * @param string $type
     * 		access|put|render etc.
     * @return array
     * 		array of tokens
     */
    public static function GetTokensByType($type)
    {
        global $CC_CONFIG, $CC_DBC;
        $res = $CC_DBC->query(
            "SELECT TO_HEX(token) AS token FROM ".$CC_CONFIG['accessTable']." WHERE type=?",
            array($type));
        while ($row = $res->fetchRow()) {
             $r[] = $row['token'];
        }
        return $r;
    }


    /* ----------------------------------------------------- metadata methods */

    /**
     * Replace metadata with new XML file or string
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $mdata
     * 		Local path of metadata XML file
     * @param string $mdataLoc
     * 		'file'|'string'
     * @return boolean|PEAR_Error
     */
    public function bsReplaceMetadata($id, $mdata, $mdataLoc='file')
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        return $ac->replaceMetaData($mdata, $mdataLoc);
    }


    /**
     * Get metadata as XML string
     *
     * @param int $id
     * 		Virtual file's local id
     * @return string|PEAR_Error
     */
    public function bsGetMetadata($id)
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        return $ac->getMetadata();
    }


    /**
     * Get dc:title (if exists)
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $gunid
     * 		Virtual file's gunid, optional, used only if not
     *      null, id is then ignored
     * @param string $lang
     * 		xml:lang value for select language version
     * @param string $deflang
     * 		xml:lang for default language
     * @return string|PEAR_Error
     */
    public function bsGetTitle($id, $gunid=NULL, $lang=NULL, $deflang=NULL)
    {
        if (is_null($gunid)) {
            $ac = StoredFile::recall($this, $id);
        } else {
            $ac = StoredFile::recallByGunid($this, $gunid);
        }
        if (PEAR::isError($ac)) {
            return $ac;
        }
        $r = $ac->md->getMetadataValue('dc:title', $lang, $deflang);
        if (PEAR::isError($r)) {
            return $r;
        }
        $title = (isset($r[0]['value']) ? $r[0]['value'] : 'unknown');
        return $title;
    }


    /**
     * Get metadata element value
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $category
     * 		metadata element name
     * @param string $lang
     * 		xml:lang value for select language version
     * @param string $deflang
     * 		xml:lang for default language
     * @return array
     * 		array of matching records (as hash {id, value, attrs})
     * @see Metadata::getMetadataValue
     */
//    public function bsGetMetadataValue($id, $category, $lang=NULL, $deflang=NULL)
//    {
//        $ac = StoredFile::recall($this, $id);
//        if (PEAR::isError($ac)) {
//            return $ac;
//        }
//        return $ac->md->getMetadataValue($category, $lang, $deflang);
//    }


    /**
     * Get metadata element value
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string|array|null $category
     * 		metadata element name, or array of metadata element names,
     * 		if null is passed, all metadata values for the given ID will
     * 		be fetched.
     * @return string|array
     * 		If a string is passed in for $category, a string is returned,
     * 		if an array is passed, an array is returned.
     * @see Metadata::getMetadataValue
     */
    public function bsGetMetadataValue($id, $category = null)
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        if (is_null($category)) {
        	return $ac->md->getAllMetadata();
        } elseif (is_array($category)) {
        	$values = array();
			foreach ($category as $tmpCat) {
				$values[$tmpCat] = $ac->md->getMetadataValue($tmpCat);
			}
			return $values;
        } else {
        	return $ac->md->getMetadataValue($category);
        }
    }


    /**
     * Set metadata element value
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $category
     * 		Metadata element identification (e.g. dc:title)
     * @param string $value
     * 		value to store, if NULL then delete record
     * @param string $lang
     * 		xml:lang value for select language version
     * @param int $mid
     * 		(optional on unique elements) metadata record id
     * @param string $container
     * 		container element name for insert
     * @param boolean $regen
     * 		flag, if true, regenerate XML file
     * @return boolean
     */
    public function bsSetMetadataValue($id, $category, $value,
        $lang=NULL, $mid=NULL, $container='metadata', $regen=TRUE)
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        if ($category == 'dcterms:extent') {
            $value = BasicStor::NormalizeExtent($value);
        }
        $res = $ac->md->setMetadataValue($category, $value, $lang, $mid, $container);
        if (PEAR::isError($res)) {
            return $res;
        }
        if ($regen) {
            $r = $ac->md->regenerateXmlFile();
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $res;
    }


    /**
     * Normalize time value to hh:mm:ss:dddddd format
     *
     * @param mixed $v
     * 		value to normalize
     * @return string
     */
    private static function NormalizeExtent($v)
    {
        if (!preg_match("|^\d{2}:\d{2}:\d{2}.\d{6}$|", $v)) {
            require_once("Playlist.php");
            $s = Playlist::playlistTimeToSeconds($v);
            $t = Playlist::secondsToPlaylistTime($s);
            return $t;
        }
        return $v;
    }


    /**
     * Set metadata values in 'batch' mode
     *
     * @param int $id
     * 		Virtual file's local ID
     * @param array $values
     * 		array of key/value pairs
     *      (e.g. 'dc:title'=>'New title')
     * @param string $lang
     * 		xml:lang value for select language version
     * @param string $container
     * 		Container element name for insert
     * @param boolean $regen
     * 		flag, if true, regenerate XML file
     * @return boolean
     */
    public function bsSetMetadataBatch(
        $id, $values, $lang=NULL, $container='metadata', $regen=TRUE)
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        foreach ($values as $category => $oneValue) {
            $res = $this->bsSetMetadataValue($id, $category, $oneValue,
                $lang, NULL, $container, FALSE);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        if ($regen) {
            $ac = StoredFile::recall($this, $id);
            if (PEAR::isError($ac)) {
                return $ac;
            }
            $r = $ac->md->regenerateXmlFile();
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return TRUE;
    }


    /**
     * Search in local metadata database.
     *
     * @param array $criteria
     * 	 has the following structure:<br>
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
     *          or array of strings for multicolumn orderby
     *          [default: dc:creator, dc:source, dc:title]
     *     </li>
     *     <li>desc : boolean - flag for descending order (optional)
     *          or array of boolean for multicolumn orderby
     *          (it corresponds to elements of orderby field)
     *          [default: all ascending]
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
     * @param int $limit
     * 		limit for result arrays (0 means unlimited)
     * @param int $offset
     * 		starting point (0 means without offset)
     * @return array
     * 		array of hashes, fields:
     *       cnt : integer - number of matching gunids
     *              of files have been found
     *       results : array of hashes:
     *          gunid: string
     *          type: string - audioclip | playlist | webstream
     *          title: string - dc:title from metadata
     *          creator: string - dc:creator from metadata
     *          source: string - dc:source from metadata
     *          length: string - dcterms:extent in extent format
     * @see DataEngine
     */
    public function bsLocalSearch($criteria, $limit=0, $offset=0)
    {
        require_once("DataEngine.php");
        $de = new DataEngine($this);
        $res = $de->localSearch($criteria, $limit, $offset);
        if (PEAR::isError($res)) {
            return $res;
        }
        return $res;
    }


    /**
     * Return values of specified metadata category
     *
     * @param string $category
     * 		metadata category name with or without namespace prefix (dc:title, author)
     * @param int $limit
     * 		limit for result arrays (0 means unlimited)
     * @param int $offset
     * 		starting point (0 means without offset)
     * @param array $criteria
     * 		see bsLocalSearch method
     * @return array
     * 		hash, fields:
     *       results : array with found values
     *       cnt : integer - number of matching values
     * @see DataEngine
     */
    public function bsBrowseCategory($category, $limit=0, $offset=0, $criteria=NULL)
    {
        require_once("DataEngine.php");
        $de = new DataEngine($this);
        return $de->browseCategory($category, $limit, $offset, $criteria);
    }


    /* ---------------------------------------------------- methods4playlists */

    /**
     * Create a tarfile with playlist export - playlist and all matching
     * sub-playlists and media files (if desired)
     *
     * @param array $plids
     * 		Array of strings, playlist global unique IDs (one gunid is accepted too)
     * @param string $type
     * 		Playlist format, possible values: lspl | smil | m3u
     * @param boolean $withContent
     * 		if true, export related files too
     * @return array
     * 		hasharray with  fields:
     *      fname string: readable fname,
     *      token string: access token
     */
    public function bsExportPlaylistOpen($plids, $type='lspl', $withContent=TRUE)
    {
        global $CC_CONFIG;
        require_once("Playlist.php");
        if (!is_array($plids)) {
            $plids = array($plids);
        }
        $gunids = array();
        foreach ($plids as $plid) {
            $pl = Playlist::recallByGunid($this, $plid);
            if (PEAR::isError($pl)) {
                return $pl;
            }
            if ($withContent) {
                $gunidsX = $pl->export();
                if (PEAR::isError($gunidsX)) {
                    return $gunidsX;
                }
            } else {
                $gunidsX = array(array('gunid'=>$plid, 'type'=>'playlist'));
            }
            $gunids = array_merge($gunids, $gunidsX);
        }
#        header("Content-type: text/plain"); var_dump($gunids); var_dump($withContent); exit;
        $plExts = array('lspl'=>"lspl", 'smil'=>"smil", 'm3u'=>"m3u");
        $plExt = (isset($plExts[$type]) ? $plExts[$type] : "xml" );
        $res = array();
        $tmpn = tempnam($CC_CONFIG['bufferDir'], 'plExport_');
        $tmpf = "$tmpn.tar";
        $tmpd = "$tmpn.dir";
        mkdir($tmpd);
        $tmpdp = "$tmpn.dir/playlist";
        mkdir($tmpdp);
        if ($withContent) {
            $tmpdc = "$tmpn.dir/audioClip";
            mkdir($tmpdc);
        }
        foreach ($gunids as $i => $it) {
            $ac = StoredFile::recallByGunid($this, $it['gunid']);
            if (PEAR::isError($ac)) {
                return $ac;
            }
            $MDfname = $ac->md->getFname();
            if (PEAR::isError($MDfname)) {
                return $MDfname;
            }
            if (file_exists($MDfname)) {
                switch ($it['type']) {
	                case "playlist":
	                    require_once("LsPlaylist.php");
	                    $ac = $r = LsPlaylist::recallByGunid($this, $it['gunid']);
	                    switch ($type) {
	                        case "smil":
	                            $string = $r = $ac->outputToSmil();
	                            break;
	                        case "m3u":
	                            $string = $r = $ac->outputToM3u();
	                            break;
	                        default:
	                            $string = $r = $ac->md->genXmlDoc();
	                    }
	                    if (PEAR::isError($r)) {
	                        return $r;
	                    }
	                    $r = BasicStor::WriteStringToFile($string, "$tmpdp/{$it['gunid']}.$plExt");
	                    if (PEAR::isError($r)) {
	                        return $r;
	                    }
	                    break;
	                default:
	                    copy($MDfname, "$tmpdc/{$it['gunid']}.xml"); break;
                } // switch
            } // if file_exists()
            $RADfname = $ac->_getRealRADFname();
            if (PEAR::isError($RADfname)) {
                return $RADfname;
            }
            $RADext = $ac->_getExt();
            if (PEAR::isError($RADext)) {
                return $RADext;
            }
            if (file_exists($RADfname)) {
                copy($RADfname, "$tmpdc/{$it['gunid']}.$RADext");
            }
        }
        if (count($plids)==1) {
            copy("$tmpdp/$plid.$plExt", "$tmpd/exportedPlaylist.$plExt");
        }
        $res = `cd $tmpd; tar cf $tmpf * --remove-files`;
        @rmdir($tmpdc);
        @rmdir($tmpdp);
        @rmdir($tmpd);
        unlink($tmpn);
        $acc = $this->bsAccess($tmpf, 'tar', NULL/*gunid*/, 'access');
        if (PEAR::isError($acc)) {
            return $acc;
        }
        return $acc;
    }


    /**
     * Close playlist export previously opened by the bsExportPlaylistOpen
     * method
     *
     * @param string $token
     * 		Access token obtained from bsExportPlaylistOpen method call.
     * @return true/PEAR_Error
     */
    public function bsExportPlaylistClose($token)
    {
        $r = $this->bsRelease($token, 'access');
        if (PEAR::isError($r)) {
            return $r;
        }
        $file = $r['realFname'];
        if (file_exists($file)) {
            if(! @unlink($file)){
                return PEAR::raiseError(
                    "BasicStor::bsExportPlaylistClose: unlink failed ($file)",
                    GBERR_FILEIO);
            }
        }
        return TRUE;
    }


    /**
     * Import playlist in LS Archive format
     *
     * @param int $parid
     * 		Destination folder local id
     * @param string $plid
     * 		Playlist gunid
     * @param string $aPath
     * 		Absolute path part of imported file (e.g. /home/user/campcaster)
     * @param string $rPath
     * 		Relative path/filename part of imported file (e.g. playlists/playlist_1.smil)
     * @param string $ext
     * 		Playlist extension (determines type of import)
     * @param array $gunids
     * 		Hash relation from filenames to gunids
     * @param int $subjid
     * 		Local subject (user) id (id of user doing the import)
     * @return int
     * 		Result file local id (or error object)
     */
    public function bsImportPlaylistRaw($parid, $plid, $aPath, $rPath, $ext, &$gunids, $subjid)
    {
        $id = BasicStor::IdFromGunid($plid);
        if (!is_null($id)) {
            return $id;
        }
        $path = realpath("$aPath/$rPath");
        if (FALSE === $path) {
            return PEAR::raiseError(
                "BasicStor::bsImportPlaylistRaw: file doesn't exist ($aPath/$rPath)"
            );
        }
        switch($ext){
            case "xml":
            case "lspl":
                $fname = $plid;
                $res = $this->bsPutFile($parid, $fname,
                    NULL, $path, $plid, 'playlist'
                );
                break;
            case "smil":
                require_once("SmilPlaylist.php");
                $res = SmilPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $parid, $subjid);
                if (PEAR::isError($res)) {
                    break;
                }
                $res = $res->getId();
                break;
            case "m3u":
                require_once("M3uPlaylist.php");
                $res = M3uPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $parid, $subjid);
                if (PEAR::isError($res)) {
                    break;
                }
                $res = $res->getId();
                break;
            default:
                $res = PEAR::raiseError(
                    "BasicStor::importPlaylistRaw: unknown playlist format".
                    " (gunid:$plid, format:$ext)"
                );
                break;
        }
        if (!PEAR::isError($res)) {
            $gunids[basename($rPath)] = $plid;
        }
        return $res;
    }


    /**
     * Import playlist in LS Archive format
     *
     * @param int $parid
     * 		Destination folder local id
     * @param string $fpath
     * 		Imported file pathname
     * @param int $subjid
     * 		Local subject (user) id (id of user doing the import)
     * @return int
     * 		Result file local id (or error object)
     */
    public function bsImportPlaylist($parid, $fpath, $subjid)
    {
        global $CC_CONFIG;
        // untar:
        $tmpn = tempnam($CC_CONFIG['bufferDir'], 'plImport_');
        $tmpd = "$tmpn.dir";
        $tmpdc = "$tmpd/audioClip";
        $tmpdp = "$tmpd/playlist";
        mkdir($tmpd);
        $res = `cd $tmpd; tar xf $fpath`;
        // clips:
        $d = @dir($tmpdc);
        $entries = array();
        $gunids = array();
        if ($d !== false) {
            while (false !== ($entry = $d->read())) {
                if (preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)) {
                    list(,$gunid, $ext) = $va;
                    switch ($ext) {
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
        foreach ($entries as $gunid => $it) {
            $rawMedia = "$tmpdc/{$it['rawMedia']}";
            if (!file_exists($rawMedia)) {
                $rawMedia = NULL;
            }
            $metadata = "$tmpdc/{$it['metadata']}";
            if (!file_exists($metadata)) {
                $metadata = NULL;
            }
            $exists = $this->bsExistsFile($gunid, NULL, TRUE);
            if( $exists ) {
                $res = BasicStor::IdFromGunid($gunid);
                if (!PEAR::isError($res)) {
                    $res = $this->bsDeleteFile($res, TRUE);
                }
            }
            if (!PEAR::isError($res) ) {
                $res = $this->bsPutFile($parid, $gunid, $rawMedia, $metadata,
                    $gunid, 'audioclip'
                );
            }
            @unlink("$tmpdc/{$it['rawMedia']}");
            @unlink("$tmpdc/{$it['metadata']}");
            if (PEAR::isError($res)) {
                break;
            }
        }
        // playlists:
        require_once("Playlist.php");
        $d = @dir($tmpdp);
        if ($d !== false) {
            while ((!PEAR::isError($res)) && false !== ($entry = $d->read())) {
                if (preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)) {
                    list(,$gunid, $ext) = $va;
                    $res = $this->bsImportPlaylistRaw($parid, $gunid,
                        $tmpdp, $entry, $ext, $gunids, $subjid);
                    unlink("$tmpdp/$entry");
                    if (PEAR::isError($res)) {
                        break;
                    }
                }
            }
            $d->close();
        }
        //@rmdir($tmpdc); @rmdir($tmpdp); @rmdir($tmpd);
        @system("rm -rf $tmpdc");
        @system("rm -rf $tmpdp");
        @system("rm -rf $tmpd");
        @unlink($tmpn);
        return $res;
    }


    /* --------------------------------------------------------- info methods */

    /**
     * List files in folder
     *
     * @param int $id
     * 		Local ID of folder
     * @return array
     * @todo THERE IS A BUG IN THIS FUNCTION
     */
    public function bsListFolder($id)
    {
        if (BasicStor::GetObjType($id) !== 'Folder') {
            return PEAR::raiseError(
                'BasicStor::bsListFolder: not a folder', GBERR_NOTF
            );
        }
        $listArr = M2tree::GetDir($id, 'id, name, type, param as target', 'name');
        if (PEAR::isError($listArr)) {
            return $listArr;
        }
        foreach ($listArr as $i=>$v) {
            if ($v['type'] == 'Folder') {
                break;
            }
            $gunid = BasicStor::GunidFromId($v['id']);
            if (PEAR::isError($gunid)) {
                return $gunid;
            }
            if (is_null($gunid)) {
                unset($listArr[$i]);
                break;
            }
            $listArr[$i]['type'] = BasicStor::GetType($gunid);
            if (PEAR::isError($listArr[$i]['type'])) {
                return $listArr[$i]['type'];
            }
            $listArr[$i]['gunid'] = $gunid;
            
            // THE BUG IS HERE - "_getState()" IS NOT A STATIC FUNCTION!
            if (StoredFile::_getState($gunid) == 'incomplete') {
                unset($listArr[$i]);
            }
        }
        return $listArr;
    }


    /**
     * Analyze media file for internal metadata information
     *
     * @param int $id
     * 		Virtual file's local id
     * @return array
     */
    public function bsAnalyzeFile($id)
    {
        $ac = StoredFile::recall($this, $id);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        $ia = $ac->analyzeMediaFile();
        return $ia;
    }


    /**
     * List files in folder
     *
     * @param int $id
     * 		Local id of object
     * @param string $relPath
     * 		Relative path
     * @return array
     */
    public function getObjIdFromRelPath($id, $relPath='.')
    {
        $relPath = trim(urldecode($relPath));
        //if(BasicStor::GetObjType($id) !== 'Folder')
        $nid = M2tree::GetParent($id);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        if (is_null($nid)) {
            return PEAR::raiseError("null parent for id=$id");
        }
        //else $nid = $id;
        if (substr($relPath, 0, 1)=='/') {
            $nid = $this->storId;
        }
        $a = split('/', $relPath);
        foreach ($a as $i => $pathItem) {
            switch ($pathItem) {
                case ".":
                    break;
                case "..":
                    if ($nid != $this->storId) {
                        $nid = M2tree::GetParent($nid);
                        if (PEAR::isError($nid)) {
                            return $nid;
                        }
                        if (is_null($nid)) {
                             return PEAR::raiseError(
                                "null parent for $nid");
                        }
                    }
                    break;
                case "":
                    break;
                default:
                    $nid = M2tree::GetObjId($pathItem, $nid);
                    if (PEAR::isError($nid)) {
                        return $nid;
                    }
                    if (is_null($nid)) {
                         return PEAR::raiseError(
                            "Object $pathItem not found (from id=$id)");
                    }
            }
        }
        return $nid;
    }


    /**
     * Check if file exists in the storage
     *
     * @param int $id
     * 		Local id
     * @param string $ftype
     * 		Internal file type
     * @param boolean $byGunid
     * 		select file by gunid (id is then ignored)
     * @return boolean
     */
    public function bsExistsFile($id, $ftype=NULL, $byGunid=FALSE)
    {
        if ($byGunid) {
            $ac = StoredFile::recallByGunid($this, $id);
        } else {
            $ac = StoredFile::recall($this, $id);
        }
        if (PEAR::isError($ac)) {
            // catch some exceptions
            switch ($ac->getCode()) {
                case GBERR_FILENEX:
                case GBERR_FOBJNEX:
                    return FALSE;
                    break;
                default:
                	return $ac;
            }
        }
        $realFtype = BasicStor::GetType($ac->gunid);
        if (!is_null($ftype) && (
            ($realFtype != $ftype)
            // webstreams are subset of audioclips
            && !($realFtype == 'webstream' && $ftype == 'audioclip')
        )) {
            return FALSE;
        }
        return TRUE;
    }


    /* ---------------------------------------------------- redefined methods */
    /**
     * Get object type by id.
     *  (RootNode, Folder, File, )
     *
     * @param int $oid
     * 		Local object id
     * @return string|PEAR_Error
     */
    public static function GetObjType($oid)
    {
        $type = M2tree::GetObjType($oid);
        if ($type == 'File') {
            $gunid = BasicStor::GunidFromId($oid);
            if (PEAR::isError($gunid)) {
                return $gunid;
            }
            $ftype = BasicStor::GetType($gunid);
            if (PEAR::isError($ftype)) {
                return $ftype;
            }
            if (!is_null($ftype)) {
                $type = $ftype;
            }
        }
        return $type;
    }


    /**
     * Add new user with home folder
     *
     * @param string $login
     * @param string $pass
     * @param string $realname
     * @return int|PEAR_Error
     */
    public function addSubj($login, $pass=NULL, $realname='')
    {
        global $CC_CONFIG;
        $uid = Subjects::addSubj($login, $pass, $realname);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        if (Subjects::IsGroup($uid) === FALSE) {
            $fid = $this->bsCreateFolder($this->storId, $login);
            if (PEAR::isError($fid)) {
                return $fid;
            }
            $res = Alib::AddPerm($uid, '_all', $fid, 'A');
            if (PEAR::isError($res)) {
                return $res;
            }
            if (!$CC_CONFIG['isArchive']) {
                $res = Subjects::AddSubjectToGroup($login, $CC_CONFIG['StationPrefsGr']);
                if (PEAR::isError($res)) {
                    return $res;
                }
                $res = Subjects::AddSubjectToGroup($login, $CC_CONFIG['AllGr']);
                if (PEAR::isError($res)) {
                    return $res;
                }
                $pfid = $this->bsCreateFolder($fid, 'public');
                if (PEAR::isError($pfid)) {
                    return $pfid;
                }
                $res = Alib::AddPerm($uid, '_all', $pfid, 'A');
                if (PEAR::isError($res)) {
                    return $res;
                }
                $allGrId =  Subjects::GetSubjId($CC_CONFIG['AllGr']);
                if (PEAR::isError($allGrId)) {
                    return $allGrId;
                }
                $res = Alib::AddPerm($allGrId, 'read', $pfid, 'A');
                if (PEAR::isError($res)) {
                    return $res;
                }
            }
        }
        return $uid;
    }


    /**
     * Remove user by login and remove also his home folder
     *
     * @param string $login
     * @return boolean|PEAR_Error
     */
    public function removeSubj($login)
    {
        global $CC_CONFIG, $CC_DBC;
        if (FALSE !== array_search($login, $CC_CONFIG['sysSubjs'])) {
            return $CC_DBC->raiseError(
                "BasicStor::removeSubj: cannot remove system user/group");
        }
        $uid = Subjects::GetSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $res = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['accessTable']." WHERE owner=$uid
        ");
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = Alib::RemoveSubj($login);
        if (PEAR::isError($res)) {
            return $res;
        }
        $id = M2tree::GetObjId($login, $this->storId);
        if (PEAR::isError($id)) {
            return $id;
        }
        if (!is_null($id)) {
            // remove home folder:
            $res = $this->bsDeleteFile($id);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return TRUE;
    }


    /**
     * Authenticate and create session
     *
     * @param string $login
     * @param string $pass
     * @return boolean|sessionId|PEAR_Error
     */
//    function login($login, $pass)
//    {
//        $r = $this->upgradeDbStructure();
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        $r = parent::login($login, $pass);
//        return $r;
//    }


    /* ================================================== "protected" methods */
    /**
     * Check authorization - auxiliary method
     *
     * @param array $acts
     * 		Array of actions
     * @param array $pars
     * 		Array of parameters - e.g. ids
     * @param string $sessid
     * 		Session id
     * @return true|PEAR_Error
     */
    public static function Authorize($acts, $pars, $sessid='')
    {
        $userid = Alib::GetSessUserId($sessid);
        if (PEAR::isError($userid)) {
            return $userid;
        }
        if (is_null($userid)) {
            return PEAR::raiseError(
                "BasicStor::Authorize: invalid session", GBERR_DENY);
        }
        if (!is_array($pars)) {
            $pars = array($pars);
        }
        if (!is_array($acts)) {
            $acts = array($acts);
        }
        $perm = true;
//        foreach ($acts as $i => $action) {
//            $res = Alib::CheckPerm($userid, $action, $pars[$i]);
//            if (PEAR::isError($res)) {
//                return $res;
//            }
//            $perm = $perm && $res;
//        }
        if ($perm) {
            return TRUE;
        }
        $adesc = "[".join(',',$acts)."]";
        return PEAR::raiseError(
            "BasicStor::$adesc: access denied", GBERR_DENY);
    }


    /**
     * Return users's home folder local ID
     *
     * @param string $subjid
     * 		Local subject id
     * @return unknown
     * 		local folder id
     */
    public function _getHomeDirId($subjid)
    {
        $login = Subjects::GetSubjName($subjid);
        if (PEAR::isError($login)) {
            return $login;
        }
        $parid = M2tree::GetObjId($login, $this->storId);
        if (PEAR::isError($parid)) {
            return $parid;
        }
        if (is_null($parid)) {
            return PEAR::raiseError("BasicStor::_getHomeDirId: ".
                "homedir not found ($subjid)", GBERR_NOTF);
        }
        return $parid;
    }


    /**
     * Return users's home folder local ID
     *
     * @param string $sessid
     * 		session ID
     * @return unknown
     * 		local folder id
     */
    public function _getHomeDirIdFromSess($sessid)
    {
        $uid = Alib::GetSessUserId($sessid);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        return $this->_getHomeDirId($uid);
    }


    /**
     * Get local id from global id.
     *
     * @param string $p_gunid
     * 		Global id
     * @return int
     * 		Local id
     */
    public static function IdFromGunid($p_gunid)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        return $CC_DBC->getOne("SELECT id FROM ".$CC_CONFIG['filesTable']." WHERE gunid=x'$p_gunid'::bigint");
    }


    /**
     * Get global id from local id
     *
     * @param int $p_id
     * 		Local id
     * @return string
     * 		Global id
     */
    public static function GunidFromId($p_id)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if (!is_numeric($p_id)) {
            return NULL;
        }
        $gunid = $CC_DBC->getOne("
            SELECT to_hex(gunid)as gunid FROM ".$CC_CONFIG['filesTable']."
            WHERE id='$p_id'
        ");
        if (PEAR::isError($gunid)) {
            return $gunid;
        }
        if (is_null($gunid)) {
            return NULL;
        }
        return StoredFile::NormalizeGunid($gunid);
    }


    /**
     * Get storage-internal file type
     *
     * @param string $p_gunid
     * 		Global unique id of file
     * @return string
     */
    public static function GetType($p_gunid)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $ftype = $CC_DBC->getOne("
            SELECT ftype FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'$p_gunid'::bigint
        ");
        return $ftype;
    }


    /**
     * Check gunid format
     *
     * @param string $p_gunid
     * 		Global unique ID
     * @return boolean
     */
    protected static function CheckGunid($p_gunid)
    {
        $res = preg_match("|^([0-9a-fA-F]{16})?$|", $p_gunid);
        return $res;
    }


    /**
     * Returns TRUE if gunid is free
     * @return boolean|PEAR_Error
     */
//    function _gunidIsFree($gunid)
//    {
//        $cnt = $CC_DBC->getOne("
//            SELECT count(*) FROM {$this->filesTable}
//            WHERE gunid=x'{$this->gunid}'::bigint
//        ");
//        if (PEAR::isError($cnt)) {
//            return $cnt;
//        }
//        if ($cnt > 0) {
//            return FALSE;
//        }
//        return TRUE;
//    }


    /**
     * Set playlist edit flag
     *
     * @param string $p_playlistId
     * 		Playlist global unique ID
     * @param boolean $p_val
     * 		Set/clear of edit flag
     * @param string $p_sessid
     * 		Session id
     * @param int $p_subjid
     * 		Subject id (if sessid is not specified)
     * @return boolean
     * 		previous state
     */
    public function setEditFlag($p_playlistId, $p_val=TRUE, $p_sessid=NULL, $p_subjid=NULL)
    {
        if (!is_null($p_sessid)) {
            $p_subjid = Alib::GetSessUserId($p_sessid);
            if (PEAR::isError($p_subjid)) {
                return $p_subjid;
            }
        }
        $ac = StoredFile::recallByGunid($this, $p_playlistId);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        $state = $ac->_getState();
        if ($p_val) {
            $r = $ac->setState('edited', $p_subjid);
        } else {
            $r = $ac->setState('ready', 'NULL');
        }
        if (PEAR::isError($r)) {
            return $r;
        }
        return ($state == 'edited');
    }


    /**
     * Check if playlist is marked as edited
     *
     * @param string $p_playlistId
     * 		Playlist global unique ID
     * @return FALSE|int
     * 		ID of user editing it
     */
    public function isEdited($p_playlistId)
    {
        $ac = StoredFile::recallByGunid($this, $p_playlistId);
        if (PEAR::isError($ac)) {
            return $ac;
        }
        if (!$ac->isEdited($p_playlistId)) {
            return FALSE;
        }
        return $ac->isEditedBy($p_playlistId);
    }


    /* ---------------------------------------- redefined "protected" methods */
    /**
     * Copy virtual file.
     * Redefined from parent class.
     *
     * @return int
     * 		New object local id
     */
    protected static function CopyObj($id, $newParid, $after=NULL)
    {
        $parid = M2tree::GetParent($id);
        $nid = M2tree::CopyObj($id, $newParid, $after);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
                $ac = StoredFile::recall($this, $id);
                if (PEAR::isError($ac)) {
                    return $ac;
                }
                $ac2 = StoredFile::CopyOf($ac, $nid);
                $ac2->rename(M2tree::GetObjName($nid));
                break;
            case "File":
            default:
        }
        return $nid;
    }


    /**
     * Move virtual file.<br>
     * Redefined from parent class.
     *
     * @return boolean
     */
    public static function MoveObj($id, $newParid, $after=NULL)
    {
        $parid = M2tree::GetParent($id);
        switch (BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
                $ac = StoredFile::recall($this, $id);
                if (PEAR::isError($ac)) {
                    return $ac;
                }
                if ($ac->isEdited()) {
                    return PEAR::raiseError(
                        'BasicStor::MoveObj: file is currently being edited, it cannot be moved.');
                }
                if ($ac->isAccessed()) {
                    return PEAR::raiseError(
                        'BasicStor::MoveObj: file is currently in use, it cannot be moved.');
                }
                break;
            default:
        }
        $nid = M2tree::MoveObj($id, $newParid, $after);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        return TRUE;
    }


    /**
     * Optionaly remove virtual file with the same name and add new one.<br>
     * Redefined from parent class.
     *
     * @return unknown
     * 		ID
     */
    public static function AddObj($name, $type, $parid=1, $aftid=NULL, $param='')
    {
        $exid = M2tree::GetObjId($name, $parid);
        if (PEAR::isError($exid)) {
            return $exid;
        }
        //if(!is_null($exid)){ $this->removeObj($exid); }
        $name2 = $name;
        for ( ;
            $xid = M2tree::GetObjId($name2, $parid),
                !is_null($xid) && !PEAR::isError($xid);
            $name2 .= "_"
        );
        if (!is_null($exid)) {
            $r = M2tree::RenameObj($exid, $name2);
        }
        return M2tree::AddObj($name, $type, $parid, $aftid, $param);
    }


    /**
     * Remove virtual file.<br>
     * Redefined from parent class.
     *
     * @param int $id
     * 		Local id of removed object
     * @param boolean $forced
     * 		Unconditional delete
     * @return true|PEAR_Error
     */
    public function removeObj($id, $forced=FALSE)
    {
        switch ($ot = BasicStor::GetObjType($id)) {
            case "audioclip":
            case "playlist":
            case "webstream":
                $ac = StoredFile::recall($this, $id);
                if (PEAR::isError($ac)) {
                    return $ac;
                }
                if ($ac->isEdited() && !$forced) {
                    return PEAR::raiseError(
                        'BasicStor.php: removeObj: is edited'
                    );
                }
                if ($ac->isAccessed() && !$forced) {
                    return PEAR::raiseError(
                        'BasicStor.php: removeObj: is accessed'
                    );
                }
                $ac->delete();
                break;
            case "File":
            case "Folder":
            case "Replica":
                break;
            default:
                return PEAR::raiseError(
                    "BasicStor::bsDeleteFile: unknown obj type ($ot)"
                );
        }
        $res = Alib::RemoveObj($id);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    /* ========================================================= misc methods */
    /**
     * Write string to file
     *
     * @param string $str
     * 		string to be written to file
     * @param string $fname
     * 		pathname to file
     * @return TRUE|raiseError
     */
    private static function WriteStringToFile($p_str, $p_fname)
    {
        $fp = @fopen($p_fname, "w");
        if ($fp === FALSE) {
            return PEAR::raiseError(
                "BasicStor::WriteStringToFile: cannot open file ($p_fname)"
            );
        }
        fwrite($fp, $p_str);
        fclose($fp);
        return TRUE;
    }


    /**
     * Check and optionally upgrade LS db structure.
     *  (add column suported only now)
     *
     * items in array with db changes:
     *  <ul>
     *      <li>tbl - table name</li>
     *      <li>fld - field name</li>
     *      <li>type - type of field</li>
     *  </ul>
     *
     * @return TRUE/error
     */
//    function upgradeDbStructure()
//    {
//        $chDb = array(
//            '1.1 Leon' => array(
//                array('tbl'=>$this->accessTable, 'fld'=>'owner',
//                    'type'=>"int REFERENCES {$this->subjTable}"
//                ),
//            ),
//            '1.1 Freetown' => array(
//                array('tbl'=>$this->filesTable, 'fld'=>'mtime',
//                    'type'=>'timestamp(6) with time zone'
//                ),
//            ),
//        );
//        foreach ($chDb as $version => $chArr) {
//            foreach ($chArr as $change) {
//                extract($change);   // tbl, op, fld, type
//                $r = $CC_DBC->tableInfo($tbl, DB_TABLEINFO_ORDERTABLE);
//                if (PEAR::isError($r)) {
//                    return $r;
//                }
//                if (!isset($r['ordertable'][$tbl][$fld])) {
//                    $q = "ALTER table $tbl ADD $fld $type";
//                    $r = $CC_DBC->query($q);
//                    if (PEAR::isError($r)) {
//                    	return $r;
//                    }
//                }
//            }
//        }
//        return TRUE;
//    }


    /* =============================================== test and debug methods */
    /**
     * Reset storageServer for debugging.
     *
     * @param boolean $loadSampleData
     * 		Flag for allow sample data loading
     * @param boolean $filesOnly
     * 		Flag for operate only on files in storage
     * @return array
     * 		result of localSearch with filetype 'all' and no conditions,
     *      i.e. array of hashes, fields:
     *       cnt : integer - number of inserted files
     *       results : array of hashes:
     *          gunid: string
     *          type: string - audioclip | playlist | webstream
     *          title: string - dc:title from metadata
     *          creator: string - dc:creator from metadata
     *          source: string - dc:source from metadata
     *          length: string - dcterms:extent in extent format
     */
    public function resetStorage($loadSampleData=TRUE, $filesOnly=FALSE)
    {
        global $CC_CONFIG;
        if ($filesOnly) {
            $this->deleteFiles();
        } else {
            $this->deleteData();
        }
        if (!$CC_CONFIG['isArchive']) {
            $tr = new Transport($this);
            $tr->resetData();
        }
        $res = array('cnt'=>0, 'results'=>array());
        if (!$loadSampleData) {
            return $res;
        }
        $rootHD = M2tree::GetObjId('root', $this->storId);
        $samples = dirname(__FILE__)."/tests/sampleData.php";
        if (file_exists($samples)) {
            include($samples);
        } else {
            $sampleData = array();
        }
        foreach ($sampleData as $k => $it) {
            $type = $it['type'];
            $xml = $it['xml'];
            if (isset($it['gunid'])) {
                $gunid = $it['gunid'];
            } else {
                $gunid = '';
            }
            switch($type){
                case "audioclip":
                    $media = $it['media'];
                    $fname = basename($media);
                    break;
                case "playlist":
                case "webstream":
                    $media = '';
                    $fname = basename($xml);
                    break;
            }
            $r = $this->bsPutFile(
                $rootHD, $fname,
                $media, $xml, $gunid, $type
            );
            if (PEAR::isError($r)) {
                return $r;
            }
            //$gunid = BasicStor::GunidFromId($r);
            //$res['results'][] = array('gunid' => $gunid, 'type' => $type);
            //$res['cnt']++;
        }
        return $this->bsLocalSearch(
            array('filetype'=>'all', 'conditions'=>array())
        );
        //return $res;
    }


    /**
     * dump
     *
     */
//    public function dump($id='', $indch='    ', $ind='', $format='{name}')
//    {
//        if ($id=='') {
//            $id = $this->storId;
//        }
//        return parent::dump($id, $indch, $ind, $format);
//    }


    /**
     *
     *
     */
    public function dumpDir($id='', $format='$o["name"]')
    {
        if ($id == '') {
            $id = $this->storId;
        }
        $arr = M2tree::GetDir($id, 'id,name');
        $arr = array_map(create_function('$o', 'return "'.$format .'";'), $arr);
        return join('', $arr);
    }


    /**
     *
     *
     */
    public function debug($va)
    {
        echo"<pre>\n"; 
        print_r($va);
    }


    /**
     * deleteFiles
     *
     * @return void
     */
    private function deleteFiles()
    {
        global $CC_CONFIG, $CC_DBC;
        $ids = $CC_DBC->getAll("SELECT id FROM ".$CC_CONFIG['filesTable']);
        if (is_array($ids)) {
            foreach ($ids as $i => $item) {
                $this->bsDeleteFile($item['id'], TRUE);
            }
        }
    }


    /**
     * deleteData
     *
     * @return void
     */
    public function deleteData()
    {
        $this->deleteFiles();
        Alib::DeleteData();
        $this->initData();
    }


    /**
     * Create BasicStor object with temporarily changed configuration
     * to prevent data changes in tests
     *
     */
//    function createTestSpace(&$dbc, $config){
//        $configBckp = $config;
//        $config['tblNamePrefix'] .= '_test_';
//        mkdir($config['storageDir'].'/tmp');
//        $config['storageDir']    .=  '/tmp/stor';
//        $config['bufferDir']      =  $config['storageDir'].'/buffer';
//        $config['transDir']      .=  '/tmp/trans';
//        $config['accessDir']     .=  '/tmp/access';
//        mkdir($config['storageDir']);
//        mkdir($config['bufferDir']);
//        $bs = new BasicStor($dbc, $config);
//        $bs->configBckp = $configBckp;
//        $r = $bs->install();
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        return $bs;
//    }


    /**
     * Clean up test space
     *
     */
//    function releaseTestSpace() {
//        $r = $this->uninstall();
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        // rmdir($this->config['bufferDir']);
//        rmdir($this->config['storageDir']);
//        $this->config = $this->configBckp;
//        rmdir($this->config['storageDir'].'/tmp');
//    }


    /**
     * testData
     *
     */
    public function testData($d='')
    {
        $exdir = dirname(__FILE__).'/tests';
        $o[] = $this->addSubj('test1', 'a');
        $o[] = $this->addSubj('test2', 'a');
        $o[] = $this->addSubj('test3', 'a');
        $o[] = $this->addSubj('test4', 'a');

        $o[] = $t1hd = M2tree::GetObjId('test1', $this->storId);
        $o[] = $t1d1 = $this->bsCreateFolder($t1hd, 'test1_folder1');
        $o[] = $this->bsCreateFolder($t1hd, 'test1_folder2');
        $o[] = $this->bsCreateFolder($t1d1, 'test1_folder1_1');
        $o[] = $t1d12 = $this->bsCreateFolder($t1d1, 'test1_folder1_2');

        $o[] = $t2hd = M2tree::GetObjId('test2', $this->storId);
        $o[] = $this->bsCreateFolder($t2hd, 'test2_folder1');

        $o[] = $this->bsPutFile($t1hd, 'file1.mp3', "$exdir/ex1.mp3", '', NULL, 'audioclip');
        $o[] = $this->bsPutFile($t1d12, 'file2.wav', "$exdir/ex2.wav", '', NULL, 'audioclip');
        $this->tdata['storage'] = $o;
    }

    /**
     * test
     *
     */
    public function test()
    {
        global $CC_CONFIG;
        $this->test_log = '';
        // if(PEAR::isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->testData();
        if ($CC_CONFIG['useTrash']) {
            $trash = "\n        ".$CC_CONFIG['TrashName'];
        } else {
            $trash = "";
        }
        if (!$CC_CONFIG['isArchive']) {
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
        } else {
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
        $r = M2tree::DumpTree($this->storId, '    ', '    ', '{name}');
        if (PEAR::isError($r)) {
            return $r;
        }
        $this->test_dump = $r;
        if ($this->test_dump == $this->test_correct) {
            $this->test_log .= "# BasicStor::test: OK\n";
            return true;
        } else {
            return PEAR::raiseError(
                "BasicStor::test:\ncorrect:\n.{$this->test_correct}.\n".
                "dump:\n.{$this->test_dump}.\n", 1, PEAR_ERROR_RETURN);
        }
    }


    /**
     * initData - initialize
     *
     */
    public function initData()
    {
        global $CC_CONFIG;
        $this->rootId = M2tree::GetRootNode();
        $this->storId = BasicStor::AddObj('StorageRoot', 'Folder', $this->rootId);
        $this->wd = $this->storId;
        // user root init
        $rootUid = Subjects::AddSubj('root', $CC_CONFIG['tmpRootPass']);
        $res = Alib::AddPerm($rootUid, '_all', $this->rootId, 'A');
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = Alib::AddPerm($rootUid, 'subjects', $this->rootId, 'A');
        if (PEAR::isError($res)) {
            return $res;
        }
        $fid = $this->bsCreateFolder($this->storId, 'root');
        if (PEAR::isError($fid)) {
            return $fid;
        }
        if ($CC_CONFIG['useTrash']) {
            $tfid = $this->bsCreateFolder(
                $this->storId, $CC_CONFIG["TrashName"]);
            if (PEAR::isError($tfid)) {
                return $tfid;
            }
        }
        $allid = Subjects::AddSubj($CC_CONFIG['AllGr']);
        if (PEAR::isError($allid)) {
            return $allid;
        }
        Subjects::AddSubjectToGroup('root', $CC_CONFIG['AllGr']);
        Alib::AddPerm($allid, 'read', $this->rootId, 'A');
        $admid = Subjects::AddSubj($CC_CONFIG['AdminsGr']);
        if (PEAR::isError($admid)) {
            return $admid;
        }
        $r = Subjects::AddSubjectToGroup('root', $CC_CONFIG['AdminsGr']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $res = Alib::AddPerm($admid, '_all', $this->rootId, 'A');
        if (PEAR::isError($res)) {
            return $res;
        }
        if (!$CC_CONFIG['isArchive']) {
            $stPrefGr = Subjects::AddSubj($CC_CONFIG['StationPrefsGr']);
            if (PEAR::isError($stPrefGr)) {
                return $stPrefGr;
            }
            Subjects::AddSubjectToGroup('root', $CC_CONFIG['StationPrefsGr']);
            // user scheduler init
            Subjects::AddSubj('scheduler', $CC_CONFIG['schedulerPass']);
            $res = Alib::AddPerm($rootUid, 'read', $this->rootId, 'A');
            if (PEAR::isError($res)) {
                return $res;
            }
            $r = Subjects::AddSubjectToGroup('scheduler', $CC_CONFIG['AllGr']);
        }
    }


    /**
     * install - create tables
     *
     * file states:
     *  <ul>
     *      <li>empty</li>
     *      <li>incomplete</li>
     *      <li>ready</li>
     *      <li>edited</li>
     *      <li>deleted</li>
     *  </ul>
     * file types:
     *  <ul>
     *      <li>audioclip</li>
     *      <li>playlist</li>
     *      <li>webstream</li>
     *  </ul>
     * access types:
     *  <ul>
     *      <li>access</li>
     *      <li>download</li>
     *  </ul>
     */
//    public function install()
//    {
//        parent::install();
//        $r = $CC_DBC->query("CREATE TABLE {$this->filesTable} (
//            id int not null,
//            gunid bigint not null,                      -- global unique ID
//            name varchar(255) not null default'',       -- human file id ;)
//            mime varchar(255) not null default'',       -- mime type
//            ftype varchar(128) not null default'',      -- file type
//            state varchar(128) not null default'empty', -- file state
//            currentlyaccessing int not null default 0,  -- access counter
//            editedby int REFERENCES {$this->subjTable}, -- who edits it
//            mtime timestamp(6) with time zone           -- lst modif.time
//        )");
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        $CC_DBC->query("CREATE UNIQUE INDEX {$this->filesTable}_id_idx
//            ON {$this->filesTable} (id)");
//        $CC_DBC->query("CREATE UNIQUE INDEX {$this->filesTable}_gunid_idx
//            ON {$this->filesTable} (gunid)");
//        $CC_DBC->query("CREATE INDEX {$this->filesTable}_name_idx
//            ON {$this->filesTable} (name)");
//
//        $CC_DBC->createSequence("{$this->mdataTable}_id_seq");
//        $r = $CC_DBC->query("CREATE TABLE {$this->mdataTable} (
//            id int not null,
//            gunid bigint,
//            subjns varchar(255),             -- subject namespace shortcut/uri
//            subject varchar(255) not null default '',
//            predns varchar(255),             -- predicate namespace shortcut/uri
//            predicate varchar(255) not null,
//            predxml char(1) not null default 'T', -- Tag or Attribute
//            objns varchar(255),              -- object namespace shortcut/uri
//            object text
//        )");
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        $CC_DBC->query("CREATE UNIQUE INDEX {$this->mdataTable}_id_idx
//            ON {$this->mdataTable} (id)");
//        $CC_DBC->query("CREATE INDEX {$this->mdataTable}_gunid_idx
//            ON {$this->mdataTable} (gunid)");
//        $CC_DBC->query("CREATE INDEX {$this->mdataTable}_subj_idx
//            ON {$this->mdataTable} (subjns, subject)");
//        $CC_DBC->query("CREATE INDEX {$this->mdataTable}_pred_idx
//            ON {$this->mdataTable} (predns, predicate)");
//
//        $r = $CC_DBC->query("CREATE TABLE {$this->accessTable} (
//            gunid bigint,                             -- global unique id
//            token bigint,                             -- access token
//            chsum char(32) not null default'',        -- md5 checksum
//            ext varchar(128) not null default'',      -- extension
//            type varchar(20) not null default'',      -- access type
//            parent bigint,                            -- parent token
//            owner int REFERENCES {$this->subjTable},  -- subject have started it
//            ts timestamp
//        )");
//        if (PEAR::isError($r)) {
//            return $r;
//        }
//        $CC_DBC->query("CREATE INDEX {$this->accessTable}_token_idx
//            ON {$this->accessTable} (token)");
//        $CC_DBC->query("CREATE INDEX {$this->accessTable}_gunid_idx
//            ON {$this->accessTable} (gunid)");
//        $CC_DBC->query("CREATE INDEX {$this->accessTable}_parent_idx
//            ON {$this->accessTable} (parent)");
//        if (!file_exists($this->storageDir)) {
//            mkdir($this->storageDir, 02775);
//        }
//        if (!file_exists($this->bufferDir)) {
//            mkdir($this->bufferDir, 02775);
//        }
//        $this->initData();
//    }


    /**
     * id  subjns  subject predns  predicate   objns   object
     * y1  literal xmbf    NULL    namespace   literal http://www.sotf.org/xbmf
     * x1  gunid   <gunid> xbmf    contributor NULL    NULL
     * x2  mdid    x1      xbmf    role        literal Editor
     *
     * predefined shortcuts:
     *      _L              = literal
     *      _G              = gunid (global id of media file)
     *      _I              = mdid (local id of metadata record)
     *      _nssshortcut    = namespace shortcut definition
     *      _blank          = blank node
     */

    /**
     * uninstall
     *
     * @return void
     */
//    public function uninstall()
//    {
//        global $CC_CONFIG, $CC_DBC;
//        $CC_DBC->query("DROP TABLE ".$CC_CONFIG['mdataTable']);
//        $CC_DBC->dropSequence($CC_CONFIG['mdataTable']."_id_seq");
//        $CC_DBC->query("DROP TABLE ".$CC_CONFIG['filesTable']);
//        $CC_DBC->query("DROP TABLE ".$CC_CONFIG['accessTable']);
//        $d = @dir($CC_CONFIG['storageDir']);
//        while (is_object($d) && (false !== ($entry = $d->read()))){
//            if (filetype($CC_CONFIG['storageDir']."/$entry")=='dir') {
//                if ($entry!='CVS' && $entry!='tmp' && strlen($entry)==3) {
//                    $dd = dir($CC_CONFIG['storageDir']."/$entry");
//                    while (false !== ($ee = $dd->read())) {
//                        if (substr($ee, 0, 1)!=='.') {
//                            unlink($CC_CONFIG['storageDir']."/$entry/$ee");
//                        }
//                    }
//                    $dd->close();
//                    rmdir($CC_CONFIG['storageDir']."/$entry");
//                }
//            }
//        }
//        if (is_object($d)) {
//            $d->close();
//        }
//        if (file_exists($this->bufferDir)) {
//            $d = dir($this->bufferDir);
//            while (false !== ($entry = $d->read())) {
//                if(substr($entry,0,1)!='.') {
//                    unlink("{$this->bufferDir}/$entry");
//                }
//            }
//            $d->close();
//            @rmdir($this->bufferDir);
//        }
//        parent::uninstall();
//    }


    /**
     * Aux logging for debug
     *
     * @param string $msg - log message
     */
    public function debugLog($msg)
    {
        global $CC_CONFIG, $CC_DBC;
        $fp = fopen($CC_CONFIG['storageDir']."/log", "a") or die("Can't write to log\n");
        fputs($fp, date("H:i:s").">$msg<\n");
        fclose($fp);
    }

} // class BasicStor
?>