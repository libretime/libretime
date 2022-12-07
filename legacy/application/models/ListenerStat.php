<?php

declare(strict_types=1);

class Application_Model_ListenerStat
{
    public function __construct()
    {
    }

    public static function getDataPointsWithinRange($p_start, $p_end)
    {
        $sql = <<<'SQL'
SELECT mount_name, count(*)
    FROM cc_listener_count AS lc
    INNER JOIN cc_timestamp AS ts ON (lc.timestamp_id = ts.ID)
    INNER JOIN cc_mount_name AS mn ON (lc.mount_name_id = mn.ID)
WHERE (ts.timestamp >=:p1 AND ts.timestamp <=:p2)
group by mount_name
SQL;
        $data = Application_Common_Database::prepareAndExecute(
            $sql,
            ['p1' => $p_start, 'p2' => $p_end]
        );
        $out = [];

        foreach ($data as $d) {
            $jump = intval($d['count'] / 1000);
            $jump = max(1, $jump);
            $remainder = $jump == 1 ? 0 : 1;

            $sql = <<<'SQL'
SELECT *
FROM
    (SELECT lc.id, ts.timestamp, lc.listener_count, mn.mount_name,
        ROW_NUMBER() OVER (ORDER BY timestamp) as rownum
    FROM cc_listener_count AS lc
    INNER JOIN cc_timestamp AS ts ON (lc.timestamp_id = ts.ID)
    INNER JOIN cc_mount_name AS mn ON (lc.mount_name_id = mn.ID)
    WHERE (ts.timestamp >=:p1 AND ts.timestamp <= :p2) AND mount_name=:p3) as temp
WHERE (temp.rownum%:p4) = :p5;
SQL;
            $result = Application_Common_Database::prepareAndExecute(
                $sql,
                ['p1' => $p_start, 'p2' => $p_end, 'p3' => $d['mount_name'], 'p4' => $jump, 'p5' => $remainder]
            );

            $utcTimezone = new DateTimeZone('UTC');
            $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());

            foreach ($result as $r) {
                $t = new DateTime($r['timestamp'], $utcTimezone);
                $t->setTimezone($displayTimezone);
                // tricking javascript so it thinks the server timezone is in UTC
                $dt = new DateTime($t->format(DEFAULT_TIMESTAMP_FORMAT), $utcTimezone);

                $r['timestamp'] = $dt->format('U');
                $out[$r['mount_name']][] = $r;
            }
        }

        return $out;
    }

    // this will currently log the average number of listeners to a specific show during a certain range
    public static function getShowDataPointsWithinRange($p_start, $p_end, $show_id)
    {
        $showData = [];
        $ccShow = CcShowQuery::create()->findPk($show_id);
        $showName = $ccShow->getDbName();

        // this query selects all show instances that aired in this date range that match the show_id
        $sql = <<<'SQL'
SELECT id, starts, ends FROM cc_show_instances WHERE show_id =:p1
AND starts >=:p2 AND ends <=:p3
SQL;
        $data = Application_Common_Database::prepareAndExecute(
            $sql,
            ['p1' => $show_id, 'p2' => $p_start, 'p3' => $p_end]
        );
        foreach ($data as $d) {
            $sql = <<<'SQL'
SELECT timestamp, SUM(listener_count) AS listeners
    FROM cc_listener_count AS lc
    INNER JOIN cc_timestamp AS ts ON (lc.timestamp_id = ts.ID)
    INNER JOIN cc_mount_name AS mn ON (lc.mount_name_id = mn.ID)
WHERE (ts.timestamp >=:p1 AND ts.timestamp <=:p2)
GROUP BY timestamp
SQL;
            $data = Application_Common_Database::prepareAndExecute(
                $sql,
                ['p1' => $d['starts'], 'p2' => $d['ends']]
            );
            $utcTimezone = new DateTimeZone('UTC');
            $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
            if (count($data) > 0) {
                $t = new DateTime($data[0]['timestamp'], $utcTimezone);
                $t->setTimezone($displayTimezone);
                // tricking javascript so it thinks the server timezone is in UTC
                $average_listeners = array_sum(array_column($data, 'listeners')) / count($data);
                $max_num_listeners = max(array_column($data, 'listeners'));
                $entry = [
                    'show' => $showName, 'time' => $t->format('Y-m-d H:i:s'), 'average_number_of_listeners' => $average_listeners,
                    'maximum_number_of_listeners' => $max_num_listeners,
                ];
                array_push($showData, $entry);
            }
        }

        return $showData;
    }

    public static function getAllShowDataPointsWithinRange($p_start, $p_end)
    {
        // this query selects the id of all show instances that aired in this date range
        $all_show_data = [];
        $sql = <<<'SQL'
SELECT show_id FROM cc_show_instances
WHERE starts >=:p1 AND ends <=:p2
GROUP BY show_id
SQL;
        $data = Application_Common_Database::prepareAndExecute(
            $sql,
            ['p1' => $p_start, 'p2' => $p_end]
        );

        foreach ($data as $show_id) {
            $all_show_data = array_merge(self::getShowDataPointsWithinRange($p_start, $p_end, $show_id['show_id']), $all_show_data);
        }
        /* option to sort by number of listeners currently commented out
        usort($all_show_data, function($a, $b) {
            return $a['average_number_of_listeners'] - $b['average_number_of_listeners'];
        });
        */
        return $all_show_data;
    }

    public static function insertDataPoints($p_dataPoints)
    {
        $timestamp_sql = 'INSERT INTO cc_timestamp (timestamp) VALUES
            (:ts::TIMESTAMP) RETURNING id;';

        $mount_name_check_sql = 'SELECT id from cc_mount_name WHERE
            mount_name = :mn;';

        $mount_name_insert_sql = 'INSERT INTO cc_mount_name (mount_name) VALUES
                (:mn) RETURNING id;';

        $stats_sql = 'INSERT INTO cc_listener_count (timestamp_id,
            listener_count, mount_name_id) VALUES (:timestamp_id,
            :listener_count, :mount_name_id)';

        foreach ($p_dataPoints as $dp) {
            $timestamp_id = Application_Common_Database::prepareAndExecute(
                $timestamp_sql,
                ['ts' => $dp['timestamp']],
                'column'
            );

            $mount_name_id = Application_Common_Database::prepareAndExecute(
                $mount_name_check_sql,
                ['mn' => $dp['mount_name']],
                'column'
            );

            if (strlen($mount_name_id) == 0) {
                // there is a race condition here where theoretically the row
                // with value "mount_name" could appear, but this is *very*
                // unlikely and won't break anything even if it happens.
                $mount_name_id = Application_Common_Database::prepareAndExecute(
                    $mount_name_insert_sql,
                    ['mn' => $dp['mount_name']],
                    'column'
                );
            }

            Application_Common_Database::prepareAndExecute(
                $stats_sql,
                [
                    'timestamp_id' => $timestamp_id,
                    'listener_count' => $dp['num_listeners'],
                    'mount_name_id' => $mount_name_id,
                ]
            );
        }
    }
}
