<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
 
/*
 * In the future, most Airtime upgrades will involve just mutating the
 * data that is stored on the system. For example, The only data
 * we need to preserve between versions is the database, /etc/airtime, and
 * /srv/airtime. Everything else is just binary files that can be removed/replaced
 * with the new version of Airtime. Of course, the data may need to be in a new
 * format, and that's what this upgrade script will be for.
 */

const VERSION_NUMBER = "2.0";

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/configs' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'DB.php';
require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/../../../airtime_mvc/application/configs/airtime-conf.php");

require_once 'UpgradeCommon.php';

/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start(){
        self::SetDefaultTimezone();
        self::setPhpDefaultTimeZoneToSystemTimezone();
        self::convert_cc_playlist();
        self::convert_cc_schedule();
        self::convert_cc_show_days();
        self::convert_cc_show_instances();

        self::doDbMigration();
        self::SetDefaultStreamSetting();
        self::GetOldLiquidsoapCfgAndUpdate();
    }

    private static function SetDefaultTimezone()
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

    private static function setPhpDefaultTimeZoneToSystemTimezone(){
        //we can get the default system timezone on debian/ubuntu by reading "/etc/timezone"
        $filename = "/etc/timezone";
        $handle = fopen($filename, "r");
        $contents = trim(fread($handle, filesize($filename)));
        echo "System timezone detected as: $contents".PHP_EOL;
        fclose($handle);

        date_default_timezone_set($contents);
    }

    private static function convert_cc_playlist(){
        /* cc_playlist has a field that keeps track of when the playlist was last modified. */
        $playlists = CcPlaylistQuery::create()->find();
        
        foreach ($playlists as $pl){
            $dt = new DateTime($pl->getDbMtime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $pl->setDbMtime($dt);
            
            $pl->save();
        }
    }

    private static function convert_cc_schedule(){
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
    
    private static function convert_cc_show_days(){
        /* cc_show_days has first_show, last_show and start_time fields that need to be changed to UTC. */
        $showDays = CcShowDaysQuery::create()->find();
        
        foreach ($showDays as $sd){
            $dt = new DateTime($sd->getDbFirstShow()." ".$sd->getDbStartTime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $sd->setDbFirstShow($dt->format("Y-m-d"));
            $sd->setDbStartTime($dt->format("H:i:s"));
            
            $dt = new DateTime($sd->getDbLastShow()." ".$sd->getDbStartTime(), new DateTimeZone(date_default_timezone_get()));
            $dt->setTimezone(new DateTimeZone("UTC"));
            $sd->setDbLastShow($dt->format("Y-m-d"));
            
            $sd->save();
        }
    }
    
    private static function convert_cc_show_instances(){
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

    private static function doDbMigration(){
        if(UpgradeCommon::DbTableExists('doctrine_migration_versions') === false) {
            $migrations = array('20110312121200', '20110331111708', '20110402164819', '20110406182005', '20110629143017', '20110711161043', '20110713161043');
            foreach($migrations as $migration) {
                UpgradeCommon::BypassMigrations(__DIR__, $migration);
            }
        }

        UpgradeCommon::MigrateTablesToVersion(__DIR__, '20110929184401');
    }

    private static function SetDefaultStreamSetting()
    {
        global $CC_DBC;

        echo "* Setting up default stream setting".PHP_EOL;
        $sql = "INSERT INTO cc_pref(keystr, valstr) VALUES('stream_type', 'ogg, mp3');
                INSERT INTO cc_pref(keystr, valstr) VALUES('stream_bitrate', '24, 32, 48, 64, 96, 128, 160, 192, 224, 256, 320');
                INSERT INTO cc_pref(keystr, valstr) VALUES('num_of_streams', '3');
                INSERT INTO cc_pref(keystr, valstr) VALUES('max_bitrate', '128');
                INSERT INTO cc_pref(keystr, valstr) VALUES('plan_level', 'disabled');
                
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('output_sound_device', 'false', 'boolean');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('icecast_vorbis_metadata', 'false', 'boolean');
                
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_output', 'icecast', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_type', 'ogg', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_bitrate', '128', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_host', '127.0.0.1', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_port', '8000', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_user', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_pass', 'hackme', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_mount', 'airtime_128', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_url', 'http://airtime.sourcefabric.org', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_description', 'Airtime Radio! Stream #1', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s1_genre', 'genre', 'string');
                
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_output', 'disabled', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_type', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_bitrate', '', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_host', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_port', '', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_user', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_pass', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_mount', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_url', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_description', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s2_genre', '', 'string');
                
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_output', 'disabled', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_type', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_bitrate', '', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_host', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_port', '', 'integer');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_user', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_pass', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_mount', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_url', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_description', '', 'string');
                INSERT INTO cc_stream_setting (keyname, value, type) VALUES ('s3_genre', '', 'string');";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }

    private static function GetOldLiquidsoapCfgAndUpdate(){
        global $CC_DBC;
        echo "* Retrieving old liquidsoap configuration".PHP_EOL;
        $map = array();
        $fh = fopen("/etc/airtime/liquidsoap.cfg", 'r');
        $newConfigMap = array();
        
        while(!feof($fh)){
            $line = fgets($fh);
            if(substr(trim($line), 0, 1) == '#' || trim($line) == ""){
                continue;
            }else{
                $info = explode('=', $line, 2);
                $map[trim($info[0])] = trim($info[1]);
            }
        }
        $newConfigMap['output_sound_device'] = $map['output_sound_device'];
        $newConfigMap['icecast_vorbis_metadata'] = $map['output_icecast_vorbis_metadata'];
        $newConfigMap['log_file'] = $map['log_file'];
        
        $count = 1;
        if( $map['output_icecast_vorbis'] == 'true'){
            $newConfigMap['s'.$count.'_output'] = 'icecast';
            $newConfigMap['s'.$count.'_host'] = $map['icecast_host'];
            $newConfigMap['s'.$count.'_port'] = $map['icecast_port'];
            $newConfigMap['s'.$count.'_pass'] = $map['icecast_pass'];
            $newConfigMap['s'.$count.'_mount'] = $map['mount_point_vorbis'];
            $newConfigMap['s'.$count.'_url'] = $map['icecast_url'];
            $newConfigMap['s'.$count.'_description'] = $map['icecast_description'];
            $newConfigMap['s'.$count.'_genre'] = $map['icecast_genre'];
            $newConfigMap['s'.$count.'_type'] = "ogg";
            $newConfigMap['s'.$count.'_bitrate'] = "128";
            $count++;
        }
        if($map['output_icecast_mp3'] == 'true'){
            $newConfigMap['s'.$count.'_output'] = 'icecast';
            $newConfigMap['s'.$count.'_host'] = $map['icecast_host'];
            $newConfigMap['s'.$count.'_port'] = $map['icecast_port'];
            $newConfigMap['s'.$count.'_pass'] = $map['icecast_pass'];
            $newConfigMap['s'.$count.'_mount'] = $map['mount_point_mp3'];
            $newConfigMap['s'.$count.'_url'] = $map['icecast_url'];
            $newConfigMap['s'.$count.'_description'] = $map['icecast_description'];
            $newConfigMap['s'.$count.'_genre'] = $map['icecast_genre'];
            $newConfigMap['s'.$count.'_type'] = "mp3";
            $newConfigMap['s'.$count.'_bitrate'] = "128";
            $count++;
        }
        if($map['output_shoutcast'] == 'true'){
            $newConfigMap['s'.$count.'_output'] = 'shoutcast';
            $newConfigMap['s'.$count.'_host'] = $map['shoutcast_host'];
            $newConfigMap['s'.$count.'_port'] = $map['shoutcast_port'];
            $newConfigMap['s'.$count.'_pass'] = $map['shoutcast_pass'];
            $newConfigMap['s'.$count.'_url'] = $map['shoutcast_url'];
            $newConfigMap['s'.$count.'_genre'] = $map['shoutcast_genre'];
            $newConfigMap['s'.$count.'_type'] = "mp3";
            $newConfigMap['s'.$count.'_bitrate'] = "128";
            $count++;
        }

        $sql = "";
        foreach( $newConfigMap as $key=>$val){
            if(substr($val, 0, 1) == '"' && substr($val, strlen($val)-1,1)){
                $val = ltrim($val, '"');
                $val = rtrim($val, '"');
            }
            $sql .= "UPDATE cc_stream_setting SET value='$val' WHERE keyname='$key';";
        }
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }
}

class AirtimeStorWatchedDirsUpgrade{

    public static function start(){
    }
}

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
        echo "* Updating configFiles\n";
        self::changeConfigFilePermissions();
        UpgradeCommon::upgradeConfigFiles();
    }

    /* Re: http://dev.sourcefabric.org/browse/CC-2797
     * We don't want config files to be world-readable so we
     * set the strictest permissions possible. */
    private static function changeConfigFilePermissions(){
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_AIRTIME, UpgradeCommon::CONF_WWW_DATA_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_API_CLIENT, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_PYPO, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of pypo.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_RECORDER, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of recorder.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_LIQUIDSOAP, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of liquidsoap.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_MEDIAMONITOR, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of media-monitor.cfg to 'pypo'. Exiting.";
            exit(1);
        }
    }

    private static function ChangeFileOwnerGroupMod($filename, $user){
        return (chown($filename, $user) &&
                chgrp($filename, $user) &&
                chmod($filename, 0640));
    }


}

/* Into this class put operations that don't fit into any of the other
 * 3 classes. For example, there may be stray files scattered throughout
 * the filesystem that we don't need anymore. Put the functions to clean
 * those out into this class. */
class AirtimeMiscUpgrade{

    public static function start(){
        self::RemoveOldMonitFile();
    }

    private static function RemoveOldMonitFile(){
        unlink("/etc/monit/conf.d/airtime-monit.cfg");
    }
}

UpgradeCommon::connectToDatabase();

AirtimeDatabaseUpgrade::start();
AirtimeStorWatchedDirsUpgrade::start();
AirtimeConfigFileUpgrade::start();
AirtimeMiscUpgrade::start();
