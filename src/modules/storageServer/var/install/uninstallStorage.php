<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 2458 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */

// Do not allow remote execution.
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit;
}

function camp_uninstall_delete_files($p_path)
{
    if (!empty($p_path) && (strlen($p_path) > 4)) {
        list($dirList,$fileList) = File_Find::maptree($p_path);
        foreach ($fileList as $filepath) {
            echo " * Removing $filepath...";
            @unlink($filepath);
            echo "done.\n";
        }
        foreach ($dirList as $dirpath) {
            echo " * Removing $dirpath...";
            @rmdir($dirpath);
            echo "done.\n";
        }
    }
}

if (!PEAR::isError($CC_DBC)) {
    if (camp_db_table_exists($CC_CONFIG['prefTable'])) {
        echo " * Removing database table ".$CC_CONFIG['prefTable']."...";
        $sql = "DROP TABLE ".$CC_CONFIG['prefTable'];
        camp_install_query($sql, false);

        $CC_DBC->dropSequence($CC_CONFIG['prefTable']."_id_seq");
        echo "done.\n";
    } else {
        echo " * Skipping: database table ".$CC_CONFIG['prefTable']."\n";
    }
}

//------------------------------------------------------------------------
// Uninstall Cron job
//------------------------------------------------------------------------
require_once(dirname(__FILE__).'/../cron/Cron.php');
$old_regex = '/transportCron\.php/';
echo " * Uninstall storageServer cron job...\n";

$cron = new Cron();
$access = $cron->openCrontab('write');
if ($access != 'write') {
    do {
       $r = $cron->forceWriteable();
    } while ($r);
}

foreach ($cron->ct->getByType(CRON_CMD) as $id => $line) {
    if (preg_match($old_regex, $line['command'])) {
        echo "    removing cron entry\n";
        $cron->ct->delEntry($id);
    }
}

$cron->closeCrontab();
echo "Done.\n";

camp_uninstall_delete_files($CC_CONFIG['storageDir']);
camp_uninstall_delete_files($CC_CONFIG['transDir']);
camp_uninstall_delete_files($CC_CONFIG['accessDir']);

?>