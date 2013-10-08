<?php



/**
 * Skeleton subclass for representing a row from the 'cc_show' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.airtime
 */
class CcShow extends BaseCcShow {

    public function getCcShowDays(){
        return CcShowDaysQuery::create()->filterByDbShowId($this->getDbId())->find();
    }

    /**
     * Gets an array of CcShowDays objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShow is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return     PropelCollection|array CcShowDays[] List of CcShowDays objects
     * @throws     PropelException
     */
    public function getFirstCcShowDay($criteria = null, PropelPDO $con = null)
    {
        if(null === $this->collCcShowDayss || null !== $criteria) {
            if ($this->isNew() && null === $this->collCcShowDayss) {
                // return empty collection
                $this->initCcShowDayss();
            } else {
                $collCcShowDayss = CcShowDaysQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->orderByDbFirstShow()
                    ->limit(1)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCcShowDayss;
                }
                $this->collCcShowDayss = $collCcShowDayss;
            }
        }
        return $this->collCcShowDayss[0];
    }

    /**
     * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShow is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return     PropelCollection|array CcShowInstances[] List of CcShowInstances objects
     * @throws     PropelException
     */
    public function getFutureCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        if(null === $this->collCcShowInstancess || null !== $criteria) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                // return empty collection
                $this->initCcShowInstancess();
            } else {
                $collCcShowInstancess = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->filterByDbStarts(gmdate("Y-m-d H:i:s"), Criteria::GREATER_THAN)
                    ->filterByDbModifiedInstance(false)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCcShowInstancess;
                }
                $this->collCcShowInstancess = $collCcShowInstancess;
            }
        }
        return $this->collCcShowInstancess;
    }

    public function isRecorded()
    {
        $ccShowDay = CcShowDaysQuery::create()
            ->filterByDbShowId($this->getDbId())
            ->filterByDbRecord(1)
            ->findOne();

        return (!is_null($ccShowDay));
    }

    public function isRebroadcast()
    {
        $ccShowRebroadcast = CcShowRebroadcastQuery::create()
            ->filterByDbShowId($this->getDbId())
            ->findOne();

        return (!is_null($ccShowRebroadcast));
    }

    public function getRebroadcastsRelative()
    {
        return CcShowRebroadcastQuery::create()
            ->filterByDbShowId($this->getDbId())
            ->orderByDbDayOffset()
            ->find();
    }

    public function getRebroadcastsAbsolute()
    {
        return CcShowInstancesQuery::create()
            ->filterByDbShowId($this->getDbId())
            ->filterByDbRebroadcast(1)
            ->filterByDbModifiedInstance(false)
            ->orderByDbStarts()
            ->find();
    }

    public function isLinked()
    {
        return $this->getDbLinked();
    }

    public function isLinkable()
    {
        return $this->getDbIsLinkable();
    }

    /**
     * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShow is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return     PropelCollection|array CcShowInstances[] List of CcShowInstances objects
     * @throws     PropelException
     */
    public function getCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        return CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->filterByDbModifiedInstance(false)
                    ->filterByDbEnds(gmdate("Y-m-d H:i:s"), criteria::GREATER_THAN)
                    ->orderByDbId()
                    ->find($con);

        /*if(null === $this->collCcShowInstancess || null !== $criteria) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                // return empty collection
                $this->initCcShowInstancess();
            } else {
                $collCcShowInstancess = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->filterByDbModifiedInstance(false)
                    ->filterByDbStarts(gmdate("Y-m-d H:i:s"), criteria::GREATER_THAN)
                    ->orderByDbId()
                    ->find($con);
                if (null !== $criteria) {
                    return $collCcShowInstancess;
                }
                $this->collCcShowInstancess = $collCcShowInstancess;
            }
        }
        return $this->collCcShowInstancess;*/
    }

    public function getInstanceIds() {
        $instanceIds = array();
        foreach ($this->getCcShowInstancess() as $ccShowInstance) {
            $instanceIds[] = $ccShowInstance->getDbId();
        }
        return $instanceIds;
    }

    public function getOtherInstances($instanceId)
    {
        return CcShowInstancesQuery::create()
            ->filterByCcShow($this)
            ->filterByDbId($instanceId, Criteria::NOT_EQUAL)
            ->find();
    }
} // CcShow
