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
    Version  : $Revision: 1.8 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/alib.php,v $

------------------------------------------------------------------------------*/
require_once 'subj.php';
define('ALIBERR_NOTLOGGED', 30);
define('ALIBERR_NOTEXISTS', 31);

/**
 *   Alib class
 *
 *   authentication/authorization class
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.8 $
 *  @see Subjects
 *  @see GreenBox
 */
class Alib extends Subjects{
    var $permTable;
    var $sessTable;
    var $login=NULL;
    var $userid=NULL;
    var $sessid=NULL;
    /**
     *   Constructor
     *
     *   @param dbc object, DB
     *   @param config array
     *   @return this
     */
    function Alib(&$dbc, $config)
    {
        parent::Subjects(&$dbc, $config);
        $this->permTable = $config['tblNamePrefix'].'perms';
        $this->sessTable = $config['tblNamePrefix'].'sess';
    }

    /* ======================================================= public methods */

    /* ----------------------------------------------- session/authentication */

    /**
     *   Authenticate and create session
     *
     *   @param login string
     *   @param pass string
     *   @return boolean/sessionId/err
     */
    function login($login, $pass)
    {
        if(FALSE === $this->authenticate($login, $pass)) return FALSE;
        $sessid = $this->_createSessid();
        if(PEAR::isError($sessid)) return $sessid;
        $userid = $this->getSubjId($login);
        $r = $this->dbc->query("INSERT INTO {$this->sessTable}
                (sessid, userid, login, ts)
            VALUES
                ('$sessid', '$userid', '$login', now())");
        if(PEAR::isError($r)) return $r;
        $this->login = $login;
        $this->userid = $userid;
        $this->sessid = $sessid;
        return $sessid;
    }
    
    /**
     *   Logout and destroy session
     *
     *   @param sessid string
     *   @return true/err
     */
    function logout($sessid)
    {
        $ct = $this->checkAuthToken($sessid);
        if($ct === FALSE)
            return PEAR::raiseError('Alib::logout: not logged ($ct)',
                ALIBERR_NOTLOGGED, PEAR_ERROR_RETURN);
        elseif(PEAR::isError($ct))
            return $ct;
        else{
            $r = $this->dbc->query("DELETE FROM {$this->sessTable}
                WHERE sessid='$sessid'");
            if(PEAR::isError($r)) return $r;
            $this->login = NULL;
            $this->userid = NULL;
            $this->sessid = NULL;
            return TRUE;
        }
    }
    
    /**
     *   Return true if the token is valid
     *
     *   @param sessid string
     *   @return boolean/err
     */
    function checkAuthToken($sessid)
    {
        $c = $this->dbc->getOne("SELECT count(*) as cnt FROM {$this->sessTable}
            WHERE sessid='$sessid'");
        return ($c == 1 ? TRUE : (PEAR::isError($c) ? $c : FALSE ));
    }
    
    /**
     *   Set valid token in alib object
     *
     *   @param sessid string
     *   @return boolean/err
     */
    function setAuthToken($sessid)
    {
        $r = checkAuthToken($sessid);
        if(PEAR::isError($r)) return $r;
        if(!$r)
            return PEAR::raiseError("ALib::setAuthToken: invalid token ($sessid)");
        $this->sessid = $sessid;
        return TRUE;
    }
    
    /* -------------------------------------------------------- authorization */
    /**
     *   Insert permission record
     *
     *   @param sid int
     *   @param action string
     *   @param oid int
     *   @param type char
     *   @return int/err
     */
    function addPerm($sid, $action, $oid, $type='A')
    {
        $permid = $this->dbc->nextId("{$this->permTable}_id_seq");
        $r = $this->dbc->query($q = "
            INSERT INTO {$this->permTable} (permid, subj, action, obj, type)
            VALUES ($permid, $sid, '$action', $oid, '$type')
        ");
        if(PEAR::isError($r)) return($r);
        return $permid;
    }

    /**
     *   Remove permission record
     *
     *   @param permid int OPT
     *   @param subj int OPT
     *   @param obj int OPT
     *   @return null/error
     */
    function removePerm($permid=NULL, $subj=NULL, $obj=NULL)
    {
        return $this->dbc->query("DELETE FROM {$this->permTable} WHERE 1=1".
            ($permid ? " AND permid=$permid" : '').
            ($subj ? " AND subj=$subj" : '').
            ($obj ? " AND obj=$obj" : '')
        );
    }

    /**
     *   Check if specified subject have permission to specified action
     *   on specified object - huh ;)<br>
     *   One of the most important method in this class hierarchy ...
     *
     *   @param sid int
     *   @param action string
     *   @param oid int OPT
     *   @return boolean/err
     */
    function checkPerm($sid, $action, $oid=NULL)
    {
        if(!is_numeric($sid)) return FALSE;
        if(is_null($oid)) $oid = $this->getObjId($this->RootNode);
        if(PEAR::isError($oid)) return $oid;
        if(!is_numeric($oid)) return FALSE;
        // query elements
        $q_flds = "m.level as S_lvl, p.subj, s.login, action, p.type, p.obj";
        $q_from = "{$this->subjTable} s, {$this->permTable} p";
        $q_join = "LEFT JOIN {$this->smembTable} m ON p.subj=m.gid ";
        $q_cond = "p.action in('_all', '$action') AND
            (m.uid=$sid OR p.subj=$sid) AND s.id=p.subj";
        // action DESC order is hack for lower priority of '_all':
        $q_ordb = "ORDER BY S_lvl, action DESC, p.type DESC";
        $qc0 = $q_cond;
        // test if object is class:
        $iscls = $this->isClass($oid);
        if(PEAR::isError($iscls)) return $iscls;
        if($iscls){
            $q_from .= ", {$this->classTable} c";
            $q_cond .= " AND c.id=p.obj AND c.id=$oid";
        }else{
            //  obj is normal node => path search => retrieve L/R values for it:
            $r1 = $this->dbc->getRow("SELECT lft, rgt, level
                FROM {$this->treeTable} WHERE id=$oid");
            if(is_null($r1))
                return PEAR::raiseError("Alib::checkPerm: object not exists ($oid)",
                    ALIBERR_NOTEXISTS, PEAR_ERROR_RETURN
                );
            if(PEAR::isError($r1)) return($r1);
            //  fetch all path to oid + join with perms
            $q_flds .= ", t.name, ({$r1['level']}-t.level)as T_lvl";
            $q_from = "{$this->treeTable} t, ".$q_from;
            $q_cond .= " AND t.id=p.obj AND t.lft<={$r1['lft']} AND
                t.rgt>={$r1['rgt']}";
            // action DESC order is hack for lower priority of '_all':
            $q_ordb = "ORDER BY T_lvl, S_lvl, action DESC, p.type DESC";
        }
        $query="SELECT $q_flds FROM $q_from $q_join WHERE $q_cond $q_ordb";
        $r2 = $this->dbc->getAll($query);
        if(PEAR::isError($r2)) return($r2);
        if(!$iscls && !(is_array($r2) && count($r2)>0)){
          //  no perm found, search in classes:
            $q_from = "{$this->cmembTable} cm, ".$q_from;
            $q_cond = $qc0.
              " AND t.lft<={$r1['lft']} AND t.rgt>={$r1['rgt']}".
              " AND cm.cid=p.obj AND cm.objid=t.id";
            $query="SELECT $q_flds FROM $q_from $q_join WHERE $q_cond $q_ordb";
            $r2 = $this->dbc->getAll($query);
            if(PEAR::isError($r2)) return($r2);
        }
        //  if there is row with type='A' on the top => permit
        return (is_array($r2) && count($r2)>0 && $r2[0]['type']=='A');
    }

    /* ---------------------------------------------------------- object tree */

    /**
     *   Remove all permissions on object and then remove object itself
     *
     *   @param id int
     *   @return void/error
     */
    function removeObj($id)
    {
        $r = $this->removePerm(NULL, NULL, $id);
        if(PEAR::isError($r)) return $r;
        return parent::removeObj($id);
    }

    /* --------------------------------------------------------- users/groups */

    /**
     *   Remove all permissions of subject and then remove subject itself
     *
     *   @param login string
     *   @return void/error
     */
    function removeSubj($login)
    {
        $uid = $this->getSubjId($login);    if(PEAR::isError($uid)) return $uid;
        $r = $this->removePerm(NULL, $uid); if(PEAR::isError($r)) return $r;
        return parent::removeSubj($login, $uid);
    }
    
    /* ------------------------------------------------------------- sessions */
    /**
     *   Get login from session id (token)
     *
     *   @param sessid string
     *   @return string/error
     */
    function getSessLogin($sessid)
    {
        return $this->dbc->getOne("
            SELECT login FROM {$this->sessTable} WHERE sessid='$sessid'");
    }

    /**
     *   Get user id from session id
     *
     *   @param sessid string
     *   @return int/error
     */
    function getSessUserId($sessid)
    {
        return $this->dbc->getOne("
            SELECT userid FROM {$this->sessTable} WHERE sessid='$sessid'");
    }

    /* --------------------------------------------------------- info methods */
    /**
     *   Get all permissions on object
     *
     *   @param id int
     *   @return array/null/err
     */
    function getObjPerms($id)
    {
        return $this->dbc->getAll("
            SELECT s.login, p.* FROM {$this->permTable} p, {$this->subjTable} s
            WHERE s.id=p.subj AND p.obj=$id");
    }

    /**
     *   Get all permissions of subject
     *
     *   @param sid int
     *   @return array
     */
    function getSubjPerms($sid)
    {
        $a1 = $this->dbc->getAll("
            SELECT t.name, t.type as otype , p.*
            FROM {$this->permTable} p, {$this->treeTable} t
            WHERE t.id=p.obj AND p.subj=$sid");
        if(PEAR::isError($a1)) return $a1;
        $a2 = $this->dbc->getAll("
            SELECT c.cname as name, 'C'as otype, p.*
            FROM {$this->permTable} p, {$this->classTable} c
            WHERE c.id=p.obj AND p.subj=$sid");
        if(PEAR::isError($a2)) return $a2;
        return array_merge($a1, $a2);
    }

    /* ------------------------ info methods related to application structure */
    /* (this part should be added/rewritten to allow defining/modifying/using
     * application structure)
     * (only very simple structure definition - in $config - supported now)
     */
    
    /**
     *   Get all actions
     *
     *   @return array
     */
    function getAllActions()
    {
        return $this->config['allActions'];
    }

    /**
     *   Get all allowed actions on specified object type
     *
     *   @param type string
     *   @return array
     */
    function getAllowedActions($type)
    {
        return $this->config['allowedActions'][$type];
    }

    /* ====================================================== private methods */
    
    /**
     *   Create new session id
     *
     *   @return string sessid
     */
    function _createSessid()
    {
        for($c=1; $c>0;){
            $sessid = md5(uniqid(rand()));
            $c = $this->dbc->getOne("SELECT count(*) FROM {$this->sessTable}
                WHERE sessid='$sessid'");
            if(PEAR::isError($c)) return $c;
        }
        return $sessid;
    }
    
    /* =============================================== test and debug methods */

    /**
     *   Dump all permissions for debug
     *
     *   @param indstr string    // indentation string
     *   @param ind string       // aktual indentation
     *   @return string
     */
    function dumpPerms($indstr='    ', $ind='')
    {
        $r = $ind.join(', ', array_map(
            create_function('$v', 'return "{$v[\'action\']}/{$v[\'type\']}";'),
            $this->dbc->getAll("SELECT action, type FROM {$this->permTable}")
        ))."\n";
        return $r;
    }
    
    /**
     *   deleteData
     *
     *   @return void
     */
    function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->permTable}");
        $this->dbc->query("DELETE FROM {$this->sessTable}");
        parent::deleteData();
    }
    /**
     *   Insert test permissions
     *
     *   @return array
     */
    function testData()
    {
        parent::testData();
        $t =& $this->tdata['tree'];
        $c =& $this->tdata['classes'];
        $s =& $this->tdata['subjects'];
        $o[] = $this->addPerm($s[0], '_all', $t[0], 'A');
        $o[] = $this->addPerm($s[1], '_all', $t[4], 'A');
        $o[] = $this->addPerm($s[1], '_all', $t[7], 'D');
#        $o[] = $this->addPerm($s[2], 'addChilds', $t[6], 'A');
        $o[] = $this->addPerm($s[2], 'read', $t[5], 'A');
        $o[] = $this->addPerm($s[2], 'edit', $t[6], 'A');
        $o[] = $this->addPerm($s[3], 'read', $c[0], 'A');
        $o[] = $this->addPerm($s[4], 'editPerms', $c[1], 'A');
        $o[] = $this->addPerm($s[4], 'editPerms', $t[7], 'D');

        $o[] = $this->addPerm($s[1], 'addChilds', $t[3], 'A');
        $o[] = $this->addPerm($s[1], 'addChilds', $t[1], 'A');
        $o[] = $this->addPerm($s[5], 'addChilds', $t[3], 'A');
        $o[] = $this->addPerm($s[5], 'addChilds', $t[1], 'A');
        $o[] = $this->addPerm($s[6], 'addChilds', $t[3], 'A');
        $o[] = $this->addPerm($s[6], 'addChilds', $t[1], 'A');
        $o[] = $this->addPerm($s[7], 'addChilds', $t[3], 'A');
        $o[] = $this->addPerm($s[7], 'addChilds', $t[1], 'A');
        $this->tdata['perms'] = $o;
    }
    
    /**
     *   Make basic test
     *
     *   @return boolean/error
     */
    function test()
    {
        if(PEAR::isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->testData();
        $this->test_correct = "_all/A, _all/A, _all/D, read/A, edit/A, read/A,".
            " editPerms/A, editPerms/D, addChilds/A, addChilds/A, addChilds/A,".
            " addChilds/A, addChilds/A, addChilds/A, addChilds/A, addChilds/A".
            "\nno, yes\n";
        $this->test_dump = $this->dumpPerms().
            ($this->checkPerm(
                $this->tdata['subjects'][1], 'edit', $this->tdata['tree'][7]
            )? 'yes':'no').", ".
            ($this->checkPerm(
                $this->tdata['subjects'][2], 'read', $this->tdata['tree'][5]
            )? 'yes':'no')."\n"
        ;
        $this->removePerm($this->tdata['perms'][1]);
        $this->removePerm($this->tdata['perms'][3]);
        $this->test_correct .= "_all/A, _all/D, edit/A, read/A, editPerms/A,".
            " editPerms/D, addChilds/A, addChilds/A, addChilds/A, addChilds/A,".
            " addChilds/A, addChilds/A, addChilds/A, addChilds/A\n";
        $this->test_dump .= $this->dumpPerms();
        $this->deleteData();
        if($this->test_dump==$this->test_correct)
        { $this->test_log.="alib: OK\n"; return TRUE;
        }else return PEAR::raiseError('Alib::test', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\n".
            "dump:\n{$this->test_dump}\n</pre>\n");
    }

    /**
     *   Create tables + initialize
     *
     *   @return void
     */
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->permTable} (
            permid int not null PRIMARY KEY,
            subj int REFERENCES {$this->subjTable} ON DELETE CASCADE,
            action varchar(20),
            obj int,
            type char(1)
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->permTable}_permid_idx
            ON {$this->permTable} (permid)");
        $this->dbc->query("CREATE INDEX {$this->permTable}_subj_obj_idx
            ON {$this->permTable} (subj, obj)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->permTable}_all_idx
            ON {$this->permTable} (subj, action, obj)");
        $this->dbc->createSequence("{$this->permTable}_id_seq");

        $this->dbc->query("CREATE TABLE {$this->sessTable} (
            sessid char(32) not null PRIMARY KEY,
            userid int REFERENCES {$this->subjTable} ON DELETE CASCADE,
            login varchar(255),
            ts timestamp
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->sessTable}_sessid_idx
            ON {$this->sessTable} (sessid)");
        $this->dbc->query("CREATE INDEX {$this->sessTable}_userid_idx
            ON {$this->sessTable} (userid)");
        $this->dbc->query("CREATE INDEX {$this->sessTable}_login_idx
            ON {$this->sessTable} (login)");
    }

    /**
     *   Drop tables etc.
     *
     *   @return void
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->permTable}");
        $this->dbc->dropSequence("{$this->permTable}_id_seq");
        $this->dbc->query("DROP TABLE {$this->sessTable}");
        parent::uninstall();
    }
}
?>