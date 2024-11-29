<?php


/**
 * Base class that represents a row from the 'cc_schedule' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSchedule extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcSchedulePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcSchedulePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the starts field.
     * @var        string
     */
    protected $starts;

    /**
     * The value for the ends field.
     * @var        string
     */
    protected $ends;

    /**
     * The value for the file_id field.
     * @var        int
     */
    protected $file_id;

    /**
     * The value for the stream_id field.
     * @var        int
     */
    protected $stream_id;

    /**
     * The value for the clip_length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $clip_length;

    /**
     * The value for the fade_in field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $fade_in;

    /**
     * The value for the fade_out field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $fade_out;

    /**
     * The value for the cue_in field.
     * @var        string
     */
    protected $cue_in;

    /**
     * The value for the cue_out field.
     * @var        string
     */
    protected $cue_out;

    /**
     * The value for the media_item_played field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $media_item_played;

    /**
     * The value for the instance_id field.
     * @var        int
     */
    protected $instance_id;

    /**
     * The value for the playout_status field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $playout_status;

    /**
     * The value for the broadcasted field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $broadcasted;

    /**
     * The value for the position field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $position;

    /**
     * @var        CcShowInstances
     */
    protected $aCcShowInstances;

    /**
     * @var        CcFiles
     */
    protected $aCcFiles;

    /**
     * @var        CcWebstream
     */
    protected $aCcWebstream;

    /**
     * @var        PropelObjectCollection|CcWebstreamMetadata[] Collection to store aggregation of CcWebstreamMetadata objects.
     */
    protected $collCcWebstreamMetadatas;
    protected $collCcWebstreamMetadatasPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccWebstreamMetadatasScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->clip_length = '00:00:00';
        $this->fade_in = '00:00:00';
        $this->fade_out = '00:00:00';
        $this->media_item_played = false;
        $this->playout_status = 1;
        $this->broadcasted = 0;
        $this->position = 0;
    }

    /**
     * Initializes internal state of BaseCcSchedule object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getDbId()
    {

        return $this->id;
    }

    /**
     * Get the [optionally formatted] temporal [starts] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbStarts($format = 'Y-m-d H:i:s')
    {
        if ($this->starts === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->starts);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->starts, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [ends] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbEnds($format = 'Y-m-d H:i:s')
    {
        if ($this->ends === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->ends);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ends, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [file_id] column value.
     *
     * @return int
     */
    public function getDbFileId()
    {

        return $this->file_id;
    }

    /**
     * Get the [stream_id] column value.
     *
     * @return int
     */
    public function getDbStreamId()
    {

        return $this->stream_id;
    }

    /**
     * Get the [clip_length] column value.
     *
     * @return string
     */
    public function getDbClipLength()
    {

        return $this->clip_length;
    }

    /**
     * Get the [optionally formatted] temporal [fade_in] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeIn($format = 'H:i:s')
    {
        if ($this->fade_in === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->fade_in);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fade_in, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [fade_out] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFadeOut($format = 'H:i:s')
    {
        if ($this->fade_out === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->fade_out);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fade_out, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            throw new PropelException('strftime format not supported anymore');
        }

        return $dt->format($format);

    }

    /**
     * Get the [cue_in] column value.
     *
     * @return string
     */
    public function getDbCueIn()
    {

        return $this->cue_in;
    }

    /**
     * Get the [cue_out] column value.
     *
     * @return string
     */
    public function getDbCueOut()
    {

        return $this->cue_out;
    }

    /**
     * Get the [media_item_played] column value.
     *
     * @return boolean
     */
    public function getDbMediaItemPlayed()
    {

        return $this->media_item_played;
    }

    /**
     * Get the [instance_id] column value.
     *
     * @return int
     */
    public function getDbInstanceId()
    {

        return $this->instance_id;
    }

    /**
     * Get the [playout_status] column value.
     *
     * @return int
     */
    public function getDbPlayoutStatus()
    {

        return $this->playout_status;
    }

    /**
     * Get the [broadcasted] column value.
     *
     * @return int
     */
    public function getDbBroadcasted()
    {

        return $this->broadcasted;
    }

    /**
     * Get the [position] column value.
     *
     * @return int
     */
    public function getDbPosition()
    {

        return $this->position;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcSchedulePeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Sets the value of [starts] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbStarts($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->starts !== null || $dt !== null) {
            $currentDateAsString = ($this->starts !== null && $tmpDt = new DateTime($this->starts)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->starts = $newDateAsString;
                $this->modifiedColumns[] = CcSchedulePeer::STARTS;
            }
        } // if either are not null


        return $this;
    } // setDbStarts()

    /**
     * Sets the value of [ends] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbEnds($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->ends !== null || $dt !== null) {
            $currentDateAsString = ($this->ends !== null && $tmpDt = new DateTime($this->ends)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->ends = $newDateAsString;
                $this->modifiedColumns[] = CcSchedulePeer::ENDS;
            }
        } // if either are not null


        return $this;
    } // setDbEnds()

    /**
     * Set the value of [file_id] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbFileId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->file_id !== $v) {
            $this->file_id = $v;
            $this->modifiedColumns[] = CcSchedulePeer::FILE_ID;
        }

        if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
            $this->aCcFiles = null;
        }


        return $this;
    } // setDbFileId()

    /**
     * Set the value of [stream_id] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbStreamId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->stream_id !== $v) {
            $this->stream_id = $v;
            $this->modifiedColumns[] = CcSchedulePeer::STREAM_ID;
        }

        if ($this->aCcWebstream !== null && $this->aCcWebstream->getDbId() !== $v) {
            $this->aCcWebstream = null;
        }


        return $this;
    } // setDbStreamId()

    /**
     * Set the value of [clip_length] column.
     *
     * @param  string $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbClipLength($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->clip_length !== $v) {
            $this->clip_length = $v;
            $this->modifiedColumns[] = CcSchedulePeer::CLIP_LENGTH;
        }


        return $this;
    } // setDbClipLength()

    /**
     * Sets the value of [fade_in] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbFadeIn($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->fade_in !== null || $dt !== null) {
            $currentDateAsString = ($this->fade_in !== null && $tmpDt = new DateTime($this->fade_in)) ? $tmpDt->format('H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('H:i:s') : null;
            if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match
                || ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
                 ) {
                $this->fade_in = $newDateAsString;
                $this->modifiedColumns[] = CcSchedulePeer::FADE_IN;
            }
        } // if either are not null


        return $this;
    } // setDbFadeIn()

    /**
     * Sets the value of [fade_out] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbFadeOut($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->fade_out !== null || $dt !== null) {
            $currentDateAsString = ($this->fade_out !== null && $tmpDt = new DateTime($this->fade_out)) ? $tmpDt->format('H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('H:i:s') : null;
            if ( ($currentDateAsString !== $newDateAsString) // normalized values don't match
                || ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
                 ) {
                $this->fade_out = $newDateAsString;
                $this->modifiedColumns[] = CcSchedulePeer::FADE_OUT;
            }
        } // if either are not null


        return $this;
    } // setDbFadeOut()

    /**
     * Set the value of [cue_in] column.
     *
     * @param  string $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbCueIn($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cue_in !== $v) {
            $this->cue_in = $v;
            $this->modifiedColumns[] = CcSchedulePeer::CUE_IN;
        }


        return $this;
    } // setDbCueIn()

    /**
     * Set the value of [cue_out] column.
     *
     * @param  string $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbCueOut($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cue_out !== $v) {
            $this->cue_out = $v;
            $this->modifiedColumns[] = CcSchedulePeer::CUE_OUT;
        }


        return $this;
    } // setDbCueOut()

    /**
     * Sets the value of the [media_item_played] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbMediaItemPlayed($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->media_item_played !== $v) {
            $this->media_item_played = $v;
            $this->modifiedColumns[] = CcSchedulePeer::MEDIA_ITEM_PLAYED;
        }


        return $this;
    } // setDbMediaItemPlayed()

    /**
     * Set the value of [instance_id] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbInstanceId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->instance_id !== $v) {
            $this->instance_id = $v;
            $this->modifiedColumns[] = CcSchedulePeer::INSTANCE_ID;
        }

        if ($this->aCcShowInstances !== null && $this->aCcShowInstances->getDbId() !== $v) {
            $this->aCcShowInstances = null;
        }


        return $this;
    } // setDbInstanceId()

    /**
     * Set the value of [playout_status] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbPlayoutStatus($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->playout_status !== $v) {
            $this->playout_status = $v;
            $this->modifiedColumns[] = CcSchedulePeer::PLAYOUT_STATUS;
        }


        return $this;
    } // setDbPlayoutStatus()

    /**
     * Set the value of [broadcasted] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbBroadcasted($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->broadcasted !== $v) {
            $this->broadcasted = $v;
            $this->modifiedColumns[] = CcSchedulePeer::BROADCASTED;
        }


        return $this;
    } // setDbBroadcasted()

    /**
     * Set the value of [position] column.
     *
     * @param  int $v new value
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setDbPosition($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[] = CcSchedulePeer::POSITION;
        }


        return $this;
    } // setDbPosition()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->clip_length !== '00:00:00') {
                return false;
            }

            if ($this->fade_in !== '00:00:00') {
                return false;
            }

            if ($this->fade_out !== '00:00:00') {
                return false;
            }

            if ($this->media_item_played !== false) {
                return false;
            }

            if ($this->playout_status !== 1) {
                return false;
            }

            if ($this->broadcasted !== 0) {
                return false;
            }

            if ($this->position !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->starts = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->ends = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->file_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->stream_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->clip_length = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->fade_in = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->fade_out = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->cue_in = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->cue_out = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->media_item_played = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
            $this->instance_id = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->playout_status = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->broadcasted = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->position = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 15; // 15 = CcSchedulePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcSchedule object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aCcFiles !== null && $this->file_id !== $this->aCcFiles->getDbId()) {
            $this->aCcFiles = null;
        }
        if ($this->aCcWebstream !== null && $this->stream_id !== $this->aCcWebstream->getDbId()) {
            $this->aCcWebstream = null;
        }
        if ($this->aCcShowInstances !== null && $this->instance_id !== $this->aCcShowInstances->getDbId()) {
            $this->aCcShowInstances = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcSchedulePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcShowInstances = null;
            $this->aCcFiles = null;
            $this->aCcWebstream = null;
            $this->collCcWebstreamMetadatas = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcScheduleQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CcSchedulePeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCcShowInstances !== null) {
                if ($this->aCcShowInstances->isModified() || $this->aCcShowInstances->isNew()) {
                    $affectedRows += $this->aCcShowInstances->save($con);
                }
                $this->setCcShowInstances($this->aCcShowInstances);
            }

            if ($this->aCcFiles !== null) {
                if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
                    $affectedRows += $this->aCcFiles->save($con);
                }
                $this->setCcFiles($this->aCcFiles);
            }

            if ($this->aCcWebstream !== null) {
                if ($this->aCcWebstream->isModified() || $this->aCcWebstream->isNew()) {
                    $affectedRows += $this->aCcWebstream->save($con);
                }
                $this->setCcWebstream($this->aCcWebstream);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->ccWebstreamMetadatasScheduledForDeletion !== null) {
                if (!$this->ccWebstreamMetadatasScheduledForDeletion->isEmpty()) {
                    CcWebstreamMetadataQuery::create()
                        ->filterByPrimaryKeys($this->ccWebstreamMetadatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccWebstreamMetadatasScheduledForDeletion = null;
                }
            }

            if ($this->collCcWebstreamMetadatas !== null) {
                foreach ($this->collCcWebstreamMetadatas as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = CcSchedulePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcSchedulePeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_schedule_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcSchedulePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcSchedulePeer::STARTS)) {
            $modifiedColumns[':p' . $index++]  = '"starts"';
        }
        if ($this->isColumnModified(CcSchedulePeer::ENDS)) {
            $modifiedColumns[':p' . $index++]  = '"ends"';
        }
        if ($this->isColumnModified(CcSchedulePeer::FILE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"file_id"';
        }
        if ($this->isColumnModified(CcSchedulePeer::STREAM_ID)) {
            $modifiedColumns[':p' . $index++]  = '"stream_id"';
        }
        if ($this->isColumnModified(CcSchedulePeer::CLIP_LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"clip_length"';
        }
        if ($this->isColumnModified(CcSchedulePeer::FADE_IN)) {
            $modifiedColumns[':p' . $index++]  = '"fade_in"';
        }
        if ($this->isColumnModified(CcSchedulePeer::FADE_OUT)) {
            $modifiedColumns[':p' . $index++]  = '"fade_out"';
        }
        if ($this->isColumnModified(CcSchedulePeer::CUE_IN)) {
            $modifiedColumns[':p' . $index++]  = '"cue_in"';
        }
        if ($this->isColumnModified(CcSchedulePeer::CUE_OUT)) {
            $modifiedColumns[':p' . $index++]  = '"cue_out"';
        }
        if ($this->isColumnModified(CcSchedulePeer::MEDIA_ITEM_PLAYED)) {
            $modifiedColumns[':p' . $index++]  = '"media_item_played"';
        }
        if ($this->isColumnModified(CcSchedulePeer::INSTANCE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"instance_id"';
        }
        if ($this->isColumnModified(CcSchedulePeer::PLAYOUT_STATUS)) {
            $modifiedColumns[':p' . $index++]  = '"playout_status"';
        }
        if ($this->isColumnModified(CcSchedulePeer::BROADCASTED)) {
            $modifiedColumns[':p' . $index++]  = '"broadcasted"';
        }
        if ($this->isColumnModified(CcSchedulePeer::POSITION)) {
            $modifiedColumns[':p' . $index++]  = '"position"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_schedule" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"id"':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '"starts"':
                        $stmt->bindValue($identifier, $this->starts, PDO::PARAM_STR);
                        break;
                    case '"ends"':
                        $stmt->bindValue($identifier, $this->ends, PDO::PARAM_STR);
                        break;
                    case '"file_id"':
                        $stmt->bindValue($identifier, $this->file_id, PDO::PARAM_INT);
                        break;
                    case '"stream_id"':
                        $stmt->bindValue($identifier, $this->stream_id, PDO::PARAM_INT);
                        break;
                    case '"clip_length"':
                        $stmt->bindValue($identifier, $this->clip_length, PDO::PARAM_STR);
                        break;
                    case '"fade_in"':
                        $stmt->bindValue($identifier, $this->fade_in, PDO::PARAM_STR);
                        break;
                    case '"fade_out"':
                        $stmt->bindValue($identifier, $this->fade_out, PDO::PARAM_STR);
                        break;
                    case '"cue_in"':
                        $stmt->bindValue($identifier, $this->cue_in, PDO::PARAM_STR);
                        break;
                    case '"cue_out"':
                        $stmt->bindValue($identifier, $this->cue_out, PDO::PARAM_STR);
                        break;
                    case '"media_item_played"':
                        $stmt->bindValue($identifier, $this->media_item_played, PDO::PARAM_BOOL);
                        break;
                    case '"instance_id"':
                        $stmt->bindValue($identifier, $this->instance_id, PDO::PARAM_INT);
                        break;
                    case '"playout_status"':
                        $stmt->bindValue($identifier, $this->playout_status, PDO::PARAM_INT);
                        break;
                    case '"broadcasted"':
                        $stmt->bindValue($identifier, $this->broadcasted, PDO::PARAM_INT);
                        break;
                    case '"position"':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCcShowInstances !== null) {
                if (!$this->aCcShowInstances->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcShowInstances->getValidationFailures());
                }
            }

            if ($this->aCcFiles !== null) {
                if (!$this->aCcFiles->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
                }
            }

            if ($this->aCcWebstream !== null) {
                if (!$this->aCcWebstream->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcWebstream->getValidationFailures());
                }
            }


            if (($retval = CcSchedulePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcWebstreamMetadatas !== null) {
                    foreach ($this->collCcWebstreamMetadatas as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CcSchedulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getDbId();
                break;
            case 1:
                return $this->getDbStarts();
                break;
            case 2:
                return $this->getDbEnds();
                break;
            case 3:
                return $this->getDbFileId();
                break;
            case 4:
                return $this->getDbStreamId();
                break;
            case 5:
                return $this->getDbClipLength();
                break;
            case 6:
                return $this->getDbFadeIn();
                break;
            case 7:
                return $this->getDbFadeOut();
                break;
            case 8:
                return $this->getDbCueIn();
                break;
            case 9:
                return $this->getDbCueOut();
                break;
            case 10:
                return $this->getDbMediaItemPlayed();
                break;
            case 11:
                return $this->getDbInstanceId();
                break;
            case 12:
                return $this->getDbPlayoutStatus();
                break;
            case 13:
                return $this->getDbBroadcasted();
                break;
            case 14:
                return $this->getDbPosition();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['CcSchedule'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcSchedule'][$this->getPrimaryKey()] = true;
        $keys = CcSchedulePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbStarts(),
            $keys[2] => $this->getDbEnds(),
            $keys[3] => $this->getDbFileId(),
            $keys[4] => $this->getDbStreamId(),
            $keys[5] => $this->getDbClipLength(),
            $keys[6] => $this->getDbFadeIn(),
            $keys[7] => $this->getDbFadeOut(),
            $keys[8] => $this->getDbCueIn(),
            $keys[9] => $this->getDbCueOut(),
            $keys[10] => $this->getDbMediaItemPlayed(),
            $keys[11] => $this->getDbInstanceId(),
            $keys[12] => $this->getDbPlayoutStatus(),
            $keys[13] => $this->getDbBroadcasted(),
            $keys[14] => $this->getDbPosition(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcShowInstances) {
                $result['CcShowInstances'] = $this->aCcShowInstances->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcFiles) {
                $result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcWebstream) {
                $result['CcWebstream'] = $this->aCcWebstream->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcWebstreamMetadatas) {
                $result['CcWebstreamMetadatas'] = $this->collCcWebstreamMetadatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CcSchedulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setDbId($value);
                break;
            case 1:
                $this->setDbStarts($value);
                break;
            case 2:
                $this->setDbEnds($value);
                break;
            case 3:
                $this->setDbFileId($value);
                break;
            case 4:
                $this->setDbStreamId($value);
                break;
            case 5:
                $this->setDbClipLength($value);
                break;
            case 6:
                $this->setDbFadeIn($value);
                break;
            case 7:
                $this->setDbFadeOut($value);
                break;
            case 8:
                $this->setDbCueIn($value);
                break;
            case 9:
                $this->setDbCueOut($value);
                break;
            case 10:
                $this->setDbMediaItemPlayed($value);
                break;
            case 11:
                $this->setDbInstanceId($value);
                break;
            case 12:
                $this->setDbPlayoutStatus($value);
                break;
            case 13:
                $this->setDbBroadcasted($value);
                break;
            case 14:
                $this->setDbPosition($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = CcSchedulePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbStarts($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbEnds($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbFileId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbStreamId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbClipLength($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbFadeIn($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbFadeOut($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbCueIn($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbCueOut($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbMediaItemPlayed($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbInstanceId($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbPlayoutStatus($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDbBroadcasted($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setDbPosition($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);

        if ($this->isColumnModified(CcSchedulePeer::ID)) $criteria->add(CcSchedulePeer::ID, $this->id);
        if ($this->isColumnModified(CcSchedulePeer::STARTS)) $criteria->add(CcSchedulePeer::STARTS, $this->starts);
        if ($this->isColumnModified(CcSchedulePeer::ENDS)) $criteria->add(CcSchedulePeer::ENDS, $this->ends);
        if ($this->isColumnModified(CcSchedulePeer::FILE_ID)) $criteria->add(CcSchedulePeer::FILE_ID, $this->file_id);
        if ($this->isColumnModified(CcSchedulePeer::STREAM_ID)) $criteria->add(CcSchedulePeer::STREAM_ID, $this->stream_id);
        if ($this->isColumnModified(CcSchedulePeer::CLIP_LENGTH)) $criteria->add(CcSchedulePeer::CLIP_LENGTH, $this->clip_length);
        if ($this->isColumnModified(CcSchedulePeer::FADE_IN)) $criteria->add(CcSchedulePeer::FADE_IN, $this->fade_in);
        if ($this->isColumnModified(CcSchedulePeer::FADE_OUT)) $criteria->add(CcSchedulePeer::FADE_OUT, $this->fade_out);
        if ($this->isColumnModified(CcSchedulePeer::CUE_IN)) $criteria->add(CcSchedulePeer::CUE_IN, $this->cue_in);
        if ($this->isColumnModified(CcSchedulePeer::CUE_OUT)) $criteria->add(CcSchedulePeer::CUE_OUT, $this->cue_out);
        if ($this->isColumnModified(CcSchedulePeer::MEDIA_ITEM_PLAYED)) $criteria->add(CcSchedulePeer::MEDIA_ITEM_PLAYED, $this->media_item_played);
        if ($this->isColumnModified(CcSchedulePeer::INSTANCE_ID)) $criteria->add(CcSchedulePeer::INSTANCE_ID, $this->instance_id);
        if ($this->isColumnModified(CcSchedulePeer::PLAYOUT_STATUS)) $criteria->add(CcSchedulePeer::PLAYOUT_STATUS, $this->playout_status);
        if ($this->isColumnModified(CcSchedulePeer::BROADCASTED)) $criteria->add(CcSchedulePeer::BROADCASTED, $this->broadcasted);
        if ($this->isColumnModified(CcSchedulePeer::POSITION)) $criteria->add(CcSchedulePeer::POSITION, $this->position);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);
        $criteria->add(CcSchedulePeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getDbId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setDbId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getDbId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of CcSchedule (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbStarts($this->getDbStarts());
        $copyObj->setDbEnds($this->getDbEnds());
        $copyObj->setDbFileId($this->getDbFileId());
        $copyObj->setDbStreamId($this->getDbStreamId());
        $copyObj->setDbClipLength($this->getDbClipLength());
        $copyObj->setDbFadeIn($this->getDbFadeIn());
        $copyObj->setDbFadeOut($this->getDbFadeOut());
        $copyObj->setDbCueIn($this->getDbCueIn());
        $copyObj->setDbCueOut($this->getDbCueOut());
        $copyObj->setDbMediaItemPlayed($this->getDbMediaItemPlayed());
        $copyObj->setDbInstanceId($this->getDbInstanceId());
        $copyObj->setDbPlayoutStatus($this->getDbPlayoutStatus());
        $copyObj->setDbBroadcasted($this->getDbBroadcasted());
        $copyObj->setDbPosition($this->getDbPosition());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcWebstreamMetadatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcWebstreamMetadata($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setDbId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return CcSchedule Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return CcSchedulePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcSchedulePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcShowInstances object.
     *
     * @param                  CcShowInstances $v
     * @return CcSchedule The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcShowInstances(CcShowInstances $v = null)
    {
        if ($v === null) {
            $this->setDbInstanceId(NULL);
        } else {
            $this->setDbInstanceId($v->getDbId());
        }

        $this->aCcShowInstances = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcShowInstances object, it will not be re-added.
        if ($v !== null) {
            $v->addCcSchedule($this);
        }


        return $this;
    }


    /**
     * Get the associated CcShowInstances object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcShowInstances The associated CcShowInstances object.
     * @throws PropelException
     */
    public function getCcShowInstances(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcShowInstances === null && ($this->instance_id !== null) && $doQuery) {
            $this->aCcShowInstances = CcShowInstancesQuery::create()->findPk($this->instance_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcShowInstances->addCcSchedules($this);
             */
        }

        return $this->aCcShowInstances;
    }

    /**
     * Declares an association between this object and a CcFiles object.
     *
     * @param                  CcFiles $v
     * @return CcSchedule The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcFiles(CcFiles $v = null)
    {
        if ($v === null) {
            $this->setDbFileId(NULL);
        } else {
            $this->setDbFileId($v->getDbId());
        }

        $this->aCcFiles = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcFiles object, it will not be re-added.
        if ($v !== null) {
            $v->addCcSchedule($this);
        }


        return $this;
    }


    /**
     * Get the associated CcFiles object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcFiles The associated CcFiles object.
     * @throws PropelException
     */
    public function getCcFiles(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcFiles === null && ($this->file_id !== null) && $doQuery) {
            $this->aCcFiles = CcFilesQuery::create()->findPk($this->file_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcFiles->addCcSchedules($this);
             */
        }

        return $this->aCcFiles;
    }

    /**
     * Declares an association between this object and a CcWebstream object.
     *
     * @param                  CcWebstream $v
     * @return CcSchedule The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcWebstream(CcWebstream $v = null)
    {
        if ($v === null) {
            $this->setDbStreamId(NULL);
        } else {
            $this->setDbStreamId($v->getDbId());
        }

        $this->aCcWebstream = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcWebstream object, it will not be re-added.
        if ($v !== null) {
            $v->addCcSchedule($this);
        }


        return $this;
    }


    /**
     * Get the associated CcWebstream object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcWebstream The associated CcWebstream object.
     * @throws PropelException
     */
    public function getCcWebstream(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcWebstream === null && ($this->stream_id !== null) && $doQuery) {
            $this->aCcWebstream = CcWebstreamQuery::create()->findPk($this->stream_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcWebstream->addCcSchedules($this);
             */
        }

        return $this->aCcWebstream;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CcWebstreamMetadata' == $relationName) {
            $this->initCcWebstreamMetadatas();
        }
    }

    /**
     * Clears out the collCcWebstreamMetadatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSchedule The current object (for fluent API support)
     * @see        addCcWebstreamMetadatas()
     */
    public function clearCcWebstreamMetadatas()
    {
        $this->collCcWebstreamMetadatas = null; // important to set this to null since that means it is uninitialized
        $this->collCcWebstreamMetadatasPartial = null;

        return $this;
    }

    /**
     * reset is the collCcWebstreamMetadatas collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcWebstreamMetadatas($v = true)
    {
        $this->collCcWebstreamMetadatasPartial = $v;
    }

    /**
     * Initializes the collCcWebstreamMetadatas collection.
     *
     * By default this just sets the collCcWebstreamMetadatas collection to an empty array (like clearcollCcWebstreamMetadatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcWebstreamMetadatas($overrideExisting = true)
    {
        if (null !== $this->collCcWebstreamMetadatas && !$overrideExisting) {
            return;
        }
        $this->collCcWebstreamMetadatas = new PropelObjectCollection();
        $this->collCcWebstreamMetadatas->setModel('CcWebstreamMetadata');
    }

    /**
     * Gets an array of CcWebstreamMetadata objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSchedule is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcWebstreamMetadata[] List of CcWebstreamMetadata objects
     * @throws PropelException
     */
    public function getCcWebstreamMetadatas($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcWebstreamMetadatasPartial && !$this->isNew();
        if (null === $this->collCcWebstreamMetadatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcWebstreamMetadatas) {
                // return empty collection
                $this->initCcWebstreamMetadatas();
            } else {
                $collCcWebstreamMetadatas = CcWebstreamMetadataQuery::create(null, $criteria)
                    ->filterByCcSchedule($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcWebstreamMetadatasPartial && count($collCcWebstreamMetadatas)) {
                      $this->initCcWebstreamMetadatas(false);

                      foreach ($collCcWebstreamMetadatas as $obj) {
                        if (false == $this->collCcWebstreamMetadatas->contains($obj)) {
                          $this->collCcWebstreamMetadatas->append($obj);
                        }
                      }

                      $this->collCcWebstreamMetadatasPartial = true;
                    }

                    $collCcWebstreamMetadatas->getInternalIterator()->rewind();

                    return $collCcWebstreamMetadatas;
                }

                if ($partial && $this->collCcWebstreamMetadatas) {
                    foreach ($this->collCcWebstreamMetadatas as $obj) {
                        if ($obj->isNew()) {
                            $collCcWebstreamMetadatas[] = $obj;
                        }
                    }
                }

                $this->collCcWebstreamMetadatas = $collCcWebstreamMetadatas;
                $this->collCcWebstreamMetadatasPartial = false;
            }
        }

        return $this->collCcWebstreamMetadatas;
    }

    /**
     * Sets a collection of CcWebstreamMetadata objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccWebstreamMetadatas A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSchedule The current object (for fluent API support)
     */
    public function setCcWebstreamMetadatas(PropelCollection $ccWebstreamMetadatas, PropelPDO $con = null)
    {
        $ccWebstreamMetadatasToDelete = $this->getCcWebstreamMetadatas(new Criteria(), $con)->diff($ccWebstreamMetadatas);


        $this->ccWebstreamMetadatasScheduledForDeletion = $ccWebstreamMetadatasToDelete;

        foreach ($ccWebstreamMetadatasToDelete as $ccWebstreamMetadataRemoved) {
            $ccWebstreamMetadataRemoved->setCcSchedule(null);
        }

        $this->collCcWebstreamMetadatas = null;
        foreach ($ccWebstreamMetadatas as $ccWebstreamMetadata) {
            $this->addCcWebstreamMetadata($ccWebstreamMetadata);
        }

        $this->collCcWebstreamMetadatas = $ccWebstreamMetadatas;
        $this->collCcWebstreamMetadatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcWebstreamMetadata objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcWebstreamMetadata objects.
     * @throws PropelException
     */
    public function countCcWebstreamMetadatas(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcWebstreamMetadatasPartial && !$this->isNew();
        if (null === $this->collCcWebstreamMetadatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcWebstreamMetadatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcWebstreamMetadatas());
            }
            $query = CcWebstreamMetadataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSchedule($this)
                ->count($con);
        }

        return count($this->collCcWebstreamMetadatas);
    }

    /**
     * Method called to associate a CcWebstreamMetadata object to this object
     * through the CcWebstreamMetadata foreign key attribute.
     *
     * @param    CcWebstreamMetadata $l CcWebstreamMetadata
     * @return CcSchedule The current object (for fluent API support)
     */
    public function addCcWebstreamMetadata(CcWebstreamMetadata $l)
    {
        if ($this->collCcWebstreamMetadatas === null) {
            $this->initCcWebstreamMetadatas();
            $this->collCcWebstreamMetadatasPartial = true;
        }

        if (!in_array($l, $this->collCcWebstreamMetadatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcWebstreamMetadata($l);

            if ($this->ccWebstreamMetadatasScheduledForDeletion and $this->ccWebstreamMetadatasScheduledForDeletion->contains($l)) {
                $this->ccWebstreamMetadatasScheduledForDeletion->remove($this->ccWebstreamMetadatasScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcWebstreamMetadata $ccWebstreamMetadata The ccWebstreamMetadata object to add.
     */
    protected function doAddCcWebstreamMetadata($ccWebstreamMetadata)
    {
        $this->collCcWebstreamMetadatas[]= $ccWebstreamMetadata;
        $ccWebstreamMetadata->setCcSchedule($this);
    }

    /**
     * @param	CcWebstreamMetadata $ccWebstreamMetadata The ccWebstreamMetadata object to remove.
     * @return CcSchedule The current object (for fluent API support)
     */
    public function removeCcWebstreamMetadata($ccWebstreamMetadata)
    {
        if ($this->getCcWebstreamMetadatas()->contains($ccWebstreamMetadata)) {
            $this->collCcWebstreamMetadatas->remove($this->collCcWebstreamMetadatas->search($ccWebstreamMetadata));
            if (null === $this->ccWebstreamMetadatasScheduledForDeletion) {
                $this->ccWebstreamMetadatasScheduledForDeletion = clone $this->collCcWebstreamMetadatas;
                $this->ccWebstreamMetadatasScheduledForDeletion->clear();
            }
            $this->ccWebstreamMetadatasScheduledForDeletion[]= clone $ccWebstreamMetadata;
            $ccWebstreamMetadata->setCcSchedule(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->starts = null;
        $this->ends = null;
        $this->file_id = null;
        $this->stream_id = null;
        $this->clip_length = null;
        $this->fade_in = null;
        $this->fade_out = null;
        $this->cue_in = null;
        $this->cue_out = null;
        $this->media_item_played = null;
        $this->instance_id = null;
        $this->playout_status = null;
        $this->broadcasted = null;
        $this->position = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collCcWebstreamMetadatas) {
                foreach ($this->collCcWebstreamMetadatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcShowInstances instanceof Persistent) {
              $this->aCcShowInstances->clearAllReferences($deep);
            }
            if ($this->aCcFiles instanceof Persistent) {
              $this->aCcFiles->clearAllReferences($deep);
            }
            if ($this->aCcWebstream instanceof Persistent) {
              $this->aCcWebstream->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcWebstreamMetadatas instanceof PropelCollection) {
            $this->collCcWebstreamMetadatas->clearIterator();
        }
        $this->collCcWebstreamMetadatas = null;
        $this->aCcShowInstances = null;
        $this->aCcFiles = null;
        $this->aCcWebstream = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcSchedulePeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
