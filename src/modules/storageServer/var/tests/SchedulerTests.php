<?php
require_once(dirname(__FILE__)."/../Schedule.php");

class SchedulerTests extends PHPUnit_TestCase {

    private $groupIdCreated;
    private $storedFileId;

    function setup() {
      $this->storedFileId = '192';
    }

    function testDateToId() {
      $dateStr = "2006-04-02 10:20:08.123456";
      $id = ScheduleItem::dateToId($dateStr);
      $expected = "20060402102008123";
      if ($id != $expected) {
        $this->fail("Did not convert date to ID correctly #1: $id != $expected");
      }

      $dateStr = "2006-04-02 10:20:08";
      $id = ScheduleItem::dateToId($dateStr);
      $expected = "20060402102008000";
      if ($id != $expected) {
        $this->fail("Did not convert date to ID correctly #2: $id != $expected");
      }
    }

    function testAddAndRemove() {
      $i = new ScheduleItem();
      $this->groupIdCreated = $i->add('2010-10-10 01:30:23', $this->storedFileId);
      if (!is_numeric($this->groupIdCreated)) {
        $this->fail("Failed to create scheduled item.");
      }

      $i = new ScheduleItem($this->groupIdCreated);
      $result = $i->remove();
      if ($result != 1) {
        $this->fail("Did not remove item.");
      }
    }

    function testIsScheduleEmptyInRange() {
      $i = new ScheduleItem();
      $this->groupIdCreated = $i->add('2011-10-10 01:30:23', $this->storedFileId);
      if (Schedule::isScheduleEmptyInRange('2011-10-10 01:30:23', '00:00:01.432153')) {
        $this->fail("Reporting empty schedule when it isnt.");
      }
      $i->remove();
      if (!Schedule::isScheduleEmptyInRange('2011-10-10 01:30:23', '00:00:01.432153')) {
        $this->fail("Reporting booked schedule when it isnt.");
      }
    }

    function testGetItems() {
      $i1 = new ScheduleItem();
      $groupId1 = $i1->add('2008-01-01 12:00:00.000', $this->storedFileId);
      $i2 = new ScheduleItem();
      $i2->addAfter($groupId1, $this->storedFileId);
      $items = Schedule::GetItems("2008-01-01", "2008-01-02");
      if (count($items) != 2) {
        $this->fail("Wrong number of items returned.");
        return;
      }
      $i1->remove();
      $i2->remove();
    }
}

?>