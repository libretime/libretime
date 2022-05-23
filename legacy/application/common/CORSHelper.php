<?php

use League\Uri\Contracts\UriException;
use League\Uri\Uri;

class CORSHelper
{
    public static function enableCrossOriginRequests(&$request, &$response)
    {
        // Chrome sends the Origin header for all requests, so we whitelist the webserver's hostname as well.
        $origin = $request->getHeader('Origin');
        $allowedOrigins = self::getAllowedOrigins($request);

        if (!($origin == '' || preg_match('/https?:\/\/localhost/', $origin) === 1 || in_array($origin, $allowedOrigins))) {
            // Don't allow CORS from other domains to prevent XSS.
            Logging::error("request origin '{$origin}' is not in allowed '" . implode(', ', $allowedOrigins) . "'!");

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
        $config = Config::getConfig();

        return array_merge(
            $config['allowedCorsOrigins'],
            self::getDatabaseAllowedOrigins(),
            self::getServerAllowedOrigins($request),
        );
    }

    /**
     * Get configured server origins.
     *
     * @param Request $request request object
     *
     * @return array
     */
    private static function getServerAllowedOrigins($request)
    {
        $scheme = $request->getServer('REQUEST_SCHEME');
        $host = $request->getServer('SERVER_NAME');
        $port = intval($request->getServer('SERVER_PORT'));

        try {
            return [
                strval(Uri::createFromComponents([
                    'scheme' => $scheme,
                    'host' => $host,
                    'port' => $port,
                ])),
            ];
        } catch (UriException|TypeError $e) {
            Logging::warn("could not parse server origin : {$e}");

            return [];
        }
    }

    /**
     * Get database allowed origins.
     *
     * @return array
     */
    private static function getDatabaseAllowedOrigins()
    {
        return array_map(
            'trim',
            explode(
                PHP_EOL,
                Application_Model_Preference::GetAllowedCorsUrls(),
            )
        );
    }
}
