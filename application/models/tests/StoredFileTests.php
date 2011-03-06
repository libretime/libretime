<?php
require_once(dirname(__FILE__).'/../StoredFile.php');

$dsn = $CC_CONFIG['dsn'];
$CC_DBC = DB::connect($dsn, TRUE);
if (PEAR::isError($CC_DBC)) {
	echo "ERROR: ".$CC_DBC->getMessage()." ".$CC_DBC->getUserInfo()."\n";
	exit(1);
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

class StoredFileTest extends PHPUnit_TestCase {

    function __construct($name) {
        parent::__construct($name);
    }

    function setup() {
    }

    function testGetAudioMetadata() {
        $filePath = dirname(__FILE__)."/ex1.mp3";
        $metadata = Metadata::LoadFromFile($filePath);
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
        //$this->assertTrue(FALSE);
    }

    function testDeleteAndPutFile() {
        $STORAGE_SERVER_PATH = dirname(__FILE__)."/../../";
        $filePath = dirname(__FILE__)."/ex1.mp3";

        // Delete any old data from previous tests
        $md5 = md5_file($filePath);
        $duplicate = StoredFile::RecallByMd5($md5);
        if ($duplicate) {
          $duplicate->delete();
        }

        // Test inserting a file by linking
        $values = array("filepath" => $filePath,
                        "dc:description" => "Unit test ".time());
        $storedFile = StoredFile::Insert($values, false);
        if (PEAR::isError($storedFile)) {
          $this->fail("Failed to create StoredFile: ".$storedFile->getMessage());
          return;
        }
        //var_dump($storedFile);
        $id = $storedFile->getId();
        if (!is_numeric($id)) {
            $this->fail("StoredFile not created correctly. id = ".$id);
            return;
        }

        // Test loading metadata
        $f = new StoredFile();
        $f->__setGunid($storedFile->getGunid());
        $f->loadMetadata();
        if (!is_array($md = $f->getMetadata())) {
          $this->fail("Unable to load metadata.");
          return;
        }
        //var_dump($md);

        // Check if the length field has been set.
        $f2 = StoredFile::RecallByGunid($storedFile->getGunid());
        $m2 = $f2->getMetadata();
        if (!isset($m2["length"]) || $m2["length"] == "00:00:00.000000") {
          $this->fail("Length not reporting correctly in metadata.");
          return;
        }

    }

}
