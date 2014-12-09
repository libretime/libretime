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
    static $user, $pass, $name, $host;

    // Array of key->value pairs for airtime.conf
    static $properties;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$user = $settings[self::DB_USER];
        self::$pass = $settings[self::DB_PASS];
        self::$name = $settings[self::DB_NAME];
        self::$host = $settings[self::DB_HOST];

        self::$properties = array(
            "host" => self::$host,
            "dbname" => self::$name,
            "dbuser" => self::$user,
            "dbpass" => self::$pass,
        );
    }

    /**
     * Runs various database checks against the given settings. If a database with the given name already exists,
     * we attempt to install the Airtime schema. If not, we first check if the user can create databases, then try
     * to create the database. If we encounter errors, the offending fields are returned in an array to the browser.
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        // Check the connection and user credentials
        if ($this->checkDatabaseConnection()) {
            // We know that the user credentials check out, so check if the database exists
            if ($this->checkDatabaseExists()) {
                // The database already exists, check if the schema exists as well
                if ($this->checkSchemaExists()) {
                    self::$message = "Airtime is already installed in this database!";
                } else {
                    if ($this->createDatabaseTables()) {
                        self::$message = "Successfully installed Airtime database to '" . self::$name . "'";
                    } else {
                        self::$message = "Something went wrong setting up the Airtime schema!";
                        self::$errors[] = self::DB_NAME;
                    }
                }
            } else {
                // The database doesn't exist, so check if the user can create databases
                if ($this->checkUserCanCreateDb()) {
                    // The user can create a database, do it
                    if ($this->createDatabase()) {
                        // Ensure that the database was installed in UTF8 (we only care about the Airtime database)
                        if ($this->checkDatabaseSchema()) {
                            if ($this->createDatabaseTables()) {
                                self::$message = "Successfully installed Airtime database to '" . self::$name . "'";
                            } else {
                                self::$message = "Something went wrong setting up the Airtime schema!";
                                self::$errors[] = self::DB_NAME;
                            }
                        } else {
                            self::$message = "The database was installed with an incorrect encoding type!";
                            self::$errors[] = self::DB_NAME;
                        }
                    } else {
                        self::$message = "There was an error installing the database!";
                        self::$errors[] = self::DB_NAME;
                    }
                } // The user can't create databases, so we're done
                else {
                    self::$message = "No database " . self::$name . " exists; user " . self::$user
                        . " does not have permission to create databases on " . self::$host;
                    self::$errors[] = self::DB_NAME;
                }
            }
        }

        if (count(self::$errors) <= 0) {
            $this->writeToTemp();
        }

        return array(
            "message" => self::$message,
            "errors" => self::$errors,
        );
    }

    function writeToTemp() {
        parent::writeToTemp(self::SECTION, self::$properties);
    }


    /**
     * Check if the user's database connection is valid
     * @return boolean true if the connection are valid
     */
    function checkDatabaseConnection() {
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
    function checkDatabaseExists() {
        exec("export PGPASSWORD=" . self::$pass . " && psql -lqt -h " . self::$host . " -U " . self::$user
             . "| cut -d \\| -f 1 | grep -w " . self::$name, $out, $status);
        return $status == 0;
    }

    /**
     * Check if the database schema has already been set up
     * @return boolean true if the database schema exists
     */
    function checkSchemaExists() {
        // Check for cc_pref to see if the schema is already installed in this database
        exec("export PGPASSWORD=" . self::$pass . " && psql -U " . self::$user . " -h "
             . self::$host . " -d " . self::$name . " -tAc \"SELECT * FROM cc_pref\"", $out, $status);
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

        foreach ($files as $f) {
            try {
                exec("export PGPASSWORD=" . self::$pass . " && psql -U " . self::$user . " --dbname "
                     . self::$name . " -h " . self::$host . " -f $sqlDir$f 2>/dev/null", $out, $status);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks whether the newly-created database's encoding was properly set to UTF8
     * @return boolean true if the database encoding is UTF8
     */
    function checkDatabaseEncoding() {
        exec("export PGPASSWORD=" . self::$pass . " && psql -U " . self::$user . " -h "
             . self::$host . " -d " . self::$name . " -tAc \"SHOW SERVER_ENCODING\"", $out, $status);
        return $out && $out[0] == "UTF8";
    }

    // TODO Since we already check the encoding, is there a purpose to verifying the schema?
    function checkDatabaseSchema() {
        $outFile = "/tmp/tempSchema.xml";
        exec("export PGPASSWORD=" . self::$pass . " && psql -U " . self::$user . " -h "
             . self::$host . " -o ${outFile} -tAc \"SELECT database_to_xml(FALSE, FALSE, '"
             . self::$name . "')\"", $out, $status);
    }

}
