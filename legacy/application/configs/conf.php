<?php

// Propel load the configuration file direclty, we also need to load
// the libraries in this files.
require_once __DIR__ . '/constants.php';

require_once VENDOR_PATH . '/autoload.php';

// THIS FILE IS NOT MEANT FOR CUSTOMIZING.
use Adbar\Dot;
use League\Uri\Contracts\UriException;
use League\Uri\Uri;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class Schema implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $force_trailing_slash = function ($v) {
            return rtrim($v, '/') . '/';
        };

        $trim_leading_slash = function ($v) {
            return ltrim($v, '/');
        };

        $treeBuilder = new TreeBuilder('');
        $treeBuilder->getRootNode()
            ->children()

            // General schema
            ->arrayNode('general')->addDefaultsIfNotSet()->children()
            /**/->scalarNode('public_url')->cannotBeEmpty()->end()
            /**/->scalarNode('api_key')->cannotBeEmpty()->end()
            /**/->scalarNode('secret_key')->cannotBeEmpty()->end()
            /**/->arrayNode('allowed_cors_origins')->scalarPrototype()->defaultValue([])->end()->end()
            /**/->scalarNode('timezone')->cannotBeEmpty()->defaultValue("UTC")
            /*  */->validate()->ifNotInArray(DateTimeZone::listIdentifiers())
            /*  */->thenInvalid('invalid general.timezone %s')
            /*  */->end()
            /**/->end()
            /**/->scalarNode('dev_env')->defaultValue('production')->end()
            /**/->scalarNode('auth')->defaultValue('local')->end()
            /**/->integerNode('cache_ahead_hours')->defaultValue(1)->end()
            ->end()->end()

            // Database schema
            ->arrayNode('database')->addDefaultsIfNotSet()->children()
            /**/->scalarNode('host')->defaultValue('localhost')->end()
            /**/->integerNode('port')->defaultValue(5432)->end()
            /**/->scalarNode('name')->defaultValue('libretime')->end()
            /**/->scalarNode('user')->defaultValue('libretime')->end()
            /**/->scalarNode('password')->defaultValue('libretime')->end()
            ->end()->end()

            // Rabbitmq schema
            ->arrayNode('rabbitmq')->addDefaultsIfNotSet()->children()
            /**/->scalarNode('host')->defaultValue('localhost')->end()
            /**/->integerNode('port')->defaultValue(5672)->end()
            /**/->scalarNode('vhost')->defaultValue('/libretime')->end()
            /**/->scalarNode('user')->defaultValue('libretime')->end()
            /**/->scalarNode('password')->defaultValue('libretime')->end()
            ->end()->end()

            // Email schema
            ->arrayNode('email')
            /**/->ignoreExtraKeys()
            ->end()

            // Storage schema
            ->arrayNode('storage')->addDefaultsIfNotSet()->children()
            /**/->scalarNode('path')->defaultValue('/srv/libretime/')
            /*  */->validate()->ifString()->then($force_trailing_slash)->end()
            /**/->end()
            ->end()->end()

            // Facebook schema
            ->arrayNode('facebook')->setDeprecated("legacy", "3.0.0-alpha.11")->children()
            /**/->scalarNode('facebook_app_id')->end()
            /**/->scalarNode('facebook_app_url')->end()
            /**/->scalarNode('facebook_app_api_key')->end()
            ->end()->end()

            // LDAP schema
            ->arrayNode('ldap')->children()
            /**/->scalarNode('hostname')->end()
            /**/->scalarNode('binddn')->end()
            /**/->scalarNode('password')->end()
            /**/->scalarNode('account_domain')->end()
            /**/->scalarNode('basedn')->end()
            /**/->scalarNode('groupmap_guest')->end()
            /**/->scalarNode('groupmap_host')->end()
            /**/->scalarNode('groupmap_program_manager')->end()
            /**/->scalarNode('groupmap_admin')->end()
            /**/->scalarNode('groupmap_superadmin')->end()
            /**/->scalarNode('filter_field')->end()
            ->end()->end()

            // Playout schema
            ->arrayNode('playout')
            /**/->ignoreExtraKeys()
            ->end()

            // Liquidsoap schema
            ->arrayNode('liquidsoap')
            /**/->ignoreExtraKeys()
            ->end()

            // Stream schema
            ->arrayNode('stream')->ignoreExtraKeys()->addDefaultsIfNotSet()->children()

            // Stream inputs
            ->arrayNode('inputs')->addDefaultsIfNotSet()->children()
            /**/->arrayNode('main')->addDefaultsIfNotSet()->children()
            /*  */->booleanNode('enabled')->defaultTrue()->end()
            /*  */->enumNode('kind')->values(['harbor'])->defaultValue('harbor')->end()
            /*  */->scalarNode('public_url')->end()
            /*  */->scalarNode('mount')->defaultValue("main")
            /*      */->validate()->ifString()->then($trim_leading_slash)->end()
            /*  */->end()
            /*  */->integerNode('port')->defaultValue(8001)->end()
            /*  */->booleanNode('secure')->defaultValue(False)->end()
            /**/->end()->end()
            /**/->arrayNode('show')->addDefaultsIfNotSet()->children()
            /*  */->booleanNode('enabled')->defaultTrue()->end()
            /*  */->enumNode('kind')->values(['harbor'])->defaultValue('harbor')->end()
            /*  */->scalarNode('public_url')->end()
            /*  */->scalarNode('mount')->defaultValue("show")
            /*      */->validate()->ifString()->then($trim_leading_slash)->end()
            /*  */->end()
            /*  */->integerNode('port')->defaultValue(8002)->end()
            /*  */->booleanNode('secure')->defaultValue(False)->end()
            /**/->end()->end()
            ->end()->end()

            // Stream outputs
            ->arrayNode('outputs')->ignoreExtraKeys()->addDefaultsIfNotSet()->children()

            // Icecast outputs
            /**/->arrayNode('icecast')->arrayPrototype()->children()
            /*  */->booleanNode('enabled')->defaultFalse()->end()
            /*  */->enumNode('kind')->values(['icecast'])->defaultValue('icecast')->end()
            /*  */->scalarNode('public_url')->end()
            /*  */->scalarNode('host')->defaultValue('localhost')->end()
            /*  */->integerNode('port')->defaultValue(8000)->end()
            /*  */->scalarNode('mount')->cannotBeEmpty()
            /*    */->validate()->ifString()->then($trim_leading_slash)->end()
            /*  */->end()
            /*  */->scalarNode('source_user')->defaultValue('source')->end()
            /*  */->scalarNode('source_password')->cannotBeEmpty()->end()
            /*  */->scalarNode('admin_user')->defaultValue('admin')->end()
            /*  */->scalarNode('admin_password')->end()
            /*  */->arrayNode('audio')->addDefaultsIfNotSet()->children()
            /*    */->scalarNode('channels')->defaultValue('stereo')
            /*        */->validate()->ifNotInArray(['stereo', 'mono'])
            /*        */->thenInvalid('invalid stream.outputs.icecast.audio.channels %s')
            /*        */->end()
            /*    */->end()
            /*    */->scalarNode('format')->cannotBeEmpty()
            /*        */->validate()->ifNotInArray(['aac', 'mp3', 'ogg', 'opus'])
            /*        */->thenInvalid('invalid stream.outputs.icecast.audio.format %s')
            /*        */->end()
            /*    */->end()
            /*    */->integerNode('bitrate')->isRequired()->end()
            /*    */->booleanNode('enable_metadata')->defaultFalse()->end()
            /*  */->end()->end()
            /*  */->scalarNode('name')->end()
            /*  */->scalarNode('description')->end()
            /*  */->scalarNode('website')->end()
            /*  */->scalarNode('genre')->end()
            /*  */->booleanNode('mobile')->defaultFalse()->end()
            /**/->end()->end()->end()

            // Shoutcast outputs
            /**/->arrayNode('shoutcast')->arrayPrototype()->children()
            /*  */->booleanNode('enabled')->defaultFalse()->end()
            /*  */->enumNode('kind')->values(['shoutcast'])->defaultValue('shoutcast')->end()
            /*  */->scalarNode('public_url')->end()
            /*  */->scalarNode('host')->defaultValue('localhost')->end()
            /*  */->integerNode('port')->defaultValue(8000)->end()
            /*  */->scalarNode('source_user')->defaultValue('source')->end()
            /*  */->scalarNode('source_password')->cannotBeEmpty()->end()
            /*  */->scalarNode('admin_user')->defaultValue('admin')->end()
            /*  */->scalarNode('admin_password')->end()
            /*  */->arrayNode('audio')->addDefaultsIfNotSet()->children()
            /*    */->scalarNode('channels')->defaultValue('stereo')
            /*        */->validate()->ifNotInArray(['stereo', 'mono'])
            /*        */->thenInvalid('invalid stream.outputs.shoutcast.audio.channels %s')
            /*        */->end()
            /*    */->end()
            /*    */->scalarNode('format')->cannotBeEmpty()
            /*        */->validate()->ifNotInArray(['aac', 'mp3'])
            /*        */->thenInvalid('invalid stream.outputs.shoutcast.audio.format %s')
            /*        */->end()
            /*    */->end()
            /*    */->integerNode('bitrate')->isRequired()->end()
            /*  */->end()->end()
            /*  */->scalarNode('name')->end()
            /*  */->scalarNode('website')->end()
            /*  */->scalarNode('genre')->end()
            /*  */->booleanNode('mobile')->defaultFalse()->end()
            /**/->end()->end()->end()

            // System outputs
            /**/->arrayNode('system')->arrayPrototype()->children()
            /*  */->booleanNode('enabled')->defaultFalse()->end()
            /*  */->scalarNode('kind')->defaultValue('alsa')
            /*    */->validate()->ifNotInArray(["alsa", "ao", "oss", "portaudio", "pulseaudio"])
            /*    */->thenInvalid('invalid stream.outputs.system.kind %s')
            /*  */->end()->end()
            /**/->end()->end()->end()

            ->end()->end()

            // END Stream schema
            ->end()->end()

            // END Schema
            ->end();

        return $treeBuilder;
    }
}

class Config
{
    private static $legacy_values;
    private static $dot_values;
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
            echo 'could not parse configuration: ' . $error->getMessage();

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

        // Merge Icecast and Shoutcast outputs
        $values['stream']['outputs']['merged'] = array_merge(
            $values['stream']['outputs']['icecast'],
            $values['stream']['outputs']['shoutcast']
        );

        self::$values = $values;
        self::fillLegacyValues($values);
        self::$dot_values = new Dot($values);
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

    public static function get(...$args)
    {
        if (is_null(self::$dot_values)) {
            self::load();
        }

        return self::$dot_values->get(...$args);
    }

    public static function has(...$args)
    {
        if (is_null(self::$dot_values)) {
            self::load();
        }

        return self::$dot_values->has(...$args);
    }

    public static function getStoragePath()
    {
        return self::get('storage.path');
    }

    public static function getPublicUrl()
    {
        return self::get('general.public_url');
    }

    public static function getBasePath()
    {
        return self::get('general.public_url_raw')->getPath();
    }

    /**
     * Legacy config
     */

    private static function fillLegacyValues($values)
    {
        $legacy_values = [];
        // General
        $legacy_values['apiKey'] = [$values['general']['api_key']];
        $legacy_values['public_url_raw'] = $values['general']['public_url_raw'];
        $legacy_values['public_url'] = $values['general']['public_url'];

        // Allowed hosts
        $legacy_values['allowedCorsOrigins'] = $values['general']['allowed_cors_origins'];

        $legacy_values['dev_env'] = $values['general']['dev_env'];
        $legacy_values['auth'] = $values['general']['auth'];
        $legacy_values['cache_ahead_hours'] = $values['general']['cache_ahead_hours'];

        // SAAS remaining fields
        $legacy_values['stationId'] = '';
        $legacy_values['phpDir'] = '';
        $legacy_values['staticBaseDir'] = '/';

        // Database
        $legacy_values['dsn']['phptype'] = 'pgsql';
        $legacy_values['dsn']['host'] = $values['database']['host'];
        $legacy_values['dsn']['port'] = $values['database']['port'];
        $legacy_values['dsn']['database'] = $values['database']['name'];
        $legacy_values['dsn']['username'] = $values['database']['user'];
        $legacy_values['dsn']['password'] = $values['database']['password'];

        // RabbitMQ
        $legacy_values['rabbitmq']['host'] = $values['rabbitmq']['host'];
        $legacy_values['rabbitmq']['port'] = $values['rabbitmq']['port'];
        $legacy_values['rabbitmq']['vhost'] = $values['rabbitmq']['vhost'];
        $legacy_values['rabbitmq']['user'] = $values['rabbitmq']['user'];
        $legacy_values['rabbitmq']['password'] = $values['rabbitmq']['password'];

        // Storage
        $legacy_values['storagePath'] = $values['storage']['path'];

        // Stream
        $legacy_values['stream'] = $values['stream'];

        // Facebook (DEPRECATED)
        if (isset($values['facebook']['facebook_app_id'])) {
            $legacy_values['facebook-app-id'] = $values['facebook']['facebook_app_id'];
            $legacy_values['facebook-app-url'] = $values['facebook']['facebook_app_url'];
            $legacy_values['facebook-app-api-key'] = $values['facebook']['facebook_app_api_key'];
        }

        // LDAP
        if (array_key_exists('ldap', $values)) {
            $legacy_values['ldap_hostname'] = $values['ldap']['hostname'];
            $legacy_values['ldap_binddn'] = $values['ldap']['binddn'];
            $legacy_values['ldap_password'] = $values['ldap']['password'];
            $legacy_values['ldap_account_domain'] = $values['ldap']['account_domain'];
            $legacy_values['ldap_basedn'] = $values['ldap']['basedn'];
            $legacy_values['ldap_groupmap_guest'] = $values['ldap']['groupmap_guest'];
            $legacy_values['ldap_groupmap_host'] = $values['ldap']['groupmap_host'];
            $legacy_values['ldap_groupmap_program_manager'] = $values['ldap']['groupmap_program_manager'];
            $legacy_values['ldap_groupmap_admin'] = $values['ldap']['groupmap_admin'];
            $legacy_values['ldap_groupmap_superadmin'] = $values['ldap']['groupmap_superadmin'];
            $legacy_values['ldap_filter_field'] = $values['ldap']['filter_field'];
        }

        self::$legacy_values = $legacy_values;
    }

    public static function setAirtimeVersion()
    {
        $version = LIBRETIME_MAJOR_VERSION;

        foreach ([ROOT_PATH, dirname(ROOT_PATH)] as $path) {
            $content = @file_get_contents($path . '/VERSION');
            if ($content) {
                $version = trim($content);

                break;
            }
        }

        if (getenv('LIBRETIME_VERSION')) {
            $version = trim(getenv('LIBRETIME_VERSION'));
        }

        self::$legacy_values['airtime_version'] = $version;
    }

    public static function getConfig()
    {
        if (is_null(self::$legacy_values)) {
            self::load();
        }

        return self::$legacy_values;
    }
}
