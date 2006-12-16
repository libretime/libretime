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


$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    echo "Database connection problem.\n";
    echo "Check if database '".$CC_CONFIG['dsn']['database']."' exists with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in conf.php.\n";
    exit;
}

$CC_DBC->setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
//$alib = new Alib();
//
//echo "Trying to uninstall all ...\n";
//$alib->uninstall();

$CC_DBC->disconnect();
?>