<?php



/**
 * Skeleton subclass for representing a row from the 'cc_show_instances' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcShowInstances extends BaseCcShowInstances {

    public function computeDbTimeFilled(PropelPDO $con)
    {
        $stmt = $con->prepare('SELECT SUM(clip_length) FROM "cc_schedule" WHERE cc_schedule.INSTANCE_ID = :p1');
        $stmt->bindValue(':p1', $this->getDbId());
        $stmt->execute();
        $result = $stmt->fetchColumn();

        //$result is in the form H:i:s.u
        //patch fix for the current problem of > 23:59:59.99 for a show content
        //which causes problems with a time without timezone column type

        try {
           $dt = new DateTime($result);
        }
        catch(Exception $e) {
            $result = "23:59:59";
        }

        return $result;
    }

} // CcShowInstances
