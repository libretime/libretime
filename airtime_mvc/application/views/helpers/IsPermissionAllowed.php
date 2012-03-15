<?php

class Airtime_View_Helper_IsPermissionAllowed extends Zend_View_Helper_Abstract
{
    public function IsPermissionAllowed()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        
        $show = Application_Model_Show::GetCurrentShow();
        $show_id = isset($show['id'])?$show['id']:0;
        if($user->canSchedule($show_id)){
            return true;
        }else{
            return false;
        }
    }
}