<?php

/**
 * Skeleton subclass for representing a row from the 'cc_show' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcShow extends BaseCcShow
{
    /*
     * Returns all cc_show_day rules that belong to a cc_show and that are
     * repeating.
     * We do this because editing a single instance from a repeating sequence
     * creates a new rule in cc_show_days with the same cc_show id and a repeat
     * type of -1 (non-repeating).
     * So when the entire cc_show is updated after that, the single edited
     * instance can remain separate from the rest of the instances
     */
    public function getRepeatingCcShowDays()
    {
        return CcShowDaysQuery::create()
            ->filterByDbShowId($this->id)
            ->filterByDbRepeatType(-1, Criteria::NOT_EQUAL)
            ->find();
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
     * @param Criteria  $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con      optional connection object
     *
     * @return array|PropelCollection CcShowDays[] List of CcShowDays objects
     *
     * @throws PropelException
     */
    public function getFirstCcShowDay($criteria = null, PropelPDO $con = null)
    {
        /*CcShowPeer::clearInstancePool();
        CcShowPeer::clearRelatedInstancePool();*/

        if (null === $this->collCcShowDayss || null !== $criteria) {
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
     * A repeating show may have a rule in cc_show_days with a repeat type
     * of -1 (not repeating). This happens when a single instances was edited
     * from the repeating sequence.
     *
     * When the repeating show gets edited in this case, we want to exclude all
     * the edited instances from the update. We do this by not returning any of
     * the cc_show_day rules with a -1 repeat type.
     */
    public function getFirstRepeatingCcShowDay()
    {
        return CcShowDaysQuery::create()
            ->filterByDbShowId($this->id)
            ->filterByDbRepeatType(-1, Criteria::NOT_EQUAL)
            ->orderByDbFirstShow()
            ->findOne();
    }

    /**
     * In order to determine if a show is repeating we need to check each
     * cc_show_day entry and check if there are any non -1 repeat types.
     * Because editing a single instances creates a new cc_show_day rule
     * with a -1 (non repeating) repeat type we need to check all cc_show_day
     * entries.
     */
    public function isRepeating()
    {
        // get all cc_show_day entries that are repeating
        $ccShowDays = CcShowDaysQuery::create()
            ->filterByDbShowId($this->id)
            ->filterByDbRepeatType(0, Criteria::GREATER_EQUAL)
            ->find();

        if (!$ccShowDays->isEmpty()) {
            return true;
        }

        return false;
    }

    /**
     * Returns all cc_show_instances that have been edited out of
     * a repeating sequence.
     */
    public function getEditedRepeatingInstanceIds()
    {
        // get cc_show_days that have been edited (not repeating)
        $ccShowDays = CcShowDaysQuery::create()
            ->filterByDbShowId($this->id)
            ->filterByDbRepeatType(-1)
            ->find();

        $startsUTC = [];

        $utc = new DateTimeZone('UTC');
        foreach ($ccShowDays as $day) {
            // convert to UTC
            $starts = new DateTime(
                $day->getDbFirstShow() . ' ' . $day->getDbStartTime(),
                new DateTimeZone($day->getDbTimezone())
            );
            $starts->setTimezone($utc);
            array_push($startsUTC, $starts->format('Y-m-d H:i:s'));
        }

        $excludeInstances = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->id)
            ->filterByDbStarts($startsUTC, criteria::IN)
            ->find();

        $excludeIds = [];
        foreach ($excludeInstances as $instance) {
            array_push($excludeIds, $instance->getDbId());
        }

        return $excludeIds;
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
     * @param Criteria  $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con      optional connection object
     *
     * @return array|PropelCollection CcShowInstances[] List of CcShowInstances objects
     *
     * @throws PropelException
     */
    public function getFutureCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCcShowInstancess || null !== $criteria) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                // return empty collection
                $this->initCcShowInstancess();
            } else {
                $collCcShowInstancess = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->filterByDbStarts(gmdate('Y-m-d H:i:s'), Criteria::GREATER_THAN)
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

        return !is_null($ccShowDay);
    }

    public function isRebroadcast()
    {
        $ccShowRebroadcast = CcShowRebroadcastQuery::create()
            ->filterByDbShowId($this->getDbId())
            ->findOne();

        return !is_null($ccShowRebroadcast);
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
     * @param Criteria  $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con      optional connection object
     *
     * @return array|PropelCollection CcShowInstances[] List of CcShowInstances objects
     *
     * @throws PropelException
     */
    public function getCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        return CcShowInstancesQuery::create(null, $criteria)
            ->filterByCcShow($this)
            ->filterByDbModifiedInstance(false)
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

    public function getInstanceIds()
    {
        $instanceIds = [];
        foreach ($this->getCcShowInstancess() as $ccShowInstance) {
            $instanceIds[] = $ccShowInstance->getDbId();
        }

        return $instanceIds;
    }

    /*
     * Returns cc_show_instance ids where the start time is greater than
     * the current time
     *
     * If a Criteria object is passed in Propel will always fetch the
     * results from the database and not return a cached collection
     */
    public function getFutureInstanceIds($criteria = null)
    {
        $instanceIds = [];
        foreach ($this->getFutureCcShowInstancess($criteria) as $ccShowInstance) {
            $instanceIds[] = $ccShowInstance->getDbId();
        }

        return $instanceIds;
    }

    // what is this??
    public function getOtherInstances($instanceId)
    {
        return CcShowInstancesQuery::create()
            ->filterByCcShow($this)
            ->filterByDbId($instanceId, Criteria::NOT_EQUAL)
            ->find();
    }

    public function getShowInfo()
    {
        $info = [];
        if ($this->getDbId() == null) {
            return $info;
        }
        $info['name'] = $this->getDbName();
        $info['id'] = $this->getDbId();
        $info['url'] = $this->getDbUrl();
        $info['genre'] = $this->getDbGenre();
        $info['description'] = $this->getDbDescription();
        $info['color'] = $this->getDbColor();
        $info['background_color'] = $this->getDbBackgroundColor();
        $info['linked'] = $this->getDbLinked();
        $info['has_autoplaylist'] = $this->getDbHasAutoPlaylist();
        $info['autoplaylist_id'] = $this->getDbAutoPlaylistId();
        $info['autoplaylist_repeat'] = $this->getDbAutoPlaylistRepeat();
        $info['override_intro_playlist'] = $this->getDbOverrideIntroPlaylist();
        $info['intro_playlist_id'] = $this->getDbIntroPlaylistId();
        $info['override_outro_playlist'] = $this->getDbOverrideOutroPlaylist();
        $info['outro_playlist_id'] = $this->getDbOutroPlaylistId();

        return $info;
    }
} // CcShow
