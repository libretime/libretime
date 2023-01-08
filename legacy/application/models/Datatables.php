<?php

class Application_Model_Datatables
{
    private static function buildWhereClauseForAdvancedSearch($dbname2searchTerm)
    {
        $where = [];
        $where['clause'] = [];
        $where['params'] = [];
        foreach ($dbname2searchTerm as $dbname => $term) {
            $isRange = false;
            if (strstr($term, '~')) {
                $info = explode('~', $term);
                if ($dbname == 'utime' || $dbname == 'mtime' || $dbname == 'lptime') {
                    try {
                        $input1 = ($info[0] != '') ? Application_Common_DateHelper::UserTimezoneStringToUTCString($info[0]) : null;
                        $input2 = ($info[1] != '') ? Application_Common_DateHelper::UserTimezoneStringToUTCString($info[1]) : null;
                    } catch (Exception $e) {
                        $input1 = null;
                        $input2 = null;
                    }
                } elseif ($dbname == 'bit_rate' || $dbname == 'sample_rate') {
                    $input1 = isset($info[0]) ? floatval($info[0]) * 1000 : null;
                    $input2 = isset($info[1]) ? floatval($info[1]) * 1000 : null;
                } else {
                    $input1 = isset($info[0]) ? $info[0] : null;
                    $input2 = isset($info[1]) ? $info[1] : null;
                }
                $isRange = true;
            } else {
                $input1 = $term;
            }

            if ($isRange) {
                $sub = [];
                if ($input1 != null) {
                    $sub[] = $dbname . ' >= :' . $dbname . '1';
                }
                if ($input2 != null) {
                    $sub[] = $dbname . ' <= :' . $dbname . '2';
                }
                if (!empty($sub)) {
                    $where['clause'][$dbname] = '(' . implode(' AND ', $sub) . ')';
                    if ($input1 != null) {
                        $where['params'][$dbname . '1'] = $input1;
                    }
                    if ($input2 != null) {
                        $where['params'][$dbname . '2'] = $input2;
                    }
                }
            } else {
                if (trim($input1) !== '') {
                    if ($dbname == 'track_type_id') {
                        $where['clause'][$dbname] = $dbname . ' = :' . $dbname . '1';
                        $where['params'][$dbname . '1'] = $input1;
                    } else {
                        $where['clause'][$dbname] = $dbname . ' ILIKE :' . $dbname . '1';
                        $where['params'][$dbname . '1'] = '%' . $input1 . '%';
                    }
                }
            }
        }

        return $where;
    }

    // query used to return data for a paginated/searchable datatable.
    public static function findEntries(
        $con,
        $displayColumns,
        $fromTable,
        $data,
        $dataProp = 'aaData'
    ) {
        $where = [];
        /* Holds the parameters for binding after the statement has been
            prepared */
        $params = [];

        if (isset($data['advSearch']) && $data['advSearch'] === 'true') {
            $librarySetting = Application_Model_Preference::getCurrentLibraryTableColumnMap();
            // $displayColumns[] = 'owner';

            // map that maps original column position to db name
            $current2dbname = [];
            // array of search terms
            $orig2searchTerm = [];
            foreach ($data as $key => $d) {
                if (strstr($key, 'mDataProp_')) {
                    [$dump, $index] = explode('_', $key);
                    $current2dbname[$index] = $d;
                } elseif (strstr($key, 'sSearch_')) {
                    [$dump, $index] = explode('_', $key);
                    $orig2searchTerm[$index] = $d;
                }
            }

            // map that maps dbname to searchTerm
            $dbname2searchTerm = [];
            foreach ($current2dbname as $currentPos => $dbname) {
                $new_index = $librarySetting($currentPos);
                // TODO : Fix this retarded hack later. Just a band aid for
                // now at least we print some warnings so that we don't
                // forget about this -- cc-4462
                if (array_key_exists($new_index, $orig2searchTerm)) {
                    $dbname2searchTerm[$dbname] = $orig2searchTerm[$new_index];
                } else {
                    Logging::warn('Trying to reorder to unknown index
                            printing as much debugging as possible...');
                    $debug = [
                        '$new_index' => $new_index,
                        '$currentPos' => $currentPos,
                        '$orig2searchTerm' => $orig2searchTerm,
                    ];
                    Logging::warn($debug);
                }
            }

            $advancedWhere = self::buildWhereClauseForAdvancedSearch($dbname2searchTerm);
            if (!empty($advancedWhere['clause'])) {
                $where[] = implode(' AND ', $advancedWhere['clause']);
                $params = $advancedWhere['params'];
            }
        }

        if ($data['sSearch'] !== '') {
            $searchTerms = explode(' ', $data['sSearch']);
        }

        $selectorCount = 'SELECT COUNT(*) ';
        $selectorRows = 'SELECT ' . implode(',', $displayColumns) . ' ';

        $sql = $selectorCount . ' FROM ' . $fromTable;
        $sqlTotalRows = $sql;

        if (isset($searchTerms)) {
            $searchCols = [];
            for ($i = 0; $i < $data['iColumns']; ++$i) {
                if ($data['bSearchable_' . $i] == 'true') {
                    $searchCols[] = $data["mDataProp_{$i}"];
                }
            }

            $outerCond = [];
            $simpleWhere = [];

            foreach ($searchTerms as $term) {
                foreach ($searchCols as $col) {
                    $simpleWhere['clause']['simple_' . $col] = "{$col}::text ILIKE :simple_" . $col;
                    $simpleWhere['params']['simple_' . $col] = '%' . $term . '%';
                }
                $outerCond[] = '(' . implode(' OR ', $simpleWhere['clause']) . ')';
            }
            $where[] = '(' . implode(' AND ', $outerCond) . ')';
            $params = array_merge($params, $simpleWhere['params']);
        }
        // End Where clause

        // Order By clause
        $orderby = [];
        for ($i = 0; $i < $data['iSortingCols']; ++$i) {
            $num = $data['iSortCol_' . $i];
            $orderby[] = $data["mDataProp_{$num}"] . ' ' . $data['sSortDir_' . $i];
        }
        $orderby[] = 'id';
        $orderby = implode(',', $orderby);
        // End Order By clause

        $displayLength = intval($data['iDisplayLength']);
        $needToBind = false;
        if (count($where) > 0) {
            $needToBind = true;
            $where = implode(' OR ', $where);
            $sql = $selectorCount . ' FROM ' . $fromTable . ' WHERE ' . $where;
            $sqlTotalDisplayRows = $sql;

            $sql = $selectorRows . ' FROM ' . $fromTable . ' WHERE ' . $where . ' ORDER BY ' . $orderby;
        } else {
            $sql = $selectorRows . ' FROM ' . $fromTable . ' ORDER BY ' . $orderby;
        }

        // limit the results returned.
        if ($displayLength !== -1) {
            $sql .= ' OFFSET ' . $data['iDisplayStart'] . ' LIMIT ' . $displayLength;
        }

        try {
            // Logging::info($sqlTotalRows);

            $r = $con->query($sqlTotalRows);
            $totalRows = $r->fetchColumn(0);

            if (isset($sqlTotalDisplayRows)) {
                // Logging::info("sql is set");
                // Logging::info($sqlTotalDisplayRows);
                $totalDisplayRows = Application_Common_Database::prepareAndExecute($sqlTotalDisplayRows, $params, 'column');
            } else {
                // Logging::info("sql is not set.");
                $totalDisplayRows = $totalRows;
            }

            // TODO
            if ($needToBind) {
                $results = Application_Common_Database::prepareAndExecute($sql, $params);
            } else {
                $stmt = $con->query($sql);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $results = $stmt->fetchAll();
            }
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }

        return [
            'sEcho' => intval($data['sEcho']),
            'iTotalDisplayRecords' => intval($totalDisplayRows),
            'iTotalRecords' => intval($totalRows),
            $dataProp => $results,
        ];
    }
}
