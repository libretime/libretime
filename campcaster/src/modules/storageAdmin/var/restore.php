<?php
/**
 * @author $Author$
 * @version $Revision$
 */
define('NSPACE', 'lse');
define('VERBOSE', FALSE);
#define('VERBOSE', TRUE);

header("Content-type: text/plain");
require_once 'conf.php';
require_once "$STORAGE_SERVER_PATH/var/conf.php";
require_once 'DB.php';
require_once "XML/Util.php";
require_once "XML/Beautifier.php";
require_once "$STORAGE_SERVER_PATH/var/BasicStor.php";
require_once "$STORAGE_SERVER_PATH/var/Prefs.php";

/* =========================================================== misc functions */
function ls_restore_processObject($el)
{
    $res = array(
        'name' => $el->attrs['name']->val,
        'type' => $el->name,
    );
    switch ($res['type']) {
        case 'folder':
            foreach ($el->children as $i => $o) {
                $res['children'][] = ls_restore_processObject($o);
            }
            break;
        default:
            $res['gunid'] = $el->attrs['id']->val;
            break;
    }
    return $res;
}

function ls_restore_checkErr($r, $ln='')
{
    if (PEAR::isError($r)) {
        echo "ERROR $ln: ".$r->getMessage()." ".$r->getUserInfo()."\n";
        exit;
    }
}

function ls_restore_restoreObject($obj, $parid, $reallyInsert=TRUE){
    global $tmpdir, $bs;
    switch ($obj['type']) {
        case "folder";
            if ($reallyInsert) {
                if (VERBOSE) {
                    echo " creating folder {$obj['name']} ...\n";
                }
                $r = BasicStor::bsCreateFolder($parid, $obj['name']);
                ls_restore_checkErr($r, __LINE__);
            } else {
                $r = $parid;
            }
            if (isset($obj['children']) && is_array($obj['children'])) {
                foreach ($obj['children'] as $i => $ch) {
                    ls_restore_restoreObject($ch, $r);
                }
            }
            break;
        case "audioClip";
        case "webstream";
        case "playlist";
            $gunid = $obj['gunid'];
            if (is_null($gunid)) {
                break;
            }
            $gunid3 = substr($gunid, 0, 3);
            $mediaFile = "$tmpdir/stor/$gunid3/$gunid";
#            echo "X1 $gunid, $gunid3, $mediaFile\n";
            if (!file_exists($mediaFile)) {
                $mediaFile = NULL;
            }
            $mdataFile = "$tmpdir/stor/$gunid3/$gunid.xml";
            if (!file_exists($mdataFile)) {
                $mdataFile = NULL;
            }
            if ($reallyInsert) {
                if (VERBOSE) {
                    echo " creating file {$obj['name']} ...\n";
                }
                $values = array(
                    "filename" => $obj['name'],
                    "filepath" => $mediaFile,
                    "metadata" => $mdataFile,
                    "gunid" => $obj['gunid'],
                    "filetype" => strtolower($obj['type'])
                );
                $r = $bs->bsPutFile($parid, $values);
                ls_restore_checkErr($r, __LINE__);
            }
        break;
    }
}

/* =============================================================== processing */

PEAR::setErrorHandling(PEAR_ERROR_RETURN);
$CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
$CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
$bs = new BasicStor();
$pr = new Prefs($bs);

$dbxml = file_get_contents($argv[1]);
$tmpdir = $argv[2];

require_once("$STORAGE_SERVER_PATH/var/XmlParser.php");
$parser = new XmlParser($dbxml);
if ($parser->isError()) {
    return PEAR::raiseError(
        "MetaData::parse: ".$parser->getError()
    );
}
$xmlTree = $parser->getTree();


/* ----------------------------------------- processing storageServer element */
$subjArr = FALSE;
$tree = FALSE;
foreach ($xmlTree->children as $i => $el) {
    switch ($el->name) {
        case "subjects":
            if ($subjArr !== FALSE) {
                echo "ERROR: unexpected subjects element\n";
            }
            $subjArr = $el->children;
            break;
        case "folder":
            if ($tree !== FALSE) {
                echo "ERROR: unexpected folder element\n";
            }
            $tree = ls_restore_processObject($el);
            break;
        default:
            echo "ERROR: unknown element name {$el->name}\n";
            exit;
    }
//    echo "{$el->name}\n";
}

/* ---------------------------------------------- processing subjects element */
$subjects = array();
$groups = array();
foreach ($subjArr as $i => $el) {
    switch ($el->name) {
        case "group":
            $grname = $el->attrs['name']->val;
            $groups[$grname] = $el->children;
            $subjects[$grname] = array(
                'type'      => 'group',
            );
            break;
        case "user":
            $login = $el->attrs['login']->val;
            $subjects[$login] = array(
                'type'      => 'user',
                'pass'      => $el->attrs['pass']->val,
#                'realname'  => $el->attrs['realname']->val,
                'realname'  => '',
                'prefs'     => (isset($el->children[0]) ? $el->children[0]->children : NULL),
            );
            break;
    }
}

/* -------------------------------------------------------- processing groups */
foreach ($groups as $grname => $group) {
    foreach ($group as $i => $el) {
        switch ($el->name) {
            case "member":
                $groups[$grname][$i] = $el->attrs['name']->val;
                break;
            case "preferences":
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

if (VERBOSE) {
    echo " resetting storage ...\n";
}
$bs->resetStorage(FALSE);
$storId = $bs->storId;

/* ------------------------------------------------------- restoring subjects */
foreach ($subjects as $login => $subj) {
    $uid0 = Subjects::GetSubjId($login);
    ls_restore_checkErr($uid0);
    switch ($subj['type']) {
        case "user":
            if ($login=='root') {
                $r = $bs->passwd($login, NULL, $subj['pass'], TRUE);
                ls_restore_checkErr($r, __LINE__);
                $uid = $uid0;
            } else {
                if (!is_null($uid0)) {
                    $r = $bs->removeSubj($login);
                    ls_restore_checkErr($r, __LINE__);
                }
                if (VERBOSE) {
                    echo " adding user $login ...\n";
                }
                $uid = $bs->addSubj($login, $subj['pass'], $subj['realname'], TRUE);
                ls_restore_checkErr($uid, __LINE__);
            }
            break;
        case "group":
            if (!is_null($uid0)) {
                $r = $bs->removeSubj($login);
                if (PEAR::isError($r)) {
                    $uid = $uid0;
                    break;
                }
                //ls_restore_checkErr($r, __LINE__);
            }
            if (VERBOSE) {
                echo " adding group $login ...\n";
            }
            $uid = $bs->addSubj($login, NULL);
            ls_restore_checkErr($uid, __LINE__);
//            var_export($uid); echo " ";
          break;
    } // switch

//    echo "$login/$uid   :\n";
    if (isset($subj['prefs'])) {
//        var_dump($subj['prefs']); exit;
        foreach ($subj['prefs'] as $i => $el) {
            switch ($el->name) {
                case "pref":
                    $prefkey = $el->attrs['name']->val;
                    $prefval = $el->attrs['val']->val;
//                    echo" PREF($prefkey)=$prefval\n";
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
foreach ($groups as $grname => $group) {
    foreach ($group as $i => $login) {
        if (VERBOSE) {
            echo " adding subject $login to group $grname ...\n";
        }
        $r = Subjects::AddSubjectToGroup($login, $grname);
        ls_restore_checkErr($r, __LINE__);
    }
}

/* -------------------------------------------------------- restoring objects */
ls_restore_restoreObject($tree, $storId, FALSE);

?>