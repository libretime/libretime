<?php
// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

if (!camp_db_table_exists($CC_CONFIG['scheduleTable'])) {
    echo " * Creating database table ".$CC_CONFIG['scheduleTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['scheduleTable']."("
        ."   id          BIGINT      NOT NULL,"
        ."   playlist    BIGINT      NOT NULL,"
        ."   starts      TIMESTAMP   NOT NULL,"
        ."   ends        TIMESTAMP   NOT NULL,"
        ."   PRIMARY KEY(id))";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['scheduleTable']."\n";
}


if (!camp_db_table_exists($CC_CONFIG['playlogTable'])) {
    echo " * Creating database table ".$CC_CONFIG['playlogTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['playlogTable']."("
        ."   id            BIGINT      NOT NULL,"
        ."   audioClipId   BIGINT      NOT NULL,"
        ."   timestamp     TIMESTAMP   NOT NULL,"
        ."   PRIMARY KEY(id))";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['playlogTable']."\n";
}


if (!camp_db_table_exists($CC_CONFIG['backupTable'])) {
    echo " * Creating database table ".$CC_CONFIG['backupTable']."...";
    $sql = "CREATE TABLE ".$CC_CONFIG['backupTable']." ("
        ."   token       VARCHAR(64)     NOT NULL,"
        ."   sessionId   VARCHAR(64)     NOT NULL,"
        ."   status      VARCHAR(32)     NOT NULL,"
        ."   fromTime    TIMESTAMP       NOT NULL,"
        ."   toTime      TIMESTAMP       NOT NULL,"
        ."   PRIMARY KEY(token))";
    camp_install_query($sql);
} else {
    echo " * Skipping: database table already exists: ".$CC_CONFIG['backupTable']."\n";
}
?>