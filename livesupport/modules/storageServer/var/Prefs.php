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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/Prefs.php,v $

------------------------------------------------------------------------------*/

/**
 *  Prefs class
 *
 *  LiveSupport preference storage class
 *
 *  @see StoredFile
 */

/* ================== Prefs ================== */
class Prefs{

    /**
     *  Constructor
     *
     *  @param gb object, GreenBox object reference
     */
    function Prefs(&$gb)
    {
        $this->gb         =& $gb;
        $this->dbc        =& $gb->dbc;
        $this->prefTable =  $gb->config['tblNamePrefix'].'pref';
    }

    /* ======================================================= public methods */
    /* ----------------------------------------------------- user preferences */
    /**
     *  Read preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @return string, preference value
     */
    function loadPref($sessid, $key)
    {
        $subjid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($subjid)) return $subjid;
        if(is_null($subjid)){
            return PEAR::raiseError("Prefs::loadPref: invalid session id",
                GBERR_SESS);
        }
        $val = $this->readVal($subjid, $key);
        if(PEAR::isError($val)) return $val;
        if($val === FALSE){
            return PEAR::raiseError("Prefs::loadPref: invalid preference key",
                GBERR_PREF);
        }
        return $val;
    }

    /**
     *  Save preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @param value string, preference value
     *  @return boolean
     */
    function savePref($sessid, $key, $value)
    {
        $subjid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($subjid)) return $subjid;
        if(is_null($subjid)){
            return PEAR::raiseError("Prefs::savePref: invalid session id",
                GBERR_SESS);
        }
        $r = $this->update($subjid, $key, $value);
        if(PEAR::isError($r)) return $r;
        if($r === FALSE){
            $r = $this->insert($subjid, $key, $value);
            if(PEAR::isError($r)) return $r;
        }
        return TRUE;
    }

    /**
     *  Delete preference record by session id
     *
     *  @param sessid string, session id
     *  @param key string, preference key
     *  @return boolean
     */
    function delPref($sessid, $key)
    {
        $subjid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($subjid)) return $subjid;
        if(is_null($subjid)){
            return PEAR::raiseError("Prefs::delPref: invalid session id",
                GBERR_SESS);
        }
        $r = $this->delete($subjid, $key);
        if(PEAR::isError($r)) return $r;
        if($r === FALSE){
            return PEAR::raiseError("Prefs::delPref: invalid preference key",
                GBERR_PREF);
        }
        return TRUE;
    }

    /* ---------------------------------------------------- group preferences */
    /**
     *  Read group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @return string, preference value
     */
    function loadGroupPref($sessid, $group, $key)
    {
        $subjid = $this->gb->getSubjId($group);
        if(PEAR::isError($subjid)) return $subjid;
        if(is_null($subjid)){
            return PEAR::raiseError(
                "Prefs::loadGroupPref: invalid group name", ALIBERR_NOTGR);
        }
        $val = $this->readVal($subjid, $key);
        if(PEAR::isError($val)) return $val;
        if($val === FALSE){
            return PEAR::raiseError(
                "Prefs::loadGroupPref: invalid preference key", GBERR_PREF);
        }
        return $val;
    }

    /**
     *  Save group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @param value string, preference value
     *  @return boolean
     */
    function saveGroupPref($sessid, $group, $key, $value)
    {
        $uid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($uid)) return $uid;
        if(is_null($uid)){
            return PEAR::raiseError(
                "Prefs::saveGroupPref: invalid session id", GBERR_SESS);
        }
        $gid = $this->gb->getSubjId($group);
        if(PEAR::isError($gid)) return $gid;
        if(is_null($gid)){
            return PEAR::raiseError(
                "Prefs::saveGroupPref: invalid group name", GBERR_SESS);
        }
        $memb = $this->gb->isMemberOf($uid, $gid);
        if(PEAR::isError($memb)) return $memb;
        if(!$memb){
            return PEAR::raiseError(
                "Prefs::saveGroupPref: access denied", GBERR_DENY);
        }
        $r = $this->update($gid, $key, $value);
        if(PEAR::isError($r)) return $r;
        if($r === FALSE){
            $r = $this->insert($gid, $key, $value);
            if(PEAR::isError($r)) return $r;
        }
        return TRUE;
    }

    /**
     *  Delete group preference record
     *
     *  @param sessid string, session id
     *  @param group string, group name
     *  @param key string, preference key
     *  @return boolean
     */
    function delGroupPref($sessid, $group, $key)
    {
        $uid = $this->gb->getSessUserId($sessid);
        if(PEAR::isError($uid)) return $uid;
        if(is_null($uid)){
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid session id", GBERR_SESS);
        }
        $gid = $this->gb->getSubjId($group);
        if(PEAR::isError($gid)) return $gid;
        if(is_null($gid)){
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid group name", GBERR_SESS);
        }
        $memb = $this->gb->isMemberOf($uid, $gid);
        if(PEAR::isError($memb)) return $memb;
        if(!$memb){
            return PEAR::raiseError(
                "Prefs::delGroupPref: access denied", GBERR_DENY);
        }
        $r = $this->delete($gid, $key);
        if(PEAR::isError($r)) return $r;
        if($r === FALSE){
            return PEAR::raiseError(
                "Prefs::delGroupPref: invalid preference key", GBERR_PREF);
        }
        return TRUE;
    }
    /* ==================================================== low level methods */
    /**
     *  Insert of new preference record
     *
     *  @param subjid int, local user/group id
     *  @param keystr string, preference key
     *  @param valstr string, preference value
     *  @return int, local user id
     */
    function insert($subjid, $keystr, $valstr='')
    {
        $id = $this->dbc->nextId("{$this->prefTable}_id_seq");
        if(PEAR::isError($id)) return $id;
        $r = $this->dbc->query("
            INSERT INTO {$this->prefTable}
                (id, subjid, keystr, valstr)
            VALUES
                ($id, $subjid, '$keystr', '$valstr')
        ");
        if(PEAR::isError($r)) return $r;
        return $id;
    }

    /**
     *  Read value of preference record
     *
     *  @param subjid int, local user/group id
     *  @param keystr string, preference key
     *  @return string, preference value
     */
    function readVal($subjid, $keystr)
    {
        $val = $this->dbc->getOne("
            SELECT valstr FROM {$this->prefTable}
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if(PEAR::isError($val)) return $val;
        if(is_null($val)) return FALSE;
        return $val;
    }

    /**
     *  Read all keys of subject's preferences
     *
     *  @param subjid int, local user/group id
     *  @return array, preference keys
     */
    function readKeys($subjid)
    {
        $res = $this->dbc->getAll("
            SELECT keystr FROM {$this->prefTable}
            WHERE subjid=$subjid
        ");
        if(PEAR::isError($res)) return $res;
        if(is_null($res)) return FALSE;
        return $res;
    }

    /**
     *  Update value of preference record
     *
     *  @param subjid int, local user/group id
     *  @param keystr string, preference key
     *  @param newvalstr string, new preference value
     *  @return boolean
     */
    function update($subjid, $keystr, $newvalstr='')
    {
        $r = $this->dbc->query("
            UPDATE {$this->prefTable} SET
                valstr='$newvalstr'
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if(PEAR::isError($r)) return $r;
        if($this->dbc->affectedRows()<1) return FALSE;
        return TRUE;
    }

    /**
     *  Delete preference record
     *
     *  @param subjid int, local user/group id
     *  @param keystr string, preference key
     *  @return boolean
     */
    function delete($subjid, $keystr)
    {
        $r = $this->dbc->query("
            DELETE FROM {$this->prefTable} 
            WHERE subjid=$subjid AND keystr='$keystr'
        ");
        if(PEAR::isError($r)) return $r;
        if($this->dbc->affectedRows()<1) return FALSE;
        return TRUE;
    }

    /* ==================================================== auxiliary methods */
    /**
     *  Test method
     *
     */
    function test()
    {
        $sessid = $this->gb->login('root', $this->gb->config['tmpRootPass']);
        $testkey = 'testKey';
        $testVal = 'abcDef 0123 ěščřžýáíé ĚŠČŘŽÝÁÍÉ';
        $r = savePref($sessid, $testKey, $testVal);
        if(PEAR::isError($r)) return $r;
        $val = loadPref($sessid, $testKey);
        if($val != $testVal){
            echo "ERROR: preference storage test failed.\n   ($testVal / $val)\n";
            return FALSE;
        }
        $r = savePref($sessid, $testKey, '');
        if(PEAR::isError($r)) return $r;
        $val = loadPref($sessid, $testKey);
        if($val != $testVal){
            echo "ERROR: preference storage test failed.\n   ('' / '$val')\n";
            return FALSE;
        }
        return TRUE;
    }

    /**
     *  Install database table for preference storage
     *
     *  @return boolean
     */
    function install()
    {
        $this->dbc->createSequence("{$this->prefTable}_id_seq");
        $r = $this->dbc->query("CREATE TABLE {$this->prefTable} (
            id int not null,
            subjid int REFERENCES {$this->gb->subjTable} ON DELETE CASCADE,
            keystr varchar(255),
            valstr text
        )");
        if(PEAR::isError($r)) return $r;
        $this->dbc->query("CREATE UNIQUE INDEX {$this->prefTable}_id_idx
            ON {$this->prefTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->prefTable}_subj_key_idx
            ON {$this->prefTable} (subjid, keystr)");
        $this->dbc->query("CREATE INDEX {$this->prefTable}_subjid_idx
            ON {$this->prefTable} (subjid)");
        $stPrefGr = $this->gb->getSubjId($this->gb->config['StationPrefsGr']);
        if(PEAR::isError($stPrefGr)) echo $stPrefGr->getMessage()."\n";
        $r = $this->insert($stPrefGr, 'stationName', "Radio Station 1");
        if(PEAR::isError($r)) echo $r->getMessage()."\n";
        $genres = file_get_contents( dirname(__FILE__).'/genres.xml');
        $r = $this->insert($stPrefGr, 'genres', $genres);
        if(PEAR::isError($r)) echo $r->getMessage()."\n";
        return TRUE;
    }

    /**
     *  Uninstall database table for preference storage
     *
     *  @return boolean
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->prefTable}");
        $this->dbc->dropSequence("{$this->prefTable}_id_seq");
    }

    /**
     *
     * /
    function ()
    {
    }*/

}
?>
