<?php


class CORSHelper
{
    public static function enableATProCrossOriginRequests(&$request, &$response)
    {
        //Allow AJAX requests from www.airtime.pro. We use this to automatically login users
        //after they sign up from the microsite.
        //Chrome sends the Origin header for all requests, so we whitelist the webserver's hostname as well.
        $response = $response->setHeader('Access-Control-Allow-Origin', '*');
        $origin = $request->getHeader('Origin');
        if ((!(preg_match("/https?:\/\/localhost/", $origin) === 1)) && ($origin != "") &&
            (!in_array($origin, self::getAllowedOrigins())))
        {
            //Don't allow CORS from other domains to prevent XSS.
            throw new Zend_Controller_Action_Exception('Forbidden', 403);
        }
    }

    public static function getAllowedOrigins()
    {
        return array("http://www.airtime.pro",
                        "https://www.airtime.pro",
                        "https://account.sourcefabric.com",
                        "https://account.sourcefabric.com:5001",
                        "http://" . $_SERVER['SERVER_NAME'],
                        "https://" . $_SERVER['SERVER_NAME']);
    }
}
