<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../../../application/configs/conf.php';
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');

AirtimeInstall::DbConnect(true);

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;
AirtimeInstall::MigrateTables(__DIR__, '20110406182005');

//setting data for new aggregate show length column.
$sql = "SELECT id FROM cc_show_instances";
$show_instances = $CC_DBC->GetAll($sql);

foreach ($show_instances as $show_instance) {
    $sql = "UPDATE cc_show_instances SET time_filled = (SELECT SUM(clip_length) FROM cc_schedule WHERE instance_id = {$show_instance}) WHERE id = {$show_instance}";
    $CC_DBC->query($sql);
}
//end setting data for new aggregate show length column.


