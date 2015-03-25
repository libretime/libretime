<?php

class UpgradeManager
{
    /** Used to determine if the database schema needs an upgrade in order for this version of the Airtime codebase to work correctly.
     * @return array A list of schema versions that this version of the codebase supports.
     */
    public static function getSupportedSchemaVersions()
    {
        //What versions of the schema does the code support today:
        return array('2.5.2');
    }

    public static function checkIfUpgradeIsNeeded()
    {
        $schemaVersion = Application_Model_Preference::GetSchemaVersion();
        $supportedSchemaVersions = self::getSupportedSchemaVersions();
        $upgradeNeeded = !in_array($schemaVersion, $supportedSchemaVersions);
        if ($upgradeNeeded) {
            self::doUpgrade();
        }
    }

    public static function doUpgrade()
    {
        $upgradeManager = new UpgradeManager();
        $upgraders = array();
        array_push($upgraders, new AirtimeUpgrader252());
        /* These upgrades do not apply to open source Airtime yet.
        array_push($upgraders, new AirtimeUpgrader253());
        array_push($upgraders, new AirtimeUpgrader254());
        */
        return $upgradeManager->runUpgrades(array(new AirtimeUpgrader252()), (dirname(__DIR__) . "/controllers"));
    }

    /**
     * Run a given set of upgrades
     * 
     * @param array $upgraders the upgrades to perform
     * @param string $dir the directory containing the upgrade sql
     * @return boolean whether or not an upgrade was performed
     */
    public function runUpgrades($upgraders, $dir) {
        $upgradePerformed = false;
        
        for($i = 0; $i < count($upgraders); $i++) {
            $upgrader = $upgraders[$i];
            if ($upgrader->checkIfUpgradeSupported()) {
                // pass the given directory to the upgrades, since __DIR__ returns parent dir of file, not executor
                $upgrader->upgrade($dir); // This will throw an exception if the upgrade fails.
                $upgradePerformed = true;
                $i = 0; // Start over, in case the upgrade handlers are not in ascending order.
            }
        }
        
        return $upgradePerformed;
    }

}

abstract class AirtimeUpgrader
{
    /** Schema versions that this upgrader class can upgrade from (an array of version strings). */
    abstract protected function getSupportedSchemaVersions();
    /** The schema version that this upgrader class will upgrade to. (returns a version string) */
    abstract public function getNewVersion();

    public static function getCurrentSchemaVersion()
    {
        CcPrefPeer::clearInstancePool(); //Ensure we don't get a cached Propel object (cached DB results) 
                                         //because we're updating this version number within this HTTP request as well.

        //Old versions use system_version
        $pref = CcPrefQuery::create()
        ->filterByKeystr('system_version')
        ->findOne();
        if (empty($pref)) {
            //New versions use schema_version
            $pref = CcPrefQuery::create()
                ->filterByKeystr('schema_version')
                ->findOne();
        }
        $schema_version = $pref->getValStr();
        return $schema_version;
    }
    
    /** 
     * This function checks to see if this class can perform an upgrade of your version of Airtime
     * @return boolean True if we can upgrade your version of Airtime.
     */
    public function checkIfUpgradeSupported()
    {        
        if (!in_array(AirtimeUpgrader::getCurrentSchemaVersion(), $this->getSupportedSchemaVersions())) {
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

/** This upgrade adds schema changes to accommodate show artwork and show instance descriptions */
class AirtimeUpgrader252 extends AirtimeUpgrader {
	protected function getSupportedSchemaVersions() {
		return array (
            '2.5.1'
		);
	}
	
	public function getNewVersion() {
		return '2.5.2';
	}
	
	public function upgrade($dir = __DIR__) {
		Cache::clear();
		assert($this->checkIfUpgradeSupported());
		
		$newVersion = $this->getNewVersion();
		
		try {
			$this->toggleMaintenanceScreen(true);
			Cache::clear();
			
			// Begin upgrade
			$airtimeConf = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
			$values = parse_ini_file($airtimeConf, true);
			
			$username = $values['database']['dbuser'];
			$password = $values['database']['dbpass'];
			$host = $values['database']['host'];
			$database = $values['database']['dbname'];
				
			passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/upgrade_sql/airtime_"
					.$this->getNewVersion()."/upgrade.sql $database 2>&1 | grep -v \"will create implicit index\"");
			
			Application_Model_Preference::SetSchemaVersion($newVersion);
			Cache::clear();
			
			$this->toggleMaintenanceScreen(false);
			
			return true;
		} catch(Exception $e) {
			$this->toggleMaintenanceScreen(false);
			throw $e;
		}
	}
}

class AirtimeUpgrader253 extends AirtimeUpgrader
{
    protected function getSupportedSchemaVersions()
    {
        return array('2.5.2');
    }
    public function getNewVersion()
    {
        return '2.5.3';
    }
    
    public function upgrade($dir = __DIR__)
    {
        Cache::clear();
        assert($this->checkIfUpgradeSupported());
        
        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            
            $this->toggleMaintenanceScreen(true);
            Cache::clear();
            
            //Begin upgrade
        
            //Update disk_usage value in cc_pref
            $musicDir = CcMusicDirsQuery::create()
            ->filterByType('stor')
            ->filterByExists(true)
            ->findOne();
            $storPath = $musicDir->getDirectory();
        
            //Update disk_usage value in cc_pref
            $storDir = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."srv/airtime/stor" : "/srv/airtime/stor";
            $diskUsage = shell_exec("du -sb $storDir | awk '{print $1}'");
        
            Application_Model_Preference::setDiskUsage($diskUsage);
                    
            //clear out the cache
            Cache::clear();
            
            $con->commit();
        
            //update system_version in cc_pref and change some columns in cc_files
            $airtimeConf = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
            $values = parse_ini_file($airtimeConf, true);
        
            $username = $values['database']['dbuser'];
            $password = $values['database']['dbpass'];
            $host = $values['database']['host'];
            $database = $values['database']['dbname'];
        
            passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/upgrade_sql/airtime_".$this->getNewVersion()."/upgrade.sql $database 2>&1 | grep -v \"will create implicit index\"");
        
            Application_Model_Preference::SetSchemaVersion($this->getNewVersion());
            //clear out the cache
            Cache::clear();
            
            $this->toggleMaintenanceScreen(false);
                    
        } catch (Exception $e) {
            $con->rollback();
            $this->toggleMaintenanceScreen(false);
        }        
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
    
    public function upgrade()
    {
        Cache::clear();
        
        assert($this->checkIfUpgradeSupported());
        
        $newVersion = $this->getNewVersion();
        
        $con = Propel::getConnection();
        //$con->beginTransaction();
        try {
            $this->toggleMaintenanceScreen(true);
            Cache::clear();
            
            //Begin upgrade

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
                    //TODO: Otherwise get the user with the lowest ID that is of type administrator:
                    //
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
                Logging::info($_SERVER['HTTP_HOST'] . ': ' . $newVersion . " Upgrade: Promoted user " . $adminUser->getLogin() . " to be a Super Admin.");
                
                //Also try to promote the sourcefabric_admin user
                $sofabAdminUser = CcSubjsQuery::create()
                ->filterByDbLogin('sourcefabric_admin')
                ->findOne();
                if ($sofabAdminUser) {
                    $sofabAdminUser = new Application_Model_User($sofabAdminUser->getDbId());
                    $sofabAdminUser->setType(UTYPE_SUPERADMIN);
                    $sofabAdminUser->save();
                    Logging::info($_SERVER['HTTP_HOST'] . ': ' . $newVersion . " Upgrade: Promoted user " . $sofabAdminUser->getLogin() . " to be a Super Admin.");                  
                }
            }
            
            //$con->commit();
            Application_Model_Preference::SetSchemaVersion($newVersion);
            Cache::clear();
            
            $this->toggleMaintenanceScreen(false);
                        
            return true;
            
        } catch(Exception $e) {
            //$con->rollback();
            $this->toggleMaintenanceScreen(false);
            throw $e; 
        }
    }
}

/* We are skipping 2.5.5 because it used to be the Show Artwork.
 *
 * DO NOT USE schema version 2.5.5!
 */


/** This is a stub. Please implement this 2.5.6 upgrader for the next schema change that we need. 
 *  (It's setup to upgrade from 2.5.4 and 2.5.5 - this is a must due to the 2.5.5 schema being phase out. Long story...
 *  
 */ 
class AirtimeUpgrader256 extends AirtimeUpgrader {
	protected function getSupportedSchemaVersions() {
		return array (
                    '2.5.4', '2.5.5'
		);
	}
	
	public function getNewVersion() {
		return '2.5.6';
	}
	
	public function upgrade($dir = __DIR__) {
		Cache::clear();
		assert($this->checkIfUpgradeSupported());
		
		$newVersion = $this->getNewVersion();
		
                try {
                    //TODO: Implement this
                    return true;
		} catch(Exception $e) {
			$this->toggleMaintenanceScreen(false);
			throw $e;
		}
	}
}
