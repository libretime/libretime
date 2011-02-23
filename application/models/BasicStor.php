<?php
/*
 * Format of search criteria: hash, with following structure:<br>
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
 *  <p>
 *  Format of search/browse results: hash, with following structure:<br>
 *   <ul>
 *      <li>results : array of gunids have found</li>
 *      <li>cnt : integer - number of matching items</li>
 *   </ul>
 *
 */
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

require_once(dirname(__FILE__)."/Alib.php");
require_once(dirname(__FILE__)."/StoredFile.php");
require_once(dirname(__FILE__)."/Transport.php");
require_once(dirname(__FILE__)."/Playlist.php");

//$g_metadata_xml_to_db_mapping = array(
//        "dc:format" => "format",
//        "ls:bitrate" => "bit_rate",
//    	"ls:samplerate" => "sample_rate",
//        "dcterms:extent" => "length",
//		"dc:title" => "track_title",
//		"dc:description" => "comments",
//    	"dc:type" => "genre",
//    	"dc:creator" => "artist_name",
//        "dc:source" => "album_title",
//    	"ls:channels" => "channels",
//		"ls:filename" => "name",
//		"ls:year" => "year",
//    	"ls:url" => "url",
//    	"ls:track_num" => "track_number",
//        "ls:mood" => "mood",
//        "ls:bpm" => "bpm",
//        "ls:disc_num" => "disc_number",
//        "ls:rating" => "rating",
//        "ls:encoded_by" => "encoded_by",
//        "dc:publisher" => "label",
//        "ls:composer" => "composer",
//        "ls:encoder" => "encoder",
//        "ls:crc" => "checksum",
//        "ls:lyrics" => "lyrics",
//        "ls:orchestra" => "orchestra",
//        "ls:conductor" => "conductor",
//        "ls:lyricist" => "lyricist",
//        "ls:originallyricist" => "original_lyricist",
//        "ls:radiostationname" => "radio_station_name",
//        "ls:audiofileinfourl" => "info_url",
//        "ls:artisturl" => "artist_url",
//        "ls:audiosourceurl" => "audio_source_url",
//        "ls:radiostationurl" => "radio_station_url",
//        "ls:buycdurl" => "buy_this_url",
//        "ls:isrcnumber" => "isrc_number",
//        "ls:catalognumber" => "catalog_number",
//        "ls:originalartist" => "original_artist",
//        "dc:rights" => "copyright",
//        "dcterms:temporal" => "report_datetime",
//        "dcterms:spatial" => "report_location",
//        "dcterms:entity" => "report_organization",
//        "dc:subject" => "subject",
//        "dc:contributor" => "contributor",
//        "dc:language" => "language");

/**
 * Core of Airtime file storage module
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @see Alib
 */
class BasicStor {
    public $storId;
    private $fileTypes;

    public function __construct()
    {
        $this->filetypes = array(
            'all'=>NULL,
            'audioclip'=>'audioclip',
            'webstream'=>'webstream',
            'playlist'=>'playlist',
        );
    }


    /**
     * Store new file in the storage
     *
     * @param array $p_values
     * 		See StoredFile::Insert() for details.
     * @param boolean $copyMedia
     * 		copy the media file if true, make symlink if false
     * @return StoredFile|PEAR_Error
     *      The StoredFile that was created.
     */
    //    public function bsPutFile($p_values, $p_copyMedia=TRUE)
    //    {
    //        $storedFile = StoredFile::Insert($p_values, $p_copyMedia);
    //        return $storedFile;
    //    }


    /**
     * Rename file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $newName
     * @return boolean|PEAR_Error
     */
    //    public function bsRenameFile($id, $newName)
    //    {
    //        switch (BasicStor::GetObjType($id)) {
    //            case "audioclip":
    //            case "playlist":
    //            case "webstream":
    //                $storedFile = StoredFile::Recall($id);
    //                if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //                    // catch nonerror exception:
    //                    //if($storedFile->getCode() != GBERR_FOBJNEX)
    //                    return $storedFile;
    //                }
    //                $res = $storedFile->setName($newName);
    //                if (PEAR::isError($res)) {
    //                    return $res;
    //                }
    //                break;
    //            case "File":
    //            default:
    //        }
    //        return TRUE;
    //    }


    /**
     * Replace file. Doesn't change filetype!
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $localFilePath
     * 		Local path of media file
     * @param string $metadataFilePath
     * 		Local path of metadata file
     * @param string $mdataLoc
     * 		'file'|'string'
     * @return true|PEAR_Error
     * @exception PEAR::error
     */
    //    public function bsReplaceFile($id, $localFilePath, $metadataFilePath, $mdataLoc='file')
    //    {
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        if (!empty($metadataFilePath) &&
    //                ($mdataLoc!='file' || file_exists($metadataFilePath))) {
    //            $r = $storedFile->setMetadata($metadataFilePath, $mdataLoc);
    //            if (PEAR::isError($r)) {
    //                return $r;
    //            }
    //        }
    //        if (!empty($localFilePath) && file_exists($localFilePath)) {
    //            $r = $storedFile->setRawMediaData($localFilePath);
    //            if (PEAR::isError($r)) {
    //                return $r;
    //            }
    //        }
    //        return TRUE;
    //    }


    /**
     * Delete file
     *
     * @param int $id
     * 		Virtual file's local id
     * @param boolean $forced
     * 		If true don't use trash
     * @return true|PEAR_Error
     */
    //    public function bsDeleteFile($id, $forced=FALSE)
    //    {
    //        global $CC_CONFIG;
    //        // full delete:
    //        if (!$CC_CONFIG['useTrash'] || $forced) {
    //            $res = BasicStor::RemoveObj($id, $forced);
    //            return $res;
    //        }
    //
    //        $storedFile = StoredFile::Recall($id);
    //
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        if ($storedFile->isAccessed()) {
    //             return PEAR::raiseError(
    //                'Cannot delete an object that is currently accessed.'
    //            );
    //        }
    //        // move to trash:
    //        switch (BasicStor::GetObjType($id)) {
    //
    //            case "audioclip":
    //                $playLists = $storedFile->getPlaylists();
    //                $item_gunid = $storedFile->getGunid();
    //                if( $playLists != NULL) {
    //
    //                    foreach($playLists as $key=>$val) {
    //                        $playList_id = BasicStor::IdFromGunidBigInt($val["gunid"]);
    //                        $playList_titles[] = BasicStor::bsGetMetadataValue($playList_id, "dc:title");
    //                    }
    //                    return PEAR::raiseError(
    //                        'Please remove this song from all playlists: ' . join(",", $playList_titles)
    //                    );
    //                }
    //                break;
    //
    //            case "playlist":
    //                if($storedFile->isScheduled()) {
    //                     return PEAR::raiseError(
    //                        'Cannot delete an object that is scheduled to play.'
    //                    );
    //                }
    //                break;
    //
    //            case "webstream":
    //
    //                break;
    //            default:
    //        }
    //
    //        $res = $storedFile->setState('deleted');
    //        if (PEAR::isError($res)) {
    //            return $res;
    //        }
    //
    //	    return TRUE;
    //    }


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
    public static function bsCheckToken($token, $type='put')
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
    public static function bsAccess($realFname, $ext, $gunid, $type='access',
    $parent='0', $owner=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_null($gunid)) {
            $gunid = StoredFile::NormalizeGunid($gunid);
        }
        $token = StoredFile::CreateGunid();
        if (!is_null($realFname)) {
            $linkFname = $CC_CONFIG['accessDir']."/$token.$ext";
            //broken links are ignored by the player, do not worry about it here
            /*            if (!is_file($realFname) && !is_link($realFname)) {
            return PEAR::raiseError(
            "BasicStor::bsAccess: real file not found ($realFname)",
            GBERR_FILEIO);
            }
            */
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
    public static function bsRelease($token, $type='access')
    {
        global $CC_CONFIG, $CC_DBC;
        if (!BasicStor::bsCheckToken($token, $type)) {
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
    //    public function bsOpenDownload($id, $part='media')
    //    {
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        $gunid = $storedFile->gunid;
    //        switch ($part) {
    //            case "media":
    //                $realfile = $storedFile->getRealFileName();
    //                $ext = $storedFile->getFileExtension();
    //                $filename = $storedFile->getName();
    //                break;
    //            case "metadata":
    //                $realfile = $storedFile->getRealMetadataFileName();
    //                $ext = "xml";
    //                $filename = $storedFile->getName();
    //                break;
    //            default:
    //                return PEAR::raiseError(
    //                 "BasicStor::bsOpenDownload: unknown part ($part)"
    //                );
    //        }
    //        $acc = BasicStor::bsAccess($realfile, $ext, $gunid, 'download');
    //        if (PEAR::isError($acc)) {
    //            return $acc;
    //        }
    //        $url = BasicStor::GetUrlPart()."access/".basename($acc['fname']);
    //        $chsum = md5_file($realfile);
    //        $size = filesize($realfile);
    //        return array(
    //            'url'=>$url, 'token'=>$acc['token'],
    //            'chsum'=>$chsum, 'size'=>$size,
    //            'filename'=>$filename
    //        );
    //    }


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
    //    public function bsCloseDownload($token, $part='media')
    //    {
    //        if (!BasicStor::bsCheckToken($token, 'download')) {
    //            return PEAR::raiseError(
    //             "BasicStor::bsCloseDownload: invalid token ($token)"
    //            );
    //        }
    //        $r = BasicStor::bsRelease($token, 'download');
    //        if (PEAR::isError($r)){
    //            return $r;
    //        }
    //        return (is_null($r['gunid']) ? $r['realFname'] : $r['gunid']);
    //    }


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
    //    public function bsOpenPut($chsum, $gunid, $owner=NULL)
    //    {
    //        global $CC_CONFIG, $CC_DBC;
    //        if (!is_null($gunid)) {
    //            $gunid = StoredFile::NormalizeGunid($gunid);
    //        }
    //        $escapedChsum = pg_escape_string($chsum);
    //        $token = StoredFile::CreateGunid();
    //        $res = $CC_DBC->query("DELETE FROM ".$CC_CONFIG['accessTable']
    //            ." WHERE token=x'$token'::bigint");
    //        if (PEAR::isError($res)) {
    //            return $res;
    //        }
    //        $gunidSql = (is_null($gunid) ? "NULL" : "x'{$gunid}'::bigint" );
    //        $ownerSql = (is_null($owner) ? "NULL" : "$owner" );
    //        $res = $CC_DBC->query("
    //            INSERT INTO ".$CC_CONFIG['accessTable']."
    //                (gunid, token, ext, chsum, type, owner, ts)
    //            VALUES
    //                ($gunidSql, x'$token'::bigint,
    //                    '', '$escapedChsum', 'put', $ownerSql, now())");
    //        if (PEAR::isError($res)) {
    //            return $res;
    //        }
    //        $fname = $CC_CONFIG['accessDir']."/$token";
    //        touch($fname);      // is it needed?
    //        $url = BasicStor::GetUrlPart()."xmlrpc/put.php?token=$token";
    //        return array('url'=>$url, 'fname'=>$fname, 'token'=>$token);
    //    }


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
    //    public function bsClosePut($token)
    //    {
    //        global $CC_CONFIG, $CC_DBC;
    //        $token = StoredFile::NormalizeGunid($token);
    //
    //        if (!BasicStor::bsCheckToken($token, 'put')) {
    //            return PEAR::raiseError(
    //                "BasicStor::bsClosePut: invalid token ($token)",
    //                GBERR_TOKEN);
    //        }
    //        $row = $CC_DBC->getRow(
    //            "SELECT chsum, owner FROM ".$CC_CONFIG['accessTable']
    //            ." WHERE token=x'{$token}'::bigint");
    //        if (PEAR::isError($row)) {
    //            return $row;
    //        }
    //        $fname = $CC_CONFIG['accessDir']."/$token";
    //        $md5sum = md5_file($fname);
    //
    //        $chsum = $row['chsum'];
    //        $owner = $row['owner'];
    //        $error = null;
    //        if ( (trim($chsum) != '') && ($chsum != $md5sum) ) {
    //            // Delete the file if the checksums do not match.
    //            if (file_exists($fname)) {
    //                @unlink($fname);
    //            }
    //            $error = new PEAR_Error(
    //                 "BasicStor::bsClosePut: md5sum does not match (token=$token)".
    //                 " [$chsum/$md5sum]",
    //                 GBERR_PUT);
    //        } else {
    //            // Remember the MD5 sum
    //            $storedFile = StoredFile::RecallByToken($token);
    //            if (!is_null($storedFile) && !PEAR::isError($storedFile)) {
    //                $storedFile->setMd5($md5sum);
    //            } else {
    //#                $error = $storedFile;
    //            }
    //        }
    //
    //        // Delete entry from access table.
    //        $res = $CC_DBC->query("DELETE FROM ".$CC_CONFIG['accessTable']
    //            ." WHERE token=x'$token'::bigint");
    //        if (PEAR::isError($error)) {
    //            return $error;
    //        } elseif (PEAR::isError($res)) {
    //            return $res;
    //        }
    //
    //        return array('fname'=>$fname, 'owner'=>$owner);
    //    }


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
    //    public function bsCheckPut($token)
    //    {
    //        global $CC_CONFIG, $CC_DBC;
    //        if (!BasicStor::bsCheckToken($token, 'put')) {
    //            return PEAR::raiseError(
    //             "BasicStor::bsCheckPut: invalid token ($token)"
    //            );
    //        }
    //        $chsum = $CC_DBC->getOne("
    //            SELECT chsum FROM ".$CC_CONFIG['accessTable']."
    //            WHERE token=x'{$token}'::bigint
    //            ");
    //        if (PEAR::isError($chsum)) {
    //            return $chsum;
    //        }
    //        $fname = $CC_CONFIG['accessDir']."/$token";
    //        $md5sum = md5_file($fname);
    //        $size = filesize($fname);
    //        $status = ($chsum == $md5sum);
    //        return array(
    //            'status'=>$status, 'size'=>$size,
    //            'expectedsum'=>$chsum,
    //            'realsum'=>$md5sum,
    //        );
    //    }


    /**
     * Return starting part of storageServer URL
     *
     * @return string
     * 		URL
     */
    //    public static function GetUrlPart()
    //    {
    //        global $CC_CONFIG;
    //        $host = $CC_CONFIG['storageUrlHost'];
    //        $port = $CC_CONFIG['storageUrlPort'];
    //        $path = $CC_CONFIG['storageUrlPath'];
    //        return "http://$host:$port$path/";
    //    }


    /**
     * Get tokens by type
     *
     * @param string $type
     * 		access|put|render etc.
     * @return array
     * 		array of tokens
     */
    //    public static function GetTokensByType($type)
    //    {
    //        global $CC_CONFIG, $CC_DBC;
    //        $res = $CC_DBC->query(
    //            "SELECT TO_HEX(token) AS token FROM ".$CC_CONFIG['accessTable']." WHERE type=?",
    //            array($type));
    //        while ($row = $res->fetchRow()) {
    //             $r[] = $row['token'];
    //        }
    //        return $r;
    //    }


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
    //    public function bsReplaceMetadata($id, $mdata, $mdataLoc='file')
    //    {
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        return $storedFile->setMetadata($mdata, $mdataLoc);
    //    }


    /**
     * Get metadata as XML string
     *
     * @param int $id
     * 		Virtual file's local id
     * @return string|PEAR_Error
     */
    //    public function bsGetMetadata($id)
    //    {
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        return $storedFile->getMetadata();
    //    }


    /**
     * Get dc:title (if exists)
     *
     * @param int $id
     * 		Virtual file's local id
     * @param string $gunid
     * 		Virtual file's gunid, optional, used only if not
     *      null, id is then ignored
     * @return string|PEAR_Error
     */
    //    public function bsGetTitle($id, $gunid=NULL)
    //    {
    //        if (is_null($gunid)) {
    //            $storedFile = StoredFile::Recall($id);
    //        } else {
    //            $storedFile = StoredFile::RecallByGunid($gunid);
    //        }
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        $r = $storedFile->md["title"];
    //        $title = (empty($r) ? 'unknown' : $r);
    //        return $title;
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
    //    public function bsGetMetadataValue($id, $category = null)
    //    {
    //        if (!is_numeric($id)) {
    //            return null;
    //        }
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        if (is_null($category)) {
    //            return $storedFile->md;
    //        } elseif (is_array($category)) {
    //            $values = array();
    //			      foreach ($category as $tmpCat) {
    //				        $values[$tmpCat] = $storedFile->md[$tmpCat];
    //			      }
    //			      return $values;
    //        } else {
    //            return $storedFile->md[$category];
    //        }
    //    }


    /**
     * Convert XML name to database column name.  Used for backwards compatibility
     * with old code.
     *
     * @param string $p_category
     * @return string|null
     */
    //    public static function xmlCategoryToDbColumn($p_category)
    //    {
    //        global $g_metadata_xml_to_db_mapping;
    //        if (array_key_exists($p_category, $g_metadata_xml_to_db_mapping)) {
    //            return $g_metadata_xml_to_db_mapping[$p_category];
    //        }
    //        return null;
    //    }


    /**
     * Convert database column name to XML name.
     *
     * @param string $p_dbColumn
     * @return string|null
     */
    //    public static function dbColumnToXmlCatagory($p_dbColumn)
    //    {
    //        global $g_metadata_xml_to_db_mapping;
    //        $str = array_search($p_dbColumn, $g_metadata_xml_to_db_mapping);
    //        // make return value consistent with xmlCategoryToDbColumn()
    //        if ($str === FALSE) {
    //            $str = null;
    //        }
    //        return $str;
    //    }

    /**
     * Set metadata element value
     *
     * @param int|StoredFile $id
     * 		Database ID of file
     * @param string $category
     * 		Metadata element identification (e.g. dc:title)
     * @param string $value
     * 		value to store, if NULL then delete record
     * @return boolean
     */
    //    public static function bsSetMetadataValue($p_id, $p_category, $p_value)
    //    {
    //      global $CC_CONFIG, $CC_DBC;
    //      if (!is_string($p_category) || is_array($p_value)) {
    //        return FALSE;
    //      }
    //      if (is_a($p_id, "StoredFile")) {
    //        $p_id  = $p_id->getId();
    //      }
    //      if ($p_category == 'dcterms:extent') {
    //        $p_value = BasicStor::NormalizeExtent($p_value);
    //      }
    //      $columnName = BasicStor::xmlCategoryToDbColumn($p_category); // Get column name
    //
    //      if (!is_null($columnName)) {
    //        $escapedValue = pg_escape_string($p_value);
    //        $sql = "UPDATE ".$CC_CONFIG["filesTable"]
    //             ." SET $columnName='$escapedValue'"
    //             ." WHERE id=$p_id";
    //        //var_dump($sql);
    //        $res = $CC_DBC->query($sql);
    //        if (PEAR::isError($res)) {
    //          return $res;
    //        }
    //      }
    //      return TRUE;
    //    }


    /**
     * Normalize time value to hh:mm:ss:dddddd format
     *
     * @param mixed $v
     * 		value to normalize
     * @return string
     */
    //    private static function NormalizeExtent($v)
    //    {
    //        if (!preg_match("|^\d{2}:\d{2}:\d{2}.\d{6}$|", $v)) {
    //            $s = Playlist::playlistTimeToSeconds($v);
    //            $t = Playlist::secondsToPlaylistTime($s);
    //            return $t;
    //        }
    //        return $v;
    //    }


    /**
     * Set metadata values in 'batch' mode
     *
     * @param int|StoredFile $id
     * 		Database ID of file or StoredFile object
     * @param array $values
     * 		array of key/value pairs
     *      (e.g. 'dc:title'=>'New title')
     * @return boolean
     */
    //    public static function bsSetMetadataBatch($id, $values)
    //    {
    //      global $CC_CONFIG, $CC_DBC;
    //      if (!is_array($values)) {
    //          $values = array($values);
    //      }
    //      if (count($values) == 0) {
    //        return true;
    //      }
    //      if (is_a($id, "StoredFile")) {
    //          $storedFile =& $id;
    //      } else {
    //          $storedFile = StoredFile::Recall($id);
    //          if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //              return $storedFile;
    //          }
    //      }
    //      foreach ($values as $category => $oneValue) {
    //        $columnName = BasicStor::xmlCategoryToDbColumn($category);
    //        if (!is_null($columnName)) {
    //          if ($category == 'dcterms:extent') {
    //            $oneValue = BasicStor::NormalizeExtent($oneValue);
    //          }
    //          // Since track_number is an integer, you cannot set
    //          // it to be the empty string, so we NULL it instead.
    //          if ($columnName == 'track_number' && empty($oneValue)) {
    //            $sqlPart = "$columnName = NULL";
    //          } elseif (($columnName == 'length') && (strlen($oneValue) > 8)) {
    //            // Postgres doesnt like it if you try to store really large hour
    //            // values.  TODO: We need to fix the underlying problem of getting the
    //            // right values.
    //            $parts = explode(':', $oneValue);
    //            $hour = intval($parts[0]);
    //            if ($hour > 24) {
    //              continue;
    //            } else {
    //              $sqlPart = "$columnName = '$oneValue'";
    //            }
    //          } else {
    //            $escapedValue = pg_escape_string($oneValue);
    //            $sqlPart = "$columnName = '$escapedValue'";
    //          }
    //          $sqlValues[] = $sqlPart;
    //        }
    //      }
    //      if (count($sqlValues)==0) {
    //        return TRUE;
    //      }
    //      $sql = "UPDATE ".$CC_CONFIG["filesTable"]
    //           ." SET ".join(",", $sqlValues)
    //           ." WHERE id=$id";
    //      $CC_DBC->query($sql);
    //      return TRUE;
    //    }

    /**
     * Method returning array with where-parts of sql queries
     *
     * @param array $conditions
     * 		See 'conditions' field in search criteria format
     *      definition in class documentation
     * @return array
     * 		array of strings - WHERE-parts of SQL queries
     */
    private function _makeWhereArr($conditions)
    {
        $ops = array('full'=>"='%s'", 'partial'=>"ILIKE '%%%s%%'",
            'prefix'=>"ILIKE '%s%%'", '<'=>"< '%s'", '='=>"= '%s'",
            '>'=>"> '%s'", '<='=>"<= '%s'", '>='=>">= '%s'"
            );
            $whereArr = array();
            if (is_array($conditions)) {
                foreach ($conditions as $cond) {
                    $columnName = StoredFile::xmlCategoryToDbColumn($cond['cat']);
                    $op = strtolower($cond['op']);
                    $value = $cond['val'];
                    if (!empty($value)) {
                        $splittedQn = XML_Util::splitQualifiedName($catQn);
                        $catNs = $splittedQn['namespace'];
                        $cat = $splittedQn['localPart'];
                        $opVal = sprintf($ops[$op], pg_escape_string($value));
                        // retype for timestamp value
                        if ($cat == 'mtime') {
                            switch ($op) {
                                case 'partial':
                                case 'prefix':
                                    break;
                                default:
                                    $retype = "::timestamp with time zone";
                                    $opVal = "$retype $opVal$retype";
                            }
                        }
                        $sqlCond = " {$columnName} {$opVal}\n";
                        $whereArr[] = $sqlCond;
                    }
                }
            }
            return $whereArr;
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
     */
    public function bsLocalSearch($criteria, $limit=0, $offset=0)
    {
        global $CC_CONFIG, $CC_DBC;

        // Input values
        $filetype = (isset($criteria['filetype']) ? $criteria['filetype'] : 'all');
        $filetype = strtolower($filetype);
        if (!array_key_exists($filetype, $this->filetypes)) {
            return PEAR::raiseError(__FILE__.":".__LINE__.': unknown filetype in search criteria');
        }
        $filetype = $this->filetypes[$filetype];
        $operator = (isset($criteria['operator']) ? $criteria['operator'] : 'and');
        $operator = strtolower($operator);
        $conditions = (isset($criteria['conditions']) ? $criteria['conditions'] : array());

        // Create the WHERE clause - this is the actual search part
        $whereArr = $this->_makeWhereArr($conditions);

        // Metadata values to fetch
        $metadataNames = array('dc:creator', 'dc:source', 'ls:track_num', 'dc:title', 'dcterms:extent');

        // Order by clause
        $orderby = TRUE;
        $orderByAllowedValues = array('dc:creator', 'dc:source', 'dc:title', 'dcterms:extent', "ls:track_num");
        $orderByDefaults = array('dc:creator', 'dc:source', 'dc:title');
        if ((!isset($criteria['orderby']))
        || (is_array($criteria['orderby']) && (count($criteria['orderby'])==0))) {
            // default ORDER BY
            // PaulB: track number removed because it doesnt work yet because
            // if track_num is not an integer (e.g. bad metadata like "1/20",
            // or if the field is blank) the SQL statement gives an error.
            //$orderbyQns  = array('dc:creator', 'dc:source', 'ls:track_num', 'dc:title');
            $orderbyQns = $orderByDefaults;
        } else {
            // ORDER BY clause is given in the parameters.

            // Convert the parameter to an array if it isnt already.
            $orderbyQns = $criteria['orderby'];
            if (!is_array($orderbyQns)) {
                $orderbyQns = array($orderbyQns);
            }

            // Check that it has valid ORDER BY values, if not, revert
            // to the default ORDER BY values.
            foreach ($orderbyQns as $metadataTag) {
                if (!in_array($metadataTag, $orderByAllowedValues)) {
                    $orderbyQns = $orderByDefaults;
                    break;
                }
            }
        }

        $descA = (isset($criteria['desc']) ? $criteria['desc'] : NULL);
        if (!is_array($descA)) {
            $descA = array($descA);
        }

        $orderBySql = array();
        // $dataName contains the names of the metadata columns we want to
        // fetch.  It is indexed numerically starting from 1, and the value
        // in the array is the qualified name with ":" replaced with "_".
        // e.g. "dc:creator" becomes "dc_creator".
        foreach ($orderbyQns as $xmlTag) {
            $columnName = StoredFile::xmlCategoryToDbColumn($xmlTag);
            $orderBySql[] = $columnName;
        }

        // Build WHERE clause
        $whereClause = "";
        if (!is_null($filetype)) {
            $whereClause .= "WHERE (ftype='$filetype')";
        }
        else {
            $whereClause .= "WHERE (ftype is NOT NULL)";
        }
        if (count($whereArr) != 0) {
            if ($operator == 'and') {
                $whereClause .= " AND ((".join(") AND (", $whereArr)."))";
            } else {
                $whereClause .= " AND ((".join(") OR (", $whereArr)."))";
            }
        }

        // Final query

        //"dcterms:extent" => "length",
        //"dc:title" => "track_title",
        //"dc:creator" => "artist_name",
        //dc:description

        global $g_metadata_xml_to_db_mapping;
        $plSelect = "SELECT ";
        $fileSelect = "SELECT ";
        $_SESSION["br"] = "";
        foreach ($g_metadata_xml_to_db_mapping as $key => $val){
            $_SESSION["br"] .= "key: ".$key." value:".$val.", ";
            if($key === "dc:title"){
                $plSelect .= "name AS ".$val.", ";
                $fileSelect .= $val.", ";
            }
            else if ($key === "dc:creator"){
                $plSelect .= "creator AS ".$val.", ";
                $fileSelect .= $val.", ";
            }
            else if ($key === "dcterms:extent"){
                $plSelect .= "length, ";
                $fileSelect .= "length, ";
            }
            else if ($key === "dc:description"){
                $plSelect .= "text(description) AS ".$val.", ";
                $fileSelect .= $val.", ";
            }
            else {
                $plSelect .= "NULL AS ".$val.", ";
                $fileSelect .= $val.", ";
            }
        }

        $sql = "SELECT * FROM ((".$plSelect."PL.id, 'playlist' AS ftype
                FROM ".$CC_CONFIG["playListTable"]." AS PL
				LEFT JOIN ".$CC_CONFIG['playListTimeView']." PLT ON PL.id = PLT.id)

                UNION

                (".$fileSelect."id, ftype FROM ".$CC_CONFIG["filesTable"]." AS FILES)) AS RESULTS ";

        $sql .= $whereClause;

        if ($orderby) {
            $sql .= " ORDER BY ".join(",", $orderBySql);
        }

        $_SESSION["debugsql"] = $sql;

        $res = $CC_DBC->getAll($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        if (!is_array($res)) {
            $res = array();
        }

        $count = count($res);
        $_SESSION["br"] .= "  COUNT: ".$count;

        $res = array_slice($res, $offset != 0 ? $offset : 0, $limit != 0 ? $limit : 10);

        $eres = array();
        foreach ($res as $it) {
            $eres[] = array(
            	'id' => $it['id'],
                'type' => strtolower($it['ftype']),
                'title' => $it['track_title'],
                'creator' => $it['artist_name'],
                'duration' => $it['length'],
                'source' => $it['album_title'],
                'track_num' => $it['track_number'],
            );
        }
        return array('results'=>$eres, 'cnt'=>$count);
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
     */
    public function bsBrowseCategory($category, $limit=0, $offset=0, $criteria=NULL)
    {
        global $CC_CONFIG, $CC_DBC;

        $pl_cat = array(
            "dcterms:extent" => "length",
    		"dc:title" => "name",
        	"dc:creator" => "creator",
        	"dc:description" => "description"
        	);

        	$category = strtolower($category);
        	$columnName = StoredFile::xmlCategoryToDbColumn($category);
        	if (is_null($columnName)) {
        	    return new PEAR_Error(__FILE__.":".__LINE__." -- could not map XML category to DB column.");
        	}
        	$sql = "SELECT DISTINCT $columnName FROM ".$CC_CONFIG["filesTable"];
        	$limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
        	($offset != 0 ? " OFFSET $offset" : '' );
        	$countRowsSql = "SELECT COUNT(DISTINCT $columnName) FROM ".$CC_CONFIG["filesTable"];

        	//$_SESSION["br"]  = "in Browse Category: ".$category;
        	$cnt = $CC_DBC->GetOne($countRowsSql);
        	if (PEAR::isError($cnt)) {
        	    return $cnt;
        	}
        	$res = $CC_DBC->getCol($sql.$limitPart);
        	if (PEAR::isError($res)) {
        	    return $res;
        	}
        	if (!is_array($res)) {
        	    $res = array();
        	}

        	if (array_key_exists($category, $pl_cat) && $category !== "dcterms:extent") {
        	    $columnName = $pl_cat[$category];

        	    $sql = "SELECT DISTINCT $columnName FROM ".$CC_CONFIG["playListTable"];
        	    $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
        	    ($offset != 0 ? " OFFSET $offset" : '' );
        	    $countRowsSql = "SELECT COUNT(DISTINCT $columnName) FROM ".$CC_CONFIG["playListTable"];

        	    $pl_cnt = $CC_DBC->GetOne($countRowsSql);
        	    if (PEAR::isError($cnt)) {
        	        return $cnt;
        	    }
        	    $pl_res = $CC_DBC->getCol($sql.$limitPart);
        	    if (PEAR::isError($res)) {
        	        return $pl_res;
        	    }
        	    if (!is_array($pl_res)) {
        	        $pl_res = array();
        	    }

        	    $res = array_merge($res, $pl_res);
        	    $res = array_slice($res, 0, $limit);
        	    $cnt = $cnt + $pl_cnt;
        	}
        	else if ($category === "dcterms:extent") {
        	    $columnName = $pl_cat[$category];

        	    $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
        	    ($offset != 0 ? " OFFSET $offset" : '' );

        	    $sql = "SELECT DISTINCT length AS $columnName FROM ".$CC_CONFIG["playListTimeView"];

        	    $countRowsSql = "SELECT COUNT(DISTINCT length) FROM ".$CC_CONFIG["playListTimeView"];

        	    $pl_cnt = $CC_DBC->GetOne($countRowsSql);
        	    if (PEAR::isError($cnt)) {
                	return $cnt;
        	    }
        	    $pl_res = $CC_DBC->getCol($sql.$limitPart);
        	    if (PEAR::isError($res)) {
                	return $pl_res;
        	    }
        	    if (!is_array($pl_res)) {
                	$pl_res = array();
        	    }

        	    $res = array_merge($res, $pl_res);
        	    $res = array_slice($res, 0, $limit);
        	    $cnt = $cnt + $pl_cnt;
        	}

        	return array('results'=>$res, 'cnt'=>$cnt);
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
    //    public function bsExportPlaylistOpen($plids, $type='lspl', $withContent=TRUE)
    //    {
    //        global $CC_CONFIG;
    //        if (!is_array($plids)) {
    //            $plids = array($plids);
    //        }
    //        $gunids = array();
    //        foreach ($plids as $plid) {
    //            $pl = StoredFile::RecallByGunid($plid);
    //            if (is_null($pl) || PEAR::isError($pl)) {
    //                return $pl;
    //            }
    //            if ($withContent) {
    //                $gunidsX = $pl->export();
    //                if (PEAR::isError($gunidsX)) {
    //                    return $gunidsX;
    //                }
    //            } else {
    //                $gunidsX = array(array('gunid'=>$plid, 'type'=>'playlist'));
    //            }
    //            $gunids = array_merge($gunids, $gunidsX);
    //        }
    //        $plExts = array('lspl'=>"lspl", 'smil'=>"smil", 'm3u'=>"m3u");
    //        $plExt = (isset($plExts[$type]) ? $plExts[$type] : "xml" );
    //        $res = array();
    //        $tmpn = tempnam($CC_CONFIG['bufferDir'], 'plExport_');
    //        $tmpf = "$tmpn.tar";
    //        $tmpd = "$tmpn.dir";
    //        mkdir($tmpd);
    //        $tmpdp = "$tmpn.dir/playlist";
    //        mkdir($tmpdp);
    //        if ($withContent) {
    //            $tmpdc = "$tmpn.dir/audioClip";
    //            mkdir($tmpdc);
    //        }
    //        foreach ($gunids as $i => $it) {
    //            $storedFile = StoredFile::RecallByGunid($it['gunid']);
    //            if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //                return $storedFile;
    //            }
    ////            $MDfname = $storedFile->md->getFileName();
    //            $MDfname = $storedFile->md["name"];
    //            if (PEAR::isError($MDfname)) {
    //                return $MDfname;
    //            }
    //            if (file_exists($MDfname)) {
    //                switch ($it['type']) {
    //	                case "playlist":
    //	                    $storedFile = $r = StoredFile::RecallByGunid($it['gunid']);
    //	                    switch ($type) {
    //	                        case "smil":
    //	                            $string = $r = $storedFile->outputToSmil();
    //	                            break;
    //	                        case "m3u":
    //	                            $string = $r = $storedFile->outputToM3u();
    //	                            break;
    //	                        default:
    ////	                            $string = $r = $storedFile->md->genXmlDoc();
    //	                    }
    //	                    if (PEAR::isError($r)) {
    //	                        return $r;
    //	                    }
    //	                    $r = BasicStor::WriteStringToFile($string, "$tmpdp/{$it['gunid']}.$plExt");
    //	                    if (PEAR::isError($r)) {
    //	                        return $r;
    //	                    }
    //	                    break;
    //	                default:
    //	                    copy($MDfname, "$tmpdc/{$it['gunid']}.xml"); break;
    //                } // switch
    //            } // if file_exists()
    //            $RADfname = $storedFile->getRealFileName();
    //            if (PEAR::isError($RADfname)) {
    //                return $RADfname;
    //            }
    //            $RADext = $storedFile->getFileExtension();
    //            if (PEAR::isError($RADext)) {
    //                return $RADext;
    //            }
    //            if (file_exists($RADfname)) {
    //                copy($RADfname, "$tmpdc/{$it['gunid']}.$RADext");
    //            }
    //        }
    //        if (count($plids)==1) {
    //            copy("$tmpdp/$plid.$plExt", "$tmpd/exportedPlaylist.$plExt");
    //        }
    //        $res = `cd $tmpd; tar cf $tmpf * --remove-files`;
    //        @rmdir($tmpdc);
    //        @rmdir($tmpdp);
    //        @rmdir($tmpd);
    //        unlink($tmpn);
    //        $acc = BasicStor::bsAccess($tmpf, 'tar', NULL/*gunid*/, 'access');
    //        if (PEAR::isError($acc)) {
    //            return $acc;
    //        }
    //        return $acc;
    //    }


    /**
     * Close playlist export previously opened by the bsExportPlaylistOpen
     * method
     *
     * @param string $token
     * 		Access token obtained from bsExportPlaylistOpen method call.
     * @return true/PEAR_Error
     */
    //    public function bsExportPlaylistClose($token)
    //    {
    //        $r = BasicStor::bsRelease($token, 'access');
    //        if (PEAR::isError($r)) {
    //            return $r;
    //        }
    //        $file = $r['realFname'];
    //        if (file_exists($file)) {
    //            if(! @unlink($file)){
    //                return PEAR::raiseError(
    //                    "BasicStor::bsExportPlaylistClose: unlink failed ($file)",
    //                    GBERR_FILEIO);
    //            }
    //        }
    //        return TRUE;
    //    }


    /**
     * Import playlist in LS Archive format
     *
     * @param string $plid
     * 		Playlist gunid
     * @param string $aPath
     * 		Absolute path part of imported file (e.g. /home/user/airtime)
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
    //    public function bsImportPlaylistRaw($plid, $aPath, $rPath, $ext, &$gunids, $subjid)
    //    {
    //        $id = BasicStor::IdFromGunid($plid);
    //        if (!is_null($id)) {
    //            return $id;
    //        }
    //        $path = realpath("$aPath/$rPath");
    //        if (FALSE === $path) {
    //            return PEAR::raiseError(
    //                "BasicStor::bsImportPlaylistRaw: file doesn't exist ($aPath/$rPath)"
    //            );
    //        }
    //        switch ($ext) {
    //            case "xml":
    //            case "lspl":
    //                $fname = $plid;
    //                $values = array(
    //                    "filename" => $fname,
    //                    "metadata" => $path,
    //                    "gunid" => $plid,
    //                    "filetype" => "playlist"
    //                );
    //                $storedFile = StoredFile::Insert($values);
    //                $res = $storedFile->getId();
    //                break;
    //            case "smil":
    //                require_once("SmilPlaylist.php");
    //                $res = SmilPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $subjid);
    //                if (PEAR::isError($res)) {
    //                    break;
    //                }
    //                $res = $res->getId();
    //                break;
    //            case "m3u":
    //                require_once("M3uPlaylist.php");
    //                $res = M3uPlaylist::import($this, $aPath, $rPath, $gunids, $plid, $subjid);
    //                if (PEAR::isError($res)) {
    //                    break;
    //                }
    //                $res = $res->getId();
    //                break;
    //            default:
    //                $res = PEAR::raiseError(
    //                    "BasicStor::importPlaylistRaw: unknown playlist format".
    //                    " (gunid:$plid, format:$ext)"
    //                );
    //                break;
    //        }
    //        if (!PEAR::isError($res)) {
    //            $gunids[basename($rPath)] = $plid;
    //        }
    //        return $res;
    //    }


    /**
     * Import playlist in LS Archive format
     *
     * @param string $fpath
     * 		Imported file pathname
     * @param int $subjid
     * 		Local subject (user) id (id of user doing the import)
     * @return int
     * 		Result file local id (or error object)
     */
    //    public function bsImportPlaylist($fpath, $subjid)
    //    {
    //        global $CC_CONFIG;
    //        // untar:
    //        $tmpn = tempnam($CC_CONFIG['bufferDir'], 'plImport_');
    //        $tmpd = "$tmpn.dir";
    //        $tmpdc = "$tmpd/audioClip";
    //        $tmpdp = "$tmpd/playlist";
    //        mkdir($tmpd);
    //        $res = `cd $tmpd; tar xf $fpath`;
    //        // clips:
    //        $d = @dir($tmpdc);
    //        $entries = array();
    //        $gunids = array();
    //        if ($d !== false) {
    //            while (false !== ($entry = $d->read())) {
    //                if (preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)) {
    //                    list(,$gunid, $ext) = $va;
    //                    switch ($ext) {
    //                        case"xml":
    //                            $entries[$gunid]['metadata'] = $entry;
    //                            break;
    //                        default:
    //                            $entries[$gunid]['rawMedia'] = $entry;
    //                            $entries[$gunid]['rawMediaExt'] = $ext;
    //                            $gunids[$entry] = $gunid;
    //                            break;
    //                    }
    //                }
    //            }
    //            $d->close();
    //        }
    //        $res = TRUE;
    //        foreach ($entries as $gunid => $it) {
    //            $rawMedia = "$tmpdc/{$it['rawMedia']}";
    //            if (!file_exists($rawMedia)) {
    //                $rawMedia = NULL;
    //            }
    //            $metadata = "$tmpdc/{$it['metadata']}";
    //            if (!file_exists($metadata)) {
    //                $metadata = NULL;
    //            }
    //            $f = StoredFile::RecallByGunid($gunid);
    //            if (!PEAR::isError($f)) {
    //              $exists = $f->existsFile();
    //              if ( $exists ) {
    //                $res = $f->delete();
    //              }
    //            }
    //            if (!PEAR::isError($res) ) {
    //                $values = array(
    //                    "filename" => $gunid,
    //                    "filepath" => $rawMedia,
    //                    "metadata" => $metadata,
    //                    "gunid" => $gunid,
    //                    "filetype" => "audioclip"
    //                );
    //                $storedFile = StoredFile::Insert($values);
    //                $res = $storedFile->getId();
    //            }
    //            @unlink("$tmpdc/{$it['rawMedia']}");
    //            @unlink("$tmpdc/{$it['metadata']}");
    //            if (PEAR::isError($res)) {
    //                break;
    //            }
    //        }
    //        // playlists:
    //        $d = @dir($tmpdp);
    //        if ($d !== false) {
    //            while ((!PEAR::isError($res)) && false !== ($entry = $d->read())) {
    //                if (preg_match("|^([0-9a-fA-F]{16})\.(.*)$|", $entry, $va)) {
    //                    list(,$gunid, $ext) = $va;
    //                    $res = $this->bsImportPlaylistRaw($gunid,
    //                        $tmpdp, $entry, $ext, $gunids, $subjid);
    //                    unlink("$tmpdp/$entry");
    //                    if (PEAR::isError($res)) {
    //                        break;
    //                    }
    //                }
    //            }
    //            $d->close();
    //        }
    //        //@rmdir($tmpdc); @rmdir($tmpdp); @rmdir($tmpd);
    //        @system("rm -rf $tmpdc");
    //        @system("rm -rf $tmpdp");
    //        @system("rm -rf $tmpd");
    //        @unlink($tmpn);
    //        return $res;
    //    }


    /* --------------------------------------------------------- info methods */

    /**
     * Analyze media file for internal metadata information
     *
     * @param int $id
     * 		Virtual file's local id
     * @return array
     */
    //    public function bsAnalyzeFile($id)
    //    {
    //        $storedFile = StoredFile::Recall($id);
    //        if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //            return $storedFile;
    //        }
    //        $ia = $storedFile->analyzeFile();
    //        return $ia;
    //    }


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
    //    public function bsExistsFile($id, $ftype=NULL, $byGunid=FALSE)
    //    {
    //        if ($byGunid) {
    //            $storedFile = StoredFile::RecallByGunid($id);
    //        } else {
    //            $storedFile = StoredFile::Recall($id);
    //        }
    //        if (is_null($storedFile)) {
    //            return $storedFile;
    //        }
    //        if (PEAR::isError($storedFile)) {
    //            // catch some exceptions
    //            switch ($storedFile->getCode()) {
    //                case GBERR_FILENEX:
    //                case GBERR_FOBJNEX:
    //                    return FALSE;
    //                    break;
    //                default:
    //                	return $storedFile;
    //            }
    //        }
    //        $realFtype = BasicStor::GetType($storedFile->gunid);
    //        if (!is_null($ftype) && (
    //            (strtolower($realFtype) != strtolower($ftype))
    //            // webstreams are subset of audioclips
    //            && !($realFtype == 'webstream' && $ftype == 'audioclip')
    //        )) {
    //            return FALSE;
    //        }
    //        return TRUE;
    //    }


    /* ---------------------------------------------------- redefined methods */
    /**
     * Get object type by id.
     *
     * @param int $oid
     * 		Local object id
     * @return string|PEAR_Error
     */
    //    public static function GetObjType($p_id)
    //    {
    //		    $type = "unknown";
    //		    $f = StoredFile::Recall($p_id);
    //		    return $f->getType();

    //        $gunid = BasicStor::GunidFromId($oid);
    //        if (PEAR::isError($gunid)) {
    //            return $gunid;
    //        }
    //        $ftype = BasicStor::GetType($gunid);
    //        if (PEAR::isError($ftype)) {
    //            return $ftype;
    //        }
    //        if (!is_null($ftype)) {
    //            $type = $ftype;
    //        }
    //        return $type;
    //    }


    /**
     * Add new user
     *
     * @param string $login
     * @param string $pass
     * @param string $realname
     * @return int|PEAR_Error
     */
    public static function addSubj($login, $pass=NULL, $realname='')
    {
        global $CC_CONFIG;
        $uid = Subjects::AddSubj($login, $pass, $realname);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        if (Subjects::IsGroup($uid) === FALSE) {
            $res = Alib::AddPerm($uid, '_all', '0', 'A');
            if (PEAR::isError($res)) {
                return $res;
            }
            $res = Subjects::AddSubjectToGroup($login, $CC_CONFIG['StationPrefsGr']);
            if (PEAR::isError($res)) {
                return $res;
            }
            //                $res = Subjects::AddSubjectToGroup($login, $CC_CONFIG['AllGr']);
            //                if (PEAR::isError($res)) {
            //                    return $res;
            //                }
            }
            return $uid;
    }


    /**
     * Remove user by login
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
        return TRUE;
    }


    /**
     * Authenticate and create session
     *
     * @param string $login
     * @param string $pass
     * @return boolean|sessionId|PEAR_Error
     */
    function login($login, $pass)
    {
        $r = Alib::Login($login, $pass);
        return $r;
    }


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
     * Get local id from global id (in hex).
     *
     * @param string $p_gunid
     * 		Global id
     * @return int
     * 		Local id
     */
    //    public static function IdFromGunid($p_gunid)
    //    {
    //        global $CC_DBC;
    //        global $CC_CONFIG;
    //        return $CC_DBC->getOne("SELECT id FROM ".$CC_CONFIG['filesTable']." WHERE gunid=x'$p_gunid'::bigint");
    //    }

    /**
     * Get local id from global id (big int).
     *
     * @param string $p_gunid
     * 		Global id
     * @return int
     * 		Local id
     */
    //    public static function IdFromGunidBigInt($p_gunid)
    //    {
    //        global $CC_DBC;
    //        global $CC_CONFIG;
    //        return $CC_DBC->getOne("SELECT id FROM ".$CC_CONFIG['filesTable']." WHERE gunid='$p_gunid'");
    //    }


    /**
     * Get global id from local id
     *
     * @param int $p_id
     * 		Local id
     * @return string
     * 		Global id
     */
    //    public static function GunidFromId($p_id)
    //    {
    //        global $CC_CONFIG;
    //        global $CC_DBC;
    //        if (!is_numeric($p_id)) {
    //            return NULL;
    //        }
    //        $gunid = $CC_DBC->getOne("
    //            SELECT to_hex(gunid)as gunid FROM ".$CC_CONFIG['filesTable']."
    //            WHERE id='$p_id'
    //        ");
    //        if (PEAR::isError($gunid)) {
    //            return $gunid;
    //        }
    //        if (is_null($gunid)) {
    //            return NULL;
    //        }
    //        return StoredFile::NormalizeGunid($gunid);
    //    }


    /**
     * Get storage-internal file type
     *
     * @param string $p_gunid
     * 		Global unique id of file
     * @return string
     */
    //    public static function GetType($p_gunid)
    //    {
    //        global $CC_CONFIG;
    //        global $CC_DBC;
    //        $ftype = $CC_DBC->getOne("
    //            SELECT ftype FROM ".$CC_CONFIG['filesTable']."
    //            WHERE gunid=x'$p_gunid'::bigint
    //        ");
    //        return $ftype;
    //    }


    /**
     * Check gunid format
     *
     * @param string $p_gunid
     * 		Global unique ID
     * @return boolean
     */
    //    protected static function CheckGunid($p_gunid)
    //    {
    //        $res = preg_match("|^([0-9a-fA-F]{16})?$|", $p_gunid);
    //        return $res;
    //    }

    /**
     * Set playlist edit flag
     *
     * @param string $p_playlistId
     * 		Playlist unique ID
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
        $pl = Playlist::Recall($p_playlistId);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        $state = $pl->getState();
        if ($p_val) {
            $r = $pl->setState('edited', $p_subjid);
        } else {
            $r = $pl->setState('ready', 'NULL');
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
        $pl = Playlist::Recall($p_playlistId);
        if (is_null($pl) || PEAR::isError($pl)) {
            return $pl;
        }
        if (!$pl->isEdited($p_playlistId)) {
            return FALSE;
        }
        return $pl->isEditedBy($p_playlistId);
    }


    /* ---------------------------------------- redefined "protected" methods */
    /**
     * Copy virtual file.
     * Redefined from parent class.
     *
     * @return int
     * 		New object local id
     */
    //    protected static function CopyObj($id, $newParid, $after=NULL)
    //    {
    //        switch (BasicStor::GetObjType($id)) {
    //            case "audioclip":
    //            case "playlist":
    //            case "webstream":
    //                $storedFile = StoredFile::Recall($id);
    //                if (is_null($storedFile) || PEAR::isError($storedFile)) {
    //                    return $storedFile;
    //                }
    //                $ac2 = StoredFile::CopyOf($storedFile, $nid);
    //                //$ac2->setName(M2tree::GetObjName($nid));
    //                break;
    //            case "File":
    //            default:
    //        }
    //        return $nid;
    //    }


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
    //    public static function RemoveObj($id, $forced=FALSE)
    //    {
    //        $ot = BasicStor::GetObjType($id);
    //        if (PEAR::isError($ot)) {
    //            return $ot;
    //        }
    //        switch ($ot) {
    //            case "audioclip":
    //            case "playlist":
    //            case "webstream":
    //                $storedFile = StoredFile::Recall($id);
    //                if (is_null($storedFile)) {
    //                    return TRUE;
    //                }
    //                if (PEAR::isError($storedFile)) {
    //                    return $storedFile;
    //                }
    //                if ($storedFile->isEdited() && !$forced) {
    //                    return PEAR::raiseError(
    //                        'BasicStor::RemoveObj(): is edited'
    //                    );
    //                }
    //                if ($storedFile->isAccessed() && !$forced) {
    //                    return PEAR::raiseError(
    //                        'BasicStor::RemoveObj(): is accessed'
    //                    );
    //                }
    //                $storedFile->delete();
    //                break;
    //            case "File":
    ////            case "Folder":
    ////            case "Replica":
    //                break;
    //            default:
    //                return PEAR::raiseError(
    //                    "BasicStor::bsDeleteFile: unknown obj type ($ot)"
    //                );
    //        }
    //        $res = Alib::RemoveObj($id);
    //        if (PEAR::isError($res)) {
    //            return $res;
    //        }
    //        return TRUE;
    //    }


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


    /* =============================================== test and debug methods */

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
    //    private function deleteFiles()
    //    {
    //        global $CC_CONFIG, $CC_DBC;
    //        $ids = $CC_DBC->getAll("SELECT id FROM ".$CC_CONFIG['filesTable']);
    //        if (is_array($ids)) {
    //            foreach ($ids as $i => $item) {
    //              $f = StoredFile::Recall($item['id']);
    //              $f->delete();
    //            }
    //        }
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

