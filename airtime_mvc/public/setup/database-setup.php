<?php
/**
 * User: sourcefabric
 * Date: 02/12/14
 *
 * Class DatabaseSetup
 *
 * Wrapper class for validating and installing the Airtime database during the installation process
 */
class DatabaseSetup extends Setup {

    const DB_USER = "dbUser",
        DB_PASS = "dbPass",
        DB_NAME = "dbName",
        DB_HOST = "dbHost";

    static $user, $pass, $name, $host;
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$user = $settings[self::DB_USER];
        self::$pass = $settings[self::DB_PASS];
        self::$name = $settings[self::DB_NAME];
        self::$host = $settings[self::DB_HOST];
    }

    /**
     * Runs various database checks against the given settings. If a database with the given name already exists,
     * we attempt to install the Airtime schema. If not, we first check if the user can create databases, then try
     * to create the database. If we encounter errors, the offending fields are returned in an array to the browser.
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        // Check the connection and user credentials
        if ($this->validateDatabaseConnection()) {
            // We know that the user credentials check out, so check if the database exists
            if ($this->validateDatabaseSettings()) {
                // The database already exists, so we can just set up the schema
                if ($this->createDatabaseTables()) {
                    self::$message = "Successfully installed Airtime database to '" . self::$name . "'";
                } else {
                    self::$message = "Something went wrong setting up the Airtime schema!";
                }
            } else {
                // The database doesn't exist, so check if the user can create databases
                if ($this->checkUserCanCreateDb()) {
                    // The user can create a database, do it
                    if ($this->createDatabase()) {
                        if ($this->createDatabaseTables()) {
                            self::$message = "Successfully installed Airtime database to '" . self::$name . "'";
                        } else {
                            self::$message = "Something went wrong setting up the Airtime schema!";
                        }
                    } else {
                        self::$message = "There was an error installing the database!";
                    }
                } // The user can't create databases, so we're done
                else {
                    self::$message = "No database " . self::$name . " exists; user " . self::$user
                        . " does not have permission to create databases on " . self::$host;
                }
            }
        }

        return array(
            "message" => self::$message,
            "errors" => self::$errors,
        );
    }

    /**
     * Check if the user's database connection is valid
     * @return boolean true if the connection are valid
     */
    function validateDatabaseConnection() {
        // This is pretty redundant, but we need to test both
        // the existence and the validity of the given credentials
        exec("export PGPASSWORD=" . self::$pass . " && psql -h "
             . self::$host . " -U " . self::$user . " 2>&1", $out, $status);
        foreach ($out as $o) {
            if (strpos($o, "host name")) {
                self::$message = "Invalid connection parameters!";
                self::$errors[] = self::DB_HOST;
                return false;
            } else if (strpos($o, "authentication")) {
                self::$message = "User credentials are invalid!";
                self::$errors[] = self::DB_USER;
                self::$errors[] = self::DB_PASS;
                return false;
            }
        }
        return $status == 0;
    }

    /**
     * Check if the database settings and credentials given are valid
     * @return boolean true if the database given exists and the user is valid and can access it
     */
    function validateDatabaseSettings() {
        exec("export PGPASSWORD=" . self::$pass . " && psql -lqt -h " . self::$host . " -U " . self::$user
             . "| cut -d \\| -f 1 | grep -w " . self::$name, $out, $status);
        return $status == 0;
    }

    /**
     * Check if the given user has access on the given host to create a new database
     * @return boolean true if the given user has permission to create a database on the given host
     */
    function checkUserCanCreateDb() {
        exec("export PGPASSWORD=" . self::$pass . " && psql -h " . self::$host . " -U " . self::$user . " -tAc"
             . "\"SELECT 1 FROM pg_roles WHERE rolname='" . self::$user . "' AND rolcreatedb='t'\"", $out, $status);
        return $status == 0;
    }

    /**
     * Creates the Airtime database using the given credentials
     * @return boolean true if the database was created
     */
    function createDatabase() {
        exec("export PGPASSWORD=" . self::$pass . " && psql -h " . self::$host . " -U " . self::$user . " -tAc"
             . "\"CREATE DATABASE " . self::$name . " WITH ENCODING 'UTF8' TEMPLATE template0 OWNER "
             . self::$user . "\"", $out, $status);
        return $status == 0;
    }

    /**
     * Creates the Airtime database schema using the given credentials
     * @return boolean true if the database tables were created without error
     */
    function createDatabaseTables() {
        $sqlDir = dirname(dirname(__DIR__)) . "/build/sql/";
        $files = array("schema.sql", "sequences.sql", "views.sql", "triggers.sql", "defaultdata.sql");

        foreach($files as $f) {
            try {
                exec("export PGPASSWORD=" . self::$pass . " && psql -U " . self::$user . " --dbname "
                     . self::$name . " -h " . self::$host . " -f $sqlDir$f 2>/dev/null", $out, $status);
            } catch(Exception $e) {
                return false;
            }
        }
        return true;
    }

}
