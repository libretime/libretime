<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/configs' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'DB.php';

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/../../../airtime_mvc/application/configs/airtime-conf.php");

class AirtimeInstall{

    public static function SetDefaultTimezone()
    {
        global $CC_DBC;
        
        $defaultTimezone = date_default_timezone_get();

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('timezone', '$defaultTimezone')";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }
}

class Airtime200Upgrade{

    public static function connectToDatabase(){
        global $CC_DBC, $CC_CONFIG;

        $values = parse_ini_file('/etc/airtime/airtime.conf', true);

        // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
    }
}

class ConvertToUtc{

    public static function convert_cc_playlist(){
        /* cc_playlist has a field that keeps track of when the playlist was last modified. */
        $playlists = CcPlaylistQuery::create()->find();
        
        foreach ($playlists as $pl){
            $dt = new DateTime($pl->getDbMtime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $pl->setDbMtime($dt);
            
            $pl->save();
        }
    }

    public static function convert_cc_schedule(){
        /* cc_schedule has start and end fields that need to be changed to UTC. */
        $schedules = CcScheduleQuery::create()->find();
        
        foreach ($schedules as $s){
            $dt = new DateTime($s->getDbStarts(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $s->setDbStarts($dt);
            
            $dt = new DateTime($s->getDbEnds(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $s->setDbEnds($dt);
            
            $s->save();
        }
    }
    
    public static function convert_cc_show_days(){
        /* cc_show_days has first_show, last_show and start_time fields that need to be changed to UTC. */
        $showDays = CcShowDaysQuery::create()->find();
        
        foreach ($showDays as $sd){
            $dt = new DateTime($sd->getDbFirstShow()." ".$sd->getDbStartTime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $sd->setDbFirstShow($dt->format("Y-m-d"));
            $sd->setDbStartTime($dt->format("H-i-s"));
            
            $dt = new DateTime($sd->getDbLastShow()." ".$sd->getDbStartTime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $sd->setDbLastShow($dt->format("Y-m-d"));
            
            $sd->save();
        }
    }
    
    public static function convert_cc_show_instances(){
        /* convert_cc_show_instances has starts and ends fields that need to be changed to UTC. */
        $showInstances = CcShowInstancesQuery::create()->find();
        
        foreach ($showInstances as $si){
            $dt = new DateTime($si->getDbStarts(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $si->setDbStarts($dt);
            
            $dt = new DateTime($si->getDbEnds(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $si->setDbEnds($dt);
            
            $si->save();
        }
    }
}

Airtime200Upgrade::connectToDatabase();
AirtimeInstall::SetDefaultTimezone();


/* Airtime 2.0.0 starts interpreting all database times in UTC format. Prior to this, all the times
 * were stored using the local time zone. Let's convert to UTC time. */
ConvertToUtc::convert_cc_playlist();
ConvertToUtc::convert_cc_schedule();
ConvertToUtc::convert_cc_show_days();
ConvertToUtc::convert_cc_show_instances();

 
