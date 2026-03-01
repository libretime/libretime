<?php

class CORSHelper
{
    public static function enableCrossOriginRequests(&$request, &$response)
    {
        // Chrome sends the Origin header for all requests, so we whitelist the webserver's hostname as well.
        $origin = $request->getHeader('Origin');
        $allowedOrigins = self::getAllowedOrigins($request);

        if (!($origin == '' || preg_match('/https?:\/\/localhost/', $origin) === 1 || in_array($origin, $allowedOrigins))) {
            // Don't allow CORS from other domains to prevent XSS.
            Logging::error(
                "request origin '{$origin}' is not in the configured 'allowed_cors_origins' '" . implode(', ', $allowedOrigins) . "'"
            );

            throw new Zend_Controller_Action_Exception('Forbidden', 403);
        }
        // Allow AJAX requests from configured websites. We use this to allow other pages to use LibreTimes API.
        if ($origin) {
            $response = $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
    }

    /**
     * Get all allowed origins.
     *
     * @param Request $request request object
     */
    public static function getAllowedOrigins($request)
    {
        return Config::get('general.allowed_cors_origins');
    }
}
