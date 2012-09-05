<?php
class Application_Common_Database
{

    public static function prepareAndExecute($sql, $paramValueMap, $type='all')
    {
        $con = Propel::getConnection();
        $stmt = $con->prepare($sql);
        foreach ($paramValueMap as $param => $v) {
            $stmt->bindValue($param, $v);
        }
        $rows = array();
        if ($stmt->execute()) {
            if ($type == 'single') {
                $rows = $stmt->fetch(PDO::FETCH_ASSOC);
            } elseif ($type == 'column'){
                $rows = $stmt->fetchColumn();
            } else {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }
        return $rows;
    }
}
