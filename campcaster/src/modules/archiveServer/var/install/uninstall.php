<?php
/**
 * @author $Author$
 * @version $Revision$
 */
require_once '../conf.php';
require_once 'DB.php';
require_once '../Archive.php';

function errCallback($err)
{
    if (assert_options(ASSERT_ACTIVE) == 1) {
    	return;
    }
    echo "ERROR:\n";
    echo "request: "; print_r($_REQUEST);
    echo "gm:\n".$err->getMessage()."\ndi:\n".$err->getDebugInfo().
        "\nui:\n".$err->getUserInfo()."\n</pre>\n";
    exit(1);
}


PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
$dbc = DB::connect($config['dsn'], TRUE);
if (PEAR::isError($dbc)) {
    echo "Database connection problem.\n";
    echo "Check if database '{$config['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    echo "Database access is defined by 'dsn' values in conf.php.\n";
    exit(1);
}

echo "#ArchiveServer uninstall:\n";
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = new Archive($dbc, $config, TRUE);
$tr = new Transport($gb);


echo "# Uninstall Transport submodule ...";
$r = $tr->uninstall();
if (PEAR::isError($r)) {
	echo $r->getUserInfo()."\n";
	exit;
}
echo "\n";

$dbc->setErrorHandling(PEAR_ERROR_RETURN);
$gb->uninstall();
echo "#ArchiveServer uninstall: OK\n";

$dbc->disconnect();
?>