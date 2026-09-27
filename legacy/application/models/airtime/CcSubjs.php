<?php

/**
 * Skeleton subclass for representing a row from the 'cc_subjs' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcSubjs extends BaseCcSubjs
{
    public function isAdminOrPM()
    {
        return in_array($this->type, [UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER], true);
    }

    public function isSuperAdmin()
    {
        return $this->type === UTYPE_SUPERADMIN;
    }

    public function isHostOfShow($showId)
    {
        return CcShowHostsQuery::create()
            ->filterByDbShow($showId)
            ->filterByDbHost($this->getDbId())
            ->count() > 0;
    }

    public function isHostOfShowInstance($instanceId)
    {
        $showInstance = CcShowInstancesQuery::create()
            ->findPk($instanceId);

        return CcShowHostsQuery::create()
            ->filterByDbShow($showInstance->getDbShowId())
            ->filterByDbHost($this->getDbId())
            ->count() > 0;
    }
} // CcSubjs
