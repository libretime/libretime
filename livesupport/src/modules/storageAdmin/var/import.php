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
    Location : $URL$

------------------------------------------------------------------------------*/
ini_set('memory_limit', '64M');
header("Content-type: text/plain");
echo "\n#StorageServer import script:\n";
//echo date('H:i:s')."\n";
$start=intval(date('U'));

require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
require_once 'DB.php';
require_once "$storageServerPath/var/GreenBox.php";

//PEAR::setErrorHandling(PEAR_ERROR_PRINT, "%s<hr>\n");
PEAR::setErrorHandling(PEAR_ERROR_RETURN);
//PEAR::setErrorHandling(PEAR_ERROR_DIE, "%s\n");
$dbc = DB::connect($config['dsn'], TRUE);
if(PEAR::isError($dbc)){ echo "ERROR: ".$dbc->getMessage()." ".$dbc->getUserInfo()."\n"; exit(1); }
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$gb = &new GreenBox($dbc, $config);

$testonly = (isset($argv[1]) && $argv[1] == '-t');

$errors=0;
$filecount=0;
function _err($r, $fn, $txt=''){
    global $errors;
    if(PEAR::isError($r)) $msg = $r->getMessage()." ".$r->getUserInfo();
    else $msg = $txt;
    echo "ERROR\n $msg\n";
    $errors++;
}

$flds = array(
    'dc:format'     => array(
        array('path'=>"['mime_type']", 'ignoreEnc'=>TRUE),
    ),
    'ls:bitrate'    => array(
        array('path'=>"['bitrate']", 'ignoreEnc'=>TRUE),
    ),
    'dcterms:extent'=> array(
        array('path'=>"['playtime_seconds']", 'ignoreEnc'=>TRUE),
    ),
    'dc:title'	    => array(
        array('path'=>"['id3v2']['TIT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TT2'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
//        array('path'=>"['id3v1']", 'dataPath'=>"['title']", 'encPath'=>"['encoding']"),
    ),
    'dc:creator'	=> array(
        array('path'=>"['id3v2']['TPE1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TP1'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
//        array('path'=>"['id3v1']", 'dataPath'=>"['artist']", 'encPath'=>"['encoding']"),
    ),
    'dc:source'	    => array(
        array('path'=>"['id3v2']['TALB'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TAL'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
    ),
    'ls:encoded_by'	=> array(
        array('path'=>"['id3v2']['TENC'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TEN'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
    ),
    'ls:track_num'	=> array(
        array('path'=>"['id3v2']['TRCK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TRK'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
    ),
    'ls:genre'	    => array(
        array('path'=>"['id3v2']['TALB'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
        array('path'=>"['id3v2']['TAL'][0]", 'dataPath'=>"['data']", 'encPath'=>"['encoding']"),
    ),
    'ls:channels'	=> array(
        array('path'=>"['audio']['channels']", 'ignoreEnc'=>TRUE),
    ),
    //'ls:year'	    => array(array('path'=>"['comments']['date']")),
    //'dc:publisher'	=> array(array('path'=>"['comments']['label']")),
    'ls:filename'	=> array(
        array('path'=>"['filename']"),
    ),
/*
    'xx:fileformat' => array(array('path'=>"['fileformat']")),
    'xx:filesize'   => array(array('path'=>"['filesize']")),
    'xx:dataformat' => array(array('path'=>"['audio']['dataformat']")),
    'xx:sample_rate'=> array(array('path'=>"['audio']['sample_rate']")),
*/
);

$titleKey = 'dc:title';

$r = $gb->getObjId('import', $gb->storId);
if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
if(is_null($r)){
    $r = $gb->bsCreateFolder($gb->storId, 'import');
    if(PEAR::isError($r)){ echo "ERROR: ".$r->getMessage()." ".$r->getUserInfo()."\n"; exit(1); }
}
$parid = $r;

function addMdata($key, $val, $iEnc='iso-8859-1'){
    global $mdata, $titleHaveSet, $titleKey;
    #echo "$key($iEnc): $val\n";
    if(!is_null($val)){
        $data = $val;
        $oEnc = 'UTF-8';
        if(function_exists('iconv') && $iEnc != $oEnc){
            $data = $r = @iconv($iEnc, $oEnc, $data);
            if($r === FALSE){
                echo "Warning: convert $key data to unicode failed\n";
                $data = $val;  // fallback
            }
        }
        if($key == $titleKey) $titleHaveSet = TRUE;
        $mdata[$key] = trim($data);
    }
}

$stdin = fopen('php://stdin', 'r');
while($filename = fgets($stdin, 2048)){
    $filename = rtrim($filename);
    if(!preg_match('/\.(ogg|wav|mp3|mpg|mpeg)$/', strtolower($filename), $var)){
        // echo "File extension not supported - skipping file\n";
        continue;
    }
    echo "$filename:   ";
    set_time_limit(30);
    //$infoFromFile = GetAllFileInfo("$filename", 'mp3');
    //prepared for getid3 upgrade:
    $getID3 = new getID3;
    $infoFromFile = $getID3->analyze("$filename");
    //echo "\n".var_export($infoFromFile)."\n"; exit;
    if(PEAR::isError($infoFromFile)){ _err($infoFromFile, $filename); continue; }
    if(isset($infoFromFile['error'])){ _err(NULL, $filename, $infoFromFile['error']); continue; }
    #if(!$infoFromFile['fileformat']){ echo "???\n"; continue; }
    if(!$infoFromFile['bitrate']){ echo "not audio?\n"; continue; }
    
    $mdata = array();
    $titleHaveSet = FALSE;
    foreach($flds as $key=>$getid3keys){
                if($testonly) echo "$key\n";
        foreach($getid3keys as $getid3key){
            // defaults:
            $ignoreEnc  = FALSE;
            $dataPath   = "";
            $encPath    = "";
            $enc        = "UTF-8";
            extract($getid3key);
            $vn = "\$infoFromFile$path$dataPath";
            if($testonly) echo "   $vn   ->   ";
            eval("\$vnFl = isset($vn);");
            if($vnFl){
                eval("\$data = $vn;");
                if($testonly) echo "$data\n";
                if(!$ignoreEnc && $encPath != ""){
                    $encVn = "\$infoFromFile$path$encPath";
                    eval("\$encVnFl = isset($encVn);");
                    if($encVnFl){
                        eval("\$enc = $encVn;");
                        }
                }
                if($testonly) echo "        ENC=$enc\n";
                //addMdata($key, $data);
                addMdata($key, $data, $enc);
                break;
            }else{
                if($testonly) echo "\n";
            }
        }
    }
    if($testonly) var_dump($mdata);

    if(!$titleHaveSet || trim($mdata[$titleKey])=='') addMdata($titleKey, basename($filename));

    if(!$testonly){
        $r = $gb->bsPutFile($parid, $mdata['ls:filename'], "$filename", "$storageServerPath/var/emptyMdata.xml", NULL, 'audioclip');
        if(PEAR::isError($r)){ _err($r, $filename); echo var_export($mdata)."\n"; continue; }
        $id = $r;

        $r = $gb->bsSetMetadataBatch($id, $mdata);
        if(PEAR::isError($r)){ _err($r, $filename); echo var_export($mdata)."\n"; continue; }
    }else{
        var_dump($infoFromFile); echo"======================= ";
        var_dump($mdata); echo"======================= ";
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