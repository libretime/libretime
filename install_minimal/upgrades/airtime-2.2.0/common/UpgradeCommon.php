<?php
/* These are helper functions that are common to each upgrade such as
 * creating connections to a database, backing up config files etc.
 */
class UpgradeCommon{
    const CONF_FILE_AIRTIME      = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO         = "/etc/airtime/pypo.cfg";
    const CONF_FILE_LIQUIDSOAP   = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT   = "/etc/airtime/api_client.cfg";

    const CONF_PYPO_GRP          = "pypo";
    const CONF_WWW_DATA_GRP      = "www-data";
    const CONF_BACKUP_SUFFIX     = "220";
    const VERSION_NUMBER         = "2.2.0";
    
    public static function SetDefaultTimezone()
    {       
        $sql = "SELECT valstr from cc_pref WHERE keystr = 'timezone'";

        $result   = self::queryDb($sql);
        $timezone = $result->fetchColumn();
                
        date_default_timezone_set($timezone);
    }
    
    public static function connectToDatabase($p_exitOnError = true)
    {
        try {
            $con = Propel::getConnection();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
            echo "Database connection problem.".PHP_EOL;
            echo "Check if database exists with corresponding permissions.".PHP_EOL;
            if ($p_exitOnError) {
                exit(1);
            }
            return false;
        }
        return true;
    }

    
    public static function DbTableExists($p_name)
    {
        $con = Propel::getConnection();
        try {
            $sql = "SELECT * FROM ".$p_name." LIMIT 1";
            $con->query($sql);
        } catch (PDOException $e){
            return false;
        }
        return true;
    }

    private static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../../../airtime_mvc";
    }

    public static function MigrateTablesToVersion($dir, $version)
    {
        echo "Upgrading database, may take several minutes, please wait".PHP_EOL;
        
        $appDir = self::GetAirtimeSrcDir();
        $command = "php --php-ini $dir/../../airtime-php.ini ".
                    "$appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/common/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction migrations:migrate $version";
        system($command);
    }

    public static function BypassMigrations($dir, $version)
    {
        $appDir = self::GetAirtimeSrcDir();
        $command = "php --php-ini $dir/../../airtime-php.ini ".
                    "$appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/common/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction --add migrations:version $version";
        system($command);
    }

    public static function upgradeConfigFiles(){

        $configFiles = array(UpgradeCommon::CONF_FILE_AIRTIME,
                             UpgradeCommon::CONF_FILE_PYPO,
                             //this is not necessary because liquidsoap configs
                             //are automatically generated
                             //UpgradeCommon::CONF_FILE_LIQUIDSOAP,
                             UpgradeCommon::CONF_FILE_MEDIAMONITOR,
                             UpgradeCommon::CONF_FILE_API_CLIENT);

        // Backup the config files
        $suffix = date("Ymdhis")."-".UpgradeCommon::VERSION_NUMBER;
        foreach ($configFiles as $conf) {
            // do not back up monit cfg
            if (file_exists($conf)) {
                echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                //copy($conf, $conf.$suffix.".bak");
                exec("cp -p $conf $conf$suffix.bak"); //use cli version to preserve file attributes
            }
        }

        self::CreateIniFiles(UpgradeCommon::CONF_BACKUP_SUFFIX);
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

        $config_copy = array(
            "../etc/airtime.conf"      => self::CONF_FILE_AIRTIME,
            "../etc/pypo.cfg"          => self::CONF_FILE_PYPO,
            "../etc/media-monitor.cfg" => self::CONF_FILE_MEDIAMONITOR,
            "../etc/api_client.cfg"    => self::CONF_FILE_API_CLIENT
        );

        echo "Copying configs:\n";
        foreach ($config_copy as $path_part => $destination) {
            $full_path = OsPath::normpath(OsPath::join(__DIR__, 
                                                       "$path_part.$suffix"));
            echo "'$full_path' --> '$destination'\n";
            if(!copy($full_path, $destination)) {
                echo "Failed on the copying operation above\n";
                exit(1);
            }
        }
    }

    private static function MergeConfigFiles(array $configFiles, $suffix) {
        foreach ($configFiles as $conf) {
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

                                if(isset($oldSettings[$section][$sectionKey])) {
                                    self::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                }
                            }
                        } else {
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
    
    public static function queryDb($p_sql){
        $con = Propel::getConnection();

        try {
            $result = $con->query($p_sql);
        } catch (Exception $e) {
            echo "Error executing $p_sql. Exiting.";
            exit(1);
        }

        return $result;
    }
}

class OsPath {
    // this function is from http://stackoverflow.com/questions/2670299/is-there-a-php-equivalent-function-to-the-python-os-path-normpath
    public static function normpath($path)
    {
        if (empty($path))
            return '.';
    
        if (strpos($path, '/') === 0)
            $initial_slashes = true;
        else
            $initial_slashes = false;
        if (
            ($initial_slashes) &&
            (strpos($path, '//') === 0) &&
            (strpos($path, '///') === false)
        )
            $initial_slashes = 2;
        $initial_slashes = (int) $initial_slashes;
    
        $comps = explode('/', $path);
        $new_comps = array();
        foreach ($comps as $comp)
        {
            if (in_array($comp, array('', '.')))
                continue;
            if (
                ($comp != '..') ||
                (!$initial_slashes && !$new_comps) ||
                ($new_comps && (end($new_comps) == '..'))
            )
                array_push($new_comps, $comp);
            elseif ($new_comps)
                array_pop($new_comps);
        }
        $comps = $new_comps;
        $path = implode('/', $comps);
        if ($initial_slashes)
            $path = str_repeat('/', $initial_slashes) . $path;
        if ($path)
            return $path;
        else
            return '.';
    }
    
    /* Similar to the os.path.join python method
     * http://stackoverflow.com/a/1782990/276949 */
    public static function join() {
        $args = func_get_args();
        $paths = array();

        foreach($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }

        foreach($paths as &$path) {
            $path = trim($path, DIRECTORY_SEPARATOR);
        }

        if (substr($args[0], 0, 1) == DIRECTORY_SEPARATOR) {
            $paths[0] = DIRECTORY_SEPARATOR . $paths[0];
        }

        return join(DIRECTORY_SEPARATOR, $paths);
    }
}
