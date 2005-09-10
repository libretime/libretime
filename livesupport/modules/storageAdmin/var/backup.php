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
define('NSPACE', 'lse');

header("Content-type: text/plain");
require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
require_once 'DB.php';
require_once "XML/Util.php";
require_once "XML/Beautifier.php";
require_once "$storageServerPath/var/BasicStor.php";
require_once "$storageServerPath/var/Prefs.php";

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$bs = &new BasicStor($dbc, $config);

$stid = $bs->storId;
#var_dump($stid); exit;
#$farr = $bs->bsListFolder($stid); var_dump($farr); exit;

function admDumpFolder(&$bs, $fid, $ind=''){
    $name = $bs->getObjName($fid);
    if(PEAR::isError($name)){ echo $name->getMessage(); exit; }
    $type = $bs->getObjType($fid);
    if(PEAR::isError($type)){ echo $type->getMessage(); exit; }
    $gunid = $bs->_gunidFromId($fid);
    if(PEAR::isError($gunid)){ echo $gunid->getMessage(); exit; }
    $pars  = array();
    if($gunid){ $pars['id']="$gunid"; }
    $pars['name']="$name";
    switch($type){
        case"Folder":
            $farr = $bs->bsListFolder($fid);
            if(PEAR::isError($farr)){ echo $farr->getMessage(); exit; }
            $res = '';
            foreach($farr as $i =>$folder){
                $fid2 = $folder['id'];
                $res .= admDumpFolder($bs, $fid2, "$ind ");
            }
            if(!$res){
                return XML_Util::createTagFromArray(array(
                    'namespace' => NSPACE,
                    'localPart' => 'folder',
                    'attributes'=> $pars,
                ));
            }else{
                return XML_Util::createTagFromArray(array(
                    'namespace' => NSPACE,
                    'localPart' => 'folder',
                    'attributes'=> $pars,
                    'content'   => $res,
                ), FALSE);
            }
            break;
        case"audioclip":
            return XML_Util::createTagFromArray(array(
                'namespace' => NSPACE,
                'localPart' => 'audioClip',
                'attributes'=> $pars,
            ));
            break;
        case"webstream":
            return XML_Util::createTagFromArray(array(
                'namespace' => NSPACE,
                'localPart' => 'webstream',
                'attributes'=> $pars,
            ));
            break;
        case"playlist":
            return XML_Util::createTagFromArray(array(
                'namespace' => NSPACE,
                'localPart' => 'playlist',
                'attributes'=> $pars,
            ));
            break;
        default:
            return "";
    }

}
function admDumpGroup(&$bs, $gid, $ind=''){
    $name = $bs->getSubjName($gid);
    if(PEAR::isError($name)){ echo $name->getMessage(); exit; }
    $isGr = $bs->isGroup($gid);
    if(PEAR::isError($isGr)){ echo $isGr->getMessage(); exit; }
    $pars = array('name'=>"$name");
    $pars['id'] = $gid;
    if(!$isGr){
        return XML_Util::createTagFromArray(array(
            'namespace' => NSPACE,
            'localPart' => 'member',
            'attributes'=> $pars,
        ));
    }
    $garr = $bs->listGroup($gid);
    if(PEAR::isError($farr)){ echo $farr->getMessage(); exit; }
    $res = '';
    foreach($garr as $i =>$member){
        $fid2 = $member['id'];
        $res .= admDumpGroup($bs, $fid2, "$ind ");
    }
    $tagarr = array(
            'namespace' => NSPACE,
            'localPart' => 'group',
            'attributes'=> $pars,
    );
    $prefs = admDumpPrefs($bs, $gid);
    if(!is_null($prefs)) $res .= $prefs;
    if($res) $tagarr['content'] = $res;
    return XML_Util::createTagFromArray($tagarr, $res === '');
    if(!$res){
    }else{
        return XML_Util::createTagFromArray(array(
            'namespace' => NSPACE,
            'localPart' => 'group',
            'attributes'=> $pars,
            'content'   => $res,
        ), FALSE);
    }

}
function admDumpSubjects(&$bs, $ind=''){
    $res ='';
    $subjs = $bs->getSubjects('id, login, pass, type');
    foreach($subjs as $i =>$member){
        switch($member['type']){
            case"U":
                $prefs = admDumpPrefs($bs, $member['id']);
                $pars = array('login'=>"{$member['login']}", 'pass'=>"{$member['pass']}");
                $pars['id'] = $member['id'];
                $tagarr =                 array(
                    'namespace' => NSPACE,
                    'localPart' => 'user',
                    'attributes'=> $pars,
                );
                if(!is_null($prefs)) $tagarr['content'] = $prefs;
                $res .= XML_Util::createTagFromArray($tagarr , FALSE);
                break;
            case"G":
                $res .= admDumpGroup($bs, $member['id'], "$ind  ");
                break;
        }
    }
#    return "$ind<subjects>\n$res$ind</subjects>\n";
    return XML_Util::createTagFromArray(array(
        'namespace' => NSPACE,
        'localPart' => 'subjects',
        'content'=> $res,
    ), FALSE);
}

function admDumpPrefs(&$bs, $subjid){
    $res ='';
    $pr =& new Prefs($bs);
    $prefkeys = $pr->readKeys($subjid);
#    var_dump($subjid); var_dump($prefkeys); #exit;
    foreach($prefkeys as $i =>$prefk){
        $keystr = $prefk['keystr'];
        $prefval = $pr->readVal($subjid, $keystr);
        $pars = array('name'=>"$keystr", 'val'=>"$prefval");
        $res .= XML_Util::createTagFromArray(array(
            'namespace' => NSPACE,
            'localPart' => 'pref',
            'attributes'=> $pars,
        ));
    }
    if(!$res) return NULL;
    return XML_Util::createTagFromArray(array(
        'namespace' => NSPACE,
        'localPart' => 'preferences',
        'content'=> $res,
    ), FALSE);
}

$subjects = admDumpSubjects($bs, ' ');
$tree = admDumpFolder($bs, $stid, ' ');

$res = XML_Util::getXMLDeclaration("1.0", "UTF-8")."\n";
$node = XML_Util::createTagFromArray(array(
    'namespace' => NSPACE,
    'localPart' => 'storageServer',
    'content'   => "$subjects$tree",
), FALSE);
$res .= $node;

$fmt = new XML_Beautifier();
$res = $fmt->formatString($res);

#var_export($res);
#var_dump($res);
echo "$res";

?>