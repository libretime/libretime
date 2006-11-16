<?php
/**
 * @author $Author$
 * @version $Revision$
 */

// no remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && $arr["DOCUMENT_ROOT"] != "") {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

require_once '../conf.php';
require_once 'DB.php';
require_once '../GreenBox.php';
require_once "../Transport.php";
require_once "../Prefs.php";

function errCallback($err)
{
    if (assert_options(ASSERT_ACTIVE) == 1) {
        return;
    }
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo().
        "\nui:\n".$err->getUserInfo()."\n";
    exit(1);
}

if (!function_exists('pg_connect')) {
  trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
  exit(2);
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
if (PEAR::isError($dbc)) {
    echo $dbc->getMessage()."\n";
    echo $dbc->getUserInfo()."\n";
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in var/conf.php ".
        "(in storageServer directory).\n";
    exit(1);
}

$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new GreenBox($dbc, $config, TRUE);
$tr = new Transport($gb);
$pr = new Prefs($gb);

//------------------------------------------------------------------------------
// install
//------------------------------------------------------------------------------

echo "#StorageServer install:\n";
echo "# Install ...\n";
#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_DIE, "%s\n");
#$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$r = $gb->install();
if(PEAR::isError($r)){ echo $r->getMessage()."\n"; echo $r->getUserInfo()."\n"; exit(1); }
#if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit(1); }

//------------------------------------------------------------------------------
// Storage directory writability test
//------------------------------------------------------------------------------

if(!($fp = @fopen($config['storageDir']."/_writeTest", 'w'))){
    echo "\n<b>make {$config['storageDir']} dir webdaemon-writeable</b>".
        "\nand run install again\n\n";
    exit(1);
}else{
    fclose($fp); unlink($config['storageDir']."/_writeTest");
    echo "#storageServer main: OK\n";
}

//------------------------------------------------------------------------------
// Submodules
//------------------------------------------------------------------------------

$dbc->setErrorHandling(PEAR_ERROR_RETURN);
echo "# Install Transport submodule ...";
$r = $tr->install();
if(PEAR::isError($r)){ echo $r->getMessage()."\n"; echo $r->getUserInfo()."\n"; exit(1); }
echo "\n";

echo "# Install Prefs submodule ...";
$r = $pr->install();
if(PEAR::isError($r)){ echo $r->getUserInfo()."\n"; exit(1); }
echo "\n";

echo "# submodules: OK\n";

//------------------------------------------------------------------------------
// Cron configuration
//------------------------------------------------------------------------------

echo "# Cron configuration ...";
require_once dirname(__FILE__).'/../cron/Cron.php';
$cron = new Cron();
$access = $r = $cron->openCrontab('write');
if ($access != 'write') {
    do {
       $r = $this->forceWriteable();
    } while ($r);
}
$cron->ct->addCron('*/2', '*', '*', '*', '*', realpath("{$config['cronDir']}/transportCron.php"));
$r = $cron->closeCrontab();
echo "# cron config: OK\n";

echo "#storageServer:  OK\n\n";

$dbc->disconnect();
?>
