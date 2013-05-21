<?php

/**
 * This file does the following things:
 * 1. Calculate how many major versions back the current installation
 *    is from the latest release
 * 2. Returns the matching icon based on result of 1, as HTML
 * 3. Returns the matching tooltip message based on result of 1, as HTML
 *    (stored in pair of invisible div tags)
 * 4. Returns the current version, as HTML (stored in pair of invisible div tags)
 */
class Airtime_View_Helper_VersionNotify extends Zend_View_Helper_Abstract{
    
    public function versionNotify(){
        return "";
    }
}
