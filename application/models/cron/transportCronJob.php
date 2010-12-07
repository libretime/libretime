#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/../conf.php');
require_once('DB.php');
require_once(dirname(__FILE__).'/../LocStor.php');

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

$gb = new LocStor();
$tr = new Transport($gb);

$pid = getmypid();
list(, $trtok) = $_SERVER['argv'];
if (TR_LOG_LEVEL > 1) {
	$tr->trLog("transportCronJob($pid) start ($trtok)");
}

// 4-pass on job:
$cnt = 4;
for ($i = 0; $i < $cnt; $i++, sleep(1)) {
    // run the action:
    $r = $tr->cronCallMethod($trtok);
    if (PEAR::isError($r)) {
        $tr->trLogPear("transportCronJob($pid): ($trtok): ", $r);
    } else {
#        $tr->trLog("X transportCronJob: ".var_export($r, TRUE));
        if ($r !== TRUE) {
        	$tr->trLog("transportCronJob($pid): ($trtok): nonTRUE returned");
        }
    }
    #if(!$r) exit(1);
    #sleep(2);
}

if (TR_LOG_LEVEL>1) {
	$tr->trLog("transportCronJob($pid) end ($trtok)");
}
exit(0);
?>