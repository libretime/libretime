<?php


/**
 * Base class that represents a row from the 'cc_show_instances' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowInstances extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcShowInstancesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcShowInstancesPeer
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
     * The value for the description field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $description;

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
     * The value for the show_id field.
     * @var        int
     */
    protected $show_id;

    /**
     * The value for the record field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $record;

    /**
     * The value for the rebroadcast field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $rebroadcast;

    /**
     * The value for the instance_id field.
     * @var        int
     */
    protected $instance_id;

    /**
     * The value for the file_id field.
     * @var        int
     */
    protected $file_id;

    /**
     * The value for the time_filled field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $time_filled;

    /**
     * The value for the created field.
     * @var        string
     */
    protected $created;

    /**
     * The value for the last_scheduled field.
     * @var        string
     */
    protected $last_scheduled;

    /**
     * The value for the modified_instance field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $modified_instance;

    /**
     * The value for the autoplaylist_built field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $autoplaylist_built;

    /**
     * @var        CcShow
     */
    protected $aCcShow;

    /**
     * @var        CcShowInstances
     */
    protected $aCcShowInstancesRelatedByDbOriginalShow;

    /**
     * @var        CcFiles
     */
    protected $aCcFiles;

    /**
     * @var        PropelObjectCollection|CcShowInstances[] Collection to store aggregation of CcShowInstances objects.
     */
    protected $collCcShowInstancessRelatedByDbId;
    protected $collCcShowInstancessRelatedByDbIdPartial;

    /**
     * @var        PropelObjectCollection|CcSchedule[] Collection to store aggregation of CcSchedule objects.
     */
    protected $collCcSchedules;
    protected $collCcSchedulesPartial;

    /**
     * @var        PropelObjectCollection|CcPlayoutHistory[] Collection to store aggregation of CcPlayoutHistory objects.
     */
    protected $collCcPlayoutHistorys;
    protected $collCcPlayoutHistorysPartial;

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
    protected $ccShowInstancessRelatedByDbIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccSchedulesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccPlayoutHistorysScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->description = '';
        $this->record = 0;
        $this->rebroadcast = 0;
        $this->time_filled = '00:00:00';
        $this->modified_instance = false;
        $this->autoplaylist_built = false;
    }

    /**
     * Initializes internal state of BaseCcShowInstances object.
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
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDbDescription()
    {

        return $this->description;
    }

    /**
     * Get the [optionally formatted] temporal [starts] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
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
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [ends] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
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
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [show_id] column value.
     *
     * @return int
     */
    public function getDbShowId()
    {

        return $this->show_id;
    }

    /**
     * Get the [record] column value.
     *
     * @return int
     */
    public function getDbRecord()
    {

        return $this->record;
    }

    /**
     * Get the [rebroadcast] column value.
     *
     * @return int
     */
    public function getDbRebroadcast()
    {

        return $this->rebroadcast;
    }

    /**
     * Get the [instance_id] column value.
     *
     * @return int
     */
    public function getDbOriginalShow()
    {

        return $this->instance_id;
    }

    /**
     * Get the [file_id] column value.
     *
     * @return int
     */
    public function getDbRecordedFile()
    {

        return $this->file_id;
    }

    /**
     * Get the [time_filled] column value.
     *
     * @return string
     */
    public function getDbTimeFilled()
    {

        return $this->time_filled;
    }

    /**
     * Get the [optionally formatted] temporal [created] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbCreated($format = 'Y-m-d H:i:s')
    {
        if ($this->created === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->created);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [last_scheduled] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLastScheduled($format = 'Y-m-d H:i:s')
    {
        if ($this->last_scheduled === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_scheduled);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_scheduled, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [modified_instance] column value.
     *
     * @return boolean
     */
    public function getDbModifiedInstance()
    {

        return $this->modified_instance;
    }

    /**
     * Get the [autoplaylist_built] column value.
     *
     * @return boolean
     */
    public function getDbAutoPlaylistBuilt()
    {

        return $this->autoplaylist_built;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Sets the value of [starts] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbStarts($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->starts !== null || $dt !== null) {
            $currentDateAsString = ($this->starts !== null && $tmpDt = new DateTime($this->starts)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->starts = $newDateAsString;
                $this->modifiedColumns[] = CcShowInstancesPeer::STARTS;
            }
        } // if either are not null


        return $this;
    } // setDbStarts()

    /**
     * Sets the value of [ends] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbEnds($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->ends !== null || $dt !== null) {
            $currentDateAsString = ($this->ends !== null && $tmpDt = new DateTime($this->ends)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->ends = $newDateAsString;
                $this->modifiedColumns[] = CcShowInstancesPeer::ENDS;
            }
        } // if either are not null


        return $this;
    } // setDbEnds()

    /**
     * Set the value of [show_id] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbShowId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->show_id !== $v) {
            $this->show_id = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::SHOW_ID;
        }

        if ($this->aCcShow !== null && $this->aCcShow->getDbId() !== $v) {
            $this->aCcShow = null;
        }


        return $this;
    } // setDbShowId()

    /**
     * Set the value of [record] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbRecord($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->record !== $v) {
            $this->record = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::RECORD;
        }


        return $this;
    } // setDbRecord()

    /**
     * Set the value of [rebroadcast] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbRebroadcast($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rebroadcast !== $v) {
            $this->rebroadcast = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::REBROADCAST;
        }


        return $this;
    } // setDbRebroadcast()

    /**
     * Set the value of [instance_id] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbOriginalShow($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->instance_id !== $v) {
            $this->instance_id = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::INSTANCE_ID;
        }

        if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null && $this->aCcShowInstancesRelatedByDbOriginalShow->getDbId() !== $v) {
            $this->aCcShowInstancesRelatedByDbOriginalShow = null;
        }


        return $this;
    } // setDbOriginalShow()

    /**
     * Set the value of [file_id] column.
     *
     * @param  int $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbRecordedFile($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->file_id !== $v) {
            $this->file_id = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::FILE_ID;
        }

        if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
            $this->aCcFiles = null;
        }


        return $this;
    } // setDbRecordedFile()

    /**
     * Set the value of [time_filled] column.
     *
     * @param  string $v new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbTimeFilled($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->time_filled !== $v) {
            $this->time_filled = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::TIME_FILLED;
        }


        return $this;
    } // setDbTimeFilled()

    /**
     * Sets the value of [created] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbCreated($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created !== null || $dt !== null) {
            $currentDateAsString = ($this->created !== null && $tmpDt = new DateTime($this->created)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created = $newDateAsString;
                $this->modifiedColumns[] = CcShowInstancesPeer::CREATED;
            }
        } // if either are not null


        return $this;
    } // setDbCreated()

    /**
     * Sets the value of [last_scheduled] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbLastScheduled($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_scheduled !== null || $dt !== null) {
            $currentDateAsString = ($this->last_scheduled !== null && $tmpDt = new DateTime($this->last_scheduled)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_scheduled = $newDateAsString;
                $this->modifiedColumns[] = CcShowInstancesPeer::LAST_SCHEDULED;
            }
        } // if either are not null


        return $this;
    } // setDbLastScheduled()

    /**
     * Sets the value of the [modified_instance] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbModifiedInstance($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->modified_instance !== $v) {
            $this->modified_instance = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::MODIFIED_INSTANCE;
        }


        return $this;
    } // setDbModifiedInstance()

    /**
     * Sets the value of the [autoplaylist_built] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setDbAutoPlaylistBuilt($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->autoplaylist_built !== $v) {
            $this->autoplaylist_built = $v;
            $this->modifiedColumns[] = CcShowInstancesPeer::AUTOPLAYLIST_BUILT;
        }


        return $this;
    } // setDbAutoPlaylistBuilt()

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
            if ($this->description !== '') {
                return false;
            }

            if ($this->record !== 0) {
                return false;
            }

            if ($this->rebroadcast !== 0) {
                return false;
            }

            if ($this->time_filled !== '00:00:00') {
                return false;
            }

            if ($this->modified_instance !== false) {
                return false;
            }

            if ($this->autoplaylist_built !== false) {
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
            $this->description = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->starts = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->ends = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->show_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->record = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->rebroadcast = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->instance_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->file_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->time_filled = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->created = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->last_scheduled = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->modified_instance = ($row[$startcol + 12] !== null) ? (boolean) $row[$startcol + 12] : null;
            $this->autoplaylist_built = ($row[$startcol + 13] !== null) ? (boolean) $row[$startcol + 13] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 14; // 14 = CcShowInstancesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcShowInstances object", $e);
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

        if ($this->aCcShow !== null && $this->show_id !== $this->aCcShow->getDbId()) {
            $this->aCcShow = null;
        }
        if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null && $this->instance_id !== $this->aCcShowInstancesRelatedByDbOriginalShow->getDbId()) {
            $this->aCcShowInstancesRelatedByDbOriginalShow = null;
        }
        if ($this->aCcFiles !== null && $this->file_id !== $this->aCcFiles->getDbId()) {
            $this->aCcFiles = null;
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
            $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcShowInstancesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcShow = null;
            $this->aCcShowInstancesRelatedByDbOriginalShow = null;
            $this->aCcFiles = null;
            $this->collCcShowInstancessRelatedByDbId = null;

            $this->collCcSchedules = null;

            $this->collCcPlayoutHistorys = null;

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
            $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcShowInstancesQuery::create()
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
            $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcShowInstancesPeer::addInstanceToPool($this);
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

            if ($this->aCcShow !== null) {
                if ($this->aCcShow->isModified() || $this->aCcShow->isNew()) {
                    $affectedRows += $this->aCcShow->save($con);
                }
                $this->setCcShow($this->aCcShow);
            }

            if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null) {
                if ($this->aCcShowInstancesRelatedByDbOriginalShow->isModified() || $this->aCcShowInstancesRelatedByDbOriginalShow->isNew()) {
                    $affectedRows += $this->aCcShowInstancesRelatedByDbOriginalShow->save($con);
                }
                $this->setCcShowInstancesRelatedByDbOriginalShow($this->aCcShowInstancesRelatedByDbOriginalShow);
            }

            if ($this->aCcFiles !== null) {
                if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
                    $affectedRows += $this->aCcFiles->save($con);
                }
                $this->setCcFiles($this->aCcFiles);
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

            if ($this->ccShowInstancessRelatedByDbIdScheduledForDeletion !== null) {
                if (!$this->ccShowInstancessRelatedByDbIdScheduledForDeletion->isEmpty()) {
                    CcShowInstancesQuery::create()
                        ->filterByPrimaryKeys($this->ccShowInstancessRelatedByDbIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowInstancessRelatedByDbIdScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowInstancessRelatedByDbId !== null) {
                foreach ($this->collCcShowInstancessRelatedByDbId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccSchedulesScheduledForDeletion !== null) {
                if (!$this->ccSchedulesScheduledForDeletion->isEmpty()) {
                    CcScheduleQuery::create()
                        ->filterByPrimaryKeys($this->ccSchedulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccSchedulesScheduledForDeletion = null;
                }
            }

            if ($this->collCcSchedules !== null) {
                foreach ($this->collCcSchedules as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccPlayoutHistorysScheduledForDeletion !== null) {
                if (!$this->ccPlayoutHistorysScheduledForDeletion->isEmpty()) {
                    foreach ($this->ccPlayoutHistorysScheduledForDeletion as $ccPlayoutHistory) {
                        // need to save related object because we set the relation to null
                        $ccPlayoutHistory->save($con);
                    }
                    $this->ccPlayoutHistorysScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlayoutHistorys !== null) {
                foreach ($this->collCcPlayoutHistorys as $referrerFK) {
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

        $this->modifiedColumns[] = CcShowInstancesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcShowInstancesPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_show_instances_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcShowInstancesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::STARTS)) {
            $modifiedColumns[':p' . $index++]  = '"starts"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::ENDS)) {
            $modifiedColumns[':p' . $index++]  = '"ends"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::SHOW_ID)) {
            $modifiedColumns[':p' . $index++]  = '"show_id"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::RECORD)) {
            $modifiedColumns[':p' . $index++]  = '"record"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::REBROADCAST)) {
            $modifiedColumns[':p' . $index++]  = '"rebroadcast"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::INSTANCE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"instance_id"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::FILE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"file_id"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::TIME_FILLED)) {
            $modifiedColumns[':p' . $index++]  = '"time_filled"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::CREATED)) {
            $modifiedColumns[':p' . $index++]  = '"created"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::LAST_SCHEDULED)) {
            $modifiedColumns[':p' . $index++]  = '"last_scheduled"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::MODIFIED_INSTANCE)) {
            $modifiedColumns[':p' . $index++]  = '"modified_instance"';
        }
        if ($this->isColumnModified(CcShowInstancesPeer::AUTOPLAYLIST_BUILT)) {
            $modifiedColumns[':p' . $index++]  = '"autoplaylist_built"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_show_instances" (%s) VALUES (%s)',
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
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"starts"':
                        $stmt->bindValue($identifier, $this->starts, PDO::PARAM_STR);
                        break;
                    case '"ends"':
                        $stmt->bindValue($identifier, $this->ends, PDO::PARAM_STR);
                        break;
                    case '"show_id"':
                        $stmt->bindValue($identifier, $this->show_id, PDO::PARAM_INT);
                        break;
                    case '"record"':
                        $stmt->bindValue($identifier, $this->record, PDO::PARAM_INT);
                        break;
                    case '"rebroadcast"':
                        $stmt->bindValue($identifier, $this->rebroadcast, PDO::PARAM_INT);
                        break;
                    case '"instance_id"':
                        $stmt->bindValue($identifier, $this->instance_id, PDO::PARAM_INT);
                        break;
                    case '"file_id"':
                        $stmt->bindValue($identifier, $this->file_id, PDO::PARAM_INT);
                        break;
                    case '"time_filled"':
                        $stmt->bindValue($identifier, $this->time_filled, PDO::PARAM_STR);
                        break;
                    case '"created"':
                        $stmt->bindValue($identifier, $this->created, PDO::PARAM_STR);
                        break;
                    case '"last_scheduled"':
                        $stmt->bindValue($identifier, $this->last_scheduled, PDO::PARAM_STR);
                        break;
                    case '"modified_instance"':
                        $stmt->bindValue($identifier, $this->modified_instance, PDO::PARAM_BOOL);
                        break;
                    case '"autoplaylist_built"':
                        $stmt->bindValue($identifier, $this->autoplaylist_built, PDO::PARAM_BOOL);
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

            if ($this->aCcShow !== null) {
                if (!$this->aCcShow->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcShow->getValidationFailures());
                }
            }

            if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null) {
                if (!$this->aCcShowInstancesRelatedByDbOriginalShow->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcShowInstancesRelatedByDbOriginalShow->getValidationFailures());
                }
            }

            if ($this->aCcFiles !== null) {
                if (!$this->aCcFiles->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
                }
            }


            if (($retval = CcShowInstancesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcShowInstancessRelatedByDbId !== null) {
                    foreach ($this->collCcShowInstancessRelatedByDbId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcSchedules !== null) {
                    foreach ($this->collCcSchedules as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcPlayoutHistorys !== null) {
                    foreach ($this->collCcPlayoutHistorys as $referrerFK) {
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
        $pos = CcShowInstancesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbDescription();
                break;
            case 2:
                return $this->getDbStarts();
                break;
            case 3:
                return $this->getDbEnds();
                break;
            case 4:
                return $this->getDbShowId();
                break;
            case 5:
                return $this->getDbRecord();
                break;
            case 6:
                return $this->getDbRebroadcast();
                break;
            case 7:
                return $this->getDbOriginalShow();
                break;
            case 8:
                return $this->getDbRecordedFile();
                break;
            case 9:
                return $this->getDbTimeFilled();
                break;
            case 10:
                return $this->getDbCreated();
                break;
            case 11:
                return $this->getDbLastScheduled();
                break;
            case 12:
                return $this->getDbModifiedInstance();
                break;
            case 13:
                return $this->getDbAutoPlaylistBuilt();
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
        if (isset($alreadyDumpedObjects['CcShowInstances'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcShowInstances'][$this->getPrimaryKey()] = true;
        $keys = CcShowInstancesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbDescription(),
            $keys[2] => $this->getDbStarts(),
            $keys[3] => $this->getDbEnds(),
            $keys[4] => $this->getDbShowId(),
            $keys[5] => $this->getDbRecord(),
            $keys[6] => $this->getDbRebroadcast(),
            $keys[7] => $this->getDbOriginalShow(),
            $keys[8] => $this->getDbRecordedFile(),
            $keys[9] => $this->getDbTimeFilled(),
            $keys[10] => $this->getDbCreated(),
            $keys[11] => $this->getDbLastScheduled(),
            $keys[12] => $this->getDbModifiedInstance(),
            $keys[13] => $this->getDbAutoPlaylistBuilt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcShow) {
                $result['CcShow'] = $this->aCcShow->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcShowInstancesRelatedByDbOriginalShow) {
                $result['CcShowInstancesRelatedByDbOriginalShow'] = $this->aCcShowInstancesRelatedByDbOriginalShow->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcFiles) {
                $result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcShowInstancessRelatedByDbId) {
                $result['CcShowInstancessRelatedByDbId'] = $this->collCcShowInstancessRelatedByDbId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcSchedules) {
                $result['CcSchedules'] = $this->collCcSchedules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcPlayoutHistorys) {
                $result['CcPlayoutHistorys'] = $this->collCcPlayoutHistorys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcShowInstancesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbDescription($value);
                break;
            case 2:
                $this->setDbStarts($value);
                break;
            case 3:
                $this->setDbEnds($value);
                break;
            case 4:
                $this->setDbShowId($value);
                break;
            case 5:
                $this->setDbRecord($value);
                break;
            case 6:
                $this->setDbRebroadcast($value);
                break;
            case 7:
                $this->setDbOriginalShow($value);
                break;
            case 8:
                $this->setDbRecordedFile($value);
                break;
            case 9:
                $this->setDbTimeFilled($value);
                break;
            case 10:
                $this->setDbCreated($value);
                break;
            case 11:
                $this->setDbLastScheduled($value);
                break;
            case 12:
                $this->setDbModifiedInstance($value);
                break;
            case 13:
                $this->setDbAutoPlaylistBuilt($value);
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
        $keys = CcShowInstancesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbDescription($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbStarts($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbEnds($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbShowId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbRecord($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbRebroadcast($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbOriginalShow($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbRecordedFile($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbTimeFilled($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbCreated($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbLastScheduled($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbModifiedInstance($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDbAutoPlaylistBuilt($arr[$keys[13]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcShowInstancesPeer::ID)) $criteria->add(CcShowInstancesPeer::ID, $this->id);
        if ($this->isColumnModified(CcShowInstancesPeer::DESCRIPTION)) $criteria->add(CcShowInstancesPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CcShowInstancesPeer::STARTS)) $criteria->add(CcShowInstancesPeer::STARTS, $this->starts);
        if ($this->isColumnModified(CcShowInstancesPeer::ENDS)) $criteria->add(CcShowInstancesPeer::ENDS, $this->ends);
        if ($this->isColumnModified(CcShowInstancesPeer::SHOW_ID)) $criteria->add(CcShowInstancesPeer::SHOW_ID, $this->show_id);
        if ($this->isColumnModified(CcShowInstancesPeer::RECORD)) $criteria->add(CcShowInstancesPeer::RECORD, $this->record);
        if ($this->isColumnModified(CcShowInstancesPeer::REBROADCAST)) $criteria->add(CcShowInstancesPeer::REBROADCAST, $this->rebroadcast);
        if ($this->isColumnModified(CcShowInstancesPeer::INSTANCE_ID)) $criteria->add(CcShowInstancesPeer::INSTANCE_ID, $this->instance_id);
        if ($this->isColumnModified(CcShowInstancesPeer::FILE_ID)) $criteria->add(CcShowInstancesPeer::FILE_ID, $this->file_id);
        if ($this->isColumnModified(CcShowInstancesPeer::TIME_FILLED)) $criteria->add(CcShowInstancesPeer::TIME_FILLED, $this->time_filled);
        if ($this->isColumnModified(CcShowInstancesPeer::CREATED)) $criteria->add(CcShowInstancesPeer::CREATED, $this->created);
        if ($this->isColumnModified(CcShowInstancesPeer::LAST_SCHEDULED)) $criteria->add(CcShowInstancesPeer::LAST_SCHEDULED, $this->last_scheduled);
        if ($this->isColumnModified(CcShowInstancesPeer::MODIFIED_INSTANCE)) $criteria->add(CcShowInstancesPeer::MODIFIED_INSTANCE, $this->modified_instance);
        if ($this->isColumnModified(CcShowInstancesPeer::AUTOPLAYLIST_BUILT)) $criteria->add(CcShowInstancesPeer::AUTOPLAYLIST_BUILT, $this->autoplaylist_built);

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
        $criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);
        $criteria->add(CcShowInstancesPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcShowInstances (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbStarts($this->getDbStarts());
        $copyObj->setDbEnds($this->getDbEnds());
        $copyObj->setDbShowId($this->getDbShowId());
        $copyObj->setDbRecord($this->getDbRecord());
        $copyObj->setDbRebroadcast($this->getDbRebroadcast());
        $copyObj->setDbOriginalShow($this->getDbOriginalShow());
        $copyObj->setDbRecordedFile($this->getDbRecordedFile());
        $copyObj->setDbTimeFilled($this->getDbTimeFilled());
        $copyObj->setDbCreated($this->getDbCreated());
        $copyObj->setDbLastScheduled($this->getDbLastScheduled());
        $copyObj->setDbModifiedInstance($this->getDbModifiedInstance());
        $copyObj->setDbAutoPlaylistBuilt($this->getDbAutoPlaylistBuilt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcShowInstancessRelatedByDbId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowInstancesRelatedByDbId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcSchedules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcSchedule($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcPlayoutHistorys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlayoutHistory($relObj->copy($deepCopy));
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
     * @return CcShowInstances Clone of current object.
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
     * @return CcShowInstancesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcShowInstancesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcShow object.
     *
     * @param                  CcShow $v
     * @return CcShowInstances The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcShow(CcShow $v = null)
    {
        if ($v === null) {
            $this->setDbShowId(NULL);
        } else {
            $this->setDbShowId($v->getDbId());
        }

        $this->aCcShow = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcShow object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowInstances($this);
        }


        return $this;
    }


    /**
     * Get the associated CcShow object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcShow The associated CcShow object.
     * @throws PropelException
     */
    public function getCcShow(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcShow === null && ($this->show_id !== null) && $doQuery) {
            $this->aCcShow = CcShowQuery::create()->findPk($this->show_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcShow->addCcShowInstancess($this);
             */
        }

        return $this->aCcShow;
    }

    /**
     * Declares an association between this object and a CcShowInstances object.
     *
     * @param                  CcShowInstances $v
     * @return CcShowInstances The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcShowInstancesRelatedByDbOriginalShow(CcShowInstances $v = null)
    {
        if ($v === null) {
            $this->setDbOriginalShow(NULL);
        } else {
            $this->setDbOriginalShow($v->getDbId());
        }

        $this->aCcShowInstancesRelatedByDbOriginalShow = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcShowInstances object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowInstancesRelatedByDbId($this);
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
    public function getCcShowInstancesRelatedByDbOriginalShow(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcShowInstancesRelatedByDbOriginalShow === null && ($this->instance_id !== null) && $doQuery) {
            $this->aCcShowInstancesRelatedByDbOriginalShow = CcShowInstancesQuery::create()->findPk($this->instance_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcShowInstancesRelatedByDbOriginalShow->addCcShowInstancessRelatedByDbId($this);
             */
        }

        return $this->aCcShowInstancesRelatedByDbOriginalShow;
    }

    /**
     * Declares an association between this object and a CcFiles object.
     *
     * @param                  CcFiles $v
     * @return CcShowInstances The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcFiles(CcFiles $v = null)
    {
        if ($v === null) {
            $this->setDbRecordedFile(NULL);
        } else {
            $this->setDbRecordedFile($v->getDbId());
        }

        $this->aCcFiles = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcFiles object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowInstances($this);
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
                $this->aCcFiles->addCcShowInstancess($this);
             */
        }

        return $this->aCcFiles;
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
        if ('CcShowInstancesRelatedByDbId' == $relationName) {
            $this->initCcShowInstancessRelatedByDbId();
        }
        if ('CcSchedule' == $relationName) {
            $this->initCcSchedules();
        }
        if ('CcPlayoutHistory' == $relationName) {
            $this->initCcPlayoutHistorys();
        }
    }

    /**
     * Clears out the collCcShowInstancessRelatedByDbId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShowInstances The current object (for fluent API support)
     * @see        addCcShowInstancessRelatedByDbId()
     */
    public function clearCcShowInstancessRelatedByDbId()
    {
        $this->collCcShowInstancessRelatedByDbId = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowInstancessRelatedByDbIdPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowInstancessRelatedByDbId collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowInstancessRelatedByDbId($v = true)
    {
        $this->collCcShowInstancessRelatedByDbIdPartial = $v;
    }

    /**
     * Initializes the collCcShowInstancessRelatedByDbId collection.
     *
     * By default this just sets the collCcShowInstancessRelatedByDbId collection to an empty array (like clearcollCcShowInstancessRelatedByDbId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowInstancessRelatedByDbId($overrideExisting = true)
    {
        if (null !== $this->collCcShowInstancessRelatedByDbId && !$overrideExisting) {
            return;
        }
        $this->collCcShowInstancessRelatedByDbId = new PropelObjectCollection();
        $this->collCcShowInstancessRelatedByDbId->setModel('CcShowInstances');
    }

    /**
     * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShowInstances is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     * @throws PropelException
     */
    public function getCcShowInstancessRelatedByDbId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessRelatedByDbIdPartial && !$this->isNew();
        if (null === $this->collCcShowInstancessRelatedByDbId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancessRelatedByDbId) {
                // return empty collection
                $this->initCcShowInstancessRelatedByDbId();
            } else {
                $collCcShowInstancessRelatedByDbId = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByCcShowInstancesRelatedByDbOriginalShow($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowInstancessRelatedByDbIdPartial && count($collCcShowInstancessRelatedByDbId)) {
                      $this->initCcShowInstancessRelatedByDbId(false);

                      foreach ($collCcShowInstancessRelatedByDbId as $obj) {
                        if (false == $this->collCcShowInstancessRelatedByDbId->contains($obj)) {
                          $this->collCcShowInstancessRelatedByDbId->append($obj);
                        }
                      }

                      $this->collCcShowInstancessRelatedByDbIdPartial = true;
                    }

                    $collCcShowInstancessRelatedByDbId->getInternalIterator()->rewind();

                    return $collCcShowInstancessRelatedByDbId;
                }

                if ($partial && $this->collCcShowInstancessRelatedByDbId) {
                    foreach ($this->collCcShowInstancessRelatedByDbId as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowInstancessRelatedByDbId[] = $obj;
                        }
                    }
                }

                $this->collCcShowInstancessRelatedByDbId = $collCcShowInstancessRelatedByDbId;
                $this->collCcShowInstancessRelatedByDbIdPartial = false;
            }
        }

        return $this->collCcShowInstancessRelatedByDbId;
    }

    /**
     * Sets a collection of CcShowInstancesRelatedByDbId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowInstancessRelatedByDbId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setCcShowInstancessRelatedByDbId(PropelCollection $ccShowInstancessRelatedByDbId, PropelPDO $con = null)
    {
        $ccShowInstancessRelatedByDbIdToDelete = $this->getCcShowInstancessRelatedByDbId(new Criteria(), $con)->diff($ccShowInstancessRelatedByDbId);


        $this->ccShowInstancessRelatedByDbIdScheduledForDeletion = $ccShowInstancessRelatedByDbIdToDelete;

        foreach ($ccShowInstancessRelatedByDbIdToDelete as $ccShowInstancesRelatedByDbIdRemoved) {
            $ccShowInstancesRelatedByDbIdRemoved->setCcShowInstancesRelatedByDbOriginalShow(null);
        }

        $this->collCcShowInstancessRelatedByDbId = null;
        foreach ($ccShowInstancessRelatedByDbId as $ccShowInstancesRelatedByDbId) {
            $this->addCcShowInstancesRelatedByDbId($ccShowInstancesRelatedByDbId);
        }

        $this->collCcShowInstancessRelatedByDbId = $ccShowInstancessRelatedByDbId;
        $this->collCcShowInstancessRelatedByDbIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowInstances objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowInstances objects.
     * @throws PropelException
     */
    public function countCcShowInstancessRelatedByDbId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessRelatedByDbIdPartial && !$this->isNew();
        if (null === $this->collCcShowInstancessRelatedByDbId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancessRelatedByDbId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowInstancessRelatedByDbId());
            }
            $query = CcShowInstancesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcShowInstancesRelatedByDbOriginalShow($this)
                ->count($con);
        }

        return count($this->collCcShowInstancessRelatedByDbId);
    }

    /**
     * Method called to associate a CcShowInstances object to this object
     * through the CcShowInstances foreign key attribute.
     *
     * @param    CcShowInstances $l CcShowInstances
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function addCcShowInstancesRelatedByDbId(CcShowInstances $l)
    {
        if ($this->collCcShowInstancessRelatedByDbId === null) {
            $this->initCcShowInstancessRelatedByDbId();
            $this->collCcShowInstancessRelatedByDbIdPartial = true;
        }

        if (!in_array($l, $this->collCcShowInstancessRelatedByDbId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowInstancesRelatedByDbId($l);

            if ($this->ccShowInstancessRelatedByDbIdScheduledForDeletion and $this->ccShowInstancessRelatedByDbIdScheduledForDeletion->contains($l)) {
                $this->ccShowInstancessRelatedByDbIdScheduledForDeletion->remove($this->ccShowInstancessRelatedByDbIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowInstancesRelatedByDbId $ccShowInstancesRelatedByDbId The ccShowInstancesRelatedByDbId object to add.
     */
    protected function doAddCcShowInstancesRelatedByDbId($ccShowInstancesRelatedByDbId)
    {
        $this->collCcShowInstancessRelatedByDbId[]= $ccShowInstancesRelatedByDbId;
        $ccShowInstancesRelatedByDbId->setCcShowInstancesRelatedByDbOriginalShow($this);
    }

    /**
     * @param	CcShowInstancesRelatedByDbId $ccShowInstancesRelatedByDbId The ccShowInstancesRelatedByDbId object to remove.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function removeCcShowInstancesRelatedByDbId($ccShowInstancesRelatedByDbId)
    {
        if ($this->getCcShowInstancessRelatedByDbId()->contains($ccShowInstancesRelatedByDbId)) {
            $this->collCcShowInstancessRelatedByDbId->remove($this->collCcShowInstancessRelatedByDbId->search($ccShowInstancesRelatedByDbId));
            if (null === $this->ccShowInstancessRelatedByDbIdScheduledForDeletion) {
                $this->ccShowInstancessRelatedByDbIdScheduledForDeletion = clone $this->collCcShowInstancessRelatedByDbId;
                $this->ccShowInstancessRelatedByDbIdScheduledForDeletion->clear();
            }
            $this->ccShowInstancessRelatedByDbIdScheduledForDeletion[]= $ccShowInstancesRelatedByDbId;
            $ccShowInstancesRelatedByDbId->setCcShowInstancesRelatedByDbOriginalShow(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShowInstances is new, it will return
     * an empty collection; or if this CcShowInstances has previously
     * been saved, it will retrieve related CcShowInstancessRelatedByDbId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShowInstances.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessRelatedByDbIdJoinCcShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcShow', $join_behavior);

        return $this->getCcShowInstancessRelatedByDbId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShowInstances is new, it will return
     * an empty collection; or if this CcShowInstances has previously
     * been saved, it will retrieve related CcShowInstancessRelatedByDbId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShowInstances.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessRelatedByDbIdJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcShowInstancessRelatedByDbId($query, $con);
    }

    /**
     * Clears out the collCcSchedules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShowInstances The current object (for fluent API support)
     * @see        addCcSchedules()
     */
    public function clearCcSchedules()
    {
        $this->collCcSchedules = null; // important to set this to null since that means it is uninitialized
        $this->collCcSchedulesPartial = null;

        return $this;
    }

    /**
     * reset is the collCcSchedules collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcSchedules($v = true)
    {
        $this->collCcSchedulesPartial = $v;
    }

    /**
     * Initializes the collCcSchedules collection.
     *
     * By default this just sets the collCcSchedules collection to an empty array (like clearcollCcSchedules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcSchedules($overrideExisting = true)
    {
        if (null !== $this->collCcSchedules && !$overrideExisting) {
            return;
        }
        $this->collCcSchedules = new PropelObjectCollection();
        $this->collCcSchedules->setModel('CcSchedule');
    }

    /**
     * Gets an array of CcSchedule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShowInstances is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     * @throws PropelException
     */
    public function getCcSchedules($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcSchedulesPartial && !$this->isNew();
        if (null === $this->collCcSchedules || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcSchedules) {
                // return empty collection
                $this->initCcSchedules();
            } else {
                $collCcSchedules = CcScheduleQuery::create(null, $criteria)
                    ->filterByCcShowInstances($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcSchedulesPartial && count($collCcSchedules)) {
                      $this->initCcSchedules(false);

                      foreach ($collCcSchedules as $obj) {
                        if (false == $this->collCcSchedules->contains($obj)) {
                          $this->collCcSchedules->append($obj);
                        }
                      }

                      $this->collCcSchedulesPartial = true;
                    }

                    $collCcSchedules->getInternalIterator()->rewind();

                    return $collCcSchedules;
                }

                if ($partial && $this->collCcSchedules) {
                    foreach ($this->collCcSchedules as $obj) {
                        if ($obj->isNew()) {
                            $collCcSchedules[] = $obj;
                        }
                    }
                }

                $this->collCcSchedules = $collCcSchedules;
                $this->collCcSchedulesPartial = false;
            }
        }

        return $this->collCcSchedules;
    }

    /**
     * Sets a collection of CcSchedule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccSchedules A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setCcSchedules(PropelCollection $ccSchedules, PropelPDO $con = null)
    {
        $ccSchedulesToDelete = $this->getCcSchedules(new Criteria(), $con)->diff($ccSchedules);


        $this->ccSchedulesScheduledForDeletion = $ccSchedulesToDelete;

        foreach ($ccSchedulesToDelete as $ccScheduleRemoved) {
            $ccScheduleRemoved->setCcShowInstances(null);
        }

        $this->collCcSchedules = null;
        foreach ($ccSchedules as $ccSchedule) {
            $this->addCcSchedule($ccSchedule);
        }

        $this->collCcSchedules = $ccSchedules;
        $this->collCcSchedulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcSchedule objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcSchedule objects.
     * @throws PropelException
     */
    public function countCcSchedules(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcSchedulesPartial && !$this->isNew();
        if (null === $this->collCcSchedules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcSchedules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcSchedules());
            }
            $query = CcScheduleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcShowInstances($this)
                ->count($con);
        }

        return count($this->collCcSchedules);
    }

    /**
     * Method called to associate a CcSchedule object to this object
     * through the CcSchedule foreign key attribute.
     *
     * @param    CcSchedule $l CcSchedule
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function addCcSchedule(CcSchedule $l)
    {
        if ($this->collCcSchedules === null) {
            $this->initCcSchedules();
            $this->collCcSchedulesPartial = true;
        }

        if (!in_array($l, $this->collCcSchedules->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcSchedule($l);

            if ($this->ccSchedulesScheduledForDeletion and $this->ccSchedulesScheduledForDeletion->contains($l)) {
                $this->ccSchedulesScheduledForDeletion->remove($this->ccSchedulesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to add.
     */
    protected function doAddCcSchedule($ccSchedule)
    {
        $this->collCcSchedules[]= $ccSchedule;
        $ccSchedule->setCcShowInstances($this);
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to remove.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function removeCcSchedule($ccSchedule)
    {
        if ($this->getCcSchedules()->contains($ccSchedule)) {
            $this->collCcSchedules->remove($this->collCcSchedules->search($ccSchedule));
            if (null === $this->ccSchedulesScheduledForDeletion) {
                $this->ccSchedulesScheduledForDeletion = clone $this->collCcSchedules;
                $this->ccSchedulesScheduledForDeletion->clear();
            }
            $this->ccSchedulesScheduledForDeletion[]= clone $ccSchedule;
            $ccSchedule->setCcShowInstances(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShowInstances is new, it will return
     * an empty collection; or if this CcShowInstances has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShowInstances.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShowInstances is new, it will return
     * an empty collection; or if this CcShowInstances has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShowInstances.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcWebstream($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcWebstream', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }

    /**
     * Clears out the collCcPlayoutHistorys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShowInstances The current object (for fluent API support)
     * @see        addCcPlayoutHistorys()
     */
    public function clearCcPlayoutHistorys()
    {
        $this->collCcPlayoutHistorys = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlayoutHistorysPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlayoutHistorys collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlayoutHistorys($v = true)
    {
        $this->collCcPlayoutHistorysPartial = $v;
    }

    /**
     * Initializes the collCcPlayoutHistorys collection.
     *
     * By default this just sets the collCcPlayoutHistorys collection to an empty array (like clearcollCcPlayoutHistorys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlayoutHistorys($overrideExisting = true)
    {
        if (null !== $this->collCcPlayoutHistorys && !$overrideExisting) {
            return;
        }
        $this->collCcPlayoutHistorys = new PropelObjectCollection();
        $this->collCcPlayoutHistorys->setModel('CcPlayoutHistory');
    }

    /**
     * Gets an array of CcPlayoutHistory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShowInstances is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlayoutHistory[] List of CcPlayoutHistory objects
     * @throws PropelException
     */
    public function getCcPlayoutHistorys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistorysPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistorys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
                // return empty collection
                $this->initCcPlayoutHistorys();
            } else {
                $collCcPlayoutHistorys = CcPlayoutHistoryQuery::create(null, $criteria)
                    ->filterByCcShowInstances($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlayoutHistorysPartial && count($collCcPlayoutHistorys)) {
                      $this->initCcPlayoutHistorys(false);

                      foreach ($collCcPlayoutHistorys as $obj) {
                        if (false == $this->collCcPlayoutHistorys->contains($obj)) {
                          $this->collCcPlayoutHistorys->append($obj);
                        }
                      }

                      $this->collCcPlayoutHistorysPartial = true;
                    }

                    $collCcPlayoutHistorys->getInternalIterator()->rewind();

                    return $collCcPlayoutHistorys;
                }

                if ($partial && $this->collCcPlayoutHistorys) {
                    foreach ($this->collCcPlayoutHistorys as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlayoutHistorys[] = $obj;
                        }
                    }
                }

                $this->collCcPlayoutHistorys = $collCcPlayoutHistorys;
                $this->collCcPlayoutHistorysPartial = false;
            }
        }

        return $this->collCcPlayoutHistorys;
    }

    /**
     * Sets a collection of CcPlayoutHistory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlayoutHistorys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function setCcPlayoutHistorys(PropelCollection $ccPlayoutHistorys, PropelPDO $con = null)
    {
        $ccPlayoutHistorysToDelete = $this->getCcPlayoutHistorys(new Criteria(), $con)->diff($ccPlayoutHistorys);


        $this->ccPlayoutHistorysScheduledForDeletion = $ccPlayoutHistorysToDelete;

        foreach ($ccPlayoutHistorysToDelete as $ccPlayoutHistoryRemoved) {
            $ccPlayoutHistoryRemoved->setCcShowInstances(null);
        }

        $this->collCcPlayoutHistorys = null;
        foreach ($ccPlayoutHistorys as $ccPlayoutHistory) {
            $this->addCcPlayoutHistory($ccPlayoutHistory);
        }

        $this->collCcPlayoutHistorys = $ccPlayoutHistorys;
        $this->collCcPlayoutHistorysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlayoutHistory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlayoutHistory objects.
     * @throws PropelException
     */
    public function countCcPlayoutHistorys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistorysPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistorys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlayoutHistorys());
            }
            $query = CcPlayoutHistoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcShowInstances($this)
                ->count($con);
        }

        return count($this->collCcPlayoutHistorys);
    }

    /**
     * Method called to associate a CcPlayoutHistory object to this object
     * through the CcPlayoutHistory foreign key attribute.
     *
     * @param    CcPlayoutHistory $l CcPlayoutHistory
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function addCcPlayoutHistory(CcPlayoutHistory $l)
    {
        if ($this->collCcPlayoutHistorys === null) {
            $this->initCcPlayoutHistorys();
            $this->collCcPlayoutHistorysPartial = true;
        }

        if (!in_array($l, $this->collCcPlayoutHistorys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlayoutHistory($l);

            if ($this->ccPlayoutHistorysScheduledForDeletion and $this->ccPlayoutHistorysScheduledForDeletion->contains($l)) {
                $this->ccPlayoutHistorysScheduledForDeletion->remove($this->ccPlayoutHistorysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlayoutHistory $ccPlayoutHistory The ccPlayoutHistory object to add.
     */
    protected function doAddCcPlayoutHistory($ccPlayoutHistory)
    {
        $this->collCcPlayoutHistorys[]= $ccPlayoutHistory;
        $ccPlayoutHistory->setCcShowInstances($this);
    }

    /**
     * @param	CcPlayoutHistory $ccPlayoutHistory The ccPlayoutHistory object to remove.
     * @return CcShowInstances The current object (for fluent API support)
     */
    public function removeCcPlayoutHistory($ccPlayoutHistory)
    {
        if ($this->getCcPlayoutHistorys()->contains($ccPlayoutHistory)) {
            $this->collCcPlayoutHistorys->remove($this->collCcPlayoutHistorys->search($ccPlayoutHistory));
            if (null === $this->ccPlayoutHistorysScheduledForDeletion) {
                $this->ccPlayoutHistorysScheduledForDeletion = clone $this->collCcPlayoutHistorys;
                $this->ccPlayoutHistorysScheduledForDeletion->clear();
            }
            $this->ccPlayoutHistorysScheduledForDeletion[]= $ccPlayoutHistory;
            $ccPlayoutHistory->setCcShowInstances(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShowInstances is new, it will return
     * an empty collection; or if this CcShowInstances has previously
     * been saved, it will retrieve related CcPlayoutHistorys from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShowInstances.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlayoutHistory[] List of CcPlayoutHistory objects
     */
    public function getCcPlayoutHistorysJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlayoutHistoryQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcPlayoutHistorys($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->description = null;
        $this->starts = null;
        $this->ends = null;
        $this->show_id = null;
        $this->record = null;
        $this->rebroadcast = null;
        $this->instance_id = null;
        $this->file_id = null;
        $this->time_filled = null;
        $this->created = null;
        $this->last_scheduled = null;
        $this->modified_instance = null;
        $this->autoplaylist_built = null;
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
            if ($this->collCcShowInstancessRelatedByDbId) {
                foreach ($this->collCcShowInstancessRelatedByDbId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcSchedules) {
                foreach ($this->collCcSchedules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcPlayoutHistorys) {
                foreach ($this->collCcPlayoutHistorys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcShow instanceof Persistent) {
              $this->aCcShow->clearAllReferences($deep);
            }
            if ($this->aCcShowInstancesRelatedByDbOriginalShow instanceof Persistent) {
              $this->aCcShowInstancesRelatedByDbOriginalShow->clearAllReferences($deep);
            }
            if ($this->aCcFiles instanceof Persistent) {
              $this->aCcFiles->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcShowInstancessRelatedByDbId instanceof PropelCollection) {
            $this->collCcShowInstancessRelatedByDbId->clearIterator();
        }
        $this->collCcShowInstancessRelatedByDbId = null;
        if ($this->collCcSchedules instanceof PropelCollection) {
            $this->collCcSchedules->clearIterator();
        }
        $this->collCcSchedules = null;
        if ($this->collCcPlayoutHistorys instanceof PropelCollection) {
            $this->collCcPlayoutHistorys->clearIterator();
        }
        $this->collCcPlayoutHistorys = null;
        $this->aCcShow = null;
        $this->aCcShowInstancesRelatedByDbOriginalShow = null;
        $this->aCcFiles = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcShowInstancesPeer::DEFAULT_STRING_FORMAT);
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
