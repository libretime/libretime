<?php

declare(strict_types=1);

/**
 * Skeleton subclass for representing a row from the 'cc_playlist' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class CcPlaylist extends BaseCcPlaylist
{
    /**
     * Get the [optionally formatted] temporal [utime] column value.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbUtime($format = 'Y-m-d H:i:s')
    {
        if ($this->utime === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->utime, new DateTimeZone('UTC'));
        } catch (Exception $x) {
            throw new PropelException('Internally stored date/time/timestamp value could not be converted to DateTime: ' . var_export($this->utime, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);
    }

    /**
     * Get the [optionally formatted] temporal [mtime] column value.
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *                       If format is NULL, then the raw DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws propelException - if unable to parse/validate the date/time value
     */
    public function getDbMtime($format = 'Y-m-d H:i:s')
    {
        if ($this->mtime === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->mtime, new DateTimeZone('UTC'));
        } catch (Exception $x) {
            throw new PropelException('Internally stored date/time/timestamp value could not be converted to DateTime: ' . var_export($this->mtime, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is TRUE, we return a DateTime object.
            return $dt;
        }
        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);
    }

    /**
     * Computes the value of the aggregate column length
     * Overridden to provide a default of 00:00:00 if the playlist is empty.
     *
     * @param PropelPDO $con A connection object
     *
     * @return mixed The scalar result from the aggregate query
     */
    public function computeDbLength(PropelPDO $con)
    {
        $sql = <<<'SQL'
        SELECT SUM(cliplength) FROM cc_playlistcontents as pc
        LEFT JOIN cc_files as f ON pc.file_id = f.id
        WHERE PLAYLIST_ID = :p1
        AND (f.file_exists is NUll or f.file_exists = true)
SQL;
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':p1', $this->getDbId());
        $stmt->execute();
        $length = $stmt->fetchColumn();
        if (is_null($length)) {
            $length = '00:00:00';
        }

        return $length;
    }
} // CcPlaylist
