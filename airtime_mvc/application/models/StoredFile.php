<?php

require_once 'formatters/LengthFormatter.php';
require_once 'formatters/SamplerateFormatter.php';
require_once 'formatters/BitrateFormatter.php';

/**
 *  Application_Model_StoredFile class
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @see MetaData
 */
class Application_Model_StoredFile
{
    /**
     * @holds propel database object
     */
    private $_file;

    /**
     * @holds PDO object reference
     */
    private $_con;

    /**
     * array of db metadata -> propel
     */
    private $_dbMD = array (
        "track_title"  => "DbTrackTitle",
        "artist_name"  => "DbArtistName",
        "album_title"  => "DbAlbumTitle",
        "genre"        => "DbGenre",
        "mood"         => "DbMood",
        "track_number" => "DbTrackNumber",
        "bpm"          => "DbBpm",
        "label"        => "DbLabel",
        "composer"     => "DbComposer",
        "encoded_by"   => "DbEncodedBy",
        "conductor"    => "DbConductor",
        "year"         => "DbYear",
        "info_url"     => "DbInfoUrl",
        "isrc_number"  => "DbIsrcNumber",
        "copyright"    => "DbCopyright",
        "length"       => "DbLength",
        "bit_rate"     => "DbBitRate",
        "sample_rate"  => "DbSampleRate",
        "mime"         => "DbMime",
        //"md5"          => "DbMd5",
        "ftype"        => "DbFtype",
        "language"     => "DbLanguage",
        "replay_gain"  => "DbReplayGain",
        "directory"    => "DbDirectory",
        "owner_id"     => "DbOwnerId",
        "cuein"        => "DbCueIn",
        "cueout"       => "DbCueOut",
    );

    function __construct($file, $con) {
        $this->_file = $file;
        $this->_con = $con;
    }

    public function getId()
    {
        return $this->_file->getDbId();
    }

    public function getFormat()
    {
        return $this->_file->getDbFtype();
    }

    public function getPropelOrm()
    {
        return $this->_file;
    }

    public function setFormat($p_format)
    {
        $this->_file->setDbFtype($p_format);
    }

    /* This function is only called after liquidsoap
     * has notified that a track has started playing.
     */
    public function setLastPlayedTime($p_now)
    {
        $this->_file->setDbLPtime($p_now);
        /* Normally we would only call save after all columns have been set
         * like in setDbColMetadata(). But since we are only setting one
         * column in this case it is OK.
         */
        $this->_file->save();
    }

    public static function createWithFile($f, $con) {
        $storedFile        = new Application_Model_StoredFile($f, $con);
        return $storedFile;
    }

    /**
     * Set multiple metadata values using defined metadata constants.
     *
     * @param array $p_md
     *  example: $p_md['MDATA_KEY_URL'] = 'http://www.fake.com'
     */
    public function setMetadata($p_md=null)
    {
        if (is_null($p_md)) {
            $this->setDbColMetadata();
        } else {
            $dbMd = array();

            if (isset($p_md["MDATA_KEY_YEAR"])) {
                // We need to make sure to clean this value before
                // inserting into database. If value is outside of range
                // [-2^31, 2^31-1] then postgresl will throw error when
                // trying to retrieve this value. We could make sure
                // number is within these bounds, but simplest is to do
                // substring to 4 digits (both values are garbage, but
                // at least our new garbage value won't cause errors).
                // If the value is 2012-01-01, then substring to first 4
                // digits is an OK result. CC-3771

                $year = $p_md["MDATA_KEY_YEAR"];

                if (strlen($year) > 4) {
                    $year = substr($year, 0, 4);
                }
                if (!is_numeric($year)) {
                    $year = 0;
                }
                $p_md["MDATA_KEY_YEAR"] = $year;
            }

            # Translate metadata attributes from media monitor (MDATA_KEY_*)
            # to their counterparts in constants.php (usually the column names)
            $track_length = $p_md['MDATA_KEY_DURATION'];
            $track_length_in_sec = Application_Common_DateHelper::calculateLengthInSeconds($track_length);
            foreach ($p_md as $mdConst => $mdValue) {
                if (defined($mdConst)) {
                    if ($mdConst == "MDATA_KEY_CUE_OUT") {
                        if ($mdValue == '0.0') {
                            $mdValue = $track_length_in_sec;
                        } else {
                            $this->_file->setDbSilanCheck(true)->save();
                        }
                    }
                    $dbMd[constant($mdConst)] = $mdValue;

                }
            }
            $this->setDbColMetadata($dbMd);
        }
    }

    /**
     * Set multiple metadata values using database columns as indexes.
     *
     * @param array $p_md
     *  example: $p_md['url'] = 'http://www.fake.com'
     */
    public function setDbColMetadata($p_md=null)
    {
        if (is_null($p_md)) {
            foreach ($this->_dbMD as $dbColumn => $propelColumn) {
                $method = "set$propelColumn";
                $this->_file->$method(null);
            }
        } else {
            $owner = $this->_file->getFkOwner();
            // if owner_id is already set we don't want to set it again.
            if (!$owner) { // no owner detected, we try to assign one.
                // if MDATA_OWNER_ID is not set then we default to the
                // first admin user we find
                if (!array_key_exists('owner_id', $p_md)) {
                    //$admins = Application_Model_User::getUsers(array('A'));
                    $admins = Application_Model_User::getUsersOfType('A');
                    if (count($admins) > 0) { // found admin => pick first one
                        $owner = $admins[0];
                    }
                }
                // get the user by id and set it like that
                else {
                    $user = CcSubjsQuery::create()
                        ->findPk($p_md['owner_id']);
                    if ($user) {
                        $owner = $user;
                    }
                }
                if ($owner) {
                    $this->_file->setDbOwnerId( $owner->getDbId() );
                } else {
                    Logging::info("Could not find suitable owner for file
                        '".$p_md['filepath']."'");
                }
            }
            # We don't want to process owner_id in bulk because we already
            # processed it in the code above. This is done because owner_id
            # needs special handling
            if (array_key_exists('owner_id', $p_md)) {
                unset($p_md['owner_id']);
            }
            foreach ($p_md as $dbColumn => $mdValue) {
                // don't blank out name, defaults to original filename on first
                // insertion to database.
                if ($dbColumn == "track_title" && (is_null($mdValue) || $mdValue == "")) {
                    continue;
                }
                # TODO : refactor string evals
                if (isset($this->_dbMD[$dbColumn])) {
                    $propelColumn = $this->_dbMD[$dbColumn];
                    $method       = "set$propelColumn";

                    /* We need to set track_number to null if it is an empty string
                     * because propel defaults empty strings to zeros */
                    if ($dbColumn == "track_number" && empty($mdValue)) $mdValue = null;
                    $this->_file->$method($mdValue);
                }
            }
        }

        $this->_file->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->_file->save($this->_con);
    }

    /**
     * Set metadata element value
     *
     * @param string $category
     *         Metadata element by metadata constant
     * @param string $value
     *         value to store, if NULL then delete record
     */
    public function setMetadataValue($p_category, $p_value)
    {
        // constant() was used because it gets quoted constant name value from
        // api_client.py. This is the wrapper funtion
        $this->setDbColMetadataValue(constant($p_category), $p_value);
    }

    /**
     * Set metadata element value
     *
     * @param string $category
     *         Metadata element by db column
     * @param string $value
     *         value to store, if NULL then delete record
     */
    public function setDbColMetadataValue($p_category, $p_value)
    {
        //don't blank out name, defaults to original filename on first insertion to database.
        if ($p_category == "track_title" && (is_null($p_value) || $p_value == "")) {
            return;
        }
        if (isset($this->_dbMD[$p_category])) {
            // TODO : fix this crust -- RG
            $propelColumn = $this->_dbMD[$p_category];
            $method = "set$propelColumn";
            $this->_file->$method($p_value);
            $this->_file->save();
        }
    }

    /**
     * Get metadata as array, indexed by the column names in the database.
     *
     * @return array
     */
    public function getDbColMetadata()
    {
        $md = array();
        foreach ($this->_dbMD as $dbColumn => $propelColumn) {
            $method = "get$propelColumn";
            $md[$dbColumn] = $this->_file->$method();
        }

        return $md;
    }

    /**
     * Get metadata as array, indexed by the constant names.
     *
     * @return array
     */
    public function getMetadata()
    {
        $c = get_defined_constants(true);
        $md = array();

        /* Create a copy of dbMD here and create a "filepath" key inside of
         * it. The reason we do this here, instead of creating this key inside
         * dbMD is because "filepath" isn't really metadata, and we don't want
         * filepath updated everytime the metadata changes. Also it needs extra
         * processing before we can write it to the database (needs to be split
         * into base and relative path)
         *  */
        $dbmd_copy = $this->_dbMD;
        $dbmd_copy["filepath"] = "DbFilepath";

        foreach ($c['user'] as $constant => $value) {
            if (preg_match('/^MDATA_KEY/', $constant)) {
                if (isset($dbmd_copy[$value])) {
                    $propelColumn  = $dbmd_copy[$value];
                    $method        = "get$propelColumn";
                    $md[$constant] = $this->_file->$method();
                }
            }
        }

        return $md;
    }

    /**
     * Returns an array of playlist objects that this file is a part of.
     * @return array
     */
    public function getPlaylists()
    {
        $con = Propel::getConnection();

        $sql = <<<SQL
SELECT playlist_id
FROM cc_playlist
WHERE file_id = :file_id
SQL;

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':file_id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $ids = $stmt->fetchAll();
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        if (is_array($ids) && count($ids) > 0) {
            return array_map( function ($id) {
                return Application_Model_Playlist::RecallById($id);
            }, $ids);
        } else {
            return array();
        }
    }

    /**
     * Delete stored virtual file
     *
     * @param boolean $p_deleteFile
     *
     */
    public function delete()
    {

        $filepath = $this->getFilePath();
        // Check if the file is scheduled to be played in the future
        if (Application_Model_Schedule::IsFileScheduledInTheFuture($this->getId())) {
            throw new DeleteScheduledFileException();
        }

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        if (!$isAdminOrPM && $this->getFileOwnerId() != $user->getId()) {
            throw new FileNoPermissionException();
        }

        $music_dir = Application_Model_MusicDir::getDirByPK($this->_file->getDbDirectory());
        $type = $music_dir->getType();

        if (file_exists($filepath) && $type == "stor") {
            $data = array("filepath" => $filepath, "delete" => 1);
            try {
                Application_Model_RabbitMq::SendMessageToMediaMonitor("file_delete", $data);
            } catch (Exception $e) {
                Logging::error($e->getMessage());
                return;
            }
        }


        // set hidden flag to true
        $this->_file->setDbHidden(true);
        $this->_file->save();

        // need to explicitly update any playlist's and block's length
        // that contains the file getting deleted
        $fileId = $this->_file->getDbId();
        $plRows = CcPlaylistcontentsQuery::create()->filterByDbFileId()->find();
        foreach ($plRows as $row) {
            $pl = CcPlaylistQuery::create()->filterByDbId($row->getDbPlaylistId($fileId))->findOne();
            $pl->setDbLength($pl->computeDbLength(Propel::getConnection(CcPlaylistPeer::DATABASE_NAME)));
            $pl->save();
        }

        $blRows = CcBlockcontentsQuery::create()->filterByDbFileId($fileId)->find();
        foreach ($blRows as $row) {
            $bl = CcBlockQuery::create()->filterByDbId($row->getDbBlockId())->findOne();
            $bl->setDbLength($bl->computeDbLength(Propel::getConnection(CcBlockPeer::DATABASE_NAME)));
            $bl->save();
        }
    }

    /**
     * This function is for when media monitor detects deletion of file
     * and trying to update airtime side
     *
     * @param boolean $p_deleteFile
     *
     */
    public function deleteByMediaMonitor($deleteFromPlaylist=false)
    {
        $filepath = $this->getFilePath();

        if ($deleteFromPlaylist) {
            Application_Model_Playlist::DeleteFileFromAllPlaylists($this->getId());
        }
        // set file_exists flag to false
        $this->_file->setDbFileExists(false);
        $this->_file->save();
    }


    public function getRealFileExtension() {
        $path = $this->_file->getDbFilepath();
        $path_elements = explode('.', $path);
        if (count($path_elements) < 2) {
            return "";
        } else {
            return $path_elements[count($path_elements) - 1];
        }
    }

    /**
     * Return suitable extension.
     *
     * @return string
     *         file extension without a dot
     */
    public function getFileExtension()
    {
        $possible_ext = $this->getRealFileExtension();
        if ($possible_ext !== "") {
            return $possible_ext;
        }

        // We fallback to guessing the extension from the mimetype if we
        // cannot extract it from the file name

        $mime = $this->_file->getDbMime();

        if ($mime == "audio/ogg" || $mime == "application/ogg") {
            return "ogg";
        } elseif ($mime == "audio/mp3" || $mime == "audio/mpeg") {
            return "mp3";
        } elseif ($mime == "audio/x-flac") {
            return "flac";
        } elseif ($mime == "audio/mp4") {
            return "mp4";
        } else {
            throw new Exception("Unknown $mime");
        }
    }

    /**
     * Get real filename of raw media data
     *
     * @return string
     */
    public function getFilePath()
    {
        $music_dir = Application_Model_MusicDir::getDirByPK($this->
            _file->getDbDirectory());
        $directory = $music_dir->getDirectory();
        $filepath  = $this->_file->getDbFilepath();

        return Application_Common_OsPath::join($directory, $filepath);
    }

    /**
     * Set real filename of raw media data
     *
     * @return string
     */
    public function setFilePath($p_filepath)
    {
        $path_info = Application_Model_MusicDir::splitFilePath($p_filepath);

        if (is_null($path_info)) {
            return -1;
        }
        $musicDir = Application_Model_MusicDir::getDirByPath($path_info[0]);

        $this->_file->setDbDirectory($musicDir->getId());
        $this->_file->setDbFilepath($path_info[1]);
        $this->_file->save($this->_con);
    }

    /**
     * Get the URL to access this file
     */
    public function getFileUrl()
    {
    	$CC_CONFIG = Config::getConfig();

    	$protocol = empty($_SERVER['HTTPS']) ? "http" : "https";

    	$serverName = $_SERVER['SERVER_NAME'];
    	$serverPort = $_SERVER['SERVER_PORT'];
    	$subDir = $CC_CONFIG['baseDir'];

    	if ($subDir[0] === "/") {
    		$subDir = substr($subDir, 1, strlen($subDir) - 1);
    	}

    	$baseUrl = "{$protocol}://{$serverName}:{$serverPort}/{$subDir}";

        return $this->getRelativeFileUrl($baseUrl);
    }

    /**
     * Sometimes we want a relative URL and not a full URL. See bug
     * http://dev.sourcefabric.org/browse/CC-2403
     */
    public function getRelativeFileUrl($baseUrl)
    {
        return $baseUrl."api/get-media/file/".$this->getId().".".$this->getFileExtension();
    }

    public static function Insert($md, $con)
    {
        // save some work by checking if filepath is given right away
        if ( !isset($md['MDATA_KEY_FILEPATH']) ) {
            return null;
        }

        $file = new CcFiles();
        $now  = new DateTime("now", new DateTimeZone("UTC"));
        $file->setDbUtime($now);
        $file->setDbMtime($now);

        $storedFile = new Application_Model_StoredFile($file, $con);

        // removed "//" in the path. Always use '/' for path separator
        // TODO : it might be better to just call OsPath::normpath on the file
        // path. Also note that mediamonitor normalizes the paths anyway
        // before passing them to php so it's not necessary to do this at all

        $filepath = str_replace("//", "/", $md['MDATA_KEY_FILEPATH']);
        $res = $storedFile->setFilePath($filepath);
        if ($res === -1) {
            return null;
        }
        $storedFile->setMetadata($md);

        return $storedFile;
    }

    /* TODO: Callers of this function should use a Propel transaction. Start
     * by creating $con outside the function with beingTransaction() */
    public static function RecallById($p_id=null, $con=null) {
        //TODO
        if (is_null($con)) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        }

        if (isset($p_id)) {
            $f =  CcFilesQuery::create()->findPK(intval($p_id), $con);
            return is_null($f) ? null : self::createWithFile($f, $con);
        } else {
            throw new Exception("No arguments passed to RecallById");
        }
    }

    public function getName()
    {
        $info = pathinfo($this->getFilePath());
        return $info['filename'];
    }

    /**
     * Fetch the Application_Model_StoredFile by looking up its filepath.
     *
     * @param  string  $p_filepath path of file stored in Airtime.
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByFilepath($p_filepath, $con)
    {
        $path_info = Application_Model_MusicDir::splitFilePath($p_filepath);

        if (is_null($path_info)) {
            return null;
        }

        $music_dir = Application_Model_MusicDir::getDirByPath($path_info[0]);
        $file = CcFilesQuery::create()
                        ->filterByDbDirectory($music_dir->getId())
                        ->filterByDbFilepath($path_info[1])
                        ->findOne($con);
        return is_null($file) ? null : self::createWithFile($file, $con);
    }

    public static function RecallByPartialFilepath($partial_path, $con)
    {
        $path_info = Application_Model_MusicDir::splitFilePath($partial_path);

        if (is_null($path_info)) {
            return null;
        }
        $music_dir = Application_Model_MusicDir::getDirByPath($path_info[0]);

        $files = CcFilesQuery::create()
                        ->filterByDbDirectory($music_dir->getId())
                        ->filterByDbFilepath("$path_info[1]%")
                        ->find($con);
        $res = array();
        foreach ($files as $file) {
            $storedFile        = new Application_Model_StoredFile($file, $con);
            $res[]             = $storedFile;
        }

        return $res;
    }


    public static function getLibraryColumns()
    {
        return array("id", "track_title", "artist_name", "album_title",
        "genre", "length", "year", "utime", "mtime", "ftype",
        "track_number", "mood", "bpm", "composer", "info_url",
        "bit_rate", "sample_rate", "isrc_number", "encoded_by", "label",
        "copyright", "mime", "language", "filepath", "owner_id",
        "conductor", "replay_gain", "lptime", "is_playlist", "is_scheduled",
        "cuein", "cueout" );
    }

    public static function searchLibraryFiles($datatables)
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);

        $displayColumns = self::getLibraryColumns();

        $plSelect     = array();
        $blSelect     = array();
        $fileSelect   = array();
        $streamSelect = array();
        foreach ($displayColumns as $key) {

            if ($key === "id") {
                $plSelect[]     = "PL.id AS ".$key;
                $blSelect[]     = "BL.id AS ".$key;
                $fileSelect[]   = "FILES.id AS $key";
                $streamSelect[] = "ws.id AS ".$key;
            } 
            elseif ($key === "track_title") {
                $plSelect[]     = "name AS ".$key;
                $blSelect[]     = "name AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "name AS ".$key;
            } 
            elseif ($key === "ftype") {
                $plSelect[]     = "'playlist'::varchar AS ".$key;
                $blSelect[]     = "'block'::varchar AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "'stream'::varchar AS ".$key;
            } 
            elseif ($key === "artist_name") {
                $plSelect[]     = "login AS ".$key;
                $blSelect[]     = "login AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "login AS ".$key;
            } 
            elseif ($key === "owner_id") {
                $plSelect[]     = "login AS ".$key;
                $blSelect[]     = "login AS ".$key;
                $fileSelect[]   = "sub.login AS $key";
                $streamSelect[] = "login AS ".$key;
            } 
            elseif ($key === "replay_gain") {
                $plSelect[]     = "NULL::NUMERIC AS ".$key;
                $blSelect[]     = "NULL::NUMERIC AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "NULL::NUMERIC AS ".$key;
            } 
            elseif ($key === "lptime") {
                $plSelect[]     = "NULL::TIMESTAMP AS ".$key;
                $blSelect[]     = "NULL::TIMESTAMP AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = $key;
            } 
            elseif ($key === "is_scheduled" || $key === "is_playlist") {
                $plSelect[]     = "NULL::boolean AS ".$key;
                $blSelect[]     = "NULL::boolean AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "NULL::boolean AS ".$key;
            } 
            elseif ($key === "cuein" || $key === "cueout") {
                $plSelect[]     = "NULL::INTERVAL AS ".$key;
                $blSelect[]     = "NULL::INTERVAL AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "NULL::INTERVAL AS ".$key;
            }
            //file length is displayed based on cueout - cuein.
            else if ($key === "length") {
            	$plSelect[]     = $key;
            	$blSelect[]     = $key;
            	$fileSelect[]   = "(cueout - cuein)::INTERVAL AS length";
            	$streamSelect[] = $key;
            }
            //same columns in each table.
            else if (in_array($key, array("utime", "mtime"))) {
                $plSelect[]     = $key;
                $blSelect[]     = $key;
                $fileSelect[]   = $key;
                $streamSelect[] = $key;
            } 
            elseif ($key === "year") {
                $plSelect[]     = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
                $blSelect[]     = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
                $fileSelect[]   = "year AS ".$key;
                $streamSelect[] = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
            }
            //need to cast certain data as ints for the union to search on.
            else if (in_array($key, array("track_number", "bit_rate", "sample_rate", "bpm"))) {
                $plSelect[]     = "NULL::int AS ".$key;
                $blSelect[]     = "NULL::int AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "NULL::int AS ".$key;
            } 
            elseif ($key === "filepath") {
                $plSelect[]     = "NULL::VARCHAR AS ".$key;
                $blSelect[]     = "NULL::VARCHAR AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "url AS ".$key;
            } 
            else if ($key == "mime") {
                $plSelect[]     = "NULL::VARCHAR AS ".$key;
                $blSelect[]     = "NULL::VARCHAR AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = $key;
            } 
            else {
                $plSelect[]     = "NULL::text AS ".$key;
                $blSelect[]     = "NULL::text AS ".$key;
                $fileSelect[]   = $key;
                $streamSelect[] = "NULL::text AS ".$key;
            }
        }

        $plSelect     = "SELECT ". join(",", $plSelect);
        $blSelect     = "SELECT ". join(",", $blSelect);
        $fileSelect   = "SELECT ". join(",", $fileSelect);
        $streamSelect = "SELECT ". join(",", $streamSelect);

        $type = intval($datatables["type"]);

        $plTable = "({$plSelect} FROM cc_playlist AS PL LEFT JOIN cc_subjs AS sub ON (sub.id = PL.creator_id))";
        $blTable = "({$blSelect} FROM cc_block AS BL LEFT JOIN cc_subjs AS sub ON (sub.id = BL.creator_id))";
        $fileTable = "({$fileSelect} FROM cc_files AS FILES LEFT JOIN cc_subjs AS sub ON (sub.id = FILES.owner_id) WHERE file_exists = 'TRUE' AND hidden='FALSE')";
        //$fileTable = "({$fileSelect} FROM cc_files AS FILES WHERE file_exists = 'TRUE')";
        $streamTable = "({$streamSelect} FROM cc_webstream AS ws LEFT JOIN cc_subjs AS sub ON (sub.id = ws.creator_id))";
        $unionTable = "({$plTable} UNION {$blTable} UNION {$fileTable} UNION {$streamTable}) AS RESULTS";

        //choose which table we need to select data from.
        // TODO : use constants instead of numbers -- RG
        switch ($type) {
            case 0:
                $fromTable = $unionTable;
                break;
            case 1:
                $fromTable = $fileTable." AS File"; //need an alias for the table if it's standalone.
                break;
            case 2:
                $fromTable = $plTable." AS Playlist"; //need an alias for the table if it's standalone.
                break;
            case 3:
                $fromTable = $blTable." AS Block"; //need an alias for the table if it's standalone.
                break;
            case 4:
                $fromTable = $streamTable." AS StreamTable"; //need an alias for the table if it's standalone.
                break;
            default:
                $fromTable = $unionTable;
        }

        // update is_scheduled to false for tracks that
        // have already played out
        self::updatePastFilesIsScheduled();
        $results = Application_Model_Datatables::findEntries($con, $displayColumns, $fromTable, $datatables);

        $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $utcTimezone = new DateTimeZone("UTC");
        
        foreach ($results['aaData'] as &$row) {
            $row['id'] = intval($row['id']);

            //taken from Datatables.php, needs to be cleaned up there.
            if (isset($r['ftype'])) {
                if ($r['ftype'] == 'playlist') {
                    $pl = new Application_Model_Playlist($r['id']);
                    $r['length'] = $pl->getLength();
                } elseif ($r['ftype'] == "block") {
                    $bl = new Application_Model_Block($r['id']);
                    $r['bl_type'] = $bl->isStatic() ? 'static' : 'dynamic';
                    $r['length']  = $bl->getLength();
                }
            }

            if ($row['ftype'] === "audioclip") {

                $cuein_formatter = new LengthFormatter($row["cuein"]);
                $row["cuein"] = $cuein_formatter->format();

                $cueout_formatter = new LengthFormatter($row["cueout"]);
                $row["cueout"] = $cueout_formatter->format();

                $cuein = Application_Common_DateHelper::playlistTimeToSeconds($row["cuein"]);
                $cueout = Application_Common_DateHelper::playlistTimeToSeconds($row["cueout"]);
                $row_length = Application_Common_DateHelper::secondsToPlaylistTime($cueout - $cuein);

                $formatter = new SamplerateFormatter($row['sample_rate']);
                $row['sample_rate'] = $formatter->format();

                $formatter = new BitrateFormatter($row['bit_rate']);
                $row['bit_rate'] = $formatter->format();

                //soundcloud status
                $file = Application_Model_StoredFile::RecallById($row['id']);
                $row['soundcloud_id'] = $file->getSoundCloudId();

                // for audio preview
                $row['audioFile'] = $row['id'].".".pathinfo($row['filepath'], PATHINFO_EXTENSION);

            }
            else {

                $row['audioFile'] = $row['id'];
                $row_length = $row['length'];
            }

            $len_formatter = new LengthFormatter($row_length);
            $row['length'] = $len_formatter->format();
            
            //convert mtime and utime to localtime
            $row['mtime'] = new DateTime($row['mtime'], $utcTimezone);
            $row['mtime']->setTimeZone($displayTimezone);
            $row['mtime'] = $row['mtime']->format('Y-m-d H:i:s');
            $row['utime'] = new DateTime($row['utime'], $utcTimezone);
            $row['utime']->setTimeZone($displayTimezone);
            $row['utime'] = $row['utime']->format('Y-m-d H:i:s');
            
            //need to convert last played to localtime if it exists.
            if (isset($row['lptime'])) {
            	$row['lptime'] = new DateTime($row['lptime'], $utcTimezone);
            	$row['lptime']->setTimeZone($displayTimezone);
            	$row['lptime'] = $row['lptime']->format('Y-m-d H:i:s');
            }

            // we need to initalize the checkbox and image row because we do not retrieve
            // any data from the db for these and datatables will complain
            $row['checkbox'] = "";
            $row['image'] = "";

            $type = substr($row['ftype'], 0, 2);
            $row['tr_id'] = "{$type}_{$row['id']}";
        }

        return $results;
    }

    public static function uploadFile($p_targetDir)
    {
        // HTTP headers for no cache etc
        header('Content-type: text/plain; charset=UTF-8');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Settings
        $cleanupTargetDir = false; // Remove old files
        $maxFileAge       = 60 * 60; // Temp file age in seconds

        // 5 minutes execution time
        @set_time_limit(5 * 60);
        // usleep(5000);

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        # TODO : should not log __FILE__ itself. there is general logging for
        #  this
        Logging::info(__FILE__.":uploadFile(): filename=$fileName to $p_targetDir");
        // Clean the fileName for security reasons
        //this needs fixing for songs not in ascii.
        //$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

        // Create target dir
        if (!file_exists($p_targetDir))
            @mkdir($p_targetDir);

        // Remove old temp files
        if (is_dir($p_targetDir) && ($dir = opendir($p_targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $filePath = $p_targetDir . DIRECTORY_SEPARATOR . $file;

                // Remove temp files if they are older than the max age
                if (preg_match('/\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
                    @unlink($filePath);
            }

            closedir($dir);
        } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": _("Failed to open temp directory.")}, "id" : "id"}');

        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        // create temp file name (CC-3086)
        // we are not using mktemp command anymore.
        // plupload support unique_name feature.
        $tempFilePath= $p_targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Old IBM code...
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while (($buff = fread($in, 4096)))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": _("Failed to open input stream.")}, "id" : "id"}');

                    fclose($out);
                    unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": _("Failed to open output stream.")}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": _("Failed to move uploaded file.")}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while (($buff = fread($in, 4096)))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": _("Failed to open input stream.")}, "id" : "id"}');

                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": _("Failed to open output stream.")}, "id" : "id"}');
        }

        return $tempFilePath;
    }

    /**
     * Check, using disk_free_space, the space available in the $destination_folder folder to see if it has
     * enough space to move the $audio_file into and report back to the user if not.
     **/
    public static function isEnoughDiskSpaceToCopy($destination_folder, $audio_file)
    {
        //check to see if we have enough space in the /organize directory to copy the file
        $freeSpace = disk_free_space($destination_folder);
        $fileSize = filesize($audio_file);

        return $freeSpace >= $fileSize;
    }

    public static function copyFileToStor($p_targetDir, $fileName, $tempname)
    {
        $audio_file = $p_targetDir . DIRECTORY_SEPARATOR . $tempname;
        Logging::info('copyFileToStor: moving file '.$audio_file);

        $storDir = Application_Model_MusicDir::getStorDir();
        $stor    = $storDir->getDirectory();
        // check if "organize" dir exists and if not create one
        if (!file_exists($stor."/organize")) {
            if (!mkdir($stor."/organize", 0777)) {
                return array(
                    "code"    => 109,
                    "message" => _("Failed to create 'organize' directory."));
            }
        }

        if (chmod($audio_file, 0644) === false) {
            Logging::info("Warning: couldn't change permissions of $audio_file to 0644");
        }

        // Check if we have enough space before copying
        if (!self::isEnoughDiskSpaceToCopy($stor, $audio_file)) {
            $freeSpace = disk_free_space($stor);
            $fileSize = filesize($audio_file);

            return array("code" => 107,
                "message" => sprintf(_("The file was not uploaded, there is "
                ."%s MB of disk space left and the file you are "
                ."uploading has a size of %s MB."), $freeSpace, $fileSize));
        }

        // Check if liquidsoap can play this file
        if (!self::liquidsoapFilePlayabilityTest($audio_file)) {
            return array(
                "code"    => 110,
                "message" => _("This file appears to be corrupted and will not "
                ."be added to media library."));
        }

        // Did all the checks for real, now trying to copy
        $audio_stor = Application_Common_OsPath::join($stor, "organize",
            $fileName);
        $user = Application_Model_User::getCurrentUser();
        if (is_null($user)) {
            $uid = Application_Model_User::getFirstAdminId();
        } else {
            $uid = $user->getId();
        }
        $id_file = "$audio_stor.identifier";
        if (file_put_contents($id_file, $uid) === false) {
            Logging::info("Could not write file to identify user: '$uid'");
            Logging::info("Id file path: '$id_file'");
            Logging::info("Defaulting to admin (no identification file was
                written)");
        } else {
            Logging::info("Successfully written identification file for
                uploaded '$audio_stor'");
        }
        //if the uploaded file is not UTF-8 encoded, let's encode it. Assuming source
        //encoding is ISO-8859-1
        $audio_stor = mb_detect_encoding($audio_stor, "UTF-8") == "UTF-8" ? $audio_stor : utf8_encode($audio_stor);
        Logging::info("copyFileToStor: moving file $audio_file to $audio_stor");
        // Martin K.: changed to rename: Much less load + quicker since this is
        // an atomic operation
        if (@rename($audio_file, $audio_stor) === false) {
            //something went wrong likely there wasn't enough space in .
            //the audio_stor to move the file too warn the user that   .
            //the file wasn't uploaded and they should check if there  .
            //is enough disk space                                     .
            unlink($audio_file); //remove the file after failed rename
            unlink($id_file); // Also remove the identifier file

            return array(
                "code"    => 108,
                "message" => _("The file was not uploaded, this error can occur if the computer "
                ."hard drive does not have enough disk space or the stor "
                ."directory does not have correct write permissions."));
        }
        // Now that we successfully added this file, we will add another tag
        // file that will identify the user that owns it
        return null;
    }

    /*
     * Pass the file through Liquidsoap and test if it is readable. Return True if readable, and False otherwise.
     */
    public static function liquidsoapFilePlayabilityTest($audio_file)
    {
        $LIQUIDSOAP_ERRORS = array('TagLib: MPEG::Properties::read() -- Could not find a valid last MPEG frame in the stream.');

        // Ask Liquidsoap if file is playable
        /* CC-5990/5991 - Changed to point directly to liquidsoap, removed PATH export */
        $command = sprintf('liquidsoap -v -c "output.dummy(audio_to_stereo(single(%s)))" 2>&1',
            escapeshellarg($audio_file));

        exec($command, $output, $rv);

        $isError = count($output) > 0 && in_array($output[0], $LIQUIDSOAP_ERRORS);

        Logging::info("Is error?! : " . $isError);
        Logging::info("ls playability response: " . $rv);
        return ($rv == 0 && !$isError);
    }

    public static function getFileCount()
    {
        $sql = "SELECT count(*) as cnt FROM cc_files WHERE file_exists";
        return Application_Common_Database::prepareAndExecute($sql, array(),
            Application_Common_Database::COLUMN);
    }

    /**
     *
     * Enter description here ...
     * @param $dir_id - if this is not provided, it returns all files with full
     * path constructed.
     */
    public static function listAllFiles($dir_id=null, $all=true)
    {
        $con = Propel::getConnection();

        $sql = <<<SQL
SELECT filepath AS fp
FROM CC_FILES AS f
WHERE f.directory = :dir_id
SQL;

        if (!$all) {
            $sql .= " AND f.file_exists = 'TRUE'";
        }

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':dir_id', $dir_id);

        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        $results = array();
        foreach ($rows as $row) {
            $results[] = $row["fp"];
        }

        return $results;
    }

    //TODO: MERGE THIS FUNCTION AND "listAllFiles" -MK
    public static function listAllFiles2($dir_id=null, $limit="ALL")
    {
        $con = Propel::getConnection();

        $sql = <<<SQL
SELECT id,
       filepath AS fp
FROM cc_files
WHERE directory = :dir_id
  AND file_exists = 'TRUE'
  AND replay_gain IS NULL LIMIT :lim
SQL;

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':dir_id', $dir_id);
        $stmt->bindParam(':lim', $limit);

        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return $rows;
    }

    public static function getAllFilesWithoutSilan() {
        $con = Propel::getConnection();

        $sql = <<<SQL
SELECT f.id,
       m.directory || f.filepath AS fp
FROM cc_files as f
JOIN cc_music_dirs as m ON f.directory = m.id
WHERE file_exists = 'TRUE'
  AND silan_check IS FALSE Limit 100
SQL;
        $stmt = $con->prepare($sql);

        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return $rows;
    }

    /* Gets number of tracks uploaded to
     * Soundcloud in the last 24 hours
     */
    public static function getSoundCloudUploads()
    {
        try {

            $sql = <<<SQL
SELECT soundcloud_id AS id,
       soundcloud_upload_time
FROM CC_FILES
WHERE (id != -2
       AND id != -3)
  AND (soundcloud_upload_time >= (now() - (INTERVAL '1 day')))
SQL;

            $rows = Application_Common_Database::prepareAndExecute($sql);

            return count($rows);
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::info("Could not connect to database.");
            exit;
        }

    }

    public function setSoundCloudLinkToFile($link_to_file)
    {
        $this->_file->setDbSoundCloudLinkToFile($link_to_file)
        ->save();
    }

    public function getSoundCloudLinkToFile()
    {
        return $this->_file->getDbSoundCloudLinkToFile();
    }

    public function setSoundCloudFileId($p_soundcloud_id)
    {
        $this->_file->setDbSoundCloudId($p_soundcloud_id)
            ->save();
    }

    public function getSoundCloudId()
    {
        return $this->_file->getDbSoundCloudId();
    }

    public function setSoundCloudErrorCode($code)
    {
        $this->_file->setDbSoundCloudErrorCode($code)
            ->save();
    }

    public function getSoundCloudErrorCode()
    {
        return $this->_file->getDbSoundCloudErrorCode();
    }

    public function setSoundCloudErrorMsg($msg)
    {
        $this->_file->setDbSoundCloudErrorMsg($msg)
            ->save();
    }

    public function getSoundCloudErrorMsg()
    {
        return $this->_file->getDbSoundCloudErrorMsg();
    }

    public function getDirectory()
    {
        return $this->_file->getDbDirectory();
    }

    public function setFileExistsFlag($flag)
    {
        $this->_file->setDbFileExists($flag)
            ->save();
    }
    public function setFileHiddenFlag($flag)
    {
        $this->_file->setDbHidden($flag)
            ->save();
    }
    public function setSoundCloudUploadTime($time)
    {
        $this->_file->setDbSoundCloundUploadTime($time)
            ->save();
    }


    // This method seems to be unsued everywhere so I've commented it out
    // If it's absence does not have any effect then it will be completely
    // removed soon
    //public function getFileExistsFlag()
    //{
        //return $this->_file->getDbFileExists();
    //}

    public function getFileOwnerId()
    {
        return $this->_file->getDbOwnerId();
    }

    // note: never call this method from controllers because it does a sleep
    public function uploadToSoundCloud()
    {
        $CC_CONFIG = Config::getConfig();

        $file = $this->_file;
        if (is_null($file)) {
            return "File does not exist";
        }
        if (Application_Model_Preference::GetUploadToSoundcloudOption()) {
            for ($i=0; $i<$CC_CONFIG['soundcloud-connection-retries']; $i++) {
                $description = $file->getDbTrackTitle();
                $tag         = array();
                $genre       = $file->getDbGenre();
                $release     = $file->getDbUtime();
                try {
                    $soundcloud     = new Application_Model_Soundcloud();
                    $soundcloud_res = $soundcloud->uploadTrack(
                        $this->getFilePath(), $this->getName(), $description,
                        $tag, $release, $genre);
                    $this->setSoundCloudFileId($soundcloud_res['id']);
                    $this->setSoundCloudLinkToFile($soundcloud_res['permalink_url']);
                    $this->setSoundCloudUploadTime(new DateTime("now"), new DateTimeZone("UTC"));
                    break;
                } catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
                    $code = $e->getHttpCode();
                    $msg  = $e->getHttpBody();
                    // TODO : Do not parse JSON by hand
                    $temp = explode('"error":',$msg);
                    $msg  = trim($temp[1], '"}');
                    $this->setSoundCloudErrorCode($code);
                    $this->setSoundCloudErrorMsg($msg);
                    // setting sc id to -3 which indicates error
                    $this->setSoundCloudFileId(SOUNDCLOUD_ERROR);
                    if (!in_array($code, array(0, 100))) {
                        break;
                    }
                }

                sleep($CC_CONFIG['soundcloud-connection-wait']);
            }
        }
    }

    public static function setIsPlaylist($p_playlistItems, $p_type, $p_status) {
        foreach ($p_playlistItems as $item) {
            $file = self::RecallById($item->getDbFileId());
            $fileId = $file->_file->getDbId();
            if ($p_type == 'playlist') {
                // we have to check if the file is in another playlist before
                // we can update
                if (!is_null($fileId) && !in_array($fileId, Application_Model_Playlist::getAllPlaylistFiles())) {
                    $file->_file->setDbIsPlaylist($p_status)->save();
                }
            } elseif ($p_type == 'block') {
                if (!is_null($fileId) && !in_array($fileId, Application_Model_Block::getAllBlockFiles())) {
                    $file->_file->setDbIsPlaylist($p_status)->save();
                }
            }
        }
    }

    public static function setIsScheduled($fileId, $status) {

        $file = self::RecallById($fileId);
        $updateIsScheduled = false;

        if (!is_null($fileId) && !in_array($fileId,
            Application_Model_Schedule::getAllFutureScheduledFiles())) {
            $file->_file->setDbIsScheduled($status)->save();
            $updateIsScheduled = true;
        }

        return $updateIsScheduled;
    }

    /**
     * 
     * Updates the is_scheduled flag to false for tracks that are no longer
     * scheduled in the future. We do this by checking the difference between
     * all files scheduled in the future and all files with is_scheduled = true.
     * The difference of the two result sets is what we need to update.
     */
    public static function updatePastFilesIsScheduled()
    {
        $futureScheduledFilesSelectCriteria = new Criteria();
        $futureScheduledFilesSelectCriteria->addSelectColumn(CcSchedulePeer::FILE_ID);
        $futureScheduledFilesSelectCriteria->setDistinct();
        $futureScheduledFilesSelectCriteria->add(CcSchedulePeer::ENDS, gmdate("Y-m-d H:i:s"), Criteria::GREATER_THAN);
        $stmt = CcSchedulePeer::doSelectStmt($futureScheduledFilesSelectCriteria);
        $filesScheduledInFuture = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $filesCurrentlySetWithIsScheduledSelectCriteria = new Criteria();
        $filesCurrentlySetWithIsScheduledSelectCriteria->addSelectColumn(CcFilesPeer::ID);
        $filesCurrentlySetWithIsScheduledSelectCriteria->add(CcFilesPeer::IS_SCHEDULED, true);
        $stmt = CcFilesPeer::doSelectStmt($filesCurrentlySetWithIsScheduledSelectCriteria);
        $filesCurrentlySetWithIsScheduled = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        $diff = array_diff($filesCurrentlySetWithIsScheduled, $filesScheduledInFuture);
        
        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        $selectCriteria = new Criteria();
        $selectCriteria->add(CcFilesPeer::ID, $diff, Criteria::IN);
        $updateCriteria = new Criteria();
        $updateCriteria->add(CcFilesPeer::IS_SCHEDULED, false);
        BasePeer::doUpdate($selectCriteria, $updateCriteria, $con);
    }
}

class DeleteScheduledFileException extends Exception {}
class FileDoesNotExistException extends Exception {}
class FileNoPermissionException extends Exception {}
