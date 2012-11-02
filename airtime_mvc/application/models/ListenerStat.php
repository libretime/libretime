<?php
class Application_Model_ListenerStat
{
    public function __construct()
    {
    }
    
    public static function getDataPointsWithinRange($p_start, $p_end) {
        $sql = <<<SQL
SELECT cc_listener_count.ID, cc_timestamp.TIMESTAMP, cc_listener_count.LISTENER_COUNT
FROM cc_listener_count
INNER JOIN cc_timestamp ON (cc_listener_count.TIMESTAMP_ID=cc_timestamp.ID)
WHERE (cc_timestamp.TIMESTAMP>=:p1 AND cc_timestamp.TIMESTAMP<=:p2)
ORDER BY cc_timestamp.TIMESTAMP
SQL;
        $data = Application_Common_Database::prepareAndExecute($sql, array('p1'=>$p_start, 'p2'=>$p_end));
        
        return $data;
    }
}