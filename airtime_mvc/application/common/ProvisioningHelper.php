<?php

/** This class provides the business logic for station provisioning.  */
class ProvisioningHelper
{

    /* @var $dbh PDO */
    static $dbh;

    // Parameter values
    private $dbuser, $dbpass, $dbname, $dbhost, $dbowner, $apikey;
    private $instanceId;

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
    }

    /**
     * Endpoint for setting up and installing the Airtime database. This all has to be done without Zend
     * which is why the code looks so old school (eg. http_response_code). (We can't currently bootstrap our
     * Zend app without the database unfortunately.)
     */
    public function createAction()
    {
        $apikey = $_SERVER['PHP_AUTH_USER'];
        if (!isset($apikey) || $apikey != $this->apikey) {
            Logging::info("Invalid API Key: $apikey");
            http_response_code(403);
            echo "ERROR: Incorrect API key";
            return;
        }

        try {

            $this->parsePostParams();
            
            //For security, the Airtime Pro provisioning system creates the database for the user.
            // $this->setNewDatabaseConnection();
            //if ($this->checkDatabaseExists()) {
            //    throw new Exception("ERROR: Airtime database already exists");
            //}
            //$this->createDatabase();

            //All we need to do is create the database tables.
            $this->createDatabaseTables();
            $this->initializeMusicDirsTable($this->instanceId);
        } catch (Exception $e) {
            http_response_code(400);
            Logging::error($e->getMessage());
            echo $e->getMessage();
            return;
        }

        http_response_code(201);
    }

    /**
     * Check if the database settings and credentials given are valid
     * @return boolean true if the database given exists and the user is valid and can access it
     */
    private function checkDatabaseExists()
    {
        $statement = self::$dbh->prepare("SELECT datname FROM pg_database WHERE datname = :dbname");
        $statement->execute(array(":dbname" => $this->dbname));
        $result = $statement->fetch();
        return isset($result[0]);
    }

    private function parsePostParams()
    {
        $this->dbuser = $_POST['dbuser'];
        $this->dbpass = $_POST['dbpass'];
        $this->dbname = $_POST['dbname'];
        $this->dbhost = $_POST['dbhost'];
        $this->dbowner = $_POST['dbowner'];
        $this->instanceId = $_POST['instanceid'];
    }

    /**
     * Set up a new database connection based on the parameters in the request
     * @throws PDOException upon failure to connect
     */
    private function setNewDatabaseConnection()
    {
        self::$dbh = new PDO("pgsql:host=" . $this->dbhost
            . ";dbname=postgres"
            . ";port=5432" . ";user=" . $this->dbuser
            . ";password=" . $this->dbpass);
        $err = self::$dbh->errorInfo();
        if ($err[1] != null) {
            throw new PDOException("ERROR: Could not connect to database");
        }
    }

    /**
     * Creates the Airtime database using the given credentials
     * @throws Exception
     */
    private function createDatabase()
    {
        Logging::info("Creating database...");
        $statement = self::$dbh->prepare("CREATE DATABASE " . pg_escape_string($this->dbname)
            . " WITH ENCODING 'UTF8' TEMPLATE template0"
            . " OWNER " . pg_escape_string($this->dbowner));
        if (!$statement->execute()) {
            throw new Exception("ERROR: Failed to create Airtime database");
        }
    }

    /**
     * Install the Airtime database
     * @throws Exception
     */
    private function createDatabaseTables()
    {
        Logging::info("Creating database tables...");
        $sqlDir = dirname(APPLICATION_PATH) . "/build/sql/";
        $files = array("schema.sql", "sequences.sql", "views.sql", "triggers.sql", "defaultdata.sql");
        foreach ($files as $f) {
            /*
             * Unfortunately, we need to use exec here due to PDO's lack of support for importing
             * multi-line .sql files. PDO->exec() almost works, but any SQL errors stop the import,
             * so the necessary DROPs on non-existent tables make it unusable. Prepared statements
             * have multiple issues; they similarly die on any SQL errors, fail to read in multi-line
             * commands, and fail on any unescaped ? or $ characters.
             */
            exec("PGPASSWORD=$this->dbpass psql -U $this->dbuser --dbname $this->dbname -h $this->dbhost -f $sqlDir$f", $out, $status);
            if ($status != 0) {
                throw new Exception("ERROR: Failed to create database tables");
            }
        }
    }

    private function initializeMusicDirsTable($instanceId)
    {
        if (!is_string($instanceId) || empty($instanceId) || !is_numeric($instanceId))
        {
            throw new Exception("Invalid instance id: " . $instanceId);
        }

        $instanceIdPrefix = $instanceId[0];

        //Reinitialize Propel, just in case...
        Propel::init(__DIR__."/../configs/airtime-conf-production.php");

        //Create the cc_music_dir entry
        $musicDir = new CcMusicDirs();
        $musicDir->setType("stor");
        $musicDir->setExists(true);
        $musicDir->setWatched(true);
        $musicDir->setDirectory("/mnt/airtimepro/instances/$instanceIdPrefix/$instanceId/srv/airtime/stor/");
        $musicDir->save();
    }


}
