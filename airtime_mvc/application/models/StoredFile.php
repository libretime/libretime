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
class Application_Model_StoredFile {

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
        "language" => "DbLanguage"
    );

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->_file->getDbId();
    }

    public function getGunId() {
        return $this->_file->getDbGunid();
    }

    public function getFormat()
    {
        return $this->_file->getDbFtype();
    }

    public function getPropelOrm(){
        return $this->_file;
    }

    public function setFormat($p_format)
    {
        $this->_file->setDbFtype($p_format);
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
        }
        else {
            $dbMd = array();
            foreach ($p_md as $mdConst => $mdValue) {
                $dbMd[constant($mdConst)] = $mdValue;
            }
            $this->setDbColMetadata($dbMd);
        }

        $this->_file->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));
        $this->_file->save();
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
        }
        else {
            foreach ($p_md as $dbColumn => $mdValue) {
                //don't blank out name, defaults to original filename on first insertion to database.
                if($dbColumn == "track_title" && (is_null($mdValue) || $mdValue == "")) {
                    continue;
                }
                if (isset($this->_dbMD[$dbColumn])) {
                    $propelColumn = $this->_dbMD[$dbColumn];
                    $method = "set$propelColumn";
                    $this->_file->$method($mdValue);
                }
            }
        }

        $this->_file->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));
        $this->_file->save();
    }

    /**
     * Set metadata element value
     *
     * @param string $category
     * 		Metadata element by metadata constant
     * @param string $value
     * 		value to store, if NULL then delete record
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
     * 		Metadata element by db column
     * @param string $value
     * 		value to store, if NULL then delete record
     */
    public function setDbColMetadataValue($p_category, $p_value)
    {
        //don't blank out name, defaults to original filename on first insertion to database.
        if($p_category == "track_title" && (is_null($p_value) || $p_value == "")) {
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
     * @param string $p_category (MDATA_KEY_URL)
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
     * @param string $p_category (url)
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

        foreach ($c['user'] as $constant => $value) {
            if (preg_match('/^MDATA_KEY/', $constant)) {
                if (isset($this->_dbMD[$value])) {
                    $md[$constant] = $this->getDbColMetadataValue($value);
                }
            }
        }

        return $md;
    }

    /**
     * Set state of virtual file
     *
     * @param string $p_state
     * 		'empty'|'incomplete'|'ready'|'edited'
     * @param int $p_editedby
     * 		 user id | 'NULL' for clear editedBy field
     * @return TRUE|PEAR_Error
     */
    public function setState($p_state, $p_editedby=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedState = pg_escape_string($p_state);
        $eb = (!is_null($p_editedby) ? ", editedBy=$p_editedby" : '');
        $sql = "UPDATE ".$CC_CONFIG['filesTable']
        ." SET state='$escapedState'$eb, mtime=now()"
        ." WHERE gunid='{$this->gunid}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->state = $p_state;
        $this->editedby = $p_editedby;
        return TRUE;
    }

    /**
     * Returns an array of playlist objects that this file is a part of.
     * @return array
     */
    public function getPlaylists() {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT playlist_id "
        ." FROM ".$CC_CONFIG['playistTable']
        ." WHERE file_id='{$this->id}'";
        $ids = $CC_DBC->getAll($sql);
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

        if (file_exists($filepath)) {

            $data = array("filepath" => $filepath, "delete" => 1);
            Application_Model_RabbitMq::SendMessageToMediaMonitor("file_delete", $data);
        }

        if ($deleteFromPlaylist){
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
     * 		file extension without a dot
     */
    public function getFileExtension()
    {
        $mime = $this->_file->getDbMime();

        if ($mime == "audio/vorbis" || $mime == "application/ogg") {
            return "ogg";
        }
        else if ($mime == "audio/mp3" || $mime == "audio/mpeg") {
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
    public function getFileUrlUsingConfigAddress(){
        global $CC_CONFIG;

        if (isset($CC_CONFIG['baseUrl'])){
            $serverName = $CC_CONFIG['baseUrl'];
        } else {
            $serverName = $_SERVER['SERVER_NAME'];
        }

        if (isset($CC_CONFIG['basePort'])){
            $serverPort = $CC_CONFIG['basePort'];
        } else {
            $serverPort = $_SERVER['SERVER_PORT'];
        }

        return $this->constructGetFileUrl($serverName, $serverPort);
    }

    private function constructGetFileUrl($p_serverName, $p_serverPort){
Logging::log("getting media! - 2");        
        return "http://$p_serverName:$p_serverPort/api/get-media/file/".$this->getGunId().".".$this->getFileExtension();
    }

    /**
     * Sometimes we want a relative URL and not a full URL. See bug
     * http://dev.sourcefabric.org/browse/CC-2403
     */
    public function getRelativeFileUrl($baseUrl)
    {
        Logging::log("getting media!");
        return $baseUrl."/api/get-media/file/".$this->getGunId().".".$this->getFileExtension();
    }

    public static function Insert($md=null)
    {
        $file = new CcFiles();
        $file->setDbGunid(md5(uniqid("", true)));
        $file->setDbUtime(new DateTime("now"), new DateTimeZone("UTC"));
        $file->setDbMtime(new DateTime("now"), new DateTimeZone("UTC"));

        $storedFile = new Application_Model_StoredFile();
        $storedFile->_file = $file;

        if(isset($md['MDATA_KEY_FILEPATH'])) {
            // removed "//" in the path. Always use '/' for path separator
            $filepath = str_replace("//", "/", $md['MDATA_KEY_FILEPATH']);
            $res = $storedFile->setFilePath($filepath);
            if ($res === -1) {
                return null;
            }
        }
        else {
            return null;
        }

        if(isset($md)) {
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
     * 		local id
     * @param string $p_gunid
     * 		global unique id of file
     * @param string $p_md5sum
     *    MD5 sum of the file
     * @return Application_Model_StoredFile|NULL
     *    Return NULL if the object doesnt exist in the DB.
     */
    public static function Recall($p_id=null, $p_gunid=null, $p_md5sum=null, $p_filepath=null)
    {
        if (isset($p_id)) {
            $file = CcFilesQuery::create()->findPK(intval($p_id));
        }
        else if (isset($p_gunid)) {
            $file = CcFilesQuery::create()
                            ->filterByDbGunid($p_gunid)
                            ->findOne();
        }
        else if (isset($p_md5sum)) {
            $file = CcFilesQuery::create()
                            ->filterByDbMd5($p_md5sum)
                            ->findOne();
        }
        else if (isset($p_filepath)) {
            $path_info = Application_Model_MusicDir::splitFilePath($p_filepath);

            if (is_null($path_info)) {
                return null;
            }
            $music_dir = Application_Model_MusicDir::getDirByPath($path_info[0]);

            $file = CcFilesQuery::create()
                            ->filterByDbDirectory($music_dir->getId())
                            ->filterByDbFilepath($path_info[1])
                            ->findOne();
        }
        else {
            return null;
        }

        if (isset($file)) {
            $storedFile = new Application_Model_StoredFile();
            $storedFile->_file = $file;

            return $storedFile;
        }
        else {
            return null;
        }
    }

    public function getName(){
        $info = pathinfo($this->getFilePath());
        return $info['filename'];
    }

    /**
     * Create instance of StoreFile object and recall existing file
     * by gunid.
     *
     * @param string $p_gunid
     * 		global unique id of file
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByGunid($p_gunid)
    {
        return Application_Model_StoredFile::Recall(null, $p_gunid);
    }


    /**
     * Fetch the Application_Model_StoredFile by looking up the MD5 value.
     *
     * @param string $p_md5sum
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByMd5($p_md5sum)
    {
        return Application_Model_StoredFile::Recall(null, null, $p_md5sum);
    }

    /**
     * Fetch the Application_Model_StoredFile by looking up its filepath.
     *
     * @param string $p_filepath path of file stored in Airtime.
     * @return Application_Model_StoredFile|NULL
     */
    public static function RecallByFilepath($p_filepath)
    {
        return Application_Model_StoredFile::Recall(null, null, null, $p_filepath);
    }

    public static function RecallByPartialFilepath($partial_path){
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
        foreach ($files as $file){
            $storedFile = new Application_Model_StoredFile();
            $storedFile->_file = $file;
            $res[] = $storedFile;
        }
        return $res;
    }

    public static function searchFilesForPlaylistBuilder($datatables) {

        $displayColumns = array("id", "track_title", "artist_name", "album_title", "genre", "length",
            "year", "utime", "mtime", "ftype", "track_number", "mood", "bpm", "composer", "info_url",
            "bit_rate", "sample_rate", "isrc_number", "encoded_by", "label", "copyright", "mime", "language"
        );

        $plSelect = array();
        $fileSelect = array();
        foreach ($displayColumns as $key) {

            if ($key === "id") {
                $plSelect[] = "PL.id AS ".$key;
                $fileSelect[] = $key;
            }
            else if ($key === "track_title") {
                $plSelect[] = "name AS ".$key;
                $fileSelect[] = $key;
            }
            else if ($key === "ftype") {
                $plSelect[] = "'playlist'::varchar AS ".$key;
                $fileSelect[] = $key;
            }
            else if ($key === "artist_name") {
                $plSelect[] = "login AS ".$key;
                $fileSelect[] = $key;
            }
            //same columns in each table.
            else if(in_array($key, array("length", "utime", "mtime"))) {
                $plSelect[] = $key;
                $fileSelect[] = $key;
            }
            else if ($key === "year") {

                $plSelect[] = "EXTRACT(YEAR FROM utime)::varchar AS ".$key;
                $fileSelect[] = "EXTRACT(YEAR FROM to_date(year, 'YYYY-MM-DD'))::varchar AS ".$key;
            }
            //need to cast certain data as ints for the union to search on.
            else if (in_array($key, array("track_number", "bit_rate", "sample_rate"))){
                $plSelect[] = "NULL::int AS ".$key;
                $fileSelect[] = $key;
            }
            else {
                $plSelect[] = "NULL::text AS ".$key;
                $fileSelect[] = $key;
            }
        }

        $plSelect = "SELECT ". join(",", $plSelect);
        $fileSelect = "SELECT ". join(",", $fileSelect);

        $type = intval($datatables["type"]);

        $plTable = "({$plSelect} FROM cc_playlist AS PL LEFT JOIN cc_subjs AS sub ON (sub.id = PL.creator_id))";
        $fileTable = "({$fileSelect} FROM cc_files AS FILES WHERE file_exists = 'TRUE')";
        $unionTable = "({$plTable} UNION {$fileTable} ) AS RESULTS";

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
            default:
                $fromTable = $unionTable;
        }

        $results = Application_Model_StoredFile::searchFiles($displayColumns, $fromTable, $datatables);

        //Used by the audio preview functionality in the library.
        $audioResults =  Application_Model_StoredFile::getAllAudioFilePaths();
    
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

            // add checkbox row
            $row['checkbox'] = "<input type='checkbox' name='cb_".$row['id']."'>";

            $type = substr($row['ftype'], 0, 2);

            $row['tr_id'] = "{$type}_{$row['id']}";

            //TODO url like this to work on both playlist/showbuilder screens.
            //datatable stuff really needs to be pulled out and generalized within the project
            //access to zend view methods to access url helpers is needed.

            if($type == "au" && isset( $audioResults )) {
                $row['audioFile'] = $audioResults[$row['id']-1]['gunid'].".".pathinfo($audioResults[$row['id']-1]['filepath'], PATHINFO_EXTENSION);
                $row['image'] = '<span class="ui-icon ui-icon-play"></span>';

            }
            else {
                $row['image'] = '<img src="/css/images/icon_playlist.png">';
            }
        }

        return $results;
    }

    public static function getAllAudioFilePaths(){
        try {
            $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
            $r = $con->query("SELECT id, gunid, filepath FROM cc_files");
            $r->setFetchMode(PDO::FETCH_ASSOC);
            $results = $r->fetchAll();
            
            return $results;
        }catch (Exception $e) {
            Logging::log($e->getMessage());
        }
    }
    
    public static function searchFiles($displayColumns, $fromTable, $data)
    {
        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        $where = array();
    
        if ($data["sSearch"] !== "") {
            $searchTerms = explode(" ", $data["sSearch"]);
        }
    
        $selectorCount = "SELECT COUNT(*) ";
        $selectorRows = "SELECT ".join(",", $displayColumns)." ";
    
        $sql = $selectorCount." FROM ".$fromTable;
        $sqlTotalRows = $sql;
    
        if (isset($searchTerms)) {
            $searchCols = array();
            for ($i = 0; $i < $data["iColumns"]; $i++) {
                if ($data["bSearchable_".$i] == "true") {
                    $searchCols[] = $data["mDataProp_{$i}"];
                }
            }
    
            $outerCond = array();
    
            foreach ($searchTerms as $term) {
                $innerCond = array();
    
                foreach ($searchCols as $col) {
                    $escapedTerm = pg_escape_string($term);
                    $innerCond[] = "{$col}::text ILIKE '%{$escapedTerm}%'";
                }
                $outerCond[] = "(".join(" OR ", $innerCond).")";
            }
            $where[] = "(".join(" AND ", $outerCond).")";
        }
        // End Where clause
    
        // Order By clause
        $orderby = array();
        for ($i = 0; $i < $data["iSortingCols"]; $i++){
            $num = $data["iSortCol_".$i];
            $orderby[] = $data["mDataProp_{$num}"]." ".$data["sSortDir_".$i];
        }
        $orderby[] = "id";
        $orderby = join("," , $orderby);
        // End Order By clause
    
        if (count($where) > 0) {
            $where = join(" AND ", $where);
            $sql = $selectorCount." FROM ".$fromTable." WHERE ".$where;
            $sqlTotalDisplayRows = $sql;
    
            $sql = $selectorRows." FROM ".$fromTable." WHERE ".$where." ORDER BY ".$orderby." OFFSET ".$data["iDisplayStart"]." LIMIT ".$data["iDisplayLength"];
        }
        else {
            $sql = $selectorRows." FROM ".$fromTable." ORDER BY ".$orderby." OFFSET ".$data["iDisplayStart"]." LIMIT ".$data["iDisplayLength"];
        }
    
        try {
            $r = $con->query($sqlTotalRows);
            $totalRows = $r->fetchColumn(0);
    
            if (isset($sqlTotalDisplayRows)) {
                $r = $con->query($sqlTotalDisplayRows);
                $totalDisplayRows = $r->fetchColumn(0);
            }
            else {
              $totalDisplayRows = $totalRows;
            }
    
            $r = $con->query($sql);
            $r->setFetchMode(PDO::FETCH_ASSOC);
            $results = $r->fetchAll();
        }
        catch (Exception $e) {
            Logging::log($e->getMessage());
        }
    
        //display sql executed in airtime log for testing
        Logging::log($sql);
    
        return array("sEcho" => intval($data["sEcho"]), "iTotalDisplayRecords" => $totalDisplayRows, "iTotalRecords" => $totalRows, "aaData" => $results);
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
    public static function checkForEnoughDiskSpaceToCopy($destination_folder, $audio_file){
        //check to see if we have enough space in the /organize directory to copy the file
        $freeSpace = disk_free_space($destination_folder);
        $fileSize = filesize($audio_file);

        if ( $freeSpace < $fileSize){
            $freeSpace = ceil($freeSpace/1024/1024);
            $fileSize = ceil($fileSize/1024/1024);
            $result = array("code" => 107, "message" => "The file was not uploaded, there is ".$freeSpace."MB of disk space left and the file you are uploading has a size of  ".$fileSize."MB.");

        }
        return $result;
    }

    public static function copyFileToStor($p_targetDir, $fileName, $tempname){
        $audio_file = $p_targetDir . DIRECTORY_SEPARATOR . $tempname;
        Logging::log('copyFileToStor: moving file '.$audio_file);
        $md5 = md5_file($audio_file);
        $duplicate = Application_Model_StoredFile::RecallByMd5($md5);
        if ($duplicate) {
            if (PEAR::isError($duplicate)) {
                $result = array("code" => 105, "message" => $duplicate->getMessage());
            }
            if (file_exists($duplicate->getFilePath())) {
                $duplicateName = $duplicate->getMetadataValue('MDATA_KEY_TITLE');
                $result = array( "code" => 106, "message" => "An identical audioclip named '$duplicateName' already exists on the server.");
            }
        }

        if (!isset($result)){//The file has no duplicate, so procceed to copy.
            $storDir = Application_Model_MusicDir::getStorDir();
            $stor = $storDir->getDirectory();

            //check to see if there is enough space in $stor to continue.
            $result = Application_Model_StoredFile::checkForEnoughDiskSpaceToCopy($stor, $audio_file);
            if (!isset($result)){//if result not set then there's enough disk space to copy the file over
                $stor .= "/organize";
                $audio_stor = $stor . DIRECTORY_SEPARATOR . $fileName;

                Logging::log("copyFileToStor: moving file $audio_file to $audio_stor");
                //Martin K.: changed to rename: Much less load + quicker since this is an atomic operation
                $r = @rename($audio_file, $audio_stor);
    
                if ($r === false) {
                   #something went wrong likely there wasn't enough space in the audio_stor to move the file too.
                   #warn the user that the file wasn't uploaded and they should check if there is enough disk space.
                    unlink($audio_file);//remove the file from the organize after failed rename
                    $result = array("code" => 108, "message" => "The file was not uploaded, this error will occur if the computer hard drive does not have enough disk space.");
                }
        }
    }
    return $result;
    }


    public static function getFileCount()
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*) as cnt FROM ".$CC_CONFIG["filesTable"];
        return $CC_DBC->GetOne($sql);
    }

    /**
     *
     * Enter description here ...
     * @param $dir_id - if this is not provided, it returns all files with full path constructed.
     * @param $propelObj - if this is true, it returns array of proepl obj
     */
    public static function listAllFiles($dir_id=null, $propelObj=false){
        global $CC_DBC;

        if($propelObj){
            $sql = "SELECT m.directory || f.filepath as fp"
                    ." FROM CC_MUSIC_DIRS m"
                    ." LEFT JOIN CC_FILES f"
                    ." ON m.id = f.directory WHERE m.id = $dir_id and f.file_exists = 'TRUE'";
        }else{
            $sql = "SELECT filepath as fp"
                    ." FROM CC_FILES"
                    ." WHERE directory = $dir_id and file_exists = 'TRUE'";
        }
        $rows = $CC_DBC->getAll($sql);

        $results = array();
        foreach ($rows as $row){
            if($propelObj){
                $results[] = Application_Model_StoredFile::RecallByFilepath($row["fp"]);
            }else{
                $results[] = $row["fp"];
            }
        }

        return $results;
    }

    public function setSoundCloudLinkToFile($link_to_file)
    {
        $this->_file->setDbSoundCloudLinkToFile($link_to_file)
        ->save();
    }

    public function getSoundCloudLinkToFile(){
        return $this->_file->getDbSoundCloudLinkToFile();
    }

    public function setSoundCloudFileId($p_soundcloud_id)
    {
        $this->_file->setDbSoundCloudId($p_soundcloud_id)
            ->save();
    }

    public function getSoundCloudId(){
        return $this->_file->getDbSoundCloudId();
    }

    public function setSoundCloudErrorCode($code){
        $this->_file->setDbSoundCloudErrorCode($code)
            ->save();
    }

    public function getSoundCloudErrorCode(){
        return $this->_file->getDbSoundCloudErrorCode();
    }

    public function setSoundCloudErrorMsg($msg){
        $this->_file->setDbSoundCloudErrorMsg($msg)
            ->save();
    }

    public function getSoundCloudErrorMsg(){
        return $this->_file->getDbSoundCloudErrorMsg();
    }

    public function setFileExistsFlag($flag){
        $this->_file->setDbFileExists($flag)
            ->save();
    }

    public function getFileExistsFlag(){
        return $this->_file->getDbFileExists();
    }

    public function uploadToSoundCloud()
    {
        global $CC_CONFIG;

        $file = $this->_file;
        if(is_null($file)) {
            return "File does not exist";
        }
        if(Application_Model_Preference::GetUploadToSoundcloudOption())
        {
            for($i=0; $i<$CC_CONFIG['soundcloud-connection-retries']; $i++) {
                $description = $file->getDbTrackTitle();
                $tag = array();
                $genre = $file->getDbGenre();
                $release = $file->getDbYear();
                try {
                    $soundcloud = new Application_Model_Soundcloud();
                    $soundcloud_res = $soundcloud->uploadTrack($this->getFilePath(), $this->getName(), $description, $tag, $release, $genre);
                    $this->setSoundCloudFileId($soundcloud_res['id']);
                    $this->setSoundCloudLinkToFile($soundcloud_res['permalink_url']);
                    break;
                }
                catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
                    $code = $e->getHttpCode();
                    $msg = $e->getHttpBody();
                    $temp = explode('"error":',$msg);
                    $msg = trim($temp[1], '"}');
                    $this->setSoundCloudErrorCode($code);
                    $this->setSoundCloudErrorMsg($msg);
                    // setting sc id to -3 which indicates error
                    $this->setSoundCloudFileId(SOUNDCLOUD_ERROR);
                    if(!in_array($code, array(0, 100))) {
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
