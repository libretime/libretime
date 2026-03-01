<?php


/**
 * Base class that represents a row from the 'third_party_track_references' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseThirdPartyTrackReferences extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'ThirdPartyTrackReferencesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ThirdPartyTrackReferencesPeer
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
     * The value for the service field.
     * @var        string
     */
    protected $service;

    /**
     * The value for the foreign_id field.
     * @var        string
     */
    protected $foreign_id;

    /**
     * The value for the file_id field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $file_id;

    /**
     * The value for the upload_time field.
     * @var        string
     */
    protected $upload_time;

    /**
     * The value for the status field.
     * @var        string
     */
    protected $status;

    /**
     * @var        CcFiles
     */
    protected $aCcFiles;

    /**
     * @var        PropelObjectCollection|CeleryTasks[] Collection to store aggregation of CeleryTasks objects.
     */
    protected $collCeleryTaskss;
    protected $collCeleryTaskssPartial;

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
    protected $celeryTaskssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->file_id = 0;
    }

    /**
     * Initializes internal state of BaseThirdPartyTrackReferences object.
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
     * Get the [service] column value.
     *
     * @return string
     */
    public function getDbService()
    {

        return $this->service;
    }

    /**
     * Get the [foreign_id] column value.
     *
     * @return string
     */
    public function getDbForeignId()
    {

        return $this->foreign_id;
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
     * Get the [optionally formatted] temporal [upload_time] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbUploadTime($format = 'Y-m-d H:i:s')
    {
        if ($this->upload_time === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->upload_time);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->upload_time, true), $x);
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
     * Get the [status] column value.
     *
     * @return string
     */
    public function getDbStatus()
    {

        return $this->status;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [service] column.
     *
     * @param  string $v new value
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbService($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->service !== $v) {
            $this->service = $v;
            $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::SERVICE;
        }


        return $this;
    } // setDbService()

    /**
     * Set the value of [foreign_id] column.
     *
     * @param  string $v new value
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbForeignId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->foreign_id !== $v) {
            $this->foreign_id = $v;
            $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::FOREIGN_ID;
        }


        return $this;
    } // setDbForeignId()

    /**
     * Set the value of [file_id] column.
     *
     * @param  int $v new value
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbFileId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->file_id !== $v) {
            $this->file_id = $v;
            $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::FILE_ID;
        }

        if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
            $this->aCcFiles = null;
        }


        return $this;
    } // setDbFileId()

    /**
     * Sets the value of [upload_time] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbUploadTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->upload_time !== null || $dt !== null) {
            $currentDateAsString = ($this->upload_time !== null && $tmpDt = new DateTime($this->upload_time)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->upload_time = $newDateAsString;
                $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::UPLOAD_TIME;
            }
        } // if either are not null


        return $this;
    } // setDbUploadTime()

    /**
     * Set the value of [status] column.
     *
     * @param  string $v new value
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setDbStatus($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->status !== $v) {
            $this->status = $v;
            $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::STATUS;
        }


        return $this;
    } // setDbStatus()

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
            if ($this->file_id !== 0) {
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
            $this->service = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->foreign_id = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->file_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->upload_time = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->status = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = ThirdPartyTrackReferencesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating ThirdPartyTrackReferences object", $e);
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
            $con = Propel::getConnection(ThirdPartyTrackReferencesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ThirdPartyTrackReferencesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcFiles = null;
            $this->collCeleryTaskss = null;

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
            $con = Propel::getConnection(ThirdPartyTrackReferencesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ThirdPartyTrackReferencesQuery::create()
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
            $con = Propel::getConnection(ThirdPartyTrackReferencesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                ThirdPartyTrackReferencesPeer::addInstanceToPool($this);
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

            if ($this->celeryTaskssScheduledForDeletion !== null) {
                if (!$this->celeryTaskssScheduledForDeletion->isEmpty()) {
                    CeleryTasksQuery::create()
                        ->filterByPrimaryKeys($this->celeryTaskssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->celeryTaskssScheduledForDeletion = null;
                }
            }

            if ($this->collCeleryTaskss !== null) {
                foreach ($this->collCeleryTaskss as $referrerFK) {
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

        $this->modifiedColumns[] = ThirdPartyTrackReferencesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ThirdPartyTrackReferencesPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('third_party_track_references_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::SERVICE)) {
            $modifiedColumns[':p' . $index++]  = '"service"';
        }
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::FOREIGN_ID)) {
            $modifiedColumns[':p' . $index++]  = '"foreign_id"';
        }
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::FILE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"file_id"';
        }
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::UPLOAD_TIME)) {
            $modifiedColumns[':p' . $index++]  = '"upload_time"';
        }
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::STATUS)) {
            $modifiedColumns[':p' . $index++]  = '"status"';
        }

        $sql = sprintf(
            'INSERT INTO "third_party_track_references" (%s) VALUES (%s)',
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
                    case '"service"':
                        $stmt->bindValue($identifier, $this->service, PDO::PARAM_STR);
                        break;
                    case '"foreign_id"':
                        $stmt->bindValue($identifier, $this->foreign_id, PDO::PARAM_STR);
                        break;
                    case '"file_id"':
                        $stmt->bindValue($identifier, $this->file_id, PDO::PARAM_INT);
                        break;
                    case '"upload_time"':
                        $stmt->bindValue($identifier, $this->upload_time, PDO::PARAM_STR);
                        break;
                    case '"status"':
                        $stmt->bindValue($identifier, $this->status, PDO::PARAM_STR);
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


            if (($retval = ThirdPartyTrackReferencesPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCeleryTaskss !== null) {
                    foreach ($this->collCeleryTaskss as $referrerFK) {
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
        $pos = ThirdPartyTrackReferencesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbService();
                break;
            case 2:
                return $this->getDbForeignId();
                break;
            case 3:
                return $this->getDbFileId();
                break;
            case 4:
                return $this->getDbUploadTime();
                break;
            case 5:
                return $this->getDbStatus();
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
        if (isset($alreadyDumpedObjects['ThirdPartyTrackReferences'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ThirdPartyTrackReferences'][$this->getPrimaryKey()] = true;
        $keys = ThirdPartyTrackReferencesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbService(),
            $keys[2] => $this->getDbForeignId(),
            $keys[3] => $this->getDbFileId(),
            $keys[4] => $this->getDbUploadTime(),
            $keys[5] => $this->getDbStatus(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcFiles) {
                $result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCeleryTaskss) {
                $result['CeleryTaskss'] = $this->collCeleryTaskss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ThirdPartyTrackReferencesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbService($value);
                break;
            case 2:
                $this->setDbForeignId($value);
                break;
            case 3:
                $this->setDbFileId($value);
                break;
            case 4:
                $this->setDbUploadTime($value);
                break;
            case 5:
                $this->setDbStatus($value);
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
        $keys = ThirdPartyTrackReferencesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbService($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbForeignId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbFileId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbUploadTime($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbStatus($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ThirdPartyTrackReferencesPeer::DATABASE_NAME);

        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::ID)) $criteria->add(ThirdPartyTrackReferencesPeer::ID, $this->id);
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::SERVICE)) $criteria->add(ThirdPartyTrackReferencesPeer::SERVICE, $this->service);
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::FOREIGN_ID)) $criteria->add(ThirdPartyTrackReferencesPeer::FOREIGN_ID, $this->foreign_id);
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::FILE_ID)) $criteria->add(ThirdPartyTrackReferencesPeer::FILE_ID, $this->file_id);
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::UPLOAD_TIME)) $criteria->add(ThirdPartyTrackReferencesPeer::UPLOAD_TIME, $this->upload_time);
        if ($this->isColumnModified(ThirdPartyTrackReferencesPeer::STATUS)) $criteria->add(ThirdPartyTrackReferencesPeer::STATUS, $this->status);

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
        $criteria = new Criteria(ThirdPartyTrackReferencesPeer::DATABASE_NAME);
        $criteria->add(ThirdPartyTrackReferencesPeer::ID, $this->id);

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
     * @param object $copyObj An object of ThirdPartyTrackReferences (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbService($this->getDbService());
        $copyObj->setDbForeignId($this->getDbForeignId());
        $copyObj->setDbFileId($this->getDbFileId());
        $copyObj->setDbUploadTime($this->getDbUploadTime());
        $copyObj->setDbStatus($this->getDbStatus());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCeleryTaskss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCeleryTasks($relObj->copy($deepCopy));
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
     * @return ThirdPartyTrackReferences Clone of current object.
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
     * @return ThirdPartyTrackReferencesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ThirdPartyTrackReferencesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcFiles object.
     *
     * @param                  CcFiles $v
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcFiles(CcFiles $v = null)
    {
        if ($v === null) {
            $this->setDbFileId(0);
        } else {
            $this->setDbFileId($v->getDbId());
        }

        $this->aCcFiles = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcFiles object, it will not be re-added.
        if ($v !== null) {
            $v->addThirdPartyTrackReferences($this);
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
                $this->aCcFiles->addThirdPartyTrackReferencess($this);
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
        if ('CeleryTasks' == $relationName) {
            $this->initCeleryTaskss();
        }
    }

    /**
     * Clears out the collCeleryTaskss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     * @see        addCeleryTaskss()
     */
    public function clearCeleryTaskss()
    {
        $this->collCeleryTaskss = null; // important to set this to null since that means it is uninitialized
        $this->collCeleryTaskssPartial = null;

        return $this;
    }

    /**
     * reset is the collCeleryTaskss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCeleryTaskss($v = true)
    {
        $this->collCeleryTaskssPartial = $v;
    }

    /**
     * Initializes the collCeleryTaskss collection.
     *
     * By default this just sets the collCeleryTaskss collection to an empty array (like clearcollCeleryTaskss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCeleryTaskss($overrideExisting = true)
    {
        if (null !== $this->collCeleryTaskss && !$overrideExisting) {
            return;
        }
        $this->collCeleryTaskss = new PropelObjectCollection();
        $this->collCeleryTaskss->setModel('CeleryTasks');
    }

    /**
     * Gets an array of CeleryTasks objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ThirdPartyTrackReferences is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CeleryTasks[] List of CeleryTasks objects
     * @throws PropelException
     */
    public function getCeleryTaskss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCeleryTaskssPartial && !$this->isNew();
        if (null === $this->collCeleryTaskss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCeleryTaskss) {
                // return empty collection
                $this->initCeleryTaskss();
            } else {
                $collCeleryTaskss = CeleryTasksQuery::create(null, $criteria)
                    ->filterByThirdPartyTrackReferences($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCeleryTaskssPartial && count($collCeleryTaskss)) {
                      $this->initCeleryTaskss(false);

                      foreach ($collCeleryTaskss as $obj) {
                        if (false == $this->collCeleryTaskss->contains($obj)) {
                          $this->collCeleryTaskss->append($obj);
                        }
                      }

                      $this->collCeleryTaskssPartial = true;
                    }

                    $collCeleryTaskss->getInternalIterator()->rewind();

                    return $collCeleryTaskss;
                }

                if ($partial && $this->collCeleryTaskss) {
                    foreach ($this->collCeleryTaskss as $obj) {
                        if ($obj->isNew()) {
                            $collCeleryTaskss[] = $obj;
                        }
                    }
                }

                $this->collCeleryTaskss = $collCeleryTaskss;
                $this->collCeleryTaskssPartial = false;
            }
        }

        return $this->collCeleryTaskss;
    }

    /**
     * Sets a collection of CeleryTasks objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $celeryTaskss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function setCeleryTaskss(PropelCollection $celeryTaskss, PropelPDO $con = null)
    {
        $celeryTaskssToDelete = $this->getCeleryTaskss(new Criteria(), $con)->diff($celeryTaskss);


        $this->celeryTaskssScheduledForDeletion = $celeryTaskssToDelete;

        foreach ($celeryTaskssToDelete as $celeryTasksRemoved) {
            $celeryTasksRemoved->setThirdPartyTrackReferences(null);
        }

        $this->collCeleryTaskss = null;
        foreach ($celeryTaskss as $celeryTasks) {
            $this->addCeleryTasks($celeryTasks);
        }

        $this->collCeleryTaskss = $celeryTaskss;
        $this->collCeleryTaskssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CeleryTasks objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CeleryTasks objects.
     * @throws PropelException
     */
    public function countCeleryTaskss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCeleryTaskssPartial && !$this->isNew();
        if (null === $this->collCeleryTaskss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCeleryTaskss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCeleryTaskss());
            }
            $query = CeleryTasksQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByThirdPartyTrackReferences($this)
                ->count($con);
        }

        return count($this->collCeleryTaskss);
    }

    /**
     * Method called to associate a CeleryTasks object to this object
     * through the CeleryTasks foreign key attribute.
     *
     * @param    CeleryTasks $l CeleryTasks
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function addCeleryTasks(CeleryTasks $l)
    {
        if ($this->collCeleryTaskss === null) {
            $this->initCeleryTaskss();
            $this->collCeleryTaskssPartial = true;
        }

        if (!in_array($l, $this->collCeleryTaskss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCeleryTasks($l);

            if ($this->celeryTaskssScheduledForDeletion and $this->celeryTaskssScheduledForDeletion->contains($l)) {
                $this->celeryTaskssScheduledForDeletion->remove($this->celeryTaskssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CeleryTasks $celeryTasks The celeryTasks object to add.
     */
    protected function doAddCeleryTasks($celeryTasks)
    {
        $this->collCeleryTaskss[]= $celeryTasks;
        $celeryTasks->setThirdPartyTrackReferences($this);
    }

    /**
     * @param	CeleryTasks $celeryTasks The celeryTasks object to remove.
     * @return ThirdPartyTrackReferences The current object (for fluent API support)
     */
    public function removeCeleryTasks($celeryTasks)
    {
        if ($this->getCeleryTaskss()->contains($celeryTasks)) {
            $this->collCeleryTaskss->remove($this->collCeleryTaskss->search($celeryTasks));
            if (null === $this->celeryTaskssScheduledForDeletion) {
                $this->celeryTaskssScheduledForDeletion = clone $this->collCeleryTaskss;
                $this->celeryTaskssScheduledForDeletion->clear();
            }
            $this->celeryTaskssScheduledForDeletion[]= clone $celeryTasks;
            $celeryTasks->setThirdPartyTrackReferences(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->service = null;
        $this->foreign_id = null;
        $this->file_id = null;
        $this->upload_time = null;
        $this->status = null;
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
            if ($this->collCeleryTaskss) {
                foreach ($this->collCeleryTaskss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcFiles instanceof Persistent) {
              $this->aCcFiles->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCeleryTaskss instanceof PropelCollection) {
            $this->collCeleryTaskss->clearIterator();
        }
        $this->collCeleryTaskss = null;
        $this->aCcFiles = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ThirdPartyTrackReferencesPeer::DEFAULT_STRING_FORMAT);
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
