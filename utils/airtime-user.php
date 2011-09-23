<?php

exitIfNotRoot();

$airtimeIni = GetAirtimeConf();
$airtime_base_dir = $airtimeIni['general']['airtime_dir'];

set_include_path("$airtime_base_dir/application/models" . PATH_SEPARATOR . get_include_path());
require_once("$airtime_base_dir/library/propel/runtime/lib/Propel.php");
Propel::init("$airtime_base_dir/application/configs/airtime-conf.php");

require_once("$airtime_base_dir/application/configs/conf.php");
require_once("$airtime_base_dir/application/models/User.php");
require_once('DB.php');
require_once('Console/Getopt.php');

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

function printUsage()
{
    echo "\n";
    echo "airtime-user\n";
    echo "===============\n";
    echo "    This program allows you to manage Airtime users.\n";
    echo "\n";
    echo "OPTIONS:\n";
    echo "    --addupdate <username>\n";
    echo "        Add the user or update user information.\n";
    echo "    --delete <username>\n";
    echo "        Remove the user.\n";
    echo "\n";
}

/**
 * Ensures that the user is running this PHP script with root
 * permissions. If not running with root permissions, causes the
 * script to exit.
 */
function exitIfNotRoot()
{
    // Need to check that we are superuser before running this.
    if(exec("whoami") != "root"){
        echo "Must be root user.\n";
        exit(1);
    }
}

if (count($argv) != 3) {
    printUsage();
    exit;
}


$action = null;
switch ($argv[1]) {
	case '--addupdate':
		$action = "addupdate";
		break;
	case '--delete':
		$action = "delete";
		break;
}

$username = $argv[2];

if (is_null($action)) {
    printUsage();
    exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    die($CC_DBC->getMessage());
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);


// Check if the user exists
$id = Application_Model_User::GetUserID($username);

if ($action == "addupdate") {

	if ($id < 0) {
        echo "Creating user\n";
		$user = new Application_Model_User("");
		$user->setLogin($username);
    } else {
		echo "Updating user\n";
		$user = new Application_Model_User($id);
	}

	do{
		echo "Enter password (min 6 characters): ";
		$line = trim(fgets(fopen("php://stdin","r")));
	}while(strlen($line) < 6);
	$user->setPassword($line);
	
	do{
		echo "Enter first name: ";
		$line = trim(fgets(fopen("php://stdin","r")));
	}while(strlen($line) < 1);
	$user->setFirstName($line);
	
	do{
		echo "Enter last name: ";
		$line = trim(fgets(fopen("php://stdin","r")));
	}while(strlen($line) < 1);
	$user->setLastName($line);
	
	do{
		echo "Enter user type [(A)dmin|(P)rogram Manager|(D)J|(G)uest]: ";
		$line = trim(fgets(fopen("php://stdin","r")));
	} while($line != "A" && $line != "P" && $line != "D" && $line != "G");
    
    $types = array("A"=>"A", "P"=>"P", "D"=>"H", "G"=>"G",);
	$user->setType($types[$line]);
	$user->save();
	
} elseif ($action == "delete") {
    if ($id < 0){  
		echo "Username not found!\n";
		exit;
	} else {
		echo "Deleting user\n";
		$user = new Application_Model_User($id);
		$user->delete();
	}
}

function GetAirtimeConf()
{
    $ini = parse_ini_file("/etc/airtime/airtime.conf", true);

    if ($ini === false){
        echo "Error reading /etc/airtime/airtime.conf.".PHP_EOL;
        exit;
    }

    return $ini;
}
