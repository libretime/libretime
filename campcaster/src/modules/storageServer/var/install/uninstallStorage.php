<?php
/**
 * @author Tomas Hlava <th@red2head.com>
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


if (camp_db_table_exists($CC_CONFIG['prefTable'])) {
    echo " * Removing database table ".$CC_CONFIG['prefTable']."...";
    $CC_DBC->query("DROP TABLE ".$CC_CONFIG['prefTable']);
    $CC_DBC->dropSequence($CC_CONFIG['prefTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['prefTable']."\n";
}


echo " * Removing all media files in ".$CC_CONFIG['storageDir']."...";
$d = @dir($CC_CONFIG['storageDir']);
while (is_object($d) && (false !== ($entry = $d->read()))){
    if (filetype($CC_CONFIG['storageDir']."/$entry") == 'dir') {
        if ( ($entry != 'CVS') && ($entry != 'tmp') && (strlen($entry)==3) ) {
            $dd = dir($CC_CONFIG['storageDir']."/$entry");
            while (false !== ($ee = $dd->read())) {
                if (substr($ee, 0, 1) !== '.') {
                    $filename = $CC_CONFIG['storageDir']."/$entry/$ee";
                    echo "   * Removing $filename...";
                    unlink($filename);
                    echo "done.\n";
                }
            }
            $dd->close();
            rmdir($CC_CONFIG['storageDir']."/$entry");
        }
    }
}
if (is_object($d)) {
    $d->close();
}
echo "done.\n";

//echo " * Removing all temporary files in ".$CC_CONFIG['bufferDir']."...";
//if (file_exists($CC_CONFIG['bufferDir'])) {
//    $d = dir($CC_CONFIG['bufferDir']);
//    while (false !== ($entry = $d->read())) {
//        if (substr($entry, 0, 1) != '.') {
//            unlink($CC_CONFIG['bufferDir']."/$entry");
//        }
//    }
//    $d->close();
//    @rmdir($this->bufferDir);
//}
//echo "done.\n";


?>