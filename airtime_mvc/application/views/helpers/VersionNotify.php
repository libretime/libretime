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
        $link = Application_Model_Preference::GetLatestLink();
        $currentExploded = explode('.', $current);
        $latestExploded = explode('.', $latest);
        if(count($currentExploded) != 3 || count($latestExploded) != 3) {
            return "";
        }
        
        // Calculate the version difference;
        // Example: if current = 1.9.5 and latest = 3.0.0, diff = 105
        // Note: algorithm assumes the number after 1st dot never goes above 9
        $versionDifference = (intval($latestExploded[0]) * 100 + intval($latestExploded[1]) *10 + intval($latestExploded[2])) 
            - (intval($currentExploded[0]) * 100 + intval($currentExploded[1] *10 + intval($currentExploded[2])));
        
        // Pick icon based on distance this version is to the latest version available
        if($versionDifference <= 0) {
            // current version is up to date or newer
            $class = "uptodate";
        } else if($versionDifference < 20) {
            // 2 or less major versions back
            $class = "update";
        } else if($versionDifference < 30) {
            // 3 major versions back
            $class = "update2";
        } else {
            // more than 3 major versions back
            $class = "outdated";
        }
        
        $result = "<div id='version-diff' style='display:none'>" . $versionDifference . "</div>"
                . "<div id='version-current' style='display:none'>" . $current . "</div>"
                . "<div id='version-latest' style='display:none'>" . $latest . "</div>"
                . "<div id='version-link' style='display:none'>" . $link . "</div>"
                . "<div id='version-icon' class='" . $class . "'></div>";
        return $result;
    }
}
