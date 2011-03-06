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
require_once(dirname(__FILE__)."/Playlist.php");

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


    /* ----------------------------------------------------- metadata methods */

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

        $plSelect = "SELECT ";
        $fileSelect = "SELECT ";
        $_SESSION["br"] = "";
        foreach (Metadata::GetMapMetadataXmlToDb() as $key => $val){
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
        if ($perm) {
            return TRUE;
        }
        $adesc = "[".join(',',$acts)."]";
        return PEAR::raiseError(
            "BasicStor::$adesc: access denied", GBERR_DENY);
    }


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

