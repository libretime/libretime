<?php
// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

?>

//------------------------------------------------------------------------
// Uninstall twitter Cron job
//------------------------------------------------------------------------
<?php
require_once(dirname(__FILE__).'/../../../storageServer/var/cron/Cron.php');
$old_regex = '/ui_twitterCron\.php/';

$cron = new Cron();
$access = $cron->openCrontab('write');
if ($access != 'write') {
    do {
       $r = $cron->forceWriteable();
    } while ($r);
}

foreach ($cron->ct->getByType(CRON_CMD) as $id => $line) {
    if (preg_match($old_regex, $line['command'])) {
        echo "    removing cron entry\n";
        $cron->ct->delEntry($id);
    }
}

$cron->closeCrontab();
echo "Done.\n";
?>