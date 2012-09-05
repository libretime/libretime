<?php
class Application_Common_Database
{
    public static function prepareAndExecute($sql, $paramValueMap, $type='all', $fetchType=PDO::FETCH_ASSOC)
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
            } else {
                $rows = $stmt->fetchAll($fetchType);
            }
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }
        return $rows;
    }
}
