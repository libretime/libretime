<?php

class Airtime_View_Helper_IsAdmin extends Zend_View_Helper_Abstract
{
    public function isAdmin()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        return $user->isAdmin();
    }
}