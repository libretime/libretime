<?php

declare(strict_types=1);

class Airtime_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract
{
    public function loggedInAs()
    {
        $username = '';
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->login;
        }

        return $username;
    }
}
