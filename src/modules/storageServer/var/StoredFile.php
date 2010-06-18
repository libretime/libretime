<?php
require_once("MetaData.php");
require_once("Playlist.php");
require_once(dirname(__FILE__)."/../../getid3/var/getid3.php");

/**
 * Track numbers in metadata tags can come in many formats:
 * "1 of 20", "1/20", "20/1".  This function parses the track
 * number and gets the real number so that we can sort by it
 * in the database.
 *
 * @param string $p_trackNumber
 * @return int
 */
function camp_parse_track_number($p_trackNumber)
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
 * Add data to the global array $mdata, also sets global variables
 * $titleHaveSet and $titleKey.
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
function camp_add_metadata(&$p_mdata, $p_key, $p_val, $p_inputEncoding='iso-8859-1')
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
function camp_get_audio_metadata($p_filename, $p_testonly = false)
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
                    $data = camp_parse_track_number($data);
                }
                camp_add_metadata($mdata, $key, $data, $enc);
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
    	camp_add_metadata($mdata, $titleKey, basename($p_filename));
    }
    return $mdata;
}


/**
 *  StoredFile class
 *
 *  Campcaster file storage support class.<br>
 *  Represents one virtual file in storage. Virtual file has up to two parts:
 *  <ul>
 *      <li>metadata in database - represented by MetaData class</li>
 *      <li>binary media data in real file</li>
 *  </ul>
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see MetaData
 */
class StoredFile {

    // *** Variable stored in the database ***

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
	private $gunidBigint;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $mime;

	/**
	 * Can be 'playlist' or 'audioclip'.
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


	// *** Variables NOT stored in the database ***

	/**
	 * @var string
	 */
	private $filepath;

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
        global $CC_CONFIG;
        global $CC_DBC;
        $this->gunid = $p_gunid;
        if (empty($this->gunid)) {
            $this->gunid = StoredFile::CreateGunid();
        }
        $this->resDir = $this->_getResDir($this->gunid);
        $this->filepath = "{$this->resDir}/{$this->gunid}";
        $this->exists = is_file($this->filepath) && is_readable($this->filepath);
        $this->md = new MetaData($this->gunid, $this->resDir);
    }


    /* ========= 'factory' methods - should be called to construct StoredFile */
    /**
     *  Create instance of StoredFile object and insert new file
     *
     * @param array $p_values
     *      "id" - required, local object id in the tree
     *      "gunid" - optional, unique id, for insert file with gunid
     *      "filename" - optional
     *      "filepath" - local path to media file, not needed for Playlist
     *      "metadata" - local path to metadata XML file or XML string
     *      "filetype" - internal file type
     *      "mime" - MIME type, highly recommended to pass in
     *      "md5" - MD5 sum, highly recommended to pass in
     *  @param boolean $p_copyMedia
     * 		copy the media file if true, make symlink if false
     *  @return StoredFile|NULL|PEAR_Error
     */
    public static function Insert($p_values, $p_copyMedia=TRUE)
    {
        global $CC_CONFIG, $CC_DBC;

        $gunid = isset($p_values['gunid'])?$p_values['gunid']:NULL;

        // Create the StoredFile object
        $storedFile = new StoredFile($gunid);
        $storedFile->name = isset($p_values['filename']) ? $p_values['filename'] : $storedFile->gunid;
        $storedFile->id = $p_values['id'];
        $storedFile->ftype = $p_values['filetype'];
        if ($storedFile->ftype == 'playlist') {
            $storedFile->mime = 'application/smil';
        } else {
            $storedFile->mime = (isset($p_values["mime"]) ? $p_values["mime"] : NULL );
        }
#        $storedFile->filepath = $p_values['filepath'];
        if (isset($p_values['md5'])) {
            $storedFile->md5 = $p_values['md5'];
        } elseif (file_exists($p_values['filepath'])) {
#            echo "StoredFile::Insert: WARNING: Having to recalculate MD5 value\n";
            $storedFile->md5 = md5_file($p_values['filepath']);
        }

        $storedFile->exists = FALSE;
        $emptyState = TRUE;

        // Insert record into the database
        $escapedName = pg_escape_string($storedFile->name);
        $escapedFtype = pg_escape_string($storedFile->ftype);
        $CC_DBC->query("BEGIN");
        $sql = "INSERT INTO ".$CC_CONFIG['filesTable']
                ."(id, name, gunid, mime, state, ftype, mtime, md5)"
                ."VALUES ('{$storedFile->id}', '{$escapedName}', "
                ." x'{$storedFile->gunid}'::bigint,"
                ." '{$storedFile->mime}', 'incomplete', '$escapedFtype',"
                ." now(), '{$storedFile->md5}')";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Insert metadata
        $metadata = $p_values['metadata'];
        // $mdataLoc = ($metadata[0]=="/")? "file":"string";
        // for non-absolute paths:
        $mdataLoc = ($metadata[0]!="<")? "file":"string";
        if (is_null($metadata) || ($metadata == '') ) {
            $metadata = dirname(__FILE__).'/emptyMdata.xml';
            $mdataLoc = 'file';
        } else {
            $emptyState = FALSE;
        }
        if ( ($mdataLoc == 'file') && !file_exists($metadata)) {
            return PEAR::raiseError("StoredFile::Insert: ".
                "metadata file not found ($metadata)");
        }
        $res = $storedFile->md->insert($metadata, $mdataLoc, $storedFile->ftype);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Save media file
        if (!empty($p_values['filepath'])) {
            if (!file_exists($p_values['filepath'])) {
                return PEAR::raiseError("StoredFile::Insert: ".
                    "media file not found ({$p_values['filepath']})");
            }
            $res = $storedFile->addFile($p_values['filepath'], $p_copyMedia);
            if (PEAR::isError($res)) {
                echo "StoredFile::Insert: ERROR adding file: '".$res->getMessage()."'\n";
                $CC_DBC->query("ROLLBACK");
                return $res;
            }
            if (empty($storedFile->mime)) {
#                echo "StoredFile::Insert: WARNING: Having to recalculate MIME value\n";
                $storedFile->setMime($storedFile->getMime());
            }
            $emptyState = FALSE;
        }

        // Save state
        if (!$emptyState) {
            $res = $storedFile->setState('ready');
        }

        // Commit changes
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }

        // Recall the object to get all the proper values
        $storedFile = StoredFile::RecallByGunid($storedFile->gunid);
        return $storedFile;
    }


    /**
     * Fetch instance of StoreFile object.<br>
     * Should be supplied with only ONE parameter, all the rest should
     * be NULL.
     *
     * @param int $p_oid
     * 		local object id in the tree
     * @param string $p_gunid
     * 		global unique id of file
     * @param string $p_md5sum
     *      MD5 sum of the file
     * @return StoredFile|Playlist|NULL
     *      Return NULL if the object doesnt exist in the DB.
     */
    public static function Recall($p_oid=null, $p_gunid=null, $p_md5sum=null)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        if (!is_null($p_oid)) {
            $cond = "id='".intval($p_oid)."'";
        } elseif (!is_null($p_gunid)) {
            $cond = "gunid=x'$p_gunid'::bigint";
        } elseif (!is_null($p_md5sum)) {
            $cond = "md5='$p_md5sum'";
        } else {
            return null;
        }
        $sql = "SELECT id, to_hex(gunid)as gunid, gunid as gunid_bigint,"
            ." name, mime, ftype, state, currentlyaccessing, editedby, "
            ." mtime, md5"
            ." FROM ".$CC_CONFIG['filesTable']
            ." WHERE $cond";
        $row = $CC_DBC->getRow($sql);
        if (PEAR::isError($row)) {
            return $row;
        }
        if (is_null($row)) {
            return null;
        }
        $gunid = StoredFile::NormalizeGunid($row['gunid']);
        if ($row['ftype'] == 'audioclip') {
            $storedFile = new StoredFile($gunid);
        } elseif ($row['ftype'] == 'playlist') {
            $storedFile = new Playlist($gunid);
        } else {        // fallback
            $storedFile = new StoredFile($gunid);
        }
        $storedFile->gunidBigint = $row['gunid_bigint'];
        $storedFile->md->gunidBigint = $row['gunid_bigint'];
        $storedFile->id = $row['id'];
        $storedFile->name = $row['name'];
        $storedFile->mime = $row['mime'];
        $storedFile->ftype = $row['ftype'];
        $storedFile->state = $row['state'];
        $storedFile->currentlyaccessing = $row['currentlyaccessing'];
        $storedFile->editedby = $row['editedby'];
        $storedFile->mtime = $row['mtime'];
        $storedFile->md5 = $row['md5'];
        $storedFile->exists = TRUE;
        $storedFile->md->setFormat($row['ftype']);
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


    /**
     * Create instance of StoreFile object and recall existing file
     * by access token.
     *
     * @param string $p_token
     * 		access token
     * @return StoredFile
     */
    public static function RecallByToken($p_token)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT to_hex(gunid) as gunid"
            ." FROM ".$CC_CONFIG['accessTable']
            ." WHERE token=x'$p_token'::bigint";
        $gunid = $CC_DBC->getOne($sql);
        if (PEAR::isError($gunid)) {
            return $gunid;
        }
        if (is_null($gunid)) {
            return PEAR::raiseError(
            "StoredFile::RecallByToken: invalid token ($p_token)", GBERR_AOBJNEX);
        }
        $gunid = StoredFile::NormalizeGunid($gunid);
        return StoredFile::Recall(null, $gunid);
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
        if ($this->exists) {
        	return FALSE;
        }
        // for files downloaded from archive:
        if ($p_localFilePath == $this->filepath) {
            $this->exists = TRUE;
            return TRUE;
        }
        umask(0002);
        if ($p_copyMedia) {
            $r = @copy($p_localFilePath, $this->filepath);
        } else {
            $r = @symlink($p_localFilePath, $this->filepath);
        }
        if ($r) {
            $this->exists = TRUE;
            return TRUE;
        } else {
            $this->exists = FALSE;
            return PEAR::raiseError(
                "StoredFile::addFile: file save failed".
                " ($p_localFilePath, {$this->filepath})",GBERR_FILEIO
            );
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
     * Delete media file from filesystem
     *
     * @return boolean|PEAR_Error
     */
    public function deleteFile()
    {
        if (!$this->exists) {
        	return FALSE;
        }
        if (!file_exists($this->filepath) || @unlink($this->filepath)) {
            $this->exists = FALSE;
            return TRUE;
        } else {
            return PEAR::raiseError(
                "StoredFile::deleteFile: unlink failed ({$this->filepath})",
                GBERR_FILEIO
            );
        }
        return $this->exists;
    }


    /**
     * Analyze file with getid3 module.<br>
     * Obtain some metadata stored in media file.<br>
     * This method should be used for prefilling metadata input form.
     *
     * @return array
     * 		hierarchical hasharray with information about media file
     */
    public function analyzeFile()
    {
        if (!$this->exists) {
        	return FALSE;
        }
        $ia = camp_get_audio_metadata($this->filepath);
        return $ia;
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
            "filepath" => $p_src->getRealFileName(),
            "filetype" => BasicStor::GetType($p_src->gunid)
        );
        $storedFile = StoredFile::Insert($values);
        if (PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $storedFile->md->replace($p_src->md->getMetadata(), 'string');
        return $storedFile;
    }


    /**
     * Replace existing file with new data.
     *
     * @param int $p_oid
     * 		NOT USED
     * @param string $p_name
     * 		name of file
     * @param string $p_localFilePath
     * 		local path to media file
     * @param string $p_metadata
     * 		local path to metadata XML file or XML string
     * @param string $p_mdataLoc
     * 		'file'|'string'
     * @return TRUE|PEAR_Error
     */
    public function replace($p_oid, $p_name, $p_localFilePath='', $p_metadata='',
        $p_mdataLoc='file')
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("BEGIN");
        $res = $this->setName($p_name);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        if ($p_localFilePath != '') {
            $res = $this->setRawMediaData($p_localFilePath);
        } else {
            $res = $this->deleteFile();
        }
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        if ($p_metadata != '') {
            $res = $this->setMetadata($p_metadata, $p_mdataLoc);
        } else {
            $res = $this->md->delete();
        }
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        return TRUE;
    }


    /**
     * Increase access counter, create access token, insert access record.
     *
     * @param int $parent
     * 		parent token
     * @return array
     * 		array with: access URL, access token
     */
    public function accessRawMediaData($p_parent='0')
    {
        $realFname = $this->getRealFileName();
        $ext = $this->getFileExtension();
        $res = BasicStor::bsAccess($realFname, $ext, $this->gunid, 'access', $p_parent);
        if (PEAR::isError($res)) {
            return $res;
        }
        $resultArray =
            array('url'=>"file://{$res['fname']}", 'token'=>$res['token']);
        return $resultArray;
    }


    /**
     * Decrease access couter, delete access record.
     *
     * @param string $p_token
     * 		access token
     * @return boolean
     */
    public function releaseRawMediaData($p_token)
    {
        $res = BasicStor::bsRelease($p_token);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    /**
     * Replace media file only with new binary file
     *
     * @param string $p_localFilePath
     * 		local path to media file
     * @return TRUE|PEAR_Error
     */
    public function setRawMediaData($p_localFilePath)
    {
        $res = $this->replaceFile($p_localFilePath);
        if (PEAR::isError($res)) {
            return $res;
        }
        $mime = $this->getMime();
        if ($mime !== FALSE) {
            $res = $this->setMime($mime);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        $r = $this->md->regenerateXmlFile();
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    }


    /**
     * Replace metadata with new XML file
     *
     * @param string $p_metadata
     * 		local path to metadata XML file or XML string
     * @param string $p_mdataLoc
     * 		'file'|'string'
     * @param string $p_format
     * 		metadata format for validation
     *      ('audioclip' | 'playlist' | 'webstream' | NULL)
     *      (NULL = no validation)
     * @return boolean
     */
    public function setMetadata($p_metadata, $p_mdataLoc='file', $p_format=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("BEGIN");
        $res = $this->md->replace($p_metadata, $p_mdataLoc, $p_format);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        $r = $this->md->regenerateXmlFile();
        if (PEAR::isError($r)) {
            $CC_DBC->query("ROLLBACK");
            return $r;
        }
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    /**
     * Get metadata as XML string
     *
     * @return XML string
     * @see MetaData
     */
    public function getMetadata()
    {
        return $this->md->getMetadata();
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
            ." WHERE gunid=x'{$this->gunid}'::bigint";
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
            ." WHERE gunid=x'{$this->gunid}'::bigint";
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
            ." WHERE gunid=x'{$this->gunid}'::bigint";
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
            ." WHERE gunid=x'{$this->gunid}'::bigint";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->md5 = $p_md5sum;
        return TRUE;
    }


    /**
     * Delete stored virtual file
     *
     * @param boolean $p_deleteFile
     * @see MetaData
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
        }
        $res = $this->md->delete();
        if (PEAR::isError($res)) {
            return $res;
        }
        $sql = "SELECT to_hex(token)as token, ext "
            ." FROM ".$CC_CONFIG['accessTable']
            ." WHERE gunid=x'{$this->gunid}'::bigint";
        $tokens = $CC_DBC->getAll($sql);
        if (is_array($tokens)) {
            foreach ($tokens as $i => $item) {
                $file = $this->_getAccessFileName($item['token'], $item['ext']);
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        $sql = "DELETE FROM ".$CC_CONFIG['accessTable']
            ." WHERE gunid=x'{$this->gunid}'::bigint";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['filesTable']
            ." WHERE gunid=x'{$this->gunid}'::bigint";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
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
            ." WHERE gunid=x'$p_gunid'::bigint";
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
            ." WHERE gunid=x'$p_playlistId'::bigint";
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
        $sql = "SELECT to_hex(gunid) "
            ." FROM ".$CC_CONFIG['filesTable']
            ." WHERE gunid=x'{$this->gunid}'::bigint";
        $indb = $CC_DBC->getRow($sql);
        if (PEAR::isError($indb)) {
            return $indb;
        }
        if (is_null($indb)) {
            return FALSE;
        }
        if (BasicStor::GetType($this->gunid) == 'audioclip') {
            return $this->existsFile();
        }
        return TRUE;
    }


    /**
     * Create new global unique id
     * @return string
     */
    public static function CreateGunid()
    {
        $ip = (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');
        $initString = microtime().$ip.rand()."org.mdlf.campcaster";
        $hash = md5($initString);
        // non-negative int8
        $hsd = substr($hash, 0, 1);
        $res = dechex(hexdec($hsd)>>1).substr($hash, 1, 15);
        return StoredFile::NormalizeGunid($res);
    }


    /**
     * Pad the gunid with zeros if it isnt 16 digits.
     *
     * @return string
     */
    public static function NormalizeGunid($p_gunid)
    {
        return str_pad($p_gunid, 16, "0", STR_PAD_LEFT);
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
    function getMime()
    {
        $a = $this->analyzeFile();
        if (PEAR::isError($a)) {
        	return $a;
        }
        if (isset($a['dc:format'])) {
        	return $a['dc:format'];
        }
        return '';
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
            ." WHERE gunid=x'$p_gunid'::bigint";
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
            ." WHERE gunid=x'$p_gunid'::bigint";
        return $CC_DBC->getOne($sql);
    }


    /**
     * Get and optionally create subdirectory in real filesystem for storing
     * raw media data.
     *
     * @return string
     */
    private function _getResDir()
    {
        global $CC_CONFIG, $CC_DBC;
        $resDir = $CC_CONFIG['storageDir']."/".substr($this->gunid, 0, 3);
        //$this->gb->debugLog("$resDir");
        // see Transport::_getResDir too for resDir name create code
        if (!is_dir($resDir)) {
            mkdir($resDir, 02775);
            chmod($resDir, 02775);
        }
        return $resDir;
    }


    /**
     * Get real filename of raw media data
     *
     * @return string
     */
    public function getRealFileName()
    {
        return $this->filepath;
    }


    /**
     * Get real filename of metadata file
     *
     * @return string
     * @see MetaData
     */
    public function getRealMetadataFileName()
    {
        return $this->md->getFileName();
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
        $token = StoredFile::NormalizeGunid($p_token);
        return $CC_CONFIG['accessDir']."/$p_token.$p_ext";
    }

} // class StoredFile
?>