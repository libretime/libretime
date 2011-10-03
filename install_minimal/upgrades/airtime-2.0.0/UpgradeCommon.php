<?php

/* These are helper functions that are common to each upgrade such as
 * creating connections to a database, backing up config files etc.
 */
class UpgradeCommon{
    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
    const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT = "/etc/airtime/api_client.cfg";

    const CONF_PYPO_GRP = "pypo";
    const CONF_WWW_DATA_GRP = "www-data";
    
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

    private static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../../airtime_mvc";
    }

    public static function MigrateTablesToVersion($dir, $version)
    {
        $appDir = self::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction migrations:migrate $version";
        system($command);
    }

    public static function BypassMigrations($dir, $version)
    {
        $appDir = self::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction --add migrations:version $version";
        system($command);
    }

    public static function upgradeConfigFiles(){

        $configFiles = array(UpgradeCommon::CONF_FILE_AIRTIME,
                             UpgradeCommon::CONF_FILE_PYPO,
                             UpgradeCommon::CONF_FILE_RECORDER,
                             UpgradeCommon::CONF_FILE_LIQUIDSOAP,
                             UpgradeCommon::CONF_FILE_MEDIAMONITOR,
                             UpgradeCommon::CONF_FILE_API_CLIENT);

        // Backup the config files
        $suffix = date("Ymdhis")."-".VERSION_NUMBER;
        foreach ($configFiles as $conf) {
            // do not back up monit cfg
            if (file_exists($conf)) {
                echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                //copy($conf, $conf.$suffix.".bak");
                exec("cp -p $conf $conf$suffix.bak"); //use cli version to preserve file attributes
            }
        }

        $default_suffix = "200";
        self::CreateIniFiles($default_suffix);
        self::MergeConfigFiles($configFiles, $suffix);
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

        if (!copy(__DIR__."/airtime.conf.$suffix", self::CONF_FILE_AIRTIME)){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/pypo.cfg.$suffix", self::CONF_FILE_PYPO)){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/recorder.cfg.$suffix", self::CONF_FILE_RECORDER)){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy(__DIR__."/api_client.cfg.$suffix", self::CONF_FILE_API_CLIENT)){
            echo "Could not copy airtime-monit.cfg to /etc/monit/conf.d/. Exiting.";
            exit(1);
        }
    }

    private static function MergeConfigFiles($configFiles, $suffix) {
        foreach ($configFiles as $conf) {
            // we want to use new liquidsoap.cfg so don't merge
            // also for monit
            if( $conf == self::CONF_FILE_LIQUIDSOAP){
                continue;
            }
            if (file_exists("$conf$suffix.bak")) {

                if($conf === self::CONF_FILE_AIRTIME) {
                    // Parse with sections
                    $newSettings = parse_ini_file($conf, true);
                    $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                }
                else {
                    $newSettings = self::ReadPythonConfig($conf);
                    $oldSettings = self::ReadPythonConfig("$conf$suffix.bak");
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
                                        self::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                    }
                                }
                            }
                        }
                        else {
                            self::UpdateIniValue($conf, $section, $oldSettings[$section]);
                        }
                    }
                }
            }
        }
    }

    private static function ReadPythonConfig($p_filename)
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
    private static function UpdateIniValue($p_filename, $p_property, $p_value)
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
}
