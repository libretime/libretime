<?php

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start(){
        echo "* Updating Database".PHP_EOL;
        self::task0();
    }

    private static function task0(){
        UpgradeCommon::MigrateTablesToVersion(__DIR__, '20120410143340');
       
        $sql = "INSERT INTO cc_pref(\"keystr\", \"valstr\") VALUES('scheduled_play_switch', 'on')";
        UpgradeCommon::nonSelectQueryDb($sql);
        
        require_once 'propel/runtime/lib/Propel.php';
        Propel::init(__DIR__."/../../configs/airtime-conf-production.php");
        
        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
        
        $showInstances = CcShowInstancesQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->filterByDbStarts(Criteria::GREATER_EQUAL)
            ->find($con);
        
        foreach ($showInstances as $instance) {
            $instance->updateScheduleStatus($con);
        }
    }

}
