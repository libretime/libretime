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
    Version  : $Revision: 1.3 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/DataEngine.php,v $

------------------------------------------------------------------------------*/
define(USE_INTERSECT, TRUE);

require_once "XML/Util.php";

/**
 *  DataEngine class
 *
 *  Format of search criteria: hash, with following structure:<br>
 *   <ul>
 *     <li>filetype - string, type of searched files,
 *       meaningful values: 'audioclip', 'playlist'</li>
 *     <li>operator - string, type of conditions join
 *       (any condition matches / all conditions match), 
 *       meaningful values: 'and', 'or', ''
 *       (may be empty or ommited only with less then 2 items in
 *       &quot;conditions&quot; field)
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
 *  @see MetaData
 *  @see StoredFile
 */
class DataEngine{

    /**
     *  Constructor
     *
     *  @param gb reference to BasicStor object
     *  @return this
     */
    function DataEngine(&$gb)
    {
        $this->gb         =& $gb;
        $this->dbc        =& $gb->dbc;
        $this->mdataTable =  $gb->mdataTable;
        $this->filesTable =  $gb->filesTable;
        $this->filetypes  =
            array('audioclip'=>'audioclip', 'playlist'=>'playlist');
    }

    /**
     *  Get metadata element value
     *
     *  @param id int, virt.file's local id
     *  @param category string, metadata element name
     *  @return array of matching records
     */
    function getMetadataValue($id, $category)
    {
        $gunid = $this->gb->_gunidFromId($id);
        if(PEAR::isError($gunid)) return $gunid;
        if(is_null($gunid)){
            return PEAR::raiseError(
                "BasicStor::bsGetMdataValue: file not found ($id)",
                GBERR_NOTF
            );
        }
        $catOrig = strtolower($category);
        // handle predicate namespace shortcut
        if(preg_match("|^([^:]+):([^:]+)$|", $catOrig, $catOrigArr)){
            $catNs = $catOrigArr[1]; $cat = $catOrigArr[2];
        }else{ $catNs=NULL; $cat=$catOrig; }
        $cond = "
                gunid=x'$gunid'::bigint AND objns='_L' AND
                predicate='$cat'
        ";
        if(!is_null($catNs)) $cond .= " AND predns='$catNs'";
        $sql = "
            SELECT object
            FROM {$this->mdataTable}
            WHERE $cond
        ";
        $res = $this->dbc->getCol($sql);
        if(PEAR::isError($res)) return $res;
        return $res;
    }

    /**
     *  Method returning array with where-parts of sql queries
     *
     *  @param conditions array - see conditions field in search criteria format
     *      definition in DataEngine class documentation
     *  @return array of strings - where-parts of SQL qyeries
     */
    function _makeWhereArr($conditions)
    {
        $ops = array('full'=>"='%s'", 'partial'=>"like '%%%s%%'", 'prefix'=>"like '%s%%'",
            '<'=>"< '%s'", '='=>"= '%s'", '>'=>"> '%s'", '<='=>"<= '%s'", '>='=>">= '%s'"
        );
        $whereArr   = array();
        if(is_array($conditions)){
            foreach($conditions as $cond){
                $catQn  = strtolower($cond['cat']);
                $op     = strtolower($cond['op']);
                $value  = strtolower($cond['val']);
                $splittedQn = XML_Util::splitQualifiedName($catQn);
                $catNs  = $splittedQn['namespace'];
                $cat    = $splittedQn['localPart'];
                $opVal  = sprintf($ops[$op], addslashes($value));
                // escape % for sprintf in whereArr construction:
                $cat    = str_replace("%", "%%", $cat);
                $opVal  = str_replace("%", "%%", $opVal);
                $sqlCond =
                    " %s.predicate = '{$cat}' AND".
                    " %s.objns='_L' AND lower(%s.object) {$opVal}\n";
                if(!is_null($catNs)){
                    $catNs  = str_replace("%", "%%", $catNs);
                    $sqlCond = " %s.predns = '{$catNs}' AND $sqlCond";
                }
                $whereArr[] = $sqlCond;
            }
        }
        return $whereArr;
    }
    
    /**
     *  Method returning SQL query for search/browse with AND operator
     *  (without using INTERSECT command)
     *
     *  @param fldsPart string - fields part of sql query
     *  @param whereArr array - array of where-parts
     *  @param ftypeCond string - condition for ftype
     *  @param browse boolean - true if browse vals required instead of gunids
     *  @param brFldNs string - namespace prefix of category for browse
     *  @param brFld string - category for browse
     *  @return query string
     */
    function _makeAndSqlWoIntersect($fldsPart, $whereArr, $ftypeCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        $innerBlocks = array();
        foreach($whereArr as $i=>$v){
            $whereArr[$i] = sprintf($v, "md$i", "md$i", "md$i", "md$i");
            $lastTbl = ($i==0 ? "f" : "md".($i-1));
            $innerBlocks[] = 
                "INNER JOIN {$this->mdataTable} md$i ON md$i.gunid = $lastTbl.gunid\n";
        }
        // query construcion:
        $sql =  "SELECT $fldsPart\nFROM {$this->filesTable} f\n".join("", $innerBlocks);
        if($browse){
            $sql .= "INNER JOIN {$this->mdataTable} br".
                "\n ON br.gunid = f.gunid AND br.objns='_L' AND br.predicate='{$brFld}'";
            if(!is_null($brFldNs)) $sql .= " AND br.predns='{$brFldNs}'";
            $sql .= "\n";
        }
        if(!is_null($ftypeCond)) $whereArr[] = " $ftypeCond";
        if(count($whereArr)>0) $sql .= "WHERE\n".join("  AND\n", $whereArr);
        if($browse) $sql .= "\nORDER BY br.object";
        return $sql;
    }
    
    /**
     *  Method returning SQL query for search/browse with AND operator
     *  (using INTERSECT command)
     *
     *  @param fldsPart string - fields part of sql query
     *  @param whereArr array - array of where-parts
     *  @param ftypeCond string - condition for ftype
     *  @param browse boolean - true if browse vals required instead of gunids
     *  @param brFldNs string - namespace prefix of category for browse
     *  @param brFld string - category for browse
     *  @return query string
     */
    function _makeAndSql($fldsPart, $whereArr, $ftypeCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        if(!USE_INTERSECT)  return $this->_makeAndSqlWoIntersect(
            $fldsPart, $whereArr, $ftypeCond, $browse, $brFldNs, $brFld);
        $isectBlocks = array();
        foreach($whereArr as $i=>$v){
            $whereArr[$i] = sprintf($v, "md$i", "md$i", "md$i", "md$i");
            $isectBlocks[] = 
                " SELECT gunid FROM {$this->mdataTable} md$i\n".
                " WHERE\n {$whereArr[$i]}";
        }
        // query construcion:
        if(count($isectBlocks)>0){
            $isectBlock = 
                "FROM\n(\n".join("INTERSECT\n", $isectBlocks).") sq\n".
                "INNER JOIN {$this->filesTable} f ON f.gunid = sq.gunid";
        }else{
            $isectBlock = "FROM {$this->filesTable} f";
        }
        $sql =
            "SELECT $fldsPart\n".$isectBlock;
        if($browse){
            $sql .= "\nINNER JOIN {$this->mdataTable} br ON br.gunid = f.gunid\n".
            "WHERE br.objns='_L' AND br.predicate='{$brFld}'";
            if(!is_null($brFldNs)) $sql .= " AND br.predns='{$brFldNs}'";
            $glue = " AND";
        }else{ $glue = "WHERE";}
        if(!is_null($ftypeCond)) $sql .= "\n$glue $ftypeCond";
        if($browse) $sql .= "\nORDER BY br.object";
        return $sql;
    }
    
    /**
     *  Method returning SQL query for search/browse with OR operator
     *
     *  @param fldsPart string - fields part of sql query
     *  @param whereArr array - array of where-parts
     *  @param ftypeCond string - condition for ftype
     *  @param browse boolean - true if browse vals required instead of gunids
     *  @param brFldNs string - namespace prefix of category for browse
     *  @param brFld string - category for browse
     *  @return query string
     */
    function _makeOrSql($fldsPart, $whereArr, $ftypeCond, $browse,
        $brFldNs=NULL, $brFld=NULL)
    {
        $whereArr[] = " FALSE\n";
        foreach($whereArr as $i=>$v){
            $whereArr[$i] = sprintf($v, "md", "md", "md", "md");
        }
        // query construcion:
        $sql = "SELECT $fldsPart\nFROM {$this->filesTable} f\n";
        if($browse){
            $sql .= "INNER JOIN {$this->mdataTable} br".
                "\n ON br.gunid = f.gunid AND br.objns='_L' AND br.predicate='{$brFld}'";
            if(!is_null($brFldNs)) $sql .= " AND br.predns='{$brFldNs}'";
            $sql .= "\n";
        }
        if(count($whereArr)>0){
            $sql .= "INNER JOIN {$this->mdataTable} md ON md.gunid=f.gunid\n";
            $sql .= "WHERE\n(\n".join("  OR\n", $whereArr).")";
            $glue = " AND";
        }else{ $glue = "WHERE"; }
        if(!is_null($ftypeCond)) $sql .= "$glue $ftypeCond";
        if($browse) $sql .= "\nORDER BY br.object";
        return $sql;
    }
    
    /**
     *  Search in local metadata database.
     *
     *  @param cri hash, search criteria see DataEngine class documentation
     *  @param limit int, limit for result arrays (0 means unlimited)
     *  @param offset int, starting point (0 means without offset)
     *  @return hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching gunids 
     *              of files have been found
     */
    function localSearch($cri, $limit=0, $offset=0)
    {
        $res = $this->_localGenSearch($cri, $limit, $offset);
        // if(PEAR::isError($res)) return $res;
        return $res;
    }
    
    /**
     *  Search in local metadata database, more general version.
     *
     *  @param criteria hash, search criteria see DataEngine class documentation
     *  @param limit int, limit for result arrays (0 means unlimited)
     *  @param offset int, starting point (0 means without offset)
     *  @param brFldNs string - namespace prefix of category for browse
     *  @param brFld string, metadata category identifier for browse
     *  @return hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching gunids 
     *              of files have been found
     */
    function _localGenSearch($criteria, $limit=0, $offset=0,
        $brFldNs=NULL, $brFld=NULL)
    {
        $filetype   = $this->filetypes[strtolower($criteria['filetype'])];
#        if(is_null($filetype)) $filetype = 
        $operator   = strtolower($criteria['operator']);
        $whereArr   = $this->_makeWhereArr($criteria['conditions']);
        $browse     = !is_null($brFld);
        if(!$browse){
            $fldsPart = "DISTINCT to_hex(f.gunid)as gunid";
        }else{
            $fldsPart = "DISTINCT br.object";
        }
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $ftypeCond = "f.ftype='$filetype'";
        if(is_null($filetype)) $ftypeCond = NULL;
        if($operator == 'and'){     // operator: and
            $sql = $this->_makeAndSql(
                $fldsPart, $whereArr, $ftypeCond, $browse, $brFldNs, $brFld);
        }else{          // operator: or
            $sql = $this->_makeOrSql(
                $fldsPart, $whereArr, $ftypeCond, $browse, $brFldNs, $brFld);
        }
        // echo "\n---\n$sql\n---\n";
        $cnt = $this->_getNumRows($sql);
        if(PEAR::isError($cnt)) return $cnt;
        $res = $this->dbc->getCol($sql.$limitPart);
        if(PEAR::isError($res)) return $res;
        if(!is_array($res)) $res = array();
        if(!$browse){
            $res = array_map(array("StoredFile", "_normalizeGunid"), $res);
        }
        return array('results'=>$res, 'cnt'=>$cnt);
    }

    /**
     *  Return values of specified metadata category
     *
     *  @param category string, metadata category name
     *          with or without namespace prefix (dc:title, author)
     *  @param limit int, limit for result arrays (0 means unlimited)
     *  @param offset int, starting point (0 means without offset)
     *  @param criteria hash
     *  @return hash, fields:
     *       results : array with gunid strings
     *       cnt : integer - number of matching values 
     */
    function browseCategory($category, $limit=0, $offset=0, $criteria=NULL)
    {
        $category = strtolower($category);
        $r = XML_Util::splitQualifiedName($category);
        $catNs  = $r['namespace'];
        $cat    = $r['localPart'];
        if(is_array($criteria) && count($criteria)>0){
            return $this->_localGenSearch($criteria, $limit, $offset, $catNs, $cat);
        }
        $sqlCond = "m.predicate='$cat' AND m.objns='_L'";
        if(!is_null($catNs)){
            $sqlCond = "m.predns = '{$catNs}' AND  $sqlCond";
        }
        $limitPart = ($limit != 0 ? " LIMIT $limit" : '' ).
            ($offset != 0 ? " OFFSET $offset" : '' );
        $sql =
            "SELECT DISTINCT m.object FROM {$this->mdataTable} m\n".
            "WHERE $sqlCond";
        // echo "\n---\n$sql\n---\n";
        $cnt = $this->_getNumRows($sql);
        if(PEAR::isError($cnt)) return $cnt;
        $res = $this->dbc->getCol($sql.$limitPart);
        if(PEAR::isError($res)) return $res;
        if(!is_array($res)) $res = array();
        return array('results'=>$res, 'cnt'=>$cnt);
    }
    
    /**
     *  Get number of rows in query result
     *
     *  @param query string, sql query
     *  @return int, number of rows in query result
     */
    function _getNumRows($query)
    {
        $rh = $this->dbc->query($query);
        if(PEAR::isError($rh)) return $rh;
        $cnt = $rh->numRows();
        if(PEAR::isError($cnt)) return $cnt;
        $rh->free();
        return $cnt;
    }
    
}

?>