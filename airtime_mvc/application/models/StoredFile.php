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
     * array of db metadata -> propel
     */
    private $_dbMD = array (
        "track_title" => "DbTrackTitle",
        "artist_name" => "DbArtistName",
        "album_title" => "DbAlbumTitle",
        "genre" => "DbGenre",
        "mood" => "DbMood",
        "track_number" => "DbTrackNumber",
        "bpm" => "DbBpm",
        "label" => "DbLabel",
        "composer" => "DbComposer",
        "encoded_by" => "DbEncodedBy",
        "conductor" => "DbConductor",
        "year" => "DbYear",
        "info_url" => "DbInfoUrl",
        "isrc_number" => "DbIsrcNumber",
        "copyright" => "DbCopyright",
        "length" => "DbLength",
        "bit_rate" => "DbBitRate",
        "sample_rate" => "DbSampleRate",
        "mime" => "DbMime",
        "md5" => "DbMd5",
        "ftype" => "DbFtype",
        "language" => "DbLanguage",
        "replay_gain" => "DbReplayGain",
        "directory" => "DbDirectory"
    );

    public function __construct()
    {

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

    /**
     * Set multiple metadata values using defined metadata constants.
     *
     * @param array $p_md
     *  example: $p_md['MDATA_KEY_URL'] = 'http://www.fake.com'
     */
    public function setMetadata($p_md=null)
    {
        Logging::log("entered setMetadata");
        if (is_null($p_md)) {
            $this->setDbColMetadata();
        } else {
            $dbMd = array();

            if (isset($p_md["MDATA_KEY_YEAR"])) {
                // We need to make sure to clean this value before inserting into database.
                // If value is outside of range [-2^31, 2^31-1] then postgresl will throw error
                // when trying to retrieve this value. We could make sure number is within these bounds,
                // but simplest is to do substring to 4 digits (both values are garbage, but at least our
                // new garbage value won't cause errors). If the value is 2012-01-01, then substring to
                // first 4 digits is an OK result.
                // CC-3771

                $year = $p_md["MDATA_KEY_YEAR"];

                if (strlen($year) > 4) {
                    $year = substr($year, 0, 4);
                }
                if (!is_numeric($year)) {
                    $year = 0;
                }
                $p_md["MDATA_KEY_YEAR"] = $year;
            }

            foreach ($p_md as $mdConst => $mdValue) {
                if (defined($mdConst)) {
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
            foreach ($p_md as $dbColumn => $mdValue) {
                //don't blank out name, defaults to original filename on first insertion to database.
                if ($dbColumn == "track_title" && (is_null($mdValue) || $mdValue == "")) {
                    continue;
                }
                if (isset($this->_dbMD[$dbColumn])) {
                    $propelColumn = $this->_dbMD[$dbColumn];
                    $method = "set$propelColumn";
                    Logging::log($method);
                    $this->_file->$method($mdValue);
                }
            }
        }

        $this->_file->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));
        $this->_file->save();
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
            $propelColumn = $this->_dbMD[$p_category];
            $method = "set$propelColumn";
            $this->_file->$method($p_value);
            $this->_file->save();
        }
    }

    /**
     * Get one metadata value.
     *
     * @param  string $p_category (MDATA_KEY_URL)
     * @return string
     */
    public function getMetadataValue($p_category)
    {
        // constant() was used because it gets quoted constant name value from
        // api_client.py. This is the wrapper funtion
        return $this->getDbColMetadataValue(constant($p_category));
    }

     /**
     * Get one metadata value.
     *
     * @param  string $p_category (url)
     * @return string
     */
    public function getDbColMetadataValue($p_category)
    {
        $propelColumn = $this->_dbMD[$p_category];
        $method = "get$propelColumn";

        return $this->_file->$method();
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
                    $propelColumn = $dbmd_copy[$value];
                    $method = "get$propelColumn";
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
        global $CC_CONFIG;
        $con = Propel::getConnection();
        $sql = "SELECT playlist_id "
            ." FROM ".$CC_CONFIG['playistTable']
            ." WHERE file_id='{$this->id}'";
        $ids = $con->query($sql)->fetchAll();
        $playlists = array();
        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $playlists[] = Application_Model_Playlist::Recall($id);
            }
        }

        return $playlists;
    }

    /**
     * Delete stored virtual file
     *
     * @param boolean $p_deleteFile
     *
     */
    public function delete($deleteFromPlaylist=false)
    {

        $filepath = $this->getFilePath();
        // Check if the file is scheduled to be played in the future
        if (Application_Model_Schedule::IsFileScheduledInTheFuture($this->getId())) {
            throw new DeleteScheduledFileException();
        }

        $music_dir = Application_Model_MusicDir::getDirByPK($this->_file->getDbDirectory());
        $type = $music_dir->getType();

        if (file_exists($filepath) && $type == "stor") {
            $data = array("filepath" => $filepath, "delete" => 1);
            Application_Model_RabbitMq::SendMessageToMediaMonitor("file_delete", $data);
        }

        if ($deleteFromPlaylist) {
            Application_Model_Playlist::DeleteFileFromAllPlaylists($this->getId());
        }
        // set file_exists falg to false
        $this->_file->setDbFileExists(false);
        $this->_file->save();
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
        // set file_exists falg to false
        $this->_file->setDbFileExists(false);
        $this->_file->save();
    }

    /**
     * Return suitable extension.
     *
     * @return string
     *         file extension without a dot
     */
    public function getFileExtension()
    {
        $mime = $this->_file->getDbMime();

        if ($mime == "audio/vorbis" || $mime == "application/ogg") {
            return "ogg";
        } elseif ($mime == "audio/mp3" || $mime == "audio/mpeg") {
            return "mp3";
        }
    }

    /**
     * Get real filename of raw media data
     *
     * @return string
     */
    public function getFilePath()
    {
        $music_dir = Application_Model_MusicDir::getDirByPK($this->_file->getDbDirectory());
        $directory = $music_dir->getDirectory();

        $filepath = $this->_file->getDbFilepath();

        return $directory.$filepath;
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
        $this->_file->save();
    }

    /**
     * Get the URL to access this file using the server name/address that
     * this PHP script was invoked through.
     */
    public function getFileUrl()
    {
        $serverName = $_SERVER['SERVER_NAME'];
        $serverPort = $_SERVER['SERVER_PORT'];

        return $this->constructGetFileUrl($serverName, $serverPort);
    }

    /**
     * Get the URL to access this file using the server name/address that
     * is specified in the airtime.conf config file. If either of these is
     * not specified, then use values provided by the $_SERVER global variable.
     */
    public function getFileUrlUsingConfigAddress()
    {
        global $CC_CONFIG;

        if (isset($CC_CONFIG['baseUrl'])) {
            $serverName = $CC_CONFIG['baseUrl'];
        } else {
            $serverName = $_SERVER['SERVER_NAME'];
        }

        if (isset($CC_CONFIG['basePort'])) {
            $serverPort = $CC_CONFIG['basePort'];
        } else {
            $serverPort = $_SERVER['SERVER_PORT'];
        }

        return $this->constructGetFileUrl($serverName, $serverPort);
    }

    private function constructGetFileUrl($p_serverName, $p_serverPort)
    {
        return "http://$p_serverName:$p_serverPort/api/get-media/file/".$this->getId().".".$this->getFileExtension();
    }

    /**
     * Sometimes we want a relative URL and not a full URL. See bug
     * http://dev.sourcefabric.org/browse/CC-2403
     */
    public function getRelativeFileUrl($baseUrl)
    {
        Logging::log("getting media!");

        return $baseUrl."/api/get-media/file/".$this->getId().".".$this->getFileExtension();
    }

    public static function Insert($md=null)
    {
        $file = new CcFiles();
        $file->setDbUtime(new DateTime("now", new DateTimeZone("UTC")));
        $file->setDbMtime(new DateTime("now", new DateTimeZone("UTC")));

        $storedFile = new Application_Model_StoredFile();
        $storedFile->_file = $file;

        if (isset($md['MDATA_KEY_FILEPATH'])) {
            // removed "//" in the path. Always use '/' for path separator
            $filepath = str_replace("//", "/", $md['MDATA_KEY_FILEPATH']);
            $res = $storedFile->setFilePath($filepath);
            if ($res === -1) {
                return null;
            }
        } else {
            return null;
        }

        if (isset($md)) {
            $storedFile->setMetadata($md);
        }

       return $storedFile;
    }

    /**
     * Fetch instance of StoreFile object.<br>
     * Should be supplied with only ONE parameter, all the rest should
     * be NULL.
     *
     * @param int $p_id
     *         local id
     * @param string $p_gunid - TODO: Remove this!
     *         global unique id of file
     * @param string $p_md5sum
     *    MD5 sum of the file
     * @param boolean $exist
     *    When this is true, it check against only files with file_exist is 'true'
     * @return Application_Model_StoredFile|NULL
     *    Return NULL if the object doesnt exist in the DB.
     */
    public static function Recall($p_id=null, $p_gunid=null, $p_md5sum=null, $p_filepath=null, $exist=false)
    {
        if (isset($p_id)) {
            $file = CcFilesQuery::create()->findPK(intval($p_id));
        } elseif (isset($p_md5sum)) {
            if ($exist) {
                $file = CcFilesQuery::create()
                            ->filterByDbMd5($p_md5sum)
                            ->filterByDbFileExists(true)
                            ->findOne();
            } else {
                $file = CcFilesQuery::create()
                            ->filterByDbMd5($p_md5sum)
                            ->findOne();
            }
        } elseif (isset($p_filepath)) {
            $path_info = Application_Model_MusicDir::splitFilePath($p_filepath);

            if (is_null($path_info)) {
                return null;
            }
            $music_dir = Application_Model_MusicDir::getDirByPath($path_info[0]);

            $file = CcFilesQuery::create()
                            ->filterByDbDirectory($music_dir->getId())
                            ->filterByDbFilepath($path_info[1])
                            ->findOne();
        } else {
            return null;
        }

        if (isset($file)) {
            $storedFile = new Application_Model_StoredFile();
            $storedFile->_file = $file;

            return $storedFile;
        } else {
            return null;
        }
    }

    public function getName()
    {
        $info = pathinfo($this->getFilePath());

        return $info['filename'];
    }

    /**
     * Fetch the Application_Model_StoredFile by looking up the MD5 value.
     *
     * @param  string                            $p_md5sum
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByMd5($p_md5sum, $exist=false)
    {
        return Application_Model_StoredFile::Recall(null, null, $p_md5sum, null, $exist);
    }

    /**
     * Fetch the Application_Model_StoredFile by looking up its filepath.
     *
     * @param  string                            $p_filepath path of file stored in Airtime.
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByFilepath($p_filepath)
    {
        return Application_Model_StoredFile::Recall(null, null, null, $p_filepath);
    }

    public static function RecallByPartialFilepath($partial_path)
    {
        $path_info = Application_Model_MusicDir::splitFilePath($partial_path);

        if (is_null($path_info)) {
            return null;
        }
        $music_dir = Application_Model_MusicDir::getDirByPath($path_info[0]);

        $files = CcFilesQuery::create()
                        ->filterByDbDirectory($music_dir->getId())
                        ->filterByDbFilepath("$path_info[1]%")
                        ->find();
        $res = array();
        foreach ($files as $file) {
            $storedFile = new Application_Model_StoredFile();
            $storedFile->_file = $file;
            $res[] = $storedFile;
        }

        return $res;
    }

    public static function searchLibraryFiles($datatables)
    {
        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);

        $displayColumns = array("id", "track_title", "artist_name", "album_title", "genre", "length",
            "year", "utime", "mtime", "ftype", "track_number", "mood", "bpm", "composer", "info_url",
            "bit_rate", "sample_rate", "isrc_number", "encoded_by", "label", "copyright", "mime",
            "language", "filepath"
        );

        $plSelect = array();
        $blSelect = array();
        $fileSelect = array();
        $streamSelect = array();
        foreach ($displayColumns as $key) {

            if ($key === "id") {
                $plSelect[] = "PL.id AS ".$key;
                $blSelect[] = "BL.id AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "ws.id AS ".$key;
            } elseif ($key === "track_title") {
                $plSelect[] = "name AS ".$key;
                $blSelect[] = "name AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "name AS ".$key;
            } elseif ($key === "ftype") {
                $plSelect[] = "'playlist'::varchar AS ".$key;
                $blSelect[] = "'block'::varchar AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "'stream'::varchar AS ".$key;
            } elseif ($key === "artist_name") {
                $plSelect[] = "login AS ".$key;
                $blSelect[] = "login AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "login AS ".$key;
            }
            //same columns in each table.
            else if (in_array($key, array("length", "utime", "mtime"))) {
                $plSelect[] = $key;
                $blSelect[] = $key;
                $fileSelect[] = $key;
                $streamSelect[] = $key;
            } elseif ($key === "year") {
                $plSelect[] = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
                $blSelect[] = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
                $fileSelect[] = "year AS ".$key;
                $streamSelect[] = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
            }
            //need to cast certain data as ints for the union to search on.
            else if (in_array($key, array("track_number", "bit_rate", "sample_rate"))) {
                $plSelect[] = "NULL::int AS ".$key;
                $blSelect[] = "NULL::int AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "NULL::int AS ".$key;
            } else if ($key === "filepath") {
                $plSelect[] = "NULL::VARCHAR AS ".$key;
                $blSelect[] = "NULL::VARCHAR AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "url AS ".$key;
            } else {
                $plSelect[] = "NULL::text AS ".$key;
                $blSelect[] = "NULL::text AS ".$key;
                $fileSelect[] = $key;
                $streamSelect[] = "NULL::text AS ".$key;
            }
        }

        $plSelect = "SELECT ". join(",", $plSelect);
        $blSelect = "SELECT ". join(",", $blSelect);
        $fileSelect = "SELECT ". join(",", $fileSelect);
        $streamSelect = "SELECT ". join(",", $streamSelect);

        $type = intval($datatables["type"]);

        $plTable = "({$plSelect} FROM cc_playlist AS PL LEFT JOIN cc_subjs AS sub ON (sub.id = PL.creator_id))";
        $blTable = "({$blSelect} FROM cc_block AS BL LEFT JOIN cc_subjs AS sub ON (sub.id = BL.creator_id))";
        $fileTable = "({$fileSelect} FROM cc_files AS FILES WHERE file_exists = 'TRUE')";
        $streamTable = "({$streamSelect} FROM cc_webstream AS ws LEFT JOIN cc_subjs AS sub ON (sub.id = ws.creator_id))";
        $unionTable = "({$plTable} UNION {$blTable} UNION {$fileTable} UNION {$streamTable}) AS RESULTS";

        //choose which table we need to select data from.
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

        $results = Application_Model_Datatables::findEntries($con, $displayColumns, $fromTable, $datatables);

        //Used by the audio preview functionality in the library.
        foreach ($results['aaData'] as &$row) {
            $row['id'] = intval($row['id']);

            $formatter = new LengthFormatter($row['length']);
            $row['length'] = $formatter->format();

            if ($row['ftype'] === "audioclip") {
                $formatter = new SamplerateFormatter($row['sample_rate']);
                $row['sample_rate'] = $formatter->format();

                $formatter = new BitrateFormatter($row['bit_rate']);
                $row['bit_rate'] = $formatter->format();
            }
  
            //convert mtime and utime to localtime
            $row['mtime'] = new DateTime($row['mtime'], new DateTimeZone('UTC'));
            $row['mtime']->setTimeZone(new DateTimeZone(date_default_timezone_get()));
            $row['mtime'] = $row['mtime']->format('Y-m-d H:i:s');
            $row['utime'] = new DateTime($row['utime'], new DateTimeZone('UTC'));
            $row['utime']->setTimeZone(new DateTimeZone(date_default_timezone_get()));
            $row['utime'] = $row['utime']->format('Y-m-d H:i:s');
            
            // add checkbox row
            $row['checkbox'] = "<input type='checkbox' name='cb_".$row['id']."'>";

            $type = substr($row['ftype'], 0, 2);

            $row['tr_id'] = "{$type}_{$row['id']}";

            //TODO url like this to work on both playlist/showbuilder screens.
            //datatable stuff really needs to be pulled out and generalized within the project
            //access to zend view methods to access url helpers is needed.

            if ($type == "au") {
                $row['audioFile'] = $row['id'].".".pathinfo($row['filepath'], PATHINFO_EXTENSION);
                $row['image'] = '<img title="Track preview" src="/css/images/icon_audioclip.png">';
            } else if ($type == "pl") {
                $row['image'] = '<img title="Playlist preview" src="/css/images/icon_playlist.png">';
            } else if ($type == "st") {
                $row['audioFile'] = $row['id'];
                $row['image'] = '<img title="Webstream preview" src="/css/images/record_icon.png">';
            } else if ($type == "bl") {
                $row['image'] = '<img title="Smart Playlist" src="/css/images/delete.png">';
            }
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
        $maxFileAge = 60 * 60; // Temp file age in seconds

        // 5 minutes execution time
        @set_time_limit(5 * 60);
        // usleep(5000);

        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        Logging::log(__FILE__.":uploadFile(): filename=$fileName to $p_targetDir");
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
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');

        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        // create temp file name (CC-3086)
        // we are not using mktemp command anymore.
        // plupload support unique_name feature.
        $tempFilePath= $p_targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                    fclose($out);
                    unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen($tempFilePath, $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
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
        Logging::log('copyFileToStor: moving file '.$audio_file);
        $md5 = md5_file($audio_file);
        $duplicate = Application_Model_StoredFile::RecallByMd5($md5, true);

        $result = null;
        if ($duplicate) {
            if (file_exists($duplicate->getFilePath())) {
                $duplicateName = $duplicate->getMetadataValue('MDATA_KEY_TITLE');
                $result = array( "code" => 106, "message" => "An identical audioclip named '$duplicateName' already exists on the server.");
            }
        }

        if (!isset($result)) {//The file has no duplicate, so proceed to copy.
            $storDir = Application_Model_MusicDir::getStorDir();
            $stor = $storDir->getDirectory();
            // check if "organize" dir exists and if not create one
            if (!file_exists($stor."/organize")) {
                if (!mkdir($stor."/organize", 0777)) {
                    $result = array("code" => 109, "message" => "Failed to create 'organize' directory.");

                    return $result;
                }
            }

            if (chmod($audio_file, 0644) === false) {
                Logging::log("Warning: couldn't change permissions of $audio_file to 0644");
            }

            //check to see if there is enough space in $stor to continue.
            if (self::isEnoughDiskSpaceToCopy($stor, $audio_file)) {
                $audio_stor = Application_Common_OsPath::join($stor, "organize", $fileName);

                if (self::liquidsoapFilePlayabilityTest($audio_file)) {

                    Logging::log("copyFileToStor: moving file $audio_file to $audio_stor");

                    //Martin K.: changed to rename: Much less load + quicker since this is an atomic operation
                    if (@rename($audio_file, $audio_stor) === false) {
                        #something went wrong likely there wasn't enough space in the audio_stor to move the file too.
                        #warn the user that the file wasn't uploaded and they should check if there is enough disk space.
                        unlink($audio_file);//remove the file after failed rename
                        $result = array("code" => 108, "message" => "The file was not uploaded, this error can occur if the computer hard drive does not have enough disk space.");
                    }
                } else {
                    $result = array("code" => 110, "message" => "This file appears to be corrupted and will not be added to media library.");
                }

            } else {
                $result = array("code" => 107, "message" => "The file was not uploaded, there is ".$freeSpace."MB of disk space left and the file you are uploading has a size of  ".$fileSize."MB.");
            }
        }

        return $result;
    }

    /*
     * Pass the file through Liquidsoap and test if it is readable. Return True if readable, and False otherwise.
     */
    public static function liquidsoapFilePlayabilityTest($audio_file)
    {
        $LIQUIDSOAP_ERRORS = array('TagLib: MPEG::Properties::read() -- Could not find a valid last MPEG frame in the stream.');

        // Ask Liquidsoap if file is playable
        $command = sprintf("/usr/bin/airtime-liquidsoap -c 'output.dummy(audio_to_stereo(single(\"%s\")))' 2>&1", $audio_file);

        exec($command, $output, $rv);

        $isError = count($output) > 0 && in_array($output[0], $LIQUIDSOAP_ERRORS);

        return ($rv == 0 && !$isError);
    }

    public static function getFileCount()
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();

        $sql = "SELECT count(*) as cnt FROM ".$CC_CONFIG["filesTable"]." WHERE file_exists";

        return $con->query($sql)->fetchColumn(0);
    }

    /**
     *
     * Enter description here ...
     * @param $dir_id - if this is not provided, it returns all files with full path constructed.
     * @param $propelObj - if this is true, it returns array of proepl obj
     */
    public static function listAllFiles($dir_id=null, $propelObj=false)
    {
        $con = Propel::getConnection();

        if ($propelObj) {
            $sql = "SELECT m.directory || f.filepath as fp"
                    ." FROM CC_MUSIC_DIRS m"
                    ." LEFT JOIN CC_FILES f"
                    ." ON m.id = f.directory WHERE m.id = $dir_id and f.file_exists = 'TRUE'";
        } else {
            $sql = "SELECT filepath as fp"
                    ." FROM CC_FILES"
                    ." WHERE directory = $dir_id and file_exists = 'TRUE'";
        }
        $rows = $con->query($sql)->fetchAll();

        $results = array();
        foreach ($rows as $row) {
            if ($propelObj) {
                $results[] = Application_Model_StoredFile::RecallByFilepath($row["fp"]);
            } else {
                $results[] = $row["fp"];
            }
        }

        return $results;
    }

    //TODO: MERGE THIS FUNCTION AND "listAllFiles" -MK
    public static function listAllFiles2($dir_id=null, $limit=null)
    {
        $con = Propel::getConnection();

        $sql = "SELECT id, filepath as fp"
                ." FROM CC_FILES"
                ." WHERE directory = $dir_id"
                ." AND file_exists = 'TRUE'"
                ." AND replay_gain is NULL";
        if (!is_null($limit) && is_int($limit)) {
            $sql .= " LIMIT $limit";
        }
                
        $rows = $con->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        return $rows;
    }

    /* Gets number of tracks uploaded to
     * Soundcloud in the last 24 hours
     */
    public static function getSoundCloudUploads()
    {
        try {
            $con = Propel::getConnection();

            $sql = "SELECT soundcloud_id as id, soundcloud_upload_time"
                    ." FROM CC_FILES"
                    ." WHERE (id != -2 and id != -3) and"
                    ." (soundcloud_upload_time >= (now() - (INTERVAL '1 day')))";

            $rows = $con->query($sql)->fetchAll();

            return count($rows);
        } catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::log("Could not connect to database.");
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
    public function setSoundCloudUploadTime($time)
    {
        $this->_file->setDbSoundCloundUploadTime($time)
            ->save();
    }

    public function getFileExistsFlag()
    {
        return $this->_file->getDbFileExists();
    }

    public function uploadToSoundCloud()
    {
        global $CC_CONFIG;

        $file = $this->_file;
        if (is_null($file)) {
            return "File does not exist";
        }
        if (Application_Model_Preference::GetUploadToSoundcloudOption()) {
            for ($i=0; $i<$CC_CONFIG['soundcloud-connection-retries']; $i++) {
                $description = $file->getDbTrackTitle();
                $tag = array();
                $genre = $file->getDbGenre();
                $release = $file->getDbYear();
                try {
                    $soundcloud = new Application_Model_Soundcloud();
                    $soundcloud_res = $soundcloud->uploadTrack($this->getFilePath(), $this->getName(), $description, $tag, $release, $genre);
                    $this->setSoundCloudFileId($soundcloud_res['id']);
                    $this->setSoundCloudLinkToFile($soundcloud_res['permalink_url']);
                    $this->setSoundCloudUploadTime(new DateTime("now"), new DateTimeZone("UTC"));
                    break;
                } catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
                    $code = $e->getHttpCode();
                    $msg = $e->getHttpBody();
                    $temp = explode('"error":',$msg);
                    $msg = trim($temp[1], '"}');
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
}

class DeleteScheduledFileException extends Exception {}
class FileDoesNotExistException extends Exception {}
