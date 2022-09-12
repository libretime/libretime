<?php

class Application_Common_Storage
{
    public static function splitFilePath($p_filepath)
    {
        $storage_path = Config::getStoragePath();

        if (strpos($p_filepath, $storage_path) !== 0) {
            return null;
        }

        return [$storage_path, substr($p_filepath, strlen($storage_path))];
    }
}
