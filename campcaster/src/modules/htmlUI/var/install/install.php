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
// Install twitter Cron job
//------------------------------------------------------------------------
<?php
require_once(dirname(__FILE__).'/../../../storageServer/var/cron/Cron.php');
$m = '*';
$h ='*';
$dom = '*';
$mon = '*';
$dow = '*';
$command = '/usr/bin/php '.realpath(dirname(__FILE__).'/../ui_twitterCron.php').' >/dev/null 2>&1';
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
        echo "    removing old entry\n";
        $cron->ct->delEntry($id);
    }
}
echo "    adding new entry\n";
$cron->ct->addCron($m, $h, $dom, $mon, $dow, $command);
$cron->closeCrontab();
echo "Done.\n";
?>
