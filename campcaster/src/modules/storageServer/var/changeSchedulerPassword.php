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
    $dbc = DB::connect($config['dsn'], TRUE);
    if (DB::isError($dbc)) {
        die($dbc->getMessage());
    }
    $dbc->setFetchMode(DB_FETCHMODE_ASSOC);

    $bs = new BasicStor($dbc, $config);

    $pass = $argv[1];
    $r = $bs->passwd('scheduler', NULL, $pass);
    if (PEAR::isError($r)) {
        die($r->getMessage());
    }
    exit(0);
?>