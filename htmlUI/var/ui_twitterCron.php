<?php
require_once(dirname(__FILE__).'/ui_conf.php');
require_once(dirname(__FILE__).'/ui_handler.class.php');

$uiHandler = new uiHandler($CC_CONFIG);
$uiHandler->init();

if (is_array($argv)) {
    define('CRON_DEBUG', array_search('debug', $argv));
} else {
    define('CRON_DEBUG', true);
    print '<pre>';
}

$uiHandler->sessid = $_COOKIE[$CC_CONFIG['authCookieName']] = Alib::Login('scheduler', 'change_me');

if (!$uiHandler->sessid) {
    print "Alib::Login failed\n";
    exit(1); 
}

if (!$uiHandler->TWITTER->isActive()) {
    if (CRON_DEBUG) print "Twitter feed is deactivated.\n";
    exit();
}

if ($uiHandler->TWITTER->needsUpdate()) {
    if ($feed = $uiHandler->TWITTER->getFeed()) {
        if (CRON_DEBUG) print "Prepare for update...\n";
        if ($res = $uiHandler->TWITTER->sendFeed($feed)) {
            if (CRON_DEBUG) print "Post with feed id: {$res->id}\nContent: $feed";
        } else {
            print "Update failed, check auth data.\n";
            exit(1);
        }
    } else {
        if (CRON_DEBUG) print "No playlist found at offset time.\n";
    }
} else {
    if (CRON_DEBUG) print "Update interval not reached.\n";
}
