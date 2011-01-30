<?php

class Airtime_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
{
    public function loggedInAs ()
    {
        //$username = "test";
        /*
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->username;
        }
    */
        return "test"; 
    }
}

?>
