<?php
// $Id: subj.php,v 1.1 2004/07/23 00:22:13 tomas Exp $

/**
*   Subj class
*
*   users + groups
*   with "linearized recursive membership" ;)
*   (allow adding users to groups or groups to groups)
*   
**/
require_once"class.php";
define('ALIBERR_NOTGR', 20);
define('ALIBERR_BADSMEMB', 21);

class Subjects extends ObjClasses{
    var $subjTable;
    var $smembTable;
    /** Subjects - constructor
    *
    *   @param dbc object
    *   @param config array
    *   @return this
    **/
    function Subjects(&$dbc, $config)
    {
        parent::ObjClasses(&$dbc, $config);
        $this->subjTable = $config['tblNamePrefix'].'subjs';
        $this->smembTable = $config['tblNamePrefix'].'smemb';
    }

    /* ========== public methods: ========== */

    /**
    *   addSubj
    *
    *   @param login string
    *   @param pass string OPT
    *   @return int/err
    **/
    function addSubj($login, $pass=NULL)
    {
        $id = $this->dbc->nextId("{$this->subjTable}_id_seq");  if(PEAR::isError($id)) return $id;
        $r = $this->dbc->query("
            INSERT INTO {$this->subjTable} (id, login, pass, type)
            VALUES ($id, '$login', ".(is_null($pass) ? "'!', 'G'" : "'".md5($pass)."', 'U'").")
        ");
        if(PEAR::isError($r)) return $r;
        return $id;
    }

    /**
    *   removeSubj
    *
    *   @param login string
    *   @param uid int OPT
    *   @return boolean/err
    **/
    function removeSubj($login, $uid=NULL)
    {
        if(is_null($uid)) $uid = $this->getSubjId($login);
        if(PEAR::isError($uid)) return $uid;
        $r = $this->dbc->query("DELETE FROM {$this->smembTable} WHERE (uid='$uid' OR gid='$uid') AND mid is null");
        if(PEAR::isError($r)) return $r;
        $r = $this->dbc->query("DELETE FROM {$this->subjTable} WHERE login='$login'");
        if(PEAR::isError($r)) return $r;
        return $this->_rebuildRels();
    }

    /**
    *   authenticate
    *
    *   @param login string
    *   @param pass string OPT
    *   @return boolean/int/err
    **/
    function authenticate($login, $pass='')
    {
        $cpass = md5($pass);
        $id = $this->dbc->getOne("
            SELECT id FROM {$this->subjTable}
            WHERE login='$login' AND pass='$cpass' AND type='U'
        ");
        if(PEAR::isError($id)) return $id;
        return (is_null($id) ? FALSE : $id);
    }

    /* --- groups --- */
    
    /**
    *   addSubj2Gr - add {login} and direct/indirect members to {gname} and to groups, where {gname} is [in]direct member
    *
    *   @param login string
    *   @param gname string
    *   @return int/err
    **/
    function addSubj2Gr($login, $gname)
    {
        $uid = $this->getSubjId($login);    if(PEAR::isError($uid)) return $uid;
        $gid = $this->getSubjId($gname);    if(PEAR::isError($gid)) return $gid;
        $isgr = $this->isGroup($gid);   if(PEAR::isError($isgr)) return $isgr;
        if(!$isgr) return PEAR::raiseError("Subjects::addSubj2Gr: Not a group ($gname)", ALIBERR_NOTGR);
        // add subject and all [in]direct members to group $gname:
        $mid = $this->_plainAddSubj2Gr($uid, $gid);  if(PEAR::isError($mid)) return $mid;
        // add it to all groups where $gname is [in]direct member:
        $marr = $this->_listRMemb($gid); if(PEAR::isError($marr)) return $marr;
        foreach($marr as $k=>$v){
            $r = $this->_plainAddSubj2Gr($uid, $v['gid'], intval($v['level'])+1, $v['id']);
            if(PEAR::isError($r)) return $r;
        }
        return $mid;
    }

    /**
    *   removeSubjFromGr
    *
    *   @param login string
    *   @param gname string
    *   @return boolean/err
    **/
    function removeSubjFromGr($login, $gname)
    {
        $uid = $this->getSubjId($login);    if(PEAR::isError($uid)) return $uid;
        $gid = $this->getSubjId($gname);    if(PEAR::isError($gid)) return $gid;
        $mid = $this->dbc->getOne($q = "SELECT id FROM {$this->smembTable} WHERE uid='$uid' AND gid='$gid' AND mid is null");
        if(is_null($mid)) return FALSE;
        if(PEAR::isError($mid)) return $mid;
        // remove it:
        $r = $this->_removeMemb($mid);  if(PEAR::isError($r)) return $r;
        // and rebuild indirect memberships:  
        $r = $this->_rebuildRels();  if(PEAR::isError($r)) return $r;
        return TRUE;
    }
    
    /* --- info methods: --- */

    /**
    *   getSubjId
    *
    *   @param login string
    *   @return int/err
    **/
    function getSubjId($login)
    {
        return $this->dbc->getOne("SELECT id FROM {$this->subjTable} WHERE login='$login'");
    }

    /**
    *   getSubjName
    *
    *   @param id int
    *   @param fld string
    *   @return string/err
    **/
    function getSubjName($id, $fld='login')
    {
        return $this->dbc->getOne("SELECT $fld FROM {$this->subjTable} WHERE id='$id'");
    }

    /**
    *   getSubjects
    *
    *   @param flds string OPT
    *   @return array/err
    **/
    function getSubjects($flds='id, login')
    {
        return $this->dbc->getAll("SELECT $flds FROM {$this->subjTable}");
    }

    /**
    *   getSubjectsWCnt - get subjects with count of direct members
    *
    *   @return array/err
    **/
    function getSubjectsWCnt()
    {
        return $this->dbc->getAll("SELECT count(m.uid)as cnt, s.id, s.login, s.type
            FROM {$this->subjTable} s LEFT JOIN {$this->smembTable} m ON m.gid=s.id
            WHERE m.mid is null GROUP BY s.id, s.login, s.type ORDER BY s.id");
    }
    
    /**
    *   isGroup
    *
    *   @param gid int
    *   @return boolean/err
    **/
    function isGroup($gid)
    {
        $r = $this->dbc->getOne("SELECT type FROM {$this->subjTable} WHERE id='$gid'");
        if(PEAR::isError($r)) return $r;
        return ($r === 'G' );
    }
    
    /**
    *   listGroup - list direct members of group
    *
    *   @param gid int
    *   @return array/err
    **/
    function listGroup($gid)
    {
        return $this->dbc->getAll("SELECT s.id, s.login, s.type FROM {$this->smembTable} m, {$this->subjTable} s
            WHERE m.uid=s.id AND m.mid is null AND m.gid='$gid'");
    }

    /* ========== "private" methods: ========== */
    
    /**
    *   _addMemb - create membership record
    *
    *   @param uid int
    *   @param gid int
    *   @param level int OPT
    *   @param mid int OPT
    *   @return int/err
    **/
    function _addMemb($uid, $gid, $level=0, $mid='null')
    {
        if($uid == $gid)  return PEAR::raiseError("Subjects::_addMemb: uid==gid ($uid)", ALIBERR_BADSMEMB);
        $a = $this->dbc->getAll("SELECT id, level, mid FROM {$this->smembTable} WHERE uid='$uid' AND gid='$gid' ORDER BY level ASC");
        if(PEAR::isError($a)) return $a;
        if(count($a)>0){
            $a0 = $a[0];
            $id = $a0['id'];
            if($level < intval($a0['level'])){
                $r = $this->dbc->query("UPDATE {$this->smembTable} SET level='$level', mid='$mid' WHERE id='{$a0['id']}'");
                if(PEAR::isError($r)) return $r;
            }
        }else{
            $id = $this->dbc->nextId("{$this->smembTable}_id_seq"); if(PEAR::isError($id)) return $id;
            $r = $this->dbc->query("
                INSERT INTO {$this->smembTable} (id, uid, gid, level, mid) VALUES ($id, $uid, $gid, $level, $mid)
            ");
            if(PEAR::isError($r)) return $r;
        }
        return $id;
    }

    /**
    *   _removeMemb
    *
    *   @param mid int
    *   @return null/err
    **/
    function _removeMemb($mid)
    {
        return $this->dbc->query("DELETE FROM {$this->smembTable} WHERE id='$mid'");
    }

    /**
    *   _listMemb - list [in]direct members of group
    *
    *   @param gid int
    *   @param uid int OPT
    *   @return array/err
    **/
    function _listMemb($gid, $uid=NULL)
    {
        return $this->dbc->getAll("SELECT id, uid, level FROM {$this->smembTable} WHERE gid='$gid'".(is_null($uid) ? '' : " AND uid='$uid'"));
    }

    /**
    *   _listRMemb - list groups where uid is [in]direct member
    *
    *   @param gid int
    *   @param uid int OPT
    *   @return array/err
    **/
    function _listRMemb($uid, $gid=NULL)
    {
        return $this->dbc->getAll("SELECT id, gid, level FROM {$this->smembTable} WHERE uid='$uid'".(is_null($gid) ? '' : " AND gid='$gid'"));
    }

    /**
    *   _plainAddSubj2Gr - add uid and its [in]direct members to gid
    *
    *   @param uid int
    *   @param gid int
    *   @param level int
    *   @param rmid int             //
    *   @return int/err
    **/
    function _plainAddSubj2Gr($uid, $gid, $level=0, $rmid='null')
    {
        $mid = $this->_addMemb($uid, $gid, $level, $rmid);   if(PEAR::isError($mid)) return $mid;
        $marr = $this->_listMemb($uid);  if(PEAR::isError($marr)) return $marr;
        foreach($marr as $k=>$v){
            $r = $this->_addMemb($v['uid'], $gid, intval($v['level'])+$level+1, $mid);
            if(PEAR::isError($r)) return $r;
        }
        return $mid;
    }

    /**
    *   _rebuildRels - rebuild indirect membership records
    *
    *   @return true/err
    **/
    function _rebuildRels()
    {
        $this->dbc->query("BEGIN");
        $r = $this->dbc->query("LOCK TABLE {$this->smembTable}");   if(PEAR::isError($r)) return $r;
        $r = $this->dbc->query("DELETE FROM {$this->smembTable} WHERE mid is not null");
        if(PEAR::isError($r)) return $r;
        $arr = $this->dbc->getAll("SELECT uid, gid FROM {$this->smembTable}");   //  WHERE mid is null
        if(PEAR::isError($arr)) return $arr;
        foreach($arr as $it){
            $marr = $this->_listRMemb($it['gid']);   if(PEAR::isError($marr)) return $marr;
            foreach($marr as $k=>$v){
                $r = $this->_plainAddSubj2Gr($it['uid'], $v['gid'], intval($v['level'])+1, $v['id']);
                if(PEAR::isError($r)) return $r;
            }
        }
        $r = $this->dbc->query("COMMIT");   if(PEAR::isError($r)) return $r;
        return TRUE;
    }
    
    /* ========== test and debug methods: ========== */

    /**
    *   dumpSubjects
    *
    *   @param indstr string    // indentation string
    *   @param ind string       // aktual indentation
    *   @return string
    **/
    function dumpSubjects($indstr='    ', $ind='')
    {
#        $r = $ind.join(', ', $this->dbc->getCol("SELECT login FROM {$this->subjTable}"))."\n";
        $r = $ind.join(', ', array_map(create_function('$v', 'return "{$v[\'login\']}({$v[\'cnt\']})";'), $this->getSubjectsWCnt()))."\n";
        return $r;
    }
    
    /**
    *   deleteData
    *
    *   @return void 
    **/
    function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->subjTable}");
        $this->dbc->query("DELETE FROM {$this->smembTable}");
        parent::deleteData();
    }

    /**
    *   testData
    *
    *   @return array
    **/
    function testData()
    {
        parent::testData();
        $o[] = $this->addSubj('root', 'q');
        $o[] = $this->addSubj('test1', 'a');
        $o[] = $this->addSubj('test2', 'a');
        $o[] = $this->addSubj('test3', 'a');
        $o[] = $this->addSubj('test4', 'a');
        $o[] = $this->addSubj('gr1');
        $o[] = $this->addSubj('gr2');
#        $this->addSubj2Gr('test1', 'gr1');
        $this->addSubj2Gr('test2', 'gr1');
        $this->addSubj2Gr('test3', 'gr1');
        $this->addSubj2Gr('test4', 'gr2');
        $this->addSubj2Gr('gr2', 'gr1');
        $o[] = $this->addSubj('gr3');
        $this->addSubj2Gr('test3', 'gr3');
        $this->addSubj2Gr('test1', 'gr3');
        $this->addSubj2Gr('gr3', 'gr2');
        return $this->tdata['subjects'] = $o;
    }
    
    /**
    *   test
    *
    **/
    function test()
    {
        if(PEAR::isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->testData();
        $this->test_correct = "root(0), test1(0), test2(0), test3(0), test4(0), gr1(3), gr2(2), gr3(2)\n";
        $this->test_dump = $this->dumpSubjects();
        $this->removeSubj('test1');
        $this->removeSubj('test3');
        $this->removeSubjFromGr('test2', 'gr1');
        $this->removeSubjFromGr('gr3', 'gr2');
        $this->test_correct .= "root(0), test2(0), test4(0), gr1(1), gr2(1), gr3(0)\n";
        $this->test_dump .= $this->dumpSubjects();
        $this->deleteData();
        if($this->test_dump == $this->test_correct){ $this->test_log.="subj: OK\n"; return TRUE; }
        else return PEAR::raiseError('Subjects::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\ndump:\n{$this->test_dump}\n</pre>\n");
    }

    /**
    *   install - create tables + initialize
    *
    **/
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->subjTable} (
            id int not null,
            login varchar(255) not null default'',
            pass varchar(255) not null default'',
            type char(1) not null default 'U'
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->subjTable}_id_idx on {$this->subjTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->subjTable}_login_idx on {$this->subjTable} (login)");
        $this->dbc->createSequence("{$this->subjTable}_id_seq");

        $this->dbc->query("CREATE TABLE {$this->smembTable} (
            id int not null,
            uid int not null default 0,
            gid int not null default 0,
            level int not null default 0,
            mid int
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->smembTable}_id_idx on {$this->smembTable} (id)");
        $this->dbc->createSequence("{$this->smembTable}_id_seq");

    }

    /**
    *   uninstall
    *
    *   @return void 
    **/
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->subjTable}");
        $this->dbc->dropSequence("{$this->subjTable}_id_seq");
        $this->dbc->query("DROP TABLE {$this->smembTable}");
        $this->dbc->dropSequence("{$this->smembTable}_id_seq");
        parent::uninstall();
    }
}
?>