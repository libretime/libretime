<?php
require_once('Subjects.php');

define('USE_ALIB_CLASSES', TRUE);
define('ALIBERR_NOTLOGGED', 30);
define('ALIBERR_NOTEXISTS', 31);

/**
 * Authentication/authorization class
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version $Revision$
 * @package Campcaster
 * @subpackage Alib
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 */
class Alib {
    /* ======================================================= public methods */

    /* ----------------------------------------------- session/authentication */

    /**
     * Authenticate and create session
     *
     * @param string $login
     * @param string $pass
     * @return boolean|sessionId|PEAR_Error
     */
    public static function Login($login, $pass)
    {
        global $CC_CONFIG, $CC_DBC;
        if (FALSE === Subjects::Authenticate($login, $pass)) {
            Subjects::SetTimeStamp($login, TRUE);
            return FALSE;
        }
        $sessid = Alib::_createSessid();
        if (PEAR::isError($sessid)) {
            return $sessid;
        }
        $userid = Subjects::GetSubjId($login);
        $sql = "INSERT INTO ".$CC_CONFIG['sessTable']." (sessid, userid, login, ts)"
            ." VALUES('$sessid', '$userid', '$login', now())";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        Subjects::SetTimeStamp($login, FALSE);
        return $sessid;
    } // fn login


    /**
     * Logout and destroy session
     *
     * @param string $sessid
     * @return true|PEAR_Error
     */
    public static function Logout($sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $ct = Alib::CheckAuthToken($sessid);
        if ($ct === FALSE) {
            return PEAR::raiseError("Alib::logout: not logged ($sessid)",
                ALIBERR_NOTLOGGED, PEAR_ERROR_RETURN);
        } elseif (PEAR::isError($ct)) {
            return $ct;
        } else {
            $sql = "DELETE FROM ".$CC_CONFIG['sessTable']
                ." WHERE sessid='$sessid'";
            $r = $CC_DBC->query($sql);
            if (PEAR::isError($r)) {
                return $r;
            }
            return TRUE;
        }
    } // fn logout


    /**
     * Return true if the token is valid
     *
     * @param string $sessid
     * @return boolean|PEAR_Error
     */
    private static function CheckAuthToken($sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*) as cnt FROM ".$CC_CONFIG['sessTable']
            ." WHERE sessid='$sessid'";
        $c = $CC_DBC->getOne($sql);
        return ($c == 1 ? TRUE : (PEAR::isError($c) ? $c : FALSE ));
    } //fn checkAuthToken


    /**
     * Set valid token in alib object
     *
     * @param string $sessid
     * @return TRUE|PEAR_Error
     */
//    public function setAuthToken($sessid)
//    {
//        $r = $this->checkAuthToken($sessid);
//        if (PEAR::isError($r)) {
//        	return $r;
//        }
//        if (!$r) {
//            return PEAR::raiseError("ALib::setAuthToken: invalid token ($sessid)");
//        }
//        //$this->sessid = $sessid;
//        return TRUE;
//    } // fn setAuthToken


    /* -------------------------------------------------------- authorization */
    /**
     * Insert permission record
     *
     * @param int $sid
     * 		local user/group id
     * @param string $action
     * @param int $oid
     * 		local object id
     * @param string $type
     * 		'A'|'D' (allow/deny)
     * @return int
     * 		local permission id
     */
    public static function AddPerm($sid, $action, $oid, $type='A')
    {
        global $CC_CONFIG, $CC_DBC;
        $permid = $CC_DBC->nextId($CC_CONFIG['permTable']."_id_seq");
        $sql = "INSERT INTO ".$CC_CONFIG['permTable']." (permid, subj, action, obj, type)"
            ." VALUES ($permid, $sid, '$action', $oid, '$type')";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return($r);
        }
        return $permid;
    } // fn addPerm


    /**
     * Remove permission record
     *
     * @param int $permid
     * 		local permission id
     * @param int $subj
     * 		local user/group id
     * @param int $obj
     * 		local object id
     * @return boolean|PEAR_Error
     */
    public static function RemovePerm($permid=NULL, $subj=NULL, $obj=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $ca = array();
        if ($permid) {
            $ca[] = "permid=$permid";
        }
        if ($subj) {
            $ca[] = "subj=$subj";
        }
        if ($obj) {
            $ca[] = "obj=$obj";
        }
        $cond = join(" AND ", $ca);
        if (!$cond) {
            return TRUE;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['permTable']." WHERE $cond";
        return $CC_DBC->query($sql);
    } // fn removePerm


    /**
     * Return object related with permission record
     *
     * @param int $permid
     * 		local permission id
     * @return int
     * 		local object id
     */
    public static function GetPermOid($permid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT obj FROM ".$CC_CONFIG['permTable']." WHERE permid=$permid";
        $res = $CC_DBC->getOne($sql);
        return $res;
    } // fn GetPermOid


    /**
     * Check if specified subject have permission to specified action
     * on specified object
     *
     * Look for sequence of corresponding permissions and order it by
     * relevence, then test the most relevant for result.
     * High relevence have direct permission (directly for specified subject
     * and object. Relevance order is done by level distance in the object
     * tree, level distance in subjects (user/group system).
     * Similar way is used for permissions related to object classes.
     * But class-related permissions have lower priority then
     * object-tree-related.
     * Support for object classes can be disabled by USE_ALIB_CLASSES const.
     *
     * @param int $sid
     * 		subject id (user or group id)
     * @param string $action
     * 		from set defined in config
     * @param int $oid
     * 		object id (default: root node)
     * @return boolean|PEAR_Error
     */
    public static function CheckPerm($sid, $action, $oid=NULL)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        if (!is_numeric($sid)) {
            return FALSE;
        }
        if (is_null($oid) or $oid=='') {
            $oid = M2tree::GetRootNode();
        }
        if (PEAR::isError($oid)) {
            return $oid;
        }
        if (!is_numeric($oid)) {
            return FALSE;
        }
        // query construction
        //      shortcuts:
        //          p: permTable,
        //          s: subjTable, m smembTable,
        //          t: treeTable ts: structTable,
        //          c: classTable, cm: cmembTable
        // main query elements:
        $q_flds = "m.level , p.subj, s.login, action, p.type, p.obj";
        $q_from = $CC_CONFIG['permTable']." p ";
        // joins for solving users/groups:
        $q_join = "LEFT JOIN ".$CC_CONFIG['subjTable']." s ON s.id=p.subj ";
        $q_join .= "LEFT JOIN ".$CC_CONFIG['smembTable']." m ON m.gid=p.subj ";
        $q_cond = "p.action in('_all', '$action') AND
            (s.id=$sid OR m.uid=$sid) ";
        // coalesce -1 for higher priority of nongroup rows:
        // action DESC order for lower priority of '_all':
        $q_ordb = "ORDER BY coalesce(m.level,-1), action DESC, p.type DESC";
        $q_flds0 = $q_flds;
        $q_from0 = $q_from;
        $q_join0 = $q_join;
        $q_cond0 = $q_cond;
        $q_ordb0 = $q_ordb;
        //  joins for solving object tree:
        $q_flds .= ", t.name, ts.level as tlevel";
        $q_join .= "LEFT JOIN ".$CC_CONFIG['treeTable']." t ON t.id=p.obj ";
        $q_join .= "LEFT JOIN ".$CC_CONFIG['structTable']." ts ON ts.parid=p.obj ";
        $q_cond .= " AND (t.id=$oid OR ts.objid=$oid)";
        // action DESC order is hack for lower priority of '_all':
        $q_ordb = "ORDER BY coalesce(ts.level,0), m.level, action DESC, p.type DESC";
        // query by tree:
        $query1 = "SELECT $q_flds FROM $q_from $q_join WHERE $q_cond $q_ordb";
        $r1 = $CC_DBC->getAll($query1);
        if (PEAR::isError($r1)) {
            return($r1);
        }
        //  if there is row with type='A' on the top => permit
        $AllowedByTree =
            (is_array($r1) && count($r1)>0 && $r1[0]['type']=='A');
        $DeniedByTree =
            (is_array($r1) && count($r1)>0 && $r1[0]['type']=='D');

        if (!USE_ALIB_CLASSES) {
            return $AllowedbyTree;
        }

        // joins for solving object classes:
        $q_flds = $q_flds0.", c.cname ";
        $q_join = $q_join0."LEFT JOIN ".$CC_CONFIG['classTable']." c ON c.id=p.obj ";
        $q_join .= "LEFT JOIN ".$CC_CONFIG['cmembTable']." cm ON cm.cid=p.obj ";
        $q_cond = $q_cond0." AND (c.id=$oid OR cm.objid=$oid)";
        $q_ordb = $q_ordb0;
        // query by class:
        $query2 = "SELECT $q_flds FROM $q_from $q_join WHERE $q_cond $q_ordb";
        $r2 = $CC_DBC->getAll($query2);
        if (PEAR::isError($r2)) {
            return $r2;
        }
        $AllowedByClass =
            (is_array($r2) && count($r2)>0 && $r2[0]['type']=='A');
        // not used now:
        // $DeniedByClass =
        //    (is_array($r2) && count($r2)>0 && $r2[0]['type']=='D');
        $res = ($AllowedByTree || (!$DeniedByTree && $AllowedByClass));
        return $res;
    } // fn CheckPerm


    /* ---------------------------------------------------------- object tree */

    /**
     * Remove all permissions on object and then remove object itself
     *
     * @param int $id
     * @return void|PEAR_Error
     */
    public static function RemoveObj($id)
    {
        $r = Alib::RemovePerm(NULL, NULL, $id);
        if (PEAR::isError($r)) {
            return $r;
        }
        return ObjClasses::RemoveObj($id);
    } // fn removeObj

    /* --------------------------------------------------------- users/groups */

    /**
     * Remove all permissions of subject and then remove subject itself
     *
     * @param string $login
     * @return void|PEAR_Error
     */
    public static function RemoveSubj($login)
    {
        global $CC_CONFIG, $CC_DBC;
        $uid = Subjects::GetSubjId($login);
        if (PEAR::isError($uid)) {
            return $uid;
        }
        if (is_null($uid)){
            return $CC_DBC->raiseError("Alib::removeSubj: Subj not found ($login)",
                ALIBERR_NOTEXISTS,  PEAR_ERROR_RETURN);
        }
        $r = Alib::RemovePerm(NULL, $uid);
        if (PEAR::isError($r)) {
            return $r;
        }
        return Subjects::RemoveSubj($login, $uid);
    } // fn RemoveSubj


    /* ------------------------------------------------------------- sessions */
    /**
     * Get login from session id (token)
     *
     * @param string $sessid
     * @return string|PEAR_Error
     */
    public static function GetSessLogin($sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT login FROM ".$CC_CONFIG['sessTable']." WHERE sessid='$sessid'";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        if (is_null($r)){
            return PEAR::raiseError("Alib::GetSessLogin:".
                " invalid session id ($sessid)",
                ALIBERR_NOTEXISTS,  PEAR_ERROR_RETURN);
        }
        return $r;
    } // fn GetSessLogin


    /**
     * Get user id from session id.
     *
     * @param string $p_sessid
     * @return int|PEAR_Error
     */
    public static function GetSessUserId($p_sessid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT userid FROM ".$CC_CONFIG['sessTable']." WHERE sessid='$p_sessid'";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        if (is_null($r)) {
            return PEAR::raiseError("Alib::getSessUserId:".
                " invalid session id ($p_sessid)",
                ALIBERR_NOTEXISTS,  PEAR_ERROR_RETURN);
        }
        return $r;
    } // fn getSessUserId


    /* --------------------------------------------------------- info methods */
    /**
     * Get all permissions on object.
     *
     * @param int $id
     * @return array|null|PEAR_Error
     */
    public static function GetObjPerms($id)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT s.login, p.* FROM ".$CC_CONFIG['permTable']." p, ".$CC_CONFIG['subjTable']." s"
            ." WHERE s.id=p.subj AND p.obj=$id";
        return $CC_DBC->getAll($sql);
    } // fn GetObjPerms


    /**
     * Get all permissions of subject.
     *
     * @param int $sid
     * @return array
     */
    public static function GetSubjPerms($sid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT t.name, t.type as otype , p.*"
            ." FROM ".$CC_CONFIG['permTable']." p, ".$CC_CONFIG['treeTable']." t"
            ." WHERE t.id=p.obj AND p.subj=$sid";
        $a1 = $CC_DBC->getAll($sql);
        if (PEAR::isError($a1)) {
            return $a1;
        }
        $sql2 = "SELECT c.cname as name, 'C'as otype, p.*"
            ." FROM ".$CC_CONFIG['permTable']." p, ".$CC_CONFIG['classTable']." c"
            ." WHERE c.id=p.obj AND p.subj=$sid";
        $a2 = $CC_DBC->getAll($sql2);
        if (PEAR::isError($a2)) {
            return $a2;
        }
        return array_merge($a1, $a2);
    } // fn GetSubjPerms


    /* ------------------------ info methods related to application structure */
    /* (this part should be added/rewritten to allow defining/modifying/using
     * application structure)
     * (only very simple structure definition - in $CC_CONFIG - supported now)
     */

    /**
     * Get all actions
     *
     * @return array
     */
    public static function GetAllActions()
    {
        global $CC_CONFIG;
        return $CC_CONFIG['allActions'];
    } // fn GetAllActions


    /**
     * Get all allowed actions on specified object type.
     *
     * @param string $type
     * @return array
     */
    public static function GetAllowedActions($type)
    {
        global $CC_CONFIG;
        return $CC_CONFIG['allowedActions'][$type];
    } // fn GetAllowedActions


    /* ====================================================== private methods */

    /**
     * Create new session id.  Return the new session ID.
     *
     * @return string
     */
    private static function _createSessid()
    {
        global $CC_CONFIG, $CC_DBC;
        for ($c = 1; $c > 0; ){
            $sessid = md5(uniqid(rand()));
            $sql = "SELECT count(*) FROM ".$CC_CONFIG['sessTable']
                ." WHERE sessid='$sessid'";
            $c = $CC_DBC->getOne($sql);
            if (PEAR::isError($c)) {
                return $c;
            }
        }
        return $sessid;
    } // fn _createSessid


    /* =============================================== test and debug methods */

    /**
     * Dump all permissions for debug
     *
     * @param string $indstr
     * 		indentation string
     * @param string $ind
     * 		actual indentation
     * @return string
     */
    public static function DumpPerms($indstr='    ', $ind='')
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT s.login, p.action, p.type"
            ." FROM ".$CC_CONFIG['permTable']." p, ".$CC_CONFIG['subjTable']." s"
            ." WHERE s.id=p.subj"
            ." ORDER BY p.permid";
        $arr = $CC_DBC->getAll($sql);
        if (PEAR::isError($arr)) {
            return $arr;
        }
        $r = $ind.join(', ', array_map(create_function('$v',
                'return "{$v[\'login\']}/{$v[\'action\']}/{$v[\'type\']}";'
            ),
            $arr
        ))."\n";
        return $r;
    } // fn dumpPerms


    /**
     * Delete everything form the permission table and session table.
     *
     * @return void
     */
    public static function DeleteData()
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['permTable']);
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['sessTable']);
        Subjects::DeleteData();
    } // fn deleteData


    /**
     * Insert test permissions
     *
     * @return array
     */
    public static function TestData()
    {
        global $CC_CONFIG, $CC_DBC;
        $tdata = Subjects::TestData();
        $t =& $tdata['tree'];
        $c =& $tdata['classes'];
        $s =& $tdata['subjects'];
        $CC_DBC->setErrorHandling(PEAR_ERROR_PRINT);
        $perms = array(
            array($s['root'], '_all', $t['root'], 'A'),
            array($s['test1'], '_all', $t['pa'], 'A'),
            array($s['test1'], 'read', $t['s2b'], 'D'),
            array($s['test2'], 'addChilds', $t['pa'], 'D'),
            array($s['test2'], 'read', $t['i2'], 'A'),
            array($s['test2'], 'edit', $t['s1a'], 'A'),
            array($s['test1'], 'addChilds', $t['s2a'], 'D'),
            array($s['test1'], 'addChilds', $t['s2c'], 'D'),
            array($s['gr2'], 'addChilds', $t['i2'], 'A'),
            array($s['test3'], '_all', $t['t1'], 'D'),
        );
        if (USE_ALIB_CLASSES){
            $perms[] = array($s['test3'], 'read', $c['cl_sa'], 'D');
            $perms[] = array($s['test4'], 'editPerms', $c['cl2'], 'A');
        }
        foreach ($perms as $p){
            $o[] = $r = Alib::AddPerm($p[0], $p[1], $p[2], $p[3]);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        $tdata['perms'] = $o;
        return $tdata;
    } // fn testData


    /**
     * Make basic test
     *
     * @return boolean|PEAR_Error
     */
    public static function Test()
    {
        $p = Subjects::test();
        if (PEAR::isError($p)) {
            return $p;
        }
        Alib::DeleteData();
        $tdata = Alib::TestData();
        if (PEAR::isError($tdata)) {
            return $tdata;
        }
        $test_correct = "root/_all/A, test1/_all/A, test1/read/D,".
            " test2/addChilds/D, test2/read/A, test2/edit/A,".
            " test1/addChilds/D, test1/addChilds/D, gr2/addChilds/A,".
            " test3/_all/D";
        if (USE_ALIB_CLASSES){
            $test_correct .= ", test3/read/D, test4/editPerms/A";
        }
        $test_correct .= "\nno, yes\n";
        $r = Alib::DumpPerms();
        if (PEAR::isError($r)) {
            return $r;
        }
        $test_dump = $r.
            (Alib::CheckPerm(
                $tdata['subjects']['test1'], 'read',
                $tdata['tree']['t1']
            )? 'yes':'no').", ".
            (Alib::CheckPerm(
                $tdata['subjects']['test1'], 'addChilds',
                $tdata['tree']['i2']
            )? 'yes':'no')."\n"
        ;
        Alib::RemovePerm($tdata['perms'][1]);
        Alib::RemovePerm($tdata['perms'][3]);
        $test_correct .= "root/_all/A, test1/read/D,".
            " test2/read/A, test2/edit/A,".
            " test1/addChilds/D, test1/addChilds/D, gr2/addChilds/A,".
            " test3/_all/D";
        if (USE_ALIB_CLASSES) {
            $test_correct .= ", test3/read/D, test4/editPerms/A";
        }
        $test_correct .= "\n";
        $test_dump .= Alib::DumpPerms();
        Alib::DeleteData();
        if ($test_dump == $test_correct) {
            $test_log .= "alib: OK\n";
            return TRUE;
        } else {
            return PEAR::raiseError('Alib::test', 1, PEAR_ERROR_DIE, '%s'.
                "<pre>\ncorrect:\n{$test_correct}\n".
                "dump:\n{$test_dump}\n</pre>\n");
        }
    } // fn test

} // class Alib
?>