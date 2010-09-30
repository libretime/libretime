#!/usr/bin/php

<?php
    /**
     * Do database restore in background. - command line php application
     *
     * required command line parameters:
     * @param 1. backup file
     * @param 2. status file
     * @param 3. token
     * @param 4. sessid
     *
     */

    require_once(dirname(__FILE__).'/../var/conf.php');
    require_once(dirname(__FILE__).'/../var/GreenBox.php');
    require_once(dirname(__FILE__).'/../var/Restore.php');
    include_once('DB.php');

    PEAR::setErrorHandling(PEAR_ERROR_RETURN);
    $CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
    if (DB::isError($CC_DBC)) {
        die($CC_DBC->getMessage());
    }
    $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

    $gb = new GreenBox();
    $rs = new Restore($gb);

    if ($rs->loglevel=='debug') {
	    $rs->addLogItem('argv:'.print_r($argv,true));
    }

#    sleep(2);

    $backupfile = $argv[1];
    $token      = $argv[3];
    $sessid     = $argv[4];
    $rs->startRestore($backupfile,$token,$sessid);

?>