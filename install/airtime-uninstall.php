<?php
/**
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

require_once(dirname(__FILE__).'/include/AirtimeIni.php');
require_once(dirname(__FILE__).'/include/AirtimeInstall.php');
// Need to check that we are superuser before running this.
AirtimeInstall::ExitIfNotRoot();

if (!file_exists(AirtimeIni::CONF_FILE_AIRTIME)) {
    echo PHP_EOL."Airtime config file '".AirtimeIni::CONF_FILE_AIRTIME."' does not exist.".PHP_EOL;
    echo "Most likely this means that Airtime is not installed, so there is nothing to do.".PHP_EOL.PHP_EOL;
    exit();
}
require_once(AirtimeInstall::GetAirtimeSrcDir().'/application/configs/conf.php');

echo PHP_EOL;
echo "******************************* Uninstall Begin ********************************".PHP_EOL;
echo "* Uninstalling Airtime ".AIRTIME_VERSION.PHP_EOL;
AirtimeInstall::UninstallPhpCode();

//------------------------------------------------------------------------
// Delete the database
// Note: Do not put a call to AirtimeInstall::DbConnect()
// before this function, even if you called $CC_DBC->disconnect(), there will
// still be a connection to the database and you wont be able to delete it.
//------------------------------------------------------------------------
echo " * Dropping the database '".$CC_CONFIG['dsn']['database']."'...".PHP_EOL;

// check if DB exists
$command = "echo \"DROP DATABASE IF EXISTS ".$CC_CONFIG['dsn']['database']."\" | su postgres -c psql";

@exec($command, $output, $dbDeleteFailed);

//------------------------------------------------------------------------
// Delete DB tables
// We do this if dropping the database fails above.
//------------------------------------------------------------------------
if ($dbDeleteFailed) {
    echo " * Couldn't delete the database, so deleting all the DB tables...".PHP_EOL;
    AirtimeInstall::DbConnect(false);

    if (!PEAR::isError($CC_DBC)) {
        $sql = "select * from pg_tables where tableowner = 'airtime'";
        $rows = $CC_DBC->GetAll($sql);
        if (PEAR::isError($rows)) {
            $rows = array();
        }

        foreach ($rows as $row) {
            $tablename = $row["tablename"];
            echo "   * Removing database table $tablename...";

            if (AirtimeInstall::DbTableExists($tablename)){
                $sql = "DROP TABLE $tablename CASCADE";
                AirtimeInstall::InstallQuery($sql, false);

                $CC_DBC->dropSequence($tablename."_id");
            }
            echo "done.".PHP_EOL;
        }
    }
}

//------------------------------------------------------------------------
// Delete the user
//------------------------------------------------------------------------
echo " * Deleting database user '{$CC_CONFIG['dsn']['username']}'...".PHP_EOL;
$command = "echo \"DROP USER IF EXISTS {$CC_CONFIG['dsn']['username']}\" | su postgres -c psql";
@exec($command, $output, $results);
if ($results == 0) {
    echo "   * User '{$CC_CONFIG['dsn']['username']}' deleted.".PHP_EOL;
} else {
    echo "   * Nothing to delete.".PHP_EOL;
}

# Disabled as this should be a manual process
#AirtimeInstall::DeleteFilesRecursive(AirtimeInstall::CONF_DIR_STORAGE);

echo PHP_EOL."*** Uninstalling Pypo ***".PHP_EOL;
$command = "python ".__DIR__."/../python_apps/pypo/install/pypo-uninstall.py";
system($command);

echo PHP_EOL."*** Uninstalling Show Recorder ***".PHP_EOL;
$command = "python ".__DIR__."/../python_apps/show-recorder/install/recorder-uninstall.py";
system($command);

echo PHP_EOL."*** Uninstalling Media Monitor ***".PHP_EOL;
$command = "python ".__DIR__."/../python_apps/pytag-fs/install/media-monitor-uninstall.py";
system($command);

#Disabled as this should be a manual process
#AirtimeIni::RemoveIniFiles();

AirtimeInstall::RemoveSymlinks();
AirtimeInstall::UninstallBinaries();
AirtimeInstall::RemoveLogDirectories();

echo PHP_EOL;
echo "****************************** Uninstall Complete ******************************".PHP_EOL;
echo PHP_EOL;
echo "NOTE: To fully remove all Airtime files, you will also have to manually delete".PHP_EOL;
echo "      the directories '".AirtimeInstall::CONF_DIR_STORAGE."'(where the media files are stored)".PHP_EOL;
echo "      and '/etc/airtime'(where the config files are stored).".PHP_EOL;
echo PHP_EOL;
