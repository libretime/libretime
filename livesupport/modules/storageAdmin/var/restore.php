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
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageAdmin/var/restore.php,v $

------------------------------------------------------------------------------*/
define('NSPACE', 'lse');
define('VERBOSE', FALSE);
#define('VERBOSE', TRUE);

header("Content-type: text/plain");
require_once 'conf.php';
require_once "$storageServerPath/var/conf.php";
require_once 'DB.php';
require_once "XML/Util.php";
require_once "XML/Beautifier.php";
require_once "$storageServerPath/var/BasicStor.php";
require_once "$storageServerPath/var/Prefs.php";

/* =========================================================== misc functions */
function ls_restore_processObject($el){
    $res = array(
        'name'      => $el->attrs['name']->val,
        'type'      => $el->name,
    );
    switch($res['type']){
        case'folder':
            foreach($el->children as $i=>$o){
                $res['children'][] = ls_restore_processObject($o);
            }
        break;
        default:
            $res['gunid'] = $el->attrs['id']->val;
        break;
    }
    return $res;
}

function ls_restore_checkErr($r, $ln=''){
    if(PEAR::isError($r)){
        echo "ERROR $ln: ".$r->getMessage()." ".$r->getUserInfo()."\n";
        exit;
    }
}

function ls_restore_restoreObject($obj, $parid, $reallyInsert=TRUE){
    global $tmpdir, $bs;
    switch($obj['type']){
        case"folder";
            if($reallyInsert){
                if(VERBOSE) echo " creating folder {$obj['name']} ...\n";
                $r = $bs->bsCreateFolder($parid, $obj['name']);
                ls_restore_checkErr($r, __LINE__);
            }else $r=$parid;
            if(is_array($obj['children'])){
                foreach($obj['children'] as $i=>$ch){
                    ls_restore_restoreObject($ch, $r);
                }
            }
        break;
        case"audioClip";
        case"webstream";
        case"playlist";
            $gunid = $obj['gunid'];
            if(is_null($gunid)) break;
            $gunid3 = substr($gunid, 0, 3);
            $mediaFile = "$tmpdir/stor/$gunid3/$gunid";
#            echo "X1 $gunid, $gunid3, $mediaFile\n";
            if(!file_exists($mediaFile)) $mediaFile = NULL;
            $mdataFile = "$tmpdir/stor/$gunid3/$gunid.xml";
            if(!file_exists($mdataFile)) $mdataFile = NULL;
            if($reallyInsert){
                if(VERBOSE) echo " creating file {$obj['name']} ...\n";
                $r = $bs->bsPutFile($parid, $obj['name'],
                     $mediaFile, $mdataFile, $obj['gunid'],
                     strtolower($obj['type']));
                ls_restore_checkErr($r, __LINE__);
            }
        break;
    }
}

/* =============================================================== processing */

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$dbc = DB::connect($config['dsn'], TRUE);
$dbc->setFetchMode(DB_FETCHMODE_ASSOC);
$bs = &new BasicStor($dbc, $config);
$pr =& new Prefs($bs);

$dbxml = file_get_contents($argv[1]);
$tmpdir = $argv[2];

require_once"$storageServerPath/var/XmlParser.php";
$parser =& new XmlParser($dbxml);
if($parser->isError()){
    return PEAR::raiseError(
        "MetaData::parse: ".$parser->getError()
    );
}
$xmlTree = $parser->getTree();


/* ----------------------------------------- processing storageServer element */
$subjArr   = FALSE;
$tree     = FALSE;
foreach($xmlTree->children as $i=>$el){
    switch($el->name){
        case"subjects":
            if($subjArr !== FALSE){
                echo"ERROR: unexpected subjects element\n";
            }
            $subjArr=$el->children;
        break;
        case"folder":
            if($tree !== FALSE){
                echo"ERROR: unexpected folder element\n";
            }
            $tree = ls_restore_processObject($el);
        break;
        default:
            echo"ERROR: unknown element name {$el->name}\n";
            exit;
    }
#    echo "{$el->name}\n";
}

/* ---------------------------------------------- processing subjects element */
$subjects   = array();
$groups     = array();
foreach($subjArr as $i=>$el){
    switch($el->name){
        case"group":
            $grname = $el->attrs['name']->val;
            $groups[$grname] = $el->children;
            $subjects[$grname] = array(
                'type'      => 'group',
            );
        break;
        case"user":
            $login = $el->attrs['login']->val;
            $subjects[$login] = array(
                'type'      => 'user',
                'pass'      => $el->attrs['pass']->val,
#                'realname'  => $el->attrs['realname']->val,
                'realname'  => '',
                'prefs'     => $el->children[0]->children,
            );
        break;
    }
}

/* -------------------------------------------------------- processing groups */
foreach($groups as $grname=>$group){
    foreach($group as $i=>$el){
        switch($el->name){
            case"member":
                $groups[$grname][$i] = $el->attrs['name']->val;
            break;
            case"preferences":
                $subjects[$grname]['prefs'] = $el->children;
                unset($groups[$grname][$i]);
            break;
        }
    }
}

#var_dump($xmlTree);
#var_dump($subjArr);
#var_dump($groups);
#var_dump($subjects);
#var_dump($tree);

#exit;

/* ================================================================ restoring */

if(VERBOSE) echo " resetting storage ...\n";
$bs->resetStorage(FALSE);
$storId = $bs->storId;

/* ------------------------------------------------------- restoring subjects */
foreach($subjects as $login=>$subj){
    $uid0 = $bs->getSubjId($login);
    ls_restore_checkErr($uid0);
    switch($subj['type']){
        case"user":
            if($login=='root'){
                $r = $bs->passwd($login, NULL, $subj['pass'], TRUE);
                ls_restore_checkErr($r, __LINE__);
                $uid = $uid0;
            }else{
                if(!is_null($uid0)){
                    $r = $bs->removeSubj($login);
                    ls_restore_checkErr($r, __LINE__);
                }
                if(VERBOSE) echo " adding user $login ...\n";
                $uid = $bs->addSubj($login, $subj['pass'], $subj['realname'], TRUE);
                ls_restore_checkErr($uid, __LINE__);
            }
        break;
        case"group":
            if(!is_null($uid0)){
                $r = $bs->removeSubj($login);
                if(PEAR::isError($r)){ $uid = $uid0; break; }
                //ls_restore_checkErr($r, __LINE__);
            }
            if(VERBOSE) echo " adding group $login ...\n";
            $uid = $bs->addSubj($login, NULL);
            ls_restore_checkErr($uid, __LINE__);
#            var_export($uid); echo " ";
        break;
    }
#    echo "$login/$uid   :\n";
    if(isset($subj['prefs'])){
#        var_dump($subj['prefs']); exit;
        foreach($subj['prefs'] as $i=>$el){
            switch($el->name){
                case"pref":
                    $prefkey = $el->attrs['name']->val;
                    $prefval = $el->attrs['val']->val;
#                    echo" PREF($prefkey)=$prefval\n";
                    $res = $pr->insert($uid, $prefkey, $prefval);
                    ls_restore_checkErr($res, __LINE__);
                break;
                default:
                    var_dump($el);
            }
        }
    }
}

/* --------------------------------------------------------- restoring groups */
#var_dump($groups);
foreach($groups as $grname=>$group){
    foreach($group as $i=>$login){
        if(VERBOSE) echo " adding subject $login to group $grname ...\n";
        $r = $bs->addSubj2Gr($login, $grname);
        ls_restore_checkErr($r, __LINE__);
    }
}

/* -------------------------------------------------------- restoring objects */
ls_restore_restoreObject($tree, $storId, FALSE);

?>