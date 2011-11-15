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
        if(Application_Model_Preference::GetPlanLevel() != 'disabled'){
            return "";
        }
        
        // retrieve and validate current and latest versions,
        $current = Application_Model_Preference::GetAirtimeVersion();
        $latest = Application_Model_Preference::GetLatestVersion();
        $pattern = "/^([0-9]+)\.([0-9]+)\.[0-9]+/";
        preg_match($pattern, $current, $curMatch);
        preg_match($pattern, $latest, $latestMatch);
        if(count($curMatch) == 0 || count($latestMatch) == 0) {
            return "";
        }
        
        // Calculate major version diff;
        // Example: if current = 1.9.5 and latest = 3.0.0, major diff = 11
        // Note: algorithm assumes the number after 1st dot never goes above 9
        $diff = (intval($latestMatch[1]) * 10 + intval($latestMatch[2])) 
                - (intval($curMatch[1]) * 10 + intval($curMatch[2]));
        
        // Pick icon
        if(($diff == 0 && $current == $latest) || $diff < 0) {
            // current version is up to date
            $class = "uptodate";
        } else if($diff <= 2) {
            // 2 or less major versions back
            $class = "update";
        } else if($diff == 3) {
            // 3 major versions back
            $class = "update2";
        } else {
            // more than 3 major versions back
            $class = "outdated";
        }
        
        $result = "<div id='version-diff' style='display:none'>" . $diff . "</div>"
                . "<div id='version-current' style='display:none'>" . $current . "</div>"
                . "<div id='version-latest' style='display:none'>" . $latest . "</div>"
                . "<div id='version-icon' class='" . $class . "'></div>";
        return $result;
    }
}
