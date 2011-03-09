<?php
/**
 * @package Airtime
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

// Do not allow remote execution.
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable".PHP_EOL;
    exit;
}

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/installInit.php');

// Need to check that we are superuser before running this.
checkIfRoot();


echo "******************************* Uninstall Begin ********************************".PHP_EOL;



function airtime_uninstall_delete_files($p_path)
{
	$command = "rm -rf $p_path";
	exec($command);
}

//------------------------------------------------------------------------
// Delete the database
// Note: Do not put a call to airtime_db_connect()
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
  airtime_db_connect(true);
  if (!PEAR::isError($CC_DBC)) {
      if (airtime_db_table_exists($CC_CONFIG['prefTable'])) {
          echo "   * Removing database table ".$CC_CONFIG['prefTable']."...";
          $sql = "DROP TABLE ".$CC_CONFIG['prefTable'];
          airtime_install_query($sql, false);

          $CC_DBC->dropSequence($CC_CONFIG['prefTable']."_id");
          echo "done.".PHP_EOL;
      } else {
          echo "   * Skipping: database table $CC_CONFIG[prefTable]".PHP_EOL;
      }
  }

  if (airtime_db_table_exists($CC_CONFIG['transTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['transTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['transTable'];
      airtime_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['transTable']."_id");
      echo "done.".PHP_EOL;
  } else {
      echo "   * Skipping: database table $CC_CONFIG[transTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['filesTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['filesTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['filesTable']." CASCADE";
      airtime_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['filesTable']."_id");

  } else {
      echo "   * Skipping: database table $CC_CONFIG[filesTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['playListTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['playListTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['playListTable']." CASCADE";
      airtime_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['playListTable']."_id");

  } else {
      echo "   * Skipping: database table $CC_CONFIG[playListTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['playListContentsTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['playListContentsTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['playListContentsTable'];
      airtime_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['playListContentsTable']."_id");

  } else {
      echo "   * Skipping: database table $CC_CONFIG[playListContentsTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['accessTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['accessTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['accessTable'];
      airtime_install_query($sql);
  } else {
      echo "   * Skipping: database table $CC_CONFIG[accessTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['permTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['permTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['permTable'];
      airtime_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['permTable']."_id");
      echo "done.".PHP_EOL;
  } else {
      echo "   * Skipping: database table $CC_CONFIG[permTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['sessTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['sessTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['sessTable'];
      airtime_install_query($sql);
  } else {
      echo "   * Skipping: database table $CC_CONFIG[sessTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['subjTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['subjTable']."...";
      $CC_DBC->dropSequence($CC_CONFIG['subjTable']."_id");

      $sql = "DROP TABLE ".$CC_CONFIG['subjTable']." CASCADE";
      airtime_install_query($sql, false);

      echo "done.".PHP_EOL;
  } else {
      echo "   * Skipping: database table $CC_CONFIG[subjTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['smembTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['smembTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['smembTable'];
      airtime_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['smembTable']."_id");
      echo "done.".PHP_EOL;
  } else {
      echo "   * Skipping: database table $CC_CONFIG[smembTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['scheduleTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['scheduleTable']."...";
      airtime_install_query("DROP TABLE ".$CC_CONFIG['scheduleTable']);
  } else {
      echo "   * Skipping: database table $CC_CONFIG[scheduleTable]".PHP_EOL;
  }

  if (airtime_db_table_exists($CC_CONFIG['backupTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['backupTable']."...";
      airtime_install_query("DROP TABLE ".$CC_CONFIG['backupTable']);
  } else {
      echo "   * Skipping: database table $CC_CONFIG[backupTable]".PHP_EOL;
  }
}

//Delete Database
//system("dropdb -h localhost -U airtime -W airtime");
//select * from pg_stat_activity where datname='airtime';
/*
$rows = airtime_get_query("select procpid from pg_stat_activity where datname='airtime'");
$rowsCount = count($rows);
for ($i=0; $i<$rowsCount; $i++){
    $command = "kill -2 {$rows[$i]['procpid']}";
    echo $command.PHP_EOL;
    system($command);
}
echo "still here!";
system("dropdb -h localhost -U airtime -W airtime");
exit;
*/


//------------------------------------------------------------------------
// Delete the user
//------------------------------------------------------------------------
echo " * Deleting database user '{$CC_CONFIG['dsn']['username']}'...".PHP_EOL;
$command = "sudo -u postgres psql postgres --command \"DROP USER {$CC_CONFIG['dsn']['username']}\" 2> /dev/null";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * User '{$CC_CONFIG['dsn']['username']}' deleted.".PHP_EOL;
} else {
  echo "   * Nothing to delete..".PHP_EOL;
}


//------------------------------------------------------------------------
// Delete files
//------------------------------------------------------------------------
airtime_uninstall_delete_files($CC_CONFIG['storageDir']);


$command = "python ".__DIR__."/../pypo/install/pypo-uninstall.py";
system($command);
echo "****************************** Uninstall Complete ******************************".PHP_EOL;

