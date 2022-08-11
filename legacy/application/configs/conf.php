<?php

// Propel load the configuration file direclty, we also need to load
// the libraries in this files.
require_once __DIR__ . '/constants.php';

require_once VENDOR_PATH . '/autoload.php';

// THIS FILE IS NOT MEANT FOR CUSTOMIZING.
use League\Uri\Contracts\UriException;
use League\Uri\Uri;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Schema implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $trim_trailing_slash = function ($v) {
            return rtrim($v, '/') . '/';
        };

        $treeBuilder = new TreeBuilder('');
        $treeBuilder->getRootNode()
            ->children()

            // General schema
            ->arrayNode('general')
            /**/->addDefaultsIfNotSet()
            /**/->children()
            /*  */->scalarNode('public_url')->cannotBeEmpty()->end()
            /*  */->scalarNode('api_key')->cannotBeEmpty()->end()
            /*  */->arrayNode('allowed_cors_origins')->scalarPrototype()->defaultValue([])->end()->end()
            /*  */->scalarNode('dev_env')->defaultValue('production')->end()
            /*  */->scalarNode('auth')->defaultValue('local')->end()
            /*  */->integerNode('cache_ahead_hours')->defaultValue(1)->end()
            /**/->end()
            ->end()

            // Database schema
            ->arrayNode('database')
            /**/->addDefaultsIfNotSet()
            /**/->children()
            /*  */->scalarNode('host')->defaultValue('localhost')->end()
            /*  */->integerNode('port')->defaultValue(5432)->end()
            /*  */->scalarNode('name')->defaultValue('libretime')->end()
            /*  */->scalarNode('user')->defaultValue('libretime')->end()
            /*  */->scalarNode('password')->defaultValue('libretime')->end()
            /**/->end()
            ->end()

            // Rabbitmq schema
            ->arrayNode('rabbitmq')
            /**/->addDefaultsIfNotSet()
            /**/->children()
            /*  */->scalarNode('host')->defaultValue('localhost')->end()
            /*  */->integerNode('port')->defaultValue(5672)->end()
            /*  */->scalarNode('vhost')->defaultValue('/libretime')->end()
            /*  */->scalarNode('user')->defaultValue('libretime')->end()
            /*  */->scalarNode('password')->defaultValue('libretime')->end()
            /**/->end()
            ->end()

            // Storage schema
            ->arrayNode('storage')
            /**/->addDefaultsIfNotSet()
            /**/->children()
            /*  */->scalarNode('path')->defaultValue('/srv/libretime')
            /*      */->validate()->ifString()->then($trim_trailing_slash)->end()
            /*  */->end()
            /**/->end()
            ->end()

            // Facebook schema
            ->arrayNode('facebook')
            /**/->setDeprecated("legacy", "3.0.0-alpha.11")
            /**/->children()
            /*  */->scalarNode('facebook_app_id')->end()
            /*  */->scalarNode('facebook_app_url')->end()
            /*  */->scalarNode('facebook_app_api_key')->end()
            /**/->end()
            ->end()

            // LDAP schema
            ->arrayNode('ldap')
            /**/->children()
            /*  */->scalarNode('hostname')->end()
            /*  */->scalarNode('binddn')->end()
            /*  */->scalarNode('password')->end()
            /*  */->scalarNode('account_domain')->end()
            /*  */->scalarNode('basedn')->end()
            /*  */->scalarNode('groupmap_guest')->end()
            /*  */->scalarNode('groupmap_host')->end()
            /*  */->scalarNode('groupmap_program_manager')->end()
            /*  */->scalarNode('groupmap_admin')->end()
            /*  */->scalarNode('groupmap_superadmin')->end()
            /*  */->scalarNode('filter_field')->end()
            /**/->end()
            ->end()

            // Playout schema
            ->arrayNode('playout')
            /**/->ignoreExtraKeys()
            ->end()

            ->end();

        return $treeBuilder;
    }
}

class Config
{
    private static $internal_values;
    private static $values;

    private static function load()
    {
        $filename = $_SERVER['LIBRETIME_CONFIG_FILEPATH'] ?? LIBRETIME_CONFIG_FILEPATH;
        $dirty = yaml_parse_file($filename);

        $schema = new Schema();
        $processor = new Processor();

        try {
            $values = $processor->processConfiguration($schema, [$dirty]);
        } catch (InvalidConfigurationException $error) {
            echo "could not parse configuration: " .  $error->getMessage();
            exit;
        }

        // Public url
        $public_url = self::validateUrl('general.public_url', $values['general']['public_url']);
        $values['general']['public_url_raw'] = $public_url;
        $values['general']['public_url'] = strval($public_url);

        // Allowed cors origins
        $values['general']['allowed_cors_origins'][] = strval($values['general']['public_url_raw']->withPath(''));

        // Storage path
        if (!is_dir($values['storage']['path'])) {
            echo "the configured storage.path '{$values['storage']['path']}' does not exists!";
            exit;
        }
        if (!is_writable($values['storage']['path'])) {
            echo "the configured storage.path '{$values['storage']['path']}' is not writable!";
            exit;
        }

        self::$values = $values;
        self::fillInternalValues($values);
    }

    private static function fillInternalValues($values)
    {
        $internal_values = [];
        // General
        // //////////////////////////////////////////////////////////////////////////////
        $internal_values['apiKey'] = [$values['general']['api_key']];
        $internal_values['public_url_raw'] = $values['general']['public_url_raw'];
        $internal_values['public_url'] = $values['general']['public_url'];

        // Allowed hosts
        $internal_values['allowedCorsOrigins'] = $values['general']['allowed_cors_origins'];

        $internal_values['dev_env'] = $values['general']['dev_env'];
        $internal_values['auth'] = $values['general']['auth'];
        $internal_values['cache_ahead_hours'] = $values['general']['cache_ahead_hours'];

        // SAAS remaining fields
        $internal_values['stationId'] = '';
        $internal_values['phpDir'] = '';
        $internal_values['staticBaseDir'] = '/';

        // Database
        // //////////////////////////////////////////////////////////////////////////////
        $internal_values['dsn']['phptype'] = 'pgsql';
        $internal_values['dsn']['host'] = $values['database']['host'];
        $internal_values['dsn']['port'] = $values['database']['port'];
        $internal_values['dsn']['database'] = $values['database']['name'];
        $internal_values['dsn']['username'] = $values['database']['user'];
        $internal_values['dsn']['password'] = $values['database']['password'];

        // RabbitMQ
        // //////////////////////////////////////////////////////////////////////////////
        $internal_values['rabbitmq']['host'] = $values['rabbitmq']['host'];
        $internal_values['rabbitmq']['port'] = $values['rabbitmq']['port'];
        $internal_values['rabbitmq']['vhost'] = $values['rabbitmq']['vhost'];
        $internal_values['rabbitmq']['user'] = $values['rabbitmq']['user'];
        $internal_values['rabbitmq']['password'] = $values['rabbitmq']['password'];

        // Storage
        // //////////////////////////////////////////////////////////////////////////////
        $internal_values['storagePath'] = $values['storage']['path'];

        // Facebook (DEPRECATED)
        // //////////////////////////////////////////////////////////////////////////////
        if (isset($values['facebook']['facebook_app_id'])) {
            $internal_values['facebook-app-id'] = $values['facebook']['facebook_app_id'];
            $internal_values['facebook-app-url'] = $values['facebook']['facebook_app_url'];
            $internal_values['facebook-app-api-key'] = $values['facebook']['facebook_app_api_key'];
        }

        // LDAP
        // //////////////////////////////////////////////////////////////////////////////
        if (array_key_exists('ldap', $values)) {
            $internal_values['ldap_hostname'] = $values['ldap']['hostname'];
            $internal_values['ldap_binddn'] = $values['ldap']['binddn'];
            $internal_values['ldap_password'] = $values['ldap']['password'];
            $internal_values['ldap_account_domain'] = $values['ldap']['account_domain'];
            $internal_values['ldap_basedn'] = $values['ldap']['basedn'];
            $internal_values['ldap_groupmap_guest'] = $values['ldap']['groupmap_guest'];
            $internal_values['ldap_groupmap_host'] = $values['ldap']['groupmap_host'];
            $internal_values['ldap_groupmap_program_manager'] = $values['ldap']['groupmap_program_manager'];
            $internal_values['ldap_groupmap_admin'] = $values['ldap']['groupmap_admin'];
            $internal_values['ldap_groupmap_superadmin'] = $values['ldap']['groupmap_superadmin'];
            $internal_values['ldap_filter_field'] = $values['ldap']['filter_field'];
        }

        self::$internal_values = $internal_values;
    }

    public static function setAirtimeVersion()
    {
        $version = @file_get_contents(dirname(ROOT_PATH) . '/VERSION');
        if (!$version) {
            // fallback to constant from constants.php if no other info is available
            $version = LIBRETIME_MAJOR_VERSION;
        }
        self::$internal_values['airtime_version'] = trim($version);
    }

    public static function getConfig()
    {
        if (is_null(self::$internal_values)) {
            self::load();
        }

        return self::$internal_values;
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

    /**
     * Validate and sanitize url.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public static function validateUrl($key, $value)
    {
        try {
            $url = Uri::createFromString($value);

            return $url->withPath(rtrim($url->getPath() ?? '', '/') . '/');
        } catch (UriException | TypeError $e) {
            echo "could not parse configuration field {$key}: " . $e->getMessage();

            exit;
        }
    }

    public static function getStoragePath()
    {
        return self::getConfig()['storagePath'];
    }

    public static function getPublicUrl()
    {
        return self::getConfig()['public_url'];
    }

    public static function getBasePath()
    {
        return self::getConfig()['public_url_raw']->getPath();
    }
}
