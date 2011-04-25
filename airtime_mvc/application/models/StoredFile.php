<?php
require_once("Playlist.php");
require_once("getid3/var/getid3.php");
require_once("Schedule.php");

class Metadata {

    private static $MAP_METADATA_XML_TO_DB = array(
    	"ls:type" => "ftype",
        "dc:format" => "format",
        "ls:bitrate" => "bit_rate",
        "ls:samplerate" => "sample_rate",
        "dcterms:extent" => "length",
        "dc:title" => "track_title",
        "dc:description" => "comments",
        "dc:type" => "genre",
        "dc:creator" => "artist_name",
        "dc:source" => "album_title",
        "ls:channels" => "channels",
        "ls:filename" => "name",
        "ls:year" => "year",
        "ls:url" => "url",
        "ls:track_num" => "track_number",
        "ls:mood" => "mood",
        "ls:bpm" => "bpm",
        "ls:disc_num" => "disc_number",
        "ls:rating" => "rating",
        "ls:encoded_by" => "encoded_by",
        "dc:publisher" => "label",
        "ls:composer" => "composer",
        "ls:encoder" => "encoder",
        "ls:crc" => "checksum",
        "ls:lyrics" => "lyrics",
        "ls:orchestra" => "orchestra",
        "ls:conductor" => "conductor",
        "ls:lyricist" => "lyricist",
        "ls:originallyricist" => "original_lyricist",
        "ls:radiostationname" => "radio_station_name",
        "ls:audiofileinfourl" => "info_url",
        "ls:artisturl" => "artist_url",
        "ls:audiosourceurl" => "audio_source_url",
        "ls:radiostationurl" => "radio_station_url",
        "ls:buycdurl" => "buy_this_url",
        "ls:isrcnumber" => "isrc_number",
        "ls:catalognumber" => "catalog_number",
        "ls:originalartist" => "original_artist",
        "dc:rights" => "copyright",
        "dcterms:temporal" => "report_datetime",
        "dcterms:spatial" => "report_location",
        "dcterms:entity" => "report_organization",
        "dc:subject" => "subject",
        "dc:contributor" => "contributor",
        "dc:language" => "language");

    public static function GetMapMetadataXmlToDb() {
        return Metadata::$MAP_METADATA_XML_TO_DB;
    }

    /**
     * Track numbers in metadata tags can come in many formats:
     * "1 of 20", "1/20", "20/1".  This function parses the track
     * number and gets the real number so that we can sort by it
     * in the database.
     *
     * @param string $p_trackNumber
     * @return int
     */
    public static function ParseTrackNumber($p_trackNumber)
    {
        $num = trim($p_trackNumber);
        if (!is_numeric($num)) {
            $matches = preg_match("/\s*([0-9]+)([^0-9]*)([0-9]*)\s*/", $num, $results);
            $trackNum = 0;
            foreach ($results as $result) {
                if (is_numeric($result)) {
                    if ($trackNum == 0) {
                        $trackNum = $result;
                    } elseif ($result < $trackNum) {
                        $trackNum = $result;
                    }
                }
            }
        } else {
            $trackNum = $num;
        }
        return $trackNum;
    }


    /**
     * Add data to the array $p_mdata.
     *
     * Converts the given string ($val) into UTF-8.
     *
     * @param array $p_mdata
     * 		The array to add the metadata to.
     * @param string $p_key
     * 		Metadata key.
     * @param string $p_val
     * 		Metadata value.
     * @param string $p_inputEncoding
     * 		Encoding type of the input value.
     */
    public static function AddToArray(&$p_mdata, $p_key, $p_val, $p_inputEncoding='iso-8859-1')
    {
        if (!is_null($p_val)) {
            $data = $p_val;
            $outputEncoding = 'UTF-8';
            //if (function_exists('iconv') && ($p_inputEncoding != $outputEncoding) ) {
            if (function_exists('iconv') && is_string($p_val)) {
                $newData = @iconv($p_inputEncoding, $outputEncoding, $data);
                if ($newData === FALSE) {
                    echo "Warning: convert $key data to unicode failed\n";
                } elseif ($newData != $data) {
                    echo "Converted string: '$data' (".gettype($data).") -> '$newData' (".gettype($newData).").\n";
                    $data = $newData;
                }
            }
            $p_mdata[$p_key] = trim($data);
        }
    }


    /**
     * Return an array with the given audio file's ID3 tags.  The keys in the
     * array can be:
     * <pre>
     * 		dc:format ("mime type")
     * 		dcterms:extent ("duration")
     * 		dc:title
     * 		dc:creator ("artist")
     * 		dc:source ("album")
     *      dc:type ("genre")
     * 		ls:bitrate
     * 		ls:encoded_by
     * 		ls:track_num
     * 		ls:channels
     * 		ls:year
     * 		ls:filename
     * </pre>
     *
     * @param string $p_filename
     * @param boolean $p_testonly
     * 		For diagnostic and debugging purposes - setting this to TRUE
     * 		will print out the values found in the file and the ones assigned
     * 		to the return array.
     * @return array|PEAR_Error
     */
    public static function LoadFromFile($p_filename, $p_testonly = false)
    {
        $getID3 = new getID3();
        $infoFromFile = $getID3->analyze($p_filename);
        if (PEAR::isError($infoFromFile)) {
            return $infoFromFile;
        }
        if (isset($infoFromFile['error'])) {
            return new PEAR_Error(array_pop($infoFromFile['error']));
        }
        if (!$infoFromFile['bitrate']) {
            return new PEAR_Error("File given is not an audio file.");
        }

        if ($p_testonly) {
            print_r($infoFromFile);
        }
        $titleKey = 'dc:title';
        $flds = array(
            'dc:format' => array(
        array('path'=>"['mime_type']", 'ignoreEnc'=>TRUE),
        ),
            'ls:bitrate' => array(
        array('path'=>"['bitrate']", 'ignoreEnc'=>TRUE),
        array('path'=>"['audio']['bitrate']", 'ignoreEnc'=>TRUE),
        ),
            'ls:samplerate' => array(
        array('path'=>"['audio']['sample_rate']", 'ignoreEnc'=>TRUE),
        ),
            'ls:encoder' => array(
        array('path'=>"['audio']['codec']", 'ignoreEnc'=>TRUE),
        ),
            'dcterms:extent'=> array(
        array('path'=>"['playtime_seconds']", 'ignoreEnc'=>TRUE),
        ),
            'ls:composer'=> array(
        array('path'=>"['id3v2']['comments']['composer']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        array('path'=>"['id3v2']['TCOM'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['id3v2']['composer']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        array('path'=>"['ogg']['comments']['composer']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['composer']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'dc:description'=> array(
        array('path'=>"['id3v1']['comments']['comment']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['comments']['comments']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        array('path'=>"['id3v2']['COMM'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['id3v2']['comments']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        array('path'=>"['ogg']['comments']['comment']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['comment']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'dc:type'=> array(
        array('path'=>"['id3v1']", 'dataPath'=>"['genre']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['comments']['content_type']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        array('path'=>"['id3v2']['TCON'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'dc:title' => array(
        array('path'=>"['id3v2']['comments']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TIT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v1']", 'dataPath'=>"['title']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'dc:creator' => array(
        array('path'=>"['id3v2']['comments']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TPE1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TP1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v1']", 'dataPath'=>"['artist']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'dc:source' => array(
        array('path'=>"['id3v2']['comments']['album']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TALB'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TAL'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['album']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['album']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'ls:encoded_by'	=> array(
        array('path'=>"['id3v2']['TENC'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TEN'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['encoded-by']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['encoded-by']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'ls:track_num' => array(
        array('path'=>"['id3v2']['TRCK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TRK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['ogg']['comments']['tracknumber']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['tracknumber']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
        //	    'ls:genre'	    => array(
        //	        array('path'=>"['id3v1']", 'dataPath'=>"['genre']", 'encPath'=>"['encoding']"),
        //	        array('path'=>"['id3v2']['TCON'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        //	        array('path'=>"['id3v2']['comments']['content_type']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
        //	        array('path'=>"['ogg']['comments']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        //	        array('path'=>"['tags']['vorbiscomment']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        //	    ),
            'ls:channels' => array(
        array('path'=>"['audio']['channels']", 'ignoreEnc'=>TRUE),
        ),
            'ls:year' => array(
        array('path'=>"['comments']['date']"),
        array('path'=>"['ogg']['comments']['date']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        array('path'=>"['tags']['vorbiscomment']['date']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
        ),
            'ls:filename' => array(
        array('path'=>"['filename']"),
        ),
        );
        $mdata = array();
        if (isset($infoFromFile['audio'])) {
            $mdata['audio'] = $infoFromFile['audio'];
        }
        if (isset($infoFromFile['playtime_seconds'])) {
            $mdata['playtime_seconds'] = $infoFromFile['playtime_seconds'];
        }

        $titleHaveSet = FALSE;
        foreach ($flds as $key => $getid3keys) {
            foreach ($getid3keys as $getid3key) {
                $path = $getid3key["path"];
                $ignoreEnc = isset($getid3key["ignoreEnc"])?
                $getid3key["ignoreEnc"]:FALSE;
                $dataPath = isset($getid3key["dataPath"])?$getid3key["dataPath"]:"";
                $encPath = isset($getid3key["encPath"])?$getid3key["encPath"]:"";
                $enc = "UTF-8";

                $tagElement = "\$infoFromFile$path$dataPath";
                eval("\$tagExists = isset($tagElement);");
                if ($tagExists) {
                    //echo "ignore encoding: ".($ignoreEnc?"yes":"no")."\n";
                    //echo "tag exists\n";
                    //echo "encode path: $encPath\n";
                    eval("\$data = $tagElement;");
                    if (!$ignoreEnc && $encPath != "") {
                        $encodedElement = "\$infoFromFile$path$encPath";
                        eval("\$encodedElementExists = isset($encodedElement);");
                        if ($encodedElementExists) {
                            eval("\$enc = $encodedElement;");
                        }
                    }

                    // Special case handling for track number
                    if ($key == "ls:track_num") {
                        $data = Metadata::ParseTrackNumber($data);
                    }
                    Metadata::AddToArray($mdata, $key, $data, $enc);
                    if ($key == $titleKey) {
                        $titleHaveSet = TRUE;
                    }
                    break;
                }
            }
        }
        if ($p_testonly) {
            var_dump($mdata);
        }

        if (!$titleHaveSet || trim($mdata[$titleKey]) == '') {
            Metadata::AddToArray($mdata, $titleKey, basename($p_filename));
        }
        return $mdata;
    }
}


/**
 *  StoredFile class
 *
 *  Airtime file storage support class.<br>
 *  Represents one virtual file in storage. Virtual file has up to two parts:
 *  <ul>
 *      <li>metadata in database </li>
 *      <li>binary media data in real file</li>
 *  </ul>
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @see MetaData
 */
class StoredFile {

    // *** Variables stored in the database ***

    /**
     * @var int
     */
    private $id;

    /**
     * Unique ID for the file.  This is stored in HEX format.  It is
     * converted to a bigint whenever it is used in a database call.
     *
     * @var string
     */
    public $gunid;

    /**
     * The unique ID of the file as it is stored in the database.
     * This is for debugging purposes and may not always exist in this
     * class.
     *
     * @var string
     */
    //private $gunidBigint;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mime;

    /**
     * Can be 'audioclip'...others might be coming, like webstream.
     *
     * @var string
     */
    private $ftype;

    /**
     * Can be 'ready', 'edited', 'incomplete'.
     *
     * @var string
     */
    private $state;

    /**
     * @var int
     */
    private $currentlyaccessing;

    /**
     * @var int
     */
    private $editedby;

    /**
     * @var timestamp
     */
    private $mtime;

    /**
     * @var string
     */
    private $md5;

    /**
     * @var string
     */
    private $filepath;


    // *** Variables NOT stored in the database ***

    /**
     * Directory where the file is located.
     *
     * @var string
     */
    private $resDir;

    /**
     * @var boolean
     */
    private $exists;

    /**
     * @var MetaData
     */
    public $md;

    /* ========================================================== constructor */
    /**
     * Constructor, but shouldn't be externally called
     *
     * @param string $p_gunid
     *  	globally unique id of file
     */
    public function __construct($p_gunid=NULL)
    {
        $this->gunid = $p_gunid;
        if (empty($this->gunid)) {
            $this->gunid = StoredFile::generateGunid();
        }
        else {
            $this->loadMetadata();
            $this->exists = is_file($this->filepath) && is_readable($this->filepath);
        }
    }

    /**
     * For testing only, do not use.
     */
    public function __setGunid($p_guid) {
        $this->gunid = $p_guid;
    }

    /**
     * Convert XML name to database column name.  Used for backwards compatibility
     * with old code.
     *
     * @param string $p_category
     * @return string|null
     */
    public static function xmlCategoryToDbColumn($p_category)
    {
        $map = Metadata::GetMapMetadataXmlToDb();
        if (array_key_exists($p_category, $map)) {
            return $map[$p_category];
        }
        return null;
    }


    /**
     * Convert database column name to XML name.
     *
     * @param string $p_dbColumn
     * @return string|null
     */
    public static function dbColumnToXmlCatagory($p_dbColumn)
    {
        $str = array_search($p_dbColumn, Metadata::GetMapMetadataXmlToDb());
        // make return value consistent with xmlCategoryToDbColumn()
        if ($str === FALSE) {
            $str = null;
        }
        return $str;
    }


    /**
     * GUNID needs to be set before you call this function.
     *
     */
    public function loadMetadata()
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedValue = pg_escape_string($this->gunid);
        $sql = "SELECT * FROM ".$CC_CONFIG["filesTable"]
        ." WHERE gunid='$escapedValue'";

        $this->md = $CC_DBC->getRow($sql);

        if (PEAR::isError($this->md)) {
            $error = $this->md;
            $this->md = null;
            return $error;
        }
        $this->filepath = $this->md["filepath"];
        if (is_null($this->md)) {
            $this->md = array();
            return;
        }
        $compatibilityData = array();
        foreach ($this->md as $key => $value) {
            if ($xmlName = StoredFile::dbColumnToXmlCatagory($key)) {
                $compatibilityData[$xmlName] = $value;
            }
        }

        $this->md = array_merge($this->md, $compatibilityData);
        //$this->md = $compatibilityData;
    }

    public function setFormat($p_value)
    {
        $this->md["format"] = $p_value;
    }

    public function replaceMetadata($p_values)
    {
        global $CC_CONFIG, $CC_DBC;
        foreach ($p_values as $category => $value) {
            $escapedValue = pg_escape_string($value);
            $columnName = StoredFile::xmlCategoryToDbColumn($category);
            if (!is_null($columnName)) {
                $sql = "UPDATE ".$CC_CONFIG["filesTable"]
                ." SET $columnName='$escapedValue'"
                ." WHERE gunid = '".$this->gunid."'";
                $CC_DBC->query($sql);
            }
        }
        $this->loadMetadata();
    }

	public function replaceDbMetadata($p_values)
    {
        global $CC_CONFIG, $CC_DBC;
        foreach ($p_values as $category => $value) {
            $escapedValue = pg_escape_string($value);
            $columnName = $category;
            if (!is_null($columnName)) {
                $sql = "UPDATE ".$CC_CONFIG["filesTable"]
                ." SET $columnName='$escapedValue'"
                ." WHERE gunid = '".$this->gunid."'";
                $CC_DBC->query($sql);
            }
        }
    }

    public function clearMetadata()
    {
        $metadataColumns = array("format",  "bit_rate", "sample_rate", "length",
        	"track_title", "comments", "genre", "artist_name", "channels", "name",
          "year", "url", "track_number");
        foreach ($metadataColumns as $columnName) {
            if (!is_null($columnName)) {
                $sql = "UPDATE ".$CC_CONFIG["filesTable"]
                ." SET $columnName=''"
                ." WHERE gunid = '".$this->gunid."'";
                $CC_DBC->query($sql);
            }
        }
    }


    /**
     *  Create instance of StoredFile object and insert new file
     *
     * @param array $p_values
     *      "filepath" - required, local path to media file (where it is before import)
     *      "id" - optional, local object id, will be generated if not given
     *      "gunid" - optional, unique id, for insert file with gunid, will be generated if not given
     *      "filename" - optional, will use "filepath" if not given
     *      "metadata" - optional, array of extra metadata, will be automatically calculated if not given.
     *      "mime" - optional, MIME type, highly recommended to pass in, will be automatically calculated if not given.
     *      "md5" - optional, MD5 sum, highly recommended to pass in, will be automatically calculated if not given.
     *
     *  @param boolean $p_copyMedia
     * 		copy the media file if true, make symlink if false
     *
     *  @return StoredFile|NULL|PEAR_Error
     */
    public static function Insert($p_values, $p_copyMedia=TRUE)
    {
        global $CC_CONFIG, $CC_DBC;

        if (!isset($p_values["filepath"])) {
            return new PEAR_Error("StoredFile::Insert: filepath not set.");
        }
        if (!file_exists($p_values['filepath'])) {
            return PEAR::raiseError("StoredFile::Insert: ".
                "media file not found ({$p_values['filepath']})");
        }

        $gunid = isset($p_values['gunid'])?$p_values['gunid']:NULL;

        // Create the StoredFile object
        $storedFile = new StoredFile($gunid);

        // Get metadata
        if (isset($p_values["metadata"])) {
            $metadata = $p_values['metadata'];
        } else {
            $metadata = Metadata::LoadFromFile($p_values["filepath"]);
        }

        $storedFile->name = isset($p_values['filename']) ? $p_values['filename'] : $p_values["filepath"];
        $storedFile->id = isset($p_values['id']) && is_integer($p_values['id'])?(int)$p_values['id']:null;
        // NOTE: POSTGRES-SPECIFIC KEYWORD "DEFAULT" BEING USED, WOULD BE "NULL" IN MYSQL
        $sqlId = !is_null($storedFile->id)?"'".$storedFile->id."'":'DEFAULT';
        $storedFile->ftype = isset($p_values['filetype']) ? strtolower($p_values['filetype']) : "audioclip";
        $storedFile->mime = (isset($p_values["mime"]) ? $p_values["mime"] : NULL );
        // $storedFile->filepath = $p_values['filepath'];
        if (isset($p_values['md5'])) {
            $storedFile->md5 = $p_values['md5'];
        } elseif (file_exists($p_values['filepath'])) {
            //echo "StoredFile::Insert: WARNING: Having to recalculate MD5 value\n";
            $storedFile->md5 = md5_file($p_values['filepath']);
        }

        // Check for duplicates -- return duplicate
        $duplicate = StoredFile::RecallByMd5($storedFile->md5);
        if ($duplicate) {
            return $duplicate;
        }

        $storedFile->exists = FALSE;

        // Insert record into the database
        $escapedName = pg_escape_string($storedFile->name);
        $escapedFtype = pg_escape_string($storedFile->ftype);
        $sql = "INSERT INTO ".$CC_CONFIG['filesTable']
        ."(id, name, gunid, mime, state, ftype, mtime, md5)"
        ."VALUES ({$sqlId}, '{$escapedName}', "
        ." '{$storedFile->gunid}',"
        ." '{$storedFile->mime}', 'incomplete', '$escapedFtype',"
        ." now(), '{$storedFile->md5}')";
        //$_SESSION["debug"] .= "sql: ".$sql."<br>";
        //echo $sql."\n";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        if (!is_integer($storedFile->id)) {
            // NOTE: POSTGRES-SPECIFIC
            $sql = "SELECT currval('".$CC_CONFIG["filesSequence"]."_seq')";
            $storedFile->id = $CC_DBC->getOne($sql);
        }
        $storedFile->setMetadataBatch($metadata);

        // Save media file
        $res = $storedFile->addFile($p_values['filepath'], $p_copyMedia);
        if (PEAR::isError($res)) {
            echo "StoredFile::Insert -- addFile(): '".$res->getMessage()."'\n";
            return $res;
        }

        if (empty($storedFile->mime)) {
            //echo "StoredFile::Insert: WARNING: Having to recalculate MIME value\n";
            $storedFile->setMime($storedFile->getMime());
        }

        // Save state
        $storedFile->setState('ready');

        // Recall the object to get all the proper values
        $storedFile = StoredFile::RecallByGunid($storedFile->gunid);
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
     * @return StoredFile|Playlist|NULL
     *    Return NULL if the object doesnt exist in the DB.
     */
    public static function Recall($p_id=null, $p_gunid=null, $p_md5sum=null)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        if (!is_null($p_id)) {
            $cond = "id='".intval($p_id)."'";
        } elseif (!is_null($p_gunid)) {
            $cond = "gunid='$p_gunid'";
        } elseif (!is_null($p_md5sum)) {
            $cond = "md5='$p_md5sum'";
        } else {
            return null;
        }
        $sql = "SELECT *"
        ." FROM ".$CC_CONFIG['filesTable']
        ." WHERE $cond";

        $row = $CC_DBC->getRow($sql);
        if (PEAR::isError($row) || is_null($row)) {
            return $row;
        }
        $gunid = $row['gunid'];
        $storedFile = new StoredFile($gunid);
        $storedFile->id = $row['id'];
        $storedFile->name = $row['name'];
        $storedFile->mime = $row['mime'];
        $storedFile->ftype = $row['ftype'];
        $storedFile->state = $row['state'];
        $storedFile->currentlyaccessing = $row['currentlyaccessing'];
        $storedFile->editedby = $row['editedby'];
        $storedFile->mtime = $row['mtime'];
        $storedFile->md5 = $row['md5'];
        $storedFile->filepath = $row['filepath'];
        $storedFile->exists = TRUE;
        $storedFile->setFormat($row['ftype']);
        return $storedFile;
    }

    /**
     * Create instance of StoreFile object and recall existing file
     * by gunid.
     *
     * @param string $p_gunid
     * 		global unique id of file
     * @return StoredFile
     */
    public static function RecallByGunid($p_gunid='')
    {
        return StoredFile::Recall(null, $p_gunid);
    }


    /**
     * Fetch the StoredFile by looking up the MD5 value.
     *
     * @param string $p_md5sum
     * @return StoredFile|NULL|PEAR_Error
     */
    public static function RecallByMd5($p_md5sum)
    {
        return StoredFile::Recall(null, null, $p_md5sum);
    }


    public static function GetAll()
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT * FROM ".$CC_CONFIG["filesTable"];
        $rows = $CC_DBC->GetAll($sql);
        return $rows;
    }

    /**
     * Generate the location to store the file.
     * It creates the subdirectory if needed.
     */
    private function generateFilePath()
    {
        global $CC_CONFIG, $CC_DBC;
        $resDir = $CC_CONFIG['storageDir']."/".substr($this->gunid, 0, 3);
        if (!is_dir($resDir)) {
            mkdir($resDir, 02775);
            chmod($resDir, 02775);
        }
        $info = pathinfo($this->name);
        $fileExt = strtolower($info["extension"]);
        return "{$resDir}/{$this->gunid}.{$fileExt}";
    }

    /**
     * Insert media file to filesystem
     *
     * @param string $p_localFilePath
     * 		local path
     * @param boolean $p_copyMedia
     * 		copy the media file if true, make symlink if false
     * @return TRUE|PEAR_Error
     */
    public function addFile($p_localFilePath, $p_copyMedia=TRUE)
    {
        global $CC_CONFIG, $CC_DBC;
        if ($this->exists) {
            return FALSE;
        }
        // for files downloaded from remote instance:
        if ($p_localFilePath == $this->filepath) {
            $this->exists = TRUE;
            return TRUE;
        }
        umask(0002);
        $dstFile = '';
        if ($p_copyMedia) {
            $dstFile = $this->generateFilePath();
            $r = @copy($p_localFilePath, $dstFile);
            if (!$r) {
                $this->exists = FALSE;
                return PEAR::raiseError(
                    "StoredFile::addFile: file save failed".
                    " ($p_localFilePath, {$this->filepath})",GBERR_FILEIO
                );
            }
        } else {
            $dstFile = $p_localFilePath;
            $r = TRUE;
        }
        $this->filepath = $dstFile;
        $sqlPath = pg_escape_string($this->filepath);
        $sql = "UPDATE ".$CC_CONFIG["filesTable"]
        ." SET filepath='{$sqlPath}'"
        ." WHERE id={$this->id}";
        //echo $sql."\n";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->exists = TRUE;
        return TRUE;
    }


    /**
     * Find and return the first exact match for the original file name
     * that was used on import.
     * @param string $p_name
     */
    public static function findByOriginalName($p_name)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT id FROM ".$CC_CONFIG["filesTable"]
            ." WHERE name='".pg_escape_string($p_name)."'";
        $id = $CC_DBC->getOne($sql);
        if (is_numeric($id)) {
            return StoredFile::Recall($id);
        } else {
            return NULL;
        }
    }


    /**
     * Delete and insert media file
     *
     * @param string $p_localFilePath
     *      local path
     * @return TRUE|PEAR_Error
     */
    public function replaceFile($p_localFilePath)
    {
        // Dont do anything if the source and destination files are
        // the same.
        if ($this->name == $p_localFilePath) {
            return TRUE;
        }

        if ($this->exists) {
            $r = $this->deleteFile();
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $this->addFile($p_localFilePath);
    }


    /**
     * Return true if file corresponding to the object exists
     *
     * @return boolean
     */
    public function existsFile()
    {
        return $this->exists;
    }


    /**
     * Create instance of StoredFile object and make copy of existing file
     *
     * @param StoredFile $p_src
     * 		source object
     * @param int $p_nid
     * 		new local id
     * @return StoredFile
     */
    public static function CopyOf(&$p_src, $p_nid)
    {
        $values = array(
            "id" => $p_nid,
            "filename" => $p_src->name,
            "filepath" => $p_src->getRealFilePath(),
            "filetype" => $p_src->getType()
        );
        $storedFile = StoredFile::Insert($values);
        if (PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $storedFile->replaceMetadata($p_src->getAllMetadata(), 'string');
        return $storedFile;
    }


    private static function NormalizeExtent($v)
    {
        if (!preg_match("|^\d{2}:\d{2}:\d{2}.\d{6}$|", $v)) {
            $s = Playlist::playlistTimeToSeconds($v);
            $t = Playlist::secondsToPlaylistTime($s);
            return $t;
        }
        return $v;
    }

    /**
     * Set metadata element value
     *
     * @param string $category
     * 		Metadata element identification (e.g. dc:title)
     * @param string $value
     * 		value to store, if NULL then delete record
     * @return boolean
     */
    public function setMetadataValue($p_category, $p_value)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_string($p_category) || is_array($p_value)) {
            return FALSE;
        }
        if ($p_category == 'dcterms:extent') {
            $p_value = StoredFile::NormalizeExtent($p_value);
        }
        $columnName = StoredFile::xmlCategoryToDbColumn($p_category); // Get column name

        if (!is_null($columnName)) {
            $escapedValue = pg_escape_string($p_value);
            $sql = "UPDATE ".$CC_CONFIG["filesTable"]
            ." SET $columnName='$escapedValue'"
            ." WHERE id={$this->id}";
            //var_dump($sql);
            $res = $CC_DBC->query($sql);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return TRUE;
    }


    /**
     * Set metadata values in 'batch' mode
     *
     * @param array $values
     * 		array of key/value pairs
     *      (e.g. 'dc:title'=>'New title')
     * @return boolean
     */
    public function setMetadataBatch($values)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_array($values)) {
            $values = array($values);
        }
        if (count($values) == 0) {
            return true;
        }
        foreach ($values as $category => $oneValue) {
            $columnName = StoredFile::xmlCategoryToDbColumn($category);
            if (!is_null($columnName)) {
                if ($category == 'dcterms:extent') {
                    $oneValue = StoredFile::NormalizeExtent($oneValue);
                }
                // Since track_number is an integer, you cannot set
                // it to be the empty string, so we NULL it instead.
                if ($columnName == 'track_number' && empty($oneValue)) {
                    $sqlPart = "$columnName = NULL";
                } elseif (($columnName == 'length') && (strlen($oneValue) > 8)) {
                    // Postgres doesnt like it if you try to store really large hour
                    // values.  TODO: We need to fix the underlying problem of getting the
                    // right values.
                    $parts = explode(':', $oneValue);
                    $hour = intval($parts[0]);
                    if ($hour > 24) {
                        continue;
                    } else {
                        $sqlPart = "$columnName = '$oneValue'";
                    }
                } else {
                    $escapedValue = pg_escape_string($oneValue);
                    $sqlPart = "$columnName = '$escapedValue'";
                }
                $sqlValues[] = $sqlPart;
            }
        }
        if (count($sqlValues)==0) {
            return TRUE;
        }
        $sql = "UPDATE ".$CC_CONFIG["filesTable"]
        ." SET ".join(",", $sqlValues)
        ." WHERE id={$this->id}";
        $CC_DBC->query($sql);
        return TRUE;
    }


    /**
     * Get metadata as array, indexed by the column names in the database.
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->md;
    }

    /**
     * Get one metadata value.
     *
     * @param string $p_name
     * @return string
     */
    public function getMetadataValue($p_name)
    {
        if (isset($this->md[$p_name])){
            return $this->md[$p_name];
        } else {
            return "";
        }
    }

    /**
     * Rename stored virtual file
     *
     * @param string $p_newname
     * @return TRUE|PEAR_Error
     */
    public function setName($p_newname)
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedName = pg_escape_string($p_newname);
        $sql = "UPDATE ".$CC_CONFIG['filesTable']
        ." SET name='$escapedName', mtime=now()"
        ." WHERE gunid='{$this->gunid}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->name = $p_newname;
        return TRUE;
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
     * Set mime-type of virtual file
     *
     * @param string $p_mime
     * 		mime-type
     * @return boolean|PEAR_Error
     */
    public function setMime($p_mime)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_string($p_mime)) {
            $p_mime = 'application/octet-stream';
        }
        $escapedMime = pg_escape_string($p_mime);
        $sql = "UPDATE ".$CC_CONFIG['filesTable']
        ." SET mime='$escapedMime', mtime=now()"
        ." WHERE gunid='{$this->gunid}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->mime = $p_mime;
        return TRUE;
    }


    /**
     * Set md5 of virtual file
     *
     * @param string $p_md5sum
     * @return boolean|PEAR_Error
     */
    public function setMd5($p_md5sum)
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedMd5 = pg_escape_string($p_md5sum);
        $sql = "UPDATE ".$CC_CONFIG['filesTable']
        ." SET md5='$escapedMd5', mtime=now()"
        ." WHERE gunid='{$this->gunid}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->md5 = $p_md5sum;
        return TRUE;
    }

    /**
     * Delete media file from filesystem.
     * You cant delete a file if it is being accessed.
     * You cant delete a file if it is scheduled to be played in the future.
     * The file will be removed from all playlists it is a part of.
     *
     * @return boolean|PEAR_Error
     */
    public function deleteFile()
    {
        global $CC_CONFIG;
        if (!$this->exists) {
            return FALSE;
        }
        if ($this->isAccessed()) {
            return PEAR::raiseError(
                'Cannot delete a file that is currently accessed.'
                );
        }

        // Check if the file is scheduled to be played in the future
        if (Schedule::IsFileScheduledInTheFuture($this->id)) {
            return PEAR::raiseError(
                'Cannot delete a file that is scheduled in the future.'
                );
        }

        // Only delete the file from filesystem if it has been copied to the
        // storage directory. (i.e. dont delete linked files)
        if (substr($this->filepath, 0, strlen($CC_CONFIG["storageDir"])) == $CC_CONFIG["storageDir"]) {
            // Delete the file
            if (!file_exists($this->filepath) || @unlink($this->filepath)) {
                $this->exists = FALSE;
                return TRUE;
            }
            else {
                return PEAR::raiseError(
                    "StoredFile::deleteFile: unlink failed ({$this->filepath})",
                    GBERR_FILEIO
                );
            }
        }
        else {
            $this->exists = FALSE;
            return TRUE;
        }
    }


    /**
     * Delete stored virtual file
     *
     * @param boolean $p_deleteFile
     *
     * @return TRUE|PEAR_Error
     */
    public function delete($p_deleteFile = true)
    {
        global $CC_CONFIG, $CC_DBC;
        if ($p_deleteFile) {
            $res = $this->deleteFile();
            if (PEAR::isError($res)) {
                return $res;
            }

            Playlist::DeleteFileFromAllPlaylists($this->id);
        }

        $sql = "DELETE FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='{$this->gunid}'";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    public static function deleteById($p_id)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!is_numeric($p_id)) {
            return FALSE;
        }
        $sql = "DELETE FROM ".$CC_CONFIG["filesTable"]." WHERE id=$p_id";

        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }

    public static function deleteAll()
    {
        global $CC_CONFIG, $CC_DBC;
        $files = StoredFile::getAll();
        foreach ($files as $file) {
            $media = StoredFile::Recall($file["id"]);
            $media->delete();
        }
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
                $playlists[] = Playlist::Recall($id);
            }
        }
        return $playlists;
    }


    /**
     * Returns true if virtual file is currently in use.<br>
     * Static or dynamic call is possible.
     *
     * @param string $p_gunid
     * 		optional (for static call), global unique id
     * @return boolean|PEAR_Error
     */
    public function isAccessed($p_gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($p_gunid)) {
            return ($this->currentlyaccessing > 0);
        }
        $sql = "SELECT currentlyAccessing FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='$p_gunid'";
        $ca = $CC_DBC->getOne($sql);
        if (is_null($ca)) {
            return PEAR::raiseError(
                "StoredFile::isAccessed: invalid gunid ($p_gunid)",
            GBERR_FOBJNEX
            );
        }
        return ($ca > 0);
    }


    /**
     * Returns true if virtual file is edited
     *
     * @param string $p_playlistId
     * 		playlist global unique ID
     * @return boolean
     */
    public function isEdited($p_playlistId=NULL)
    {
        if (is_null($p_playlistId)) {
            return ($this->state == 'edited');
        }
        $state = $this->getState($p_playlistId);
        if ($state != 'edited') {
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Returns id of user editing playlist
     *
     * @param string $p_playlistId
     * 		playlist global unique ID
     * @return int|null|PEAR_Error
     * 		id of user editing it
     */
    public function isEditedBy($p_playlistId=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($p_playlistId)) {
            $p_playlistId = $this->gunid;
        }
        $sql = "SELECT editedBy FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='$p_playlistId'";
        $ca = $CC_DBC->getOne($sql);
        if (PEAR::isError($ca)) {
            return $ca;
        }
        if (is_null($ca)) {
            return $ca;
        }
        return intval($ca);
    }


    /**
     * Return local ID of virtual file.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Return global ID of virtual file.
     *
     * @return string
     */
    public function getGunid()
    {
        return $this->gunid;
    }


    /**
     * Returns true if raw media file exists
     * @return boolean|PEAR_Error
     */
    public function exists()
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT gunid "
        ." FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='{$this->gunid}'";
        $indb = $CC_DBC->getRow($sql);
        if (PEAR::isError($indb)) {
            return $indb;
        }
        if (is_null($indb)) {
            return FALSE;
        }
        if ($this->ftype == 'audioclip') {
            return $this->existsFile();
        }
        return TRUE;
    }


    /**
     * Create new global unique id
     * @return string
     */
    public static function generateGunid()
    {
        return md5(uniqid("", true));
    }


    /**
     * Return suitable extension.
     *
     * @todo make it general - is any tool for it?
     *
     * @return string
     * 		file extension without a dot
     */
    public function getFileExtension()
    {
        $fname = $this->getName();
        $pos = strrpos($fname, '.');
        if ($pos !== FALSE) {
            $ext = substr($fname, $pos+1);
            if ($ext !== FALSE) {
                return $ext;
            }
        }
        switch (strtolower($this->mime)) {
            case "audio/mpeg":
                $ext = "mp3";
                break;
            case "audio/x-wav":
            case "audio/x-wave":
                $ext = "wav";
                break;
            case "audio/x-ogg":
            case "application/x-ogg":
                $ext = "ogg";
                break;
            default:
                $ext = "bin";
                break;
        }
        return $ext;
    }


    /**
     * Get mime-type stored in the file.
     * Warning: this function is slow!
     *
     * @return string
     */
    public function getMime()
    {
        $a = Metadata::LoadFromFile($this->filepath);
        if (PEAR::isError($a)) {
            return $a;
        }
        if (isset($a['dc:format'])) {
            return $a['dc:format'];
        }
        return '';
    }


    /**
     * Convenience function.
     * @return string
     */
    public function getTitle()
    {
        return $this->md["title"];
    }

    public function getType()
    {
        return $this->ftype;
    }

    /**
     * Get storage-internal file state
     *
     * @param string $p_gunid
     * 		global unique id of file
     * @return string
     * 		see install()
     */
    public function getState($p_gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($p_gunid)) {
            return $this->state;
        }
        $sql = "SELECT state FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='$p_gunid'";
        return $CC_DBC->getOne($sql);
    }


    /**
     * Get mnemonic file name
     *
     * @param string $p_gunid
     * 		global unique id of file
     * @return string
     */
    public function getName($p_gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($p_gunid)) {
            return $this->name;
        }
        $sql = "SELECT name FROM ".$CC_CONFIG['filesTable']
        ." WHERE gunid='$p_gunid'";
        return $CC_DBC->getOne($sql);
    }


    /**
     * Get real filename of raw media data
     *
     * @return string
     */
    public function getRealFilePath()
    {
        return $this->filepath;
    }

    /**
     * Get the URL to access this file.
     */
    public function getFileUrl()
    {
        global $CC_CONFIG;
        return $this->gunid.".".$this->getFileExtension();
    }

    /**
     * Get real filename of metadata file
     *
     * @return string
     * @see MetaData
     */
    public function getRealMetadataFileName()
    {
        //return $this->md->getFileName();
        return $this->md["name"];
    }


    /**
     * Create and return name for temporary symlink.
     *
     * @todo Should be more unique
     * @return string
     */
    private function _getAccessFileName($p_token, $p_ext='EXT')
    {
        global $CC_CONFIG;
        return $CC_CONFIG['accessDir']."/$p_token.$p_ext";
    }


	public static function searchFilesForPlaylistBuilder($datatables) {
		global $CC_CONFIG;

		$plSelect = "SELECT ";
        $fileSelect = "SELECT ";
        foreach (Metadata::GetMapMetadataXmlToDb() as $key => $val){

            if($key === "dc:title"){
                $plSelect .= "name AS ".$val.", ";
                $fileSelect .= $val.", ";
            }
			else if ($key === "ls:type"){
                $plSelect .= "'playlist' AS ".$val.", ";
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

		$fromTable = " ((".$plSelect."PL.id
		    FROM ".$CC_CONFIG["playListTable"]." AS PL
			LEFT JOIN ".$CC_CONFIG['playListTimeView']." AS PLT USING(id))

		    UNION

		    (".$fileSelect."id FROM ".$CC_CONFIG["filesTable"]." AS FILES)) AS RESULTS";

		return StoredFile::searchFiles($fromTable, $datatables);

	}

	public static function searchPlaylistsForSchedule($datatables)
    {
		$fromTable = "cc_playlist AS pl LEFT JOIN cc_playlisttimes AS plt USING(id) LEFT JOIN cc_subjs AS sub ON pl.editedby = sub.id";
        //$datatables["optWhere"][] = "INTERVAL '{$time_remaining}' > INTERVAL '00:00:00'";
        $datatables["optWhere"][] = "plt.length > INTERVAL '00:00:00'";

		return StoredFile::searchFiles($fromTable, $datatables);
	}

	public static function searchFiles($fromTable, $data)
	{
		global $CC_CONFIG, $CC_DBC;

		$columnsDisplayed = explode(",", $data["sColumns"]);

		if($data["sSearch"] !== "")
			$searchTerms = explode(" ", $data["sSearch"]);

		$selectorCount = "SELECT COUNT(*)";
		$selectorRows = "SELECT ". join("," , $columnsDisplayed);

        $sql = $selectorCount." FROM ".$fromTable;
		$totalRows = $CC_DBC->getOne($sql);

		//	Where clause
		if(isset($data["optWhere"])) {
			$where[] = join(" AND ", $data["optWhere"]);
		}

		if(isset($searchTerms)) {
			$searchCols = array();
			for($i=0; $i<$data["iColumns"]; $i++) {
				if($data["bSearchable_".$i] == "true") {
					$searchCols[] = $columnsDisplayed[$i];
				}
			}

			$outerCond = array();

			foreach($searchTerms as $term) {
				$innerCond = array();

				foreach($searchCols as $col) {
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
		for($i=0; $i<$data["iSortingCols"]; $i++){
			$orderby[] = $columnsDisplayed[$data["iSortCol_".$i]]." ".$data["sSortDir_".$i];
		}
		$orderby[] = "id";
		$orderby = join("," , $orderby);
		// End Order By clause

		//ordered by integer as expected by datatables.
		$CC_DBC->setFetchMode(DB_FETCHMODE_ORDERED);

		if(isset($where)) {
			$where = join(" AND ", $where);
			$sql = $selectorCount." FROM ".$fromTable." WHERE ".$where;
			$totalDisplayRows = $CC_DBC->getOne($sql);
			$sql = $selectorRows." FROM ".$fromTable." WHERE ".$where." ORDER BY ".$orderby." OFFSET ".$data["iDisplayStart"]." LIMIT ".$data["iDisplayLength"];
		}
		else {
			$sql = $selectorRows." FROM ".$fromTable." ORDER BY ".$orderby." OFFSET ".$data["iDisplayStart"]." LIMIT ".$data["iDisplayLength"];
		}

		$results = $CC_DBC->getAll($sql);
		//echo $results;
		//echo $sql;

		//put back to default fetch mode.
		$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

		if(!isset($totalDisplayRows)) {
			$totalDisplayRows = $totalRows;
		}

		return array("sEcho" => intval($data["sEcho"]), "iTotalDisplayRecords" => $totalDisplayRows, "iTotalRecords" => $totalRows, "aaData" => $results);
	}

    public static function uploadFile($targetDir) {

        // HTTP headers for no cache etc
		header('Content-type: text/plain; charset=UTF-8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Settings
		//$targetDir = ini_get("upload_tmp_dir"); //. DIRECTORY_SEPARATOR . "plupload";
		$cleanupTargetDir = false; // Remove old files
		$maxFileAge = 60 * 60; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);
		// usleep(5000);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		//$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);

		// Remove old temp files
		if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

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

		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
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

		$audio_file = $targetDir . DIRECTORY_SEPARATOR . $fileName;

		$md5 = md5_file($audio_file);
		$duplicate = StoredFile::RecallByMd5($md5);
		if ($duplicate) {
			if (PEAR::isError($duplicate)) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' . $duplicate->getMessage() .'}}');
			}
			else {
				$duplicateName = $duplicate->getMetadataValue(UI_MDATA_KEY_TITLE);
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "An identical audioclip named ' . $duplicateName . ' already exists in the storage server."}}');
			}
		}

		$metadata = Metadata::LoadFromFile($audio_file);

		if (PEAR::isError($metadata)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' + $metadata->getMessage() + '}}');
		}

		// #2196 no id tag -> use the original filename
		if (basename($audio_file) == $metadata[UI_MDATA_KEY_TITLE]) {
			$metadata[UI_MDATA_KEY_TITLE] = basename($audio_file);
			$metadata[UI_MDATA_KEY_FILENAME] = basename($audio_file);
		}

		// setMetadataBatch doesnt like these values
		unset($metadata['audio']);
		unset($metadata['playtime_seconds']);

		$values = array(
		    "filename" =>  basename($audio_file),
		    "filepath" => $audio_file,
		    "filetype" => "audioclip",
		    "mime" => $metadata[UI_MDATA_KEY_FORMAT],
		    "md5" => $md5
		);
		$storedFile = StoredFile::Insert($values);

		if (PEAR::isError($storedFile)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": ' + $storedFile->getMessage() + '}}');
		}

		$storedFile->setMetadataBatch($metadata);

		return $storedFile;
    }

}

