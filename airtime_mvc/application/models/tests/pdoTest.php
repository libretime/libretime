<?php
require_once(__DIR__.'/../../3rd_party/php/propel/runtime/lib/Propel.php');
// Initialize Propel with the runtime configuration

//Example how to use PDO:
//Propel::init(__DIR__."/../propel-db/build/conf/airtime-conf.php");

//Add the generated 'classes' directory to the include path
set_include_path(__DIR__."/../propel-db/build/classes" . PATH_SEPARATOR . get_include_path());
$con = Propel::getConnection("campcaster");

$sql = "SELECT COUNT(*) FROM cc_schedule WHERE (starts >= '2010-01-01 00:00:00.000') "
        ." AND (ends <= (TIMESTAMP '2011-01-01 00:00:00.000' + INTERVAL '01:00:00.123456'))";
$rows1 = $con->query($sql);
var_dump($rows1->fetchAll());

$sql2 = "SELECT COUNT(*) FROM cc_playlistcontents";
$rows2 = $con->query($sql2);
var_dump($rows2->fetchAll());

$sql3 = "SELECT TIMESTAMP '2011-01-01 00:00:00.000' + INTERVAL '01:00:00.123456'";
$result3 = $con->query($sql3);
var_dump($result3->fetchAll());


