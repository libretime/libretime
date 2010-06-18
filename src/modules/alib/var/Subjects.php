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
class Subjects {
//class Subjects extends ObjClasses {

    /* ======================================================= public methods */

    /**
     * Add new subject (a.k.a. "user")
     *
     * @param string $p_login
     * @param string $p_pass
     * @param string $p_realname
     * @param boolean $p_passenc
     * 		password already encrypted if true
     * @return int|PEAR_Error
     */
    public static function AddSubj($p_login, $p_pass=NULL, $p_realname='', $p_passenc=FALSE)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!$p_login) {
            return $CC_DBC->raiseError("Subjects::AddSubj: empty login");
        }
        $id = $CC_DBC->nextId($CC_CONFIG['subjTable']."_id_seq");
        if (PEAR::isError($id)) {
            return $id;
        }
        if (!is_null($p_pass) && !$p_passenc) {
            $p_pass = md5($p_pass);
        }
        $sql = "INSERT INTO ".$CC_CONFIG['subjTable']." (id, login, pass, type, realname)"
            ." VALUES ($id, '$p_login', ".
                (is_null($p_pass) ? "'!', 'G'" : "'$p_pass', 'U'").",
                '$p_realname')";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $id;
    }


    /**
     * Remove subject by uid or by login
     *
     * @param string $login
     * @param int $uid
     * @return boolean|PEAR_Error
     */
    public static function RemoveSubj($login, $uid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (is_null($uid)) {
            $uid = Subjects::GetSubjId($login);
        }
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['smembTable']
            ." WHERE (uid='$uid' OR gid='$uid') AND mid is null";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        $sql2 = "DELETE FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login'";
        $r = $CC_DBC->query($sql2);
        if (PEAR::isError($r)) {
            return $r;
        }
        return Subjects::_rebuildRels();
    } // fn removeSubj


    /**
     * Check login and password
     *
     * @param string $login
     * @param string $pass
     * 		optional
     * @return boolean|int|PEAR_Error
     */
    public static function Authenticate($login, $pass='')
    {
        global $CC_CONFIG, $CC_DBC;
        $cpass = md5($pass);
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login' AND pass='$cpass' AND type='U'";
        $id = $CC_DBC->getOne($sql);
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
     * @return boolean|int|PEAR_Error
     */
    public static function SetTimeStamp($login, $failed=FALSE)
    {
        global $CC_CONFIG, $CC_DBC;
        $fld = ($failed ? 'lastfail' : 'lastlogin');
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET $fld=now()"
            ." WHERE login='$login'";
        $r = $CC_DBC->query($sql);
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
     * @return boolean|PEAR_Error
     */
    public static function Passwd($login, $oldpass=null, $pass='', $passenc=FALSE)
    {
        global $CC_CONFIG, $CC_DBC;
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
        $sql = "UPDATE ".$CC_CONFIG['subjTable']." SET pass='$cpass'"
            ." WHERE login='$login' $oldpCond AND type='U'";
        $r = $CC_DBC->query($sql);
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
     * @return int|PEAR_Error
     */
    public static function AddSubjectToGroup($login, $gname)
    {
        $uid = Subjects::GetSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $gid = Subjects::GetSubjId($gname);
        if (PEAR::isError($gid)) {
            return $gid;
        }
        $isgr = Subjects::IsGroup($gid);
        if (PEAR::isError($isgr)) {
            return $isgr;
        }
        if (!$isgr) {
            return PEAR::raiseError("Subjects::addSubj2Gr: Not a group ($gname)", ALIBERR_NOTGR);
        }
        // add subject and all [in]direct members to group $gname:
        $mid = Subjects::_plainAddSubjectToGroup($uid, $gid);
        if (PEAR::isError($mid)) {
            return $mid;
        }
        // add it to all groups where $gname is [in]direct member:
        $marr = Subjects::_listRMemb($gid);
        if (PEAR::isError($marr)) {
            return $marr;
        }
        foreach ($marr as $k => $v) {
            $r = Subjects::_plainAddSubjectToGroup(
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
     * @return boolean|PEAR_Error
     */
    public static function RemoveSubjectFromGroup($login, $gname)
    {
        global $CC_CONFIG, $CC_DBC;
        $uid = Subjects::GetSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        $gid = Subjects::GetSubjId($gname);
        if (PEAR::isError($gid)) {
            return $gid;
        }
        $sql = "SELECT id FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid' AND gid='$gid' AND mid is null";
        $mid = $CC_DBC->getOne($sql);
        if (is_null($mid)) {
            return FALSE;
        }
        if (PEAR::isError($mid)) {
            return $mid;
        }
        // remove it:
        $r = Subjects::_removeMemb($mid);
        if (PEAR::isError($r)) {
            return $r;
        }
        // and rebuild indirect memberships:
        $r = Subjects::_rebuildRels();
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
     * @return int|PEAR_Error
     */
    public static function GetSubjId($login)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $sql = "SELECT id FROM ".$CC_CONFIG['subjTable']
            ." WHERE login='$login'";
        return $CC_DBC->getOne($sql);
    } // fn getSubjId


    /**
     * Get subject name (login) from id
     *
     * @param int $id
     * @param string $fld
     * @return string|PEAR_Error
     */
    public static function GetSubjName($id, $fld='login')
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $sql = "SELECT $fld FROM ".$CC_CONFIG['subjTable']
            ." WHERE id='$id'";
        return $CC_DBC->getOne($sql);
    } // fn getSubjName


    /**
     * Get one subject from the table.
     *
     * @param string $p_fieldValue
     * @param string $p_fieldName
     * @return array
     */
    public static function GetSubject($p_fieldValue, $p_fieldName='login')
    {
        global $CC_CONFIG, $CC_DBC;
        if (!in_array($p_fieldName, array("login", "id"))) {
            return null;
        }
        $escapedValue = pg_escape_string($p_fieldValue);
        $sql = "SELECT * FROM ".$CC_CONFIG['subjTable']
            ." WHERE $p_fieldName='$escapedValue'";
        $row = $CC_DBC->GetRow($sql);
        return $row;
    }


    /**
     * Get all subjects
     *
     * @param string $flds
     * @return array|PEAR_Error
     */
    public static function GetSubjects($flds='id, login')
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT $flds FROM ".$CC_CONFIG['subjTable'];
        return $CC_DBC->getAll($sql);
    } // fn getSubjects


    /**
     * Get subjects with count of direct members
     *
     * @return array|PEAR_Error
     */
    public static function GetSubjectsWCnt()
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(m.uid)as cnt, s.id, s.login, s.type"
            ." FROM ".$CC_CONFIG['subjTable']." s"
            ." LEFT JOIN ".$CC_CONFIG['smembTable']." m ON m.gid=s.id"
            ." WHERE m.mid is null"
            ." GROUP BY s.id, s.login, s.type"
            ." ORDER BY s.id";
        return $CC_DBC->getAll($sql);
    } // fn getSubjectsWCnt


    /**
     * Return true if subject is a group
     *
     * @param int $gid
     * @return boolean|PEAR_Error
     */
    public static function IsGroup($gid)
    {
        global $CC_CONFIG, $CC_DBC;
        if (empty($gid)) {
            return FALSE;
        }
        $sql = "SELECT type FROM ".$CC_CONFIG['subjTable']
            ." WHERE id='$gid'";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return ($r === 'G');
    } // fn isGroup


    /**
     * List direct members of group
     *
     * @param int $gid
     * @return array|PEAR_Error
     */
    public static function ListGroup($gid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT s.id, s.login, s.type"
            ." FROM ".$CC_CONFIG['smembTable']." m, ".$CC_CONFIG['subjTable']." s"
            ." WHERE m.uid=s.id AND m.mid is null AND m.gid='$gid'";
        return $CC_DBC->getAll($sql);
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
    public static function IsMemberOf($uid, $gid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*)as cnt"
            ." FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid' AND gid='$gid'";
        $res = $CC_DBC->getOne($sql);
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
     * @return int|PEAR_Error
     */
    private static function _addMemb($uid, $gid, $level=0, $mid='null')
    {
        global $CC_CONFIG, $CC_DBC;
        if ($uid == $gid) {
            return PEAR::raiseError("Subjects::_addMemb: uid==gid ($uid)", ALIBERR_BADSMEMB);
        }
        $sql = "SELECT id, level, mid FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid' AND gid='$gid' ORDER BY level ASC";
        $a = $CC_DBC->getAll($sql);
        if (PEAR::isError($a)) {
            return $a;
        }
        if (count($a) > 0) {
            $a0 = $a[0];
            $id = $a0['id'];
            if ($level < intval($a0['level'])){
                $sql2 = "UPDATE ".$CC_CONFIG['smembTable']
                    ." SET level='$level', mid=$mid WHERE id='{$a0['id']}'";
                $r = $CC_DBC->query($sql2);
                if (PEAR::isError($r)) {
                    return $r;
                }
            }
        } else {
            $id = $CC_DBC->nextId($CC_CONFIG['smembTable']."_id_seq");
            if (PEAR::isError($id)) {
                return $id;
            }
            $sql3 = "INSERT INTO ".$CC_CONFIG['smembTable']." (id, uid, gid, level, mid)"
                ." VALUES ($id, $uid, $gid, $level, $mid)";
            $r = $CC_DBC->query($sql3);
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
    private static function _removeMemb($mid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "DELETE FROM ".$CC_CONFIG['smembTable']
            ." WHERE id='$mid'";
        return $CC_DBC->query($sql);
    } // fn _removeMemb


    /**
     * List [in]direct members of group
     *
     * @param int $gid
     * @param int $uid
     * @return array|PEAR_Error
     */
    private static function _listMemb($gid, $uid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT id, uid, level FROM ".$CC_CONFIG['smembTable']
            ." WHERE gid='$gid'".(is_null($uid) ? '' : " AND uid='$uid'");
        return $CC_DBC->getAll($sql);
    } // fn _listMemb


    /**
     * List groups where uid is [in]direct member
     *
     * @param int $gid
     * @param int $uid
     * @return array|PEAR_Error
     */
    private static function _listRMemb($uid, $gid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT id, gid, level FROM ".$CC_CONFIG['smembTable']
            ." WHERE uid='$uid'".(is_null($gid) ? '' : " AND gid='$gid'");
        return $CC_DBC->getAll($sql);
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
    private static function _plainAddSubjectToGroup($uid, $gid, $level=0, $rmid='null')
    {
        $mid = Subjects::_addMemb($uid, $gid, $level, $rmid);
        if (PEAR::isError($mid)) {
            return $mid;
        }
        $marr = Subjects::_listMemb($uid);
        if (PEAR::isError($marr)) {
            return $marr;
        }
        foreach ($marr as $k => $v) {
            $r = Subjects::_addMemb(
                $v['uid'], $gid, intval($v['level'])+$level+1, $mid
            );
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $mid;
    }


    /**
     * Rebuild indirect membership records<br>
     * it's probably more complicated to do removing without rebuild ...
     *
     * @return true|PEAR_Error
     */
    private static function _rebuildRels()
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("BEGIN");
        $r = $CC_DBC->query("LOCK TABLE ".$CC_CONFIG['smembTable']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['smembTable']
            ." WHERE mid is not null";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        $arr = $CC_DBC->getAll("SELECT uid, gid FROM ".$CC_CONFIG['smembTable']);
                            //  WHERE mid is null
        if (PEAR::isError($arr)) {
            return $arr;
        }
        foreach ($arr as $it) {
            $marr = Subjects::_listRMemb($it['gid']);
            if (PEAR::isError($marr)) {
                return $marr;
            }
            foreach ($marr as $k => $v) {
                $r = Subjects::_plainAddSubjectToGroup(
                    $it['uid'], $v['gid'], intval($v['level'])+1, $v['id']
                );
                if (PEAR::isError($r)) {
                    return $r;
                }
            }
        }
        $r = $CC_DBC->query("COMMIT");
        if (PEAR::isError($r)) {
            return $r;
        }
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
    public static function DumpSubjects($indstr='    ', $ind='')
    {
        $r = $ind.join(', ', array_map(
            create_function('$v', 'return "{$v[\'login\']}({$v[\'cnt\']})";'),
            Subjects::GetSubjectsWCnt()
        ))."\n";
        return $r;
    } // fn dumpSubjects


    /**
     * Delete all subjects and membership records
     *
     * @return void
     */
    public static function DeleteData()
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['subjTable']);
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['smembTable']);
        ObjClasses::DeleteData();
    } // fn deleteData


    /**
     * Insert test data
     *
     * @return array
     */
    public function TestData()
    {
        $tdata = ObjClasses::TestData();
        $o['root'] = Subjects::AddSubj('root', 'q');
        $o['test1'] = Subjects::AddSubj('test1', 'a');
        $o['test2'] = Subjects::AddSubj('test2', 'a');
        $o['test3'] = Subjects::AddSubj('test3', 'a');
        $o['test4'] = Subjects::AddSubj('test4', 'a');
        $o['test5'] = Subjects::AddSubj('test5', 'a');
        $o['gr1'] = Subjects::AddSubj('gr1');
        $o['gr2'] = Subjects::AddSubj('gr2');
        $o['gr3'] = Subjects::AddSubj('gr3');
        $o['gr4'] = Subjects::AddSubj('gr4');
        Subjects::AddSubjectToGroup('test1', 'gr1');
        Subjects::AddSubjectToGroup('test2', 'gr2');
        Subjects::AddSubjectToGroup('test3', 'gr3');
        Subjects::AddSubjectToGroup('test4', 'gr4');
        Subjects::AddSubjectToGroup('test5', 'gr1');
        Subjects::AddSubjectToGroup('gr4', 'gr3');
        Subjects::AddSubjectToGroup('gr3', 'gr2');
        $tdata['subjects'] = $o;
        return $tdata;
    } // fn TestData


    /**
     * Make basic test
     *
     */
    public static function Test()
    {
        $p = ObjClasses::Test();
        if (PEAR::isError($p)) {
            return $p;
        }
        Subjects::DeleteData();
        Subjects::TestData();
        $test_correct = "root(0), test1(0), test2(0), test3(0),".
            " test4(0), test5(0), gr1(2), gr2(2), gr3(2), gr4(1)\n";
        $test_dump = Subjects::DumpSubjects();
        Subjects::RemoveSubj('test1');
        Subjects::RemoveSubj('test3');
        Subjects::RemoveSubjectFromGroup('test5', 'gr1');
        Subjects::RemoveSubjectFromGroup('gr3', 'gr2');
        $test_correct .= "root(0), test2(0), test4(0), test5(0),".
            " gr1(0), gr2(1), gr3(1), gr4(1)\n";
        $test_dump .= Subjects::DumpSubjects();
        Subjects::DeleteData();
        if ($test_dump == $test_correct) {
            $test_log .= "subj: OK\n";
            return TRUE;
        } else {
            return PEAR::raiseError(
                'Subjects::test:', 1, PEAR_ERROR_DIE, '%s'.
                "<pre>\ncorrect:\n{$test_correct}\n".
                "dump:\n{$test_dump}\n</pre>\n");
        }
    } // fn test

} // class Subjects
?>