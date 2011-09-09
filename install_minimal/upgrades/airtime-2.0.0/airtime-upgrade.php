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
    const CONF_DIR_BINARIES = "/usr/lib/airtime";
    
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

    public static function GetUtilsSrcDir()
    {
        return __DIR__."/../../../utils";
    }

    public static function CreateSymlinksToUtils()
    {
        echo "* Installing airtime-log".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-log";
        copy(AirtimeInstall::GetUtilsSrcDir()."/airtime-log.php", AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-log.php");
        
        exec("ln -s $dir /usr/bin/airtime-log");
    }

    public static function SetDefaultStreamSetting()
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
    
    public static function BypassMigrations($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction --add migrations:version $version";
        system($command);
    }

    public static function MigrateTablesToVersion($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction migrations:migrate $version";
        system($command);
    }
    
    public static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../../airtime_mvc";
    }
    
    public static function DbTableExists($p_name)
    {
        global $CC_DBC;
        $sql = "SELECT * FROM ".$p_name;
        $result = $CC_DBC->GetOne($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }
    public static function GetOldLiquidsoapCfgAndUpdate(){
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
    
    public static function InstallAirtimePhpServerCode($phpDir)
        {
    
            $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');
    
            // delete old files
            exec("rm -rf ".$phpDir);
            echo "* Installing PHP code to ".$phpDir.PHP_EOL;
            exec("mkdir -p ".$phpDir);
            exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
        }
}

class ConvertToUtc{

    public static function setPhpDefaultTimeZoneToSystemTimezone(){
        //we can get the default system timezone on debian/ubuntu by reading "/etc/timezone"
        $filename = "/etc/timezone";
        $handle = fopen($filename, "r");
        $contents = trim(fread($handle, filesize($filename)));
        echo "System timezone detected as: $contents".PHP_EOL;
        fclose($handle);

        date_default_timezone_set($contents);
    }

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

class AirtimeIni200{

    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
    const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT = "/etc/airtime/api_client.cfg";
    const CONF_FILE_MONIT = "/etc/monit/conf.d/airtime-monit.cfg";

    const CONF_PYPO_GRP = "pypo";
    const CONF_WWW_DATA_GRP = "www-data";

    /**
     * This function updates an INI style config file.
     *
     * A property and the value the property should be changed to are
     * supplied. If the property is not found, then no changes are made.
     *
     * @param string $p_filename
     *      The path the to the file.
     * @param string $p_property
     *      The property to look for in order to change its value.
     * @param string $p_value
     *      The value the property should be changed to.
     *
     */
    public static function UpdateIniValue($p_filename, $p_property, $p_value)
    {
        $lines = file($p_filename);
        $n=count($lines);
        foreach ($lines as &$line) {
            if ($line[0] != "#"){
                $key_value = explode("=", $line);
                $key = trim($key_value[0]);

                if ($key == $p_property){
                    $line = "$p_property = $p_value".PHP_EOL;
                }
            }
        }

        $fp=fopen($p_filename, 'w');
        for($i=0; $i<$n; $i++){
            fwrite($fp, $lines[$i]);
        }
        fclose($fp);
    }

    public static function ReadPythonConfig($p_filename)
    {
        $values = array();
        
        $fh = fopen($p_filename, 'r');
        
        while(!feof($fh)){
            $line = fgets($fh);
            if(substr(trim($line), 0, 1) == '#' || trim($line) == ""){
                continue;
            }else{
                $info = explode('=', $line, 2);
                $values[trim($info[0])] = trim($info[1]);
            }
        }

        return $values;
    }

    public static function MergeConfigFiles($configFiles, $suffix) {
        foreach ($configFiles as $conf) {
            // we want to use new liquidsoap.cfg so don't merge
            // also for monit
            if( $conf == AirtimeIni200::CONF_FILE_LIQUIDSOAP || $conf == AirtimeIni200::CONF_FILE_MONIT){
                continue;
            }
            if (file_exists("$conf$suffix.bak")) {

                if($conf === AirtimeIni200::CONF_FILE_AIRTIME) {
                    // Parse with sections
                    $newSettings = parse_ini_file($conf, true);
                    $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                }
                else {
                    $newSettings = AirtimeIni200::ReadPythonConfig($conf);
                    $oldSettings = AirtimeIni200::ReadPythonConfig("$conf$suffix.bak");
                }

                $settings = array_keys($newSettings);

                foreach($settings as $section) {
                    if(isset($oldSettings[$section])) {
                        if(is_array($oldSettings[$section])) {
                            $sectionKeys = array_keys($newSettings[$section]);
                            foreach($sectionKeys as $sectionKey) {
                                // skip airtim_dir as we want to use new value
                                if($sectionKey != "airtime_dir"){
                                    if(isset($oldSettings[$section][$sectionKey])) {
                                        AirtimeIni200::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                    }
                                }
                            }
                        }
                        else {
                            AirtimeIni200::UpdateIniValue($conf, $section, $oldSettings[$section]);
                        }
                    }
                }
            }
        }
    }

    /* Re: http://dev.sourcefabric.org/browse/CC-2797
     * We don't want config files to be world-readable so we
     * set the strictest permissions possible. */
    public static function changeConfigFilePermissions(){
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_AIRTIME, self::CONF_WWW_DATA_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_API_CLIENT, self::CONF_PYPO_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_PYPO, self::CONF_PYPO_GRP)){
            echo "Could not set ownership of pypo.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_RECORDER, self::CONF_PYPO_GRP)){
            echo "Could not set ownership of recorder.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_LIQUIDSOAP, self::CONF_PYPO_GRP)){
            echo "Could not set ownership of liquidsoap.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(AirtimeIni200::CONF_FILE_MEDIAMONITOR, self::CONF_PYPO_GRP)){
            echo "Could not set ownership of media-monitor.cfg to 'pypo'. Exiting.";
            exit(1);
        }
    }

    public static function ChangeFileOwnerGroupMod($filename, $user){
        return (chown($filename, $user) &&
                chgrp($filename, $user) &&
                chmod($filename, 0640));
    }

    public static function upgradeConfigFiles(){

        $configFiles = array(AirtimeIni200::CONF_FILE_AIRTIME,
                             AirtimeIni200::CONF_FILE_PYPO,
                             AirtimeIni200::CONF_FILE_RECORDER,
                             AirtimeIni200::CONF_FILE_LIQUIDSOAP,
                             AirtimeIni200::CONF_FILE_MEDIAMONITOR,
                             AirtimeIni200::CONF_FILE_API_CLIENT);

        // Backup the config files
        $suffix = date("Ymdhis")."-1.9.3";
        foreach ($configFiles as $conf) {
            // do not back up monit cfg
            if (file_exists($conf) && $conf != AirtimeIni200::CONF_FILE_MONIT) {
                echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                //copy($conf, $conf.$suffix.".bak");
                exec("cp -p $conf $conf$suffix.bak"); //use cli version to preserve file attributes
            }
        }

        $default_suffix = "200";
        AirtimeIni200::CreateIniFiles($default_suffix);
        AirtimeIni200::MergeConfigFiles($configFiles, $suffix);
    }

    /**
     * This function creates the /etc/airtime configuration folder
     * and copies the default config files to it.
     */
    public static function CreateIniFiles($suffix)
    {
        if (!file_exists("/etc/airtime/")){
            if (!mkdir("/etc/airtime/", 0755, true)){
                echo "Could not create /etc/airtime/ directory. Exiting.";
                exit(1);
            }
        }

        if (!copy(__DIR__."/airtime.conf.$suffix", AirtimeIni200::CONF_FILE_AIRTIME)){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/pypo.cfg.$suffix", AirtimeIni200::CONF_FILE_PYPO)){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/recorder.cfg.$suffix", AirtimeIni200::CONF_FILE_RECORDER)){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        /*if (!copy(__DIR__."/liquidsoap.cfg.$suffix", AirtimeIni200::CONF_FILE_LIQUIDSOAP)){
            echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }*/
        if (!copy(__DIR__."/api_client.cfg.$suffix", AirtimeIni200::CONF_FILE_API_CLIENT)){
            echo "Could not copy airtime-monit.cfg to /etc/monit/conf.d/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/airtime-monit.cfg.$suffix", AirtimeIni200::CONF_FILE_MONIT)){
            echo "Could not copy airtime-monit.cfg to /etc/monit/conf.d/. Exiting.";
            exit(1);
        }
    }
}

Airtime200Upgrade::connectToDatabase();
AirtimeInstall::SetDefaultTimezone();
AirtimeInstall::CreateSymlinksToUtils();

/* Airtime 2.0.0 starts interpreting all database times in UTC format. Prior to this, all the times
 * were stored using the local time zone. Let's convert to UTC time. */
ConvertToUtc::setPhpDefaultTimeZoneToSystemTimezone();
ConvertToUtc::convert_cc_playlist();
ConvertToUtc::convert_cc_schedule();
ConvertToUtc::convert_cc_show_days();
ConvertToUtc::convert_cc_show_instances();

// merging/updating config files
echo "* Updating configFiles\n";
AirtimeIni200::changeConfigFilePermissions();
AirtimeIni200::upgradeConfigFiles();

$values = parse_ini_file(AirtimeIni200::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime200Upgrade::InstallAirtimePhpServerCode($phpDir);

if(AirtimeInstall::DbTableExists('doctrine_migration_versions') === false) {
    $migrations = array('20110312121200', '20110331111708', '20110402164819', '20110406182005', '20110629143017', '20110711161043', '20110713161043');
    foreach($migrations as $migration) {
        AirtimeInstall::BypassMigrations(__DIR__, $migration);
    }
}

AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110829143306');

AirtimeInstall::SetDefaultStreamSetting();

AirtimeInstall::GetOldLiquidsoapCfgAndUpdate();

// restart monit
exec("service monit restart");



 
