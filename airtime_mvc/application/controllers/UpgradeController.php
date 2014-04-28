<?php

class UpgradeController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $airtime_upgrade_version = '2.5.3';
        
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->verifyAuth()) {
            return;
        }
        
        if (!$this->verifyAirtimeVersion()) {
            return;
        }

        $con = Propel::getConnection();
        $con->beginTransaction();
        try {
            //Disable Airtime UI
            //create a temporary maintenance notification file
            //when this file is on the server, zend framework redirects all
            //requests to the maintenance page and sets a 503 response code

            $maintenanceFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."maintenance.txt" : "/tmp/maintenance.txt";
            $file = fopen($maintenanceFile, 'w');
            fclose($file);

            //Begin upgrade
            
            //Update disk_usage value in cc_pref
            $storDir = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."srv/airtime/stor" : "/srv/airtime/stor";
            $diskUsage = shell_exec("du -sb $storDir | awk '{print $1}'");
        
            Application_Model_Preference::setDiskUsage($diskUsage);
            
            //update application.ini
            $iniFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."application.ini" : "/usr/share/airtime/application/configs/application.ini";
            
            $newLines = "resources.frontController.moduleDirectory = APPLICATION_PATH \"/modules\"\n".
                        "resources.frontController.plugins.putHandler = \"Zend_Controller_Plugin_PutHandler\"\n".
                        ";load everything in the modules directory including models\n".
                        "resources.modules[] = \"\"\n";
    
            $currentIniFile = file_get_contents($iniFile);
    
            /* We want to add the new lines after a specific line. So we must find read the file 
             * into an array, find the key to which our desired line belongs, and use the key
             * to split the file in two halves, $beginning and $end.
             * The $newLines will go inbetween $beginning and $end
             */
            $lines = explode("\n", $currentIniFile);

            $key = array_search("resources.layout.layoutPath = APPLICATION_PATH \"/layouts/scripts/\"", $lines);
            if (!$key) {
                throw new Exception('Upgrade to Airtime 2.5.3 FAILED. Could not upgrade application.ini');
            }

            $beginning = implode("\n", array_slice($lines, 0, $key));
            $end = implode("\n", array_slice($lines, $key));
            
            if (!is_writeable($iniFile)) {
                throw new Exception('Upgrade to Airtime 2.5.3 FAILED. Could not upgrade application.ini - Permission denied.');
            }
            $file = new SplFileObject($iniFile, "w");
            $file->fwrite($beginning."\n".$newLines.$end);

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
            
            //delete maintenance.txt to give users access back to Airtime
            unlink($maintenanceFile);

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody("Upgrade to Airtime 2.5.3 OK");

        } catch(Exception $e) {
            $con->rollback();
            unlink($maintenanceFile);
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody($e->getMessage());
        }
    }

    private function verifyAuth()
    {
        //The API key is passed in via HTTP "basic authentication":
        //http://en.wikipedia.org/wiki/Basic_access_authentication
        
        $CC_CONFIG = Config::getConfig();
        
        //Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader("Authorization");

        $encodedRequestApiKey = substr($authHeader, strlen("Basic "));
        $encodedStoredApiKey = base64_encode($CC_CONFIG["apiKey"][0] . ":");

        if ($encodedRequestApiKey !== $encodedStoredApiKey)
        {
            $this->getResponse()
                ->setHttpResponseCode(401)
                ->appendBody("Error: Incorrect API key.");
            return false;
        }
        return true;
    }

    private function verifyAirtimeVersion()
    {
        $pref = CcPrefQuery::create()
            ->filterByKeystr('system_version')
            ->findOne();
        $airtime_version = $pref->getValStr();

        if (!in_array($airtime_version, array('2.5.1', '2.5.2'))) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("Upgrade to Airtime 2.5.3 FAILED. You must be using Airtime 2.5.1 or 2.5.2 to upgrade.");
            return false;
        }
        return true;
    }
}
