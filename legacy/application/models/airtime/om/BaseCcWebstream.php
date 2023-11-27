<?php


/**
 * Base class that represents a row from the 'cc_webstream' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcWebstream extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcWebstreamPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcWebstreamPeer
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
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $length;

    /**
     * The value for the creator_id field.
     * @var        int
     */
    protected $creator_id;

    /**
     * The value for the mtime field.
     * @var        string
     */
    protected $mtime;

    /**
     * The value for the utime field.
     * @var        string
     */
    protected $utime;

    /**
     * The value for the lptime field.
     * @var        string
     */
    protected $lptime;

    /**
     * The value for the mime field.
     * @var        string
     */
    protected $mime;

    /**
     * @var        PropelObjectCollection|CcSchedule[] Collection to store aggregation of CcSchedule objects.
     */
    protected $collCcSchedules;
    protected $collCcSchedulesPartial;

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
    protected $ccSchedulesScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->length = '00:00:00';
    }

    /**
     * Initializes internal state of BaseCcWebstream object.
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getDbName()
    {

        return $this->name;
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
     * Get the [url] column value.
     *
     * @return string
     */
    public function getDbUrl()
    {

        return $this->url;
    }

    /**
     * Get the [length] column value.
     *
     * @return string
     */
    public function getDbLength()
    {

        return $this->length;
    }

    /**
     * Get the [creator_id] column value.
     *
     * @return int
     */
    public function getDbCreatorId()
    {

        return $this->creator_id;
    }

    /**
     * Get the [optionally formatted] temporal [mtime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbMtime($format = 'Y-m-d H:i:s')
    {
        if ($this->mtime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->mtime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->mtime, true), $x);
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
     * Get the [optionally formatted] temporal [utime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbUtime($format = 'Y-m-d H:i:s')
    {
        if ($this->utime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->utime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->utime, true), $x);
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
     * Get the [optionally formatted] temporal [lptime] column value.
     *
     *
     * @param string $format The date/time format string (date()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLPtime($format = 'Y-m-d H:i:s')
    {
        if ($this->lptime === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->lptime);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->lptime, true), $x);
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
     * Get the [mime] column value.
     *
     * @return string
     */
    public function getDbMime()
    {

        return $this->mime;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::NAME;
        }


        return $this;
    } // setDbName()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Set the value of [url] column.
     *
     * @param  string $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::URL;
        }


        return $this;
    } // setDbUrl()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbLength($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::LENGTH;
        }


        return $this;
    } // setDbLength()

    /**
     * Set the value of [creator_id] column.
     *
     * @param  int $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbCreatorId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->creator_id !== $v) {
            $this->creator_id = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::CREATOR_ID;
        }


        return $this;
    } // setDbCreatorId()

    /**
     * Sets the value of [mtime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbMtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->mtime !== null || $dt !== null) {
            $currentDateAsString = ($this->mtime !== null && $tmpDt = new DateTime($this->mtime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->mtime = $newDateAsString;
                $this->modifiedColumns[] = CcWebstreamPeer::MTIME;
            }
        } // if either are not null


        return $this;
    } // setDbMtime()

    /**
     * Sets the value of [utime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbUtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->utime !== null || $dt !== null) {
            $currentDateAsString = ($this->utime !== null && $tmpDt = new DateTime($this->utime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->utime = $newDateAsString;
                $this->modifiedColumns[] = CcWebstreamPeer::UTIME;
            }
        } // if either are not null


        return $this;
    } // setDbUtime()

    /**
     * Sets the value of [lptime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbLPtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->lptime !== null || $dt !== null) {
            $currentDateAsString = ($this->lptime !== null && $tmpDt = new DateTime($this->lptime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->lptime = $newDateAsString;
                $this->modifiedColumns[] = CcWebstreamPeer::LPTIME;
            }
        } // if either are not null


        return $this;
    } // setDbLPtime()

    /**
     * Set the value of [mime] column.
     *
     * @param  string $v new value
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setDbMime($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->mime !== $v) {
            $this->mime = $v;
            $this->modifiedColumns[] = CcWebstreamPeer::MIME;
        }


        return $this;
    } // setDbMime()

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
            if ($this->length !== '00:00:00') {
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
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->description = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->url = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->length = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->creator_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->mtime = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->utime = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->lptime = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->mime = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 10; // 10 = CcWebstreamPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcWebstream object", $e);
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
            $con = Propel::getConnection(CcWebstreamPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcWebstreamPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCcSchedules = null;

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
            $con = Propel::getConnection(CcWebstreamPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcWebstreamQuery::create()
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
            $con = Propel::getConnection(CcWebstreamPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcWebstreamPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = CcWebstreamPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcWebstreamPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_webstream_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcWebstreamPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = '"url"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::CREATOR_ID)) {
            $modifiedColumns[':p' . $index++]  = '"creator_id"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::MTIME)) {
            $modifiedColumns[':p' . $index++]  = '"mtime"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::UTIME)) {
            $modifiedColumns[':p' . $index++]  = '"utime"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::LPTIME)) {
            $modifiedColumns[':p' . $index++]  = '"lptime"';
        }
        if ($this->isColumnModified(CcWebstreamPeer::MIME)) {
            $modifiedColumns[':p' . $index++]  = '"mime"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_webstream" (%s) VALUES (%s)',
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
                    case '"name"':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"url"':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case '"length"':
                        $stmt->bindValue($identifier, $this->length, PDO::PARAM_STR);
                        break;
                    case '"creator_id"':
                        $stmt->bindValue($identifier, $this->creator_id, PDO::PARAM_INT);
                        break;
                    case '"mtime"':
                        $stmt->bindValue($identifier, $this->mtime, PDO::PARAM_STR);
                        break;
                    case '"utime"':
                        $stmt->bindValue($identifier, $this->utime, PDO::PARAM_STR);
                        break;
                    case '"lptime"':
                        $stmt->bindValue($identifier, $this->lptime, PDO::PARAM_STR);
                        break;
                    case '"mime"':
                        $stmt->bindValue($identifier, $this->mime, PDO::PARAM_STR);
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


            if (($retval = CcWebstreamPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcSchedules !== null) {
                    foreach ($this->collCcSchedules as $referrerFK) {
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
        $pos = CcWebstreamPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbName();
                break;
            case 2:
                return $this->getDbDescription();
                break;
            case 3:
                return $this->getDbUrl();
                break;
            case 4:
                return $this->getDbLength();
                break;
            case 5:
                return $this->getDbCreatorId();
                break;
            case 6:
                return $this->getDbMtime();
                break;
            case 7:
                return $this->getDbUtime();
                break;
            case 8:
                return $this->getDbLPtime();
                break;
            case 9:
                return $this->getDbMime();
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
        if (isset($alreadyDumpedObjects['CcWebstream'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcWebstream'][$this->getPrimaryKey()] = true;
        $keys = CcWebstreamPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbName(),
            $keys[2] => $this->getDbDescription(),
            $keys[3] => $this->getDbUrl(),
            $keys[4] => $this->getDbLength(),
            $keys[5] => $this->getDbCreatorId(),
            $keys[6] => $this->getDbMtime(),
            $keys[7] => $this->getDbUtime(),
            $keys[8] => $this->getDbLPtime(),
            $keys[9] => $this->getDbMime(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCcSchedules) {
                $result['CcSchedules'] = $this->collCcSchedules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcWebstreamPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbName($value);
                break;
            case 2:
                $this->setDbDescription($value);
                break;
            case 3:
                $this->setDbUrl($value);
                break;
            case 4:
                $this->setDbLength($value);
                break;
            case 5:
                $this->setDbCreatorId($value);
                break;
            case 6:
                $this->setDbMtime($value);
                break;
            case 7:
                $this->setDbUtime($value);
                break;
            case 8:
                $this->setDbLPtime($value);
                break;
            case 9:
                $this->setDbMime($value);
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
        $keys = CcWebstreamPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbDescription($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbUrl($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbLength($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbCreatorId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbMtime($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbUtime($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbLPtime($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbMime($arr[$keys[9]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcWebstreamPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcWebstreamPeer::ID)) $criteria->add(CcWebstreamPeer::ID, $this->id);
        if ($this->isColumnModified(CcWebstreamPeer::NAME)) $criteria->add(CcWebstreamPeer::NAME, $this->name);
        if ($this->isColumnModified(CcWebstreamPeer::DESCRIPTION)) $criteria->add(CcWebstreamPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CcWebstreamPeer::URL)) $criteria->add(CcWebstreamPeer::URL, $this->url);
        if ($this->isColumnModified(CcWebstreamPeer::LENGTH)) $criteria->add(CcWebstreamPeer::LENGTH, $this->length);
        if ($this->isColumnModified(CcWebstreamPeer::CREATOR_ID)) $criteria->add(CcWebstreamPeer::CREATOR_ID, $this->creator_id);
        if ($this->isColumnModified(CcWebstreamPeer::MTIME)) $criteria->add(CcWebstreamPeer::MTIME, $this->mtime);
        if ($this->isColumnModified(CcWebstreamPeer::UTIME)) $criteria->add(CcWebstreamPeer::UTIME, $this->utime);
        if ($this->isColumnModified(CcWebstreamPeer::LPTIME)) $criteria->add(CcWebstreamPeer::LPTIME, $this->lptime);
        if ($this->isColumnModified(CcWebstreamPeer::MIME)) $criteria->add(CcWebstreamPeer::MIME, $this->mime);

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
        $criteria = new Criteria(CcWebstreamPeer::DATABASE_NAME);
        $criteria->add(CcWebstreamPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcWebstream (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbName($this->getDbName());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbUrl($this->getDbUrl());
        $copyObj->setDbLength($this->getDbLength());
        $copyObj->setDbCreatorId($this->getDbCreatorId());
        $copyObj->setDbMtime($this->getDbMtime());
        $copyObj->setDbUtime($this->getDbUtime());
        $copyObj->setDbLPtime($this->getDbLPtime());
        $copyObj->setDbMime($this->getDbMime());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcSchedules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcSchedule($relObj->copy($deepCopy));
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
     * @return CcWebstream Clone of current object.
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
     * @return CcWebstreamPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcWebstreamPeer();
        }

        return self::$peer;
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
        if ('CcSchedule' == $relationName) {
            $this->initCcSchedules();
        }
    }

    /**
     * Clears out the collCcSchedules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcWebstream The current object (for fluent API support)
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
     * If this CcWebstream is new, it will return
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
                    ->filterByCcWebstream($this)
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
     * @return CcWebstream The current object (for fluent API support)
     */
    public function setCcSchedules(PropelCollection $ccSchedules, PropelPDO $con = null)
    {
        $ccSchedulesToDelete = $this->getCcSchedules(new Criteria(), $con)->diff($ccSchedules);


        $this->ccSchedulesScheduledForDeletion = $ccSchedulesToDelete;

        foreach ($ccSchedulesToDelete as $ccScheduleRemoved) {
            $ccScheduleRemoved->setCcWebstream(null);
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
                ->filterByCcWebstream($this)
                ->count($con);
        }

        return count($this->collCcSchedules);
    }

    /**
     * Method called to associate a CcSchedule object to this object
     * through the CcSchedule foreign key attribute.
     *
     * @param    CcSchedule $l CcSchedule
     * @return CcWebstream The current object (for fluent API support)
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
        $ccSchedule->setCcWebstream($this);
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to remove.
     * @return CcWebstream The current object (for fluent API support)
     */
    public function removeCcSchedule($ccSchedule)
    {
        if ($this->getCcSchedules()->contains($ccSchedule)) {
            $this->collCcSchedules->remove($this->collCcSchedules->search($ccSchedule));
            if (null === $this->ccSchedulesScheduledForDeletion) {
                $this->ccSchedulesScheduledForDeletion = clone $this->collCcSchedules;
                $this->ccSchedulesScheduledForDeletion->clear();
            }
            $this->ccSchedulesScheduledForDeletion[]= $ccSchedule;
            $ccSchedule->setCcWebstream(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcWebstream is new, it will return
     * an empty collection; or if this CcWebstream has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcWebstream.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcShowInstances($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcShowInstances', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcWebstream is new, it will return
     * an empty collection; or if this CcWebstream has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcWebstream.
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
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->description = null;
        $this->url = null;
        $this->length = null;
        $this->creator_id = null;
        $this->mtime = null;
        $this->utime = null;
        $this->lptime = null;
        $this->mime = null;
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
            if ($this->collCcSchedules) {
                foreach ($this->collCcSchedules as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcSchedules instanceof PropelCollection) {
            $this->collCcSchedules->clearIterator();
        }
        $this->collCcSchedules = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcWebstreamPeer::DEFAULT_STRING_FORMAT);
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
