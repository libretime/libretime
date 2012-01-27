<?php

class Application_Model_Scheduler {

    private $propSched;

    public function __construct($id = null) {

        if (is_null($id)) {
            $this->propSched = new CcSchedule();
        }
        else {
            $this->propSched = CcScheduleQuery::create()->findPK($id);
        }
    }

    /*
    public function findScheduledItems($starts, $ends) {

        CcScheduleQuery::create()
            ->filterByDbStarts(array('min' => $starts->format('Y-m-d H:i:s'), 'max' => $ends->format('Y-m-d H:i:s')))
            ->find();
    }
    */

    public function addScheduledItem($starts, $duration, $adjustSched = true) {

    }

    /*
     * @param DateTime $starts
     */
    public function updateScheduledItem($p_newStarts, $p_adjustSched = true) {

        $origStarts = $this->propSched->getDbStarts(null);

        $diff = $origStarts->diff($p_newStarts);

        //item is scheduled further in future
        if ($diff->format("%R") === "+") {

            CcScheduleQuery::create()
                ->filterByDbStarts($this->propSched->getDbStarts(), Criteria::GREATER_THAN)
                ->filterByDbId($this->propSched->getDbId(), Criteria::NOT_EQUAL)
                ->find();

        }
        //item has been scheduled earlier
        else {
            CcScheduleQuery::create()
                ->filterByDbStarts($this->propSched->getDbStarts(), Criteria::GREATER_THAN)
                ->filterByDbId($this->propSched->getDbId(), Criteria::NOT_EQUAL)
                ->find();
        }
    }

    public function removeScheduledItem($adjustSched = true) {

        if ($adjustSched === true) {
            $duration = $this->propSched->getDbEnds('U') - $this->propSched->getDbStarts('U');

            CcScheduleQuery::create()
                ->filterByDbInstanceId()
                ->filterByDbStarts()
                ->find();
        }

        $this->propSched->delete();
    }
}