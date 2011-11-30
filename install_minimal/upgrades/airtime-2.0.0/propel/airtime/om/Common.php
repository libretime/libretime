<?php

class Common {	
	
	public static function setTimeInSub($row, $col, $time)
    {
  	    $class = get_class($row).'Peer';

        $con = Propel::getConnection($class::DATABASE_NAME);

        $sql = 'UPDATE '.$class::TABLE_NAME
        . ' SET '.$col.' = :f1'
        . ' WHERE ' .$class::ID. ' = :p1';
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':f1', $time);
        $stmt->bindValue(':p1', $row->getDbId());
        $stmt->execute();
    }
}
