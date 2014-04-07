<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../install_minimal/../airtime_mvc/application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library')
)));

/*set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library/propel/runtime/lib')
)));*/

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
            ->filterByDbType('stor')
            ->filterByDbExists(true)
            ->findOne();
        $storPath = $musicDir->getDbDirectory();

        $freeSpace = disk_free_space($storPath);
        $totalSpace = disk_total_space($storPath);

        Application_Model_Preference::setDiskUsage($totalSpace - $freeSpace);
    }
}
