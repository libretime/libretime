<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../../../airtime_mvc/application/configs/conf.php';
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');

AirtimeInstall::DbConnect(true);

echo PHP_EOL."*** Updating Database Tables ***".PHP_EOL;

if(AirtimeInstall::DbTableExists('doctrine_migration_versions') === false) {
    $migrations = array('20110312121200', '20110331111708', '20110402164819');
    foreach($migrations as $migration) {
        AirtimeInstall::BypassMigrations(__DIR__, $migration);
    }
}
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110406182005');

//setting data for new aggregate show length column.
$sql = "SELECT id FROM cc_show_instances";
$show_instances = $CC_DBC->GetAll($sql);

foreach ($show_instances as $show_instance) {
    $sql = "UPDATE cc_show_instances SET time_filled = (SELECT SUM(clip_length) FROM cc_schedule WHERE instance_id = {$show_instance["id"]}) WHERE id = {$show_instance["id"]}";
    $CC_DBC->query($sql);
}
//end setting data for new aggregate show length column.


