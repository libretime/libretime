<?php

/**
 * User: sourcefabric
 * Date: 02/12/14
 * Class DatabaseSetup
 * Wrapper class for validating and installing the Airtime database during the installation process
 */
class DatabaseSetup extends Setup {

    // airtime.conf section header
    const SECTION = "[database]";

    // Constant form field names for passing errors back to the front-end
    const DB_USER = "dbUser",
        DB_PASS = "dbPass",
        DB_NAME = "dbName",
        DB_HOST = "dbHost";

    // Form field values
    private $user, $pass, $name, $host;

    // Array of key->value pairs for airtime.conf
    static $properties;

    static $dbh = null;

    public function __construct($settings) {
        $this->user = $settings[self::DB_USER];
        $this->pass = $settings[self::DB_PASS];
        $this->name = $settings[self::DB_NAME];
        $this->host = $settings[self::DB_HOST];

        self::$properties = array(
            "host" => $this->host,
            "dbname" => $this->name,
            "dbuser" => $this->user,
            "dbpass" => $this->pass,
        );
    }

    private function setNewDatabaseConnection($dbName) {
        self::$dbh = new PDO("pgsql:host=" . $this->host . ";dbname=" . $dbName . ";port=5432"
                             . ";user=" . $this->user . ";password=" . $this->pass);
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
            throw new AirtimeDatabaseException("Couldn't establish a connection to the database! "
                                               . "Please check your credentials and try again. "
                                               . "PDO Exception: " .  $e->getMessage(),
                                               array(
                                                   self::DB_NAME,
                                                   self::DB_USER,
                                                   self::DB_PASS,
                                               ));
        }

        $this->writeToTemp();

        self::$dbh = null;
        return array(
            "message" => "Airtime database was created successfully!",
            "errors" => array(),
        );
    }

    protected function writeToTemp() {
        parent::writeToTemp(self::SECTION, self::$properties);
    }

    private function installDatabaseTables() {
        $this->checkDatabaseEncoding();
        $this->setNewDatabaseConnection($this->name);
        $this->checkSchemaExists();
        $this->createDatabaseTables();
    }

    /**
     * Check if the database settings and credentials given are valid
     * @return boolean true if the database given exists and the user is valid and can access it
     */
    private function checkDatabaseExists() {
        $statement = self::$dbh->prepare("SELECT datname FROM pg_database WHERE datname = :dbname");
        $statement->execute(array(":dbname" => $this->name));
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
        $statement->execute(array(":dbuser" => $this->user));
        $result = $statement->fetch();
        if (!isset($result[0])) {
            throw new AirtimeDatabaseException("No database " . $this->name . " exists; user '" . $this->user
                                               . "' does not have permission to create databases on " . $this->host,
                                               array(
                                                   self::DB_NAME,
                                                   self::DB_USER,
                                                   self::DB_PASS,
                                               ));
        }
    }

    /**
     * Creates the Airtime database using the given credentials
     * @throws AirtimeDatabaseException
     */
    private function createDatabase() {
        $statement = self::$dbh->prepare("CREATE DATABASE " . pg_escape_string($this->name)
                                         . " WITH ENCODING 'UTF8' TEMPLATE template0"
                                         . " OWNER " . pg_escape_string($this->user));
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
                exec("export PGPASSWORD=" . $this->pass . " && psql -U " . $this->user . " --dbname "
                     . $this->name . " -h " . $this->host . " -f $sqlDir$f 2>/dev/null", $out, $status);
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
        $statement->execute(array(":dbname" => $this->name));
        $encoding = $statement->fetch();
        if (!($encoding && $encoding[0] == "UTF8")) {
            throw new AirtimeDatabaseException("The database was installed with an incorrect encoding type!",
                                               array(self::DB_NAME,));
        }
    }

}