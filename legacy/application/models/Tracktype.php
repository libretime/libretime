<?php

declare(strict_types=1);

class Application_Model_Tracktype
{
    private $_tracktypeInstance;

    public function __construct($tracktypeId)
    {
        if (empty($tracktypeId)) {
            $this->_tracktypeInstance = $this->createTracktype();
        } else {
            $this->_tracktypeInstance = CcTracktypesQuery::create()->findPK($tracktypeId);

            if (is_null($this->_tracktypeInstance)) {
                throw new Exception();
            }
        }
    }

    public function getId()
    {
        return $this->_tracktypeInstance->getDbId();
    }

    public function setCode($code)
    {
        $tracktype = $this->_tracktypeInstance;
        $tracktype->setDbCode($code);
    }

    public function setTypeName($typeName)
    {
        $tracktype = $this->_tracktypeInstance;
        $tracktype->setDbTypeName($typeName);
    }

    public function setDescription($description)
    {
        $tracktype = $this->_tracktypeInstance;
        $tracktype->setDbDescription($description);
    }

    public function setVisibility($visibility)
    {
        $tracktype = $this->_tracktypeInstance;
        $tracktype->setDbVisibility($visibility);
    }

    public function setAnalyzeCuePoints($value)
    {
        $tracktype = $this->_tracktypeInstance;
        $tracktype->setDbAnalyzeCuePoints($value);
    }

    public function getCode()
    {
        $tracktype = $this->_tracktypeInstance;

        return $tracktype->getDbCode();
    }

    public function getTypeName()
    {
        $tracktype = $this->_tracktypeInstance;

        return $tracktype->getDbTypeName();
    }

    public function getDescription()
    {
        $tracktype = $this->_tracktypeInstance;

        return $tracktype->getDbDescription();
    }

    public function getVisibility()
    {
        $tracktype = $this->_tracktypeInstance;

        return $tracktype->getDbVisibility();
    }

    public function getAnalyzeCuePoints()
    {
        $tracktype = $this->_tracktypeInstance;

        return $tracktype->getDbAnalyzeCuePoints();
    }

    public function save()
    {
        $this->_tracktypeInstance->save();
    }

    public function delete()
    {
        if (!$this->_tracktypeInstance->isDeleted()) {
            $this->_tracktypeInstance->delete();
        }
    }

    private function createTracktype()
    {
        return new CcTracktypes();
    }

    public static function getTracktypes($search = null)
    {
        return Application_Model_Tracktype::getTracktypesData([true], $search);
    }

    public static function getTracktypesData(array $visible, $search = null)
    {
        $con = Propel::getConnection();

        $sql_gen = 'SELECT id, code, type_name, description FROM cc_track_types ';

        $visibility = [];
        $params = [];
        for ($i = 0; $i < count($visible); ++$i) {
            $p = ":visibility{$i}";
            $visibility[] = "visibility = {$p}";
            $params[$p] = $visible[$i];
        }

        $sql_type = implode(' OR ', $visibility);

        $sql = $sql_gen . ' WHERE (' . $sql_type . ') ';

        $sql .= ' AND code ILIKE :search';
        $params[':search'] = "%{$search}%";

        $sql = $sql . ' ORDER BY id';

        return Application_Common_Database::prepareAndExecute($sql, $params, 'all');
    }

    public static function getTracktypeCount()
    {
        $sql_gen = 'SELECT count(*) AS cnt FROM cc_track_types';

        $query = Application_Common_Database::prepareAndExecute(
            $sql_gen,
            [],
            Application_Common_Database::COLUMN
        );

        return ($query !== false) ? $query : null;
    }

    public static function getTracktypesDataTablesInfo($datatables)
    {
        $con = Propel::getConnection(CcTracktypesPeer::DATABASE_NAME);

        $displayColumns = ['id', 'code', 'type_name', 'description', 'visibility'];
        $fromTable = 'cc_track_types';
        $tracktypename = '';

        $res = Application_Model_Datatables::findEntries($con, $displayColumns, $fromTable, $datatables);

        foreach ($res['aaData'] as $key => &$record) {
            if ($record['code'] == $tracktypename) {
                $record['delete'] = 'self';
            } else {
                $record['delete'] = '';
            }
            $record = array_map('htmlspecialchars', $record);
        }

        $res['aaData'] = array_values($res['aaData']);

        return $res;
    }

    public static function getTracktypeData($id)
    {
        $sql = <<<'SQL'
SELECT code, type_name, description, visibility, id, analyze_cue_points
FROM cc_track_types
WHERE id = :id
SQL;

        return Application_Common_Database::prepareAndExecute($sql, [
            ':id' => $id,
        ], 'single');
    }
}
