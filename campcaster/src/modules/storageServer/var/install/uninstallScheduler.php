<?PHP
// Do not allow remote execution.
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit;
}


if (camp_db_table_exists($CC_CONFIG['scheduleTable'])) {
    echo " * Removing database table ".$CC_CONFIG['scheduleTable']."...";
    $CC_DBC->query("DROP TABLE ".$CC_CONFIG['scheduleTable']);
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['scheduleTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['backupTable'])) {
    echo " * Removing database table ".$CC_CONFIG['backupTable']."...";
    $CC_DBC->query("DROP TABLE ".$CC_CONFIG['backupTable']);
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['backupTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['playlogTable'])) {
    echo " * Removing database table ".$CC_CONFIG['playlogTable']."...";
    $CC_DBC->query("DROP TABLE ".$CC_CONFIG['playlogTable']);
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['playlogTable']."\n";
}

?>