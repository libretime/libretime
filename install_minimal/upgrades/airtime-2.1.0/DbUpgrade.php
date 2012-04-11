<?php

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start(){
        echo "* Updating Database".PHP_EOL;
        self::task0();
        self::task1();
    }

    private static function task0(){
        UpgradeCommon::MigrateTablesToVersion(__DIR__, '20120411102907');
       
        $sql = "INSERT INTO cc_pref(\"keystr\", \"valstr\") VALUES('scheduled_play_switch', 'on')";
        UpgradeCommon::nonSelectQueryDb($sql);
    }
    
    /*
     * set values for playout_status in cc_schedule
     */
    private static function task1() {
        
        // Define path to application directory
        defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(__DIR__."/../../application"));
        
        // Ensure library is on include_path
        set_include_path(implode(PATH_SEPARATOR, array(
                get_include_path(),
                realpath(APPLICATION_PATH . '/../library')
        )));
        
        //Propel classes.
        set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());
        
        require_once 'propel/runtime/lib/Propel.php';
        Propel::init(APPLICATION_PATH."/configs/airtime-conf-production.php");
        
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
