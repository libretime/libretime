<?php

require_once 'soundcloud-api/Services/Soundcloud.php';

class Airtime_View_Helper_SoundCloudLink extends Zend_View_Helper_Abstract
{
    public function soundCloudLink()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $host = $request->getHttpHost();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $redirectUrl = "http://{$host}/{$controller}/{$action}";

        $soundcloud = new Services_Soundcloud('2CLCxcSXYzx7QhhPVHN4A', 'pZ7beWmF06epXLHVUP1ufOg2oEnIt9XhE8l8xt0bBs', $redirectUrl);
        $authorizeUrl = $soundcloud->getAuthorizeUrl();
    
        return $authorizeUrl;
    }
}

