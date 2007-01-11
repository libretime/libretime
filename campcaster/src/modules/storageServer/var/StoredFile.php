<?php
require_once("MetaData.php");
require_once(dirname(__FILE__)."/../../getid3/var/getid3.php");

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
        if (function_exists('iconv') && ($p_inputEncoding != $outputEncoding) ) {
            $data = @iconv($p_inputEncoding, $outputEncoding, $data);
            if ($data === FALSE) {
                echo "Warning: convert $key data to unicode failed\n";
                $data = $p_val;  // fallback
            }
        }
        $p_mdata[$p_key] = trim($data);
    }
}


/**
 * Return an array with the given audio file's ID3 tags.  The keys in the
 * array can be:
 * <pre>
 * 		dc:format
 * 		ls:bitrate
 * 		dcterms:extent
 * 		dc:title
 * 		dc:creator
 * 		dc:source
 * 		ls:encoded_by
 * 		ls:track_num
 * 		ls:genre
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
 * @return array/PEAR_Error
 */
function camp_get_audio_metadata($p_filename, $p_testonly = false)
{
    $getID3 = new getID3();
    $infoFromFile = $getID3->analyze($p_filename);
    //echo "\n".var_export($infoFromFile)."\n"; exit;
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
	    'dc:format'     => array(
	        array('path'=>"['mime_type']", 'ignoreEnc'=>TRUE),
	    ),
	    'ls:bitrate'    => array(
	        array('path'=>"['bitrate']", 'ignoreEnc'=>TRUE),
	    ),
	    'dcterms:extent'=> array(
	        array('path'=>"['playtime_seconds']", 'ignoreEnc'=>TRUE),
	    ),
	    'dc:title'	    => array(
	        array('path'=>"['id3v2']['comments']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TIT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v1']", 'dataPath'=>"['title']", 'encPath'=>"['encoding']"),
	        array('path'=>"['ogg']['comments']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['tags']['vorbiscomment']['title']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	    ),
	    'dc:creator'	=> array(
	        array('path'=>"['id3v2']['comments']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TPE1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TP1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v1']", 'dataPath'=>"['artist']", 'encPath'=>"['encoding']"),
	        array('path'=>"['ogg']['comments']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['tags']['vorbiscomment']['artist']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	    ),
	    'dc:source'	    => array(
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
	    'ls:track_num'	=> array(
	        array('path'=>"['id3v2']['TRCK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TRK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['ogg']['comments']['tracknumber']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['tags']['vorbiscomment']['tracknumber']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	    ),
	    'ls:genre'	    => array(
	        array('path'=>"['id3v1']", 'dataPath'=>"['genre']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['TCON'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
	        array('path'=>"['id3v2']['comments']['content_type']", 'dataPath'=>"[0]", 'ignoreEnc'=>TRUE),
	        array('path'=>"['ogg']['comments']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['tags']['vorbiscomment']['genre']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	    ),
	    'ls:channels'	=> array(
	        array('path'=>"['audio']['channels']", 'ignoreEnc'=>TRUE),
	    ),
	    'ls:year'	    => array(
	    	array('path'=>"['comments']['date']"),
	        array('path'=>"['ogg']['comments']['date']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	        array('path'=>"['tags']['vorbiscomment']['date']", 'dataPath'=>"[0]", 'encPath'=>"['encoding']"),
	    ),
	    'ls:filename'	=> array(
	        array('path'=>"['filename']"),
	    ),
	/*
	    'xx:fileformat' => array(array('path'=>"['fileformat']")),
	    'xx:filesize'   => array(array('path'=>"['filesize']")),
	    'xx:dataformat' => array(array('path'=>"['audio']['dataformat']")),
	    'xx:sample_rate'=> array(array('path'=>"['audio']['sample_rate']")),
	*/
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
        if ($p_testonly) {
        	echo "$key\n";
        }
        foreach ($getid3keys as $getid3key) {
            $path = $getid3key["path"];
            $ignoreEnc = isset($getid3key["ignoreEnc"])?$getid3key["ignoreEnc"]:FALSE;
            $dataPath = isset($getid3key["dataPath"])?$getid3key["dataPath"]:"";
            $encPath = isset($getid3key["encPath"])?$getid3key["encPath"]:"";
            $enc = "UTF-8";

            $vn = "\$infoFromFile$path$dataPath";
            if ($p_testonly) {
            	echo "   $vn   ->   ";
            }
            eval("\$vnFl = isset($vn);");
            if ($vnFl) {
                eval("\$data = $vn;");
                if ($p_testonly) {
                	echo "$data\n";
                }
                if (!$ignoreEnc && $encPath != "") {
                    $encVn = "\$infoFromFile$path$encPath";
                    eval("\$encVnFl = isset($encVn);");
                    if ($encVnFl) {
                    	eval("\$enc = $encVn;");
                    }
                }
                if ($p_testonly) {
                	echo "        ENC=$enc\n";
                }
                camp_add_metadata($mdata, $key, $data, $enc);
		        if ($key == $titleKey) {
		        	$titleHaveSet = TRUE;
		        }
                break;
            } else {
                if ($p_testonly) {
                	echo "\n";
                }
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
     * @param string $gunid
     *  	globally unique id of file
     */
    public function __construct($gunid=NULL)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $this->gunid = $gunid;
        if (is_null($this->gunid)) {
            $this->gunid = StoredFile::CreateGunid();
        }
        $this->resDir = $this->_getResDir($this->gunid);
        $this->filepath = $this->makeFileName();
        $this->exists = is_file($this->filepath) && is_readable($this->filepath);
        $this->md = new MetaData($this->gunid, $this->resDir);
    }


    /* ========= 'factory' methods - should be called to construct StoredFile */
    /**
     *  Create instance of StoredFile object and insert new file
     *
     *  @param int $oid
     * 		local object id in the tree
     *  @param string $filename
     * 		name of new file
     *  @param string $localFilePath
     * 		local path to media file
     *  @param string $metadata
     * 		local path to metadata XML file or XML string
     *  @param string $mdataLoc
     * 		'file'|'string'
     *  @param global $gunid
     * 		unique id - for insert file with gunid
     *  @param string $ftype
     * 		internal file type
     *  @param boolean $copyMedia
     * 		copy the media file if true, make symlink if false
     *  @return StoredFile
     */
    public static function insert($oid, $filename, $localFilePath='',
        $metadata='', $mdataLoc='file', $gunid=NULL, $ftype=NULL, $copyMedia=TRUE)
    {
        global $CC_CONFIG, $CC_DBC;
        $storedFile = new StoredFile(($gunid ? $gunid : NULL));
        if (PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $storedFile->name = $filename;
        $storedFile->id = $oid;
        $storedFile->mime = "unknown";
        $emptyState = TRUE;
        if ($storedFile->name == '') {
            $storedFile->name = $storedFile->gunid;
        }
        $storedFile->exists = FALSE;
        if (file_exists($localFilePath)) {
            $storedFile->exists = TRUE;
            $md5 = md5_file($localFilePath);
        }
        $escapedName = pg_escape_string($filename);
        $escapedFtype = pg_escape_string($ftype);
        $CC_DBC->query("BEGIN");
        $sql = "INSERT INTO ".$CC_CONFIG['filesTable']
                ."(id, name, gunid, mime, state, ftype, mtime, md5)"
                ."VALUES ('$oid', '{$escapedName}', x'{$storedFile->gunid}'::bigint,
                 '{$storedFile->mime}', 'incomplete', '$escapedFtype', now(), '$md5')";
        $res = $CC_DBC->query($sql);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        // --- metadata insert:
        if (is_null($metadata) || ($metadata == '') ) {
            $metadata = dirname(__FILE__).'/emptyMdata.xml';
            $mdataLoc = 'file';
        } else {
            $emptyState = FALSE;
        }
        if ( ($mdataLoc == 'file') && !file_exists($metadata)) {
            return PEAR::raiseError("StoredFile::insert: ".
                "metadata file not found ($metadata)");
        }
        $res = $storedFile->md->insert($metadata, $mdataLoc, $ftype);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        // --- media file insert:
        if ($localFilePath != '') {
            if (!file_exists($localFilePath)) {
                return PEAR::raiseError("StoredFile::insert: ".
                    "media file not found ($localFilePath)");
            }
            $res = $storedFile->addFile($localFilePath, $copyMedia);
            if (PEAR::isError($res)) {
                $CC_DBC->query("ROLLBACK");
                return $res;
            }
            $mime = $storedFile->getMime();
            if ($mime !== FALSE) {
                $res = $storedFile->setMime($mime);
                if (PEAR::isError($res)) {
                    $CC_DBC->query("ROLLBACK");
                    return $res;
                }
            }
            $emptyState = FALSE;
        }
        if (!$emptyState) {
            $res = $storedFile->setState('ready');
            if (PEAR::isError($res)) {
                $CC_DBC->query("ROLLBACK");
                return $res;
            }
        }
        $res = $CC_DBC->query("COMMIT");
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        return $storedFile;
    }


    /**
     * Create instance of StoreFile object and recall existing file.<br>
     * Should be supplied with oid OR gunid - not both.
     *
     * @param int $oid
     * 		local object id in the tree
     * @param string $gunid
     * 		global unique id of file
     * @return StoredFile
     */
    public static function Recall($oid='', $gunid='')
    {
        global $CC_DBC;
        global $CC_CONFIG;
        $cond = ($oid != ''
            ? "id='".intval($oid)."'"
            : "gunid=x'$gunid'::bigint"
        );
        $row = $CC_DBC->getRow("
            SELECT id, to_hex(gunid)as gunid, name, mime, ftype, state, currentlyaccessing, editedby, mtime, md5
            FROM ".$CC_CONFIG['filesTable']." WHERE $cond");
        if (PEAR::isError($row)) {
            return $row;
        }
        if (is_null($row)) {
            $r =& PEAR::raiseError(
                "StoredFile::recall: fileobj not exist ($oid/$gunid)",
                GBERR_FOBJNEX
            );
            return $r;
        }
        $gunid = StoredFile::NormalizeGunid($row['gunid']);
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
        $storedFile->exists = TRUE;
        $storedFile->md->setFormat($row['ftype']);
        return $storedFile;
    }


    /**
     * Create instance of StoreFile object and recall existing file
     * by gunid.
     *
     * @param string $gunid
     * 		global unique id of file
     * @return StoredFile
     */
    public static function RecallByGunid($gunid='')
    {
        return StoredFile::Recall('', $gunid);
    }


    /**
     * Create instance of StoreFile object and recall existing file
     * by access token.
     *
     * @param string $token
     * 		access token
     * @return StoredFile
     */
    public static function RecallByToken($token)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = $CC_DBC->getOne("
            SELECT to_hex(gunid) as gunid
            FROM ".$CC_CONFIG['accessTable']."
            WHERE token=x'$token'::bigint");
        if (PEAR::isError($gunid)) {
            return $gunid;
        }
        if (is_null($gunid)) {
            return PEAR::raiseError(
            "StoredFile::RecallByToken: invalid token ($token)", GBERR_AOBJNEX);
        }
        $gunid = StoredFile::NormalizeGunid($gunid);
        return StoredFile::Recall('', $gunid);
    }


    /**
     * Check if the MD5 value already exists.
     *
     * @param string $p_md5sum
     * @return StoredFile|FALSE|PEAR_Error
     */
    public static function RecallByMd5($p_md5sum)
    {
        global $CC_CONFIG, $CC_DBC;
        $gunid = $CC_DBC->getOne(
            "SELECT to_hex(gunid) as gunid
            FROM ".$CC_CONFIG['filesTable']."
            WHERE md5='$p_md5sum'");
        if (PEAR::isError($gunid)) {
            return $gunid;
        }
        if ($gunid) {
            $gunid = StoredFile::NormalizeGunid($gunid);
            return StoredFile::Recall('', $gunid);
        } else {
            return FALSE;
        }
    }


    /**
     * Insert media file to filesystem
     *
     * @param string $localFilePath
     * 		local path
     * @param boolean $copyMedia
     * 		copy the media file if true, make symlink if false
     * @return TRUE|PEAR_Error
     */
    public function addFile($localFilePath, $copyMedia=TRUE)
    {
        if ($this->exists) {
        	return FALSE;
        }
        // for files downloaded from archive:
        if ($localFilePath == $this->filepath) {
            $this->exists = TRUE;
            return TRUE;
        }
        umask(0002);
        if ($copyMedia) {
            $r = @copy($localFilePath, $this->filepath);
        } else {
            $r = @symlink($localFilePath, $this->filepath);
        }
        if ( $r ) {
            $this->exists = TRUE;
            return TRUE;
        } else {
            //@unlink($this->fname);    // maybe useless
            $this->exists = FALSE;
            return PEAR::raiseError(
                "StoredFile::addFile: file save failed".
                " ($localFilePath, {$this->filepath})",GBERR_FILEIO
            );
        }
    }


    /**
     * Delete and insert media file
     *
     * @param string $localFilePath
     *      local path
     * @return TRUE|PEAR_Error
     */
    public function replaceFile($localFilePath)
    {
        // Dont do anything if the source and destination files are
        // the same.
        if ($this->name == $localFilePath) {
            return TRUE;
        }

        if ($this->exists) {
        	$r = $this->deleteFile();
            if (PEAR::isError($r)) {
            	return $r;
            }
        }
        return $this->addFile($localFilePath);
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
     * Contruct filepath of media file
     *
     * @return string
     */
    public function makeFileName()
    {
        return "{$this->resDir}/{$this->gunid}";
    }


    /**
     * Create instance of StoredFile object and make copy of existing file
     *
     * @param StoredFile $src
     * 		source object
     * @param int $nid
     * 		new local id
     * @return StoredFile
     */
    public static function CopyOf(&$src, $nid)
    {
        $storedFile = StoredFile::insert($nid, $src->name, $src->getRealFileName(),
            '', '', NULL, BasicStor::GetType($src->gunid));
        if (PEAR::isError($storedFile)) {
            return $storedFile;
        }
        $storedFile->md->replace($src->md->getMetadata(), 'string');
        return $storedFile;
    }


    /**
     * Replace existing file with new data.
     *
     * @param int $oid
     * 		local id
     * @param string $name
     * 		name of file
     * @param string $localFilePath
     * 		local path to media file
     * @param string $metadata
     * 		local path to metadata XML file or XML string
     * @param string $mdataLoc
     * 		'file'|'string'
     * @return TRUE|PEAR_Error
     */
    public function replace($oid, $name, $localFilePath='', $metadata='',
        $mdataLoc='file')
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("BEGIN");
        $res = $this->setName($name);
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        if ($localFilePath != '') {
            $res = $this->setRawMediaData($localFilePath);
        } else {
            $res = $this->deleteFile();
        }
        if (PEAR::isError($res)) {
            $CC_DBC->query("ROLLBACK");
            return $res;
        }
        if ($metadata != '') {
            $res = $this->setMetadata($metadata, $mdataLoc);
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
    public function accessRawMediaData($parent='0')
    {
        $realFname = $this->getRealFileName();
        $ext = $this->getFileExtension();
        $res = BasicStor::bsAccess($realFname, $ext, $this->gunid, 'access', $parent);
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
     * @param string $token
     * 		access token
     * @return boolean
     */
    public function releaseRawMediaData($token)
    {
        $res = BasicStor::bsRelease($token);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    /**
     * Replace media file only with new binary file
     *
     * @param string $localFilePath
     * 		local path to media file
     * @return TRUE|PEAR_Error
     */
    public function setRawMediaData($localFilePath)
    {
        $res = $this->replaceFile($localFilePath);
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
     * @param string $metadata
     * 		local path to metadata XML file or XML string
     * @param string $mdataLoc
     * 		'file'|'string'
     * @param string $format
     * 		metadata format for validation
     *      ('audioclip' | 'playlist' | 'webstream' | NULL)
     *      (NULL = no validation)
     * @return boolean
     */
    public function setMetadata($metadata, $mdataLoc='file', $format=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("BEGIN");
        $res = $this->md->replace($metadata, $mdataLoc, $format);
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
     * @param string $newname
     * @return TRUE|PEAR_Error
     */
    public function setName($p_newname)
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedName = pg_escape_string($p_newname);
        $res = $CC_DBC->query("
            UPDATE ".$CC_CONFIG['filesTable']." SET name='$escapedName', mtime=now()
            WHERE gunid=x'{$this->gunid}'::bigint");
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->name = $p_newname;
        return TRUE;
    }


    /**
     * Set state of virtual file
     *
     * @param string $state
     * 		'empty'|'incomplete'|'ready'|'edited'
     * @param int $editedby
     * 		 user id | 'NULL' for clear editedBy field
     * @return TRUE|PEAR_Error
     */
    public function setState($p_state, $p_editedby=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $escapedState = pg_escape_string($p_state);
        $eb = (!is_null($p_editedby) ? ", editedBy=$p_editedby" : '');
        $res = $CC_DBC->query("
            UPDATE ".$CC_CONFIG['filesTable']."
            SET state='$escapedState'$eb, mtime=now()
            WHERE gunid=x'{$this->gunid}'::bigint");
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
     * @param string $mime
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
        $res = $CC_DBC->query(
            "UPDATE ".$CC_CONFIG['filesTable']." SET mime='$escapedMime', mtime=now()
            WHERE gunid=x'{$this->gunid}'::bigint");
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
        $res = $CC_DBC->query(
            "UPDATE ".$CC_CONFIG['filesTable']." SET md5='$escapedMd5', mtime=now()
            WHERE gunid=x'{$this->gunid}'::bigint");
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->md5 = $p_md5sum;
        return TRUE;
    }


    /**
     * Delete stored virtual file
     *
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
        $tokens = $CC_DBC->getAll("
            SELECT to_hex(token)as token, ext FROM ".$CC_CONFIG['accessTable']."
            WHERE gunid=x'{$this->gunid}'::bigint");
        if (is_array($tokens)) {
            foreach ($tokens as $i => $item) {
                $file = $this->_getAccessFileName($item['token'], $item['ext']);
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        $res = $CC_DBC->query(
            "DELETE FROM ".$CC_CONFIG['accessTable']."
            WHERE gunid=x'{$this->gunid}'::bigint");
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = $CC_DBC->query(
            "DELETE FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'{$this->gunid}'::bigint");
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    }


    /**
     * Returns true if virtual file is currently in use.<br>
     * Static or dynamic call is possible.
     *
     * @param string $gunid
     * 		optional (for static call), global unique id
     * @return boolean|PEAR_Error
     */
    public function isAccessed($gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($gunid)) {
            return ($this->currentlyaccessing > 0);
        }
        $ca = $CC_DBC->getOne("
            SELECT currentlyAccessing FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'$gunid'::bigint");
        if (is_null($ca)) {
            return PEAR::raiseError(
                "StoredFile::isAccessed: invalid gunid ($gunid)",
                GBERR_FOBJNEX
            );
        }
        return ($ca > 0);
    }


    /**
     * Returns true if virtual file is edited
     *
     * @param string $playlistId
     * 		playlist global unique ID
     * @return boolean
     */
    public function isEdited($playlistId=NULL)
    {
        if (is_null($playlistId)) {
            return ($this->state == 'edited');
        }
        $state = $this->getState($playlistId);
        if ($state != 'edited') {
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Returns id of user editing playlist
     *
     * @param string $playlistId
     * 		playlist global unique ID
     * @return int|null|PEAR_Error
     * 		id of user editing it
     */
    public function isEditedBy($playlistId=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($playlistId)) {
            $playlistId = $this->gunid;
        }
        $ca = $CC_DBC->getOne("
            SELECT editedBy FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'$playlistId'::bigint
        ");
        if (PEAR::isError($ca)) {
            return $ca;
        }
        if (is_null($ca)) {
            return $ca;
        }
        return intval($ca);
    }


    /**
     * Returns local id of virtual file
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Returns true if raw media file exists
     * @return boolean|PEAR_Error
     */
    public function exists()
    {
        global $CC_CONFIG, $CC_DBC;
        $indb = $CC_DBC->getRow(
            "SELECT to_hex(gunid) FROM ".$CC_CONFIG['filesTable']
            ." WHERE gunid=x'{$this->gunid}'::bigint");
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
    public static function NormalizeGunid($gunid)
    {
        return str_pad($gunid, 16, "0", STR_PAD_LEFT);
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
     * @param string $gunid
     * 		global unique id of file
     * @return string
     * 		see install()
     */
    public function getState($gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($gunid)) {
            return $this->state;
        }
        return $CC_DBC->getOne("
            SELECT state FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'$gunid'::bigint
        ");
    }


    /**
     * Get mnemonic file name
     *
     * @param string $gunid
     * 		global unique id of file
     * @return string
     */
    public function getName($gunid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($gunid)) {
            return $this->name;
        }
        return $CC_DBC->getOne("
            SELECT name FROM ".$CC_CONFIG['filesTable']."
            WHERE gunid=x'$gunid'::bigint
        ");
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
    private function _getAccessFileName($token, $ext='EXT')
    {
        global $CC_CONFIG;
        $token = StoredFile::NormalizeGunid($token);
        return $CC_CONFIG['accessDir']."/$token.$ext";
    }

} // class StoredFile
?>