<?php
// $Id: class.php,v 1.1 2004/07/23 00:22:13 tomas Exp $

/**
*   ObjClass class
*
*   class for 'object classes' handling - i.e. groups of object in tree
*   @parent Mtree
**/
require_once"mtree.php";

class ObjClasses extends Mtree{
    var $classTable;
    var $cmembTable;
    /** ObjClasses - constructor
    *
    *   @param dbc object
    *   @param config array
    *   @return this
    **/
    function ObjClasses(&$dbc, $config)
    {
        parent::MTree(&$dbc, $config);
        $this->classTable = $config['tblNamePrefix'].'classes';
        $this->cmembTable = $config['tblNamePrefix'].'cmemb';
    }

    /* ========== public methods: ========== */

    /**
    *   addClass
    *
    *   @param cname string
    *   @return id/error
    **/
    function addClass($cname)
    {
        $id = $this->dbc->nextId("{$this->treeTable}_id_seq");  if(PEAR::isError($id)) return $id;
        $r = $this->dbc->query("
            INSERT INTO {$this->classTable} (id, cname)
            VALUES ($id, '$cname')
        ");
        if(PEAR::isError($r)) return $r;
        return $id;
    }

    /**
    *   removeClass
    *
    *   @param cname string
    *   @return boolean/err
    **/
    function removeClass($cname)
    {
        $cid = $this->getClassId($cname);   if(PEAR::isError($cid)) return($cid);
        return $this->removeClassById($cid);
    }

    /**
    *   removeClassById
    *
    *   @param cid int
    *   @return boolean/err
    **/
    function removeClassById($cid)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable} WHERE cid=$cid");
        if(PEAR::isError($r)) return $r;
        $r = $this->dbc->query("DELETE FROM {$this->classTable} WHERE id=$cid");
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
    *   addObj2Class
    *
    *   @param cid int
    *   @param oid int
    *   @return boolean/err
    **/
    function addObj2Class($cid, $oid)
    {
        $r = $this->dbc->query("INSERT INTO {$this->cmembTable} (cid, objid) VALUES ($cid, $oid)");
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
    *   removeObjFromClass
    *
    *   @param oid int
    *   @param cid int OPT      // if not specified, remove obj from all classes
    *   @return boolean/err
    **/
    function removeObjFromClass($oid, $cid=NULL)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable} WHERE objid=$oid".(is_null($cid)? '':" AND cid=$cid"));
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /* --- object tree --- */

    /**
    *   removeObj
    *
    *   @param id int
    *   @return boolean/err
    **/
    function removeObj($id)
    {
        $r = $this->removeObjFromClass($id);    if(PEAR::isError($r)) return $r;
        return parent::removeObj($id);
    }

    /* --- info methods: --- */

    /**
    *   getClassId
    *
    *   @param cname string
    *   @return int/err
    **/
    function getClassId($cname)
    {
        return $this->dbc->getOne($query = "SELECT id FROM {$this->classTable} WHERE cname='$cname'");
    }

    /**
    *   getClassName
    *
    *   @param id int
    *   @return string/err
    **/
    function getClassName($id)
    {
        return $this->dbc->getOne($query = "SELECT cname FROM {$this->classTable} WHERE id=$id");
    }

    /**
    *   isClass
    *
    *   @param id int
    *   @return boolean/err
    **/
    function isClass($id)
    {
        $r = $this->dbc->getOne("SELECT count(*) FROM {$this->classTable} WHERE id=$id");
        if(PEAR::isError($r)) return $r;
        return ($r > 0);
    }

    /**
    *   getClasses
    *
    *   @return array/err
    **/
    function getClasses()
    {
        return $this->dbc->getAll("SELECT * FROM {$this->classTable}");
    }

    /**
    *   listClass
    *
    *   @param id int
    *   @return array/err
    **/
    function listClass($id)
    {
        return $this->dbc->getAll("SELECT t.* FROM {$this->cmembTable} cm, {$this->treeTable} t
            WHERE cm.cid=$id AND cm.objid=t.id");
    }

    /* ========== test and debug methods: ========== */

    /**
    *   dumpClasses
    *
    *   @param id int
    *   @param indstr string    // indentation string
    *   @param ind string       // aktual indentation
    *   @return string
    **/
    function dumpClasses($indstr='    ', $ind='')
    {
        $r = $ind.join(', ', array_map(create_function('$v', 'return "{$v[\'cname\']} ({$v[\'cnt\']})";'),
            $this->dbc->getAll("
                SELECT cname, count(cm.objid)as cnt FROM {$this->classTable} c
                LEFT JOIN {$this->cmembTable} cm ON c.id=cm.cid
                GROUP BY cname, c.id ORDER BY c.id
            ")
        ))."\n";
        return $r;
    }
    
    /**
    *   testData
    *
    **/
    function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->cmembTable}");
        $this->dbc->query("DELETE FROM {$this->classTable}");
        parent::deleteData();
    }
    function testData()
    {
        parent::testData();
        $o[] = $this->addClass('Sections b');
        $o[] = $this->addClass('Class 2');
        $this->addObj2Class($o[1], $this->tdata['tree'][4]);
        $this->addObj2Class($o[1], $this->tdata['tree'][9]);
        $this->tdata['classes'] = $o;
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
        $this->test_correct = "Sections b (0), Class 2 (2)\n";
        $this->test_dump = $this->dumpClasses();
        $this->removeClass('Sections b');
        $this->removeObjFromClass($this->tdata['tree'][4], $this->tdata['classes'][1]);
        $this->test_correct .= "Class 2 (1)\n";
        $this->test_dump .= $this->dumpClasses();
        $this->deleteData();
        if($this->test_dump==$this->test_correct){ $this->test_log.="class: OK\n"; return TRUE; }
        else return PEAR::raiseError('ObjClasses::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\ndump:\n{$this->test_dump}\n</pre>\n");
    }

    /**
    *   install - create tables + initialize
    *
    **/
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->classTable} (
            id int not null,
            cname varchar(20)
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->classTable}_id_idx on {$this->classTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->classTable}_cname_idx on {$this->classTable} (cname)");

        $this->dbc->query("CREATE TABLE {$this->cmembTable} (
            objid int not null,
            cid int not null
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->cmembTable}_idx on {$this->cmembTable} (objid, cid)");
    }
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->classTable}");
        $this->dbc->query("DROP TABLE {$this->cmembTable}");
        parent::uninstall();
    }
}
?>