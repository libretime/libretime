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
        $musicDir = CcMusicDirsQuery::create()
            ->filterByType('stor')
            ->filterByExists(true)
            ->findOne();
        $storPath = $musicDir->getDirectory();

        $freeSpace = disk_free_space($storPath);
        $totalSpace = disk_total_space($storPath);

        Application_Model_Preference::setDiskUsage($totalSpace - $freeSpace);
    }
}
