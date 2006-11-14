<?php

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
    #echo "$key($iEnc): $val\n";
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
    #if(!$infoFromFile['fileformat']){ echo "???\n"; continue; }
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
	    //'dc:publisher'	=> array(array('path'=>"['comments']['label']")),
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
 * RawMediaData class
 *
 * File storage support class
 * Store media files in real filesystem and handle access to them.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see StoredFile
 */
class RawMediaData {

    /**
     * Constructor
     *
     * @param string $gunid
     * 		global unique id
     * @param string $resDir
     * 		resource directory
     */
    public function __construct($gunid, $resDir)
    {
        $this->gunid  = $gunid;
        $this->resDir = $resDir;
        $this->fname  = $this->makeFname();
        $this->exists =
            is_file($this->fname) &&
            is_readable($this->fname)
        ;
    }


    /**
     * Insert media file to filesystem
     *
     * @param string $mediaFileLP
     * 		local path
     * @return mixed
     * 		true or PEAR::error
     */
    function insert($mediaFileLP)
    {
        if ($this->exists) {
        	return FALSE;
        }
        // for files downloaded from archive:
        if ($mediaFileLP == $this->fname) {
            $this->exists = TRUE;
            return TRUE;
        }
        umask(0002);
        if (@copy($mediaFileLP, $this->fname)) {
            $this->exists = TRUE;
            return TRUE;
        } else {
            //@unlink($this->fname);    // maybe useless
            $this->exists = FALSE;
            return PEAR::raiseError(
                "RawMediaData::insert: file save failed".
                " ($mediaFileLP, {$this->fname})",GBERR_FILEIO
            );
        }
    }


    /**
     * Delete and insert media file
     *
     * @param string $mediaFileLP, local path
     * @return mixed
     * 		true or PEAR::error
     */
    function replace($mediaFileLP)
    {
        if ($this->exists) {
        	$r = $this->delete();
        } else {
        	$r = NULL;
        }
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $this->insert($mediaFileLP);
    }


    /**
     * Return true if file corresponding to the object exists
     *
     * @return boolean
     */
    function exists()
    {
        return $this->exists;
    }


    /**
     * Return filename
     *
     * @return string
     */
    function getFname()
    {
        return $this->fname;
    }


    /**
     * Delete media file from filesystem
     *
     * @return mixed
     * 		boolean or PEAR::error
     */
    function delete()
    {
        if (!$this->exists) {
        	return FALSE;
        }
        if (@unlink($this->fname)) {
            $this->exists = FALSE;
            return TRUE;
        } else {
            return PEAR::raiseError(
                "RawMediaData::delete: unlink failed ({$this->fname})",
                GBERR_FILEIO
            );
        }
        return $this->exists;
    }


    /**
     * Analyze media file with getid3 module
     *
     * @return array
     * 		hierarchical hasharray with information about media file
     */
    function analyze()
    {
        if (!$this->exists) {
        	return FALSE;
        }
        $ia = camp_get_audio_metadata($this->fname);
//        echo "<pre>";
//        $ia = camp_get_audio_metadata($this->fname, true);
//        print_r($ia);
//        exit;
        return $ia;
    }


    /**
     * Get mime-type returned by getid3 module
     *
     * @return string
     */
    function getMime()
    {
        $a = $this->analyze();
        if (PEAR::isError($a)) {
        	return $a;
        }
        if (isset($a['dc:format'])) {
        	return $a['dc:format'];
        }
        return '';
    }


    /**
     * Contruct filepath of media file
     *
     * @return string
     */
    function makeFname()
    {
        return "{$this->resDir}/{$this->gunid}";
    }


    /**
     * Test method
     *
     * @param string $testFname1
     * @param string $testFname2
     * @param string $accLinkFname
     * @return string
     */
    function test($testFname1, $testFname2, $accLinkFname)
    {
        $log = '';
        if ($this->exists()) {
            $log .= "---: exists: YES\n";
        } else {
            $log .= "---: exists: NO\n";
        }
        if (!($r = $this->delete())) {
            $log .= "---: delete: nothing to delete\n";
        }
        if (PEAR::isError($r)) {
            $log .= "ERR: ".$r->getMessage()."\n";
        }
        if ($r = $this->insert($testFname1)) {
            $log .= "---: insert: already exists\n";
        }
        if (PEAR::isError($r)) {
            $log .= "ERR: ".$r->getMessage()."\n";
        }
        if ($r = $this->replace($testFname2)) {
            $log .= "---: replace: already exists\n";
        }
        if (PEAR::isError($r)) {
            $log .= "ERR: ".$r->getMessage()."\n";
        }
        if ($this->exists()) {
            $log .= "---: exists: YES\n";
        } else {
            $log .= "---: exists: NO\n";
        }
        if (!$this->access($accLinkFname)) {
            $log .= "---: access: not exists\n";
        }
        if (($ft = filetype($accLinkFname)) == 'link') {
            if (($rl = readlink($accLinkFname)) != $this->fname) {
                $log .= "ERR: wrong target ($rl)\n";
            }
        } else {
            $log .= "ERR: wrong file type ($ft)\n";
        }
        if (!$this->release($accLinkFname)) {
            $log .= "---: access: not exists\n";
        }
        return $log;
    }

} // class RawMediaData
?>