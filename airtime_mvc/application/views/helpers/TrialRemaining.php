<?php

class Airtime_View_Helper_TrialRemaining extends Zend_View_Helper_Abstract
{
    public function trialRemaining()
    {
        $ending_date = Application_Model_Preference::GetTrialEndingDate();
        if ($ending_date == '') {
            return '';
        }
        $datetime1 = new DateTime();
        $datetime2 = new DateTime($ending_date);
        $interval = $datetime1->diff($datetime2);
        if($interval->format('%R') == '-'){
            return "Trial expired.";
        }
        return $interval->format('%a');
    }
}
