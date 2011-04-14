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
require_once(dirname(__FILE__).'/../application/configs/conf.php');


AirtimeInstall::RemoveSymlinks();

echo "Uninstalling Airtime ".AIRTIME_VERSION.PHP_EOL;

echo "******************************* Uninstall Begin ********************************".PHP_EOL;
//------------------------------------------------------------------------
// Delete the database
// Note: Do not put a call to AirtimeInstall::DbConnect()
// before this function, even if you called $CC_DBC->disconnect(), there will
// still be a connection to the database and you wont be able to delete it.
//------------------------------------------------------------------------
echo " * Dropping the database '".$CC_CONFIG['dsn']['database']."'...".PHP_EOL;
$command = "sudo -u postgres dropdb {$CC_CONFIG['dsn']['database']} 2> /dev/null";
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
$command = "sudo -u postgres psql postgres --command \"DROP USER {$CC_CONFIG['dsn']['username']}\" 2> /dev/null";
@exec($command, $output, $results);
if ($results == 0) {
    echo "   * User '{$CC_CONFIG['dsn']['username']}' deleted.".PHP_EOL;
} else {
    echo "   * Nothing to delete.".PHP_EOL;
}


//------------------------------------------------------------------------
// Delete files
//------------------------------------------------------------------------
AirtimeInstall::DeleteFilesRecursive($CC_CONFIG['storageDir']);
AirtimeIni::RemoveIniFiles();

$command = "python ".__DIR__."/../python_apps/pypo/install/pypo-uninstall.py";
system($command);

$command = "python ".__DIR__."/../python_apps/show-recorder/install/recorder-uninstall.py";
system($command);
echo "****************************** Uninstall Complete ******************************".PHP_EOL;

