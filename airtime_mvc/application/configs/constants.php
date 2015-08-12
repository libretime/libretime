<?php

define('PRODUCT_NAME'       , 'Airtime');
define('PRODUCT_SITE_URL'   , 'http://airtime.sourcefabric.org');

define('SAAS_PRODUCT_BRANDING_NAME', 'Airtime Pro');

define('COMPANY_NAME'       , 'Sourcefabric');
define('COMPANY_SUFFIX'     , 'z.ú.');
define('COMPANY_SITE'       , 'Sourcefabric.org');
define('COMPANY_SITE_URL'   , 'http://sourcefabric.org/');
define('SUPPORT_EMAIL_ADDRESS', "help@sourcefabric.org");

define('HELP_URL'                   , 'http://help.sourcefabric.org/');
define('FAQ_URL'                    , 'https://sourcefabricberlin.zendesk.com/hc/en-us/sections/200994309-Airtime-FAQ');
define('WHOS_USING_URL'             , 'http://sourcefabric.org/en/airtime/whosusing');
define('TERMS_AND_CONDITIONS_URL'   , 'http://www.sourcefabric.org/en/about/policy/');
define('PRIVACY_POLICY_URL'         , 'http://www.sourcefabric.org/en/about/policy/');
define('USER_MANUAL_URL'            , 'http://sourcefabric.booktype.pro/airtime-pro-for-broadcasters');
define('AIRTIME_TRANSIFEX_URL'      , 'https://www.transifex.com/projects/p/airtime/');
define('WHMCS_PASSWORD_RESET_URL'   , 'https://account.sourcefabric.com/pwreset.php');

define('LICENSE_VERSION'    , 'GNU AGPL v.3');
define('LICENSE_URL'        , 'http://www.gnu.org/licenses/agpl-3.0-standalone.html');

define('AIRTIME_COPYRIGHT_DATE' , '2010-2015');
define('AIRTIME_REST_VERSION'   , '1.1');
define('AIRTIME_API_VERSION'    , '1.1');
// XXX: it's important that we upgrade this every time we add an upgrade!
define('AIRTIME_CODE_VERSION'   , '2.5.14');

// Defaults
define('DEFAULT_LOGO_PLACEHOLDER', 1);
define('DEFAULT_LOGO_FILE', 'images/airtime_logo.png');
define('DEFAULT_TIMESTAMP_FORMAT', 'Y-m-d H:i:s');
define('DEFAULT_MICROTIME_FORMAT', 'Y-m-d H:i:s.u');
define('DEFAULT_ICECAST_PORT', 8000);
define('DEFAULT_ICECAST_PASS', 'hackme');
define('DEFAULT_SHOW_COLOR', '5B9CAB');

// Metadata Keys for files
define('MDATA_KEY_FILEPATH'    , 'filepath');
define('MDATA_KEY_DIRECTORY'   , 'directory');
define('MDATA_KEY_MD5'         , 'md5');
define('MDATA_KEY_TITLE'       , 'track_title');
define('MDATA_KEY_CREATOR'     , 'artist_name');
define('MDATA_KEY_SOURCE'      , 'album_title');
define('MDATA_KEY_DURATION'    , 'length');
define('MDATA_KEY_MIME'        , 'mime');
define('MDATA_KEY_FTYPE'       , 'ftype');
define('MDATA_KEY_URL'         , 'info_url');
define('MDATA_KEY_GENRE'       , 'genre');
define('MDATA_KEY_MOOD'        , 'mood');
define('MDATA_KEY_LABEL'       , 'label');
define('MDATA_KEY_COMPOSER'    , 'composer');
define('MDATA_KEY_DESCRIPTION' , 'description');
define('MDATA_KEY_SAMPLERATE'  , 'sample_rate');
define('MDATA_KEY_BITRATE'     , 'bit_rate');
define('MDATA_KEY_ENCODER'     , 'encoded_by');
define('MDATA_KEY_ISRC'        , 'isrc_number');
define('MDATA_KEY_COPYRIGHT'   , 'copyright');
define('MDATA_KEY_YEAR'        , 'year');
define('MDATA_KEY_BPM'         , 'bpm');
define('MDATA_KEY_TRACKNUMBER' , 'track_number');
define('MDATA_KEY_CONDUCTOR'   , 'conductor');
define('MDATA_KEY_LANGUAGE'    , 'language');
define('MDATA_KEY_REPLAYGAIN'  , 'replay_gain');
define('MDATA_KEY_OWNER_ID'    , 'owner_id');
define('MDATA_KEY_CUE_IN'      , 'cuein');
define('MDATA_KEY_CUE_OUT'     , 'cueout');

define('UI_MDATA_VALUE_FORMAT_FILE'   , 'File');
define('UI_MDATA_VALUE_FORMAT_STREAM' , 'live stream');

//User types
define('UTYPE_HOST'            , 'H');
define('UTYPE_ADMIN'           , 'A');
define('UTYPE_SUPERADMIN'      , 'S');
define('UTYPE_GUEST'           , 'G');
define('UTYPE_PROGRAM_MANAGER' , 'P');

//Constants for playout history template fields
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

//WHMCS integration
define("WHMCS_API_URL", "https://account.sourcefabric.com/includes/api.php");
define("SUBDOMAIN_WHMCS_CUSTOM_FIELD_NAME", "Choose your domain");

//Sentry error logging
define('SENTRY_CONFIG_PATH', '/etc/airtime-saas/sentry.airtime_web.ini');

//Provisioning status
define('PROVISIONING_STATUS_SUSPENDED' , 'Suspended');
define('PROVISIONING_STATUS_ACTIVE' , 'Active');

//TuneIn integration
define("TUNEIN_API_URL", "http://air.radiotime.com/Playing.ashx");

// SoundCloud
define('SOUNDCLOUD', 'SoundCloud');
define('DEFAULT_SOUNDCLOUD_LICENSE_TYPE', 'all-rights-reserved');
define('DEFAULT_SOUNDCLOUD_SHARING_TYPE', 'public');

// Celery
define('CELERY_PENDING_STATUS', 'PENDING');
define('CELERY_SUCCESS_STATUS', 'SUCCESS');
define('CELERY_FAILED_STATUS', 'FAILED');

// Celery Services
define('SOUNDCLOUD_SERVICE_NAME', 'soundcloud');
