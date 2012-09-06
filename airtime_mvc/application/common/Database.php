<?php
class Application_Common_Database
{
    public static function prepareAndExecute($sql, array $paramValueMap,
        $type='all', $fetchType=PDO::FETCH_ASSOC)
    {
        $con = Propel::getConnection();
        $stmt = $con->prepare($sql);
        foreach ($paramValueMap as $param => $v) {
            $stmt->bindValue($param, $v);
        }
        $rows = array();
        if ($stmt->execute()) {
            if ($type == 'single') {
                $rows = $stmt->fetch($fetchType);
            } else if ($type == 'column'){
                $rows = $stmt->fetchColumn();
            } else if ($type == 'all') {
                $rows = $stmt->fetchAll($fetchType);
            } else if ($type == 'execute') {
                $rows = null;
            } else {
                $msg = "bad type passed: type($type)";
                throw new Exception("Error: $msg");
            }
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }
        return $rows;
    }
}
