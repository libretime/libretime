<?
// $Id: install.php,v 1.1 2004/07/23 00:22:13 tomas Exp $
require_once 'example/conf.php';
require_once 'DB.php';
require_once 'alib.php';

function errCallback($err)
{
    if(assert_options(ASSERT_ACTIVE)==1) return;
    echo "<pre>\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo()."\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit;
}


PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){
    echo "Database connection problem.\n";
    echo "Create database '{$config['dsn']['database']}' or change 'dsn' values in conf.php.\n";
    exit;
}

$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$alib =& new Alib($dbc, $config);
?>
<html><head>
<title>ALib install</title>
</head><body>
<h3>Alib install</h3>
<pre>
<?
if($_REQUEST['ak']=='inst'){
    $dbc->setErrorHandling(PEAR_ERROR_RETURN);
#    $dbc->setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
    echo "Trying to uninstall all ...\n";
    $alib->uninstall();
    $dbc->setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
#    $dbc->setErrorHandling(PEAR_ERROR_CALLBACK, 'errCallback');
    echo "Install ...\n";
    $alib->install();

    echo "Testing ...\n";
    $alib->test();
    $log = $alib->test_log;
    echo "TESTS:\n$log\n---\n";

    echo "Reinstall + testdata insert ...\n";
    $alib->reinstall();
    $alib->testData();

    echo "TREE DUMP:\n";
    echo $alib->dumpTree();
    echo "\n<b>Alib is probably installed OK</b>\n\n\n";
}
$dbc->disconnect();
?>
</pre>
<a href="install.php?ak=inst">Install/reinstall !</a><br>
<br>
<a href="example/">Example</a><br>
<a href="xmlrpc/">XmlRpc test</a>
</body></html>

