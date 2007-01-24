<?php
require_once("M2tree.php");

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
class ObjClasses {
//class ObjClasses extends M2tree {

    /* ======================================================= public methods */

    /**
     * Add new class of objects
     *
     * @param string $cname
     * @return id|PEAR_Error
     */
    public static function AddClass($cname)
    {
        global $CC_CONFIG, $CC_DBC;
        $id = $CC_DBC->nextId($CC_CONFIG['treeTable']."_id_seq");
        if (PEAR::isError($id)) {
        	return $id;
        }
        $r = $CC_DBC->query("
            INSERT INTO ".$CC_CONFIG['classTable']." (id, cname)
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
     * @return boolean|PEAR_Error
     */
//    public static function RemoveClass($cname)
//    {
//        $cid = ObjClasses::GetClassId($cname);
//        if (PEAR::isError($cid)) {
//        	return($cid);
//        }
//        return ObjClasses::RemoveClassById($cid);
//    }


    /**
     * Remove class by id
     *
     * @param int $cid
     * @return boolean|PEAR_Error
     */
    public static function RemoveClassById($cid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "DELETE FROM ".$CC_CONFIG['cmembTable']
            ." WHERE cid=$cid";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
        	return $r;
        }
        $sql = "DELETE FROM ".$CC_CONFIG['classTable']
            ." WHERE id=$cid";
        $r = $CC_DBC->query($sql);
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
     * @return TRUE|PEAR_Error
     */
    public static function AddObjectToClass($cid, $oid)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "INSERT INTO ".$CC_CONFIG['cmembTable']." (cid, objid)"
            ." VALUES ($cid, $oid)";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return TRUE;
    }


    /**
     * Remove object from class
     *
     * @param int $oid
     * @param int $cid, default: remove obj from all classes
     * @return TRUE|PEAR_Error
     */
    public static function RemoveObjectFromClass($oid, $cid=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "DELETE FROM ".$CC_CONFIG['cmembTable']
            ." WHERE objid=$oid".(is_null($cid)? '':" AND cid=$cid");
        $r = $CC_DBC->query($sql);
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
     * @return boolean|PEAR_Error
     */
    public static function RemoveObj($id)
    {
        $r = ObjClasses::RemoveObjectFromClass($id);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return M2tree::RemoveObj($id);
    }


    /* --------------------------------------------------------- info methods */

    /**
     * Get class id from name
     *
     * @param string $cname
     * @return int|PEAR_Error
     */
    public static function GetClassId($cname)
    {
        global $CC_CONFIG, $CC_DBC;
        $cname = pg_escape_string($cname);
        $sql = "SELECT id FROM ".$CC_CONFIG['classTable']
            ." WHERE cname='$cname'";
        return $CC_DBC->getOne($sql);
    }


    /**
     * Get class name from id
     *
     * @param int $id
     * @return string|PEAR_Error
     */
    public static function GetClassName($id)
    {
        global $CC_DBC, $CC_CONFIG;
        $sql = "SELECT cname FROM ".$CC_CONFIG['classTable']." WHERE id=$id";
        return $CC_DBC->getOne($sql);
    }


    /**
     * Return true is object is class
     *
     * @param int $id
     * @return boolean|PEAR_Error
     */
    public static function IsClass($id)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT count(*) FROM ".$CC_CONFIG['classTable']
            ." WHERE id=$id";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
        	return $r;
        }
        return ($r > 0);
    }


    /**
     * Return all classes
     *
     * @return array|PEAR_Error
     */
    public static function GetClasses()
    {
        global $CC_CONFIG, $CC_DBC;
        return $CC_DBC->getAll("SELECT * FROM ".$CC_CONFIG['classTable']);
    }


    /**
     * Return all objects in class
     *
     * @param int $id
     * @return array|PEAR_Error
     */
    public static function ListClass($id)
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT t.* FROM ".$CC_CONFIG['cmembTable']." cm, ".$CC_CONFIG['treeTable']." t"
            ." WHERE cm.cid=$id AND cm.objid=t.id";
        return $CC_DBC->getAll($sql);
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
    public static function DumpClasses($indstr='    ', $ind='')
    {
        global $CC_CONFIG, $CC_DBC;
        $sql = "SELECT cname, count(cm.objid)as cnt FROM ".$CC_CONFIG['classTable']." c"
            ." LEFT JOIN ".$CC_CONFIG['cmembTable']." cm ON c.id=cm.cid"
            ." GROUP BY cname, c.id ORDER BY c.id";
        $r = $ind.join(', ', array_map(
            create_function('$v', 'return "{$v[\'cname\']} ({$v[\'cnt\']})";'),
            $CC_DBC->getAll($sql)
        ))."\n";
        return $r;
    }


    /**
     * Delete all classes and membership records.
     * @return void
     */
    public static function DeleteData()
    {
        global $CC_CONFIG, $CC_DBC;
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['cmembTable']);
        $CC_DBC->query("DELETE FROM ".$CC_CONFIG['classTable']);
        M2tree::reset();
    }


    /**
     * Insert test data
     *
     */
    public static function TestData()
    {
        $tdata = M2tree::testData();
        $o['cl_sa'] = ObjClasses::AddClass('Sections a');
        $o['cl2'] = ObjClasses::AddClass('Class 2');
        ObjClasses::AddObjectToClass($o['cl_sa'], $tdata['tree']['s1a']);
        ObjClasses::AddObjectToClass($o['cl_sa'], $tdata['tree']['s2a']);
        ObjClasses::AddObjectToClass($o['cl2'], $tdata['tree']['t1']);
        ObjClasses::AddObjectToClass($o['cl2'], $tdata['tree']['pb']);
        $tdata['classes'] = $o;
        return $tdata;
    }


    /**
     * Make basic test
     *
     */
    public static function Test()
    {
        $p = M2tree::test();
        if (PEAR::isError($p)) {
        	return $p;
        }
        ObjClasses::DeleteData();
        ObjClasses::TestData();
        $test_correct = "Sections a (2), Class 2 (2)\n";
        $test_dump = ObjClasses::DumpClasses();
        //$this->removeClass('Sections a');
        ObjClasses::RemoveObjectFromClass($tdata['tree']['pb'],
            $tdata['classes']['cl2']);
        $test_correct .= "Class 2 (1)\n";
        $test_dump .= ObjClasses::DumpClasses();
        ObjClasses::DeleteData();
        if ($test_dump == $test_correct) {
            $test_log .= "class: OK\n";
            return TRUE;
        } else {
        	return PEAR::raiseError(
                'ObjClasses::test:', 1, PEAR_ERROR_DIE, '%s'.
                "<pre>\ncorrect:\n{$test_correct}\n".
                "dump:\n{$test_dump}\n</pre>\n");
        }
    }

} // class ObjClasses
?>