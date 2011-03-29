<?php

// Do not allow remote execution
$arr = array_diff_assoc($_SERVER, $_ENV);
if (isset($arr["DOCUMENT_ROOT"]) && ($arr["DOCUMENT_ROOT"] != "") ) {
    header("HTTP/1.1 400");
    header("Content-type: text/plain; charset=UTF-8");
    echo "400 Not executable\r\n";
    exit(1);
}

if (!function_exists('pg_connect')) {
    trigger_error("PostgreSQL PHP extension required and not found.", E_USER_ERROR);
    exit(2);
}

function CreateINIFile(){
    if (!file_exists("/etc/airtime/")){
        if (!mkdir("/etc/airtime/", 0755, true)){
            echo "Could not create /etc/airtime/ directory. Exiting.";
            exit(1);
        }
    }
    
    if (!copy(__DIR__."/../../build/airtime.conf", "/etc/airtime/airtime.conf")){
        echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy(__DIR__."/../../python_apps/pypo/pypo.cfg", "/etc/airtime/pypo.cfg")){
        echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
    if (!copy(__DIR__."/../../python_apps/show-recorder/recorder.cfg", "/etc/airtime/recorder.cfg")){
        echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
        exit(1);
    }
}

function RemoveINIFile(){
    if (file_exists("/etc/airtime/airtime.conf")){
        unlink("/etc/airtime/airtime.conf");
    }

    if (file_exists("etc/airtime")){
        rmdir("/etc/airtime/");
    }
}

function ExitIfNotRoot()
{
    // Need to check that we are superuser before running this.
    if(exec("whoami") != "root"){
        echo "Must be root user.\n";
        exit(1);
    }
}
