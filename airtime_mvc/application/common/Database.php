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
    /*
        Wrapper around prepareAndExecute that allows you to use multipe :xx's
        in one query. Transforms $sql to :xx1, :xx2, ....
     */
    public static function smartPrepareAndExecute($sql, array $params,
        $type='all', $fetchType=PDO::FETCH_ASSOC)
    {
        $new_params = array();
        $new_sql    = $sql;
        foreach ($params as $k => $v) {
            $matches_count = substr_count($sql, $k);
            if ($matches_count == 0) {
                throw new Exception("Argument $k is not inside $sql");
            } elseif ($matches_count == 1) {
                $new_params[$k] = $new_params[$v];
            } else {
                foreach ( range(1,$matches_count) as $i ) {
                    preg_replace( "/$k(\D)/", "$k$i${1}", $sql, 1);
                    $new_params[ $k.$i ] = $v;
                }
            }
        }
        return Application_Common_Database::prepareAndExecute( $new_sql,
            $new_params, $type, $fetchType);
    }
}
