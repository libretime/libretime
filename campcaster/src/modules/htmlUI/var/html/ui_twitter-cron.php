<?php
require_once(dirname(__FILE__).'/../ui_handler_init.php');
require_once("../Input.php");

if (get_magic_quotes_gpc()) {
    $_REQUEST = Input::CleanMagicQuotes($_REQUEST);
}

if ($feed = $uiHandler->TWITTER->getFeed()) {
    if ($uiHandler->TWITTER->needsUpdate()) {
        print "Prepare for update...\n";
        if ($res = $uiHandler->TWITTER->sendFeed($feed)) {
            print "Feed id {$res->id}\n $feed";   
        } else {
            print "Update failed, check auth data.";   
        }
    } else {
        print "Update interval not reached.";   
    }
} else {
    print "No playlist found.";   
}