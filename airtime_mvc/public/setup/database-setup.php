<?php

/**
 * User: sourcefabric
 * Date: 02/12/14
 * Class DatabaseSetup
 * Wrapper class for validating and installing the Airtime database during the installation process
 */
class DatabaseSetup extends Setup {
    
    // airtime.conf section header
    protected static $_section = "[database]";

    // Constant form field names for passing errors back to the front-end
    const DB_USER = "dbUser",
        DB_PASS = "dbPass",
        DB_NAME = "dbName",
        DB_HOST = "dbHost";

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    /**
     * @var PDO
     */
    static $dbh = null;

    public function __construct($settings) {
        static::$_properties = array(
            "host"   => $settings[self::DB_HOST],
            "dbname" => $settings[self::DB_NAME],
            "dbuser" => $settings[self::DB_USER],
            "dbpass" => $settings[self::DB_PASS],
        );
    }

    private function setNewDatabaseConnection($dbName) {
        self::$dbh = new PDO("pgsql:host=" . self::$_properties["host"] . ";dbname=" . $dbName . ";port=5432"
                             . ";user=" . self::$_properties["dbuser"] . ";password=" . self::$_properties["dbpass"]);
        $err = self::$dbh->errorInfo();
        if ($err[1] != null) {
            throw new PDOException();
        }
    }

    /**
     * Runs various database checks against the given settings. If a database with the given name already exists,
     * we attempt to install the Airtime schema. If not, we first check if the user can create databases, then try
     * to create the database. If we encounter errors, the offending fields are returned in an array to the browser.
     * @return array associative array containing a display message and fields with errors
     * @throws AirtimeDatabaseException
     */
    public function runSetup() {
        $this->writeToTemp();
        try {
            $this->setNewDatabaseConnection("postgres");
            if ($this->checkDatabaseExists()) {
                $this->installDatabaseTables();
            } else {
                $this->checkUserCanCreateDb();
                $this->createDatabase();
                $this->installDatabaseTables();
            }
        } catch (PDOException $e) {
            throw new AirtimeDatabaseException("Couldn't establish a connection to the database! ".
                                                "Please check your credentials and try again. "
                                               . "PDO Exception: " .  $e->getMessage(),
                                               array(self::DB_NAME, self::DB_USER, self::DB_PASS));
        }
        self::$dbh = null;
        return array(
            "message" => "Airtime database was created successfully!",
            "errors" => array(),
        );
    }

    private function installDatabaseTables() {
        $this->checkDatabaseEncoding();
        $this->setNewDatabaseConnection(self::$_properties["dbname"]);
        $this->checkSchemaExists();
        $this->createDatabaseTables();
    }

    /**
     * Check if the database settings and credentials given are valid
     * @return boolean true if the database given exists and the user is valid and can access it
     */
    private function checkDatabaseExists() {
        $statement = self::$dbh->prepare("SELECT datname FROM pg_database WHERE datname = :dbname");
        $statement->execute(array(":dbname" => self::$_properties["dbname"]));
        $result = $statement->fetch();
        return isset($result[0]);
    }

    /**
     * Check if the database schema has already been set up
     * @throws AirtimeDatabaseException
     */
    private function checkSchemaExists() {
        $statement = self::$dbh->prepare("SELECT EXISTS (SELECT relname FROM pg_class WHERE relname='cc_files')");
        $statement->execute();
        $result = $statement->fetch();
        if (isset($result[0]) && $result[0] == "t") {
            throw new AirtimeDatabaseException("Airtime is already installed in this database!", array());
        }
    }

    /**
     * Check if the given user has access on the given host to create a new database
     * @throws AirtimeDatabaseException
     */
    private function checkUserCanCreateDb() {
        $statement = self::$dbh->prepare("SELECT 1 FROM pg_roles WHERE rolname=:dbuser AND rolcreatedb='t'");
        $statement->execute(array(":dbuser" => self::$_properties["dbuser"]));
        $result = $statement->fetch();
        if (!isset($result[0])) {
            throw new AirtimeDatabaseException("No database " . self::$_properties["dbname"] . " exists; user '"
                                               . self::$_properties["dbuser"] . "' does not have permission to "
                                               . "create databases on " . self::$_properties["host"],
                                               array(self::DB_NAME, self::DB_USER, self::DB_PASS));
        }
    }

    /**
     * Creates the Airtime database using the given credentials
     * @throws AirtimeDatabaseException
     */
    private function createDatabase() {
        $statement = self::$dbh->prepare("CREATE DATABASE " . pg_escape_string(self::$_properties["dbname"])
                                         . " WITH ENCODING 'UNICODE' TEMPLATE template0"
                                         . " OWNER " . pg_escape_string(self::$_properties["dbuser"]));
        if (!$statement->execute()) {
            throw new AirtimeDatabaseException("There was an error creating the database!",
                                               array(self::DB_NAME,));
        }
    }

    /**
     * Creates the Airtime database schema using the given credentials
     * @throws AirtimeDatabaseException
     */
    private function createDatabaseTables() {
        $sqlDir = dirname(dirname(__DIR__)) . "/build/sql/";
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
                exec("export PGPASSWORD=" . self::$_properties["dbpass"] . " && psql -U " . self::$_properties["dbuser"]
                     . " --dbname " . self::$_properties["dbname"] . " -h " . self::$_properties["host"]
                     . " -f $sqlDir$f 2>/dev/null", $out, $status);
            } catch (Exception $e) {
                throw new AirtimeDatabaseException("There was an error setting up the Airtime schema!",
                                                   array(self::DB_NAME,));
            }
        }
    }

    /**
     * Checks whether the newly-created database's encoding was properly set to UTF8
     * @throws AirtimeDatabaseException
     */
    private function checkDatabaseEncoding() {
        $statement = self::$dbh->prepare("SELECT pg_encoding_to_char(encoding) "
                                         . "FROM pg_database WHERE datname = :dbname");
        $statement->execute(array(":dbname" => self::$_properties["dbname"]));
        $encoding = $statement->fetch();
        if (!($encoding && $encoding[0] == "UTF8")) {
            throw new AirtimeDatabaseException("The database was installed with an incorrect encoding type!",
                                               array(self::DB_NAME,));
        }
    }

}
