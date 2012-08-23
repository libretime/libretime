<?php

class Application_Model_Datatables
{
    private static function buildWhereClauseForAdvancedSearch($dbname2searchTerm)
    {
        $where = array();
        foreach ($dbname2searchTerm as $dbname=>$term) {
            $isRange = false;
            if (strstr($term, '~')) {
                $info = explode('~', $term);
                $input1 = isset($info[0])?$info[0]:null;
                $input2 = isset($info[1])?$info[1]:null;
                $isRange = true;
            } else {
                $input1 = $term;
            }
            
            if ($isRange) {
                $sub = array();
                if ($input1 != null) {
                    $sub[] = $dbname." >= '".$input1."'";
                }
                if ($input2 != null) {
                    $sub[] = $dbname." <= '".$input2."'";
                }
                if (!empty($sub)) {
                    $where[] = "(".implode(' AND ', $sub).")";
                }
            } else {
                if (trim($input1) !== "") {
                    $where[] = $dbname." ILIKE "."'%".$input1."%'";
                }
            }
        }
        return implode(" AND ", $where);
    }
    /*
     * query used to return data for a paginated/searchable datatable.
     */
    public static function findEntries($con, $displayColumns, $fromTable, $data, $dataProp = "aaData")
    {
        $librarySetting = Application_Model_Preference::getCurrentLibraryTableSetting();
        
        // map that maps original column position to db name
        $current2dbname = array();
        // array of search terms
        $orig2searchTerm= array();
        foreach ($data as $key=>$d) {
            if (strstr($key, "mDataProp_")) {
                list($dump, $index) = explode("_", $key);
                $current2dbname[$index] = $d;
            } else if (strstr($key, "sSearch_")) {
                list($dump, $index) = explode("_", $key);
                $orig2searchTerm[$index] = $d;
            }
        }
        // map that maps current column position to original position
        $current2orig = $librarySetting['ColReorder'];
        
        // map that maps dbname to searchTerm
        $dbname2searchTerm = array();
        foreach ($current2dbname as $currentPos=>$dbname) {
            $dbname2searchTerm[$dbname] = $orig2searchTerm[$current2orig[$currentPos]];
        }
        
        $where = array();
        
        $advancedWhere = self::buildWhereClauseForAdvancedSearch($dbname2searchTerm);
        if ($advancedWhere != "") {
            $where[] = $advancedWhere;
        }

        if ($data["sSearch"] !== "") {
            $searchTerms = explode(" ", $data["sSearch"]);
        }

        $selectorCount = "SELECT COUNT(*) ";
        $selectorRows = "SELECT ".join(",", $displayColumns)." ";

        $sql = $selectorCount." FROM ".$fromTable;
        $sqlTotalRows = $sql;

        if (isset($searchTerms)) {
            $searchCols = array();
            for ($i = 0; $i < $data["iColumns"]; $i++) {
                if ($data["bSearchable_".$i] == "true") {
                    $searchCols[] = $data["mDataProp_{$i}"];
                }
            }

            $outerCond = array();

            foreach ($searchTerms as $term) {
                $innerCond = array();

                foreach ($searchCols as $col) {
                    $escapedTerm = pg_escape_string($term);
                    $innerCond[] = "{$col}::text ILIKE '%{$escapedTerm}%'";
                }
                $outerCond[] = "(".join(" OR ", $innerCond).")";
            }
            $where[] = "(".join(" AND ", $outerCond).")";
        }
        // End Where clause

        // Order By clause
        $orderby = array();
        for ($i = 0; $i < $data["iSortingCols"]; $i++) {
            $num = $data["iSortCol_".$i];
            $orderby[] = $data["mDataProp_{$num}"]." ".$data["sSortDir_".$i];
        }
        $orderby[] = "id";
        $orderby = join("," , $orderby);
        // End Order By clause

        $displayLength = intval($data["iDisplayLength"]);
        if (count($where) > 0) {
            $where = join(" AND ", $where);
            $sql = $selectorCount." FROM ".$fromTable." WHERE ".$where;
            $sqlTotalDisplayRows = $sql;

            $sql = $selectorRows." FROM ".$fromTable." WHERE ".$where." ORDER BY ".$orderby;

            //limit the results returned.
            if ($displayLength !== -1) {
                $sql .= " OFFSET ".$data["iDisplayStart"]." LIMIT ".$displayLength;
            }
        } else {
            $sql = $selectorRows." FROM ".$fromTable." ORDER BY ".$orderby;

            //limit the results returned.
            if ($displayLength !== -1) {
                $sql .= " OFFSET ".$data["iDisplayStart"]." LIMIT ".$displayLength;
            }
        }

        try {
            $r = $con->query($sqlTotalRows);
            $totalRows = $r->fetchColumn(0);

            if (isset($sqlTotalDisplayRows)) {
                $r = $con->query($sqlTotalDisplayRows);
                $totalDisplayRows = $r->fetchColumn(0);
            } else {
                $totalDisplayRows = $totalRows;
            }

            $r = $con->query($sql);
            $r->setFetchMode(PDO::FETCH_ASSOC);
            $results = $r->fetchAll();
            // we need to go over all items and fix length for playlist
            // in case the playlist contains dynamic block
            foreach ($results as &$r) {
                if ($r['ftype'] == 'playlist') {
                    $pl = new Application_Model_Playlist($r['id']);
                    $r['length'] = $pl->getLength();
                } else if ($r['ftype'] == "block") {
                    $bl = new Application_Model_Block($r['id']);
                    if ($bl->isStatic()) {
                        $r['bl_type'] = 'static';
                    } else {
                        $r['bl_type'] = 'dynamic';
                    }
                    $r['length'] = $bl->getLength();
                }
            }
        } catch (Exception $e) {
            Logging::debug($e->getMessage());
        }

        return array(
            "sEcho" => intval($data["sEcho"]),
            "iTotalDisplayRecords" => intval($totalDisplayRows),
            "iTotalRecords" => intval($totalRows),
            $dataProp => $results
        );
    }
}
