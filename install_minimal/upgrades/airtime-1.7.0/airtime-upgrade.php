<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/../../include/AirtimeIni.php');
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');

/**
* This function creates the /etc/airtime configuration folder
* and copies the default config files to it.
*/
function CreateIniFiles()
{
    global $AIRTIME_PYTHON_APPS;

    if (!file_exists("/etc/airtime/")){
        if (!mkdir("/etc/airtime/", 0755, true)){
            echo "Could not create /etc/airtime/ directory. Exiting.";
            exit(1);
        }
    }

    if (!copy("airtime.conf.170", CONF_FILE_AIRTIME)){
        echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/pypo/pypo.cfg", CONF_FILE_PYPO)){
        echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/show-recorder/recorder.cfg", CONF_FILE_RECORDER)){
        echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy($AIRTIME_PYTHON_APPS."/pypo/liquidsoap_scripts/liquidsoap.cfg", CONF_FILE_LIQUIDSOAP)){
        echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
}

CreateIniFiles();
AirtimeIni::UpdateIniFiles();

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110402164819');

echo PHP_EOL."*** Updating Pypo ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/pypo/install/pypo-install.py");

echo PHP_EOL."*** Recorder Installation ***".PHP_EOL;
system("python ".__DIR__."/../../../python_apps/show-recorder/install/recorder-install.py");

