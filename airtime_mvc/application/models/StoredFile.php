<?php

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
     * @var CcFiles
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
        "description"  => "DbDescription",
        "artwork"      => "DbArtwork",
        "track_type"   => "DbTrackType"
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

    /**
     * @return CcFiles
     */
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
        $storedFile = new Application_Model_StoredFile($f, $con);
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
            // in order to edit the owner of a file we see if owner_id exists in the track form metadata otherwise
            // we determine it via the algorithm below
            if (!array_key_exists('owner_id', $p_md)) {
                $owner = $this->_file->getFkOwner();
                // if owner_id is already set we don't want to set it again.
                if (!$owner) { // no owner detected, we try to assign one.
                    // if MDATA_OWNER_ID is not set then we default to the
                    // first admin user we find
                    if (!array_key_exists('owner_id', $p_md)) {
                        //$admins = Application_Model_User::getUsers(array('A'));
                        $admins = array_merge(Application_Model_User::getUsersOfType('A')->getData(),
                            Application_Model_User::getUsersOfType('S')->getData());
                        if (count($admins) > 0) { // found admin => pick first one
                            $owner = $admins[0];
                        }
                    } // get the user by id and set it like that
                    else {
                        $user = CcSubjsQuery::create()
                            ->findPk($p_md['owner_id']);
                        if ($user) {
                            $owner = $user;
                        }
                    }
                    if ($owner) {
                        $this->_file->setDbOwnerId($owner->getDbId());
                    } else {
                        Logging::info("Could not find suitable owner for file
                        '" . $p_md['filepath'] . "'");
                    }
                }
            }
            foreach ($p_md as $dbColumn => $mdValue) {
                // don't blank out name, defaults to original filename on first
                // insertion to database.
                if ($dbColumn == "track_title" && (is_null($mdValue) || $mdValue == "")) {
                    continue;
                }

                // Bpm gets POSTed as a string type. With Propel 1.6 this value
                // was casted to an integer type before saving it to the db. But
                // Propel 1.7 does not do this
                if ($dbColumn == "bpm") {
                    $mdValue = (int) $mdValue;
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
     * Check if the file (on disk) corresponding to this class exists or not.
     * @return boolean true if the file exists, false otherwise.
     */
    public function existsOnDisk()
    {
        $exists = false;
        try {
            $filePaths = $this->getFilePaths();
            $filePath = $filePaths[0];
            $exists = (file_exists($filePath) && !is_dir($filePath));
        } catch (Exception $e) {
            return false;
        }
        return $exists;
    }

    /**
     * Deletes the physical file from the local file system or from the cloud
     *
     */
    public function delete($quiet=false)
    {
        // Check if the file is scheduled to be played in the future
        if (Application_Model_Schedule::IsFileScheduledInTheFuture($this->_file->getCcFileId())) {
            throw new DeleteScheduledFileException();
        }

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $isAdminOrPM = $user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        if (!$isAdminOrPM && $this->getFileOwnerId() != $user->getId()) {
            throw new FileNoPermissionException();
        }
        $file_id = $this->_file->getDbId();
        Logging::info($file_id);
        Logging::info("User ".$user->getLogin()." is deleting file: ".$this->_file->getDbTrackTitle()." - file id: ".$file_id);

        $filesize = $this->_file->getFileSize();
        if ($filesize < 0) {
            throw new Exception("Cannot delete file with filesize ".$filesize);
        }

        //Delete the physical file from either the local stor directory
        //or from the cloud
        if ($this->_file->getDbImportStatus() == CcFiles::IMPORT_STATUS_SUCCESS) {
            try {
                $this->_file->deletePhysicalFile();
            }
            catch (Exception $e)
            {
                if ($quiet) {
                    Logging::info($e);
                } else {
                    //Just log the exception and continue.
                    Logging::error($e);
                }
            }
        }

        //Update the user's disk usage
        Application_Model_Preference::updateDiskUsage(-1 * $filesize);

        //Explicitly update any playlist's and block's length that contain
        //the file getting deleted
        self::updateBlockAndPlaylistLength($this->_file->getDbId());

        //delete the file record from cc_files (and cloud_file, if applicable)
        $this->_file->delete();
    }

    /*
     * This function is meant to be called when a file is getting
     * deleted from the library. It re-calculates the length of
     * all blocks and playlists that contained the deleted file.
     */
    private static function updateBlockAndPlaylistLength($fileId)
    {
        $plRows = CcPlaylistcontentsQuery::create()->filterByDbFileId($fileId)->find();
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
        if ($deleteFromPlaylist) {
            Application_Model_Playlist::DeleteFileFromAllPlaylists($this->getId());
        }
        // set file_exists flag to false
        $this->_file->setDbFileExists(false);
        $this->_file->save();
    }

    /**
     * Get the absolute filepath
     *
     * @return array of strings
     */
    public function getFilePaths()
    {
        assert($this->_file);

        return $this->_file->getURLsForTrackPreviewOrDownload();
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

        if ($protocol === 'https' && $serverPort == 80) {
            $serverPort = 443;
        }

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
        return $baseUrl."api/get-media/file/".$this->getId();
    }

    public function getResourceId()
    {
        return $this->_file->getResourceId();
    }

    public function getFileSize()
    {
        $filesize = $this->_file->getFileSize();

        // It's OK for the file size to be zero. Pypo will make a request to Airtime and update
        // the file size and md5 hash if they are not set.
        if ($filesize < 0) {
            throw new Exception ("Could not determine filesize for file id: ".$this->_file->getDbId().". Filesize: ".$filesize);
        }
        return $filesize;
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
    /**
     * @param int $p_id
     * @param Propel Connection
     *
     * @return Application_Model_StoredFile
     * @throws Exception
     */
    public static function RecallById($p_id=null, $con=null) {
        //TODO
        if (is_null($con)) {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        }

        if (isset($p_id)) {
            $p_id = intval($p_id);

            $storedFile =  CcFilesQuery::create()->findPK($p_id, $con);
            if (is_null($storedFile)) {
                throw new Exception("Could not recall file with id: ".$p_id);
            }

            //Attempt to get the cloud file object and return it. If no cloud
            //file object is found then we are dealing with a regular stored
            //object so return that
            $cloudFile = CloudFileQuery::create()->findOneByCcFileId($p_id);

            if (is_null($cloudFile)) {
                return self::createWithFile($storedFile, $con);
            } else {
                return self::createWithFile($cloudFile, $con);
            }
        } else {
            throw new Exception("No arguments passed to RecallById");
        }
    }

    public function getName()
    {
        return $this->_file->getFilename();
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
        "cuein", "cueout", "description", "artwork", "track_type" );
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
        switch ($type) {
            case MediaType::FILE:
                $fromTable = $fileTable." AS File"; //need an alias for the table if it's standalone.
                break;
            case MediaType::PLAYLIST:
                $fromTable = $plTable." AS Playlist"; //need an alias for the table if it's standalone.
                break;
            case MediaType::BLOCK:
                $fromTable = $blTable." AS Block"; //need an alias for the table if it's standalone.
                break;
            case MediaType::WEBSTREAM:
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

        $storDir = Application_Model_MusicDir::getStorDir();
        $fp = $storDir->getDirectory();

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

                $get_artwork = FileDataHelper::getArtworkData($row['artwork'], 32, $fp);
                $row['artwork_data'] = $get_artwork;

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
            $row['mtime'] = $row['mtime']->format(DEFAULT_TIMESTAMP_FORMAT);
            $row['utime'] = new DateTime($row['utime'], $utcTimezone);
            $row['utime']->setTimeZone($displayTimezone);
            $row['utime'] = $row['utime']->format(DEFAULT_TIMESTAMP_FORMAT);

            //need to convert last played to localtime if it exists.
            if (isset($row['lptime'])) {
            	$row['lptime'] = new DateTime($row['lptime'], $utcTimezone);
            	$row['lptime']->setTimeZone($displayTimezone);
            	$row['lptime'] = $row['lptime']->format(DEFAULT_TIMESTAMP_FORMAT);
            }

            // we need to initalize the checkbox and image row because we do not retrieve
            // any data from the db for these and datatables will complain
            $row['checkbox'] = "";
            $row['image'] = "";
            $row['options'] = "";

            $type = substr($row['ftype'], 0, 2);
            $row['tr_id'] = "{$type}_{$row['id']}";
        }

        return $results;
    }

    /**
     * Copy a newly uploaded audio file from its temporary upload directory
     * on the local disk (like /tmp) over to Airtime's "stor" directory,
     * which is where all ingested music/media live.
     *
     * This is done in PHP here on the web server rather than in libretime-analyzer because
     * the libretime-analyzer might be running on a different physical computer than the web server,
     * and it probably won't have access to the web server's /tmp folder. The stor/organize directory
     * is, however, both accessible to the machines running libretime-analyzer and the web server
     * on Airtime Pro.
     *
     * The file is actually copied to "stor/organize", which is a staging directory where files go
     * before they're processed by libretime-analyzer, which then moves them to "stor/imported" in the final
     * step.
     *
     * @param string $tempFilePath
     * @param string $originalFilename
     * @param bool $copyFile Copy the file instead of moving it.
     * @throws Exception
     * @return Ambigous <unknown, string>
     */
    public static function moveFileToStor($tempFilePath, $originalFilename, $copyFile=false)
    {
        $audio_file = $tempFilePath;

        $storDir = Application_Model_MusicDir::getStorDir();
        $stor    = $storDir->getDirectory();
        // check if "organize" dir exists and if not create one
        if (!file_exists($stor."/organize")) {
            if (!mkdir($stor."/organize", 0777)) {
                throw new Exception("Failed to create organize directory.");
            }
        }

        if (chmod($audio_file, 0644) === false) {
            Logging::info("Warning: couldn't change permissions of $audio_file to 0644");
        }

        // Did all the checks for real, now trying to copy
        $audio_stor = Application_Common_OsPath::join($stor, "organize",
                $originalFilename);
        //if the uploaded file is not UTF-8 encoded, let's encode it. Assuming source
        //encoding is ISO-8859-1
        $audio_stor = mb_detect_encoding($audio_stor, "UTF-8") == "UTF-8" ? $audio_stor : utf8_encode($audio_stor);
        if ($copyFile) {
            Logging::info("Copying file $audio_file to $audio_stor");
            if (@copy($audio_file, $audio_stor) === false) {
                throw new Exception("Failed to copy $audio_file to $audio_stor");
            }
        } else {
            Logging::info("Moving file $audio_file to $audio_stor");

            //Ensure we have permissions to overwrite the file in stor, in case it already exists.
            if (file_exists($audio_stor)) {
                chmod($audio_stor, 0644);
            }

            // Martin K.: changed to rename: Much less load + quicker since this is
            // an atomic operation
            if (rename($audio_file, $audio_stor) === false) {
                //something went wrong likely there wasn't enough space in .
                //the audio_stor to move the file too warn the user that   .
                //the file wasn't uploaded and they should check if there  .
                //is enough disk space                                     .
                unlink($audio_file); //remove the file after failed rename

                throw new Exception("The file was not uploaded, this error can occur if the computer "
                    . "hard drive does not have enough disk space or the stor "
                    . "directory does not have correct write permissions.");
            }
        }
        return $audio_stor;
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
        $futureScheduledFilesSelectCriteria->add(CcSchedulePeer::ENDS, gmdate(DEFAULT_TIMESTAMP_FORMAT), Criteria::GREATER_THAN);
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
