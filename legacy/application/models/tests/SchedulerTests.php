<?php

/**
 * @internal
 *
 * @coversNothing
 */
class SchedulerTests extends PHPUnit_TestCase
{
    private $groupIdCreated;
    private $storedFile;
    private $storedFile2;

    public function setup()
    {
        global $CC_CONFIG;

        // Clear the files table
        // $sql = "DELETE FROM ".$CC_CONFIG["filesTable"];

        // Add a file
        $values = ['filepath' => dirname(__FILE__) . '/test10001.mp3'];
        $this->storedFile = Application_Model_StoredFile::Insert($values, false);

        // Add a file
        $values = ['filepath' => dirname(__FILE__) . '/test10002.mp3'];
        $this->storedFile2 = Application_Model_StoredFile::Insert($values, false);

        // Clear the schedule table
        // $sql = "DELETE FROM ".$CC_CONFIG["scheduleTable"];
    }

    public function testDateToId()
    {
        $dateStr = '2006-04-02 10:20:08.123456';
        $id = Application_Model_ScheduleGroup::dateToId($dateStr);
        $expected = '20060402102008123';
        if ($id != $expected) {
            $this->fail("Did not convert date to ID correctly #1: {$id} != {$expected}");
        }

        $dateStr = '2006-04-02 10:20:08';
        $id = Application_Model_ScheduleGroup::dateToId($dateStr);
        $expected = '20060402102008000';
        if ($id != $expected) {
            $this->fail("Did not convert date to ID correctly #2: {$id} != {$expected}");
        }
    }

    public function testAddAndRemoveAudioFile()
    {
        $i = new Application_Model_ScheduleGroup();
        $this->groupIdCreated = $i->add('2010-10-10 01:30:23', $this->storedFile->getId());

        $i = new Application_Model_ScheduleGroup($this->groupIdCreated);
        $result = $i->remove();
        if ($result != 1) {
            $this->fail('Did not remove item.');
        }
    }

    public function testAddAndRemovePlaylist()
    {
        // Create a playlist
        $playlist = new Application_Model_Playlist();
        $playlist->create('Scheduler Unit Test ' . uniqid());
        $result = $playlist->addAudioClip($this->storedFile->getId());
        $result = $playlist->addAudioClip($this->storedFile2->getId());
        $result = $playlist->addAudioClip($this->storedFile2->getId());

        $i = new Application_Model_ScheduleGroup();
        $this->groupIdCreated = $i->add('2010-11-11 01:30:23', null, $playlist->getId());
        $group = new Application_Model_ScheduleGroup($this->groupIdCreated);
        if ($group->count() != 3) {
            $this->fail('Wrong number of items added.');
        }
        $items = $group->getItems();
        if (!is_array($items) || ($items[1]['starts'] != '2010-11-11 01:30:34.231')) {
            $this->fail('Wrong start time for 2nd item.');
        }

        $result = $group->remove();
        if ($result != 1) {
            $this->fail('Did not remove item.');
        }

        Application_Model_Playlist::Delete($playlist->getId());
    }

    public function testIsScheduleEmptyInRange()
    {
        $i = new Application_Model_ScheduleGroup();
        $this->groupIdCreated = $i->add('2011-10-10 01:30:23', $this->storedFile->getId());
        if (Application_Model_Schedule::isScheduleEmptyInRange('2011-10-10 01:30:23', '00:00:12.555')) {
            $this->fail('Reporting empty schedule when it isnt.');

            return;
        }
        //    echo "groupid: ".$this->groupIdCreated."\n";
        $success = $i->remove();
        if ($success === false) {
            $this->fail('Failed to delete schedule group.');

            return;
        }
        if (!Application_Model_Schedule::isScheduleEmptyInRange('2011-10-10 01:30:23', '00:00:12.555')) {
            $this->fail('Reporting booked schedule when it isnt.');

            return;
        }
    }

    /*
        function testGetItems() {
            $i1 = new Application_Model_ScheduleGroup();
            $groupId1 = $i1->add('2008-01-01 12:00:00.000', $this->storedFile->getId());
            $i2 = new Application_Model_ScheduleGroup();
            $i2->addAfter($groupId1, $this->storedFile->getId());
            $items = Application_Model_Schedule::getItems("2008-01-01", "2008-01-02");
            if (count($items) != 2) {
                $this->fail("Wrong number of items returned.");
                return;
            }
            $i1->remove();
            $i2->remove();
        }
    */
}
