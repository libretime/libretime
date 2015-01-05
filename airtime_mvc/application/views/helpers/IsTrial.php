<?php

class Airtime_View_Helper_IsTrial extends Zend_View_Helper_Abstract{
    public function isTrial(){
        $plan = Application_Model_Preference::GetPlanLevel();
        if($plan == 'trial'){
            return true;
        }else{
            return false;
        }
    }
}