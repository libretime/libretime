<?php

// Path constants
define('ROOT_PATH', dirname(__DIR__, 2));
define('LIB_PATH', ROOT_PATH . '/library');
define('BUILD_PATH', ROOT_PATH . '/build');
define('APPLICATION_PATH', ROOT_PATH . '/application');
define('CONFIG_PATH', APPLICATION_PATH . '/configs');
define('VENDOR_PATH', ROOT_PATH . '/vendor');

define('SAMPLE_CONFIG_FILEPATH', BUILD_PATH . '/airtime.example.conf');
define('PROPEL_CONFIG_FILEPATH', CONFIG_PATH . '/airtime-conf-production.php');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'production');
defined('VERBOSE_STACK_TRACE') || define('VERBOSE_STACK_TRACE', getenv('VERBOSE_STACK_TRACE') ?? true);

// Project constants
define('LIBRETIME_LOG_DIR', getenv('LIBRETIME_LOG_DIR') ?: '/var/log/libretime');
define('LIBRETIME_LOG_FILEPATH', getenv('LIBRETIME_LOG_FILEPATH') ?: LIBRETIME_LOG_DIR . '/legacy.log');

define('LIBRETIME_CONFIG_DIR', getenv('LIBRETIME_CONFIG_DIR') ?: '/etc/libretime');
define('LIBRETIME_CONF_DIR', LIBRETIME_CONFIG_DIR); // Deprecated
define('LIBRETIME_CONFIG_FILEPATH', getenv('LIBRETIME_CONFIG_FILEPATH') ?: LIBRETIME_CONFIG_DIR . '/config.yml');

// Legacy constants
define('PRODUCT_NAME', 'LibreTime');
define('PRODUCT_SITE_URL', 'https://libretime.org');

define('SAAS_PRODUCT_BRANDING_NAME', 'LibreTime');
define('SAAS_LOGIN_REFERRER', 'https://libretime.org');

define('COMPANY_NAME', 'LibreTime Community');
define('COMPANY_SUFFIX', '');
define('COMPANY_SITE', 'libretime.org');
define('COMPANY_SITE_URL', 'https://libretime.org');
define('SUPPORT_ADDRESS', 'https://discourse.libretime.org/');

define('HELP_URL', 'https://discourse.libretime.org/');
define('WHOS_USING_URL', 'https://github.com/orgs/libretime/people');
define('TERMS_AND_CONDITIONS_URL', 'https://github.com/libretime/libretime/blob/main/README.md');
define('PRIVACY_POLICY_URL', 'https://github.com/libretime/organization/blob/main/CODE_OF_CONDUCT.md');
define('USER_MANUAL_URL', 'https://libretime.org/docs');
define('TROUBLESHOOTING_URL', 'https://libretime.org/docs/admin-manual/troubleshooting/');
define('ABOUT_AIRTIME_URL', 'https://libretime.org');
define('LIBRETIME_CONTRIBUTE_URL', 'https://libretime.org/contribute');
define('LIBRETIME_DISCOURSE_URL', 'https://discourse.libretime.org');
define('UI_REVAMP_EMBED_URL', 'https://www.youtube.com/embed/nqpNnCKGluY');
define('LIBRETIME_WHATS_NEW_URL', 'https://github.com/libretime/libretime/releases');
define('LIBRETIME_UPDATE_FEED', 'https://github.com/libretime/libretime/releases.atom');
define('LIBRETIME_EMAIL_FROM', 'noreply@libretime.org');

define('LICENSE_VERSION', 'GNU AGPL v.3');
define('LICENSE_URL', 'https://www.gnu.org/licenses/agpl-3.0-standalone.html');

define('AIRTIME_COPYRIGHT_DATE', '2010-2015');
define('AIRTIME_REST_VERSION', '1.1');
define('AIRTIME_API_VERSION', '1.1');
// XXX: it's important that we upgrade this on major version bumps, usually users get more exact info from VERSION in airtime root dir
define('LIBRETIME_MAJOR_VERSION', '3');

// Defaults
define('DEFAULT_LOGO_PLACEHOLDER', 1);
define('DEFAULT_LOGO_FILE', 'images/airtime_logo.png');
define('DEFAULT_TIMESTAMP_FORMAT', 'Y-m-d H:i:s');
define('DEFAULT_MICROTIME_FORMAT', 'Y-m-d H:i:s.u');
define('DEFAULT_ICECAST_PORT', 8000);
define('DEFAULT_ICECAST_PASS', 'hackme');
define('DEFAULT_SHOW_COLOR', '76aca5');
define('DEFAULT_INTERVAL_FORMAT', 'H:i:s.u');

// Metadata Keys for files
define('MDATA_KEY_FILEPATH', 'filepath');
define('MDATA_KEY_DIRECTORY', 'directory');
define('MDATA_KEY_MD5', 'md5');
define('MDATA_KEY_TITLE', 'track_title');
define('MDATA_KEY_CREATOR', 'artist_name');
define('MDATA_KEY_SOURCE', 'album_title');
define('MDATA_KEY_DURATION', 'length');
define('MDATA_KEY_MIME', 'mime');
define('MDATA_KEY_FTYPE', 'ftype');
define('MDATA_KEY_URL', 'info_url');
define('MDATA_KEY_GENRE', 'genre');
define('MDATA_KEY_MOOD', 'mood');
define('MDATA_KEY_LABEL', 'label');
define('MDATA_KEY_COMPOSER', 'composer');
define('MDATA_KEY_DESCRIPTION', 'description');
define('MDATA_KEY_SAMPLERATE', 'sample_rate');
define('MDATA_KEY_BITRATE', 'bit_rate');
define('MDATA_KEY_ENCODER', 'encoded_by');
define('MDATA_KEY_ISRC', 'isrc_number');
define('MDATA_KEY_COPYRIGHT', 'copyright');
define('MDATA_KEY_YEAR', 'year');
define('MDATA_KEY_BPM', 'bpm');
define('MDATA_KEY_TRACKNUMBER', 'track_number');
define('MDATA_KEY_CONDUCTOR', 'conductor');
define('MDATA_KEY_LANGUAGE', 'language');
define('MDATA_KEY_REPLAYGAIN', 'replay_gain');
define('MDATA_KEY_OWNER_ID', 'owner_id');
define('MDATA_KEY_CUE_IN', 'cuein');
define('MDATA_KEY_CUE_OUT', 'cueout');
define('MDATA_KEY_ARTWORK', 'artwork');
define('MDATA_KEY_ARTWORK_DATA', 'artwork_data');
define('MDATA_KEY_TRACK_TYPE', 'track_type_id');

define('UI_MDATA_VALUE_FORMAT_FILE', 'File');
define('UI_MDATA_VALUE_FORMAT_STREAM', 'live stream');

// User types
define('UTYPE_HOST', 'H');
define('UTYPE_ADMIN', 'A');
define('UTYPE_SUPERADMIN', 'S');
define('UTYPE_GUEST', 'G');
define('UTYPE_PROGRAM_MANAGER', 'P');

// Constants for playout history template fields
define('TEMPLATE_DATE', 'date');
define('TEMPLATE_TIME', 'time');
define('TEMPLATE_DATETIME', 'datetime');
define('TEMPLATE_STRING', 'string');
define('TEMPLATE_BOOLEAN', 'boolean');
define('TEMPLATE_INT', 'integer');
define('TEMPLATE_FLOAT', 'float');

// Session Keys
define('UI_PLAYLISTCONTROLLER_OBJ_SESSNAME', 'PLAYLISTCONTROLLER_OBJ');
/*define('UI_PLAYLIST_SESSNAME', 'PLAYLIST');
define('UI_BLOCK_SESSNAME', 'BLOCK');*/

// TuneIn integration
define('TUNEIN_API_URL', 'https://air.radiotime.com/Playing.ashx');

// Celery
define('CELERY_PENDING_STATUS', 'PENDING');
define('CELERY_SUCCESS_STATUS', 'SUCCESS');
define('CELERY_FAILED_STATUS', 'FAILED');

// Celery Services
define('PODCAST_SERVICE_NAME', 'podcast');

// Publish Services
define('STATION_PODCAST_SERVICE_NAME', 'station_podcast');

// Podcast Types
// define('STATION_PODCAST', 0);
// define('IMPORTED_PODCAST', 1);

define('ITUNES_XML_NAMESPACE_URL', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
