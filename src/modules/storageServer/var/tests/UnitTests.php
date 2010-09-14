<?php
require_once(dirname(__FILE__).'/../conf.php');
require_once('PHPUnit.php');
require_once('DB.php');
require_once(dirname(__FILE__).'/../StoredFile.php');
require_once(dirname(__FILE__).'/../BasicStor.php');
require_once(dirname(__FILE__).'/../GreenBox.php');

$dsn = $CC_CONFIG['dsn'];
$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

class BasicStorTest extends PHPUnit_TestCase {

    private $greenbox;

    function __construct($name) {
        parent::__construct($name);
    }

    function setup() {
        $this->greenbox = new GreenBox();
    }

    function testGetAudioMetadata() {
        $filePath = dirname(__FILE__)."/ex1.mp3";
        $metadata = camp_get_audio_metadata($filePath);
        if (PEAR::isError($metadata)) {
            $this->fail($metadata->getMessage());
            return;
        }
        if (($metadata["dc:description"] != "Tmu sem tam videla ...")
            || ($metadata["audio"]["dataformat"] != "mp3")
            || ($metadata["dc:type"] != "Speech")) {
            $str = "  [dc:description] = " . $metadata["dc:description"] ."\n"
							   . "  [audio][dataformat] = " . $metadata["audio"]["dataformat"]."\n"
							   . "  [dc:type] = ".$metadata["dc:type"]."\n";
            $this->fail("Metadata has unexpected values:\n".$str);
        }
        //var_dump($metadata);
    }

    function testPutFile() {
        $STORAGE_SERVER_PATH = dirname(__FILE__)."/../../";
        $filePath = dirname(__FILE__)."/ex1.mp3";
        $md5sum = md5_file($filePath);
        $metadata = camp_get_audio_metadata($filePath);
        if (PEAR::isError($metadata)) {
            $this->fail($metadata->getMessage());
            return;
        }
        // bsSetMetadataBatch doesnt like these values
        unset($metadata['audio']);
        unset($metadata['playtime_seconds']);
        $values = array(
            "filename" => $metadata['ls:filename'],
            "filepath" => $filePath,
            "metadata" => "$STORAGE_SERVER_PATH/var/emptyMdata.xml",
            "gunid" => NULL,
            "filetype" => "audioclip",
            "md5" => $md5sum,
            "mime" => $metadata['dc:format']
        );
        $storedFile = $this->greenbox->bsPutFile($values, false);
        $this->assertFalse(PEAR::isError($storedFile));
        $id = $storedFile->getId();
        if (!is_numeric($id)) {
            $this->fail("StoredFile not created correctly. id = ".$id);
            return;
        }

        $r = $this->greenbox->bsSetMetadataBatch($storedFile, $metadata);
        if (PEAR::isError($r)) {
        	$this->fail($r->getMessage());
        	//echo var_export($metadata)."\n";
        	return;
        }
    }


}
?>