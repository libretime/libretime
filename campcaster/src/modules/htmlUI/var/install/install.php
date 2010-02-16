<?php
// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

//------------------------------------------------------------------------
// Install twitter Cron job
//------------------------------------------------------------------------
require_once(dirname(__FILE__).'/../../../storageServer/var/cron/Cron.php');
$command = '/usr/bin/php '.realpath(dirname(__FILE__).'/../html/ui_twitterCron.php');

$cron = new Cron();
$access = $cron->openCrontab('write');
if ($access != 'write') {
    do {
       $r = $cron->forceWriteable();
    } while ($r);
}

foreach ($cron->ct->getByType(CRON_CMD) as $line) {
    if (preg_match('/ui_twitterCron\.php/', $line['command'])) {
        $cron->closeCrontab();
        echo " * Twitter cron job already exists.\n";
        exit;
    }
}
echo " * Adding twitter cron job...";
$cron->ct->addCron('*', '*', '*', '*', '*', $command);
$cron->closeCrontab();
echo "Done\n";
?>
