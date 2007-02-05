<?php
define('ALIBERR_MTREE', 10);

/**
 * M2tree class
 *
 * A class for tree hierarchy stored in db.
 *
 *  example config: example/conf.php<br>
 *  example minimal config:
 *   <pre><code>
 *    $CC_CONFIG = array(
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
 *   </code></pre>
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
class M2tree {

    /* ======================================================= public methods */
    /**
     * Add new object of specified type to the tree under specified parent
     * node
     *
     * @param string $p_name
     * 		mnemonic name for new object
     * @param string $p_type
     * 		type of new object
     * @param int $p_parentId
     * 		parent id
     * @return int|PEAR_Error
     * 		New id of inserted object
     */
    public static function AddObj($p_name, $p_type, $p_parentId = NULL)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if ( ($p_name == '') || ($p_type == '') ) {
            return $CC_DBC->raiseError("M2tree::addObj: Wrong name or type", ALIBERR_MTREE);
        }
        if (is_null($p_parentId)) {
            $p_parentId = M2tree::GetRootNode();
        }
        // changing name if the same is in the dest. folder:
        $xid = M2tree::GetObjId($p_name, $p_parentId);
        while (!is_null($xid) && !PEAR::isError($xid)) {
            $p_name .= "_";
            $xid = M2tree::GetObjId($p_name, $p_parentId);
        }
        if (PEAR::isError($xid)) {
            return $xid;
        }
        // insert new object record:
        $CC_DBC->query("BEGIN");
        $oid = $CC_DBC->nextId($CC_CONFIG['treeTable']."_id_seq");
        if (PEAR::isError($oid)) {
            return M2tree::_dbRollback($oid);
        }
        $escapedName = pg_escape_string($p_name);
        $escapedType = pg_escape_string($p_type);
        $r = $CC_DBC->query("INSERT INTO ".$CC_CONFIG['treeTable']." (id, name, type)"
            ." VALUES ($oid, '$escapedName', '$escapedType')");
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        $dataArr = array();
        // build data ($dataArr) for INSERT of structure records:
        for ($p=$p_parentId, $l=1; !is_null($p); $p = M2tree::GetParent($p), $l++) {
            $rid = $CC_DBC->nextId($CC_CONFIG['structTable']."_id_seq");
            if (PEAR::isError($rid)) {
                return M2tree::_dbRollback($rid);
            }
            $dataArr[] = array($rid, $oid, $p, $l);
        }
        // build and prepare INSERT command automatically:
        $pr = $CC_DBC->autoPrepare($CC_CONFIG['structTable'],
            array('rid', 'objid', 'parid', 'level'), DB_AUTOQUERY_INSERT);
        if (PEAR::isError($pr)) {
            return M2tree::_dbRollback($pr);
        }
        // execute INSERT command for $dataArr:
        $r = $CC_DBC->executeMultiple($pr, $dataArr);
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        $r = $CC_DBC->query("COMMIT");
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        return $oid;
    } // fn addObj


    /**
     * Remove specified object
     *
     * @param int $oid
     * 		object id to remove
     * @return TRUE|PEAR_Error
     */
    public static function RemoveObj($oid)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if ($oid == M2tree::GetRootNode()) {
            return $CC_DBC->raiseError("M2tree::RemoveObj: Can't remove root");
        }
        $dir = M2tree::GetDir($oid);
        if (PEAR::isError($dir)) {
            return $dir;
        }
        foreach ($dir as $k => $ch) {
            $r = M2tree::RemoveObj($ch['id']);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        $r = $CC_DBC->query("DELETE FROM ".$CC_CONFIG['treeTable']
                            ." WHERE id=$oid");
        if (PEAR::isError($r)) {
            return $r;
        }
        /* done by automatic reference trigger:
        $r = $CC_DBC->query("
            DELETE FROM ".$CC_CONFIG['structTable']."
            WHERE objid=$oid
        ");
        if (PEAR::isError($r)) return $r;
        */
        return TRUE;
    } // fn removeObj


    /**
     * Create copy of specified object and insert copy to new position
     * recursively
     *
     * @param int $oid
     * 		source object id
     * @param int $newParid
     * 		destination parent id
     * @param null $after
     * 		dummy argument for back-compatibility
     * @return int|PEAR_Error
     *      New id of inserted object
     */
    public static function CopyObj($oid, $newParid, $after=NULL)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if (TRUE === ($r = M2tree::IsChildOf($newParid, $oid, TRUE))) {
            return $CC_DBC->raiseError("M2tree::CopyObj: Can't copy into itself");
        }
        if (PEAR::isError($r)) {
            return $r;
        }
        // get name:
        $name = M2tree::GetObjName($oid);
        if (PEAR::isError($name)) {
            return $name;
        }
        // get parent id:
        $parid = M2tree::GetParent($oid);
        if (PEAR::isError($parid)) {
            return $parid;
        }
        if ($parid == $newParid) {
            $name .= "_copy";
        }
        // get type:
        $type = M2tree::GetObjType($oid);
        if (PEAR::isError($type)) {
            return $type;
        }
        // look for children:
        $dir = M2tree::GetDir($oid, $flds='id');
        if (PEAR::isError($dir)) {
            return $dir;
        }
        // insert aktual object:
        $nid = M2tree::AddObj($name, $type, $newParid);
        if (PEAR::isError($nid)) {
            return $nid;
        }
        // if no children:
        if (is_null($dir)) {
            return $nid;
        }
        // optionally insert children recursively:
        foreach ($dir as $k => $item) {
            $r = M2tree::CopyObj($item['id'], $nid);
            if (PEAR::isError($r)) {
                return $r;
            }
        }
        return $nid;
    } // fn copyObj


    /**
     * Move subtree to another node without removing/adding
     *
     * @param int $oid
     * @param int $newParid
     * @param null $after
     * 		dummy argument for back-compatibility
     *  @return boolean|PEAR_Error
     */
    public static function MoveObj($oid, $newParid, $after=NULL)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if (TRUE === (
                $r = M2tree::IsChildOf($newParid, $oid, TRUE)
                || $oid == $newParid
            )) {
            return $CC_DBC->raiseError("M2tree::MoveObj: Can't move into itself");
        }
        if (PEAR::isError($r)) {
            return $r;
        }
        // get name:
        $name0 = $name = M2tree::GetObjName($oid);
        if (PEAR::isError($name)) {
            return $name;
        }
        $CC_DBC->query("BEGIN");
        // cut it from source:
        $r = M2tree::_cutSubtree($oid);
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        // changing name if the same is in the dest. folder:
        for( ;
            $xid = M2tree::GetObjId($name, $newParid),
                !is_null($xid) && !PEAR::isError($xid);
            $name .= "_"
        );
        if (PEAR::isError($xid)) {
            return M2tree::_dbRollback($xid);
        }
        if ($name != $name0) {
            $r = M2tree::RenameObj($oid, $name);
            if (PEAR::isError($r)) {
                return M2tree::_dbRollback($r);
            }
        }
        // paste it to dest.:
        $r = M2tree::_pasteSubtree($oid, $newParid);
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        $r = $CC_DBC->query("COMMIT");
        if (PEAR::isError($r)) {
            return M2tree::_dbRollback($r);
        }
        return TRUE;
    } //fn moveObj


    /**
     * Rename of specified object
     *
     * @param int $oid
     * 		object id to rename
     * @param string $newName
     * 		new name
     * @return TRUE|PEAR_Error
     */
    public static function RenameObj($oid, $newName)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        // get parent id:
        $parid = M2tree::GetParent($oid);
        if (PEAR::isError($parid)) {
            return $parid;
        }
        // changing name if the same is in the folder:
        for( ;
            $xid = M2tree::GetObjId($newName, $parid),
                !is_null($xid) && !PEAR::isError($xid);
            $newName .= "_"
        );
        if (PEAR::isError($xid)) {
            return $xid;
        }
        $escapedName = pg_escape_string($newName);
        $sql = "UPDATE ".$CC_CONFIG['treeTable']." SET name='$escapedName' WHERE id=$oid";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn renameObj


    /* --------------------------------------------------------- info methods */
    /**
     * Search for child id by name in sibling set
     *
     * @param string $name
     * 		searched name
     * @param int $parId
     * 		parent id (default is root node)
     * @return int|null|PEAR_Error
     *      Child id (if found) or null
     */
    public static function GetObjId($name, $parId = null)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if ($name == '') {
            return null;
        }
        $escapedName = pg_escape_string($name);
        $parcond = (is_null($parId) ? "parid is null" :
            "parid='$parId' AND level=1");
        $sql = "SELECT id FROM ".$CC_CONFIG['treeTable']." t"
            ." LEFT JOIN ".$CC_CONFIG['structTable']." s ON id=objid"
            ." WHERE name='$escapedName' AND $parcond";
        $r = $CC_DBC->getOne($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return $r;
    } // fn getObjId


    /**
     * Get one value for object by id (default: get name)
     *
     * @param int $oid
     * @param string $fld
     * 		requested field (default: name)
     * @return string|PEAR_Error
     */
    public static function GetObjName($p_oid, $p_fld='name')
    {
        global $CC_CONFIG;
        global $CC_DBC;

        if (is_numeric($p_oid)) {
            $sql = "SELECT $p_fld FROM ".$CC_CONFIG['treeTable']
                    ." WHERE id=$p_oid";
            $r = $CC_DBC->getOne($sql);
            return $r;
        } else {
            return new PEAR_Error("M2tree::GetObjType: invalid argument given for oid: '$p_oid'");
        }
    } // fn getObjName


    /**
     * Get object type by id.
     *
     * @param int $oid
     * @return string|PEAR_Error
     */
    public static function GetObjType($p_oid)
    {
        if (is_numeric($p_oid)) {
            return M2tree::GetObjName($p_oid, 'type');
        } else {
            return new PEAR_Error("M2tree::GetObjType: invalid argument given for oid: '$p_oid'");
        }
    } // fn getObjType


    /**
     * Get parent id
     *
     * @param int $oid
     * @return int|PEAR_Error
     */
    public static function GetParent($p_oid)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $r = 0;
        if (is_numeric($p_oid)) {
            $sql = "SELECT parid FROM ".$CC_CONFIG['structTable']
                ." WHERE objid=$p_oid AND level=1";
            $r = $CC_DBC->getOne($sql);
        }
        return $r;
    } // fn getParent


    /**
     * Get array of nodes in object's path from root node
     *
     * @param int $oid
     * @param string $flds
     * @param boolean $withSelf
     * 		flag for include specified object to the path
     * @return array|PEAR_Error
     */
    public static function GetPath($oid, $flds='id', $withSelf=TRUE)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $sql = "SELECT $flds"
            ." FROM ".$CC_CONFIG['treeTable']
            ." LEFT JOIN ".$CC_CONFIG['structTable']." s ON id=parid"
            ." WHERE objid=$oid"
            ." ORDER BY coalesce(level, 0) DESC";
        $path = $CC_DBC->getAll($sql);
        if (PEAR::isError($path)) {
        	return $path;
        }
        if ($withSelf) {
            $sql = "SELECT $flds FROM ".$CC_CONFIG['treeTable']
                ." WHERE id=$oid";
            $r = $CC_DBC->getRow($sql);
            if (PEAR::isError($r)) {
            	return $r;
            }
            array_push($path, $r);
        }
        return $path;
    } // fn getPath


    /**
     * Get array of childnodes
     *
     * @param int $oid
     * @param string $flds
     * 		comma separated list of requested fields
     * @param string $order
     * 		fieldname for order by clause
     * @return array|PEAR_Error
     */
    public static function GetDir($oid, $flds='id', $order='name')
    {
        global $CC_CONFIG;
        global $CC_DBC;
        $sql = "SELECT $flds"
            ." FROM ".$CC_CONFIG['treeTable']
            ." INNER JOIN ".$CC_CONFIG['structTable']." ON id=objid AND level=1"
            ." WHERE parid=$oid"
            ." ORDER BY $order";
        $r = $CC_DBC->getAll($sql);
        return $r;
    } // fn getDir


    /**
     * Get level of object relatively to specified root
     *
     * @param int $oid
     * 		object id
     * @param string $flds
     * 		list of field names for select
     * @param int $rootId
     * 		root for relative levels
     *      (if NULL - use root of whole tree)
     * @return hash-array with field name/value pairs
     */
    public static function GetObjLevel($oid, $flds='level', $rootId=NULL)
    {
        global $CC_CONFIG;
        global $CC_DBC;
        if (is_null($rootId)) {
            $rootId = M2tree::GetRootNode();
        }
        $sql = "SELECT $flds"
            ." FROM ".$CC_CONFIG['treeTable']
            ." LEFT JOIN ".$CC_CONFIG['structTable']." s ON id=objid AND parid=$rootId"
            ." WHERE id=$oid";
        $re = $CC_DBC->getRow($sql);
        if (PEAR::isError($re)) {
            return $re;
        }
        $re['level'] = intval($re['level']);
        return $re;
    } // fn getObjLevel


    /**
     * Get subtree of specified node
     *
     * @param int $oid
     * 		default: root node
     * @param boolean $withRoot
     * 		include/exclude specified node
     * @param int $rootId
     * 		root for relative levels
     * @return array|PEAR_Error
     */
    public static function GetSubTree($oid=NULL, $withRoot=FALSE, $rootId=NULL)
    {
        if (is_null($oid)) {
            $oid = M2tree::GetRootNode();
        }
        if (is_null($rootId)) {
            $rootId = $oid;
        }
        $r = array();
        if ($withRoot) {
            $r[] = $re = M2tree::GetObjLevel($oid, 'id, name, level', $rootId);
        } else {
            $re = NULL;
        }
        if (PEAR::isError($re)) {
            return $re;
        }
        $dirarr = M2tree::GetDir($oid, 'id, level');
        if (PEAR::isError($dirarr)) {
            return $dirarr;
        }
        foreach ($dirarr as $k => $snod) {
            $re = M2tree::GetObjLevel($snod['id'], 'id, name, level', $rootId);
            if (PEAR::isError($re)) {
                return $re;
            }
            $r[] = $re;
            $r = array_merge($r, M2tree::GetSubTree($snod['id'], FALSE, $rootId));
        }
        return $r;
    } // fn getSubTree


    /**
     * Returns true if first object if child of second one
     *
     * @param int $oid
     * 		object id of tested object
     * @param int $parid
     * 		object id of parent
     * @param boolean $indirect
     * 		test indirect or only direct relation
     * @return boolean|PEAR_Error
     */
    public static function IsChildOf($oid, $parid, $indirect=FALSE)
    {
        if (!$indirect) {
            $paridD = M2tree::GetParent($oid);
            if (PEAR::isError($paridD)) {
                return $paridD;
            }
            return ($paridD == $parid);
        }
        $path = M2tree::GetPath($oid, 'id', FALSE);
        if (PEAR::isError($path)) {
            return $path;
        }
        $res = FALSE;
        foreach ($path as $k=>$item) {
            if ($item['id'] == $parid) {
                $res = TRUE;
            }
        }
        return $res;
    } // fn isChildOf


    /**
     * Get id of root node
     *
     * @return int|PEAR_Error
     */
    public static function GetRootNode()
    {
        global $CC_CONFIG;
        return M2tree::GetObjId($CC_CONFIG['RootNode']);
    } // fn getRootNode


    /**
     * Get all objects in the tree as array of hashes
     *
     * @return array|PEAR_Error
     */
    public static function GetAllObjects()
    {
        global $CC_CONFIG;
        global $CC_DBC;
        return $CC_DBC->getAll("SELECT * FROM ".$CC_CONFIG['treeTable']);
    } // fn getAllObjects


    /* ------------------------ info methods related to application structure */
    /* (this part should be redefined in extended class to allow
     * defining/modifying/using application structure)
     * (only very simple structure definition - in $CC_CONFIG - supported now)
     */

    /**
     * Get child types allowed by application definition
     *
     * @param string $type
     * @return array
     */
    public static function GetAllowedChildTypes($type)
    {
        global $CC_CONFIG;
        return $CC_CONFIG['objtypes'][$type];
    } // fn getAllowedChildTypes


    /* ==================================================== "private" methods */

    /**
     * Cut subtree of specified object from tree.
     * Preserve subtree structure.
     *
     * @param int $oid
     * 		object id
     * @return boolean
     */
    private static function _cutSubtree($oid)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        $lvl = M2tree::GetObjLevel($oid);
        if (PEAR::isError($lvl)) {
            return $lvl;
        }
        $lvl = $lvl['level'];
        // release downside structure
        $sql = "DELETE FROM ".$CC_CONFIG['structTable']
            ." WHERE rid IN ("
            ." SELECT s3.rid FROM ".$CC_CONFIG['structTable']." s1"
            ." INNER JOIN ".$CC_CONFIG['structTable']." s2 ON s1.objid=s2.objid"
            ." INNER JOIN ".$CC_CONFIG['structTable']." s3 ON s3.objid=s1.objid"
            ." WHERE (s1.parid=$oid OR s1.objid=$oid)"
            ." AND s2.parid=1 AND s3.level>(s2.level-$lvl) )";
        $r = $CC_DBC->query($sql);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // fn _cutSubtree


    /**
     * Paste subtree previously cut by _cutSubtree method into main tree
     *
     * @param int $oid
     * 		object id
     * @param int $newParid
     * 		destination object id
     * @return boolean
     */
    private static function _pasteSubtree($oid, $newParid)
    {
        global $CC_DBC;
        global $CC_CONFIG;
        $dataArr = array();
        // build data ($dataArr) for INSERT:
        foreach (M2tree::GetSubTree($oid, TRUE) as $o) {
            $l = intval($o['level'])+1;
            for ($p = $newParid; !is_null($p); $p = M2tree::GetParent($p), $l++) {
                $rid = $CC_DBC->nextId($CC_CONFIG['structTable']."_id_seq");
                if (PEAR::isError($rid)) {
                    return $rid;
                }
                $dataArr[] = array($rid, $o['id'], $p, $l);
            }
        }
        // build and prepare INSERT command automatically:
        $pr = $CC_DBC->autoPrepare($CC_CONFIG['structTable'],
            array('rid', 'objid', 'parid', 'level'), DB_AUTOQUERY_INSERT);
        if (PEAR::isError($pr)) {
            return $pr;
        }
        // execute INSERT command for $dataArr:
        $r = $CC_DBC->executeMultiple($pr, $dataArr);
        if (PEAR::isError($r)) {
            return $r;
        }
        return TRUE;
    } // _pasteSubtree


    /**
     * Do SQL rollback and return PEAR::error
     *
     * @param object|string $r
     * 		error object or error message
     * @return PEAR_Error
     */
    private static function _dbRollback($r)
    {
        global $CC_DBC;
        $CC_DBC->query("ROLLBACK");
        if (PEAR::isError($r)) {
            return $r;
        } elseif (is_string($r)) {
            $msg = basename(__FILE__)."::M2tree: $r";
        } else {
            $msg = basename(__FILE__)."::M2tree: unknown error";
        }
        return $CC_DBC->raiseError($msg, ALIBERR_MTREE, PEAR_ERROR_RETURN);
    } // fn _dbRollback


    /* ==================================================== auxiliary methods */

    /**
     * Human readable dump of subtree - for debug
     *
     * @param int $oid
     * 		start object id
     * @param string $indstr
     * 		indentation string
     * @param string $ind
     * 		actual indentation
     * @return string
     */
    public static function DumpTree($oid=NULL, $indstr='    ', $ind='',
        $format='{name}({id})', $withRoot=TRUE)
    {
        $r='';
        foreach ($st = M2tree::GetSubTree($oid, $withRoot) as $o) {
            if (PEAR::isError($st)) {
                return $st;
            }
            $r .= $ind.str_repeat($indstr, $o['level']).
                preg_replace(array('|\{name\}|', '|\{id\}|'),
                    array($o['name'], $o['id']), $format).
                "\n";
        }
        return $r;
    } // fn dumpTree


    /**
     * Clean up tree - delete all except the root node.
     * @return void|PEAR_Error
     */
    public static function reset()
    {
        global $CC_DBC;
        global $CC_CONFIG;
        $rid = M2tree::GetRootNode();
        if (PEAR::isError($rid)) {
            return $rid;
        }
        $r = $CC_DBC->query("DELETE FROM ".$CC_CONFIG['structTable']);
        if (PEAR::isError($r)) {
            return $r;
        }
        $r = $CC_DBC->query("DELETE FROM ".$CC_CONFIG['treeTable']." WHERE id<>$rid");
        if (PEAR::isError($r)) {
            return $r;
        }
    } // fn reset


    /**
     * Insert test data to the tree.
     * Only for compatibility with previous mtree - will be removed.
     *
     * @return array
     */
    public static function Test()
    {
        global $CC_DBC;
        global $CC_CONFIG;
        require_once("m2treeTest.php");
        $mt = new M2treeTest($CC_DBC, $CC_CONFIG);
        $r = $mt->_test();
        return $r;
    } // fn test


    /**
     * Insert test data to the tree.
     * Only for compatibility with previous mtree - will be removed.
     *
     * @return array
     */
    public static function TestData()
    {
        $o['root'] = M2tree::GetRootNode();
        $o['pa'] = M2tree::AddObj('Publication A', 'Publication', $o['root']);
        $o['i1'] = M2tree::AddObj('Issue 1', 'Issue', $o['pa']);
        $o['s1a'] = M2tree::AddObj('Section a', 'Section', $o['i1']);
        $o['s1b'] = M2tree::AddObj('Section b', 'Section', $o['i1']);
        $o['i2'] = M2tree::AddObj('Issue 2', 'Issue', $o['pa']);
        $o['s2a'] = M2tree::AddObj('Section a', 'Section', $o['i2']);
        $o['s2b'] = M2tree::AddObj('Section b', 'Section', $o['i2']);
        $o['t1'] = M2tree::AddObj('Title', 'Title', $o['s2b']);
        $o['s2c'] = M2tree::AddObj('Section c', 'Section', $o['i2']);
        $o['pb'] = M2tree::AddObj('Publication B', 'Publication', $o['root']);
        $tdata['tree'] = $o;
        return $tdata;
    } // fn testData

} // class M2Tree
?>