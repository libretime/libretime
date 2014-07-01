<?php

abstract class AirtimeUpgrader
{
    /** Versions that this upgrader class can upgrade from (an array of version strings). */
    abstract protected function getSupportedVersions();
    /** The version that this upgrader class will upgrade to. (returns a version string) */
    abstract public function getNewVersion();

    public static function getCurrentVersion()
    {
        $pref = CcPrefQuery::create()
        ->filterByKeystr('system_version')
        ->findOne();
        $airtime_version = $pref->getValStr();
        return $airtime_version;
    }
    
    /** 
     * This function checks to see if this class can perform an upgrade of your version of Airtime
     * @return boolean True if we can upgrade your version of Airtime.
     */
    public function checkIfUpgradeSupported()
    {        
        if (!in_array(AirtimeUpgrader::getCurrentVersion(), $this->getSupportedVersions())) {
            return false;
        }
        return true;
    }
    
    protected function toggleMaintenanceScreen($toggle)
    {
        if ($toggle)
        {
            //Disable Airtime UI
            //create a temporary maintenance notification file
            //when this file is on the server, zend framework redirects all
            //requests to the maintenance page and sets a 503 response code
            $this->maintenanceFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."maintenance.txt" : "/tmp/maintenance.txt";
            $file = fopen($this->maintenanceFile, 'w');
            fclose($file);
        } else {
            //delete maintenance.txt to give users access back to Airtime
            if ($this->maintenanceFile) {
                unlink($this->maintenanceFile);
            }
        }
    }
            
    /** Implement this for each new version of Airtime */
    abstract public function upgrade();
}

class AirtimeUpgrader253 extends AirtimeUpgrader
{
    protected function getSupportedVersions()
    {
        return array('2.5.1', '2.5.2');
    }
    public function getNewVersion()
    {
        return '2.5.3';
    }
    
    public function upgrade()
    {
        assert($this->checkIfUpgradeSupported());
        
        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            
            $this->toggleMaintenanceScreen(true);
            
            //Begin upgrade
        
            //Update disk_usage value in cc_pref
            $musicDir = CcMusicDirsQuery::create()
            ->filterByType('stor')
            ->filterByExists(true)
            ->findOne();
            $storPath = $musicDir->getDirectory();
        
            $freeSpace = disk_free_space($storPath);
            $totalSpace = disk_total_space($storPath);
        
            Application_Model_Preference::setDiskUsage($totalSpace - $freeSpace);
        
            //TODO: clear out the cache
        
            $con->commit();
        
            //update system_version in cc_pref and change some columns in cc_files
            $airtimeConf = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
            $values = parse_ini_file($airtimeConf, true);
        
            $username = $values['database']['dbuser'];
            $password = $values['database']['dbpass'];
            $host = $values['database']['host'];
            $database = $values['database']['dbname'];
            $dir = __DIR__;
        
            passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/upgrade_sql/airtime_$airtime_upgrade_version/upgrade.sql $database 2>&1 | grep -v \"will create implicit index\"");
        
            Application_Model_Preference::SetAirtimeVersion($this->getNewVersion());
            
            $this->toggleMaintenanceScreen(false);
                    
        } catch (Exception $e) {
            $con->rollback();
            $this->toggleMaintenanceScreen(false);
        }        
    }
}

class AirtimeUpgrader254 extends AirtimeUpgrader
{
    protected function getSupportedVersions()
    {
        return array('2.5.3');
    }
    public function getNewVersion()
    {
        return '2.5.4';
    }
    
    public function upgrade()
    {
        assert($this->checkIfUpgradeSupported());

        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            $this->toggleMaintenanceScreen(true);
            
            //Begin upgrade

            //First, ensure there are no superadmins already.
            $numberOfSuperAdmins = CcSubjsQuery::create()
            ->filterByType(UTYPE_SUPERADMIN)
            ->count();
            
            //Only create a super admin if there isn't one already.
            if ($numberOfSuperAdmins == 0)
            {
                //Find the "admin" user and promote them to superadmin.
                $adminUser = CcSubjsQuery::create()
                ->filterByLogin('admin')
                ->findOne();
                if (!$adminUser)
                {
                    //TODO: Otherwise get the user with the lowest ID that is of type administrator:
                    //
                    $adminUser = CcSubjsQuery::create()
                    ->filterByType(UTYPE_ADMIN)
                    ->orderByDbId(Criteria::ASC)
                    ->findOne();
                    
                    if (!$adminUser) {
                        throw new Exception("Failed to find any users of type 'admin' ('A').");
                    }
                }
                
                $adminUser = new Application_Model_User($adminUser);
                $adminUser->setType(UTYPE_SUPERADMIN);
                $adminUser->save();
                Logging::info($this->getNewVersion() . " Upgrade: Promoted user " . $adminUser->getLogin() . " to be a Super Admin.");    
            }
            
            $con->commit();
            
            $this->toggleMaintenanceScreen(false);
            
            Application_Model_Preference::SetAirtimeVersion($this->getNewVersion());
            
            return true;
            
        } catch(Exception $e) {
            $con->rollback();
            $this->toggleMaintenanceScreen(false);
            throw $e; 
        }
    }
}