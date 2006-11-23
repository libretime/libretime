<?
/**
 * @author $Author$
 * @version  $Revision$
 */
require_once('../example/conf.php');
require_once('DB.php');
require_once('../Alib.php');

function errCallback($err)
{
    if (assert_options(ASSERT_ACTIVE)==1) {
    	return;
    }
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit;
}


$dbc = DB::connect($config['dsn'], TRUE);
if (PEAR::isError($dbc)) {
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in conf.php.\n";
    exit;
}

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$alib = new Alib($dbc, $config);

#    $dbc->setErrorHandling(PEAR_ERROR_RETURN);
echo "Trying to uninstall all ...\n";
$alib->uninstall();

$dbc->disconnect();
?>