<?php 
class Application_Service_ShowInstanceService
{
    private $service_show;
    const NO_REPEAT = -1;
    const REPEAT_WEEKLY = 0;
    const REPEAT_BI_WEEKLY = 1;
    const REPEAT_MONTHLY_MONTHLY = 2;
    const REPEAT_MONTHLY_WEEKLY = 3;

    public function __construct()
    {
        $this->service_show = new Application_Service_ShowService();
    }

    /**
     * 
     * Receives a cc_show id and determines whether to create a 
     * single show_instance or repeating show instances
     */
    public function createShowInstances($showId)
    {
        $populateUntil = $this->service_show->getPopulateShowUntilDateTIme();

        $showDays = $this->service_show->getShowDays($showId);
        foreach ($showDays as $day) {
            switch ($day["repeat_type"]) {
                case self::NO_REPEAT:
                    $this->createNonRepeatingShowInstance($day, $populateUntil);
                    break;
                case self::REPEAT_WEEKLY:
                    $this->createRepeatingShowInstances($day, $populateUntil, "P7D");
                    break;
                case self::REPEAT_BI_WEEKLY:
                    $this->createRepeatingShowInstances($day, $populateUntil, "P14D");
                    break;
                case self::REPEAT_MONTHLY_MONTHLY:
                    $this->createRepeatingShowInstances($day, $populateUntil, "P1M");
                    break;
                case self::REPEAT_MONTHLY_WEEKLY:
                    // do something here
                    break;
            }
        }
    }

    /**
     * 
     * Enter description here ...
     * @param $showDay
     * @param $populateUntil
     */
    private function createNonRepeatingShowInstance($showDay, $populateUntil)
    {
        $start = $showDay["first_show"]." ".$showDay["start_time"];

        list($utcStartDateTime, $utcEndDateTime) = $this->service_show->createUTCStartEndDateTime(
            $start, $showDay["duration"], $showDay["timezone"]);

        if ($utcStartDateTime->getTimestamp() < $populateUntil->getTimestamp()) {
            $currentUtcTimestamp = gmdate("Y-m-d H:i:s");

            $ccShowInstance = new CcShowInstances();
            if ($ccShowInstance->getTimestamp() > $currentUtcTimestamp) {
                $ccShowInstance->setDbShowId($showDay["show_id"]);
                $ccShowInstance->setDbStarts($utcStartDateTime);
                $ccShowInstance->setDbEnds($utcEndDateTime);
                $ccShowInstance->setDbRecord($showDay["record"]);
                $ccShowInstance->save();
            }
        }
    }

    /**
     * 
     * Enter description here ...
     * @param $showDay
     * @param $populateUntil
     * @param $repeatInterval
     */
    private function createRepeatingShowInstances($showDay, $populateUntil, $repeatInterval)
    {
        Logging::info("repeating");
    }
}