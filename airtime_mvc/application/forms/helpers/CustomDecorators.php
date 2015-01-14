<?php

/** Hide a Zend_Form_Element unless you're logged in as a SuperAdmin. */
class Airtime_Decorator_SuperAdmin_Only extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $currentUser = Application_Model_User::getCurrentUser();
        if ($currentUser->isSuperAdmin()) {
            return $content;
        } else {
            return "";
        }
    }
}