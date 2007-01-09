<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 2774 $
 * @package Campcaster
 * @subpackage Scheduler
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


require_once('installInit.php');
require_once('uninstallScheduler.php');

echo " * Scheduler uninstall complete\n";

?>