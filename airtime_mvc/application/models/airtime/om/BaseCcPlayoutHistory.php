<?php


/**
 * Base class that represents a row from the 'cc_playout_history' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistory extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcPlayoutHistoryPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcPlayoutHistoryPeer
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
     * The value for the file_id field.
     * @var        int
     */
    protected $file_id;

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
     * The value for the instance_id field.
     * @var        int
     */
    protected $instance_id;

    /**
     * @var        CcFiles
     */
    protected $aCcFiles;

    /**
     * @var        CcShowInstances
     */
    protected $aCcShowInstances;

    /**
     * @var        PropelObjectCollection|CcPlayoutHistoryMetaData[] Collection to store aggregation of CcPlayoutHistoryMetaData objects.
     */
    protected $collCcPlayoutHistoryMetaDatas;
    protected $collCcPlayoutHistoryMetaDatasPartial;

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
    protected $ccPlayoutHistoryMetaDatasScheduledForDeletion = null;

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
     * Get the [file_id] column value.
     *
     * @return int
     */
    public function getDbFileId()
    {

        return $this->file_id;
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
     * Get the [instance_id] column value.
     *
     * @return int
     */
    public function getDbInstanceId()
    {

        return $this->instance_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcPlayoutHistoryPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [file_id] column.
     *
     * @param  int $v new value
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setDbFileId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->file_id !== $v) {
            $this->file_id = $v;
            $this->modifiedColumns[] = CcPlayoutHistoryPeer::FILE_ID;
        }

        if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
            $this->aCcFiles = null;
        }


        return $this;
    } // setDbFileId()

    /**
     * Sets the value of [starts] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setDbStarts($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->starts !== null || $dt !== null) {
            $currentDateAsString = ($this->starts !== null && $tmpDt = new DateTime($this->starts)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->starts = $newDateAsString;
                $this->modifiedColumns[] = CcPlayoutHistoryPeer::STARTS;
            }
        } // if either are not null


        return $this;
    } // setDbStarts()

    /**
     * Sets the value of [ends] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setDbEnds($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->ends !== null || $dt !== null) {
            $currentDateAsString = ($this->ends !== null && $tmpDt = new DateTime($this->ends)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->ends = $newDateAsString;
                $this->modifiedColumns[] = CcPlayoutHistoryPeer::ENDS;
            }
        } // if either are not null


        return $this;
    } // setDbEnds()

    /**
     * Set the value of [instance_id] column.
     *
     * @param  int $v new value
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setDbInstanceId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->instance_id !== $v) {
            $this->instance_id = $v;
            $this->modifiedColumns[] = CcPlayoutHistoryPeer::INSTANCE_ID;
        }

        if ($this->aCcShowInstances !== null && $this->aCcShowInstances->getDbId() !== $v) {
            $this->aCcShowInstances = null;
        }


        return $this;
    } // setDbInstanceId()

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
            $this->file_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->starts = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->ends = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->instance_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = CcPlayoutHistoryPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcPlayoutHistory object", $e);
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
            $con = Propel::getConnection(CcPlayoutHistoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcPlayoutHistoryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcFiles = null;
            $this->aCcShowInstances = null;
            $this->collCcPlayoutHistoryMetaDatas = null;

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
            $con = Propel::getConnection(CcPlayoutHistoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcPlayoutHistoryQuery::create()
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
            $con = Propel::getConnection(CcPlayoutHistoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcPlayoutHistoryPeer::addInstanceToPool($this);
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

            if ($this->aCcFiles !== null) {
                if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
                    $affectedRows += $this->aCcFiles->save($con);
                }
                $this->setCcFiles($this->aCcFiles);
            }

            if ($this->aCcShowInstances !== null) {
                if ($this->aCcShowInstances->isModified() || $this->aCcShowInstances->isNew()) {
                    $affectedRows += $this->aCcShowInstances->save($con);
                }
                $this->setCcShowInstances($this->aCcShowInstances);
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

            if ($this->ccPlayoutHistoryMetaDatasScheduledForDeletion !== null) {
                if (!$this->ccPlayoutHistoryMetaDatasScheduledForDeletion->isEmpty()) {
                    CcPlayoutHistoryMetaDataQuery::create()
                        ->filterByPrimaryKeys($this->ccPlayoutHistoryMetaDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPlayoutHistoryMetaDatasScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlayoutHistoryMetaDatas !== null) {
                foreach ($this->collCcPlayoutHistoryMetaDatas as $referrerFK) {
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

        $this->modifiedColumns[] = CcPlayoutHistoryPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcPlayoutHistoryPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_playout_history_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcPlayoutHistoryPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcPlayoutHistoryPeer::FILE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"file_id"';
        }
        if ($this->isColumnModified(CcPlayoutHistoryPeer::STARTS)) {
            $modifiedColumns[':p' . $index++]  = '"starts"';
        }
        if ($this->isColumnModified(CcPlayoutHistoryPeer::ENDS)) {
            $modifiedColumns[':p' . $index++]  = '"ends"';
        }
        if ($this->isColumnModified(CcPlayoutHistoryPeer::INSTANCE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"instance_id"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_playout_history" (%s) VALUES (%s)',
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
                    case '"file_id"':
                        $stmt->bindValue($identifier, $this->file_id, PDO::PARAM_INT);
                        break;
                    case '"starts"':
                        $stmt->bindValue($identifier, $this->starts, PDO::PARAM_STR);
                        break;
                    case '"ends"':
                        $stmt->bindValue($identifier, $this->ends, PDO::PARAM_STR);
                        break;
                    case '"instance_id"':
                        $stmt->bindValue($identifier, $this->instance_id, PDO::PARAM_INT);
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

            if ($this->aCcFiles !== null) {
                if (!$this->aCcFiles->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
                }
            }

            if ($this->aCcShowInstances !== null) {
                if (!$this->aCcShowInstances->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcShowInstances->getValidationFailures());
                }
            }


            if (($retval = CcPlayoutHistoryPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcPlayoutHistoryMetaDatas !== null) {
                    foreach ($this->collCcPlayoutHistoryMetaDatas as $referrerFK) {
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
        $pos = CcPlayoutHistoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbFileId();
                break;
            case 2:
                return $this->getDbStarts();
                break;
            case 3:
                return $this->getDbEnds();
                break;
            case 4:
                return $this->getDbInstanceId();
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
        if (isset($alreadyDumpedObjects['CcPlayoutHistory'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcPlayoutHistory'][$this->getPrimaryKey()] = true;
        $keys = CcPlayoutHistoryPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbFileId(),
            $keys[2] => $this->getDbStarts(),
            $keys[3] => $this->getDbEnds(),
            $keys[4] => $this->getDbInstanceId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcFiles) {
                $result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcShowInstances) {
                $result['CcShowInstances'] = $this->aCcShowInstances->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcPlayoutHistoryMetaDatas) {
                $result['CcPlayoutHistoryMetaDatas'] = $this->collCcPlayoutHistoryMetaDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcPlayoutHistoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbFileId($value);
                break;
            case 2:
                $this->setDbStarts($value);
                break;
            case 3:
                $this->setDbEnds($value);
                break;
            case 4:
                $this->setDbInstanceId($value);
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
        $keys = CcPlayoutHistoryPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbFileId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbStarts($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbEnds($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbInstanceId($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcPlayoutHistoryPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcPlayoutHistoryPeer::ID)) $criteria->add(CcPlayoutHistoryPeer::ID, $this->id);
        if ($this->isColumnModified(CcPlayoutHistoryPeer::FILE_ID)) $criteria->add(CcPlayoutHistoryPeer::FILE_ID, $this->file_id);
        if ($this->isColumnModified(CcPlayoutHistoryPeer::STARTS)) $criteria->add(CcPlayoutHistoryPeer::STARTS, $this->starts);
        if ($this->isColumnModified(CcPlayoutHistoryPeer::ENDS)) $criteria->add(CcPlayoutHistoryPeer::ENDS, $this->ends);
        if ($this->isColumnModified(CcPlayoutHistoryPeer::INSTANCE_ID)) $criteria->add(CcPlayoutHistoryPeer::INSTANCE_ID, $this->instance_id);

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
        $criteria = new Criteria(CcPlayoutHistoryPeer::DATABASE_NAME);
        $criteria->add(CcPlayoutHistoryPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcPlayoutHistory (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbFileId($this->getDbFileId());
        $copyObj->setDbStarts($this->getDbStarts());
        $copyObj->setDbEnds($this->getDbEnds());
        $copyObj->setDbInstanceId($this->getDbInstanceId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcPlayoutHistoryMetaDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlayoutHistoryMetaData($relObj->copy($deepCopy));
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
     * @return CcPlayoutHistory Clone of current object.
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
     * @return CcPlayoutHistoryPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcPlayoutHistoryPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcFiles object.
     *
     * @param                  CcFiles $v
     * @return CcPlayoutHistory The current object (for fluent API support)
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
            $v->addCcPlayoutHistory($this);
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
                $this->aCcFiles->addCcPlayoutHistorys($this);
             */
        }

        return $this->aCcFiles;
    }

    /**
     * Declares an association between this object and a CcShowInstances object.
     *
     * @param                  CcShowInstances $v
     * @return CcPlayoutHistory The current object (for fluent API support)
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
            $v->addCcPlayoutHistory($this);
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
                $this->aCcShowInstances->addCcPlayoutHistorys($this);
             */
        }

        return $this->aCcShowInstances;
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
        if ('CcPlayoutHistoryMetaData' == $relationName) {
            $this->initCcPlayoutHistoryMetaDatas();
        }
    }

    /**
     * Clears out the collCcPlayoutHistoryMetaDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcPlayoutHistory The current object (for fluent API support)
     * @see        addCcPlayoutHistoryMetaDatas()
     */
    public function clearCcPlayoutHistoryMetaDatas()
    {
        $this->collCcPlayoutHistoryMetaDatas = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlayoutHistoryMetaDatasPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlayoutHistoryMetaDatas collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlayoutHistoryMetaDatas($v = true)
    {
        $this->collCcPlayoutHistoryMetaDatasPartial = $v;
    }

    /**
     * Initializes the collCcPlayoutHistoryMetaDatas collection.
     *
     * By default this just sets the collCcPlayoutHistoryMetaDatas collection to an empty array (like clearcollCcPlayoutHistoryMetaDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlayoutHistoryMetaDatas($overrideExisting = true)
    {
        if (null !== $this->collCcPlayoutHistoryMetaDatas && !$overrideExisting) {
            return;
        }
        $this->collCcPlayoutHistoryMetaDatas = new PropelObjectCollection();
        $this->collCcPlayoutHistoryMetaDatas->setModel('CcPlayoutHistoryMetaData');
    }

    /**
     * Gets an array of CcPlayoutHistoryMetaData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcPlayoutHistory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlayoutHistoryMetaData[] List of CcPlayoutHistoryMetaData objects
     * @throws PropelException
     */
    public function getCcPlayoutHistoryMetaDatas($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistoryMetaDatasPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistoryMetaDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistoryMetaDatas) {
                // return empty collection
                $this->initCcPlayoutHistoryMetaDatas();
            } else {
                $collCcPlayoutHistoryMetaDatas = CcPlayoutHistoryMetaDataQuery::create(null, $criteria)
                    ->filterByCcPlayoutHistory($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlayoutHistoryMetaDatasPartial && count($collCcPlayoutHistoryMetaDatas)) {
                      $this->initCcPlayoutHistoryMetaDatas(false);

                      foreach ($collCcPlayoutHistoryMetaDatas as $obj) {
                        if (false == $this->collCcPlayoutHistoryMetaDatas->contains($obj)) {
                          $this->collCcPlayoutHistoryMetaDatas->append($obj);
                        }
                      }

                      $this->collCcPlayoutHistoryMetaDatasPartial = true;
                    }

                    $collCcPlayoutHistoryMetaDatas->getInternalIterator()->rewind();

                    return $collCcPlayoutHistoryMetaDatas;
                }

                if ($partial && $this->collCcPlayoutHistoryMetaDatas) {
                    foreach ($this->collCcPlayoutHistoryMetaDatas as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlayoutHistoryMetaDatas[] = $obj;
                        }
                    }
                }

                $this->collCcPlayoutHistoryMetaDatas = $collCcPlayoutHistoryMetaDatas;
                $this->collCcPlayoutHistoryMetaDatasPartial = false;
            }
        }

        return $this->collCcPlayoutHistoryMetaDatas;
    }

    /**
     * Sets a collection of CcPlayoutHistoryMetaData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlayoutHistoryMetaDatas A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function setCcPlayoutHistoryMetaDatas(PropelCollection $ccPlayoutHistoryMetaDatas, PropelPDO $con = null)
    {
        $ccPlayoutHistoryMetaDatasToDelete = $this->getCcPlayoutHistoryMetaDatas(new Criteria(), $con)->diff($ccPlayoutHistoryMetaDatas);


        $this->ccPlayoutHistoryMetaDatasScheduledForDeletion = $ccPlayoutHistoryMetaDatasToDelete;

        foreach ($ccPlayoutHistoryMetaDatasToDelete as $ccPlayoutHistoryMetaDataRemoved) {
            $ccPlayoutHistoryMetaDataRemoved->setCcPlayoutHistory(null);
        }

        $this->collCcPlayoutHistoryMetaDatas = null;
        foreach ($ccPlayoutHistoryMetaDatas as $ccPlayoutHistoryMetaData) {
            $this->addCcPlayoutHistoryMetaData($ccPlayoutHistoryMetaData);
        }

        $this->collCcPlayoutHistoryMetaDatas = $ccPlayoutHistoryMetaDatas;
        $this->collCcPlayoutHistoryMetaDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlayoutHistoryMetaData objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlayoutHistoryMetaData objects.
     * @throws PropelException
     */
    public function countCcPlayoutHistoryMetaDatas(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlayoutHistoryMetaDatasPartial && !$this->isNew();
        if (null === $this->collCcPlayoutHistoryMetaDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlayoutHistoryMetaDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlayoutHistoryMetaDatas());
            }
            $query = CcPlayoutHistoryMetaDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcPlayoutHistory($this)
                ->count($con);
        }

        return count($this->collCcPlayoutHistoryMetaDatas);
    }

    /**
     * Method called to associate a CcPlayoutHistoryMetaData object to this object
     * through the CcPlayoutHistoryMetaData foreign key attribute.
     *
     * @param    CcPlayoutHistoryMetaData $l CcPlayoutHistoryMetaData
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function addCcPlayoutHistoryMetaData(CcPlayoutHistoryMetaData $l)
    {
        if ($this->collCcPlayoutHistoryMetaDatas === null) {
            $this->initCcPlayoutHistoryMetaDatas();
            $this->collCcPlayoutHistoryMetaDatasPartial = true;
        }

        if (!in_array($l, $this->collCcPlayoutHistoryMetaDatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlayoutHistoryMetaData($l);

            if ($this->ccPlayoutHistoryMetaDatasScheduledForDeletion and $this->ccPlayoutHistoryMetaDatasScheduledForDeletion->contains($l)) {
                $this->ccPlayoutHistoryMetaDatasScheduledForDeletion->remove($this->ccPlayoutHistoryMetaDatasScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData The ccPlayoutHistoryMetaData object to add.
     */
    protected function doAddCcPlayoutHistoryMetaData($ccPlayoutHistoryMetaData)
    {
        $this->collCcPlayoutHistoryMetaDatas[]= $ccPlayoutHistoryMetaData;
        $ccPlayoutHistoryMetaData->setCcPlayoutHistory($this);
    }

    /**
     * @param	CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData The ccPlayoutHistoryMetaData object to remove.
     * @return CcPlayoutHistory The current object (for fluent API support)
     */
    public function removeCcPlayoutHistoryMetaData($ccPlayoutHistoryMetaData)
    {
        if ($this->getCcPlayoutHistoryMetaDatas()->contains($ccPlayoutHistoryMetaData)) {
            $this->collCcPlayoutHistoryMetaDatas->remove($this->collCcPlayoutHistoryMetaDatas->search($ccPlayoutHistoryMetaData));
            if (null === $this->ccPlayoutHistoryMetaDatasScheduledForDeletion) {
                $this->ccPlayoutHistoryMetaDatasScheduledForDeletion = clone $this->collCcPlayoutHistoryMetaDatas;
                $this->ccPlayoutHistoryMetaDatasScheduledForDeletion->clear();
            }
            $this->ccPlayoutHistoryMetaDatasScheduledForDeletion[]= clone $ccPlayoutHistoryMetaData;
            $ccPlayoutHistoryMetaData->setCcPlayoutHistory(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->file_id = null;
        $this->starts = null;
        $this->ends = null;
        $this->instance_id = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
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
            if ($this->collCcPlayoutHistoryMetaDatas) {
                foreach ($this->collCcPlayoutHistoryMetaDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcFiles instanceof Persistent) {
              $this->aCcFiles->clearAllReferences($deep);
            }
            if ($this->aCcShowInstances instanceof Persistent) {
              $this->aCcShowInstances->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcPlayoutHistoryMetaDatas instanceof PropelCollection) {
            $this->collCcPlayoutHistoryMetaDatas->clearIterator();
        }
        $this->collCcPlayoutHistoryMetaDatas = null;
        $this->aCcFiles = null;
        $this->aCcShowInstances = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcPlayoutHistoryPeer::DEFAULT_STRING_FORMAT);
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
