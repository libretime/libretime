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
    echo "400 Not executable\r\n";
    exit;
}

// Need to check that we are superuser before running this.
if(exec("whoami") != "root"){
  echo "Must be root user.\n";
  exit(1);
}


echo "******************************* Uninstall Begin ********************************\n";

require_once(dirname(__FILE__).'/../application/configs/conf.php');
require_once(dirname(__FILE__).'/installInit.php');

function airtime_uninstall_delete_files($p_path)
{
	$command = "rm -rf $p_path";
	exec($command);
}

//------------------------------------------------------------------------
// Delete the database
// Note: Do not put a call to campcaster_db_connect()
// before this function, even if you called $CC_DBC->disconnect(), there will
// still be a connection to the database and you wont be able to delete it.
//------------------------------------------------------------------------
echo " * Dropping the database '".$CC_CONFIG['dsn']['database']."'...\n";
$command = "sudo -u postgres dropdb {$CC_CONFIG['dsn']['database']} 2> /dev/null";
@exec($command, $output, $dbDeleteFailed);

//------------------------------------------------------------------------
// Delete DB tables
// We do this if dropping the database fails above.
//------------------------------------------------------------------------
if ($dbDeleteFailed) {
  echo " * Couldn't delete the database, so deleting all the DB tables...\n";
  campcaster_db_connect(true);
  if (!PEAR::isError($CC_DBC)) {
      if (camp_db_table_exists($CC_CONFIG['prefTable'])) {
          echo "   * Removing database table ".$CC_CONFIG['prefTable']."...";
          $sql = "DROP TABLE ".$CC_CONFIG['prefTable'];
          camp_install_query($sql, false);

          $CC_DBC->dropSequence($CC_CONFIG['prefTable']."_id");
          echo "done.\n";
      } else {
          echo "   * Skipping: database table ".$CC_CONFIG['prefTable']."\n";
      }
  }

  if (camp_db_table_exists($CC_CONFIG['transTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['transTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['transTable'];
      camp_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['transTable']."_id");
      echo "done.\n";
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['transTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['filesTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['filesTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['filesTable']." CASCADE";
      camp_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['filesTable']."_id");

  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['filesTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['playListTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['playListTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['playListTable']." CASCADE";
      camp_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['playListTable']."_id");

  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['playListTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['playListContentsTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['playListContentsTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['playListContentsTable'];
      camp_install_query($sql);
      $CC_DBC->dropSequence($CC_CONFIG['playListContentsTable']."_id");

  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['playListContentsTable']."\n";
  }

  //if (camp_db_sequence_exists($CC_CONFIG['filesSequence'])) {
  //    $sql = "DROP SEQUENCE ".$CC_CONFIG['filesSequence'];
  //    camp_install_query($sql);
  //}
  //
  if (camp_db_table_exists($CC_CONFIG['accessTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['accessTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['accessTable'];
      camp_install_query($sql);
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['accessTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['permTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['permTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['permTable'];
      camp_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['permTable']."_id");
      echo "done.\n";
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['permTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['sessTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['sessTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['sessTable'];
      camp_install_query($sql);
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['sessTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['subjTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['subjTable']."...";
      $CC_DBC->dropSequence($CC_CONFIG['subjTable']."_id");

      $sql = "DROP TABLE ".$CC_CONFIG['subjTable']." CASCADE";
      camp_install_query($sql, false);

      echo "done.\n";
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['subjTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['smembTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['smembTable']."...";
      $sql = "DROP TABLE ".$CC_CONFIG['smembTable'];
      camp_install_query($sql, false);

      $CC_DBC->dropSequence($CC_CONFIG['smembTable']."_id");
      echo "done.\n";
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['smembTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['scheduleTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['scheduleTable']."...";
      camp_install_query("DROP TABLE ".$CC_CONFIG['scheduleTable']);
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['scheduleTable']."\n";
  }

  if (camp_db_table_exists($CC_CONFIG['backupTable'])) {
      echo "   * Removing database table ".$CC_CONFIG['backupTable']."...";
      camp_install_query("DROP TABLE ".$CC_CONFIG['backupTable']);
  } else {
      echo "   * Skipping: database table ".$CC_CONFIG['backupTable']."\n";
  }
}


//------------------------------------------------------------------------
// Uninstall Cron job
//------------------------------------------------------------------------
/*
$old_regex = '/transportCron\.php/';
echo " * Uninstalling cron job...";

$cron = new Cron();
$access = $cron->openCrontab('write');
if ($access != 'write') {
    do {
       $r = $cron->forceWriteable();
    } while ($r);
}
foreach ($cron->ct->getByType(CRON_CMD) as $id => $line) {
    if (preg_match($old_regex, $line['command'])) {
        //echo "    removing cron entry\n";
        $cron->ct->delEntry($id);
    }
}
$cron->closeCrontab();
echo "done.\n";
*/


//------------------------------------------------------------------------
// Disconnect from the database
//------------------------------------------------------------------------
//echo " * Disconnecting from database...\n";
//$CC_DBC->disconnect();

//------------------------------------------------------------------------
// Delete the user
//------------------------------------------------------------------------
echo " * Deleting database user '{$CC_CONFIG['dsn']['username']}'...\n";
$command = "sudo -u postgres psql postgres --command \"DROP USER {$CC_CONFIG['dsn']['username']}\" 2> /dev/null";
@exec($command, $output, $results);
if ($results == 0) {
  echo "   * User '{$CC_CONFIG['dsn']['username']}' deleted.\n";
} else {
  echo "   * Nothing to delete..\n";
}


//------------------------------------------------------------------------
// Delete files
//------------------------------------------------------------------------
airtime_uninstall_delete_files($CC_CONFIG['storageDir']);


$command = "python ".__DIR__."/../pypo/install/pypo-uninstall.py";
system($command);
echo "****************************** Uninstall Complete ******************************\n";

