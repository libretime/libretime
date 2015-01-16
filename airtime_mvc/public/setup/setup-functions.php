<?php
define("BUILD_PATH", dirname(dirname(__DIR__)) . "/build/");
define("AIRTIME_CONF_TEMP_PATH", "/tmp/airtime.conf.temp");

/**
 * Class Setup
 *
 * @author sourcefabric
 * 
 * Abstract superclass for the setup and installation process
 */
abstract class Setup {

    abstract function __construct($settings);

    abstract function runSetup();

    /**
     * Write new property values to a given section in airtime.conf.temp
     *
     * @param string $section
     *            the configuration section to write to
     * @param array $properties
     *            the configuration properties and values to overwrite
     */
    protected function writeToTemp($section, $properties) {
        if (!file_exists(AIRTIME_CONF_TEMP_PATH)) {
            copy(BUILD_PATH . "airtime.example.conf", AIRTIME_CONF_TEMP_PATH);
        }
        
        $file = file(AIRTIME_CONF_TEMP_PATH);
        $fileOutput = "";
        
        $inSection = false;
        
        foreach ($file as $line) {
            if (strpos($line, $section) !== false) {
                $inSection = true;
            } else if (strpos($line, "[") !== false) {
                $inSection = false;
            }
            
            if (substr(trim($line), 0, 1) == "#") {
                /* Workaround to strip comments from airtime.conf.
                 * We need to do this because python's ConfigObj and PHP
                 * have different (and equally strict) rules about comment 
                 * characters in configuration files.
                 */
            } else if ($inSection) {
                $key = trim(@substr($line, 0, strpos($line, "=")));
                $fileOutput .= $key && isset($properties[$key]) ? $key . " = " . $properties[$key] . "\n" : $line;
            } else {
                $fileOutput .= $line;
            }
        }
        
        file_put_contents(AIRTIME_CONF_TEMP_PATH, $fileOutput);
    }

    /**
     * Generates a random string.
     *
     * @param integer $p_len
     *            length of the output string
     * @param string $p_chars
     *            characters to use in the output string
     * @return string the generated random string
     */
    protected function generateRandomString($p_len = 20, $p_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $string = '';
        for($i = 0; $i < $p_len; $i++) {
            $pos = mt_rand(0, strlen($p_chars) - 1);
            $string .= $p_chars{$pos};
        }
        return $string;
    }

}

/**
 * Class AirtimeDatabaseException
 * 
 * @author sourcefabric
 *
 * Exception class for database setup errors
 */
class AirtimeDatabaseException extends Exception {
    
    protected $message = "Unknown Airtime database exception";
    protected $errors = array();

    public function __construct($message = null, $errors = array(), $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrorFields() {
        return $this->errors;
    }

}

// Import Setup subclasses
require_once ('database-setup.php');
require_once ('rabbitmq-setup.php');
require_once ('general-setup.php');
require_once ('media-setup.php');

// If airtime.conf exists, we shouldn't be here
if (!file_exists("/etc/airtime/airtime.conf")) {
    if (isset($_GET["obj"]) && $objType = $_GET["obj"]) {
        $obj = new $objType($_POST);
        if ($obj instanceof Setup) {
            try {
                $response = $obj->runSetup();
            } catch (AirtimeDatabaseException $e) {
                $response = array(
                        "message" => $e->getMessage(),
                        "errors" => $e->getErrorFields() 
                );
            }
            
            echo json_encode($response);
        }
    }
}
