<?php

declare(strict_types=1);

/**
 * @internal
 *
 * @coversNothing
 */
class SchedulerExportTests extends PHPUnit_TestCase
{
    public function setup()
    {
        global $CC_CONFIG;
        $con = Propel::getConnection();

        // Clear the files table
        $sql = 'DELETE FROM ' . $CC_CONFIG['filesTable'];
        $con->exec($sql);

        // Add a file
        $values = ['filepath' => dirname(__FILE__) . '/test10001.mp3'];
        $this->storedFile = Application_Model_StoredFile::Insert($values, false);

        // Add a file
        $values = ['filepath' => dirname(__FILE__) . '/test10002.mp3'];
        $this->storedFile2 = Application_Model_StoredFile::Insert($values, false);

        // Clear the schedule table
        $sql = 'DELETE FROM ' . $CC_CONFIG['scheduleTable'];
        $con->exec($sql);

        // Create a playlist
        $playlist = new Application_Model_Playlist();
        $playlist->create('Scheduler Unit Test');
        $result = $playlist->addAudioClip($this->storedFile->getId());
        $result = $playlist->addAudioClip($this->storedFile2->getId());
        $result = $playlist->addAudioClip($this->storedFile2->getId());

        // Schedule it
        $i = new Application_Model_ScheduleGroup();
        $this->groupIdCreated = $i->add('2010-11-11 01:30:23', null, $playlist->getId());
    }

    public function testExport()
    {
        echo Application_Model_Schedule::ExportRangeAsJson('2010-01-01 00:00:00', '2011-01-01 00:00:00');
    }
}
