<?
// $Id: alib_h.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
session_start();
require_once 'conf.php';
require_once 'DB.php';
require_once '../alib.php';

#PEAR::setErrorHandling(PEAR_ERROR_RETURN);
#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
#PEAR::setErrorHandling(PEAR_ERROR_TRIGGER, E_USER_WARNING);
PEAR::setErrorHandling(PEAR_ERROR_DIE);
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallback');

function errCallback($err)
{
	if(assert_options(ASSERT_ACTIVE)==1) return;
	echo "<pre>\n";
	echo "request: "; print_r($_REQUEST);
    echo "\ngm:\n".$err->getMessage()."\nui:\n".$err->getUserInfo()."\n";
    echo "<hr>BackTrace:\n";
    print_r($err->backtrace);
    echo "</pre>\n";
    exit;
}

$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$alib =& new Alib($dbc, $config);
?>