#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/../conf.php');
require_once(dirname(__FILE__).'/../../../alib/var/Subjects.php');
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
    echo "campcaster-user\n";
    echo "===============\n";
    echo "    This program allows you to manage Campcaster users.\n";
    echo "\n";
    echo "OPTIONS:\n";
    echo "    --addupdate <username> <password>\n";
    echo "        Add the user or update the password for the user.\n";
    echo "    --delete <username>\n";
    echo "        Remove the user.\n";
    echo "\n";
}

$parsedCommandLine = Console_Getopt::getopt($argv, null, array("addupdate", "delete"));

if (PEAR::isError($parsedCommandLine)) {
    printUsage();
    exit(1);
}
$cmdLineOptions = $parsedCommandLine[0];
if (count($parsedCommandLine[1]) == 0) {
    printUsage();
    exit;
}

$action = null;
foreach ($cmdLineOptions as $tmpValue) {
    $optionName = $tmpValue[0];
    $optionValue = $tmpValue[1];
    switch ($optionName) {
        case '--addupdate':
            $action = "addupdate";
            break 2;
        case "--delete":
            $action = "delete";
            break 2;
    }
}

if (is_null($action)) {
    printUsage();
    exit;
}

if (count($parsedCommandLine) < 1) {
    printUsage();
    exit;
}

$username = $parsedCommandLine[1][0];
$password = $parsedCommandLine[1][1];

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
if (PEAR::isError($CC_DBC)) {
    die($CC_DBC->getMessage());
}
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

// Check if the user exists
$user = Subjects::GetSubject($username);

if ($action == "addupdate") {
    if (empty($password)) {
        printUsage();
        exit;
    }
    if (empty($user)) {
        // Add the user.
        $r = Subjects::AddSubj($username, $password);
    } else {
        // Update the password
        $r = Subjects::Passwd($username, NULL, $password);
    }
} elseif (($action == "delete") && (is_array($user))) {
    // Delete the user
    $r = Subjects::RemoveSubj($username);
}

if (PEAR::isError($r)) {
    die($r->getMessage());
}
exit(0);
?>