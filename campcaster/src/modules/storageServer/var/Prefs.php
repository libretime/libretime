<?php

require_once("GreenBox.php");

/**
 * Preference storage class.
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see StoredFile
 */
/* ================== Prefs ================== */
class Prefs {

    public $gb;

    /**
     *  Constructor
     *
     * @param GreenBox $gb
     * 		GreenBox object reference
     */
    public function __construct(&$gb)
    {
        $this->gb =& $gb;
    }


    /* ======================================================= public methods */
    /* ----------------------------------------------------- user preferences */
    /**
     * Read preference record by session id
     *
     * @param string $sessid
     * 		session id
     * @param string $key
     * 		preference key
     * @return string
     * 		preference value
     */
    function loadPref($sessid, $key)
    {
        $subjid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($subjid)) {
        	return $subjid;
        }
        if (is_null($subjid)) {
            return PEAR::raiseError("Prefs::loadPref: invalid session id",
                GBERR_SESS);
        }
        $val = $this->readVal($subjid, $key);
        if (PEAR::isError($val)) {
        	return $val;
        }
        if ($val === FALSE) {
            return PEAR::raiseError("Prefs::loadPref: invalid preference key",
                GBERR_PREF);
        }
        return $val;
    }


    /**
     * Save preference record by session id
     *
     * @param string $sessid
     * 		session id
     * @param string $key
     * 		preference key
     * @param string $value
     * 		preference value
     * @return boolean
     */
    function savePref($sessid, $key, $value)
    {
        $subjid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($subjid)) {
        	return $subjid;
        }
        if (is_null($subjid)) {
            return PEAR::raiseError("Prefs::savePref: invalid session id",
                GBERR_SESS);
        }
        $r = $this->update($subjid, $key, $value);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
            $r = $this->insert($subjid, $key, $value);
            if (PEAR::isError($r)) {
            	return $r;
            }
        }
        return TRUE;
    }


    /**
     *  Delete preference record by session id
     *
     * @param string $sessid
     * 		session id
     * @param string $key
     * 		preference key
     * @return boolean
     */
    function delPref($sessid, $key)
    {
        $subjid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($subjid)) {
        	return $subjid;
        }
        if (is_null($subjid)) {
            return PEAR::raiseError("Prefs::delPref: invalid session id",
                GBERR_SESS);
        }
        $r = $this->delete($subjid, $key);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
            return PEAR::raiseError("Prefs::delPref: invalid preference key",
                GBERR_PREF);
        }
        return TRUE;
    }


    /* ---------------------------------------------------- group preferences */
    /**
     *  Read group preference record
     *
     * @param string $sessid
     * 		session id
     * @param string $group
     * 		group name
     * @param string $key
     * 		preference key
     * @return string
     * 		preference value
     */
    function loadGroupPref($sessid, $group, $key)
    {
        // if sessid is would be used here fix Transport::cronCallMethod !
        $subjid = Subjects::GetSubjId($group);
        if (PEAR::isError($subjid)) {
        	return $subjid;
        }
        if (is_null($subjid)) {
            return PEAR::raiseError(
                "Prefs::loadGroupPref: invalid group name", ALIBERR_NOTGR);
        }
        $val = $this->readVal($subjid, $key);
        if (PEAR::isError($val)) {
        	return $val;
        }
        if ($val === FALSE) {
            return PEAR::raiseError(
                "Prefs::loadGroupPref: invalid preference key", GBERR_PREF);
        }
        return $val;
    }


    /**
     *  Save group preference record
     *
     * @param string $sessid
     * 		session id
     * @param string $group
     * 		group name
     * @param string $key
     * 		preference key
     * @param string $value
     * 		preference value
     * @return boolean
     */
    function saveGroupPref($sessid, $group, $key, $value)
    {
        $uid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($uid)) {
        	return $uid;
        }
        if (is_null($uid)) {
            return PEAR::raiseError(
                "Prefs::saveGroupPref: invalid session id", GBERR_SESS);
        }
        $gid = Subjects::GetSubjId($group);
        if (PEAR::isError($gid)) {
        	return $gid;
        }
        if (is_null($gid)) {
            return PEAR::raiseError(
                "Prefs::saveGroupPref: invalid group name", GBERR_SESS);
        }
        $memb = Subjects::IsMemberOf($uid, $gid);
        if (PEAR::isError($memb)) {
        	return $memb;
        }
        if (!$memb) {
            return PEAR::raiseError(
                "Prefs::saveGroupPref: access denied", GBERR_DENY);
        }
        $r = $this->update($gid, $key, $value);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
            $r = $this->insert($gid, $key, $value);
            if (PEAR::isError($r)) {
            	return $r;
            }
        }
        return TRUE;
    }

    /**
     *  Delete group preference record
     *
     * @param string $sessid
     * 		session id
     * @param string $group
     * 		group name
     * @param string $key
     * 		preference key
     * @return boolean
     */
    function delGroupPref($sessid, $group, $key)
    {
        $uid = GreenBox::GetSessUserId($sessid);
        if (PEAR::isError($uid)) {
        	return $uid;
        }
        if (is_null($uid)) {
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid session id", GBERR_SESS);
        }
        $gid = Subjects::GetSubjId($group);
        if (PEAR::isError($gid)) {
        	return $gid;
        }
        if (is_null($gid)) {
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid group name", GBERR_SESS);
        }
        $memb = Subjects::IsMemberOf($uid, $gid);
        if (PEAR::isError($memb)) {
        	return $memb;
        }
        if (!$memb) {
            return PEAR::raiseError(
                "Prefs::delGroupPref: access denied", GBERR_DENY);
        }
        $r = $this->delete($gid, $key);
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($r === FALSE) {
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid preference key", GBERR_PREF);
        }
        return TRUE;
    }


    /* ==================================================== low level methods */
    /**
     *  Insert of new preference record
     *
     * @param int $subjid
     * 		local user/group id
     * @param string $keystr
     * 		preference key
     * @param string $valstr
     * 		preference value
     * @return int
     * 		local user id
     */
    public static function Insert($subjid, $keystr, $valstr='')
    {
        global $CC_CONFIG, $CC_DBC;
        $id = $CC_DBC->nextId($CC_CONFIG['prefTable']."_id_seq");
        if (PEAR::isError($id)) {
        	return $id;
        }
        $r = $CC_DBC->query("
            INSERT INTO ".$CC_CONFIG['prefTable']."
                (id, subjid, keystr, valstr)
            VALUES
                ($id, $subjid, '$keystr', '$valstr')
        ");
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $id;
    }


    /**
     *  Read value of preference record
     *
     * @param int $subjid
     * 		local user/group id
     * @param string $keystr
     * 		preference key
     * @return string
     * 		preference value
     */
    function readVal($subjid, $keystr)
    {
        global $CC_CONFIG, $CC_DBC;
        $val = $CC_DBC->getOne("
            SELECT valstr FROM ".$CC_CONFIG['prefTable']."
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if (PEAR::isError($val)) {
        	return $val;
        }
        if (is_null($val)) {
        	return FALSE;
        }
        return $val;
    }


    /**
     *  Read all keys of subject's preferences
     *
     * @param int $subjid
     * 		local user/group id
     * @return array
     * 		preference keys
     */
    function readKeys($subjid)
    {
        global $CC_CONFIG, $CC_DBC;
        $res = $CC_DBC->getAll("
            SELECT keystr FROM ".$CC_CONFIG['prefTable']."
            WHERE subjid=$subjid
        ");
        if (PEAR::isError($res)) {
        	return $res;
        }
        if (is_null($res)) {
        	return FALSE;
        }
        return $res;
    }


    /**
     *  Update value of preference record
     *
     * @param int $subjid
     * 		local user/group id
     * @param string $keystr
     * 		preference key
     * @param string $newvalstr
     * 		new preference value
     * @return boolean
     */
    function update($subjid, $keystr, $newvalstr='')
    {
        global $CC_CONFIG, $CC_DBC;
        $r = $CC_DBC->query("
            UPDATE ".$CC_CONFIG['prefTable']." SET
                valstr='$newvalstr'
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($CC_DBC->affectedRows() < 1) {
        	return FALSE;
        }
        return TRUE;
    }


    /**
     *  Delete preference record
     *
     * @param int $subjid
     * 		local user/group id
     * @param string $keystr
     * 		preference key
     * @return boolean
     */
    function delete($subjid, $keystr)
    {
        global $CC_CONFIG, $CC_DBC;
        $r = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['prefTable']."
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if (PEAR::isError($r)) {
        	return $r;
        }
        if ($CC_DBC->affectedRows() < 1) {
        	return FALSE;
        }
        return TRUE;
    }


    /* ==================================================== auxiliary methods */
    /**
     *  Test method
     *
     */
    function test()
    {
        global $CC_CONFIG;
        $sessid = Alib::Login('root', $CC_CONFIG['tmpRootPass']);
        $testkey = 'testKey';
        $testVal = 'abcDef 0123 ěščřžýáíé ĚŠČŘŽÝÁÍÉ';
        $r = savePref($sessid, $testKey, $testVal);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $val = loadPref($sessid, $testKey);
        if ($val != $testVal) {
            echo "ERROR: preference storage test failed.\n   ($testVal / $val)\n";
            return FALSE;
        }
        $r = savePref($sessid, $testKey, '');
        if (PEAR::isError($r)) {
        	return $r;
        }
        $val = loadPref($sessid, $testKey);
        if ($val != $testVal) {
            echo "ERROR: preference storage test failed.\n   ('' / '$val')\n";
            return FALSE;
        }
        return TRUE;
    }


    /**
     *  Install database table for preference storage
     *
     * @return boolean
     */
//    function install()
//    {
//        global $CC_CONFIG, $CC_DBC;
//        $CC_DBC->createSequence("{$this->prefTable}_id_seq");
//        $r = $CC_DBC->query("CREATE TABLE {$this->prefTable} (
//            id int not null,
//            subjid int REFERENCES ".$CC_CONFIG['subjTable']." ON DELETE CASCADE,
//            keystr varchar(255),
//            valstr text
//        )");
//        if (PEAR::isError($r)) {
//        	return $r;
//        }
//        $CC_DBC->query("CREATE UNIQUE INDEX {$this->prefTable}_id_idx
//            ON {$this->prefTable} (id)");
//        $CC_DBC->query("CREATE UNIQUE INDEX {$this->prefTable}_subj_key_idx
//            ON {$this->prefTable} (subjid, keystr)");
//        $CC_DBC->query("CREATE INDEX {$this->prefTable}_subjid_idx
//            ON {$this->prefTable} (subjid)");
//        $stPrefGr = Subjects::GetSubjId($CC_CONFIG['StationPrefsGr']);
//        if (PEAR::isError($stPrefGr)) {
//        	echo $stPrefGr->getMessage()."\n";
//        }
//        $r = Prefs::Insert($stPrefGr, 'stationName', "Radio Station 1");
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()."\n";
//        }
//        $genres = file_get_contents(dirname(__FILE__).'/../genres.xml');
//        $r = Prefs::Insert($stPrefGr, 'genres', $genres);
//        if (PEAR::isError($r)) {
//        	echo $r->getMessage()."\n";
//        }
//        return TRUE;
//    }


    /**
     *  Uninstall database table for preference storage
     *
     * @return boolean
     */
//    function uninstall()
//    {
//        global $CC_CONFIG, $CC_DBC;
//        $CC_DBC->query("DROP TABLE {$this->prefTable}");
//        $CC_DBC->dropSequence("{$this->prefTable}_id_seq");
//    }

} // class Prefs
?>