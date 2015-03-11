<?php

class Application_Common_HTTPHelper
{
    /**
     * Returns start and end DateTime vars from given 
     * HTTP Request object
     *
     * @param Request
     * @return array(start DateTime, end DateTime)
     */
    public static function getStartEndFromRequest($request)
    {
        return Application_Common_DateHelper::getStartEnd(
            $request->getParam("start", null),
            $request->getParam("end", null),
            $request->getParam("timezone", null)
        );
    }

    public static function getStationUrl()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = $CC_CONFIG['baseUrl'];
        $baseDir = $CC_CONFIG['baseDir'];
        $basePort = $CC_CONFIG['basePort'];
        if (empty($baseDir)) {
            $baseDir = "/";
        }
        if ($baseDir[0] != "") {
            $baseDir = "/" . $baseDir;
        }

        $scheme = "http";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = "https";
            $basePort = "443"; //Airtime Pro compatibility hack
        }

        $stationUrl = "$scheme://${baseUrl}:${basePort}${baseDir}";

        return $stationUrl;
    }
}
