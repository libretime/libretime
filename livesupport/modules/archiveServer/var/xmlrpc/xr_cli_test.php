<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/archiveServer/var/xmlrpc/xr_cli_test.php,v $

------------------------------------------------------------------------------*/

include_once "../../../storageServer/var/xmlrpc/XML/RPC.php";

$pars = $argv;
array_shift($pars);
$verbose = FALSE;
if($pars[0] == '-v'){ $verbose = TRUE; array_shift($pars); }
if($pars[0] == '-s'){
    array_shift($pars);
    $serverPath = array_shift($pars);
}else $serverPath = 'http://localhost:80/livesupportArchiveServer/xmlrpc/xrLocStor.php';

#$serverPath = "http://localhost:80/livesupportArchiveServerCVS/xmlrpc/xrLocStor.php";

$url = parse_url($serverPath);
$client = new XML_RPC_Client($url['path'], $url['host']);
$method = array_shift($pars);

if($verbose){
    echo "serverPath: $serverPath\n";
    echo "host: {$url['host']}, path: {$url['path']}\n";
    echo "method: $method\n";
    echo "pars:\n";
    var_dump($pars);
}

$infos = array(
    "listMethods"       => array('m'=>"system.listMethods", 'p'=>NULL),
    "methodHelp"        => array('m'=>"system.methodHelp", 'p'=>0),
    "methodSignature"   => array('m'=>"system.methodSignature", 'p'=>0),
    "test"              =>
        array('m'=>"archive.test", 'p'=>array('sessid', 'teststring')),
    "authenticate"      => array('m'=>"archive.authenticate",
        'p'=>array('login', 'pass'), 'r'=>'authenticate'),
    "login"             => array('m'=>"archive.login",
        'p'=>array('login', 'pass'), 'r'=>'sessid'),
    "logout"            => array('m'=>"archive.logout",
        'p'=>array('sessid'), 'r'=>'status'),

    "storeAudioClipOpen"        => array('m'=>"archive.storeAudioClipOpen",
        'p'=>array('sessid', 'gunid', 'metadata', 'fname', 'chsum'),
        'r'=>array('url', 'token')
    ),
    "storeAudioClipClose"       => array('m'=>"archive.storeAudioClipClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "accessRawAudioData"        => array('m'=>"archive.accessRawAudioData",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "releaseRawAudioData"       => array('m'=>"archive.releaseRawAudioData",
        'p'=>array('sessid', 'token'), 'r'=>'status'),
    "downloadRawAudioDataOpen"  =>
        array('m'=>"archive.downloadRawAudioDataOpen",
            'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadRawAudioDataClose" =>
        array('m'=>"archive.downloadRawAudioDataClose",
            'p'=>array('sessid', 'token'), 'r'=>'gunid'),
    "downloadMetadataOpen"      => array('m'=>"archive.downloadMetadataOpen",
        'p'=>array('sessid', 'gunid'), 'r'=>array('url', 'token')),
    "downloadMetadataClose"     => array('m'=>"archive.downloadMetadataClose",
        'p'=>array('sessid', 'token'), 'r'=>'gunid'),

    "deleteAudioClip"   =>
        array('m'=>"archive.deleteAudioClip",
            'p'=>array('sessid', 'gunid'), 'r'=>'status'),
    "existsAudioClip"   => array('m'=>"archive.existsAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'exists'),
    "getAudioClip"  => array('m'=>"archive.getAudioClip",
        'p'=>array('sessid', 'gunid'), 'r'=>'metadata'),
    "updateAudioClipMetadata"   => array('m'=>"archive.updateAudioClipMetadata",
        'p'=>array('sessid', 'gunid', 'metadata'), 'r'=>'status'),
    "searchMetadata"    => array('m'=>"archive.searchMetadata", 'p'=>NULL),
    "resetStorage"  => array('m'=>"archive.resetStorage", 'p'=>array()),

    "createPlaylist"    => array('m'=>"archive.createPlaylist",
        'p'=>array('sessid', 'plid', 'fname'), 'r'=>'plid'),
    "editPlaylist"      => array('m'=>"archive.editPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "savePlaylist"      => array('m'=>"archive.savePlaylist",
        'p'=>array('sessid', 'token', 'newPlaylist'), 'r'=>'plid'),
    "deletePlaylist"    => array('m'=>"archive.deletePlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>'status'),
    "accessPlaylist"    => array('m'=>"archive.accessPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>array('url', 'token')),
    "releasePlaylist"   => array('m'=>"archive.releasePlaylist",
        'p'=>array('sessid', 'token'), 'r'=>'plid'),
    "existsPlaylist"    => array('m'=>"archive.existsPlaylist",
        'p'=>array('sessid', 'plid'), 'r'=>'exists'),
    "playlistIsAvailable"   => array('m'=>"archive.playlistIsAvailable",
        'p'=>array('sessid', 'plid'), 'r'=>'available'),

    "loadPref"      => array('m'=>"archive.loadPref",
        'p'=>array('sessid', 'key'), 'r'=>'value'),
    "savePref"      => array('m'=>"archive.savePref",
        'p'=>array('sessid', 'key', 'value'), 'r'=>'status'),
    "delPref"       => array('m'=>"archive.delPref",
        'p'=>array('sessid', 'key'), 'r'=>'status'),
    "openPut"       => array('m'=>"archive.openPut", 'p'=>array()),
    "closePut"      => array('m'=>"archive.closePut", 'p'=>array()),
);


$fullmethod = $infos[$method]['m'];
$pinfo = $infos[$method]['p'];
if(is_null($pinfo)){
    $parr = NULL;
}elseif(!is_array($pinfo)){
    $parr = $pars[0];
    #echo "pinfo not null and not array.\n"; exit;
}elseif(count($pinfo) == 0){
    $parr = array();
}else{
    $parr = array(); $i=0;
    foreach($pinfo as $it){
        $parr[$it] = $pars[$i++];
    }
}
if($method == 'searchMetadata'){
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
}
$msg = new XML_RPC_Message($fullmethod, array(XML_RPC_encode($parr)));

if($verbose){
    echo "parr:\n";
    var_dump($parr);
    echo "message:\n";
    echo $msg->serialize()."\n";
}

$res = $client->send($msg);
if($res->faultCode() > 0) {
    echo "xr_cli_test.php: ".$res->faultString()." ".$res->faultCode()."\n";
    exit(1);
}

if($verbose){
    echo "result:\n";
    echo $res->serialize();
}

$resp = XML_RPC_decode($res->value());
if(isset($infos[$method]['r'])){
    $pom = $infos[$method]['r'];
    if(is_array($pom)){
        foreach($pom as $k=>$it) $pom[$k] = $resp[$it];
        echo join(' ', $pom)."\n";
    }else switch($pom){
        case"status":
        case"exists":
            echo ($resp[$pom]=='1' ? "TRUE" : "FALSE" )."\n";
            break;
        default:
            echo "{$resp[$pom]}\n";
    }
}else{
    print_r($resp);
#    echo"\n";
}

?>
