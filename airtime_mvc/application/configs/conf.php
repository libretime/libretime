<?php
/* THIS FILE IS NOT MEANT FOR CUSTOMIZING.
 * PLEASE EDIT THE FOLLOWING TO CHANGE YOUR CONFIG:
 * /etc/airtime/airtime.conf
 */

class Config {
    private static $CC_CONFIG = null;
    public static function loadConfig() {
        $CC_CONFIG = array(
                /* ================================================ storage configuration */
        
                'soundcloud-client-id' => '2CLCxcSXYzx7QhhPVHN4A',
                'soundcloud-client-secret' => 'pZ7beWmF06epXLHVUP1ufOg2oEnIt9XhE8l8xt0bBs',
        
                "rootDir" => __DIR__."/../.."
        );
        
        //In the unit testing environment, we always want to use our local airtime.conf in airtime_mvc/application/test:
        if (getenv('AIRTIME_UNIT_TEST') == '1') {
            $filename = "airtime.conf";
        } else {
            $filename = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
        }
        
        $values = parse_ini_file($filename, true);

        // Name of the web server user
        $CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
        $CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

        $CC_CONFIG['baseDir'] = $values['general']['base_dir'];
        $CC_CONFIG['baseUrl'] = $values['general']['base_url'];
        $CC_CONFIG['basePort'] = $values['general']['base_port'];
//        $CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
        
        $CC_CONFIG['cache_ahead_hours'] = $values['general']['cache_ahead_hours'];
        
	    // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_CONFIG['apiKey'] = array($values['general']['api_key']);
        
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == "development"){
            $CC_CONFIG['apiKey'][] = "";
        }

        $CC_CONFIG['soundcloud-connection-retries'] = $values['soundcloud']['connection_retries'];
        $CC_CONFIG['soundcloud-connection-wait'] = $values['soundcloud']['time_between_retries'];
        
        if(isset($values['demo']['demo'])){
            $CC_CONFIG['demo'] = $values['demo']['demo'];
        }
        self::$CC_CONFIG = $CC_CONFIG;
    }
    
    public static function setAirtimeVersion() {
        $airtime_version = Application_Model_Preference::GetAirtimeVersion();
        $uniqueid = Application_Model_Preference::GetUniqueId();
        self::$CC_CONFIG['airtime_version'] = md5($airtime_version.$uniqueid);
    }
    
    public static function getConfig() {
        if (is_null(self::$CC_CONFIG)) {
            self::loadConfig();
        }
        return self::$CC_CONFIG;
    }
}
