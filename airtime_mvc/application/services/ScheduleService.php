<?php
class Application_Service_ScheduleService
{
    /**
     * 
     * Enter description here ...
     * @param array $instanceIds
     */
    public static function updateScheduleStartTime($instanceIds, $diff)
    {
        if (count($instanceIds) > 0 && $diff != 0) {
            $showIdList = implode(",", $instanceIds);
            $sql = <<<SQL
UPDATE cc_schedule
SET starts = starts + diff1::INTERVAL,
    ends = ends + diff2::INTERVAL
WHERE instance_id IN :showIds
SQL;

            Application_Common_Database::prepareAndExecute($sql,
                array(':diff1' => $diff, ':diff2' => $diff, 
                    ':showIds' => $showIdList),
                'execute');
        }
    }
}