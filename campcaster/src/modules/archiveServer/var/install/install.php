<?php
/**
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage ArchiveServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 *
 */

// no remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && $arr["DOCUMENT_ROOT"] != "") {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

echo "*************************\n";
echo "* ArchiveServer Install *\n";
echo "*************************\n";

require_once('../conf.php');
require_once('../../../storageServer/var/GreenBox.php');
require_once('../../../storageServer/var/install/installInit.php');
campcaster_db_connect();
require_once('../../../storageServer/var/install/installMain.php');

echo "**********************************\n";
echo "* ArchiveServer Install Complete *\n";
echo "**********************************\n";

?>