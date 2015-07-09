<?php

/**
 * Check if a given classname belongs to a subclass of AirtimeUpgrader
 *
 * @param $c string class name
 *
 * @return bool true if the $c is a subclass of AirtimeUpgrader
 */
function isUpgrade($c) {
    return is_subclass_of($c, "AirtimeUpgrader");
}

/**
 * Filter all declared classes to get all upgrade classes dynamically
 *
 * @return array all upgrade classes
 */
function getUpgrades() {
    return array_filter(get_declared_classes(), "isUpgrade");
}

class UpgradeManager
{

    /**
     * Used to determine if the database schema needs an upgrade in order for this version of the Airtime codebase to work correctly.
     * @return array A list of schema versions that this version of the codebase supports.
     */
    public static function getSupportedSchemaVersions()
    {
        //What versions of the schema does the code support today:
        return array(AIRTIME_CODE_VERSION);
    }

    public static function checkIfUpgradeIsNeeded()
    {
        $schemaVersion = Application_Model_Preference::GetSchemaVersion();
        $supportedSchemaVersions = self::getSupportedSchemaVersions();
        return !in_array($schemaVersion, $supportedSchemaVersions);
        // We shouldn't run the upgrade as a side-effect of this function!
        /*
        if ($upgradeNeeded) {
            self::doUpgrade();
        }
        */
    }

    /**
     * Upgrade the Airtime schema version to match the highest supported version
     *
     * @return boolean whether or not an upgrade was performed
     */
    public static function doUpgrade()
    {
        // Get all upgrades dynamically (in declaration order!) so we don't have to add them explicitly each time
        // TODO: explicitly sort classnames by ascending version suffix for safety
        $upgraders = getUpgrades();
        $dir = (dirname(__DIR__) . "/controllers");
        $upgradePerformed = false;

        foreach ($upgraders as $upgrader) {
            $upgradePerformed = self::_runUpgrade(new $upgrader($dir)) ? true : $upgradePerformed;
        }

        return $upgradePerformed;
    }

    /**
     * Downgrade the Airtime schema version to match the given version
     *
     * @param string $toVersion the version we want to downgrade to
     *
     * @return boolean whether or not an upgrade was performed
     */
    public static function doDowngrade($toVersion)
    {
        $downgraders = array_reverse(getUpgrades());  // Reverse the array because we're downgrading
        $dir = (dirname(__DIR__) . "/controllers");
        $downgradePerformed = false;

        foreach ($downgraders as $downgrader) {
            /** @var AirtimeUpgrader $downgrader */
            $downgrader = new $downgrader($dir);
            if ($downgrader->getNewVersion() == $toVersion) {
                break;  // We've reached the version we wanted to downgrade to, so break
            }
            $downgradePerformed = self::_runDowngrade($downgrader) ? true : $downgradePerformed;
        }

        return $downgradePerformed;
    }

    /**
     * Run the given upgrade
     *
     * @param $upgrader AirtimeUpgrader the upgrader class to be executed
     *
     * @return bool true if the upgrade was successful, otherwise false
     */
    private static function _runUpgrade(AirtimeUpgrader $upgrader) {
        return $upgrader->checkIfUpgradeSupported() && $upgrader->upgrade();
    }

    /**
     * Run the given downgrade
     *
     * @param $downgrader           AirtimeUpgrader the upgrader class to be executed
     * @param $supportedVersions    array           array of supported versions
     *
     * @return bool true if the downgrade was successful, otherwise false
     */
    private static function _runDowngrade(AirtimeUpgrader $downgrader) {
        return $downgrader->checkIfDowngradeSupported() && $downgrader->downgrade();
    }

}

abstract class AirtimeUpgrader
{
    protected $_dir;

    protected $username, $password, $host, $database;

    /**
     * @param $dir string directory housing upgrade files
     */
    public function __construct($dir) {
        $this->_dir = $dir;
    }

    /** Schema versions that this upgrader class can upgrade from (an array of version strings). */
    abstract protected function getSupportedSchemaVersions();

    /** The schema version that this upgrader class will upgrade to. (returns a version string) */
    abstract public function getNewVersion();

    public static function getCurrentSchemaVersion()
    {
        return Application_Model_Preference::GetSchemaVersion();
    }

    /**
     * This function checks to see if this class can perform an upgrade of your version of Airtime
     * @return boolean True if we can upgrade your version of Airtime.
     */
    public function checkIfUpgradeSupported()
    {
        return in_array(static::getCurrentSchemaVersion(), $this->getSupportedSchemaVersions());
    }

    /**
     * This function checks to see if this class can perform a downgrade of your version of Airtime
     *
     * @return boolean True if we can downgrade your version of Airtime.
     */
    public function checkIfDowngradeSupported()
    {
        return static::getCurrentSchemaVersion() == $this->getNewVersion();
    }

    protected function toggleMaintenanceScreen($toggle)
    {
        if ($toggle)
        {
            //Disable Airtime UI
            //create a temporary maintenance notification file
            //when this file is on the server, zend framework redirects all
            //requests to the maintenance page and sets a 503 response code
            /* DISABLED because this does not work correctly
            $this->maintenanceFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."maintenance.txt" : "/tmp/maintenance.txt";
            $file = fopen($this->maintenanceFile, 'w');
            fclose($file);
             */
        } else {
            //delete maintenance.txt to give users access back to Airtime
            /* DISABLED because this does not work correctly
            if ($this->maintenanceFile) {
                unlink($this->maintenanceFile);
            }*/
        }
    }

    /**
     * Implement this for each new version of Airtime
     * This function abstracts out the core upgrade functionality,
     * allowing child classes to overwrite _runUpgrade to reduce duplication
     */
    public function upgrade() {
        Cache::clear();
        assert($this->checkIfUpgradeSupported());

        try {
            // $this->toggleMaintenanceScreen(true);
            Cache::clear();

            $this->_getDbValues();
            $this->_runUpgrade();

            Application_Model_Preference::SetSchemaVersion($this->getNewVersion());
            Cache::clear();

            // $this->toggleMaintenanceScreen(false);
        } catch(Exception $e) {
            // $this->toggleMaintenanceScreen(false);
            return false;
        }

        return true;
    }

    /**
     * Implement this for each new version of Airtime
     * This function abstracts out the core downgrade functionality,
     * allowing child classes to overwrite _runDowngrade to reduce duplication
     */
    public function downgrade() {
        Cache::clear();

        try {
            $this->_getDbValues();
            $this->_runDowngrade();

            $highestSupportedVersion = null;
            foreach ($this->getSupportedSchemaVersions() as $v) {
                // version_compare returns 1 (true) if the second parameter is lower
                if (!$highestSupportedVersion || version_compare($v, $highestSupportedVersion)) {
                    $highestSupportedVersion = $v;
                }
            }

            // Set the schema version to the highest supported version so we don't skip versions when downgrading
            Application_Model_Preference::SetSchemaVersion($highestSupportedVersion);

            Cache::clear();
        } catch(Exception $e) {
            return false;
        }

        return true;
    }

    protected function _getDbValues() {
        $airtimeConf = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
        $values = parse_ini_file($airtimeConf, true);

        $this->username = $values['database']['dbuser'];
        $this->password = $values['database']['dbpass'];
        $this->host     = $values['database']['host'];
        $this->database = $values['database']['dbname'];
    }

    protected function _runUpgrade() {
        passthru("export PGPASSWORD=".$this->password." && psql -h ".$this->host." -U ".$this->username." -q -f ".$this->_dir."/upgrade_sql/airtime_"
                 .$this->getNewVersion()."/upgrade.sql ".$this->database." 2>&1 | grep -v -E \"will create implicit sequence|will create implicit index\"");
    }

    protected function _runDowngrade() {
        passthru("export PGPASSWORD=".$this->password." && psql -h ".$this->host." -U ".$this->username." -q -f ".$this->_dir."/downgrade_sql/airtime_"
                 .$this->getNewVersion()."/downgrade.sql ".$this->database." 2>&1 | grep -v -E \"will create implicit sequence|will create implicit index\"");
    }

}

class AirtimeUpgrader253 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions()
    {
        return array('2.5.1', '2.5.2');

    }
    public function getNewVersion()
    {
        return '2.5.3';
    }

    protected function _runUpgrade()
    {
        //Update disk_usage value in cc_pref
        $storDir = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."srv/airtime/stor" : "/srv/airtime/stor";
        $diskUsage = shell_exec("du -sb $storDir | awk '{print $1}'");

        Application_Model_Preference::setDiskUsage($diskUsage);

        //update system_version in cc_pref and change some columns in cc_files
        parent::_runUpgrade();
    }
}

class AirtimeUpgrader254 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions()
    {
        return array('2.5.3');
    }
    public function getNewVersion()
    {
        return '2.5.4';
    }
    
    protected function _runUpgrade()
    {
        //First, ensure there are no superadmins already.
        $numberOfSuperAdmins = CcSubjsQuery::create()
            ->filterByDbType(UTYPE_SUPERADMIN)
            ->filterByDbLogin("sourcefabric_admin", Criteria::NOT_EQUAL) //Ignore sourcefabric_admin users
            ->count();

        //Only create a super admin if there isn't one already.
        if ($numberOfSuperAdmins == 0)
        {
            //Find the "admin" user and promote them to superadmin.
            $adminUser = CcSubjsQuery::create()
                ->filterByDbLogin('admin')
                ->findOne();
            if (!$adminUser)
            {
                // Otherwise get the user with the lowest ID that is of type administrator:
                $adminUser = CcSubjsQuery::create()
                    ->filterByDbType(UTYPE_ADMIN)
                    ->orderByDbId(Criteria::ASC)
                    ->findOne();

                if (!$adminUser) {
                    throw new Exception("Failed to find any users of type 'admin' ('A').");
                }
            }

            $adminUser = new Application_Model_User($adminUser->getDbId());
            $adminUser->setType(UTYPE_SUPERADMIN);
            $adminUser->save();
            Logging::info($_SERVER['HTTP_HOST'] . ': ' . $this->getNewVersion() . " Upgrade: Promoted user " . $adminUser->getLogin() . " to be a Super Admin.");

            //Also try to promote the sourcefabric_admin user
            $sofabAdminUser = CcSubjsQuery::create()
                ->filterByDbLogin('sourcefabric_admin')
                ->findOne();
            if ($sofabAdminUser) {
                $sofabAdminUser = new Application_Model_User($sofabAdminUser->getDbId());
                $sofabAdminUser->setType(UTYPE_SUPERADMIN);
                $sofabAdminUser->save();
                Logging::info($_SERVER['HTTP_HOST'] . ': ' . $this->getNewVersion() . " Upgrade: Promoted user " . $sofabAdminUser->getLogin() . " to be a Super Admin.");
            }
        }
    }
}

class AirtimeUpgrader255 extends AirtimeUpgrader {
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.4'
        );
    }

    public function getNewVersion() {
        return '2.5.5';
    }
}

class AirtimeUpgrader259 extends AirtimeUpgrader {
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.5'
        );
    }
    
    public function getNewVersion() {
        return '2.5.9';
    }
}

class AirtimeUpgrader2510 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.9'
        );
    }

    public function getNewVersion() {
        return '2.5.10';
    }
}

class AirtimeUpgrader2511 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.10'
        );
    }

    public function getNewVersion() {
        return '2.5.11';
    }

    protected function _runUpgrade() {
        $queryResult = CcFilesQuery::create()
            ->select(array('disk_usage'))
            ->withColumn('SUM(CcFiles.filesize)', 'disk_usage')
            ->find();
        $disk_usage = $queryResult[0];
        Application_Model_Preference::setDiskUsage($disk_usage);
    }
}

class AirtimeUpgrader2512 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.10',
            '2.5.11'
        );
    }

    public function getNewVersion() {
        return '2.5.12';
    }
}

/**
 * Class AirtimeUpgrader2513 - Celery and SoundCloud upgrade
 *
 * Adds third_party_track_references and celery_tasks tables for third party service
 * authentication and task architecture.
 *
 * <br/><b>third_party_track_references</b> schema:
 *
 *      id              -> int          PK
 *      service         -> string       internal service name
 *      foreign_id      -> int          external unique service id
 *      file_id         -> int          internal FK->cc_files track id
 *      upload_time     -> timestamp    internal upload timestamp
 *      status          -> string       external service status
 *
 * <br/><b>celery_tasks</b> schema:
 *
 *      id              -> int          PK
 *      task_id         -> string       external unique amqp results identifier
 *      track_reference -> int          internal FK->third_party_track_references id
 *      name            -> string       external Celery task name
 *      dispatch_time   -> timestamp    internal message dispatch time
 *      status          -> string       external Celery task status
 *
 */
class AirtimeUpgrader2513 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.12'
        );
    }

    public function getNewVersion() {
        return '2.5.13';
    }
}

/**
 * Class AirtimeUpgrader2514
 *
 * SAAS-923 - Add a partial constraint to cc_pref so that keystrings must be unique
 */
class AirtimeUpgrader2514 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions() {
        return array (
            '2.5.13'
        );
    }

    public function getNewVersion() {
        return '2.5.14';
    }
}
