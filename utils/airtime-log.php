<?php

set_include_path(__DIR__.'/../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();

$log_files = array("media-monitor" => "/var/log/airtime/media-monitor/media-monitor.log",
                    "recorder" => "/var/log/airtime/show-recorder/show-recorder.log",
                    "playout" => "/var/log/airtime/pypo/pypo.log",
                    "web" => "/var/log/airtime/zendphp.log");

function printUsage($userMsg = "")
{
    global $opts;
    
    $msg = $opts->getUsageMessage();
    if (strlen($userMsg)>0)
        echo $userMsg;
    echo PHP_EOL."Usage: airtime-log [options]";
    echo substr($msg, strpos($msg, "\n")).PHP_EOL;
}

function isKeyValid($key){
    global $log_files;
    return array_key_exists($key, $log_files);
}

function viewSpecificLog($key){
    global $log_files;

    if (isKeyValid($key)){
        echo "Viewing $key log\n";
        pcntl_exec(exec("which less"), array($log_files[$key]));
        pcntl_wait($status);
    } else printUsage();
}

function dumpAllLogs(){
    $dateStr = gmdate("Y-m-d-H-i-s");
    $filename = __DIR__."/airtime-log-all-$dateStr.tgz";
    echo "Creating Airtime logs tgz file at $filename";
    $command = "tar cfz $filename /var/log/airtime 2>/dev/null";
    exec($command);
}

function dumpSpecificLog($key){
    global $log_files;

    if (isKeyValid($key)){
        $dateStr = gmdate("Y-m-d-H-i-s");
        $filename = __DIR__."/airtime-log-$key-$dateStr.tgz";
        echo "Creating Airtime logs tgz file at $filename";
        $dir = dirname($log_files[$key]);
        $command = "tar cfz $filename $dir 2>/dev/null";
        exec($command);
    } else printUsage();
}

function tailAllLogs(){
    global $log_files;
    echo "Tail all Airtime logs";
    pcntl_exec(exec("which multitail"), $log_files);
    pcntl_wait($status);
}

function tailSpecificLog($key){
    global $log_files;

    if (isKeyValid($key)){
        echo "Tail $key log";
        pcntl_exec(exec("which tail"), array("-F", $log_files[$key]));
        pcntl_wait($status);        
    } else printUsage();
}

try {
    $opts = new Zend_Console_Getopt(
        array(
            'view|v=s' => "Display log file\n"
                            ."\t\tmedia-monitor|playout|recorder|web (ALL by default)",
            'dump|d-s' => "Collect all log files and compress into a tarball\n"
                            ."\t\tmedia-monitor|playout|recorder|web (ALL by default)",
            'tail|t-s' => "View any new entries appended to log files in real-time\n"
                            ."\t\tmedia-monitor|playout|recorder|web (ALL by default)"
        )
    );
    $opts->parse();
}
catch (Zend_Console_Getopt_Exception $e) {
    print $e->getMessage() .PHP_EOL;
    printUsage();
    exit(1);
}

if (isset($opts->v)){
    if ($opts->v === true){
        //Should never get here. Zend_Console_Getopt requires v to provide a string parameter.
    } else {
        viewSpecificLog($opts->v);
    }
} else if (isset($opts->d)){
    if ($opts->d === true){
        dumpAllLogs();
    } else {
        dumpSpecificLog($opts->d);
    }
} else if (isset($opts->t)){
    if ($opts->t === true){
        tailAllLogs();
    } else {
        tailSpecificLog($opts->t);
    }
}

echo PHP_EOL;

