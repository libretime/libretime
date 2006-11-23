<?php
require_once("ObjClasses.php");
define('ALIBERR_NOTGR', 20);
define('ALIBERR_BADSMEMB', 21);

/**
 * Subj class
 *
 * users + groups
 * with "linearized recursive membership" ;)
 *   (allow adding users to groups or groups to groups)
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage Alib
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see ObjClasses
 * @see Alib
 */
class Subjects extends ObjClasses {
	/**
	 * The name of the 'Subjects' database table.
	 *
	 * @var string
	 */
    public $subjTable;

    /**
     * The name of a database table.
     *
     * @var string
     */
    public $smembTable;


    /**
     * Constructor
     *
     * @param object $dbc
     * @param array $config
     * @return this
     */
    public function __construct(&$dbc, $config)
    {
        parent::__construct($dbc, $config);
        $this->subjTable = $config['tblNamePrefix'].'subjs';
        $this->smembTable = $config['tblNamePrefix'].'smemb';
    } // constructor


    /* ======================================================= public methods */

    /**
     * Add new subject
     *
     * @param string $login
     * @param string $pass
     * @param string $realname
     * @param boolean $passenc
     * 		password already encrypted if true
     * @return int/err
     */
    public function addSubj($login, $pass=NULL, $realname='', $passenc=FALSE)
    {
        if(!$login) {
            return $this->dbc->raiseError(get_class($this)."::addSubj: empty login");
        }
        $id = $this->dbc->nextId("{$this->subjTable}_id_seq");
        if (PEAR::isError($id)) {
            return $id;
        }
        if (!is_null($pass) && !$passenc) {
            $pass = md5($pass);
        }
        $sql = "INSERT INTO {$this->subjTable} (id, login, pass, type, realname)
            VALUES ($id, '$login', ".
                (is_null($pass) ? "'!', 'G'" : "'$pass', 'U'").",
                '$realname')";
        $r = $this->dbc->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $id;
    } // fn addSubj


    /**
     * Remove subject by uid or by login
     *
     * @param string $login
     * @param int $uid
     * 		optional, default: null
     * @return boolean/err
     */
    public function removeSubj($login, $uid=NULL)
    {
        if (is_null($uid)) {
            $uid = $this->getSubjId($login);
        }
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $sql = "DELETE FROM {$this->smembTable}
            WHERE (uid='$uid' OR gid='$uid') AND mid is null";
        $r = $this->dbc->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        $sql2 = "DELETE FROM {$this->subjTable}
            WHERE login='$login'";
        $r = $this->dbc->query($sql2);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $this->_rebuildRels();
    } // fn removeSubj


    /**
     * Check login and password
     *
     * @param string $login
     * @param string $pass
     * 		optional
     * @return boolean/int/err
     */
    public function authenticate($login, $pass='')
    {
        $cpass = md5($pass);
        $sql = "SELECT id FROM {$this->subjTable}
            WHERE login='$login' AND pass='$cpass' AND type='U'";
        $id = $this->dbc->getOne($sql);
        if (PEAR::isError($id)) {
            return $id;
        }
        return (is_null($id) ? FALSE : $id);
    } // fn authenticate


    /**
     * Set lastlogin or lastfail timestamp
     *
     * @param string $login
     * @param boolean $failed
     * 		true=> set lastfail, false=> set lastlogin
     * @return boolean/int/err
     */
    public function setTimeStamp($login, $failed=FALSE)
    {
        $fld = ($failed ? 'lastfail' : 'lastlogin');
        $sql = "UPDATE {$this->subjTable} SET $fld=now()
            WHERE login='$login'";
        $r = $this->dbc->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn setTimeStamp


    /**
     * Change user password
     *
     * @param string $login
     * @param string $oldpass
     * 		old password (optional for 'superuser mode')
     * @param string $pass
     * 		optional
     * @param boolean $passenc
     * 		optional, password already encrypted if true
     * @return boolean/err
     */
    public function passwd($login, $oldpass=null, $pass='', $passenc=FALSE)
    {
        if (!$passenc) {
            $cpass = md5($pass);
        } else {
            $cpass = $pass;
        }
        if (!is_null($oldpass)) {
            $oldcpass = md5($oldpass);
            $oldpCond = "AND pass='$oldcpass'";
        } else {
            $oldpCond = '';
        }
        $sql = "UPDATE {$this->subjTable} SET pass='$cpass'
            WHERE login='$login' $oldpCond AND type='U'";
        $r = $this->dbc->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn passwd


    /* --------------------------------------------------------------- groups */

    /**
     * Add {login} and direct/indirect members to {gname} and to groups,
     * where {gname} is [in]direct member
     *
     * @param string $login
     * @param string $gname
     * @return int/err
     */
    public function addSubj2Gr($login, $gname)
    {
        $uid = $this->getSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $gid = $this->getSubjId($gname);
        if (PEAR::isError($gid)) {
            return $gid;
        }
        $isgr = $this->isGroup($gid);
        if (PEAR::isError($isgr)) {
            return $isgr;
        }
        if (!$isgr) {
            return PEAR::raiseError("Subjects::addSubj2Gr: Not a group ($gname)", ALIBERR_NOTGR);
        }
        // add subject and all [in]direct members to group $gname:
        $mid = $this->_plainAddSubj2Gr($uid, $gid);
        if (PEAR::isError($mid)) {
            return $mid;
        }
        // add it to all groups where $gname is [in]direct member:
        $marr = $this->_listRMemb($gid);
        if (PEAR::isError($marr)) {
            return $marr;
        }
        foreach($marr as $k=>$v){
            $r = $this->_plainAddSubj2Gr(
                $uid, $v['gid'], intval($v['level'])+1, $v['id']);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $mid;
    } // fn addSubj2Gr


    /**
     * Remove subject from group
     *
     * @param string $login
     * @param string $gname
     * @return boolean/err
     */
    public function removeSubjFromGr($login, $gname)
    {
        $uid = $this->getSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $gid = $this->getSubjId($gname);
        if (PEAR::isError($gid)) {
            return $gid;
        }
        $sql = "SELECT id FROM {$this->smembTable}
            WHERE uid='$uid' AND gid='$gid' AND mid is null";
        $mid = $this->dbc->getOne($sql);
        if (is_null($mid)) {
            return FALSE;
        }
        if (PEAR::isError($mid)) {
            return $mid;
        }
        // remove it:
        $r = $this->_removeMemb($mid);
        if (PEAR::isError($r)) {
            return $r;
        }
        // and rebuild indirect memberships:
        $r = $this->_rebuildRels();
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn removeSubjFromGr


    /* --------------------------------------------------------- info methods */

    /**
     * Get subject id from login
     *
     * @param string $login
     * @return int/err
     */
    public function getSubjId($login)
    {
        $sql = "SELECT id FROM {$this->subjTable}
            WHERE login='$login'";
        return $this->dbc->getOne($sql);
    } // fn getSubjId


    /**
     * Get subject name (login) from id
     *
     * @param int $id
     * @param string $fld
     * @return string/err
     */
    public function getSubjName($id, $fld='login')
    {
        $sql = "SELECT $fld FROM {$this->subjTable}
            WHERE id='$id'";
        return $this->dbc->getOne($sql);
    } // fn getSubjName


    /**
     * Get all subjects
     *
     * @param string $flds
     * @return array/err
     */
    public function getSubjects($flds='id, login')
    {
        $sql = "SELECT $flds FROM {$this->subjTable}";
        return $this->dbc->getAll($sql);
    } // fn getSubjects


    /**
     * Get subjects with count of direct members
     *
     * @return array/err
     */
    public function getSubjectsWCnt()
    {
        $sql = "
            SELECT count(m.uid)as cnt, s.id, s.login, s.type
            FROM {$this->subjTable} s
            LEFT JOIN {$this->smembTable} m ON m.gid=s.id
            WHERE m.mid is null
            GROUP BY s.id, s.login, s.type
            ORDER BY s.id";
        return $this->dbc->getAll($sql);
    } // fn getSubjectsWCnt


    /**
     * Return true if subject is a group
     *
     * @param int $gid
     * @return boolean/err
     */
    public function isGroup($gid)
    {
        $sql = "SELECT type FROM {$this->subjTable}
            WHERE id='$gid'";
        $r = $this->dbc->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return ($r === 'G');
    } // fn isGroup


    /**
     * List direct members of group
     *
     * @param int $gid
     * @return array/err
     */
    public function listGroup($gid)
    {
        $sql = "SELECT s.id, s.login, s.type
            FROM {$this->smembTable} m, {$this->subjTable} s
            WHERE m.uid=s.id AND m.mid is null AND m.gid='$gid'";
        return $this->dbc->getAll($sql);
    } // fn listGroup


    /**
     * Return true if uid is [id]direct member of gid
     *
     * @param int $uid
     * 		local user id
     * @param int $gid
     * 		local group id
     * @return boolean
     */
    public function isMemberOf($uid, $gid)
    {
        $sql = "
            SELECT count(*)as cnt
            FROM {$this->smembTable}
            WHERE uid='$uid' AND gid='$gid'
        ";
        $res = $this->dbc->getOne($sql);
        if (PEAR::isError($res)) {
            return $res;
        }
        return (intval($res) > 0);
    } // fn isMemberOf


    /* ==================================================== "private" methods */

    /**
     * Create membership record
     *
     * @param int $uid
     * @param int $gid
     * @param int $level
     * @param int $mid
     * @return int/err
     */
    private function _addMemb($uid, $gid, $level=0, $mid='null')
    {
        if($uid == $gid)  {
            return PEAR::raiseError("Subjects::_addMemb: uid==gid ($uid)", ALIBERR_BADSMEMB);
        }
        $sql = "SELECT id, level, mid FROM {$this->smembTable}
            WHERE uid='$uid' AND gid='$gid' ORDER BY level ASC";
        $a = $this->dbc->getAll($sql);
        if (PEAR::isError($a)) {
            return $a;
        }
        if (count($a) > 0) {
            $a0 = $a[0];
            $id = $a0['id'];
            if ($level < intval($a0['level'])){
                $sql2 = "UPDATE {$this->smembTable}
                    SET level='$level', mid=$mid WHERE id='{$a0['id']}'";
                $r = $this->dbc->query($sql2);
                if (PEAR::isError($r)) {
                    return $r;
                }
            }
        } else {
            $id = $this->dbc->nextId("{$this->smembTable}_id_seq");
            if (PEAR::isError($id)) {
                return $id;
            }
            $sql3 = "
                INSERT INTO {$this->smembTable} (id, uid, gid, level, mid)
                VALUES ($id, $uid, $gid, $level, $mid)
            ";
            $r = $this->dbc->query($sql3);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $id;
    } // fn _addMemb


    /**
     * Remove membership record
     *
     * @param int $mid
     * @return null|PEAR_Error
     */
    private function _removeMemb($mid)
    {
        $sql = "DELETE FROM {$this->smembTable}
            WHERE id='$mid'";
        return $this->dbc->query($sql);
    } // fn _removeMemb


    /**
     * List [in]direct members of group
     *
     * @param int $gid
     * @param int $uid
     * @return array|PEAR_Error
     */
    private function _listMemb($gid, $uid=NULL)
    {
        $sql = "
            SELECT id, uid, level FROM {$this->smembTable}
            WHERE gid='$gid'".(is_null($uid) ? '' : " AND uid='$uid'");
        return $this->dbc->getAll($sql);
    } // fn _listMemb


    /**
     * List groups where uid is [in]direct member
     *
     * @param int $gid
     * @param int $uid
     * @return array/err
     */
    private function _listRMemb($uid, $gid=NULL)
    {
        $sql = "
            SELECT id, gid, level FROM {$this->smembTable}
            WHERE uid='$uid'".(is_null($gid) ? '' : " AND gid='$gid'");
        return $this->dbc->getAll($sql);
    } // fn listRMemb


    /**
     * Add uid and its [in]direct members to gid
     *
     * @param int $uid
     * @param int $gid
     * @param int $level
     * @param int $rmid
     * @return int|PEAR_Error
     */
    private function _plainAddSubj2Gr($uid, $gid, $level=0, $rmid='null')
    {
        $mid = $this->_addMemb($uid, $gid, $level, $rmid);
        if (PEAR::isError($mid)) {
            return $mid;
        }
        $marr = $this->_listMemb($uid);
        if (PEAR::isError($marr)) {
            return $marr;
        }
        foreach ($marr as $k => $v) {
            $r = $this->_addMemb(
                $v['uid'], $gid, intval($v['level'])+$level+1, $mid
            );
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $mid;
    } // fn _plainAddSubj2Gr


    /**
     * Rebuild indirect membership records<br>
     * it's probably more complicated to do removing without rebuild ...
     *
     * @return true|PEAR_Error
     */
    private function _rebuildRels()
    {
        $this->dbc->query("BEGIN");
        $r = $this->dbc->query("LOCK TABLE {$this->smembTable}");
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = $this->dbc->query("DELETE FROM {$this->smembTable}
            WHERE mid is not null");
        if (PEAR::isError($r)) {
            return $r;
        }
        $arr = $this->dbc->getAll("SELECT uid, gid FROM {$this->smembTable}");
                            //  WHERE mid is null
        if (PEAR::isError($arr)) {
            return $arr;
        }
        foreach ($arr as $it) {
            $marr = $this->_listRMemb($it['gid']);
            if (PEAR::isError($marr)) {
                return $marr;
            }
            foreach ($marr as $k => $v) {
                $r = $this->_plainAddSubj2Gr(
                    $it['uid'], $v['gid'], intval($v['level'])+1, $v['id']
                );
                if (PEAR::isError($r)) {
                    return $r;
                }
            }
        }
        $r = $this->dbc->query("COMMIT");   if(PEAR::isError($r)) return $r;
        return TRUE;
    } // fn _rebuildRels


    /* =============================================== test and debug methods */

    /**
     * Dump subjects for debug
     *
     * @param string $indstr
     * 		indentation string
     * @param string $ind
     * 		actual indentation
     * @return string
     */
    public function dumpSubjects($indstr='    ', $ind='')
    {
        $r = $ind.join(', ', array_map(
            create_function('$v', 'return "{$v[\'login\']}({$v[\'cnt\']})";'),
            $this->getSubjectsWCnt()
        ))."\n";
        return $r;
    } // fn dumpSubjects


    /**
     * Delete all subjects and membership records
     *
     * @return void
     */
    public function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->subjTable}");
        $this->dbc->query("DELETE FROM {$this->smembTable}");
        parent::deleteData();
    } // fn deleteData


    /**
     * Insert test data
     *
     * @return array
     */
    public function testData()
    {
        parent::testData();
        $o['root'] = $this->addSubj('root', 'q');
        $o['test1'] = $this->addSubj('test1', 'a');
        $o['test2'] = $this->addSubj('test2', 'a');
        $o['test3'] = $this->addSubj('test3', 'a');
        $o['test4'] = $this->addSubj('test4', 'a');
        $o['test5'] = $this->addSubj('test5', 'a');
        $o['gr1'] = $this->addSubj('gr1');
        $o['gr2'] = $this->addSubj('gr2');
        $o['gr3'] = $this->addSubj('gr3');
        $o['gr4'] = $this->addSubj('gr4');
        $this->addSubj2Gr('test1', 'gr1');
        $this->addSubj2Gr('test2', 'gr2');
        $this->addSubj2Gr('test3', 'gr3');
        $this->addSubj2Gr('test4', 'gr4');
        $this->addSubj2Gr('test5', 'gr1');
        $this->addSubj2Gr('gr4', 'gr3');
        $this->addSubj2Gr('gr3', 'gr2');
        return $this->tdata['subjects'] = $o;
    } // fn testData


    /**
     * Make basic test
     *
     */
    public function test()
    {
        if (PEAR::isError($p = parent::test())) {
            return $p;
        }
        $this->deleteData();
        $this->testData();
        $this->test_correct = "root(0), test1(0), test2(0), test3(0),".
            " test4(0), test5(0), gr1(2), gr2(2), gr3(2), gr4(1)\n";
        $this->test_dump = $this->dumpSubjects();
        $this->removeSubj('test1');
        $this->removeSubj('test3');
        $this->removeSubjFromGr('test5', 'gr1');
        $this->removeSubjFromGr('gr3', 'gr2');
        $this->test_correct .= "root(0), test2(0), test4(0), test5(0),".
            " gr1(0), gr2(1), gr3(1), gr4(1)\n";
        $this->test_dump .= $this->dumpSubjects();
        $this->deleteData();
        if ($this->test_dump == $this->test_correct) {
            $this->test_log.="subj: OK\n";
            return TRUE;
        } else {
            return PEAR::raiseError(
            'Subjects::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\n".
            "dump:\n{$this->test_dump}\n</pre>\n");
        }
    } // fn test


    /**
     * Create tables + initialize
     *
     */
    public function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->subjTable} (
            id int not null PRIMARY KEY,
            login varchar(255) not null default'',
            pass varchar(255) not null default'',
            type char(1) not null default 'U',
            realname varchar(255) not null default'',
            lastlogin timestamp,
            lastfail timestamp
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->subjTable}_id_idx
            ON {$this->subjTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->subjTable}_login_idx
            ON {$this->subjTable} (login)");
        $this->dbc->createSequence("{$this->subjTable}_id_seq");

        $this->dbc->query("CREATE TABLE {$this->smembTable} (
            id int not null PRIMARY KEY,
            uid int not null default 0,
            gid int not null default 0,
            level int not null default 0,
            mid int
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->smembTable}_id_idx
            ON {$this->smembTable} (id)");
        $this->dbc->createSequence("{$this->smembTable}_id_seq");
    } // fn install


    /**
     * Drop tables etc.
     *
     * @return void
     */
    public function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->subjTable}");
        $this->dbc->dropSequence("{$this->subjTable}_id_seq");
        $this->dbc->query("DROP TABLE {$this->smembTable}");
        $this->dbc->dropSequence("{$this->smembTable}_id_seq");
        parent::uninstall();
    } // fn uninstall

} // class Subjects
?>