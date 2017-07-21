<?php
/* THIS FILE IS NOT MEANT FOR CUSTOMIZING.
 * PLEASE EDIT THE FOLLOWING TO CHANGE YOUR CONFIG:
 * LIBRETIME_CONF_DIR/airtime.conf
 */

require_once __DIR__ . '/constants.php';

class Config {
    private static $CC_CONFIG = null;
    private static $rootDir;
    public static function loadConfig() {

        self::$rootDir = __DIR__."/../..";
        $CC_CONFIG = array(
                /* ================================================ storage configuration */
                "rootDir" => self::$rootDir
        );
        
        //In the unit testing environment, LIBRETIME_CONF_DIR will our local airtime.conf in airtime_mvc/application/test/conf:
        $filename = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : LIBRETIME_CONF_DIR . "/airtime.conf";
        
        $values = parse_ini_file($filename, true);

        // Name of the web server user
        $CC_CONFIG['webServerUser'] = $values['general']['web_server_user'];
        $CC_CONFIG['rabbitmq'] = $values['rabbitmq'];

        $CC_CONFIG['baseDir'] = $values['general']['base_dir'];
        $CC_CONFIG['baseUrl'] = $values['general']['base_url'];
        $CC_CONFIG['basePort'] = $values['general']['base_port'];
        $CC_CONFIG['stationId'] = $values['general']['station_id'];
        $CC_CONFIG['phpDir'] = $values['general']['airtime_dir'];
        if (isset($values['general']['dev_env'])) {
            $CC_CONFIG['dev_env'] = $values['general']['dev_env'];
        } else {
            $CC_CONFIG['dev_env'] = 'production';
        }

        $CC_CONFIG['auth'] = 'local';
        if (isset($values['general']['auth'])) {
            $CC_CONFIG['auth'] = $values['general']['auth'];
        }

        //Backported static_base_dir default value into saas for now.
        if (array_key_exists('static_base_dir', $values['general'])) {
            $CC_CONFIG['staticBaseDir'] = $values['general']['static_base_dir'];
        } else {
            $CC_CONFIG['staticBaseDir'] = '/';
        }

        $CC_CONFIG['amazon_S3'] = array(
            'provider' => $values['amazon_S3']['provider'],
            'bucket' => $values['amazon_S3']['bucket'],
            'api_key' => $values['amazon_S3']['api_key'],
            'api_key_secret' => $values['amazon_S3']['api_key_secret']
        );

        // Tells us where file uploads will be uploaded to.
        // It will either be set to a cloud storage backend or local file storage.
        $CC_CONFIG["current_backend"] = $values["current_backend"]["storage_backend"];

        $CC_CONFIG['cache_ahead_hours'] = $values['general']['cache_ahead_hours'];
        
        // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_CONFIG['apiKey'] = array($values['general']['api_key']);
        
        $CC_CONFIG['soundcloud-connection-retries'] = $values['soundcloud']['connection_retries'];
        $CC_CONFIG['soundcloud-connection-wait'] = $values['soundcloud']['time_between_retries'];

        $CC_CONFIG['soundcloud-client-id'] = $values['soundcloud']['soundcloud_client_id'];
        $CC_CONFIG['soundcloud-client-secret'] = $values['soundcloud']['soundcloud_client_secret'];
        $CC_CONFIG['soundcloud-redirect-uri'] = $values['soundcloud']['soundcloud_redirect_uri'];
        if (isset($values['facebook']['facebook_app_id'])) {
            $CC_CONFIG['facebook-app-id'] = $values['facebook']['facebook_app_id'];
            $CC_CONFIG['facebook-app-url'] = $values['facebook']['facebook_app_url'];
            $CC_CONFIG['facebook-app-api-key'] = $values['facebook']['facebook_app_api_key'];
        }

        // ldap config
        if (array_key_exists('ldap', $values)) {
            $CC_CONFIG['ldap_hostname'] = $values['ldap']['hostname'];
            $CC_CONFIG['ldap_binddn'] = $values['ldap']['binddn'];
            $CC_CONFIG['ldap_password'] = $values['ldap']['password'];
            $CC_CONFIG['ldap_account_domain'] = $values['ldap']['account_domain'];
            $CC_CONFIG['ldap_basedn'] = $values['ldap']['basedn'];
            $CC_CONFIG['ldap_groupmap_guest'] = $values['ldap']['groupmap_guest'];
            $CC_CONFIG['ldap_groupmap_host'] = $values['ldap']['groupmap_host'];
            $CC_CONFIG['ldap_groupmap_program_manager'] = $values['ldap']['groupmap_program_manager'];
            $CC_CONFIG['ldap_groupmap_admin'] = $values['ldap']['groupmap_admin'];
            $CC_CONFIG['ldap_groupmap_superadmin'] = $values['ldap']['groupmap_superadmin'];
            $CC_CONFIG['ldap_filter_field'] = $values['ldap']['filter_field'];
        }

        if(isset($values['demo']['demo'])){
            $CC_CONFIG['demo'] = $values['demo']['demo'];
        }
        self::$CC_CONFIG = $CC_CONFIG;
    }
    
    public static function setAirtimeVersion() {
        $version = @file_get_contents(self::$rootDir."/../VERSION");
        if (!$version) {
            // fallback to constant from constants.php if no other info is available
            $version = LIBRETIME_MAJOR_VERSION;
        }
        self::$CC_CONFIG['airtime_version'] = trim($version);
    }
    
    public static function getConfig() {
        if (is_null(self::$CC_CONFIG)) {
            self::loadConfig();
        }
        return self::$CC_CONFIG;
    }
}
