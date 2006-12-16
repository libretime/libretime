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

$alib = new Alib();

echo "\n\n======\n".
    "This is Alib standalone installation script, it is NOT needed to run ".
    "for Campcaster.\nAlib is automatically used by storageServer without it.".
    "\n======\n\n";

exit;

//echo "Alib: uninstall ...\n";
//$alib->uninstall();

$CC_DBC->setErrorHandling(PEAR_ERROR_DIE, "%s<hr>\n");
//echo "Alib: install ...\n";
//$alib->install();

echo " Testing ...\n";
$r = Alib::Test();
if (PEAR::isError($r)) { 
    echo $r->getMessage()."\n".$r->getUserInfo()."\n"; 
    exit; 
}
$log = $alib->test_log;
echo " TESTS:\n$log\n---\n";

echo " clean up + testdata insert ...\n";
Alib::DeleteData();
Alib::TestData();

echo " TREE DUMP:\n";
echo M2tree::DumpTree();
echo "\n Alib is probably installed OK\n";

$CC_DBC->disconnect();
?>