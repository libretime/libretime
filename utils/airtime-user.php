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

$shortopts = "u:p:t:f:l:";

$longopts  = array(
    "username:",
    "password:",
    "type:",
    "first-name:",
    "last-name:",
    "addupdate",
    "delete",
);
$options = getopt($shortopts, $longopts);

$action = null;
if (isset($options["addupdate"])) {
    $action = "addupdate";
}
else if (isset($options["delete"])) {
    $action = "delete";
}
else {
    printUsage();
    exit;
}

if (isset($options["u"]) || isset($options["username"])) {
    $username = $options["u"] ?: $options["username"];
}
else {
    printUsage();
    exit;
}

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
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
    }
    else {
		echo "Updating user\n";
		$user = new Application_Model_User($id);
	}

    //setting password
    if (isset($options["p"]) || isset($options["password"])) {

        $password = $options["p"] ?: $options["password"];
        $user->setPassword($password);
    }

    //setting first-name
    if (isset($options["f"]) || isset($options["first-name"])) {

        $firstname = $options["f"] ?: $options["first-name"];
        $user->setFirstName($firstname);
    }

    //setting last-name
    if (isset($options["l"]) || isset($options["last-name"])) {

        $lastname = $options["l"] ?: $options["last-name"];
        $user->setLastName($lastname);
    }

    $types = array("A"=>"A", "P"=>"P", "D"=>"H", "G"=>"G",);
    //setting type
    if (isset($options["t"]) || isset($options["type"])) {

        $type = $options["t"] ?: $options["type"];
        if (in_array($type, $types)) {
            $user->setType($type);
        }
    }

	$user->save();
}
else if ($action == "delete") {

    if ($id < 0){
		echo "Username not found!\n";
		exit;
	}
    else {
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

function printUsage()
{
    echo "\n";
    echo "airtime-user\n";
    echo "===============\n";
    echo "    This program allows you to manage Airtime users.\n";
    echo "\n";
    echo "OPTIONS:\n";
    echo "    airtime-user --addupdate -u|--username <USERNAME> [-p|--password <PASSWORD>] [-t|--type <A|P|D|G>] [-f|--first-name 'Name'] [-l|--last-name 'Name'] \n";
    echo "        Add the user or update user information.\n";
    echo "\n";
    echo "    airtime-user --delete -u|--username <USERNAME>\n";
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
