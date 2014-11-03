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

        $f = $storPath;
        $io = popen('/usr/bin/du -bs ' . $f, 'r');
        $size = fgets($io, 4096);
        $size = substr($size, 0, strpos($size, "\t"));
        pclose($io);

        Application_Model_Preference::setDiskUsage($size);
    }
}
