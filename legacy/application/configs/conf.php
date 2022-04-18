<?php

// Propel load the configuration file direclty, we also need to load
// the libraries in this files.
require_once __DIR__ . '/constants.php';

require_once VENDOR_PATH . '/autoload.php';

// THIS FILE IS NOT MEANT FOR CUSTOMIZING.
use League\Uri\Contracts\UriException;
use League\Uri\Uri;

class Config
{
    private static $CC_CONFIG;

    public static function loadConfig()
    {
        $filename = $_SERVER['LIBRETIME_CONFIG_FILEPATH'] ?? LIBRETIME_CONFIG_FILEPATH;
        $values = yaml_parse_file($filename);

        $CC_CONFIG = [];

        // General
        // //////////////////////////////////////////////////////////////////////////////
        $CC_CONFIG['apiKey'] = [$values['general']['api_key']];

        // Explode public_url into multiple component with possible defaults for required fields
        try {
            $public_url = Uri::createFromString($values['general']['public_url']);
        } catch (UriException|TypeError $e) {
            echo 'could not parse configuration field general.public_url: ' . $e->getMessage();

            exit;
        }

        $scheme = $public_url->getScheme() ?? 'http';
        $host = $public_url->getHost() ?? 'localhost';
        $port = $public_url->getPort() ?? ($scheme == 'https' ? 443 : 80);
        $path = rtrim($public_url->getPath() ?? '', '/') . '/'; // Path requires a trailing slash

        $CC_CONFIG['protocol'] = $scheme;
        $CC_CONFIG['baseUrl'] = $host;
        $CC_CONFIG['basePort'] = $port;
        $CC_CONFIG['baseDir'] = $path;

        // Allowed hosts
        $CC_CONFIG['allowedCorsOrigins'] = $values['general']['allowed_cors_origins'] ?? [];
        $CC_CONFIG['allowedCorsOrigins'][] = strval($public_url->withPath(''));

        $CC_CONFIG['dev_env'] = $values['general']['dev_env'] ?? 'production';
        $CC_CONFIG['auth'] = $values['general']['auth'] ?? 'local';
        $CC_CONFIG['cache_ahead_hours'] = $values['general']['cache_ahead_hours'] ?? 1;

        // SAAS remaining fields
        $CC_CONFIG['stationId'] = $values['general']['station_id'] ?? '';
        $CC_CONFIG['phpDir'] = $values['general']['airtime_dir'] ?? '';
        $CC_CONFIG['staticBaseDir'] = $values['general']['static_base_dir'] ?? '/';

        // Database
        // //////////////////////////////////////////////////////////////////////////////
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['host'] = $values['database']['host'] ?? 'localhost';
        $CC_CONFIG['dsn']['port'] = $values['database']['port'] ?? 5432;
        $CC_CONFIG['dsn']['database'] = $values['database']['name'] ?? 'libretime';
        $CC_CONFIG['dsn']['username'] = $values['database']['user'] ?? 'libretime';
        $CC_CONFIG['dsn']['password'] = $values['database']['password'] ?? 'libretime';

        // RabbitMQ
        // //////////////////////////////////////////////////////////////////////////////
        $CC_CONFIG['rabbitmq']['host'] = $values['rabbitmq']['host'] ?? 'localhost';
        $CC_CONFIG['rabbitmq']['port'] = $values['rabbitmq']['port'] ?? 5672;
        $CC_CONFIG['rabbitmq']['vhost'] = $values['rabbitmq']['vhost'] ?? '/libretime';
        $CC_CONFIG['rabbitmq']['user'] = $values['rabbitmq']['user'] ?? 'libretime';
        $CC_CONFIG['rabbitmq']['password'] = $values['rabbitmq']['password'] ?? 'libretime';

        // Storage
        // //////////////////////////////////////////////////////////////////////////////
        $CC_CONFIG['storagePath'] = $values['storage']['path'] ?? '/srv/libretime';
        if (!is_dir($CC_CONFIG['storagePath'])) {
            echo "the configured storage.path '{$CC_CONFIG['storagePath']}' does not exists!";

            exit;
        }
        if (!is_writable($CC_CONFIG['storagePath'])) {
            echo "the configured storage.path '{$CC_CONFIG['storagePath']}' is not writable!";

            exit;
        }

        // Facebook (DEPRECATED)
        // //////////////////////////////////////////////////////////////////////////////
        if (isset($values['facebook']['facebook_app_id'])) {
            $CC_CONFIG['facebook-app-id'] = $values['facebook']['facebook_app_id'];
            $CC_CONFIG['facebook-app-url'] = $values['facebook']['facebook_app_url'];
            $CC_CONFIG['facebook-app-api-key'] = $values['facebook']['facebook_app_api_key'];
        }

        // LDAP
        // //////////////////////////////////////////////////////////////////////////////
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

        // Demo
        // //////////////////////////////////////////////////////////////////////////////
        if (isset($values['demo']['demo'])) {
            $CC_CONFIG['demo'] = $values['demo']['demo'];
        }

        self::$CC_CONFIG = $CC_CONFIG;
    }

    public static function setAirtimeVersion()
    {
        $version = @file_get_contents(dirname(ROOT_PATH) . '/VERSION');
        if (!$version) {
            // fallback to constant from constants.php if no other info is available
            $version = LIBRETIME_MAJOR_VERSION;
        }
        self::$CC_CONFIG['airtime_version'] = trim($version);
    }

    public static function getConfig()
    {
        if (is_null(self::$CC_CONFIG)) {
            self::loadConfig();
        }

        return self::$CC_CONFIG;
    }

    /**
     * Check if the string is one of 'yes' or 'true' (case insensitive).
     *
     * @param mixed $value
     */
    public static function isYesValue($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (!is_string($value)) {
            return false;
        }

        return in_array(strtolower($value), ['yes', 'true']);
    }

    public static function getStoragePath()
    {
        return rtrim(self::getConfig()['storagePath'], '/') . '/';
    }
}
