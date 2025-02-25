<?php


/**
 * Base class that represents a row from the 'cc_show_days' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowDays extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcShowDaysPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcShowDaysPeer
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
     * The value for the first_show field.
     * @var        string
     */
    protected $first_show;

    /**
     * The value for the last_show field.
     * @var        string
     */
    protected $last_show;

    /**
     * The value for the start_time field.
     * @var        string
     */
    protected $start_time;

    /**
     * The value for the timezone field.
     * @var        string
     */
    protected $timezone;

    /**
     * The value for the duration field.
     * @var        string
     */
    protected $duration;

    /**
     * The value for the day field.
     * @var        int
     */
    protected $day;

    /**
     * The value for the repeat_type field.
     * @var        int
     */
    protected $repeat_type;

    /**
     * The value for the next_pop_date field.
     * @var        string
     */
    protected $next_pop_date;

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
     * @var        CcShow
     */
    protected $aCcShow;

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
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->record = 0;
    }

    /**
     * Initializes internal state of BaseCcShowDays object.
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
     * Get the [optionally formatted] temporal [first_show] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbFirstShow($format = 'Y-m-d')
    {
        if ($this->first_show === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->first_show);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->first_show, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [last_show] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLastShow($format = 'Y-m-d')
    {
        if ($this->last_show === null) {
            return null;
        }

        try {
            $dt = new DateTime($this->last_show);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_show, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [start_time] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbStartTime($format = 'H:i:s')
    {
        if ($this->start_time === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->start_time);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_time, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        return $dt->format($format);

    }

    /**
     * Get the [timezone] column value.
     *
     * @return string
     */
    public function getDbTimezone()
    {

        return $this->timezone;
    }

    /**
     * Get the [duration] column value.
     *
     * @return string
     */
    public function getDbDuration()
    {

        return $this->duration;
    }

    /**
     * Get the [day] column value.
     *
     * @return int
     */
    public function getDbDay()
    {

        return $this->day;
    }

    /**
     * Get the [repeat_type] column value.
     *
     * @return int
     */
    public function getDbRepeatType()
    {

        return $this->repeat_type;
    }

    /**
     * Get the [optionally formatted] temporal [next_pop_date] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbNextPopDate($format = 'Y-m-d')
    {
        if ($this->next_pop_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->next_pop_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->next_pop_date, true), $x);
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
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Sets the value of [first_show] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbFirstShow($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->first_show !== null || $dt !== null) {
            $currentDateAsString = ($this->first_show !== null && $tmpDt = new DateTime($this->first_show)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->first_show = $newDateAsString;
                $this->modifiedColumns[] = CcShowDaysPeer::FIRST_SHOW;
            }
        } // if either are not null


        return $this;
    } // setDbFirstShow()

    /**
     * Sets the value of [last_show] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbLastShow($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_show !== null || $dt !== null) {
            $currentDateAsString = ($this->last_show !== null && $tmpDt = new DateTime($this->last_show)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_show = $newDateAsString;
                $this->modifiedColumns[] = CcShowDaysPeer::LAST_SHOW;
            }
        } // if either are not null


        return $this;
    } // setDbLastShow()

    /**
     * Sets the value of [start_time] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbStartTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start_time !== null || $dt !== null) {
            $currentDateAsString = ($this->start_time !== null && $tmpDt = new DateTime($this->start_time)) ? $tmpDt->format('H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->start_time = $newDateAsString;
                $this->modifiedColumns[] = CcShowDaysPeer::START_TIME;
            }
        } // if either are not null


        return $this;
    } // setDbStartTime()

    /**
     * Set the value of [timezone] column.
     *
     * @param  string $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbTimezone($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->timezone !== $v) {
            $this->timezone = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::TIMEZONE;
        }


        return $this;
    } // setDbTimezone()

    /**
     * Set the value of [duration] column.
     *
     * @param  string $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbDuration($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->duration !== $v) {
            $this->duration = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::DURATION;
        }


        return $this;
    } // setDbDuration()

    /**
     * Set the value of [day] column.
     *
     * @param  int $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbDay($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->day !== $v) {
            $this->day = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::DAY;
        }


        return $this;
    } // setDbDay()

    /**
     * Set the value of [repeat_type] column.
     *
     * @param  int $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbRepeatType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->repeat_type !== $v) {
            $this->repeat_type = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::REPEAT_TYPE;
        }


        return $this;
    } // setDbRepeatType()

    /**
     * Sets the value of [next_pop_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbNextPopDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->next_pop_date !== null || $dt !== null) {
            $currentDateAsString = ($this->next_pop_date !== null && $tmpDt = new DateTime($this->next_pop_date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->next_pop_date = $newDateAsString;
                $this->modifiedColumns[] = CcShowDaysPeer::NEXT_POP_DATE;
            }
        } // if either are not null


        return $this;
    } // setDbNextPopDate()

    /**
     * Set the value of [show_id] column.
     *
     * @param  int $v new value
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbShowId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->show_id !== $v) {
            $this->show_id = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::SHOW_ID;
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
     * @return CcShowDays The current object (for fluent API support)
     */
    public function setDbRecord($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->record !== $v) {
            $this->record = $v;
            $this->modifiedColumns[] = CcShowDaysPeer::RECORD;
        }


        return $this;
    } // setDbRecord()

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
            if ($this->record !== 0) {
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
            $this->first_show = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->last_show = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->start_time = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->timezone = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->duration = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->day = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->repeat_type = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->next_pop_date = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->show_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->record = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = CcShowDaysPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcShowDays object", $e);
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
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcShowDaysPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcShow = null;
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
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcShowDaysQuery::create()
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
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcShowDaysPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = CcShowDaysPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcShowDaysPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_show_days_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcShowDaysPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::FIRST_SHOW)) {
            $modifiedColumns[':p' . $index++]  = '"first_show"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::LAST_SHOW)) {
            $modifiedColumns[':p' . $index++]  = '"last_show"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::START_TIME)) {
            $modifiedColumns[':p' . $index++]  = '"start_time"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::TIMEZONE)) {
            $modifiedColumns[':p' . $index++]  = '"timezone"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::DURATION)) {
            $modifiedColumns[':p' . $index++]  = '"duration"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::DAY)) {
            $modifiedColumns[':p' . $index++]  = '"day"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::REPEAT_TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"repeat_type"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::NEXT_POP_DATE)) {
            $modifiedColumns[':p' . $index++]  = '"next_pop_date"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::SHOW_ID)) {
            $modifiedColumns[':p' . $index++]  = '"show_id"';
        }
        if ($this->isColumnModified(CcShowDaysPeer::RECORD)) {
            $modifiedColumns[':p' . $index++]  = '"record"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_show_days" (%s) VALUES (%s)',
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
                    case '"first_show"':
                        $stmt->bindValue($identifier, $this->first_show, PDO::PARAM_STR);
                        break;
                    case '"last_show"':
                        $stmt->bindValue($identifier, $this->last_show, PDO::PARAM_STR);
                        break;
                    case '"start_time"':
                        $stmt->bindValue($identifier, $this->start_time, PDO::PARAM_STR);
                        break;
                    case '"timezone"':
                        $stmt->bindValue($identifier, $this->timezone, PDO::PARAM_STR);
                        break;
                    case '"duration"':
                        $stmt->bindValue($identifier, $this->duration, PDO::PARAM_STR);
                        break;
                    case '"day"':
                        $stmt->bindValue($identifier, $this->day, PDO::PARAM_INT);
                        break;
                    case '"repeat_type"':
                        $stmt->bindValue($identifier, $this->repeat_type, PDO::PARAM_INT);
                        break;
                    case '"next_pop_date"':
                        $stmt->bindValue($identifier, $this->next_pop_date, PDO::PARAM_STR);
                        break;
                    case '"show_id"':
                        $stmt->bindValue($identifier, $this->show_id, PDO::PARAM_INT);
                        break;
                    case '"record"':
                        $stmt->bindValue($identifier, $this->record, PDO::PARAM_INT);
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


            if (($retval = CcShowDaysPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
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
        $pos = CcShowDaysPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbFirstShow();
                break;
            case 2:
                return $this->getDbLastShow();
                break;
            case 3:
                return $this->getDbStartTime();
                break;
            case 4:
                return $this->getDbTimezone();
                break;
            case 5:
                return $this->getDbDuration();
                break;
            case 6:
                return $this->getDbDay();
                break;
            case 7:
                return $this->getDbRepeatType();
                break;
            case 8:
                return $this->getDbNextPopDate();
                break;
            case 9:
                return $this->getDbShowId();
                break;
            case 10:
                return $this->getDbRecord();
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
        if (isset($alreadyDumpedObjects['CcShowDays'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcShowDays'][$this->getPrimaryKey()] = true;
        $keys = CcShowDaysPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbFirstShow(),
            $keys[2] => $this->getDbLastShow(),
            $keys[3] => $this->getDbStartTime(),
            $keys[4] => $this->getDbTimezone(),
            $keys[5] => $this->getDbDuration(),
            $keys[6] => $this->getDbDay(),
            $keys[7] => $this->getDbRepeatType(),
            $keys[8] => $this->getDbNextPopDate(),
            $keys[9] => $this->getDbShowId(),
            $keys[10] => $this->getDbRecord(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcShow) {
                $result['CcShow'] = $this->aCcShow->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = CcShowDaysPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbFirstShow($value);
                break;
            case 2:
                $this->setDbLastShow($value);
                break;
            case 3:
                $this->setDbStartTime($value);
                break;
            case 4:
                $this->setDbTimezone($value);
                break;
            case 5:
                $this->setDbDuration($value);
                break;
            case 6:
                $this->setDbDay($value);
                break;
            case 7:
                $this->setDbRepeatType($value);
                break;
            case 8:
                $this->setDbNextPopDate($value);
                break;
            case 9:
                $this->setDbShowId($value);
                break;
            case 10:
                $this->setDbRecord($value);
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
        $keys = CcShowDaysPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbFirstShow($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbLastShow($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbStartTime($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbTimezone($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbDuration($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbDay($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbRepeatType($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbNextPopDate($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbShowId($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbRecord($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcShowDaysPeer::ID)) $criteria->add(CcShowDaysPeer::ID, $this->id);
        if ($this->isColumnModified(CcShowDaysPeer::FIRST_SHOW)) $criteria->add(CcShowDaysPeer::FIRST_SHOW, $this->first_show);
        if ($this->isColumnModified(CcShowDaysPeer::LAST_SHOW)) $criteria->add(CcShowDaysPeer::LAST_SHOW, $this->last_show);
        if ($this->isColumnModified(CcShowDaysPeer::START_TIME)) $criteria->add(CcShowDaysPeer::START_TIME, $this->start_time);
        if ($this->isColumnModified(CcShowDaysPeer::TIMEZONE)) $criteria->add(CcShowDaysPeer::TIMEZONE, $this->timezone);
        if ($this->isColumnModified(CcShowDaysPeer::DURATION)) $criteria->add(CcShowDaysPeer::DURATION, $this->duration);
        if ($this->isColumnModified(CcShowDaysPeer::DAY)) $criteria->add(CcShowDaysPeer::DAY, $this->day);
        if ($this->isColumnModified(CcShowDaysPeer::REPEAT_TYPE)) $criteria->add(CcShowDaysPeer::REPEAT_TYPE, $this->repeat_type);
        if ($this->isColumnModified(CcShowDaysPeer::NEXT_POP_DATE)) $criteria->add(CcShowDaysPeer::NEXT_POP_DATE, $this->next_pop_date);
        if ($this->isColumnModified(CcShowDaysPeer::SHOW_ID)) $criteria->add(CcShowDaysPeer::SHOW_ID, $this->show_id);
        if ($this->isColumnModified(CcShowDaysPeer::RECORD)) $criteria->add(CcShowDaysPeer::RECORD, $this->record);

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
        $criteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);
        $criteria->add(CcShowDaysPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcShowDays (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbFirstShow($this->getDbFirstShow());
        $copyObj->setDbLastShow($this->getDbLastShow());
        $copyObj->setDbStartTime($this->getDbStartTime());
        $copyObj->setDbTimezone($this->getDbTimezone());
        $copyObj->setDbDuration($this->getDbDuration());
        $copyObj->setDbDay($this->getDbDay());
        $copyObj->setDbRepeatType($this->getDbRepeatType());
        $copyObj->setDbNextPopDate($this->getDbNextPopDate());
        $copyObj->setDbShowId($this->getDbShowId());
        $copyObj->setDbRecord($this->getDbRecord());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

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
     * @return CcShowDays Clone of current object.
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
     * @return CcShowDaysPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcShowDaysPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcShow object.
     *
     * @param                  CcShow $v
     * @return CcShowDays The current object (for fluent API support)
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
            $v->addCcShowDays($this);
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
                $this->aCcShow->addCcShowDayss($this);
             */
        }

        return $this->aCcShow;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->first_show = null;
        $this->last_show = null;
        $this->start_time = null;
        $this->timezone = null;
        $this->duration = null;
        $this->day = null;
        $this->repeat_type = null;
        $this->next_pop_date = null;
        $this->show_id = null;
        $this->record = null;
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
            if ($this->aCcShow instanceof Persistent) {
              $this->aCcShow->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aCcShow = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcShowDaysPeer::DEFAULT_STRING_FORMAT);
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
