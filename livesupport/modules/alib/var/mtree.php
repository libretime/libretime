<?php
// $Id: mtree.php,v 1.1 2004/07/23 00:22:13 tomas Exp $

/**
*   Mtree class
*
*   class for tree hierarchy stored in db
*
*   example config: example/conf.php
*   example minimal config:
*    $config = array(
*        'dsn'       => array(           // data source definition
*            'username' => DBUSER,
*            'password' => DBPASSWORD,
*            'hostspec' => 'localhost',
*            'phptype'  => 'pgsql',
*            'database' => DBNAME
*        ),
*        'tblNamePrefix'     => 'al_',
*        'RootNode'	=>'RootNode',
*    );
*   (mysql phptype is tested too, but psql is recommended)
**/
define('ALIBERR_MTREE', 10);

class Mtree{
    var $dbc;
    var $config;
    var $treeTable;
    var $rootNodeName;
    /** Mtree - constructor
    *
    *   @param dbc object
    *   @param config array
    *   @return this
    **/
    function Mtree(&$dbc, $config)
    {
        $this->dbc =& $dbc;
        $this->config = $config;
        $this->treeTable = $config['tblNamePrefix'].'tree';
        $this->rootNodeName = $config['RootNode'];
    }

    /* ========== public methods: ========== */

    /**
    *   addObj
    *
    *   @param name string
    *   @param type string
    *   @param parid int OPT        // parent id
    *   @param aftid int OPT        // after id
    *   @param param string OPT
    *   @return int/err             // new id of inserted object
    **/
    function addObj($name, $type, $parid=1, $aftid=NULL, $param='')
    {
        if($name=='' || $type=='') return PEAR::raiseError('Mtree::addObj: Wrong name or type', ALIBERR_MTREE);
        $this->dbc->query("BEGIN");
        $r = $this->dbc->query("LOCK TABLE {$this->treeTable}");    if(PEAR::isError($r)) return $r;
        // position resolving:
        if(is_null($aftid)){                      // add object as last child
            $after = $this->dbc->getOne("
                SELECT max(rgt) FROM {$this->treeTable} WHERE parid='$parid'
            ");
        }else{                                    // use 'aftid'
            $after = $this->dbc->getOne("
                SELECT ".($aftid == $parid ? 'lft' : 'rgt')."
                FROM {$this->treeTable} WHERE id='$aftid'");
        }
        if(PEAR::isError($after)) return $this->_dbRollback($after);
        if(is_null($after)){                      // position not specified - add as first child
            $after = $this->dbc->getOne("
                SELECT lft FROM {$this->treeTable} WHERE id='$parid'
            ");
        }
        if(PEAR::isError($after)) return $this->_dbRollback($after);
        $after = intval($after);
        // tree level resolving:
        $level = $this->dbc->getOne("SELECT level FROM {$this->treeTable} WHERE id='$parid'");
        if(is_null($level)) return $this->_dbRollback('addObj: parent does not exist');
        if(PEAR::isError($level)) return $this->_dbRollback($level);
        $id = $this->dbc->nextId("{$this->treeTable}_id_seq");
        if(PEAR::isError($id)) return $this->_dbRollback($id);
        // creating space in rgt/lft sequencies:
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt+2 WHERE rgt>$after");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft+2 WHERE lft>$after");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        // inserting object:
        $r = $this->dbc->query("
            INSERT INTO {$this->treeTable} (id, name, type, parid, level, lft, rgt, param)
            VALUES ('$id', '$name', '$type', $parid, ".($level+1).", ".($after+1).", ".($after+2).", '$param')
        "); 
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        return $id;
    }
    
    /**
    *   copyObj
    *
    *   @param id int
    *   @param newParid int
    *   @param after int OPT
    *   @return int/err
    **/
    function copyObj($id, $newParid, $after=NULL)
    {
        $o = $this->dbc->getRow("SELECT * FROM {$this->treeTable} WHERE id='$id'");
        if(PEAR::isError($o)) return $o;
        $nid = $this->addObj($o['name'], $o['type'], $newParid, $after, $o['param']);
        return $nid;
    }

    /**
    *   renameObj
    *
    *   @param id int
    *   @param newName string
    *   @return int/err
    **/
    function renameObj($id, $newName)
    {
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET name='$newName' WHERE id='$id'");
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
    *   removeObj
    *
    *   @param id int
    *   @return boolean/err
    **/
    function removeObj($id)
    {
        $dirarr = $this->getDir($id); if(PEAR::isError($dirarr)) return $dirarr;
        foreach($dirarr as $k=>$snod)
        {
            $this->removeObj($snod['id']);
        }
        $this->dbc->query("BEGIN");
        $r = $this->dbc->query("LOCK TABLE {$this->treeTable}");    if(PEAR::isError($r)) return $r;
        $rgt = $this->dbc->getOne("SELECT rgt FROM {$this->treeTable} WHERE id='$id'");
        if(is_null($rgt)) return $this->_dbRollback('removeObj: object not exists');
        // deleting object:
        $r = $this->dbc->query("DELETE FROM {$this->treeTable} WHERE id='$id'");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        // closing the space in rgt/lft sequencies:
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt-2 WHERE rgt>$rgt");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft-2 WHERE lft>$rgt");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        return TRUE;
    }
    
    /* --- info methods: --- */

    /**
    *   getObjId - search dir for object by name
    *
    *   @param name string
    *   @param parId int OPT
    *   @return int/err
    **/
    function getObjId($name, $parId=NULL)
    {
        if($name=='' && is_null($parId)) $name = $this->rootNodeName;
        return $this->dbc->getOne(
            "SELECT id FROM {$this->treeTable} WHERE name='$name' and ".($parId ? "parid='$parId'":"parid is null")
        );
    }

    /**
    *   getObjName - get one value for object (default: get name)
    *
    *   @param oid int
    *   @param fld string OPT
    *   @return string/err
    **/
    function getObjName($oid, $fld='name')
    {
        return $this->dbc->getOne("SELECT $fld FROM {$this->treeTable} WHERE id='$oid'");
    }

    /**
    *   getObjType
    *
    *   @param oid int
    *   @return string/err
    **/
    function getObjType($oid)
    {
        return $this->getObjName($oid, 'type');
    }
    
    /**
    *   getParent
    *
    *   @param id int
    *   @return string/err
    **/
    function getParent($oid)
    {
        return $this->getObjName($oid, 'parid');
    }
    
    /**
    *   getPath - get array of nodes in object's path
    *
    *   @param id int
    *   @param flds string OPT
    *   @return array/err
    **/
    function getPath($id, $flds='id')
    {
        $this->dbc->query("BEGIN");
        $a = $this->dbc->getRow("SELECT name, lft, rgt FROM {$this->treeTable} WHERE id='$id'");
        $res = $this->dbc->getAll("
            SELECT $flds FROM {$this->treeTable} WHERE lft<={$a['lft']} AND rgt>={$a['rgt']}
            ORDER by lft
        ");
        $this->dbc->query("COMMIT");
        return $res;
    }
    
    /**
    *   getDir - get array of childnodes
    *
    *   @param id int
    *   @param flds string OPT
    *   @param order string OPT
    *   @return array/err
    **/
    function getDir($id, $flds='id', $order='lft')
    {
        return $this->dbc->getAll("
            SELECT $flds FROM {$this->treeTable} WHERE parid='$id' ORDER BY $order
        ");
    }
    
    /**
    *   getSubTree
    *
    *   @param id int OPT
    *   @param withRoot boolean OPT
    *   @return array/err
    **/
    function getSubTree($id=NULL, $withRoot=FALSE)
    {
        if(is_null($id)) $id = $this->getRootNode();
        $r = array();
        if($withRoot) $r[] = $re = $this->dbc->getRow("SELECT id, name, level FROM {$this->treeTable} WHERE id='$id'");
        if(PEAR::isError($re)) return $re;
        $dirarr = $this->getDir($id); if(PEAR::isError($dirarr)) return $dirarr;
        foreach($dirarr as $k=>$snod)
        {
            $r[] = $re = $this->dbc->getRow("SELECT id, name, level FROM {$this->treeTable} WHERE id={$snod['id']}");
            if(PEAR::isError($re)) return $re;
            $r = array_merge($r, $this->getSubTree($snod['id']));
        }
        return $r;
    }

    /**
    *   getRootNode - get id of root node
    *
    *   @return int/err
    **/
    function getRootNode()
    {
        return $this->getObjId($this->rootNodeName);
    }

    /**
    *   getAllObjects
    *
    *   @return array/err
    **/
    function getAllObjects()
    {
        return $this->dbc->getAll("SELECT * FROM {$this->treeTable} ORDER BY lft");
    }
    
    /* --- info methods related to application structure: --- */
    /* (this part should be added/rewritten to allow defining/modifying/using application structure) */
    /* (only very simple structure definition - in config - supported now) */
    
    /**
    *   getAllowedChildTypes
    *
    *   @param type string
    *   @return array
    **/
    function getAllowedChildTypes($type)
    {
        return $this->config['objtypes'][$type];
    }
    
    
    /* ========== "private" methods: ========== */
    
    
    /**
    *   _dbRollback
    *
    *   @param r object/string
    *   @return err
    **/
    function _dbRollback($r)
    {
        $this->dbc->query("ROLLBACK");
        if(PEAR::isError($r)) return $r;
        elseif(is_string($r)) return PEAR::raiseError("ERROR: ".get_class($this).": $r", ALIBERR_MTREE, PEAR_ERROR_RETURN);
        else return PEAR::raiseError("ERROR: ".get_class($this).": unknown error", ALIBERR_MTREE, PEAR_ERROR_RETURN);
    }

    /**
    *   _relocateSubtree - move subtree to another node without removing/adding
    *
    *   @param id int
    *   @param newParid int
    *   @param after int
    *   @return boolean/err
    **/
    function _relocateSubtree($id, $newParid, $after=NULL)
    {
        $this->dbc->query("BEGIN");
        $r = $this->dbc->query("LOCK TABLE {$this->treeTable}");    if(PEAR::isError($r)) return $r;
        // obtain values for source node:
        $a1 = $this->dbc->getRow("SELECT lft, rgt, level FROM {$this->treeTable} WHERE id='$id'");
        if(is_null($a1)) return $this->_dbRollback('_relocateSubtree: object not exists');
        extract($a1);
        // values for destination node:
        $a2 = $this->dbc->getRow("SELECT rgt, level FROM {$this->treeTable} WHERE id='$newParid'");
        if(is_null($a2)) return $this->_dbRollback('_relocateSubtree: new parent not exists');
        $nprgt = $a2['rgt']; $newLevel = $a2['level'];
        // calculate differencies:
        if(is_null($after)) $after = $nprgt-1;
        $dif1 = $rgt-$lft+1;
        $dif2 = $after-$lft+1;
        $dif3 = $newLevel-$level+1;
        // relocate the object"
        $r = $this->dbc->query("UPDATE {$this->treeTable} SET parid='$newParid' WHERE id='$id'");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        if($after>$rgt){
            // relocate subtree to the right:
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt+$dif1 WHERE rgt>$after");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft+$dif1 WHERE lft>$after");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable}
                SET lft=lft+$dif2, rgt=rgt+$dif2, level=level+$dif3
                WHERE lft>=$lft AND rgt <=$rgt");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt-$dif1 WHERE rgt>$rgt");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft-$dif1 WHERE lft>$rgt");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
        }else{
            // relocate subtree to the left:
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt+$dif1 WHERE rgt>$after");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft+$dif1 WHERE lft>$after");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable}
                SET lft=lft+$dif2-$dif1, rgt=rgt+$dif2-$dif1, level=level+$dif3
                WHERE lft>=$lft+$dif1 AND rgt <=$rgt+$dif1");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET rgt=rgt-$dif1 WHERE rgt>$rgt+$dif1");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
            $r = $this->dbc->query("UPDATE {$this->treeTable} SET lft=lft-$dif1 WHERE lft>$rgt+$dif1");
            if(PEAR::isError($r)) return $this->_dbRollback($r);
        }
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        return TRUE;
    }

    /**
    *   _copySubtree - recursive copyObj
    *
    *   @param id int
    *   @param newParid int
    *   @param after int
    *   @return array
    **/
    function _copySubtree($id, $newParid, $after=NULL)
    {
        $nid = $this->copyObj($id, $newParid, $after);
        if(PEAR::isError($nid)) return $nid;
        $dirarr = $this->getDir($id); if(PEAR::isError($dirarr)) return $dirarr;
        foreach($dirarr as $k=>$snod)
        {
            $r = $this->_copySubtree($snod['id'], $nid);
            if(PEAR::isError($r)) return $r;
        }
    }
    
    /* ========== test and debug methods: ========== */

    /**
    *   dumpTree
    *
    *   @param id int
    *   @param indstr string    // indentation string
    *   @param ind string       // aktual indentation
    *   @return string
    **/
    function dumpTree($id=NULL, $indstr='    ', $ind='', $format='{name}', $withRoot=TRUE)
    {
        $r='';
        foreach($this->getSubTree($id, $withRoot) as $o)
            $r .= str_repeat($indstr, intval($o['level'])).
                preg_replace(array('|\{name\}|', '|\{id\}|'), array($o['name'], $o['id']), $format).
                "\n";
        return $r;
    }
    
    /**
    *   deleteData
    *
    **/
    function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->treeTable} WHERE parid is not null");
    }

    /**
    *   testData
    *
    *   @param id int OPT
    *   @return array
    **/
    function testData()
    {
        $o[] = $rootId = $this->getRootNode();
        $o[] = $p1 = $this->addObj('Publication A', 'Publication', $rootId);    // 1
        $o[] = $p2 = $this->addObj('Publication B', 'Publication', $rootId);    // 2
        $o[] = $i1 = $this->addObj('Issue 1', 'Issue', $p1);                    // 3
        $o[] = $i2 = $this->addObj('Issue 2', 'Issue', $p1);                 // 4
        $o[] = $s1 = $this->addObj('Section a', 'Section', $i2);
        $o[] = $s2 = $this->addObj('Section b', 'Section', $i2);             // 6
        $o[] = $s3 = $this->addObj('Section c', 'Section', $i2);
        $o[] = $t1 = $this->addObj('Title', 'Title', $s2);
        $o[] = $s4 = $this->addObj('Section a', 'Section', $i1);
        $o[] = $s5 = $this->addObj('Section b', 'Section', $i1);
        $this->tdata['tree'] = $o;
    }

    /**
    *   test
    *
    **/
    function test()
    {
        $this->deleteData();
        $this->testData();
        $rootId = $this->getRootNode();
        $this->test_correct ="RootNode\n    Publication A\n        Issue 1\n            Section a\n            Section b\n        Issue 2\n            Section a\n            Section b\n                Title\n            Section c\n    Publication B\nRootNode\n";
        $this->test_dump = $this->dumpTree();
        $this->removeObj($this->tdata['tree'][1]);
        $this->removeObj($this->tdata['tree'][2]);
        $this->test_dump .= $this->dumpTree();
        $this->deleteData();
        if($this->test_dump == $this->test_correct){ $this->test_log.="tree: OK\n"; return TRUE; }
        else return PEAR::raiseError('Mtree::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n.{$this->test_correct}.\ndump:\n.{$this->test_dump}.\n</pre>\n");
    }

    /**
    *   install - create tables + initialize
    *
    **/
    function install()
    {
        $this->dbc->query("CREATE TABLE {$this->treeTable} (
            id int not null,
            name varchar(255) not null default'',
            parid int,
            lft int,
            rgt int,
            level int,
            type varchar(255) not null default'',
            param varchar(255)
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->treeTable}_id_idx on {$this->treeTable} (id)");
        $this->dbc->query("CREATE INDEX {$this->treeTable}_name_idx on {$this->treeTable} (name)");
        $this->dbc->createSequence("{$this->treeTable}_id_seq");

        $id = $this->dbc->nextId("{$this->treeTable}_id_seq");
        $this->dbc->query("INSERT INTO {$this->treeTable} (id, name, parid, level, lft, rgt, type)
            VALUES ($id, '{$this->rootNodeName}', NULL, 0, 1, 2, 'RootNode')");
    }

    /**
    *   uninstall
    *
    **/
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->treeTable}");
        $this->dbc->dropSequence("{$this->treeTable}_id_seq");
    }

    /**
    *   reinstall
    *
    **/
    function reinstall()
    {
        $this->uninstall();
        $this->install();
    }
}
?>