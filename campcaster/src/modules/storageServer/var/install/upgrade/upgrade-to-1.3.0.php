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
echo "* StorageServer Upgrade from 1.2.X to 1.3.0 *\n";
echo "*********************************************\n";

require_once(dirname(__FILE__).'/../../conf.php');
require_once(dirname(__FILE__)."/../installInit.php");
campcaster_db_connect();
require_once(dirname(__FILE__)."/../../StoredFile.php");

// Move audio clips from the archive to the local storage

echo "*******************************************\n";
echo "* StorageServer Upgrade to 1.3.0 Complete *\n";
echo "*******************************************\n";

?>