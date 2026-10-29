<?php

/**
 * The legacy answer for every fixture file: "<file id>|true|false".
 *
 * ab.sh runs this, then re-seeds and runs the Python side, then compares. Printing
 * the same shape from both sides is what makes compare.py trivial and the evidence
 * readable in a CI log.
 */
$legacyRoot = getenv('LEGACY_ROOT') ?: '/var/www/html';
require_once $legacyRoot . '/application/preload.php';
require_once $legacyRoot . '/application/check.php';
configureDatabase();

$pdo = Propel::getConnection();
$files = $pdo
    ->query('select id from cc_files where id between 9000 and 9099 order by id')
    ->fetchAll(PDO::FETCH_COLUMN);

if (count($files) === 0) {
    fwrite(STDERR, "php: no fixture files, run seed.sql first\n");
    exit(1);
}

foreach ($files as $id) {
    $future = Application_Model_Schedule::IsFileScheduledInTheFuture((int) $id);
    printf("%d|%s\n", $id, $future ? 'true' : 'false');
}
