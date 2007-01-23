<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
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

echo "*************************\n";
echo "* StorageServer Install *\n";
echo "*************************\n";

require_once('../conf.php');
require_once('../GreenBox.php');
require_once("installInit.php");
campcaster_db_connect(true);
require_once('installMain.php');
require_once('installStorage.php');

echo "**********************************\n";
echo "* StorageServer Install Complete *\n";
echo "**********************************\n";

?>