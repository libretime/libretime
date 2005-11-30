<?
define('ALIBERR_MTREE', 10);

/**
 *   M2tree class
 *
 *   class for tree hierarchy stored in db
 *
 *   example config: example/conf.php<br>
 *   example minimal config:
 *   <pre><code>
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
 *   </code></pre>
 *  @author  $Author$
 *  @version $Revision$
 *  @see ObjClasses
 *  Original author Tom Hlava
 */
class M2tree{
    /**
     *  Database object container
     */
    var $dbc;
    /**
     *  Configuration tree
     */
    var $config;
    /**
     *  Tree table name
     */
    var $treeTable;
    /**
     *  Structure table name
     */
    var $structTable;
    /**
     *  Root node name
     */
    var $rootNodeName;
    /**
     *  Constructor
     *
     *  @param dbc object
     *  @param config array
     *  @return this
     */
    function M2tree(&$dbc, $config)
    {
        $this->dbc =& $dbc;
        $this->config = $config;
        $this->treeTable = $config['tblNamePrefix'].'tree';
        $this->structTable = $config['tblNamePrefix'].'struct';
        $this->rootNodeName = $config['RootNode'];
    }

    /* ======================================================= public methods */
    /**
     *   Add new object of specified type to the tree under specified parent
     *   node
     *
     *   @param name string, mnemonic name for new object
     *   @param type string, type of new object
     *   @param parid int, optional, parent id
     *   @return int/err - new id of inserted object or PEAR::error
     */
    function addObj($name, $type, $parid=NULL)
    {
        if($name=='' || $type=='') return $this->dbc->raiseError(
            "M2tree::addObj: Wrong name or type", ALIBERR_MTREE
        );
        if(is_null($parid)) $parid = $this->getRootNode();
        // changing name if the same is in the dest. folder:
        for( ;
            $xid = $this->getObjId($name, $parid),
                !is_null($xid) && !$this->dbc->isError($xid);
            $name .= "_"
        );
        if($this->dbc->isError($xid)) return $xid;
        // insert new object record:
        $this->dbc->query("BEGIN");
        $oid = $this->dbc->nextId("{$this->treeTable}_id_seq");
        if($this->dbc->isError($oid)) return $this->_dbRollback($oid);
        $r = $this->dbc->query("
            INSERT INTO {$this->treeTable} (id, name, type)
            VALUES ($oid, '$name', '$type')
        "); 
        if($this->dbc->isError($r)) return $this->_dbRollback($r);
        $dataArr = array();
        // build data ($dataArr) for INSERT of structure records:
        for($p=$parid, $l=1; !is_null($p); $p=$this->getParent($p), $l++){
            $rid = $this->dbc->nextId("{$this->structTable}_id_seq");
            if($this->dbc->isError($rid)) return $this->_dbRollback($rid);
            $dataArr[] = array($rid, $oid, $p, $l);
        }
        // build and prepare INSERT command automatically:
        $pr = $this->dbc->autoPrepare($this->structTable,
            array('rid', 'objid', 'parid', 'level'), DB_AUTOQUERY_INSERT);
        if($this->dbc->isError($pr)) return $this->_dbRollback($pr);
        // execute INSERT command for $dataArr:
        $r = $this->dbc->executeMultiple($pr, $dataArr);
        if($this->dbc->isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        return $oid;
    }

    /**
     *   Remove specified object
     *
     *   @param oid int, object id to remove
     *   @return boolean/err - TRUE or PEAR::error
     */
    function removeObj($oid)
    {
        if($oid == $this->getRootNode()){
            return $this->dbc->raiseError(
                "M2tree::removeObj: Can't remove root"
            );
        }
        $dir = $this->getDir($oid);
        if($this->dbc->isError($dir)) return $dir;
        foreach($dir as $k=>$ch){
            $r = $this->removeObj($ch['id']);
            if($this->dbc->isError($r)) return $r;
        }
        $r = $this->dbc->query("
            DELETE FROM {$this->treeTable}
            WHERE id=$oid
        ");
        if($this->dbc->isError($r)) return $r;
        /* done by automatic reference trigger:
        $r = $this->dbc->query("
            DELETE FROM {$this->structTable}
            WHERE objid=$oid
        ");
        if($this->dbc->isError($r)) return $r;
        */
        return TRUE;
    }


    /**
     *  Create copy of specified object and insert copy to new position
     *  recursively
     *  
     *  @param oid int, source object id
     *  @param newParid int, destination parent id
     *  @param after null, dummy argument for back-compatibility
     *  @return int/err - new id of inserted object or PEAR::error
     */
    function copyObj($oid, $newParid, $after=NULL)
    {
        if(TRUE === ($r = $this->isChildOf($newParid, $oid, TRUE))){
            return $this->dbc->raiseError(
                "M2tree::copyObj: Can't copy into itself"
            );
        }
        if($this->dbc->isError($r)) return $r;
        // get name:
        $name = $this->getObjName($oid);
        if($this->dbc->isError($name)) return $name;
        // get parent id:
        $parid = $this->getParent($oid);
        if($this->dbc->isError($parid)) return $parid;
        if($parid == $newParid) $name .= "_copy";
        // get type:
        $type = $this->getObjType($oid);
        if($this->dbc->isError($type)) return $type;
        // look for children:
        $dir = $this->getDir($oid, $flds='id'); 
        if($this->dbc->isError($dir)) return $dir;
        // insert aktual object:
        $nid = $this->addObj($name, $type, $newParid);
        if($this->dbc->isError($nid)) return $nid;
        // if no children:
        if(is_null($dir)) return $nid;
        // optionally insert children recursively:
        foreach($dir as $k=>$item){
            $r = $this->copyObj($item['id'], $nid);
            if($this->dbc->isError($r)) return $r;
        }
        return $nid;
    }

    /**
     *  Move subtree to another node without removing/adding
     *
     *  @param oid int
     *  @param newParid int
     *  @param after null, dummy argument for back-compatibility
     *  @return boolean/err
     */
    function moveObj($oid, $newParid, $after=NULL)
    {
        if(TRUE === (
                $r = $this->isChildOf($newParid, $oid, TRUE)
                || $oid == $newParid
            )){
            return $this->dbc->raiseError(
                "M2tree::moveObj: Can't move into itself"
            );
        }
        if($this->dbc->isError($r)) return $r;
        // get name:
        $name0 = $name = $this->getObjName($oid);
        if($this->dbc->isError($name)) return $name;
        $this->dbc->query("BEGIN");
        // cut it from source:
        $r = $this->_cutSubtree($oid);
        if($this->dbc->isError($r)) return $this->_dbRollback($r);
        // changing name if the same is in the dest. folder:
        for( ;
            $xid = $this->getObjId($name, $newParid),
                !is_null($xid) && !$this->dbc->isError($xid);
            $name .= "_"
        );
        if($this->dbc->isError($xid)) return $this->_dbRollback($xid);
        if($name != $name0){
            $r = $this->renameObj($oid, $name);
            if($this->dbc->isError($r)) return $this->_dbRollback($r);
        }
        // paste it to dest.:
        $r = $this->_pasteSubtree($oid, $newParid);
        if($this->dbc->isError($r)) return $this->_dbRollback($r);
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $this->_dbRollback($r);
        return TRUE;
    }

    /**
     *   Rename of specified object
     *
     *   @param oid int, object id to rename
     *   @param newName string, new name
     *   @return boolean/err - True or PEAR::error
     */
    function renameObj($oid, $newName)
    {
        // get parent id:
        $parid = $this->getParent($oid);
        if($this->dbc->isError($parid)) return $parid;
        // changing name if the same is in the folder:
        for( ;
            $xid = $this->getObjId($newName, $parid),
                !is_null($xid) && !$this->dbc->isError($xid);
            $newName .= "_"
        );
        if($this->dbc->isError($xid)) return $xid;
        $r = $this->dbc->query("
            UPDATE {$this->treeTable}
            SET name='$newName'
            WHERE id=$oid
        ");
        if($this->dbc->isError($r)) return $r;
        return TRUE;
    }

    /* --------------------------------------------------------- info methods */
    /**
     *   Search for child id by name in sibling set
     *
     *   @param name string, searched name
     *   @param parId int, optional, parent id (default is root node)
     *   @return int/null/err - child id (if found) or null or PEAR::error
     */
    function getObjId($name, $parId=NULL)
    {
        if($name=='' && is_null($parId)) $name = $this->rootNodeName;
        $parcond = (is_null($parId) ? "parid is null" :
            "parid='$parId' AND level=1");
        $r = $this->dbc->getOne("
            SELECT id FROM {$this->treeTable} t
            LEFT JOIN {$this->structTable} s ON id=objid
            WHERE name='$name' AND $parcond"
        );
        if($this->dbc->isError($r)) return $r;
        return $r;
    }

    /**
     *   Get one value for object by id (default: get name)
     *
     *   @param oid int
     *   @param fld string, optional, requested field (default: name)
     *   @return string/err
     */
    function getObjName($oid, $fld='name')
    {
        $r = $this->dbc->getOne("
            SELECT $fld FROM {$this->treeTable}
            WHERE id=$oid
        ");
        return $r;
    }

    /**
     *   Get object type by id
     *
     *   @param oid int
     *   @return string/err
     */
    function getObjType($oid)
    {
        return $this->getObjName($oid, 'type');
    }

    /**
     *   Get parent id
     *
     *   @param oid int
     *   @return int/err
     */
    function getParent($oid)
    {
        $r = $this->dbc->getOne("
            SELECT parid FROM {$this->structTable}
            WHERE objid=$oid AND level=1
        ");
        return $r;
    }

    /**
     *   Get array of nodes in object's path from root node
     *
     *   @param oid int
     *   @param flds string, optional
     *   @param withSelf boolean - flag for include specified object to the path
     *   @return array/err
     */
    function getPath($oid, $flds='id', $withSelf=TRUE)
    {
        $path = $this->dbc->getAll("
            SELECT $flds
            FROM {$this->treeTable}
            LEFT JOIN {$this->structTable} s ON id=parid
            WHERE objid=$oid
            ORDER BY coalesce(level, 0) DESC
        ");
        if($this->dbc->isError($path)) return $path;
        if($withSelf){
            $r = $this->dbc->getRow("
                SELECT $flds FROM {$this->treeTable}
                WHERE id=$oid
            ");
            if($this->dbc->isError($r)) return $r;
            array_push($path, $r);
        }
        return $path;
    }

    /**
     *   Get array of childnodes
     *
     *   @param oid int
     *   @param flds string, optional, comma separated list of requested fields
     *   @param order string, optional, fieldname for order by clause
     *   @return array/err
     */
    function getDir($oid, $flds='id', $order='name')
    {
        $r = $this->dbc->getAll("
            SELECT $flds
            FROM {$this->treeTable}
            INNER JOIN {$this->structTable} ON id=objid AND level=1
            WHERE parid=$oid
            ORDER BY $order
        ");
        return $r;
    }

    /**
     *  Get level of object relatively to specified root
     *
     *  @param oid int, object id
     *  @param flds string, list of field names for select
     *      (optional - default: 'level')
     *  @param rootId int, root for relative levels
     *      (optional - default: NULL - use root of whole tree)
     *  @return hash-array with field name/value pairs
     */
    function getObjLevel($oid, $flds='level', $rootId=NULL)
    {
        if(is_null($rootId)) $rootId = $this->getRootNode();
        $re = $this->dbc->getRow("
            SELECT $flds
            FROM {$this->treeTable}
            LEFT JOIN {$this->structTable} s ON id=objid AND parid=$rootId
            WHERE id=$oid
        ");
        if($this->dbc->isError($re)) return $re;
        $re['level'] = intval($re['level']);
        return $re;
    }
    
    /**
     *  Get subtree of specified node
     *
     *  @param oid int, optional, default: root node
     *  @param withRoot boolean, optional, include/exclude specified node
     *  @param rootId int, root for relative levels, optional
     *  @return array/err
     */
    function getSubTree($oid=NULL, $withRoot=FALSE, $rootId=NULL)
    {
        if(is_null($oid)) $oid = $this->getRootNode();
        if(is_null($rootId)) $rootId = $oid;
        $r = array();
        if($withRoot){
            $r[] = $re = $this->getObjLevel($oid, 'id, name, level', $rootId);
        }else $re=NULL;
        if($this->dbc->isError($re)) return $re;
        $dirarr = $this->getDir($oid, 'id, level');
        if($this->dbc->isError($dirarr)) return $dirarr;
        foreach($dirarr as $k=>$snod)
        {
            $re = $this->getObjLevel($snod['id'], 'id, name, level', $rootId);
            if($this->dbc->isError($re)) return $re;
#            $re['level'] = intval($re['level'])+1;
            $r[] = $re;
            $r = array_merge($r,
                $this->getSubTree($snod['id'], FALSE, $rootId));
        }
        return $r;
    }

    /**
     *  Returns true if first object if child of second one
     *
     *  @param oid int, object id of tested object
     *  @param parid int, object id of parent
     *  @param indirect boolean, test indirect or only direct relation
     *  @return boolean
     */
    function isChildOf($oid, $parid, $indirect=FALSE)
    {
        if(!$indirect){
            $paridD = $this->getParent($oid);
            if($this->dbc->isError($paridD)) return $paridD;
            return ($paridD == $parid);
        }
        $path = $this->getPath($oid, 'id', FALSE);
        if($this->dbc->isError($path)) return $path;
        $res = FALSE;
        foreach($path as $k=>$item){ if($item['id'] == $parid) $res = TRUE; }
        return $res;
    }

    /**
     *   Get id of root node
     *
     *   @return int/err
     */
    function getRootNode()
    {
        return $this->getObjId($this->rootNodeName);
    }

    /**
     *   Get all objects in the tree as array of hashes
     *
     *   @return array/err
     */
    function getAllObjects()
    {
        return $this->dbc->getAll(
            "SELECT * FROM {$this->treeTable}"
        );
    }

    /* ------------------------ info methods related to application structure */
    /* (this part should be redefined in extended class to allow
     * defining/modifying/using application structure)
     * (only very simple structure definition - in $config - supported now)
     */
    
    /**
     *   Get child types allowed by application definition
     *
     *   @param type string
     *   @return array
     */
    function getAllowedChildTypes($type)
    {
        return $this->config['objtypes'][$type];
    }
    
    /* ==================================================== "private" methods */

    /**
     *  Cut subtree of specified object from tree.
     *  Preserve subtree structure.
     *
     *  @param oid int, object id
     *  @return boolean
     */
    function _cutSubtree($oid)
    {
        $lvl = $this->getObjLevel($oid);
        if($this->dbc->isError($lvl)) return $lvl;
        $lvl = $lvl['level'];
        // release downside structure
        $r = $this->dbc->query("
            DELETE FROM {$this->structTable}
            WHERE rid IN (
                SELECT s3.rid FROM {$this->structTable} s1
                INNER JOIN {$this->structTable} s2 ON s1.objid=s2.objid
                INNER JOIN {$this->structTable} s3 ON s3.objid=s1.objid
                WHERE (s1.parid=$oid OR s1.objid=$oid)
                    AND s2.parid=1 AND s3.level>(s2.level-$lvl)
            )
        ");
        if($this->dbc->isError($r)) return $r;
        return TRUE;
    }
    
    /**
     *  Paste subtree previously cut by _cutSubtree method into main tree
     *
     *  @param oid int, object id
     *  @param newParid int, destination object id
     *  @return boolean
     */
    function _pasteSubtree($oid, $newParid)
    {
        $dataArr = array();
        // build data ($dataArr) for INSERT:
        foreach($this->getSubTree($oid, TRUE) as $o){
            $l=intval($o['level'])+1;
            for($p=$newParid; !is_null($p); $p=$this->getParent($p), $l++){
                $rid = $this->dbc->nextId("{$this->structTable}_id_seq");
                if($this->dbc->isError($rid)) return $rid;
                $dataArr[] = array($rid, $o['id'], $p, $l);
            }
        }
        // build and prepare INSERT command automatically:
        $pr = $this->dbc->autoPrepare($this->structTable,
            array('rid', 'objid', 'parid', 'level'), DB_AUTOQUERY_INSERT);
        if($this->dbc->isError($pr)) return $pr;
        // execute INSERT command for $dataArr:
        $r = $this->dbc->executeMultiple($pr, $dataArr);
        if($this->dbc->isError($r)) return $r;
        return TRUE;
    }
    
    /**
     *   Do SQL rollback and return PEAR::error
     *
     *   @param r object/string, error object or error message
     *   @return err
     */
    function _dbRollback($r)
    {
        $this->dbc->query("ROLLBACK");
        if($this->dbc->isError($r)) return $r;
        elseif(is_string($r)){
            $msg = basename(__FILE__)."::".get_class($this).": $r";
        }else{
            $msg = basename(__FILE__)."::".get_class($this).": unknown error";
        }
        return $this->dbc->raiseError($msg, ALIBERR_MTREE, PEAR_ERROR_RETURN);
    }

    /* ==================================================== auxiliary methods */

    /**
     *   Human readable dump of subtree - for debug
     *
     *   @param oid int, start object id
     *   @param indstr string, indentation string
     *   @param ind string, aktual indentation
     *   @return string
     */
    function dumpTree($oid=NULL, $indstr='    ', $ind='',
        $format='{name}({id})', $withRoot=TRUE)
    {
        $r='';
        foreach($st = $this->getSubTree($oid, $withRoot) as $o){
            if($this->dbc->isError($st)) return $st;
            $r .= $ind.str_repeat($indstr, $o['level']).
                preg_replace(array('|\{name\}|', '|\{id\}|'),
                    array($o['name'], $o['id']), $format).
                "\n";
        }
        return $r;
    }

    /**
     *   Create tables + initialize root node
     *
     */
    function install()
    {
        $r = $this->dbc->query("BEGIN");
        if(PEAR::isError($r)) return $r;
        $r = $this->dbc->query("CREATE TABLE {$this->treeTable} (
            id int not null PRIMARY KEY,
            name varchar(255) not null default'',
            -- parid int,
            type varchar(255) not null default'',
            param varchar(255)
        )");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->createSequence("{$this->treeTable}_id_seq");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE UNIQUE INDEX {$this->treeTable}_id_idx
            ON {$this->treeTable} (id)");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE INDEX {$this->treeTable}_name_idx
            ON {$this->treeTable} (name)");
        if($this->dbc->isError($r)) return $r;

        $r = $this->dbc->query("CREATE TABLE {$this->structTable} (
            rid int not null PRIMARY KEY,
            objid int not null REFERENCES {$this->treeTable} ON DELETE CASCADE,
            parid int not null REFERENCES {$this->treeTable} ON DELETE CASCADE,
            level int
        )");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->createSequence("{$this->structTable}_id_seq");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE UNIQUE INDEX {$this->structTable}_rid_idx
            ON {$this->structTable} (rid)");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE INDEX {$this->structTable}_objid_idx
            ON {$this->structTable} (objid)");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE INDEX {$this->structTable}_parid_idx
            ON {$this->structTable} (parid)");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("CREATE INDEX {$this->structTable}_level_idx
            ON {$this->structTable} (level)");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("
            CREATE UNIQUE INDEX {$this->structTable}_objid_level_idx
            ON {$this->structTable} (objid, level)
        ");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("
            CREATE UNIQUE INDEX {$this->structTable}_objid_parid_idx
            ON {$this->structTable} (objid, parid)
        ");
        if($this->dbc->isError($r)) return $r;

        $oid = $this->dbc->nextId("{$this->treeTable}_id_seq");
        if($this->dbc->isError($oid)) return $oid;
        $r = $this->dbc->query("
            INSERT INTO {$this->treeTable}
                (id, name, type)
            VALUES
                ($oid, '{$this->rootNodeName}', 'RootNode')
        ");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("COMMIT");
        if(PEAR::isError($r)) return $r;
    }

    /**
     *  Drop all tables and sequencies
     *
     */
    function uninstall()
    {
        $this->dbc->query("DROP TABLE {$this->structTable}");
        $this->dbc->dropSequence("{$this->structTable}_id_seq");
        $this->dbc->query("DROP TABLE {$this->treeTable}");
        $this->dbc->dropSequence("{$this->treeTable}_id_seq");
    }

    /**
     *   Uninstall and install
     *
     */
    function reinstall()
    {
        $this->uninstall();
        $this->install();
    }

    /**
     *  Clean up tree - delete all except root node
     *
     */
    function reset()
    {
        $rid = $this->getRootNode();
        if($this->dbc->isError($rid)) return $rid;
        $r = $this->dbc->query("DELETE FROM {$this->structTable}");
        if($this->dbc->isError($r)) return $r;
        $r = $this->dbc->query("DELETE FROM {$this->treeTable} WHERE id<>$rid");
        if($this->dbc->isError($r)) return $r;
    }

    /**
     *  Insert test data to the tree.
     *  Only for compatibility with previous mtree - will be removed.
     *
     *  @return array
     */
    function test()
    {
        require_once"m2treeTest.php";
        $mt = &new M2treeTest($this->dbc, $this->config);
        $r = $mt->_test();
        return $r;
    }
    
    /**
     *  Insert test data to the tree.
     *  Only for compatibility with previous mtree - will be removed.
     *
     *  @return array
     */
    function testData()
    {
        $o['root'] = $this->getRootNode();
        $o['pa'] = $this->addObj('Publication A', 'Publication', $o['root']);
        $o['i1'] = $this->addObj('Issue 1', 'Issue', $o['pa']);
        $o['s1a'] = $this->addObj('Section a', 'Section', $o['i1']);
        $o['s1b'] = $this->addObj('Section b', 'Section', $o['i1']);
        $o['i2'] = $this->addObj('Issue 2', 'Issue', $o['pa']);
        $o['s2a'] = $this->addObj('Section a', 'Section', $o['i2']);
        $o['s2b'] = $this->addObj('Section b', 'Section', $o['i2']);
        $o['t1'] = $this->addObj('Title', 'Title', $o['s2b']);
        $o['s2c'] = $this->addObj('Section c', 'Section', $o['i2']);
        $o['pb'] = $this->addObj('Publication B', 'Publication', $o['root']);
        $this->tdata['tree'] = $o;
    }
}
?>
