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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/alib/var/class.php,v $

------------------------------------------------------------------------------*/
require_once "mtree.php";

/**
 *  ObjClass class
 *
 *  class for 'object classes' handling - i.e. groups of object in tree
 *
 *  @author  $Author: tomas $
 *  @version $Revision: 1.4 $
 *  @see Mtree
 *  @see Subj
 */
class ObjClasses extends Mtree{
    var $classTable;
    var $cmembTable;
    /**
     *  Constructor
     *
     *  @param dbc object
     *  @param config array
     *  @return this
     */
    function ObjClasses(&$dbc, $config)
    {
        parent::MTree($dbc, $config);
        $this->classTable = $config['tblNamePrefix'].'classes';
        $this->cmembTable = $config['tblNamePrefix'].'cmemb';
    }

    /* ======================================================= public methods */

    /**
     *   Add new class of objects
     *
     *   @param cname string
     *   @return id/error
     */
    function addClass($cname)
    {
        $id = $this->dbc->nextId("{$this->treeTable}_id_seq");
        if(PEAR::isError($id)) return $id;
        $r = $this->dbc->query("
            INSERT INTO {$this->classTable} (id, cname)
            VALUES ($id, '$cname')
        ");
        if(PEAR::isError($r)) return $r;
        return $id;
    }

    /**
     *   Remove class by name
     *
     *   @param cname string
     *   @return boolean/err
     */
    function removeClass($cname)
    {
        $cid = $this->getClassId($cname);
        if(PEAR::isError($cid)) return($cid);
        return $this->removeClassById($cid);
    }

    /**
     *   Remove class by id
     *
     *   @param cid int
     *   @return boolean/err
     */
    function removeClassById($cid)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable}
            WHERE cid=$cid");
        if(PEAR::isError($r)) return $r;
        $r = $this->dbc->query("DELETE FROM {$this->classTable}
            WHERE id=$cid");
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
     *   Add object to class
     *
     *   @param cid int
     *   @param oid int
     *   @return boolean/err
     */
    function addObj2Class($cid, $oid)
    {
        $r = $this->dbc->query("INSERT INTO {$this->cmembTable} (cid, objid)
            VALUES ($cid, $oid)");
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /**
     *   Remove object from class
     *
     *   @param oid int
     *   @param cid int, optional, default: remove obj from all classes
     *   @return boolean/err
     */
    function removeObjFromClass($oid, $cid=NULL)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable}
            WHERE objid=$oid".(is_null($cid)? '':" AND cid=$cid"));
        if(PEAR::isError($r)) return $r;
        return TRUE;
    }

    /* ---------------------------------------------------------- object tree */

    /**
     *   Remove object from all classes and remove object itself
     *
     *   @param id int
     *   @return boolean/err
     */
    function removeObj($id)
    {
        $r = $this->removeObjFromClass($id);
        if(PEAR::isError($r)) return $r;
        return parent::removeObj($id);
    }

    /* --------------------------------------------------------- info methods */

    /**
     *   Get class id from name
     *
     *   @param cname string
     *   @return int/err
     */
    function getClassId($cname)
    {
        return $this->dbc->getOne($query = "SELECT id FROM {$this->classTable}
            WHERE cname='$cname'");
    }

    /**
     *   Get class name from id
     *
     *   @param id int
     *   @return string/err
     */
    function getClassName($id)
    {
        return $this->dbc->getOne(
            $query = "SELECT cname FROM {$this->classTable}WHERE id=$id");
    }

    /**
     *   Return true is object is class
     *
     *   @param id int
     *   @return boolean/err
     */
    function isClass($id)
    {
        $r = $this->dbc->getOne("SELECT count(*) FROM {$this->classTable}
            WHERE id=$id");
        if(PEAR::isError($r)) return $r;
        return ($r > 0);
    }

    /**
     *   Return all classes
     *
     *   @return array/err
     */
    function getClasses()
    {
        return $this->dbc->getAll("SELECT * FROM {$this->classTable}");
    }

    /**
     *   Return all objects in class
     *
     *   @param id int
     *   @return array/err
     */
    function listClass($id)
    {
        return $this->dbc->getAll("
            SELECT t.* FROM {$this->cmembTable} cm, {$this->treeTable} t
            WHERE cm.cid=$id AND cm.objid=t.id");
    }

    /* =============================================== test and debug methods */

    /**
     *   Dump all classes fot debug
     *
     *   @param indstr string    // indentation string
     *   @param ind string       // aktual indentation
     *   @return string
     */
    function dumpClasses($indstr='    ', $ind='')
    {
        $r = $ind.join(', ', array_map(
            create_function('$v', 'return "{$v[\'cname\']} ({$v[\'cnt\']})";'),
            $this->dbc->getAll("
                SELECT cname, count(cm.objid)as cnt FROM {$this->classTable} c
                LEFT JOIN {$this->cmembTable} cm ON c.id=cm.cid
                GROUP BY cname, c.id ORDER BY c.id
            ")
        ))."\n";
        return $r;
    }
    
    /**
     *   Delete all classes and membeship records
     *
     */
    function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->cmembTable}");
        $this->dbc->query("DELETE FROM {$this->classTable}");
        parent::deleteData();
    }
    /**
     *   Insert test data
     *
     */
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
     *   Make basic test
     *
     */
    function test()
    {
        if(PEAR::isError($p = parent::test())) return $p;
        $this->deleteData();
        $this->testData();
        $this->test_correct = "Sections b (0), Class 2 (2)\n";
        $this->test_dump = $this->dumpClasses();
        $this->removeClass('Sections b');
        $this->removeObjFromClass($this->tdata['tree'][4],
            $this->tdata['classes'][1]);
        $this->test_correct .= "Class 2 (1)\n";
        $this->test_dump .= $this->dumpClasses();
        $this->deleteData();
        if($this->test_dump==$this->test_correct){
            $this->test_log.="class: OK\n"; return TRUE; 
        }else return PEAR::raiseError(
            'ObjClasses::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\n".
            "dump:\n{$this->test_dump}\n</pre>\n");
    }

    /**
     *   Create tables + initialize
     *
     */
    function install()
    {
        parent::install();
        $this->dbc->query("CREATE TABLE {$this->classTable} (
            id int not null PRIMARY KEY,
            cname varchar(20)
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->classTable}_id_idx
            ON {$this->classTable} (id)");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->classTable}_cname_idx
            ON {$this->classTable} (cname)");

        $this->dbc->query("CREATE TABLE {$this->cmembTable} (
            objid int not null,
            cid int not null
        )");
        $this->dbc->query("CREATE UNIQUE INDEX {$this->cmembTable}_idx
            ON {$this->cmembTable} (objid, cid)");
    }
    /**
     *   Drop tables etc.
     *
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->classTable}");
        $this->dbc->query("DROP TABLE {$this->cmembTable}");
        parent::uninstall();
    }
}
?>