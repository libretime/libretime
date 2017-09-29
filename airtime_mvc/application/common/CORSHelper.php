<?php


class CORSHelper
{
    public static function enableCrossOriginRequests(&$request, &$response)
    {
        //Chrome sends the Origin header for all requests, so we whitelist the webserver's hostname as well.
        $origin = $request->getHeader('Origin');

        if ((!(preg_match("/https?:\/\/localhost/", $origin) === 1)) && ($origin != "") &&
            (!in_array($origin, self::getAllowedOrigins($request))))
        {
            //Don't allow CORS from other domains to prevent XSS.
            throw new Zend_Controller_Action_Exception('Forbidden', 403);
        }
        //Allow AJAX requests from configured websites. We use this to allow other pages to use LibreTimes API.
        if ($origin) {
            $response = $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
    }

    /**
     * Get all allowed origins
     *
     * @param Request $request request object
     */
    public static function getAllowedOrigins($request)
    {
        $allowedCorsUrls = array_map(
            function($v) { return trim($v); },
            explode(PHP_EOL, Application_Model_Preference::GetAllowedCorsUrls())
        );

        // always allow the configured server in (as reported by the server and not what is i baseUrl)
        $scheme = $request->getServer('REQUEST_SCHEME');
        $host = $request->getServer('SERVER_NAME');
        $port = $request->getServer('SERVER_PORT');

        $portString = '';
        if (
            $scheme == 'https' && $port != 443 ||
            $scheme == 'http' && $port != 80
        ) {
            $portString = sprintf(':%s', $port);
        }
        $requestedUrl = sprintf(
            '%s://%s%s',
            $scheme,
            $host,
            $portString
        );
        return array_merge($allowedCorsUrls, array(
            $requestedUrl
        ));
    }
}
