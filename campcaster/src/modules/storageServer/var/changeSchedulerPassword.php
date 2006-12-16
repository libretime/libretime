#!/usr/bin/php

<?php
    /**
     * Change password for the scheduler account in storageServer
     *
     * required command line parameters:
     * @param 1. scheduler password
     *
     */

    require_once dirname(__FILE__).'/../var/conf.php';
    require_once dirname(__FILE__).'/../var/BasicStor.php';
    include_once 'DB.php';

    if(trim(`whoami`) != 'root') {
        die("Please run this script as root.\n");
    }

    PEAR::setErrorHandling(PEAR_ERROR_RETURN);
    $CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
    if (PEAR::isError($CC_DBC)) {
        die($CC_DBC->getMessage());
    }
    $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

    $bs = new BasicStor();

    $pass = $argv[1];
    $r = $bs->passwd('scheduler', NULL, $pass);
    if (PEAR::isError($r)) {
        die($r->getMessage());
    }
    exit(0);
?>