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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageAdmin/var/import.php,v $

------------------------------------------------------------------------------*/
header("Content-type: text/plain");
echo "\n#StorageServer import script:\n";
//echo date('H:i:s')."\n";
$start=intval(date('U'));

require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
require_once 'DB.php';
require_once "$storageServerPath/var/GreenBox.php";

#PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){ echo "ERROR: ".$dbc->getMessage()." ".$dbc->getUserInfo()."\n"; exit(1); }
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox($dbc, $config);

$testonly = ($argv[1] == '-n');

$errors=0;
$filecount=0;
function _err($r, $fn){
    global $errors;
    echo "ERROR\n ".$r->getMessage()." ".$r->getUserInfo()."\n";
    $errors++;
}

$flds = array(
//    'fileformat'    => NULL,
    'mime_type'     => 'dc:format',
    'bitrate'       => 'ls:bitrate',
    'playtime_seconds'  => 'dcterms:extent',
    'tags'  => array(
        'TT2' => 'dc:title',
        'TIT2' => 'dc:title',
        'TP1' => 'dc:creator',
        'TPE1' => 'dc:creator',
        'TAL' => 'dc:source',
        'TALB' => 'dc:source',
//        'TCO' => NULL,
        'TEN' => 'ls:encoded_by',
        'TENC' => 'ls:encoded_by',
        'TRK' => 'ls:track_num',
        'TRCK' => 'ls:track_num',
    ),
    'audio' => array(
        'channels'   => 'ls:channels',
//        'bitrate'    => 'ls:bitrate',
    ),
    'comments' => array(
        'genre'     => 'dc:type',
        'title'     => 'dc:title',
        'artist'    => 'dc:creator',
        'album'     => 'dc:source',
        'tracknumber'=> 'ls:track_num',
        'date'      => 'ls:year',
        'label'      => 'dc:publisher',
//        'genreid'  => 'GENREID',
    ),
    'filename'  => 'ls:filename',
);

$r = $gb->getObjId('import', $gb->storId);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
if(is_null($r)){
    $r = $gb->bsCreateFolder($gb->storId, 'import');
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
}
$parid = $r;

$stdin = fopen('php://stdin', 'r');
while($filename = fgets($stdin, 2048)){
    $filename = rtrim($filename);
    echo "$filename:   ";
    set_time_limit(30);
    $ia = GetAllFileInfo("$filename", 'mp3');
    if(PEAR::isError($ia)){ _err($ia, $filename); continue; }
    if(!$ia['fileformat']){ echo "???\n"; continue; }
    if(!$ia['bitrate']){ echo "not audio?\n"; continue; }
    
    $mdata = array();
    foreach($flds as $k1=>$fn1){
        if(is_null($fn1)) continue;
        list($fn, $v)  = array($fn1, $ia[$k1]);
        if(is_array($fn1)){
            $k0 = $k1;
            if($k0=='tags') $k1=$ia['tags'][0];
            list($fn, $v)  = array($fn1, $ia[$k1]);
            foreach($fn1 as $k2=>$fn2){
                if(is_null($fn2)) continue;
                if(!isset($ia[$k1][$k2])) continue;
                switch($k0){
                case"tags":
                    list($fn, $v)  = array($fn2, $ia[$k1][$k2]['data']);
                    $enc = $ia[$k1][$k2]['encoding'];
                    if($enc != 'UTF-8' && $enc != 'ISO-8859-1'){
                        echo " Warning: wrong encoding '$enc' in $fn2.\n";
                    }
                    break;
                case"comments":
                    list($fn, $v)  = array($fn2, $ia[$k1][$k2][0]);
                    break;
                default;
                    list($fn, $v)  = array($fn2, $ia[$k1][$k2]);
                }
#                if(is_array($fn)) var_dump($fn);
                if(!is_null($v)) $mdata[$fn] = addslashes($v);
            }
        }else{
            switch($fn){
            case"dcterms:extent":
                list($fn, $v)  = array($fn1, round($ia[$k1], 6));
                break;
            default:
                list($fn, $v)  = array($fn1, $ia[$k1]);
            }
            if(!is_null($v)) $mdata[$fn] = addslashes($v);
        }
    }

    if(!$testonly){
        $r = $gb->bsPutFile($parid, $mdata['ls:filename'], "$filename", "$storageServerPath/var/emptyMdata.xml", NULL, 'audioclip');
        if(PEAR::isError($r)){ _err($r, $filename); echo var_export($mdata)."\n"; continue; }
        $id = $r;

        $r = $gb->bsSetMetadataBatch($id, $mdata);
        if(PEAR::isError($r)){ _err($r, $filename); echo var_export($mdata)."\n"; continue; }
    }else{
        var_dump($mdata); echo"======================= ";
#        var_dump($ia); echo"======================= ";
    }

    echo "OK\n";
    $filecount++;
}

fclose($stdin);
$end    = intval(date('U'));
//echo date('H:i:s')."\n";
$time   = $end-$start;
if($time>0) $speed  = round(($filecount+$errors)/$time, 1);
else $speed = "N/A";
echo " Files ".($testonly ? "analyzed" : "imported").": $filecount, in $time s, $speed files/s, errors: $errors\n";
?>