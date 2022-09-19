<?php

class Assets
{
    private static $cache;

    private static function getChecksum($path)
    {
        if (is_null(self::$cache)) {
            self::$cache = json_decode(@file_get_contents(APPLICATION_PATH . '/assets.json'), true);
        }

        return self::$cache[$path];
    }

    public static function url($path)
    {
        $base_url = Config::getBasePath();

        return $base_url . $path . '?' . self::getChecksum($path);
    }
}
