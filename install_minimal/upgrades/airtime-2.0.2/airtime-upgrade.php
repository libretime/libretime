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

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/configs' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/propel/airtime-conf.php");

require_once 'UpgradeCommon.php';

class AirtimeStorWatchedDirsUpgrade{

    public static function start(){
    }
}

/* This class deals with any modifications to config files in /etc/airtime
 * as well as backups. All functions other than start() should be marked
 * as private. */
class AirtimeConfigFileUpgrade{

    public static function start(){
        echo "* Updating configFiles\n";
        self::changeConfigFilePermissions();
        UpgradeCommon::upgradeConfigFiles();
    }

    /* Re: http://dev.sourcefabric.org/browse/CC-2797
     * We don't want config files to be world-readable so we
     * set the strictest permissions possible. */
    private static function changeConfigFilePermissions(){
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_AIRTIME, UpgradeCommon::CONF_WWW_DATA_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_API_CLIENT, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of api_client.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_PYPO, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of pypo.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_LIQUIDSOAP, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of liquidsoap.cfg to 'pypo'. Exiting.";
            exit(1);
        }
        if (!self::ChangeFileOwnerGroupMod(UpgradeCommon::CONF_FILE_MEDIAMONITOR, UpgradeCommon::CONF_PYPO_GRP)){
            echo "Could not set ownership of media-monitor.cfg to 'pypo'. Exiting.";
            exit(1);
        }
    }

    private static function ChangeFileOwnerGroupMod($filename, $user){
        return (chown($filename, $user) &&
                chgrp($filename, $user) &&
                chmod($filename, 0640));
    }


}

UpgradeCommon::connectToDatabase();
UpgradeCommon::SetDefaultTimezone();
AirtimeConfigFileUpgrade::start();
