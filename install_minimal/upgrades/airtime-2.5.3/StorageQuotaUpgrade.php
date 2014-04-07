<?php
class StorageQuotaUpgrade
{
    public static function startUpgrade()
    {
        echo "* Updating storage usage for new quota tracking".PHP_EOL;
        self::setStorageUsage();
    }

    private static function setStorageUsage()
    {
        
    }
}