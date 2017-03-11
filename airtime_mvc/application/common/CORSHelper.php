<?php


class CORSHelper
{
    public static function enableCrossOriginRequests(&$request, &$response)
    {
        //Chrome sends the Origin header for all requests, so we whitelist the webserver's hostname as well.
        $origin = $request->getHeader('Origin');
        if ((!(preg_match("/https?:\/\/localhost/", $origin) === 1)) && ($origin != "") &&
            (!in_array($origin, self::getAllowedOrigins())))
        {
            //Don't allow CORS from other domains to prevent XSS.
            throw new Zend_Controller_Action_Exception('Forbidden', 403);
        }
        //Allow AJAX requests from configured websites. We use this to allow other pages to use LibreTimes API.
        if ($origin) {
            $response = $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
    }

    public static function getAllowedOrigins()
    {
        $allowedCorsUrls = array_map(
            function($v) { return trim($v); },
            explode(PHP_EOL, Application_Model_Preference::GetAllowedCorsUrls())
        );
        return array_merge($allowedCorsUrls, array(
                        "http://" . $_SERVER['SERVER_NAME'],
                        "https://" . $_SERVER['SERVER_NAME']));
    }
}
