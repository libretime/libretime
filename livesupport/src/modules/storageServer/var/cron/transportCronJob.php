#!/usr/bin/php
<?php
require_once dirname(__FILE__).'/../conf.php';
require_once 'DB.php';
require_once dirname(__FILE__).'/../LocStor.php';

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);

$gb =& new LocStor($dbc, $config);
$tr =& new Transport($gb);

list(, $trtok) = $_SERVER['argv'];
if(TR_LOG_LEVEL>1){ $tr->trLog("transportCronJob start ($trtok)"); }

// 4-pass on job:
$cnt = 4;
for($i=0; $i<$cnt; $i++, sleep(1)){
    // run the action:
    $r = $tr->cronCallMethod($trtok);
    if(PEAR::isError($r)){
        $tr->trLogPear("transportCronJob: ($trtok): ", $r);
    }else{
#        $tr->trLog("X transportCronJob: ".var_export($r, TRUE));
        if($r!==TRUE) $tr->trLog("transportCronJob: ($trtok): nonTRUE returned");
    }
    #if(!$r) exit(1);
    #sleep(2);
}

if(TR_LOG_LEVEL>1){ $tr->trLog("transportCronJob end ($trtok)"); }
exit(0);
?>