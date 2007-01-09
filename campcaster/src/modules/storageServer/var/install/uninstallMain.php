<?php
/**
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision: 2458 $
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */

// Do not allow remote execution.
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit;
}

if (camp_db_table_exists($CC_CONFIG['transTable'])) {
    echo " * Removing database table ".$CC_CONFIG['transTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['transTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['transTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['transTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['mdataTable'])) {
    echo " * Removing database table ".$CC_CONFIG['mdataTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['mdataTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['mdataTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['mdataTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['filesTable'])) {
    echo " * Removing database table ".$CC_CONFIG['filesTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['filesTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['filesTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['accessTable'])) {
    echo " * Removing database table ".$CC_CONFIG['accessTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['accessTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['accessTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['permTable'])) {
    echo " * Removing database table ".$CC_CONFIG['permTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['permTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['permTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['permTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['sessTable'])) {
    echo " * Removing database table ".$CC_CONFIG['sessTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['sessTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['sessTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['subjTable'])) {
    echo " * Removing database table ".$CC_CONFIG['subjTable']."...";
    $CC_DBC->dropSequence($CC_CONFIG['subjTable']."_id_seq");

    $sql = "DROP TABLE ".$CC_CONFIG['subjTable'];
    camp_install_query($sql, false);

    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['subjTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['smembTable'])) {
    echo " * Removing database table ".$CC_CONFIG['smembTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['smembTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['smembTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['smembTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['classTable'])) {
    echo " * Removing database table ".$CC_CONFIG['classTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['classTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['classTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['cmembTable'])) {
    echo " * Removing database table ".$CC_CONFIG['cmembTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['cmembTable'];
    camp_install_query($sql);
} else {
    echo " * Skipping: database table ".$CC_CONFIG['cmembTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['structTable'])) {
    echo " * Removing database table ".$CC_CONFIG['structTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['structTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['structTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['structTable']."\n";
}

if (camp_db_table_exists($CC_CONFIG['treeTable'])) {
    echo " * Removing database table ".$CC_CONFIG['treeTable']."...";
    $sql = "DROP TABLE ".$CC_CONFIG['treeTable'];
    camp_install_query($sql, false);

    $CC_DBC->dropSequence($CC_CONFIG['treeTable']."_id_seq");
    echo "done.\n";
} else {
    echo " * Skipping: database table ".$CC_CONFIG['treeTable']."\n";
}

?>