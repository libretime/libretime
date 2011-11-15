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
        $bg = "/css/images/";
        if(($diff == 0 && $current == $latest) || $diff < 0) {
            // current version is up to date
            $bg .= "icon_uptodate.png";
        } else if($diff <= 2) {
            // 2 or less major versions back
            $bg .= "icon_update.png";
        } else if($diff == 3) {
            // 3 major versions back
            $bg .= "icon_update2.png";
        } else {
            // more than 3 major versions back
            $bg .= "icon_outdated.png";
        }
        
        $result = "<div id='version_diff' style='display:none'>" . $diff . "</div>"
                . "<div id='version_current' style='display:none'>" . $current . "</div>"
                . "<div id='version_latest' style='display:none'>" . $latest . "</div>"
                . "<div id='version_icon' style='background-image: url(" . $bg . ");'></div>";
        return $result;
    }
}
