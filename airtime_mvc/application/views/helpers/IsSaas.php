<?php

class Airtime_View_Helper_IsSaas extends Zend_View_Helper_Abstract{
    public function isSaas(){
        $plan = Application_Model_Preference::GetPlanLevel();
        if($plan == 'disabled'){
            return false;
        }else{
            return true;
        }
    }
}