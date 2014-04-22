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
        
        //Begin upgrade
        $filename = isset($_SERVER['AIRTIME_CONF']) ? $_SERVER['AIRTIME_CONF'] : "/etc/airtime/airtime.conf";
        $values = parse_ini_file($filename, true);
        
        $username = $values['database']['dbuser'];
        $password = $values['database']['dbpass'];
        $host = $values['database']['host'];
        $database = $values['database']['dbname'];
        $dir = __DIR__;
        
        passthru("export PGPASSWORD=$password && psql -h $host -U $username -q -f $dir/upgrade_sql/airtime_$airtime_upgrade_version/upgrade.sql $database 2>&1 | grep -v \"will create implicit index\"");
        
        $storDir = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."srv/airtime/stor" : "/srv/airtime/stor";
        $diskUsage = shell_exec("du -sb $storDir | awk '{print $1}'");
        
        Application_Model_Preference::setDiskUsage($diskUsage);

        $iniFile = isset($_SERVER['AIRTIME_BASE']) ? $_SERVER['AIRTIME_BASE']."application.ini" : "/usr/share/airtime/application/configs/application.ini";
        
        //update application.ini
        $newLines = "resources.frontController.moduleDirectory = APPLICATION_PATH '/modules'\n".
                    "resources.frontController.plugins.putHandler = 'Zend_Controller_Plugin_PutHandler'".
                    ";load everything in the modules directory including models".
                    "resources.modules[] = ''";

        $file = fopen($iniFile, "r+");
        //set pointer to line after '[production]' - kind of hacky but will do for now
        fseek($file, -1, SEEK_CUR);
        fwrite($file, $newLines);
        fclose($file);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("Upgrade to Airtime 2.5.3 OK");
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

        if ($airtime_version != '2.5.2') {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("Upgrade to Airtime 2.5.3 FAILED. You must be using Airtime 2.5.2 to upgrade.");
            return false;
        }
        return true;
    }
}