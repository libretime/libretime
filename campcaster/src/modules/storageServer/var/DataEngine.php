<?php
define('USE_INTERSECT', TRUE);

require_once("XML/Util.php");

/**
 *  DataEngine class
 *
 *  Format of search criteria: hash, with following structure:<br>
 *   <ul>
 *     <li>filetype - string, type of searched files,
 *       meaningful values: 'audioclip', 'webstream', 'playlist', 'all'</li>
 *     <li>operator - string, type of conditions join
 *       (any condition matches / all conditions match),
 *       meaningful values: 'and', 'or', ''
 *       (may be empty or ommited only with less then 2 items in
 *       &quot;conditions&quot; field)
 *     </li>
 *     <li>orderby : string - metadata category for sorting (optional)
 *          or array of strings for multicolumn orderby
 *          [default: dc:creator, dc:source, dc:title]
 *     </li>
 *     <li>desc : boolean - flag for descending order (optional)
 *          or array of boolean for multicolumn orderby
 *          (it corresponds to elements of orderby field)
 *          [default: all ascending]
 *     </li>
 *     <li>conditions - array of hashes with structure:
 *       <ul>
 *           <li>cat - string, metadata category name</li>
 *           <li>op - string, operator - meaningful values:
 *               'full', 'partial', 'prefix', '=', '&lt;',
 *               '&lt;=', '&gt;', '&gt;='</li>
 *           <li>val - string, search value</li>
 *       </ul>
 *     </li>
 *   </ul>
 *  <p>
 *  Format of search/browse results: hash, with following structure:<br>
 *   <ul>
 *      <li>results : array of gunids have found</li>
 *      <li>cnt : integer - number of matching items</li>
 *   </ul>
 *
 * @author Tomas Hlava <th@red2head.com>
 * @author Paul Baranowski <paul@paulbaranowski.org>
 * @version  $Revision$
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2006 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.campware.org
 * @see MetaData
 * @see StoredFile
 */
class DataEngine {

    /**
     * Constructor
     *
     * @param BasicStor $gb
     */
    public function __construct(&$gb)
    {
        $this->gb =& $gb;
        $this->filetypes = array(
            'all'=>NULL,
            'audioclip'=>'audioclip',
            'webstream'=>'webstream',
            'playlist'=>'playlist',
        );
    }


    /**
     * Method returning array with where-parts of sql queries
     *
     * @param array $conditions
     * 		See 'conditions' field in search criteria format
     *      definition in DataEngine class documentation
     * @return array
     * 		array of strings - WHERE-parts of SQL queries
     */
    private function _makeWhereArr($conditions)
    {
        $ops = array('full'=>"='%s'", 'partial'=>"like '%%%s%%'",
            'prefix'=>"like '%s%%'", '<'=>"< '%s'", '='=>"= '%s'",
            '>'=>"> '%s'", '<='=>"<= '%s'", '>='=>">= '%s'"
        );
        $whereArr = array();
        if (is_array($conditions)) {
            foreach ($conditions as $cond) {
                $catQn = $cond['cat'];
                $op = strtolower($cond['op']);
                $value = strtolower($cond['val']);
                if (!empty($value)) {
                    $splittedQn = XML_Util::splitQualifiedName($catQn);
                    $catNs = $splittedQn['namespace'];
                    $cat = $splittedQn['localPart'];
                    $opVal = sprintf($ops[$op], pg_escape_string($value));
                    // retype for timestamp value
                    if ($cat == 'mtime') {
                        switch ($op) {
                            case 'partial':
                            case 'prefix':
                            	break;
                            default:
                                $retype = "::timestamp with time zone";
                                $opVal = "$retype $opVal$retype";
                        }
                    }
                    // escape % for sprintf in whereArr construction:
                    $cat = str_replace("%", "%%", $cat);
                    $opVal = str_replace("%", "%%", $opVal);
                    $sqlCond =
                        " %s.predicate = '{$cat}' AND".
                        " %s.objns='_L' AND %s.predxml='T'".
                        " AND lower(%s.object) {$opVal}\n";
                    if (!is_null($catNs)) {
                        $catNs  = str_replace("%", "%%", $catNs);
                        $sqlCond = " %s.predns = '{$catNs}' AND $sqlCond";
                    }
                    $whereArr[] = $sqlCond;
                }
            }
        }
        return $whereArr;
    }


    /**
     * Method returning SQL query for search/browse with AND operator
     * (without using INTERSECT command)
     *
     * @param string $fldsPart
     * 		fields part of SQL query
     * @param array $whereArr
     * 		array of WHERE-parts
     * @param string $fileCond
     * 		Condition for files table
     * @param boolean $browse
     * 		TRUE if browse vals required instead of gunids
     * @param string $brFldNs
     * 		Namespace prefix of category for browse
     * @param string $brFld
     * 		Category for browse
     * @return string
     * 		query
     */
    private function _makeAndSqlWoIntersect($fldsPart, $whereArr, $fileCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        $innerBlocks = array();
        foreach ($whereArr as $i => $v) {
            $whereArr[$i] = sprintf($v, "md$i", "md$i", "md$i", "md$i", "md$i");
            $lastTbl = ($i==0 ? "f" : "md".($i-1));
            $innerBlocks[] = "INNER JOIN ".$CC_CONFIG['mdataTable']." md$i ON md$i.gunid = $lastTbl.gunid\n";
        }
        // query construcion:
        $sql =  "SELECT $fldsPart FROM ".$CC_CONFIG['filesTable']." f ".join("", $innerBlocks);
        if ($browse) {
            $sql .= "INNER JOIN ".$CC_CONFIG['mdataTable']." br".
                "\n ON br.gunid = f.gunid AND br.objns='_L'".
                " AND br.predicate='{$brFld}' AND br.predxml='T'";
            if (!is_null($brFldNs)) {
            	$sql .= " AND br.predns='{$brFldNs}'";
            }
            $sql .= "\n";
        }
        if (!is_null($fileCond)) {
        	$whereArr[] = " $fileCond";
        }
        if (count($whereArr) > 0) {
        	$sql .= "WHERE\n".join("  AND\n", $whereArr);
        }
        if ($browse) {
        	$sql .= "\nORDER BY br.object";
        }
        return $sql;
    }


    /**
     * Method returning SQL query for search/browse with AND operator
     * (using INTERSECT command)
     *
     * @param string $fldsPart
     * 		Fields part of sql query
     * @param array $whereArr
     * 		Array of where-parts
     * @param string $fileCond
     * 		Condition for files table
     * @param boolean $browse
     * 		true if browse vals required instead of gunids
     * @param string $brFldNs
     * 		namespace prefix of category for browse
     * @param string $brFld
     * 		category for browse
     * @return string
     * 		query
     */
    private function _makeAndSql($fldsPart, $whereArr, $fileCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        if (!USE_INTERSECT) {
        	return $this->_makeAndSqlWoIntersect($fldsPart, $whereArr, $fileCond, $browse, $brFldNs, $brFld);
        }
        $isectBlocks = array();
        foreach ($whereArr as $i => $v) {
            $whereArr[$i] = sprintf($v, "md$i", "md$i", "md$i", "md$i", "md$i");
            $isectBlocks[] =
                " SELECT gunid FROM ".$CC_CONFIG['mdataTable']." md$i \n".
                " WHERE {$whereArr[$i]}";
        }
        // query construcion:
        if (count($isectBlocks) > 0) {
            $isectBlock =
                "FROM (".join("INTERSECT\n", $isectBlocks).") sq \n".
                " INNER JOIN ".$CC_CONFIG['filesTable']." f ON f.gunid = sq.gunid";
        } else {
            $isectBlock = "FROM ".$CC_CONFIG['filesTable']." f \n";
        }
        $sql = "SELECT $fldsPart ".$isectBlock;
        if ($browse) {
            $sql .= " INNER JOIN ".$CC_CONFIG['mdataTable']." br ON br.gunid = f.gunid \n".
            		" WHERE br.objns='_L' AND br.predxml='T' AND br.predicate='{$brFld}'";
            if (!is_null($brFldNs)) {
            	$sql .= " AND br.predns='{$brFldNs}'";
            }
            $glue = " AND";
        } else {
        	$glue = " WHERE ";
        }
        if (!is_null($fileCond)) {
        	$sql .= " $glue $fileCond";
        }
        if ($browse) {
        	$sql .= " ORDER BY br.object";
        }
        return $sql;
    }

    /**
     * Method returning SQL query for search/browse with OR operator
     *
     * @param string $fldsPart
     * 		Fields part of sql query
     * @param array $whereArr
     * 		Array of where-parts
     * @param string $fileCond
     * 		Condition for files table
     * @param boolean $browse
     * 		True if browse vals required instead of gunids
     * @param string $brFldNs
     * 		Namespace prefix of category for browse
     * @param string $brFld
     * 		Category for browse
     * @return string
     * 		query
     */
    private function _makeOrSql($fldsPart, $whereArr, $fileCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        //$whereArr[] = " FALSE\n";
        foreach ($whereArr as $i => $v) {
            $whereArr[$i] = sprintf($v, "md", "md", "md", "md", "md");
        }
        // query construcion:
        $sql = "SELECT $fldsPart FROM ".$CC_CONFIG['filesTable']." f ";
        if ($browse) {
            $sql .= "INNER JOIN ".$CC_CONFIG['mdataTable']." br".
                "\n ON br.gunid = f.gunid AND br.objns='_L'".
                " AND br.predxml='T' AND br.predicate='{$brFld}'";
            if (!is_null($brFldNs)) {
            	$sql .= " AND br.predns='{$brFldNs}'";
            }
            $sql .= "\n";
        }
        if (count($whereArr) > 0) {
            $sql .= "INNER JOIN ".$CC_CONFIG['mdataTable']." md ON md.gunid=f.gunid\n";
            $sql .= "WHERE\n(\n".join("  OR\n", $whereArr).")";
            $glue = " AND";
        } else {
        	$glue = "WHERE";
        }
        if (!is_null($fileCond)) {
        	$sql .= "$glue $fileCond";
        }
        if ($browse) {
        	$sql .= "\nORDER BY br.object";
        }
        return $sql;
    }


    /**
     * Search in local metadata database.
     *
     * @param array $cri
     * 		Search criteria see DataEngine class documentation
     * @param int $limit
     * 		Limit for result arrays (0 means unlimited)
     * @param int $offset
     * 		Starting point (0 means without offset)
     * @return array
     * 		hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching gunids
     *              of files have been found
     */
    public function localSearch($cri, $limit=0, $offset=0)
    {
        $res = $this->_localGenSearch($cri, $limit, $offset);
        // if (PEAR::isError($res)) return $res;
        return $res;
    }


    /**
     * Search in local metadata database, more general version.
     *
     * @param hash $criteria
     * 		Search criteria see DataEngine class documentation
     * @param int $limit
     * 		Limit for result arrays (0 means unlimited)
     * @param int $offset
     * 		Starting point (0 means without offset)
     * @return array
     * 		arrays of hashes, fields:
     *       cnt : integer - number of matching gunids
     *              of files have been found
     *       results : array of hashes:
     *          gunid: string
     *          type: string - audioclip | playlist | webstream
     *          title: string - dc:title from metadata
     *          creator: string - dc:creator from metadata
     *          source: string - dc:source from metadata
     *          length: string - dcterms:extent in extent format
     *     OR (in browse mode)
     *       results: array of strings - browsed values
     */
    private function _localGenSearch($criteria, $limit=0, $offset=0)
    {
        global $CC_CONFIG, $CC_DBC;

        // Input values
        $filetype = (isset($criteria['filetype']) ? $criteria['filetype'] : 'all');
        $filetype = strtolower($filetype);
        if (!array_key_exists($filetype, $this->filetypes)) {
            return PEAR::raiseError(
                'DataEngine::_localGenSearch: unknown filetype in search criteria'
            );
        }
        $filetype = $this->filetypes[$filetype];
        $operator = (isset($criteria['operator']) ? $criteria['operator'] : 'and');
        $operator = strtolower($operator);
        $conditions = (isset($criteria['conditions']) ? $criteria['conditions'] : array());

        // Create the WHERE clause - this is the actual search part
        $whereArr = $this->_makeWhereArr($conditions);

        // Metadata values to fetch
        $metadataNames = array('dc:creator', 'dc:source', 'ls:track_num', 'dc:title', 'dcterms:extent');

        // Order by clause
        $orderby = TRUE;
        $orderByAllowedValues = array('dc:creator', 'dc:source', 'dc:title', 'dcterms:extent', "ls:track_num");
        $orderByDefaults = array('dc:creator', 'dc:source', 'dc:title');
        if ((!isset($criteria['orderby']))
        	|| (is_array($criteria['orderby']) && (count($criteria['orderby'])==0))) {
      		// default ORDER BY
            // PaulB: track number removed because it doesnt work yet because
            // if track_num is not an integer (e.g. bad metadata like "1/20",
            // or if the field is blank) the SQL statement gives an error.
            //$orderbyQns  = array('dc:creator', 'dc:source', 'ls:track_num', 'dc:title');
            $orderbyQns = $orderByDefaults;
        } else {
            // ORDER BY clause is given in the parameters.

            // Convert the parameter to an array if it isnt already.
            $orderbyQns = $criteria['orderby'];
            if (!is_array($orderbyQns)) {
                $orderbyQns = array($orderbyQns);
            }

            // Check that it has valid ORDER BY values, if not, revert
            // to the default ORDER BY values.
            foreach ($orderbyQns as $metadataTag) {
                if (!in_array($metadataTag, $orderByAllowedValues)) {
                    $orderbyQns = $orderByDefaults;
                    break;
                }
            }
        }

        $descA = (isset($criteria['desc']) ? $criteria['desc'] : NULL);
        if (!is_array($descA)) {
            $descA = array($descA);
        }

        // This section of code adds the metadata values to the results.
        // Each metadata value is LEFT JOINED to the results, and has the
        // name of its qualified name with ":" replaced with "_".
        // Here we also make the ORDER BY clause.
        $metadataJoinSql = array();
        $orderBySql = array();
        // $dataName contains the names of the metadata columns we want to
        // fetch.  It is indexed numerically starting from 1, and the value
        // in the array is the qualified name with ":" replaced with "_".
        // e.g. "dc:creator" becomes "dc_creator".
        $dataName = array();
        foreach ($metadataNames as $j => $qname) {
            $i = $j + 1;
            $obSplitQn = XML_Util::splitQualifiedName($qname);
            $obNs = $obSplitQn['namespace'];
            $obLp = $obSplitQn['localPart'];
            $desc = (isset($descA[$j]) ? $descA[$j] : NULL);
            $retype = ($obLp == 'mtime' ? '::timestamp with time zone' : '' );
            $metadataJoinSql[] =
                "LEFT JOIN ".$CC_CONFIG['mdataTable']." m$i\n".
                "  ON m$i.gunid = sq2.gunid AND m$i.predicate='$obLp'".
                " AND m$i.objns='_L' AND m$i.predxml='T'".
                (!is_null($obNs)? " AND m$i.predns='$obNs'":'');

            $dataName[$qname] = str_replace(":", "_", $qname);
            if (in_array($qname, $orderbyQns)) {
                $orderBySql[] = $dataName[$qname].$retype.($desc? ' DESC':'');
            }
        }

        if (!$orderby) {
            $fldsPart = "DISTINCT to_hex(f.gunid)as gunid, f.ftype, f.id ";
        } else {
            $fldsPart = "DISTINCT f.gunid, f.ftype, f.id ";
        }

        $fileCond = "f.state='ready'";
        if (!is_null($filetype)) {
        	$fileCond .= " AND f.ftype='$filetype'";
        }
        if ($operator == 'and') {
            $sql = $this->_makeAndSql($fldsPart, $whereArr, $fileCond, false);
        } else {
            $sql = $this->_makeOrSql($fldsPart, $whereArr, $fileCond, false);
        }

        // the actual values to fetch
        if ($orderby) {
            $tmpSql = "SELECT to_hex(sq2.gunid)as gunid, sq2.ftype, sq2.id";
            $i = 1;
            foreach ($metadataNames as $qname) {
                // Special case for track number because if we use text
                // sorting of this value, then 10 comes right after 1.
                // So we convert track number to an integer for ordering.

                // PaulB: see my note above about why this is commented out.
                //if ($qname == "ls:track_num") {
                //    $tmpSql .= ", CAST(m$i.object as integer) as ls_track_num";
                //} else {
                $tmpSql .= ", m$i.object as ".$dataName[$qname];
                //}
                $i++;
            }

            $tmpSql .= "\nFROM (\n$sql\n)sq2\n".
                    join("\n", $metadataJoinSql).
                   "ORDER BY ".join(",", $orderBySql)."\n";
            $sql = $tmpSql;
        }

        // Get the number of results
        $cnt = $this->_getNumRows($sql);
        if (PEAR::isError($cnt)) {
        	return $cnt;
        }

        // Get actual results
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $res = $CC_DBC->getAll($sql.$limitPart);
        if (PEAR::isError($res)) {
        	return $res;
        }
        if (!is_array($res)) {
        	$res = array();
        }
        $eres = array();
        foreach ($res as $it) {
            $gunid = StoredFile::NormalizeGunid($it['gunid']);
            $eres[] = array(
            	'id' => $it['id'],
                'gunid' => $gunid,
                'type' => strtolower($it['ftype']),
                'title' => $it['dc_title'],
                'creator' => $it['dc_creator'],
                'duration' => $it['dcterms_extent'],
                'length' => $it['dcterms_extent'],
                'source' => $it['dc_source'],
                'track_num' => $it['ls_track_num'],
            );
        }
        return array('results'=>$eres, 'cnt'=>$cnt);
    }


    /**
     * Return values of specified metadata category
     *
     * @param string $category
     * 		metadata category name, with or without namespace prefix
     *      (dc:title, author)
     * @param int $limit
     * 		limit for result arrays (0 means unlimited)
     * @param int $offset
     * 		starting point (0 means without offset)
     * @param array $criteria
     * @return array
     * 		hash, fields:
     *       results : array with found values
     *       cnt : integer - number of matching values
     */
    public function browseCategory($category, $limit=0, $offset=0, $criteria=NULL)
    {
        global $CC_CONFIG, $CC_DBC;
        //$category = strtolower($category);
        $r = XML_Util::splitQualifiedName($category);
        $catNs = $r['namespace'];
        $cat = $r['localPart'];
        if (is_array($criteria) && count($criteria) > 0) {
            return $this->_browseCategory($criteria, $limit, $offset, $catNs, $cat);
        }
        $sqlCond = "m.predicate='$cat' AND m.objns='_L' AND m.predxml='T'";
        if (!is_null($catNs)) {
            $sqlCond = "m.predns = '{$catNs}' AND  $sqlCond";
        }
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $sql =
            "SELECT DISTINCT m.object FROM ".$CC_CONFIG['mdataTable']." m\n".
            "WHERE $sqlCond";
        $cnt = $this->_getNumRows($sql);
        if (PEAR::isError($cnt)) {
        	return $cnt;
        }
        $res = $CC_DBC->getCol($sql.$limitPart);
        if (PEAR::isError($res)) {
        	return $res;
        }
        if (!is_array($res)) {
        	$res = array();
        }
        return array('results'=>$res, 'cnt'=>$cnt);
    }


    /**
     * Fetching the list of metadata values for a particular category.
     *
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     * @param string $brFldNs
     * 		Namespace prefix of category for browse
     * @param string $brFld
     * 		Metadata category identifier for browse.
     * @return array|PEAR_Error
     */
    private function _browseCategory($criteria, $limit=0, $offset=0,
        $brFldNs=NULL, $brFld=NULL)
    {
        global $CC_CONFIG, $CC_DBC;

        // Input values
        $filetype = (isset($criteria['filetype']) ? $criteria['filetype'] : 'all');
        $filetype = strtolower($filetype);
        if (!array_key_exists($filetype, $this->filetypes)) {
            return PEAR::raiseError(
                'DataEngine::_browseCategory: unknown filetype in search criteria'
            );
        }
        $filetype = $this->filetypes[$filetype];
        $operator = (isset($criteria['operator']) ? $criteria['operator'] : 'and');
        $operator = strtolower($operator);
        $conditions = (isset($criteria['conditions']) ? $criteria['conditions'] : array());

        // Create the WHERE clause - this is the actual search part
        $whereArr = $this->_makeWhereArr($conditions);

        $fldsPart = "DISTINCT br.object as txt";
        $fileCond = "f.state='ready'";
        if (!is_null($filetype)) {
        	$fileCond .= " AND f.ftype='$filetype'";
        }
        if ($operator == 'and') {
            $sql = $this->_makeAndSql($fldsPart, $whereArr, $fileCond, true, $brFldNs, $brFld);
        } else {
            $sql = $this->_makeOrSql($fldsPart, $whereArr, $fileCond, true, $brFldNs, $brFld);
        }

        // Get the number of results
        $cnt = $this->_getNumRows($sql);
        if (PEAR::isError($cnt)) {
        	return $cnt;
        }

        // Get actual results
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $res = $CC_DBC->getAll($sql.$limitPart);
        if (PEAR::isError($res)) {
        	return $res;
        }
        if (!is_array($res)) {
        	$res = array();
        }
        $eres = array();
        foreach ($res as $it) {
            $eres[] = $it['txt'];
        }
        return array('results'=>$eres, 'cnt'=>$cnt);
    }


    /**
     * Get number of rows in query result
     *
     * @param string $query
     * 		SQL query
     * @return int
     * 		Number of rows in query result
     */
    private function _getNumRows($query)
    {
        global $CC_CONFIG, $CC_DBC;
        $rh = $CC_DBC->query($query);
        if (PEAR::isError($rh)) {
        	return $rh;
        }
        $cnt = $rh->numRows();
        if (PEAR::isError($cnt)) {
        	return $cnt;
        }
        $rh->free();
        return $cnt;
    }

} // class DataEngine

?>