<?php
if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

require_once('DB.php');

function camp_db_table_exists($p_name)
{
    global $CC_DBC;
    $sql = "SELECT * FROM ".$p_name;
    $result = $CC_DBC->GetOne($sql);
    if (PEAR::isError($result)) {
        return false;
    }
    return true;
}

function camp_install_query($sql)
{
    global $CC_DBC;
    $result = $CC_DBC->query($sql);
    if (PEAR::isError($result)) {
        echo "Error! ".$result->getMessage()."\n";
        echo "   SQL statement was:\n";
        echo "   ".$sql."\n\n";
    } else {
        echo "done.\n";
    }
}

$options = getopt("c:");

if (!$options) {
    echo "\nYou must specific the config file with -c.\n\n";
    exit;
}

$configFile = $options['c'];
if (!file_exists($configFile)) {
    echo "\nThe config file '$configFile' does not exist.\n\n";
    exit;
}

echo " * Using config file $configFile\n";

$xml = file_get_contents($configFile);
$parser = xml_parser_create();
xml_parse_into_struct($parser, $xml, $vals, $index);
xml_parser_free($parser);

$CC_CONFIG['dsn'] = array('hostspec' => 'localhost',
                          'phptype' => 'pgsql');

// Get the user index
$userIndex = $index['SIMPLECONNECTIONMANAGER'][0];
$CC_CONFIG['dsn']['username'] = $vals[$userIndex]['attributes']['USERNAME'];
$CC_CONFIG['dsn']['password'] = $vals[$userIndex]['attributes']['PASSWORD'];
$CC_CONFIG['dsn']['database'] = $vals[$userIndex]['attributes']['DSN'];

$CC_CONFIG['playlogTable'] = 'playlog';
$CC_CONFIG['scheduleTable'] = 'schedule';
$CC_CONFIG['backupTable'] = 'backup';

$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    echo $CC_DBC->getMessage()."\n";
    echo $CC_DBC->getUserInfo()."\n";
    echo "Database connection problem.\n";
    echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
        " with corresponding permissions.\n";
    exit(1);
} else {
    echo " * Connected to database\n";
}

$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

?>