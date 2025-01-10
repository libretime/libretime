<?php


/**
 * Base class that represents a row from the 'cc_block' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlock extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcBlockPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcBlockPeer
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
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $name;

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
     * The value for the creator_id field.
     * @var        int
     */
    protected $creator_id;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $length;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'dynamic'
     * @var        string
     */
    protected $type;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjs;

    /**
     * @var        PropelObjectCollection|CcPlaylistcontents[] Collection to store aggregation of CcPlaylistcontents objects.
     */
    protected $collCcPlaylistcontentss;
    protected $collCcPlaylistcontentssPartial;

    /**
     * @var        PropelObjectCollection|CcBlockcontents[] Collection to store aggregation of CcBlockcontents objects.
     */
    protected $collCcBlockcontentss;
    protected $collCcBlockcontentssPartial;

    /**
     * @var        PropelObjectCollection|CcBlockcriteria[] Collection to store aggregation of CcBlockcriteria objects.
     */
    protected $collCcBlockcriterias;
    protected $collCcBlockcriteriasPartial;

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
    protected $ccPlaylistcontentssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccBlockcontentssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccBlockcriteriasScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->name = '';
        $this->length = '00:00:00';
        $this->type = 'dynamic';
    }

    /**
     * Initializes internal state of BaseCcBlock object.
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
     * Get the [creator_id] column value.
     *
     * @return int
     */
    public function getDbCreatorId()
    {

        return $this->creator_id;
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
     * Get the [length] column value.
     *
     * @return string
     */
    public function getDbLength()
    {

        return $this->length;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getDbType()
    {

        return $this->type;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcBlockPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CcBlockPeer::NAME;
        }


        return $this;
    } // setDbName()

    /**
     * Sets the value of [mtime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbMtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->mtime !== null || $dt !== null) {
            $currentDateAsString = ($this->mtime !== null && $tmpDt = new DateTime($this->mtime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->mtime = $newDateAsString;
                $this->modifiedColumns[] = CcBlockPeer::MTIME;
            }
        } // if either are not null


        return $this;
    } // setDbMtime()

    /**
     * Sets the value of [utime] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbUtime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->utime !== null || $dt !== null) {
            $currentDateAsString = ($this->utime !== null && $tmpDt = new DateTime($this->utime)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->utime = $newDateAsString;
                $this->modifiedColumns[] = CcBlockPeer::UTIME;
            }
        } // if either are not null


        return $this;
    } // setDbUtime()

    /**
     * Set the value of [creator_id] column.
     *
     * @param  int $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbCreatorId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->creator_id !== $v) {
            $this->creator_id = $v;
            $this->modifiedColumns[] = CcBlockPeer::CREATOR_ID;
        }

        if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
            $this->aCcSubjs = null;
        }


        return $this;
    } // setDbCreatorId()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CcBlockPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbLength($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = CcBlockPeer::LENGTH;
        }


        return $this;
    } // setDbLength()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return CcBlock The current object (for fluent API support)
     */
    public function setDbType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CcBlockPeer::TYPE;
        }


        return $this;
    } // setDbType()

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
            if ($this->name !== '') {
                return false;
            }

            if ($this->length !== '00:00:00') {
                return false;
            }

            if ($this->type !== 'dynamic') {
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
            $this->mtime = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->utime = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->creator_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->description = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->length = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->type = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = CcBlockPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcBlock object", $e);
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

        if ($this->aCcSubjs !== null && $this->creator_id !== $this->aCcSubjs->getDbId()) {
            $this->aCcSubjs = null;
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
            $con = Propel::getConnection(CcBlockPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcBlockPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcSubjs = null;
            $this->collCcPlaylistcontentss = null;

            $this->collCcBlockcontentss = null;

            $this->collCcBlockcriterias = null;

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
            $con = Propel::getConnection(CcBlockPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcBlockQuery::create()
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
            $con = Propel::getConnection(CcBlockPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                // aggregate_column behavior
                if (null !== $this->collCcBlockcontentss) {
                    $this->setDbLength($this->computeDbLength($con));
                    if ($this->isModified()) {
                        $this->save($con);
                    }
                }

                CcBlockPeer::addInstanceToPool($this);
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

            if ($this->aCcSubjs !== null) {
                if ($this->aCcSubjs->isModified() || $this->aCcSubjs->isNew()) {
                    $affectedRows += $this->aCcSubjs->save($con);
                }
                $this->setCcSubjs($this->aCcSubjs);
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

            if ($this->ccPlaylistcontentssScheduledForDeletion !== null) {
                if (!$this->ccPlaylistcontentssScheduledForDeletion->isEmpty()) {
                    CcPlaylistcontentsQuery::create()
                        ->filterByPrimaryKeys($this->ccPlaylistcontentssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPlaylistcontentssScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlaylistcontentss !== null) {
                foreach ($this->collCcPlaylistcontentss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccBlockcontentssScheduledForDeletion !== null) {
                if (!$this->ccBlockcontentssScheduledForDeletion->isEmpty()) {
                    CcBlockcontentsQuery::create()
                        ->filterByPrimaryKeys($this->ccBlockcontentssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccBlockcontentssScheduledForDeletion = null;
                }
            }

            if ($this->collCcBlockcontentss !== null) {
                foreach ($this->collCcBlockcontentss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccBlockcriteriasScheduledForDeletion !== null) {
                if (!$this->ccBlockcriteriasScheduledForDeletion->isEmpty()) {
                    CcBlockcriteriaQuery::create()
                        ->filterByPrimaryKeys($this->ccBlockcriteriasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccBlockcriteriasScheduledForDeletion = null;
                }
            }

            if ($this->collCcBlockcriterias !== null) {
                foreach ($this->collCcBlockcriterias as $referrerFK) {
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

        $this->modifiedColumns[] = CcBlockPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcBlockPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_block_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcBlockPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcBlockPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(CcBlockPeer::MTIME)) {
            $modifiedColumns[':p' . $index++]  = '"mtime"';
        }
        if ($this->isColumnModified(CcBlockPeer::UTIME)) {
            $modifiedColumns[':p' . $index++]  = '"utime"';
        }
        if ($this->isColumnModified(CcBlockPeer::CREATOR_ID)) {
            $modifiedColumns[':p' . $index++]  = '"creator_id"';
        }
        if ($this->isColumnModified(CcBlockPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CcBlockPeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(CcBlockPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_block" (%s) VALUES (%s)',
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
                    case '"mtime"':
                        $stmt->bindValue($identifier, $this->mtime, PDO::PARAM_STR);
                        break;
                    case '"utime"':
                        $stmt->bindValue($identifier, $this->utime, PDO::PARAM_STR);
                        break;
                    case '"creator_id"':
                        $stmt->bindValue($identifier, $this->creator_id, PDO::PARAM_INT);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"length"':
                        $stmt->bindValue($identifier, $this->length, PDO::PARAM_STR);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
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

            if ($this->aCcSubjs !== null) {
                if (!$this->aCcSubjs->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
                }
            }


            if (($retval = CcBlockPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcPlaylistcontentss !== null) {
                    foreach ($this->collCcPlaylistcontentss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcBlockcontentss !== null) {
                    foreach ($this->collCcBlockcontentss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcBlockcriterias !== null) {
                    foreach ($this->collCcBlockcriterias as $referrerFK) {
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
        $pos = CcBlockPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbMtime();
                break;
            case 3:
                return $this->getDbUtime();
                break;
            case 4:
                return $this->getDbCreatorId();
                break;
            case 5:
                return $this->getDbDescription();
                break;
            case 6:
                return $this->getDbLength();
                break;
            case 7:
                return $this->getDbType();
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
        if (isset($alreadyDumpedObjects['CcBlock'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcBlock'][$this->getPrimaryKey()] = true;
        $keys = CcBlockPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbName(),
            $keys[2] => $this->getDbMtime(),
            $keys[3] => $this->getDbUtime(),
            $keys[4] => $this->getDbCreatorId(),
            $keys[5] => $this->getDbDescription(),
            $keys[6] => $this->getDbLength(),
            $keys[7] => $this->getDbType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcSubjs) {
                $result['CcSubjs'] = $this->aCcSubjs->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcPlaylistcontentss) {
                $result['CcPlaylistcontentss'] = $this->collCcPlaylistcontentss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcBlockcontentss) {
                $result['CcBlockcontentss'] = $this->collCcBlockcontentss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcBlockcriterias) {
                $result['CcBlockcriterias'] = $this->collCcBlockcriterias->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcBlockPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbMtime($value);
                break;
            case 3:
                $this->setDbUtime($value);
                break;
            case 4:
                $this->setDbCreatorId($value);
                break;
            case 5:
                $this->setDbDescription($value);
                break;
            case 6:
                $this->setDbLength($value);
                break;
            case 7:
                $this->setDbType($value);
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
        $keys = CcBlockPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbMtime($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbUtime($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbCreatorId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbDescription($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbLength($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbType($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcBlockPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcBlockPeer::ID)) $criteria->add(CcBlockPeer::ID, $this->id);
        if ($this->isColumnModified(CcBlockPeer::NAME)) $criteria->add(CcBlockPeer::NAME, $this->name);
        if ($this->isColumnModified(CcBlockPeer::MTIME)) $criteria->add(CcBlockPeer::MTIME, $this->mtime);
        if ($this->isColumnModified(CcBlockPeer::UTIME)) $criteria->add(CcBlockPeer::UTIME, $this->utime);
        if ($this->isColumnModified(CcBlockPeer::CREATOR_ID)) $criteria->add(CcBlockPeer::CREATOR_ID, $this->creator_id);
        if ($this->isColumnModified(CcBlockPeer::DESCRIPTION)) $criteria->add(CcBlockPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CcBlockPeer::LENGTH)) $criteria->add(CcBlockPeer::LENGTH, $this->length);
        if ($this->isColumnModified(CcBlockPeer::TYPE)) $criteria->add(CcBlockPeer::TYPE, $this->type);

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
        $criteria = new Criteria(CcBlockPeer::DATABASE_NAME);
        $criteria->add(CcBlockPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcBlock (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbName($this->getDbName());
        $copyObj->setDbMtime($this->getDbMtime());
        $copyObj->setDbUtime($this->getDbUtime());
        $copyObj->setDbCreatorId($this->getDbCreatorId());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbLength($this->getDbLength());
        $copyObj->setDbType($this->getDbType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcPlaylistcontentss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlaylistcontents($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcBlockcontentss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcBlockcontents($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcBlockcriterias() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcBlockcriteria($relObj->copy($deepCopy));
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
     * @return CcBlock Clone of current object.
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
     * @return CcBlockPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcBlockPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return CcBlock The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcSubjs(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setDbCreatorId(NULL);
        } else {
            $this->setDbCreatorId($v->getDbId());
        }

        $this->aCcSubjs = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addCcBlock($this);
        }


        return $this;
    }


    /**
     * Get the associated CcSubjs object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcSubjs The associated CcSubjs object.
     * @throws PropelException
     */
    public function getCcSubjs(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcSubjs === null && ($this->creator_id !== null) && $doQuery) {
            $this->aCcSubjs = CcSubjsQuery::create()->findPk($this->creator_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcSubjs->addCcBlocks($this);
             */
        }

        return $this->aCcSubjs;
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
        if ('CcPlaylistcontents' == $relationName) {
            $this->initCcPlaylistcontentss();
        }
        if ('CcBlockcontents' == $relationName) {
            $this->initCcBlockcontentss();
        }
        if ('CcBlockcriteria' == $relationName) {
            $this->initCcBlockcriterias();
        }
    }

    /**
     * Clears out the collCcPlaylistcontentss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcBlock The current object (for fluent API support)
     * @see        addCcPlaylistcontentss()
     */
    public function clearCcPlaylistcontentss()
    {
        $this->collCcPlaylistcontentss = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlaylistcontentssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlaylistcontentss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlaylistcontentss($v = true)
    {
        $this->collCcPlaylistcontentssPartial = $v;
    }

    /**
     * Initializes the collCcPlaylistcontentss collection.
     *
     * By default this just sets the collCcPlaylistcontentss collection to an empty array (like clearcollCcPlaylistcontentss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlaylistcontentss($overrideExisting = true)
    {
        if (null !== $this->collCcPlaylistcontentss && !$overrideExisting) {
            return;
        }
        $this->collCcPlaylistcontentss = new PropelObjectCollection();
        $this->collCcPlaylistcontentss->setModel('CcPlaylistcontents');
    }

    /**
     * Gets an array of CcPlaylistcontents objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcBlock is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     * @throws PropelException
     */
    public function getCcPlaylistcontentss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistcontentssPartial && !$this->isNew();
        if (null === $this->collCcPlaylistcontentss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
                // return empty collection
                $this->initCcPlaylistcontentss();
            } else {
                $collCcPlaylistcontentss = CcPlaylistcontentsQuery::create(null, $criteria)
                    ->filterByCcBlock($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlaylistcontentssPartial && count($collCcPlaylistcontentss)) {
                      $this->initCcPlaylistcontentss(false);

                      foreach ($collCcPlaylistcontentss as $obj) {
                        if (false == $this->collCcPlaylistcontentss->contains($obj)) {
                          $this->collCcPlaylistcontentss->append($obj);
                        }
                      }

                      $this->collCcPlaylistcontentssPartial = true;
                    }

                    $collCcPlaylistcontentss->getInternalIterator()->rewind();

                    return $collCcPlaylistcontentss;
                }

                if ($partial && $this->collCcPlaylistcontentss) {
                    foreach ($this->collCcPlaylistcontentss as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlaylistcontentss[] = $obj;
                        }
                    }
                }

                $this->collCcPlaylistcontentss = $collCcPlaylistcontentss;
                $this->collCcPlaylistcontentssPartial = false;
            }
        }

        return $this->collCcPlaylistcontentss;
    }

    /**
     * Sets a collection of CcPlaylistcontents objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlaylistcontentss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcBlock The current object (for fluent API support)
     */
    public function setCcPlaylistcontentss(PropelCollection $ccPlaylistcontentss, PropelPDO $con = null)
    {
        $ccPlaylistcontentssToDelete = $this->getCcPlaylistcontentss(new Criteria(), $con)->diff($ccPlaylistcontentss);


        $this->ccPlaylistcontentssScheduledForDeletion = $ccPlaylistcontentssToDelete;

        foreach ($ccPlaylistcontentssToDelete as $ccPlaylistcontentsRemoved) {
            $ccPlaylistcontentsRemoved->setCcBlock(null);
        }

        $this->collCcPlaylistcontentss = null;
        foreach ($ccPlaylistcontentss as $ccPlaylistcontents) {
            $this->addCcPlaylistcontents($ccPlaylistcontents);
        }

        $this->collCcPlaylistcontentss = $ccPlaylistcontentss;
        $this->collCcPlaylistcontentssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlaylistcontents objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlaylistcontents objects.
     * @throws PropelException
     */
    public function countCcPlaylistcontentss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistcontentssPartial && !$this->isNew();
        if (null === $this->collCcPlaylistcontentss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlaylistcontentss());
            }
            $query = CcPlaylistcontentsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcBlock($this)
                ->count($con);
        }

        return count($this->collCcPlaylistcontentss);
    }

    /**
     * Method called to associate a CcPlaylistcontents object to this object
     * through the CcPlaylistcontents foreign key attribute.
     *
     * @param    CcPlaylistcontents $l CcPlaylistcontents
     * @return CcBlock The current object (for fluent API support)
     */
    public function addCcPlaylistcontents(CcPlaylistcontents $l)
    {
        if ($this->collCcPlaylistcontentss === null) {
            $this->initCcPlaylistcontentss();
            $this->collCcPlaylistcontentssPartial = true;
        }

        if (!in_array($l, $this->collCcPlaylistcontentss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlaylistcontents($l);

            if ($this->ccPlaylistcontentssScheduledForDeletion and $this->ccPlaylistcontentssScheduledForDeletion->contains($l)) {
                $this->ccPlaylistcontentssScheduledForDeletion->remove($this->ccPlaylistcontentssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlaylistcontents $ccPlaylistcontents The ccPlaylistcontents object to add.
     */
    protected function doAddCcPlaylistcontents($ccPlaylistcontents)
    {
        $this->collCcPlaylistcontentss[]= $ccPlaylistcontents;
        $ccPlaylistcontents->setCcBlock($this);
    }

    /**
     * @param	CcPlaylistcontents $ccPlaylistcontents The ccPlaylistcontents object to remove.
     * @return CcBlock The current object (for fluent API support)
     */
    public function removeCcPlaylistcontents($ccPlaylistcontents)
    {
        if ($this->getCcPlaylistcontentss()->contains($ccPlaylistcontents)) {
            $this->collCcPlaylistcontentss->remove($this->collCcPlaylistcontentss->search($ccPlaylistcontents));
            if (null === $this->ccPlaylistcontentssScheduledForDeletion) {
                $this->ccPlaylistcontentssScheduledForDeletion = clone $this->collCcPlaylistcontentss;
                $this->ccPlaylistcontentssScheduledForDeletion->clear();
            }
            $this->ccPlaylistcontentssScheduledForDeletion[]= $ccPlaylistcontents;
            $ccPlaylistcontents->setCcBlock(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcBlock is new, it will return
     * an empty collection; or if this CcBlock has previously
     * been saved, it will retrieve related CcPlaylistcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcBlock.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     */
    public function getCcPlaylistcontentssJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlaylistcontentsQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcPlaylistcontentss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcBlock is new, it will return
     * an empty collection; or if this CcBlock has previously
     * been saved, it will retrieve related CcPlaylistcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcBlock.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcPlaylistcontents[] List of CcPlaylistcontents objects
     */
    public function getCcPlaylistcontentssJoinCcPlaylist($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcPlaylistcontentsQuery::create(null, $criteria);
        $query->joinWith('CcPlaylist', $join_behavior);

        return $this->getCcPlaylistcontentss($query, $con);
    }

    /**
     * Clears out the collCcBlockcontentss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcBlock The current object (for fluent API support)
     * @see        addCcBlockcontentss()
     */
    public function clearCcBlockcontentss()
    {
        $this->collCcBlockcontentss = null; // important to set this to null since that means it is uninitialized
        $this->collCcBlockcontentssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcBlockcontentss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcBlockcontentss($v = true)
    {
        $this->collCcBlockcontentssPartial = $v;
    }

    /**
     * Initializes the collCcBlockcontentss collection.
     *
     * By default this just sets the collCcBlockcontentss collection to an empty array (like clearcollCcBlockcontentss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcBlockcontentss($overrideExisting = true)
    {
        if (null !== $this->collCcBlockcontentss && !$overrideExisting) {
            return;
        }
        $this->collCcBlockcontentss = new PropelObjectCollection();
        $this->collCcBlockcontentss->setModel('CcBlockcontents');
    }

    /**
     * Gets an array of CcBlockcontents objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcBlock is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcBlockcontents[] List of CcBlockcontents objects
     * @throws PropelException
     */
    public function getCcBlockcontentss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcontentssPartial && !$this->isNew();
        if (null === $this->collCcBlockcontentss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcontentss) {
                // return empty collection
                $this->initCcBlockcontentss();
            } else {
                $collCcBlockcontentss = CcBlockcontentsQuery::create(null, $criteria)
                    ->filterByCcBlock($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcBlockcontentssPartial && count($collCcBlockcontentss)) {
                      $this->initCcBlockcontentss(false);

                      foreach ($collCcBlockcontentss as $obj) {
                        if (false == $this->collCcBlockcontentss->contains($obj)) {
                          $this->collCcBlockcontentss->append($obj);
                        }
                      }

                      $this->collCcBlockcontentssPartial = true;
                    }

                    $collCcBlockcontentss->getInternalIterator()->rewind();

                    return $collCcBlockcontentss;
                }

                if ($partial && $this->collCcBlockcontentss) {
                    foreach ($this->collCcBlockcontentss as $obj) {
                        if ($obj->isNew()) {
                            $collCcBlockcontentss[] = $obj;
                        }
                    }
                }

                $this->collCcBlockcontentss = $collCcBlockcontentss;
                $this->collCcBlockcontentssPartial = false;
            }
        }

        return $this->collCcBlockcontentss;
    }

    /**
     * Sets a collection of CcBlockcontents objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccBlockcontentss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcBlock The current object (for fluent API support)
     */
    public function setCcBlockcontentss(PropelCollection $ccBlockcontentss, PropelPDO $con = null)
    {
        $ccBlockcontentssToDelete = $this->getCcBlockcontentss(new Criteria(), $con)->diff($ccBlockcontentss);


        $this->ccBlockcontentssScheduledForDeletion = $ccBlockcontentssToDelete;

        foreach ($ccBlockcontentssToDelete as $ccBlockcontentsRemoved) {
            $ccBlockcontentsRemoved->setCcBlock(null);
        }

        $this->collCcBlockcontentss = null;
        foreach ($ccBlockcontentss as $ccBlockcontents) {
            $this->addCcBlockcontents($ccBlockcontents);
        }

        $this->collCcBlockcontentss = $ccBlockcontentss;
        $this->collCcBlockcontentssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcBlockcontents objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcBlockcontents objects.
     * @throws PropelException
     */
    public function countCcBlockcontentss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcontentssPartial && !$this->isNew();
        if (null === $this->collCcBlockcontentss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcontentss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcBlockcontentss());
            }
            $query = CcBlockcontentsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcBlock($this)
                ->count($con);
        }

        return count($this->collCcBlockcontentss);
    }

    /**
     * Method called to associate a CcBlockcontents object to this object
     * through the CcBlockcontents foreign key attribute.
     *
     * @param    CcBlockcontents $l CcBlockcontents
     * @return CcBlock The current object (for fluent API support)
     */
    public function addCcBlockcontents(CcBlockcontents $l)
    {
        if ($this->collCcBlockcontentss === null) {
            $this->initCcBlockcontentss();
            $this->collCcBlockcontentssPartial = true;
        }

        if (!in_array($l, $this->collCcBlockcontentss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcBlockcontents($l);

            if ($this->ccBlockcontentssScheduledForDeletion and $this->ccBlockcontentssScheduledForDeletion->contains($l)) {
                $this->ccBlockcontentssScheduledForDeletion->remove($this->ccBlockcontentssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcBlockcontents $ccBlockcontents The ccBlockcontents object to add.
     */
    protected function doAddCcBlockcontents($ccBlockcontents)
    {
        $this->collCcBlockcontentss[]= $ccBlockcontents;
        $ccBlockcontents->setCcBlock($this);
    }

    /**
     * @param	CcBlockcontents $ccBlockcontents The ccBlockcontents object to remove.
     * @return CcBlock The current object (for fluent API support)
     */
    public function removeCcBlockcontents($ccBlockcontents)
    {
        if ($this->getCcBlockcontentss()->contains($ccBlockcontents)) {
            $this->collCcBlockcontentss->remove($this->collCcBlockcontentss->search($ccBlockcontents));
            if (null === $this->ccBlockcontentssScheduledForDeletion) {
                $this->ccBlockcontentssScheduledForDeletion = clone $this->collCcBlockcontentss;
                $this->ccBlockcontentssScheduledForDeletion->clear();
            }
            $this->ccBlockcontentssScheduledForDeletion[]= $ccBlockcontents;
            $ccBlockcontents->setCcBlock(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcBlock is new, it will return
     * an empty collection; or if this CcBlock has previously
     * been saved, it will retrieve related CcBlockcontentss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcBlock.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcBlockcontents[] List of CcBlockcontents objects
     */
    public function getCcBlockcontentssJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcBlockcontentsQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcBlockcontentss($query, $con);
    }

    /**
     * Clears out the collCcBlockcriterias collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcBlock The current object (for fluent API support)
     * @see        addCcBlockcriterias()
     */
    public function clearCcBlockcriterias()
    {
        $this->collCcBlockcriterias = null; // important to set this to null since that means it is uninitialized
        $this->collCcBlockcriteriasPartial = null;

        return $this;
    }

    /**
     * reset is the collCcBlockcriterias collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcBlockcriterias($v = true)
    {
        $this->collCcBlockcriteriasPartial = $v;
    }

    /**
     * Initializes the collCcBlockcriterias collection.
     *
     * By default this just sets the collCcBlockcriterias collection to an empty array (like clearcollCcBlockcriterias());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcBlockcriterias($overrideExisting = true)
    {
        if (null !== $this->collCcBlockcriterias && !$overrideExisting) {
            return;
        }
        $this->collCcBlockcriterias = new PropelObjectCollection();
        $this->collCcBlockcriterias->setModel('CcBlockcriteria');
    }

    /**
     * Gets an array of CcBlockcriteria objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcBlock is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcBlockcriteria[] List of CcBlockcriteria objects
     * @throws PropelException
     */
    public function getCcBlockcriterias($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcriteriasPartial && !$this->isNew();
        if (null === $this->collCcBlockcriterias || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcriterias) {
                // return empty collection
                $this->initCcBlockcriterias();
            } else {
                $collCcBlockcriterias = CcBlockcriteriaQuery::create(null, $criteria)
                    ->filterByCcBlock($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcBlockcriteriasPartial && count($collCcBlockcriterias)) {
                      $this->initCcBlockcriterias(false);

                      foreach ($collCcBlockcriterias as $obj) {
                        if (false == $this->collCcBlockcriterias->contains($obj)) {
                          $this->collCcBlockcriterias->append($obj);
                        }
                      }

                      $this->collCcBlockcriteriasPartial = true;
                    }

                    $collCcBlockcriterias->getInternalIterator()->rewind();

                    return $collCcBlockcriterias;
                }

                if ($partial && $this->collCcBlockcriterias) {
                    foreach ($this->collCcBlockcriterias as $obj) {
                        if ($obj->isNew()) {
                            $collCcBlockcriterias[] = $obj;
                        }
                    }
                }

                $this->collCcBlockcriterias = $collCcBlockcriterias;
                $this->collCcBlockcriteriasPartial = false;
            }
        }

        return $this->collCcBlockcriterias;
    }

    /**
     * Sets a collection of CcBlockcriteria objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccBlockcriterias A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcBlock The current object (for fluent API support)
     */
    public function setCcBlockcriterias(PropelCollection $ccBlockcriterias, PropelPDO $con = null)
    {
        $ccBlockcriteriasToDelete = $this->getCcBlockcriterias(new Criteria(), $con)->diff($ccBlockcriterias);


        $this->ccBlockcriteriasScheduledForDeletion = $ccBlockcriteriasToDelete;

        foreach ($ccBlockcriteriasToDelete as $ccBlockcriteriaRemoved) {
            $ccBlockcriteriaRemoved->setCcBlock(null);
        }

        $this->collCcBlockcriterias = null;
        foreach ($ccBlockcriterias as $ccBlockcriteria) {
            $this->addCcBlockcriteria($ccBlockcriteria);
        }

        $this->collCcBlockcriterias = $ccBlockcriterias;
        $this->collCcBlockcriteriasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcBlockcriteria objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcBlockcriteria objects.
     * @throws PropelException
     */
    public function countCcBlockcriterias(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcBlockcriteriasPartial && !$this->isNew();
        if (null === $this->collCcBlockcriterias || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcBlockcriterias) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcBlockcriterias());
            }
            $query = CcBlockcriteriaQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcBlock($this)
                ->count($con);
        }

        return count($this->collCcBlockcriterias);
    }

    /**
     * Method called to associate a CcBlockcriteria object to this object
     * through the CcBlockcriteria foreign key attribute.
     *
     * @param    CcBlockcriteria $l CcBlockcriteria
     * @return CcBlock The current object (for fluent API support)
     */
    public function addCcBlockcriteria(CcBlockcriteria $l)
    {
        if ($this->collCcBlockcriterias === null) {
            $this->initCcBlockcriterias();
            $this->collCcBlockcriteriasPartial = true;
        }

        if (!in_array($l, $this->collCcBlockcriterias->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcBlockcriteria($l);

            if ($this->ccBlockcriteriasScheduledForDeletion and $this->ccBlockcriteriasScheduledForDeletion->contains($l)) {
                $this->ccBlockcriteriasScheduledForDeletion->remove($this->ccBlockcriteriasScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcBlockcriteria $ccBlockcriteria The ccBlockcriteria object to add.
     */
    protected function doAddCcBlockcriteria($ccBlockcriteria)
    {
        $this->collCcBlockcriterias[]= $ccBlockcriteria;
        $ccBlockcriteria->setCcBlock($this);
    }

    /**
     * @param	CcBlockcriteria $ccBlockcriteria The ccBlockcriteria object to remove.
     * @return CcBlock The current object (for fluent API support)
     */
    public function removeCcBlockcriteria($ccBlockcriteria)
    {
        if ($this->getCcBlockcriterias()->contains($ccBlockcriteria)) {
            $this->collCcBlockcriterias->remove($this->collCcBlockcriterias->search($ccBlockcriteria));
            if (null === $this->ccBlockcriteriasScheduledForDeletion) {
                $this->ccBlockcriteriasScheduledForDeletion = clone $this->collCcBlockcriterias;
                $this->ccBlockcriteriasScheduledForDeletion->clear();
            }
            $this->ccBlockcriteriasScheduledForDeletion[]= clone $ccBlockcriteria;
            $ccBlockcriteria->setCcBlock(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->mtime = null;
        $this->utime = null;
        $this->creator_id = null;
        $this->description = null;
        $this->length = null;
        $this->type = null;
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
            if ($this->collCcPlaylistcontentss) {
                foreach ($this->collCcPlaylistcontentss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcBlockcontentss) {
                foreach ($this->collCcBlockcontentss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcBlockcriterias) {
                foreach ($this->collCcBlockcriterias as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcPlaylistcontentss instanceof PropelCollection) {
            $this->collCcPlaylistcontentss->clearIterator();
        }
        $this->collCcPlaylistcontentss = null;
        if ($this->collCcBlockcontentss instanceof PropelCollection) {
            $this->collCcBlockcontentss->clearIterator();
        }
        $this->collCcBlockcontentss = null;
        if ($this->collCcBlockcriterias instanceof PropelCollection) {
            $this->collCcBlockcriterias->clearIterator();
        }
        $this->collCcBlockcriterias = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcBlockPeer::DEFAULT_STRING_FORMAT);
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

    // aggregate_column behavior

    /**
     * Computes the value of the aggregate column length *
     * @param PropelPDO $con A connection object
     *
     * @return mixed The scalar result from the aggregate query
     */
    public function computeDbLength(PropelPDO $con)
    {
        $stmt = $con->prepare('SELECT SUM(cliplength) FROM "cc_blockcontents" WHERE cc_blockcontents.block_id = :p1');
        $stmt->bindValue(':p1', $this->getDbId());
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Updates the aggregate column length *
     * @param PropelPDO $con A connection object
     */
    public function updateDbLength(PropelPDO $con)
    {
        $this->setDbLength($this->computeDbLength($con));
        $this->save($con);
    }

}
