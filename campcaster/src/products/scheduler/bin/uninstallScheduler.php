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
    camp_install_query("DROP TABLE ".$CC_CONFIG['scheduleTable']);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['scheduleTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['backupTable'])) {
    echo " * Removing database table ".$CC_CONFIG['backupTable']."...";
    camp_install_query("DROP TABLE ".$CC_CONFIG['backupTable']);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['backupTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['playlogTable'])) {
    echo " * Removing database table ".$CC_CONFIG['playlogTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['playlogTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['playlogTable']."\n";
}

?>