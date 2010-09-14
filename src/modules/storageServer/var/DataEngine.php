<?php
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
 * @package Campcaster
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
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
        $ops = array('full'=>"='%s'", 'partial'=>"ILIKE '%%%s%%'",
            'prefix'=>"ILIKE '%s%%'", '<'=>"< '%s'", '='=>"= '%s'",
            '>'=>"> '%s'", '<='=>"<= '%s'", '>='=>">= '%s'"
        );
        $whereArr = array();
        if (is_array($conditions)) {
            foreach ($conditions as $cond) {
                $columnName = BasicStor::xmlCategoryToDbColumn($cond['cat']);
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
                    $sqlCond = " {$columnName} {$opVal}\n";
                    $whereArr[] = $sqlCond;
                }
            }
        }
        return $whereArr;
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
    public function localSearch($criteria, $limit=0, $offset=0)
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

        $orderBySql = array();
        // $dataName contains the names of the metadata columns we want to
        // fetch.  It is indexed numerically starting from 1, and the value
        // in the array is the qualified name with ":" replaced with "_".
        // e.g. "dc:creator" becomes "dc_creator".
        foreach ($orderbyQns as $xmlTag) {
            $columnName = BasicStor::xmlCategoryToDbColumn($xmlTag);
            $orderBySql[] = $columnName;
        }

        // Build WHERE clause
        $whereClause = " WHERE (state='ready' OR state='edited')";
        if (!is_null($filetype)) {
        	$whereClause .= " AND (ftype='$filetype')";
        }
        if (count($whereArr) != 0) {
            if ($operator == 'and') {
                $whereClause .= " AND ((".join(") AND (", $whereArr)."))";
            } else {
                $whereClause .= " AND ((".join(") OR (", $whereArr)."))";
            }
        }

        // Final query
        $sql = "SELECT * "
                 . " FROM ".$CC_CONFIG["filesTable"]
                 . $whereClause;
        if ($orderby) {
           $sql .= " ORDER BY ".join(",", $orderBySql);
        }

        $countRowsSql = "SELECT COUNT(*) "
                 . " FROM ".$CC_CONFIG["filesTable"]
                 . $whereClause;
        $cnt = $CC_DBC->GetOne($countRowsSql);

        // Get the number of results
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
                'title' => $it['track_title'],
                'creator' => $it['artist_name'],
                'duration' => $it['length'],
                'length' => $it['length'],
                'source' => $it['album_title'],
                'track_num' => $it['track_number'],
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
        $category = strtolower($category);
        $columnName = BasicStor::xmlCategoryToDbColumn($category);
        if (is_null($columnName)) {
            return new PEAR_Error("DataEngine::browseCategory() -- could not map XML category to DB column.");
        }
        $sql = "SELECT DISTINCT $columnName FROM ".$CC_CONFIG["filesTable"];
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $countRowsSql = "SELECT COUNT(DISTINCT $columnName) FROM ".$CC_CONFIG["filesTable"];

        $cnt = $CC_DBC->GetOne($countRowsSql);
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


} // class DataEngine

?>