<?php

declare(strict_types=1);

/** Hide a Zend_Form_Element unless you're logged in as a SuperAdmin. */
class Airtime_Decorator_SuperAdmin_Only extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $currentUser = Application_Model_User::getCurrentUser();
        if ($currentUser->isSuperAdmin()) {
            return $content;
        }

        return '';
    }
}

/** Hide a Zend_Form_Element unless you're logged in as an Admin or SuperAdmin. */
class Airtime_Decorator_Admin_Only extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $currentUser = Application_Model_User::getCurrentUser();
        if ($currentUser->isSuperAdmin() || $currentUser->isAdmin()) {
            return $content;
        }

        return '';
    }
}
