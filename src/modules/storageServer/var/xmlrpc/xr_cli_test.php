<?php
require_once(dirname(__FILE__).'/../conf.php');
include_once("XML/RPC.php");
include_once("Console/Getopt.php");

function printUsage()
{
    echo "Usage:\n";
    echo "    -v                            Verbose output\n";
    echo "    -s arg                        Server Path\n";
    echo "    -o arg1:value1,arg2:value2    Function Arguments\n";
    echo "    -h                            Help\n";
    echo "\n";
}

$verbose = TRUE;
$parsedCommandLine = Console_Getopt::getopt($argv, "vs:o:h");

$cmdLineOptions = $parsedCommandLine[0];

if (count($parsedCommandLine[1]) == 0) {
    printUsage();
    exit;
}

$method = array_pop($parsedCommandLine[1]);

foreach ($cmdLineOptions as $tmpValue) {
    $optionName = $tmpValue[0];
    $optionValue = $tmpValue[1];
    switch ($optionName) {
        case "h":
            printUsage();
            exit;
        case "v":
            $verbose = TRUE;
            break;
        case "s":
            $serverPath = $optionValue;
            break;
        case "o":
            $optStr = $optionValue;
            $optArr = split(",", $optStr);
            foreach ($optArr as $opt) {
                list($k, $v) = split(':', $opt);
                $options[$k] = $v;
            }

    }
}

if (!isset($serverPath)) {
    $serverPath =
      "http://{$CC_CONFIG['storageUrlHost']}:{$CC_CONFIG['storageUrlPort']}".
      "{$CC_CONFIG['storageUrlPath']}/{$CC_CONFIG['storageXMLRPC']}";
}

$url = parse_url($serverPath);
$client = new XML_RPC_Client($url['path'], $url['host']);

if ($verbose) {
    $client->debug = 1;
    echo "ServerPath: $serverPath\n";
    echo "Host: {$url['host']}, path: {$url['path']}\n";
    echo "Method: $method\n";
    echo "Parameters:\n";
    var_dump($pars);
}

$infos = array(
    "listMethods" => array('m'=>"system.listMethods", 'p'=>NULL),
    "methodHelp" => array('m'=>"system.methodHelp", 'p'=>0),
    "methodSignature" => array('m'=>"system.methodSignature", 'p'=>0),
    "test" => array('m'=>"locstor.test", 'p'=>array('sessid', 'teststring')),
    "getVersion" => array('m'=>"locstor.getVersion", 'p'=>array(), 'r'=>'version'),
    "authenticate" => array('m'=>"locstor.authenticate", 'p'=>array('login', 'pass'), 'r'=>'authenticate'),
    "login" => array('m'=>"locstor.login", 'p'=>array('login', 'pass'), 'r'=>'sessid'),
    "logout" => array('m'=>"locstor.logout", 'p'=>array('sessid'), 'r'=>'status'),
    "storeAudioClipOpen" => array('m'=>"locstor.storeAudioClipOpen",
        'p'=>array('sessid', 'gunid', 'metadata', 'fname', 'chsum'),
        'r'=>array('url', 'token')
    ),
    "storeAudioClipClose" => array('m'=>"locstor.storeAudioClipClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "accessRawAudioData" => array('m'=>"locstor.accessRawAudioData",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "releaseRawAudioData" => array('m'=>"locstor.releaseRawAudioData",
        'p'=>array('token'), 'r'=>'status'),
    "downloadRawAudioDataOpen" =>
        array('m'=>"locstor.downloadRawAudioDataOpen",
            'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadRawAudioDataClose" =>
        array('m'=>"locstor.downloadRawAudioDataClose",
            'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "downloadMetadataOpen" => array('m'=>"locstor.downloadMetadataOpen",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadMetadataClose" => array('m'=>"locstor.downloadMetadataClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),

    "deleteAudioClip" =>
        array('m'=>"locstor.deleteAudioClip",
            'p'=>array('sessid', 'gunid', 'forced'), 'r'=>'status'),
    "existsAudioClip" => array('m'=>"locstor.existsAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'exists'),
    "getAudioClip" => array('m'=>"locstor.getAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'metadata'),
    "updateAudioClipMetadata" => array('m'=>"locstor.updateAudioClipMetadata",
        'p'=>array('sessid', 'gunid', 'metadata'), 'r'=>'status'),
    "searchMetadata" => array('m'=>"locstor.searchMetadata", 'p'=>NULL),
    "browseCategory" => array('m'=>"locstor.browseCategory", 'p'=>NULL),
    "resetStorage" => array('m'=>"locstor.resetStorage",
        'p'=>array()),
#        'p'=>array('loadSampleData', 'invalidateSessionIds')),
    "storeWebstream" => array('m'=>"locstor.storeWebstream",
        'p'=>array('sessid', 'gunid', 'metadata', 'fname', 'url'),
        'r'=>array('gunid')
    ),

    "createPlaylist" => array('m'=>"locstor.createPlaylist",
        'p'=>array('sessid', 'plid', 'fname'), 'r'=>'plid'),
    "editPlaylist" => array('m'=>"locstor.editPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "savePlaylist" => array('m'=>"locstor.savePlaylist",
        'p'=>array('sessid', 'token', 'newPlaylist'), 'r'=>'plid'),
    "revertEditedPlaylist" => array('m'=>"locstor.revertEditedPlaylist",
        'p'=>array('sessid', 'token'), 'r'=>'plid'),
    "deletePlaylist" => array('m'=>"locstor.deletePlaylist",
        'p'=>array('sessid', 'plid', 'forced'), 'r'=>'status'),
    "accessPlaylist" => array('m'=>"locstor.accessPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "releasePlaylist" => array('m'=>"locstor.releasePlaylist",
        'p'=>array('token'), 'r'=>'plid'),
    "existsPlaylist" => array('m'=>"locstor.existsPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>'exists'),
    "playlistIsAvailable" => array('m'=>"locstor.playlistIsAvailable",
        'p'=>array('sessid', 'plid'), 'r'=>array('available', 'ownerid', 'ownerlogin')),

    "exportPlaylistOpen" => array('m'=>"locstor.exportPlaylistOpen",
        'p'=>array('sessid', 'plids', 'type', 'standalone'),
        'r'=>array('url', 'token')),
    "exportPlaylistClose" => array('m'=>"locstor.exportPlaylistClose",
        'p'=>array('token'), 'r'=>array('status')),
    "importPlaylistOpen" => array('m'=>"locstor.importPlaylistOpen",
        'p'=>array('sessid', 'chsum'), 'r'=>array('url', 'token')),
    "importPlaylistClose" => array('m'=>"locstor.importPlaylistClose",
        'p'=>array('token'), 'r'=>array('gunid')),

    "renderPlaylistToFileOpen" => array('m'=>"locstor.renderPlaylistToFileOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToFileCheck" => array('m'=>"locstor.renderPlaylistToFileCheck",
        'p'=>array('token'), 'r'=>array('status', 'url')),
    "renderPlaylistToFileClose"   => array('m'=>"locstor.renderPlaylistToFileClose",
        'p'=>array('token'), 'r'=>array('status')),
    "renderPlaylistToStorageOpen"   => array('m'=>"locstor.renderPlaylistToStorageOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToStorageCheck" => array('m'=>"locstor.renderPlaylistToStorageCheck",
        'p'=>array('token'), 'r'=>array('status', 'gunid')),
    "renderPlaylistToRSSOpen"   => array('m'=>"locstor.renderPlaylistToRSSOpen",
        'p'=>array('sessid', 'plid'),
        'r'=>array('token')),
    "renderPlaylistToRSSCheck" => array('m'=>"locstor.renderPlaylistToRSSCheck",
        'p'=>array('token'), 'r'=>array('status', 'url')),
    "renderPlaylistToRSSClose" => array('m'=>"locstor.renderPlaylistToRSSClose",
        'p'=>array('token'), 'r'=>array('status')),

    "loadPref" => array('m'=>"locstor.loadPref",
        'p'=>array('sessid', 'key'), 'r'=>'value'),
    "savePref" => array('m'=>"locstor.savePref",
        'p'=>array('sessid', 'key', 'value'), 'r'=>'status'),
    "delPref" => array('m'=>"locstor.delPref",
        'p'=>array('sessid', 'key'), 'r'=>'status'),
    "loadGroupPref" => array('m'=>"locstor.loadGroupPref",
        'p'=>array('sessid', 'group', 'key'), 'r'=>'value'),
    "saveGroupPref" => array('m'=>"locstor.saveGroupPref",
        'p'=>array('sessid', 'group', 'key', 'value'), 'r'=>'status'),

    "getTransportInfo" => array('m'=>"locstor.getTransportInfo",
        'p'=>array('trtok'),
        'r'=>array('state', 'realsize', 'expectedsize', 'realsum', 'expectedsum')),
    "turnOnOffTransports" => array('m'=>"locstor.turnOnOffTransports",
        'p'=>array('sessid', 'onOff'), 'r'=>array('state')),
    "doTransportAction" => array('m'=>"locstor.doTransportAction",
        'p'=>array('sessid', 'trtok', 'action'), 'r'=>array('state')),
    "uploadFile2Hub" => array('m'=>"locstor.uploadFile2Hub",
        'p'=>array('sessid', 'filePath'), 'r'=>array('trtok')),
    "getHubInitiatedTransfers" => array('m'=>"locstor.getHubInitiatedTransfers",
        'p'=>array('sessid'), 'r'=>array()),
    "startHubInitiatedTransfer" => array('m'=>"locstor.startHubInitiatedTransfer",
        'p'=>array('trtok'), 'r'=>array()),
    "upload2Hub" => array('m'=>"locstor.upload2Hub",
        'p'=>array('sessid', 'gunid'), 'r'=>array('trtok')),
    "downloadFromHub" => array('m'=>"locstor.downloadFromHub",
        'p'=>array('sessid', 'gunid'), 'r'=>array('trtok')),
    "globalSearch" => array('m'=>"locstor.globalSearch",
        'p'=>array('sessid', 'criteria'), 'r'=>array('trtok')),
    "getSearchResults" => array('m'=>"locstor.getSearchResults",
        'p'=>array('trtok')),

    "createBackupOpen" => array('m'=>"locstor.createBackupOpen",
        'p'=>array('sessid', 'criteria'), 'r'=>array('token')),
    "createBackupCheck" => array('m'=>"locstor.createBackupCheck",
#        'p'=>array('token'), 'r'=>array('status', 'url', 'metafile', 'faultString')),
        'p'=>array('token'), 'r'=>array('status', 'url', 'tmpfile')),
    "createBackupClose" => array('m'=>"locstor.createBackupClose",
        'p'=>array('token'), 'r'=>array('status')),
    "restoreBackupOpen" => array('m'=>"locstor.restoreBackupOpen",
        'p'=>array('sessid', 'chsum'), 'r'=>array('url', 'token')),
    "restoreBackupClosePut" => array('m'=>"locstor.restoreBackupClosePut",
        'p'=>array('sessid', 'token'), 'r'=>array('token')),
    "restoreBackupCheck" => array('m'=>"locstor.restoreBackupCheck",
        'p'=>array('token'), 'r'=>array('status', 'faultString')),
    "restoreBackupClose" => array('m'=>"locstor.restoreBackupClose",
        'p'=>array('token'), 'r'=>array('status')),

/*
    "uploadToArchive"       => array('m'=>"locstor.uploadToArchive",
        'p'=>array('sessid', 'gunid'), 'r'=>'trtok'),
    "downloadFromArchive"       => array('m'=>"locstor.downloadFromArchive",
        'p'=>array('sessid', 'gunid'), 'r'=>'trtok'),
*/

    "openPut"       => array('m'=>"locstor.openPut", 'p'=>array()),
    "closePut"      => array('m'=>"locstor.closePut", 'p'=>array()),
);


switch ($method) {
    case "searchMetadata":
    case "globalSearch":
    case "createBackupOpen":
        $parr = array(
            'sessid'=>$pars[0],
            'criteria'=>array(
                'filetype'=>'audioclip',
                'operator'=>'and',
                'limit'=> 0,
                'offset'=> 0,
                'conditions'=>array(
                    array('cat'=>$pars[1], 'op'=>'partial', 'val'=>$pars[2])
                )
            ),
        );
        break;
    case "browseCategory":
        $parr = array(
            'sessid'=>$pars[0],
            'category'=>$pars[1],
            'criteria'=>array(
                'filetype'=>'audioclip',
                'operator'=>'and',
                'limit'=> 0,
                'offset'=> 0,
                'conditions'=>array(
                    array('cat'=>$pars[2], 'op'=>'partial', 'val'=>$pars[3])
                )
            ),
        );
        break;
    case "resetStorage":
        $parr = array(
            'loadSampleData'=>(boolean)$pars[0],
            'invalidateSessionIds'=>(boolean)$pars[1],
        );
        break;
    default:
        $pinfo = $infos[$method]['p'];
        if (is_null($pinfo)) {
            $parr = NULL;
        } elseif(!is_array($pinfo)) {
            $parr = $pars[0];
            #echo "pinfo not null and not array.\n"; exit;
        } elseif(count($pinfo) == 0) {
            $parr = (object)array();
        } else {
            $parr = array(); $i=0;
            foreach($pinfo as $it){
                if(isset($pars[$i])) $parr[$it] = $pars[$i];
                $i++;
            }
        }
} // switch

$fullmethod = $infos[$method]['m'];
$msg = new XML_RPC_Message($fullmethod, array(XML_RPC_encode($parr)));

if ($verbose) {
    echo "parr:\n";
    var_dump($parr);
    echo "message:\n";
    echo $msg->serialize()."\n";
}

#$client->setDebug(1);
$res = $client->send($msg);
if ($res->faultCode() > 0) {
    echo "xr_cli_test.php: ".$res->faultString()." ".$res->faultCode()."\n";
#    echo var_export($res);
    exit(1);
}

if ($verbose) {
    echo "result:\n";
    echo $res->serialize();
}

$resp = XML_RPC_decode($res->value());
if (isset($infos[$method]['r'])) {
    $pom = $infos[$method]['r'];
    if (is_array($pom)) {
        foreach ($pom as $k => $it) {
            $pom[$k] = $resp[$it];
        }
        echo join(' ', $pom)."\n";
    } else {
        switch ($pom) {
            case "status":
            case "exists":
                echo ($resp[$pom]=='1' ? "TRUE" : "FALSE" )."\n";
                break;
            default:
                echo "{$resp[$pom]}\n";
        }
    }
} else {
    switch ($method) {
        case "searchMetadata":
        case "getSearchResults":
            $acCnt = 0;
            $acGunids = array();
            $plCnt = 0;
            $plGunids = array();
            $fld = (isset($options['category']) ? $options['category'] : 'gunid' );
            foreach ($resp['results'] as $k => $v) {
                if ($v['type']=='audioclip') {
                    $acCnt++;
                    $acGunids[] = $v[$fld];
                }
                if ($v['type']=='playlist') {
                    $plCnt++;
                    $plGunids[] = $v[$fld];
                }
            }
            echo "AC({$acCnt}): ".
                    join(", ", $acGunids).
                " | PL({$plCnt}): ".
                    join(", ", $plGunids).
                "\n";
            break;
        case "browseCategory":
            echo "RES({$resp['cnt']}): ".
                    join(", ", $resp['results']).
                "\n";
            break;
        default:
            print_r($resp);
    }
}

?>