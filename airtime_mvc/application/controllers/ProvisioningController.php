<?php

require_once 'ProxyStorageBackend.php';

use Aws\S3\S3Client;

class ProvisioningController extends Zend_Controller_Action
{

    static $dbh;

    // Parameter values
    private $dbuser, $dbpass, $dbname, $dbhost;

    public function init()
    {
    }

    /**
     * Delete the Airtime Pro station's files from Amazon S3
     */
    public function terminateAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!RestAuth::verifyAuth(true, true, $this)) {
            return;
        }
        
        $CC_CONFIG = Config::getConfig();
        
        foreach ($CC_CONFIG["supportedStorageBackends"] as $storageBackend) {
            $proxyStorageBackend = new ProxyStorageBackend($storageBackend);
            $proxyStorageBackend->deleteAllCloudFileObjects();
        }
        
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("OK");
    }
    
    /**
     * RESTful endpoint for setting up and installing the Airtime database
     */
    public function createDatabaseAction() {
        Logging::info("Create Database action received");

        if (!RestAuth::verifyAuth(true, true, $this)) {
            return;
        }

        try {
            $this->getParams();
            $this->setNewDatabaseConnection();
            $this->createDatabaseTables();
        } catch(Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody($e->getMessage());
            return;
        }

        $this->getResponse()
            ->setHttpResponseCode(201);
    }

    private function getParams() {
        $this->dbuser = $this->_getParam('dbuser', '');
        $this->dbpass = $this->_getParam('dbpass', '');
        $this->dbname = $this->_getParam('dbname', '');
        $this->dbhost = $this->_getParam('dbhost', '');
    }

    /**
     * Set up a new database connection based on the parameters in the request
     * @throws PDOException upon failure to connect
     */
    private function setNewDatabaseConnection() {
        self::$dbh = new PDO("pgsql:host=" . $this->dbhost
                             . ";dbname=" . $this->dbname
                             . ";port=5432" . ";user=" . $this->dbuser
                             . ";password=" . $this->dbpass);
        $err = self::$dbh->errorInfo();
        if ($err[1] != null) {
            throw new PDOException("ERROR: Could not connect to database");
        }
    }

    /**
     * Install the Airtime database
     * @throws Exception
     */
    private function createDatabaseTables() {
        $sqlDir = dirname(APPLICATION_PATH) . "/build/sql/";
        $files = array("schema.sql", "sequences.sql", "views.sql", "triggers.sql", "defaultdata.sql");
        foreach ($files as $f) {
            try {
                /*
                 * Unfortunately, we need to use exec here due to PDO's lack of support for importing
                 * multi-line .sql files. PDO->exec() almost works, but any SQL errors stop the import,
                 * so the necessary DROPs on non-existent tables make it unusable. Prepared statements
                 * have multiple issues; they similarly die on any SQL errors, fail to read in multi-line
                 * commands, and fail on any unescaped ? or $ characters.
                 */
                exec("export PGPASSWORD=" . $this->dbpass . " && psql -U " . $this->dbuser . " --dbname "
                     . $this->dbname . " -h " . $this->dbhost . " -f $sqlDir$f 2>/dev/null", $out, $status);
            } catch (Exception $e) {
                throw new Exception("ERROR: Failed to create database tables");
            }
        }
    }

}
