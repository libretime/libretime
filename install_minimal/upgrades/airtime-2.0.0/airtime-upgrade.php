<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'DB.php';

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/../../../airtime_mvc/application/configs/airtime-conf.php");

class AirtimeInstall{

    public static function SetDefaultTimezone()
    {
        global $CC_DBC;
        
        $defaultTimezone = date_default_timezone_get();

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('timezone', '$defaultTimezone')";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }
}

class Airtime200Upgrade{

    public static function connectToDatabase(){
        global $CC_DBC, $CC_CONFIG;

        $values = parse_ini_file('/etc/airtime/airtime.conf', true);

        // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
    }

}

Airtime200Upgrade::connectToDatabase();
AirtimeInstall::SetDefaultTimezone();
