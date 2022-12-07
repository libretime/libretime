<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'cc_playout_history_metadata' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcPlayoutHistoryMetaData extends BaseCcPlayoutHistoryMetaData
{
    /**
     * Set the value of [value] column.
     *
     * @param string $v new value
     *
     * @return CcPlayoutHistoryMetaData The current object (for fluent API support)
     */
    public function setDbValue($v)
    {
        // make sure the metadata isn't longer than the DB field.
        $v = substr($v, 0, 128);

        parent::setDbValue($v);

        return $this;
    } // setDbValue()
} // CcPlayoutHistoryMetaData
