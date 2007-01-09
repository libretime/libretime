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
 * Note: This file was broken into two parts: install.php and
 * installMain.php so that the archive server could use the same
 * installation script, but with just a different config file.
 */

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

echo "**********************************\n";
echo "* StorageServer Upgrade to 1.2.0 *\n";
echo "**********************************\n";

require_once('../../conf.php');
require_once("../installInit.php");
require_once("../../StoredFile.php");

// Check to see if upgrade has already been applied
//$sql = "SELECT md5 FROM ".$CC_CONFIG['filesTable']." LIMIT 1";
//$result = $CC_DBC->query($sql);
//if (!PEAR::isError($result)) {
//    echo "THIS UPGRADE HAS ALREADY BEEN APPLIED.\n";
//    exit(0);
//}
//
//echo " * Modifying '".$CC_CONFIG['filesTable']." table...";
//$sql = "ALTER TABLE ".$CC_CONFIG['filesTable']." ADD COLUMN md5 char(32)";
//camp_install_query($sql);
//
//$sql = "ALTER TABLE ".$CC_CONFIG['filesTable']." ALTER COLUMN md5 SET STORAGE EXTENDED";
//camp_install_query($sql);
//
//$sql = "CREATE INDEX ".$CC_CONFIG['filesTable']."_md5_idx ON ".$CC_CONFIG['filesTable']." (md5)";
//camp_install_query($sql);

// Get MD5 values for all files
$sql = "SELECT gunid FROM ".$CC_CONFIG['filesTable'] ." WHERE ftype='audioclip'";
$rows = $CC_DBC->GetAll($sql);
foreach ($rows as $row) {
    echo $row['gunid']."\n";
    $gunid = StoredFile::NormalizeGunid($gunid);
    $storedFile = new StoredFile($row['gunid']);
    $fileName = $storedFile->getRealFileName();
    echo $fileName."\n";
    if (file_exists($fileName)) {
        $md5 = md5_file($fileName);
        $storedFile->setMd5($md5);
    }
}


echo "**********************************\n";
echo "* StorageServer Install Complete *\n";
echo "**********************************\n";

?>