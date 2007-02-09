<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 2834 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 *
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

echo "*********************************************\n";
echo "* StorageServer Upgrade from 1.1.X to 1.2.0 *\n";
echo "*********************************************\n";

require_once(dirname(__FILE__).'/../../conf.php');
require_once(dirname(__FILE__)."/../installInit.php");
campcaster_db_connect();
require_once(dirname(__FILE__)."/../../StoredFile.php");

// Check to see if upgrade has already been applied
$sql = "SELECT md5 FROM ".$CC_CONFIG['filesTable']." LIMIT 1";
$result = $CC_DBC->query($sql);
if (!PEAR::isError($result)) {
    echo " * THIS UPGRADE HAS ALREADY BEEN APPLIED.\n";
    exit(0);
}

echo " * Adding column 'md5' to '".$CC_CONFIG['filesTable']." table...";
$sql = "ALTER TABLE ".$CC_CONFIG['filesTable']." ADD COLUMN md5 char(32)";
camp_install_query($sql, false);
$sql = "ALTER TABLE ".$CC_CONFIG['filesTable']." ALTER COLUMN md5 SET STORAGE EXTENDED";
camp_install_query($sql);

echo " * Creating index on column 'md5'...";
$sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_md5_idx ON ".$CC_CONFIG['filesTable']." (md5)";
camp_install_query($sql);

echo " * Converting metadata values 'ls:genre' to 'dc:type'...";
$sql = "UPDATE ".$CC_CONFIG['mdataTable']." SET predns='dc', predicate='type' WHERE predns='ls' and predicate='genre'";
camp_install_query($sql);

echo " * Adding 'jobpid' to ".$CC_CONFIG['transTable']."...";
$sql = "ALTER TABLE ".$CC_CONFIG['transTable']." ADD COLUMN jobpid int";
camp_install_query($sql);

echo " * Fixing track numbers...\n";
$sql = "SELECT id, object as track_num FROM ".$CC_CONFIG['mdataTable']
    ." WHERE predns='ls' AND predicate='track_num'";
$rows = $CC_DBC->GetAll($sql);
foreach ($rows as $row) {
    $newTrackNum = camp_parse_track_number($row["track_num"]);
    if ($row["track_num"] != $newTrackNum) {
        echo "   * Converting '".$row["track_num"]."' --> '$newTrackNum'\n";
        $sql = "UPDATE ".$CC_CONFIG["mdataTable"]
            ." SET object='$newTrackNum'"
            ." WHERE id=".$row["id"];
        $CC_DBC->query($sql);
    }
}

// Get MD5 values for all files
echo " * Computing MD5 sums for all files (this may take a while)...\n";
$sql = "SELECT to_hex(gunid) as gunid, name FROM ".$CC_CONFIG['filesTable'] ." WHERE ftype='audioclip'";
$rows = $CC_DBC->GetAll($sql);
$errorFiles = array();
foreach ($rows as $row) {
    $gunid = StoredFile::NormalizeGunid($row['gunid']);
    $storedFile = new StoredFile($gunid);
    $fileName = $storedFile->getRealFileName();
    $humanName = basename($row['name']);
    echo "   File: $humanName\n";
    if (file_exists($fileName)) {
        $md5 = md5_file($fileName);
        $storedFile->setMd5($md5);
        //echo "         MD5: $md5\n";
    } else {
        $errorFiles[] = "$gunid -- $humanName";
        echo "         ERROR: file does not exist! (GUNID: $gunid)\n";
    }
}

if (count($errorFiles) > 0) {
    echo "\n\nWARNING\n";
    echo "The following files were not found:\n";
    foreach ($errorFiles as $file) {
        echo $file."\n";
    }
}
echo "*******************************************\n";
echo "* StorageServer Upgrade to 1.2.0 Complete *\n";
echo "*******************************************\n";

?>
