<?php
require_once "m2tree.php";

/**
 * ObjClass class
 *
 * A class for 'object classes' handling - i.e. groups of object in tree
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
class ObjClasses extends M2tree {
	/**
	 * The name of the database table ("PREFIXclasses")
	 *
	 * @var string
	 */
    public $classTable;

    /**
     * The name of a database table ("PREFIXcmemb")
     *
     * @var string
     */
    public $cmembTable;


    /**
     * @param object $dbc
     * @param array $config
     * @return this
     */
    public function __construct(&$dbc, $config)
    {
        parent::__construct($dbc, $config);
        $this->classTable = $config['tblNamePrefix'].'classes';
        $this->cmembTable = $config['tblNamePrefix'].'cmemb';
    }


    /* ======================================================= public methods */

    /**
     * Add new class of objects
     *
     * @param string $cname
     * @return id/error
     */
    public function addClass($cname)
    {
        $id = $this->dbc->nextId("{$this->treeTable}_id_seq");
        if (PEAR::isError($id)) {
        	return $id;
        }
        $r = $this->dbc->query("
            INSERT INTO {$this->classTable} (id, cname)
            VALUES ($id, '$cname')
        ");
        if (PEAR::isError($r)) {
        	return $r;
        }
        return $id;
    }


    /**
     * Remove class by name
     *
     * @param string $cname
     * @return boolean/err
     */
    public function removeClass($cname)
    {
        $cid = $this->getClassId($cname);
        if (PEAR::isError($cid)) {
        	return($cid);
        }
        return $this->removeClassById($cid);
    }


    /**
     * Remove class by id
     *
     * @param int $cid
     * @return boolean/err
     */
    public function removeClassById($cid)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable}
            WHERE cid=$cid");
        if (PEAR::isError($r)) {
        	return $r;
        }
        $r = $this->dbc->query("DELETE FROM {$this->classTable}
            WHERE id=$cid");
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Add object to class
     *
     * @param int $cid
     * @param int $oid
     * @return boolean/err
     */
    public function addObj2Class($cid, $oid)
    {
        $r = $this->dbc->query("INSERT INTO {$this->cmembTable} (cid, objid)
            VALUES ($cid, $oid)");
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Remove object from class
     *
     * @param int $oid
     * @param int $cid, optional, default: remove obj from all classes
     * @return boolean/err
     */
    public function removeObjFromClass($oid, $cid=NULL)
    {
        $r = $this->dbc->query("DELETE FROM {$this->cmembTable}
            WHERE objid=$oid".(is_null($cid)? '':" AND cid=$cid"));
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /* ---------------------------------------------------------- object tree */

    /**
     * Remove object from all classes and remove object itself
     *
     * @param int $id
     * @return boolean/err
     */
    public function removeObj($id)
    {
        $r = $this->removeObjFromClass($id);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return parent::removeObj($id);
    }


    /* --------------------------------------------------------- info methods */

    /**
     * Get class id from name
     *
     * @param string $cname
     * @return int/err
     */
    public function getClassId($cname)
    {
        $cname = pg_escape_string($cname);
        return $this->dbc->getOne($query = "SELECT id FROM {$this->classTable}
            WHERE cname='$cname'");
    }


    /**
     * Get class name from id
     *
     * @param int $id
     * @return string/err
     */
    public function getClassName($id)
    {
        return $this->dbc->getOne(
            $query = "SELECT cname FROM {$this->classTable} WHERE id=$id");
    }


    /**
     * Return true is object is class
     *
     * @param int $id
     * @return boolean/err
     */
    public function isClass($id)
    {
        $r = $this->dbc->getOne("SELECT count(*) FROM {$this->classTable}
            WHERE id=$id");
        if (PEAR::isError($r)) {
        	return $r;
        }
        return ($r > 0);
    }


    /**
     * Return all classes
     *
     * @return array/err
     */
    public function getClasses()
    {
        return $this->dbc->getAll("SELECT * FROM {$this->classTable}");
    }


    /**
     * Return all objects in class
     *
     * @param int $id
     * @return array/err
     */
    public function listClass($id)
    {
        return $this->dbc->getAll("
            SELECT t.* FROM {$this->cmembTable} cm, {$this->treeTable} t
            WHERE cm.cid=$id AND cm.objid=t.id");
    }


    /* =============================================== test and debug methods */

    /**
     * Dump all classes for debug
     *
     * @param string $indstr
     * 		indentation string
     * @param string $ind
     * 		actual indentation
     * @return string
     */
    public function dumpClasses($indstr='    ', $ind='')
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
     * Delete all classes and membeship records
     * @return void
     */
    public function deleteData()
    {
        $this->dbc->query("DELETE FROM {$this->cmembTable}");
        $this->dbc->query("DELETE FROM {$this->classTable}");
        parent::reset();
    }


    /**
     * Insert test data
     *
     */
    public function testData()
    {
        parent::testData();
        $o['cl_sa'] = $this->addClass('Sections a');
        $o['cl2'] = $this->addClass('Class 2');
        $this->addObj2Class($o['cl_sa'], $this->tdata['tree']['s1a']);
        $this->addObj2Class($o['cl_sa'], $this->tdata['tree']['s2a']);
        $this->addObj2Class($o['cl2'], $this->tdata['tree']['t1']);
        $this->addObj2Class($o['cl2'], $this->tdata['tree']['pb']);
        $this->tdata['classes'] = $o;
    }


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
        $this->test_correct = "Sections a (2), Class 2 (2)\n";
        $this->test_dump = $this->dumpClasses();
        $this->removeClass('Sections a');
        $this->removeObjFromClass($this->tdata['tree']['pb'],
            $this->tdata['classes']['cl2']);
        $this->test_correct .= "Class 2 (1)\n";
        $this->test_dump .= $this->dumpClasses();
        $this->deleteData();
        if ($this->test_dump==$this->test_correct) {
            $this->test_log.="class: OK\n"; return TRUE;
        } else {
        	return PEAR::raiseError(
            'ObjClasses::test:', 1, PEAR_ERROR_DIE, '%s'.
            "<pre>\ncorrect:\n{$this->test_correct}\n".
            "dump:\n{$this->test_dump}\n</pre>\n");
        }
    }


    /**
     * Create tables + initialize
     *
     */
    public function install()
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
     * Drop tables etc.
     *
     */
    public function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->classTable}");
        $this->dbc->query("DROP TABLE {$this->cmembTable}");
        parent::uninstall();
    }
} // class ObjClasses
?>