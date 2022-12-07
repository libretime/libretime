<?php

declare(strict_types=1);

/**
 * @internal
 *
 * @coversNothing
 */
class StoredFileTest extends PHPUnit_TestCase
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function setup()
    {
    }

    public function testGetAudioMetadata()
    {
        $filePath = dirname(__FILE__) . '/ex1.mp3';
        $metadata = Metadata::LoadFromFile($filePath);
        if (($metadata['dc:description'] != 'Tmu sem tam videla ...')
            || ($metadata['audio']['dataformat'] != 'mp3')
            || ($metadata['dc:type'] != 'Speech')
        ) {
            $str = '  [dc:description] = ' . $metadata['dc:description'] . "\n"
                . '  [audio][dataformat] = ' . $metadata['audio']['dataformat'] . "\n"
                . '  [dc:type] = ' . $metadata['dc:type'] . "\n";
            $this->fail("Metadata has unexpected values:\n" . $str);
        }
        // var_dump($metadata);
        // $this->assertTrue(FALSE);
    }

    public function testDeleteAndPutFile()
    {
        $STORAGE_SERVER_PATH = dirname(__FILE__) . '/../../';
        $filePath = dirname(__FILE__) . '/ex1.mp3';

        // Delete any old data from previous tests
        $md5 = md5_file($filePath);
        $duplicate = Application_Model_StoredFile::RecallByMd5($md5);
        if ($duplicate) {
            $duplicate->delete();
        }

        // Test inserting a file by linking
        $values = [
            'filepath' => $filePath,
            'dc:description' => 'Unit test ' . time(),
        ];
        $storedFile = Application_Model_StoredFile::Insert($values, false);
        // var_dump($storedFile);
        $id = $storedFile->getId();
        if (!is_numeric($id)) {
            $this->fail('StoredFile not created correctly. id = ' . $id);

            return;
        }

        // Test loading metadata
        $f = new Application_Model_StoredFile();
        $f->__setGunid($storedFile->getGunid());
        $f->loadMetadata();
        if (!is_array($md = $f->getMetadata())) {
            $this->fail('Unable to load metadata.');

            return;
        }
        // var_dump($md);

        // Check if the length field has been set.
        $f2 = Application_Model_StoredFile::RecallByGunid($storedFile->getGunid());
        $m2 = $f2->getMetadata();
        if (!isset($m2['length']) || $m2['length'] == '00:00:00.000000') {
            $this->fail('Length not reporting correctly in metadata.');

            return;
        }
    }
}
