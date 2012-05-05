<?php

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start($p_dbValues){
        echo "* Updating Database".PHP_EOL;
        self::task0($p_dbValues);
        self::task1();
    }

    private static function task0($p_dbValues){
        //UpgradeCommon::MigrateTablesToVersion(__DIR__, '20120411174904');
        
        $username = $p_dbValues['database']['dbuser'];
        $password = $p_dbValues['database']['dbpass'];
        $host = $p_dbValues['database']['host'];
        $database = $p_dbValues['database']['dbname'];
        $dir = __DIR__;
        
        passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/data/upgrade.sql $database");
       
        $sql = "INSERT INTO cc_pref(\"keystr\", \"valstr\") VALUES('scheduled_play_switch', 'on')";
        UpgradeCommon::queryDb($sql);
    }
    
    /*
     * set values for playout_status in cc_schedule
     */
    private static function task1() {
        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
        
        $showInstances = CcShowInstancesQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->filterByDbStarts(gmdate("Y-m-d H:i:s"), Criteria::GREATER_EQUAL)
            ->find($con);
        
        foreach ($showInstances as $instance) {
            $instance->updateScheduleStatus($con);
        }
    }

}
