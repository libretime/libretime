<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
 
/*
 * In the future, most Airtime upgrades will involve just mutating the
 * data that is stored on the system. For example, The only data
 * we need to preserve between versions is the database, /etc/airtime, and
 * /srv/airtime. Everything else is just binary files that can be removed/replaced
 * with the new version of Airtime. Of course, the data may need to be in a new
 * format, and that's what this upgrade script will be for.
 */

const VERSION_NUMBER = "2.0.0";

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/configs' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'DB.php';
require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/../../../airtime_mvc/application/configs/airtime-conf.php");

require_once 'UpgradeCommon.php';


/* All functions other than start() should be marked as
 * private.
 */
class AirtimeDatabaseUpgrade{

    public static function start(){
        //self::doDbMigration();
    }

    private static function doDbMigration(){
        if(UpgradeCommon::DbTableExists('doctrine_migration_versions') === false) {
            $migrations = array('20110312121200', '20110331111708', '20110402164819', '20110406182005', '20110629143017', '20110711161043', '20110713161043');
            foreach($migrations as $migration) {
                UpgradeCommon::BypassMigrations(__DIR__, $migration);
            }
        }

        UpgradeCommon::MigrateTablesToVersion(__DIR__, '20110925171256');
    }
}

class AirtimeStorWatchedDirsUpgrade{

    public static function start(){
    }
}

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
        UpgradeCommon::upgradeConfigFiles();
    }
}

/* Into this class put operations that don't fit into any of the other
 * 3 classes. For example, there may be stray files scattered throughout
 * the filesystem that we don't need anymore. Put the functions to clean
 * those out into this class. */
class AirtimeMiscUpgrade{1

    public static function start(){
    }
}

UpgradeCommonFunctions::connectToDatabase();

AirtimeDatabaseUpgrade::start();
AirtimeStorWatchedDirsUpgrade::start();
AirtimeConfigFileUpgrade::start();
AirtimeMiscUpgrade::start();
