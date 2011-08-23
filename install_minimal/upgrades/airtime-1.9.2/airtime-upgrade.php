<?php
    class Airtime192Upgrade{

        public static function InstallAirtimePhpServerCode($phpDir)
        {
    
            $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');
    
            echo "* Installing PHP code to ".$phpDir.PHP_EOL;
            exec("mkdir -p ".$phpDir);
            exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
        }
        
    }
    
    class AirtimeIni192{

        const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
        const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
        const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
        const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
        const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
        const CONF_FILE_API_CLIENT = "/etc/airtime/api_client.cfg";
        const CONF_FILE_MONIT = "/etc/monit/conf.d/airtime-monit.cfg";
    
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
    
            $lines = file($p_filename);
            $n=count($lines);
            for ($i=0; $i<$n; $i++) {
                if (strlen($lines[$i]) && !in_array(substr($lines[$i], 0, 1), array('#', PHP_EOL))){
                     $info = explode("=", $lines[$i]);
                     $values[trim($info[0])] = trim($info[1]);
                 }
            }
    
            return $values;
        }
    
        public static function MergeConfigFiles($configFiles, $suffix) {
            foreach ($configFiles as $conf) {
                if (file_exists("$conf$suffix.bak")) {
    
                    if($conf === AirtimeIni192::CONF_FILE_AIRTIME) {
                        // Parse with sections
                        $newSettings = parse_ini_file($conf, true);
                        $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                    }
                    else {
                        $newSettings = AirtimeIni192::ReadPythonConfig($conf);
                        $oldSettings = AirtimeIni192::ReadPythonConfig("$conf$suffix.bak");
                    }
    
                    $settings = array_keys($newSettings);
    
                    foreach($settings as $section) {
                        // skip airtim_dir as we want to use new value
                        if(isset($oldSettings[$section])) {
                            if(is_array($oldSettings[$section])) {
                                $sectionKeys = array_keys($newSettings[$section]);
                                foreach($sectionKeys as $sectionKey) {
                                    if($sectionKey != "airtime_dir"){
                                        if(isset($oldSettings[$section][$sectionKey])) {
                                            AirtimeIni192::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                        }
                                    }
                                }
                            }
                            else {
                                AirtimeIni192::UpdateIniValue($conf, $section, $oldSettings[$section]);
                            }
                        }
                    }
                }
            }
        }
    
        public static function upgradeConfigFiles(){
    
            $configFiles = array(AirtimeIni192::CONF_FILE_AIRTIME,
                                 AirtimeIni192::CONF_FILE_PYPO,
                                 AirtimeIni192::CONF_FILE_RECORDER,
                                 AirtimeIni192::CONF_FILE_LIQUIDSOAP);
    
            // Backup the config files
            $suffix = date("Ymdhis")."-1.9.0";
            foreach ($configFiles as $conf) {
                if (file_exists($conf)) {
                    echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                    copy($conf, $conf.$suffix.".bak");
                }
            }
    
            $default_suffix = "192";
            AirtimeIni192::CreateIniFiles($default_suffix);
            AirtimeIni192::MergeConfigFiles($configFiles, $suffix);
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
    
            if (!copy(__DIR__."/airtime.conf.$suffix", AirtimeIni192::CONF_FILE_AIRTIME)){
                echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
                exit(1);
            }
            if (!copy(__DIR__."/pypo.cfg.$suffix", AirtimeIni192::CONF_FILE_PYPO)){
                echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
                exit(1);
            }
            if (!copy(__DIR__."/recorder.cfg.$suffix", AirtimeIni192::CONF_FILE_RECORDER)){
                echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
                exit(1);
            }
            if (!copy(__DIR__."/liquidsoap.cfg.$suffix", AirtimeIni192::CONF_FILE_LIQUIDSOAP)){
                echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
                exit(1);
            }
        }
    }
    
    // change site-available/airtime and restart apache
    echo "* Reconfiguring Apache\n";
    exec("find /etc/apache2/sites-available/ -name '*' -type f -exec sed -i 's/\/var\/www\/airtime\/public/\/usr\/share\/airtime\/public/g' '{}' \;");
    exec("service apache2 restart");
    
    echo "* Updating configFiles\n";
    AirtimeIni192::upgradeConfigFiles();
    
    // delete files in /var/www/airtime
    echo "* Deleting old PHP codes\n";
    exec("rm -rf /var/www/airtime");
   
    $values = parse_ini_file(AirtimeIni192::CONF_FILE_AIRTIME, true);
    $phpDir = $values['general']['airtime_dir'];
    Airtime192Upgrade::InstallAirtimePhpServerCode($phpDir);
?>