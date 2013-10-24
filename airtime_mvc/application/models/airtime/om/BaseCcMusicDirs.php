<?php

namespace Airtime\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Airtime\CcFiles;
use Airtime\CcFilesQuery;
use Airtime\CcMusicDirs;
use Airtime\CcMusicDirsPeer;
use Airtime\CcMusicDirsQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFileQuery;

/**
 * Base class that represents a row from the 'cc_music_dirs' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcMusicDirs extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\CcMusicDirsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcMusicDirsPeer
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
     * The value for the directory field.
     * @var        string
     */
    protected $directory;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the exists field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $exists;

    /**
     * The value for the watched field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $watched;

    /**
     * @var        PropelObjectCollection|CcFiles[] Collection to store aggregation of CcFiles objects.
     */
    protected $collCcFiless;
    protected $collCcFilessPartial;

    /**
     * @var        PropelObjectCollection|AudioFile[] Collection to store aggregation of AudioFile objects.
     */
    protected $collAudioFiles;
    protected $collAudioFilesPartial;

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
    protected $ccFilessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $audioFilesScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->exists = true;
        $this->watched = true;
    }

    /**
     * Initializes internal state of BaseCcMusicDirs object.
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
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [directory] column value.
     *
     * @return string
     */
    public function getDirectory()
    {

        return $this->directory;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [exists] column value.
     *
     * @return boolean
     */
    public function getExists()
    {

        return $this->exists;
    }

    /**
     * Get the [watched] column value.
     *
     * @return boolean
     */
    public function getWatched()
    {

        return $this->watched;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcMusicDirsPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [directory] column.
     *
     * @param  string $v new value
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setDirectory($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->directory !== $v) {
            $this->directory = $v;
            $this->modifiedColumns[] = CcMusicDirsPeer::DIRECTORY;
        }


        return $this;
    } // setDirectory()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CcMusicDirsPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Sets the value of the [exists] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setExists($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->exists !== $v) {
            $this->exists = $v;
            $this->modifiedColumns[] = CcMusicDirsPeer::EXISTS;
        }


        return $this;
    } // setExists()

    /**
     * Sets the value of the [watched] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setWatched($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->watched !== $v) {
            $this->watched = $v;
            $this->modifiedColumns[] = CcMusicDirsPeer::WATCHED;
        }


        return $this;
    } // setWatched()

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
            if ($this->exists !== true) {
                return false;
            }

            if ($this->watched !== true) {
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
            $this->directory = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->type = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->exists = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->watched = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = CcMusicDirsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcMusicDirs object", $e);
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
            $con = Propel::getConnection(CcMusicDirsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcMusicDirsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCcFiless = null;

            $this->collAudioFiles = null;

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
            $con = Propel::getConnection(CcMusicDirsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcMusicDirsQuery::create()
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
            $con = Propel::getConnection(CcMusicDirsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcMusicDirsPeer::addInstanceToPool($this);
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

            if ($this->ccFilessScheduledForDeletion !== null) {
                if (!$this->ccFilessScheduledForDeletion->isEmpty()) {
                    foreach ($this->ccFilessScheduledForDeletion as $ccFiles) {
                        // need to save related object because we set the relation to null
                        $ccFiles->save($con);
                    }
                    $this->ccFilessScheduledForDeletion = null;
                }
            }

            if ($this->collCcFiless !== null) {
                foreach ($this->collCcFiless as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->audioFilesScheduledForDeletion !== null) {
                if (!$this->audioFilesScheduledForDeletion->isEmpty()) {
                    foreach ($this->audioFilesScheduledForDeletion as $audioFile) {
                        // need to save related object because we set the relation to null
                        $audioFile->save($con);
                    }
                    $this->audioFilesScheduledForDeletion = null;
                }
            }

            if ($this->collAudioFiles !== null) {
                foreach ($this->collAudioFiles as $referrerFK) {
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

        $this->modifiedColumns[] = CcMusicDirsPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcMusicDirsPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_music_dirs_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcMusicDirsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcMusicDirsPeer::DIRECTORY)) {
            $modifiedColumns[':p' . $index++]  = '"directory"';
        }
        if ($this->isColumnModified(CcMusicDirsPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }
        if ($this->isColumnModified(CcMusicDirsPeer::EXISTS)) {
            $modifiedColumns[':p' . $index++]  = '"exists"';
        }
        if ($this->isColumnModified(CcMusicDirsPeer::WATCHED)) {
            $modifiedColumns[':p' . $index++]  = '"watched"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_music_dirs" (%s) VALUES (%s)',
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
                    case '"directory"':
                        $stmt->bindValue($identifier, $this->directory, PDO::PARAM_STR);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case '"exists"':
                        $stmt->bindValue($identifier, $this->exists, PDO::PARAM_BOOL);
                        break;
                    case '"watched"':
                        $stmt->bindValue($identifier, $this->watched, PDO::PARAM_BOOL);
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


            if (($retval = CcMusicDirsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcFiless !== null) {
                    foreach ($this->collCcFiless as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAudioFiles !== null) {
                    foreach ($this->collAudioFiles as $referrerFK) {
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
        $pos = CcMusicDirsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getId();
                break;
            case 1:
                return $this->getDirectory();
                break;
            case 2:
                return $this->getType();
                break;
            case 3:
                return $this->getExists();
                break;
            case 4:
                return $this->getWatched();
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
        if (isset($alreadyDumpedObjects['CcMusicDirs'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcMusicDirs'][$this->getPrimaryKey()] = true;
        $keys = CcMusicDirsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDirectory(),
            $keys[2] => $this->getType(),
            $keys[3] => $this->getExists(),
            $keys[4] => $this->getWatched(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCcFiless) {
                $result['CcFiless'] = $this->collCcFiless->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAudioFiles) {
                $result['AudioFiles'] = $this->collAudioFiles->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcMusicDirsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setId($value);
                break;
            case 1:
                $this->setDirectory($value);
                break;
            case 2:
                $this->setType($value);
                break;
            case 3:
                $this->setExists($value);
                break;
            case 4:
                $this->setWatched($value);
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
        $keys = CcMusicDirsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDirectory($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setType($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setExists($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setWatched($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcMusicDirsPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcMusicDirsPeer::ID)) $criteria->add(CcMusicDirsPeer::ID, $this->id);
        if ($this->isColumnModified(CcMusicDirsPeer::DIRECTORY)) $criteria->add(CcMusicDirsPeer::DIRECTORY, $this->directory);
        if ($this->isColumnModified(CcMusicDirsPeer::TYPE)) $criteria->add(CcMusicDirsPeer::TYPE, $this->type);
        if ($this->isColumnModified(CcMusicDirsPeer::EXISTS)) $criteria->add(CcMusicDirsPeer::EXISTS, $this->exists);
        if ($this->isColumnModified(CcMusicDirsPeer::WATCHED)) $criteria->add(CcMusicDirsPeer::WATCHED, $this->watched);

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
        $criteria = new Criteria(CcMusicDirsPeer::DATABASE_NAME);
        $criteria->add(CcMusicDirsPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of CcMusicDirs (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDirectory($this->getDirectory());
        $copyObj->setType($this->getType());
        $copyObj->setExists($this->getExists());
        $copyObj->setWatched($this->getWatched());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcFiless() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcFiles($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAudioFiles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAudioFile($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
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
     * @return CcMusicDirs Clone of current object.
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
     * @return CcMusicDirsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcMusicDirsPeer();
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
        if ('CcFiles' == $relationName) {
            $this->initCcFiless();
        }
        if ('AudioFile' == $relationName) {
            $this->initAudioFiles();
        }
    }

    /**
     * Clears out the collCcFiless collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcMusicDirs The current object (for fluent API support)
     * @see        addCcFiless()
     */
    public function clearCcFiless()
    {
        $this->collCcFiless = null; // important to set this to null since that means it is uninitialized
        $this->collCcFilessPartial = null;

        return $this;
    }

    /**
     * reset is the collCcFiless collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcFiless($v = true)
    {
        $this->collCcFilessPartial = $v;
    }

    /**
     * Initializes the collCcFiless collection.
     *
     * By default this just sets the collCcFiless collection to an empty array (like clearcollCcFiless());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcFiless($overrideExisting = true)
    {
        if (null !== $this->collCcFiless && !$overrideExisting) {
            return;
        }
        $this->collCcFiless = new PropelObjectCollection();
        $this->collCcFiless->setModel('CcFiles');
    }

    /**
     * Gets an array of CcFiles objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcMusicDirs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     * @throws PropelException
     */
    public function getCcFiless($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessPartial && !$this->isNew();
        if (null === $this->collCcFiless || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcFiless) {
                // return empty collection
                $this->initCcFiless();
            } else {
                $collCcFiless = CcFilesQuery::create(null, $criteria)
                    ->filterByCcMusicDirs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcFilessPartial && count($collCcFiless)) {
                      $this->initCcFiless(false);

                      foreach ($collCcFiless as $obj) {
                        if (false == $this->collCcFiless->contains($obj)) {
                          $this->collCcFiless->append($obj);
                        }
                      }

                      $this->collCcFilessPartial = true;
                    }

                    $collCcFiless->getInternalIterator()->rewind();

                    return $collCcFiless;
                }

                if ($partial && $this->collCcFiless) {
                    foreach ($this->collCcFiless as $obj) {
                        if ($obj->isNew()) {
                            $collCcFiless[] = $obj;
                        }
                    }
                }

                $this->collCcFiless = $collCcFiless;
                $this->collCcFilessPartial = false;
            }
        }

        return $this->collCcFiless;
    }

    /**
     * Sets a collection of CcFiles objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccFiless A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setCcFiless(PropelCollection $ccFiless, PropelPDO $con = null)
    {
        $ccFilessToDelete = $this->getCcFiless(new Criteria(), $con)->diff($ccFiless);


        $this->ccFilessScheduledForDeletion = $ccFilessToDelete;

        foreach ($ccFilessToDelete as $ccFilesRemoved) {
            $ccFilesRemoved->setCcMusicDirs(null);
        }

        $this->collCcFiless = null;
        foreach ($ccFiless as $ccFiles) {
            $this->addCcFiles($ccFiles);
        }

        $this->collCcFiless = $ccFiless;
        $this->collCcFilessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcFiles objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcFiles objects.
     * @throws PropelException
     */
    public function countCcFiless(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessPartial && !$this->isNew();
        if (null === $this->collCcFiless || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcFiless) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcFiless());
            }
            $query = CcFilesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcMusicDirs($this)
                ->count($con);
        }

        return count($this->collCcFiless);
    }

    /**
     * Method called to associate a CcFiles object to this object
     * through the CcFiles foreign key attribute.
     *
     * @param    CcFiles $l CcFiles
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function addCcFiles(CcFiles $l)
    {
        if ($this->collCcFiless === null) {
            $this->initCcFiless();
            $this->collCcFilessPartial = true;
        }

        if (!in_array($l, $this->collCcFiless->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcFiles($l);

            if ($this->ccFilessScheduledForDeletion and $this->ccFilessScheduledForDeletion->contains($l)) {
                $this->ccFilessScheduledForDeletion->remove($this->ccFilessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcFiles $ccFiles The ccFiles object to add.
     */
    protected function doAddCcFiles($ccFiles)
    {
        $this->collCcFiless[]= $ccFiles;
        $ccFiles->setCcMusicDirs($this);
    }

    /**
     * @param	CcFiles $ccFiles The ccFiles object to remove.
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function removeCcFiles($ccFiles)
    {
        if ($this->getCcFiless()->contains($ccFiles)) {
            $this->collCcFiless->remove($this->collCcFiless->search($ccFiles));
            if (null === $this->ccFilessScheduledForDeletion) {
                $this->ccFilessScheduledForDeletion = clone $this->collCcFiless;
                $this->ccFilessScheduledForDeletion->clear();
            }
            $this->ccFilessScheduledForDeletion[]= $ccFiles;
            $ccFiles->setCcMusicDirs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcMusicDirs is new, it will return
     * an empty collection; or if this CcMusicDirs has previously
     * been saved, it will retrieve related CcFiless from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcMusicDirs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     */
    public function getCcFilessJoinFkOwner($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcFilesQuery::create(null, $criteria);
        $query->joinWith('FkOwner', $join_behavior);

        return $this->getCcFiless($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcMusicDirs is new, it will return
     * an empty collection; or if this CcMusicDirs has previously
     * been saved, it will retrieve related CcFiless from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcMusicDirs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     */
    public function getCcFilessJoinCcSubjsRelatedByDbEditedby($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcFilesQuery::create(null, $criteria);
        $query->joinWith('CcSubjsRelatedByDbEditedby', $join_behavior);

        return $this->getCcFiless($query, $con);
    }

    /**
     * Clears out the collAudioFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcMusicDirs The current object (for fluent API support)
     * @see        addAudioFiles()
     */
    public function clearAudioFiles()
    {
        $this->collAudioFiles = null; // important to set this to null since that means it is uninitialized
        $this->collAudioFilesPartial = null;

        return $this;
    }

    /**
     * reset is the collAudioFiles collection loaded partially
     *
     * @return void
     */
    public function resetPartialAudioFiles($v = true)
    {
        $this->collAudioFilesPartial = $v;
    }

    /**
     * Initializes the collAudioFiles collection.
     *
     * By default this just sets the collAudioFiles collection to an empty array (like clearcollAudioFiles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAudioFiles($overrideExisting = true)
    {
        if (null !== $this->collAudioFiles && !$overrideExisting) {
            return;
        }
        $this->collAudioFiles = new PropelObjectCollection();
        $this->collAudioFiles->setModel('AudioFile');
    }

    /**
     * Gets an array of AudioFile objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcMusicDirs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AudioFile[] List of AudioFile objects
     * @throws PropelException
     */
    public function getAudioFiles($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAudioFilesPartial && !$this->isNew();
        if (null === $this->collAudioFiles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAudioFiles) {
                // return empty collection
                $this->initAudioFiles();
            } else {
                $collAudioFiles = AudioFileQuery::create(null, $criteria)
                    ->filterByCcMusicDirs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAudioFilesPartial && count($collAudioFiles)) {
                      $this->initAudioFiles(false);

                      foreach ($collAudioFiles as $obj) {
                        if (false == $this->collAudioFiles->contains($obj)) {
                          $this->collAudioFiles->append($obj);
                        }
                      }

                      $this->collAudioFilesPartial = true;
                    }

                    $collAudioFiles->getInternalIterator()->rewind();

                    return $collAudioFiles;
                }

                if ($partial && $this->collAudioFiles) {
                    foreach ($this->collAudioFiles as $obj) {
                        if ($obj->isNew()) {
                            $collAudioFiles[] = $obj;
                        }
                    }
                }

                $this->collAudioFiles = $collAudioFiles;
                $this->collAudioFilesPartial = false;
            }
        }

        return $this->collAudioFiles;
    }

    /**
     * Sets a collection of AudioFile objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $audioFiles A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function setAudioFiles(PropelCollection $audioFiles, PropelPDO $con = null)
    {
        $audioFilesToDelete = $this->getAudioFiles(new Criteria(), $con)->diff($audioFiles);


        $this->audioFilesScheduledForDeletion = $audioFilesToDelete;

        foreach ($audioFilesToDelete as $audioFileRemoved) {
            $audioFileRemoved->setCcMusicDirs(null);
        }

        $this->collAudioFiles = null;
        foreach ($audioFiles as $audioFile) {
            $this->addAudioFile($audioFile);
        }

        $this->collAudioFiles = $audioFiles;
        $this->collAudioFilesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AudioFile objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AudioFile objects.
     * @throws PropelException
     */
    public function countAudioFiles(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAudioFilesPartial && !$this->isNew();
        if (null === $this->collAudioFiles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAudioFiles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAudioFiles());
            }
            $query = AudioFileQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcMusicDirs($this)
                ->count($con);
        }

        return count($this->collAudioFiles);
    }

    /**
     * Method called to associate a AudioFile object to this object
     * through the AudioFile foreign key attribute.
     *
     * @param    AudioFile $l AudioFile
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function addAudioFile(AudioFile $l)
    {
        if ($this->collAudioFiles === null) {
            $this->initAudioFiles();
            $this->collAudioFilesPartial = true;
        }

        if (!in_array($l, $this->collAudioFiles->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAudioFile($l);

            if ($this->audioFilesScheduledForDeletion and $this->audioFilesScheduledForDeletion->contains($l)) {
                $this->audioFilesScheduledForDeletion->remove($this->audioFilesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AudioFile $audioFile The audioFile object to add.
     */
    protected function doAddAudioFile($audioFile)
    {
        $this->collAudioFiles[]= $audioFile;
        $audioFile->setCcMusicDirs($this);
    }

    /**
     * @param	AudioFile $audioFile The audioFile object to remove.
     * @return CcMusicDirs The current object (for fluent API support)
     */
    public function removeAudioFile($audioFile)
    {
        if ($this->getAudioFiles()->contains($audioFile)) {
            $this->collAudioFiles->remove($this->collAudioFiles->search($audioFile));
            if (null === $this->audioFilesScheduledForDeletion) {
                $this->audioFilesScheduledForDeletion = clone $this->collAudioFiles;
                $this->audioFilesScheduledForDeletion->clear();
            }
            $this->audioFilesScheduledForDeletion[]= $audioFile;
            $audioFile->setCcMusicDirs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcMusicDirs is new, it will return
     * an empty collection; or if this CcMusicDirs has previously
     * been saved, it will retrieve related AudioFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcMusicDirs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AudioFile[] List of AudioFile objects
     */
    public function getAudioFilesJoinMediaItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AudioFileQuery::create(null, $criteria);
        $query->joinWith('MediaItem', $join_behavior);

        return $this->getAudioFiles($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcMusicDirs is new, it will return
     * an empty collection; or if this CcMusicDirs has previously
     * been saved, it will retrieve related AudioFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcMusicDirs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AudioFile[] List of AudioFile objects
     */
    public function getAudioFilesJoinCcSubjs($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AudioFileQuery::create(null, $criteria);
        $query->joinWith('CcSubjs', $join_behavior);

        return $this->getAudioFiles($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->directory = null;
        $this->type = null;
        $this->exists = null;
        $this->watched = null;
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
            if ($this->collCcFiless) {
                foreach ($this->collCcFiless as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAudioFiles) {
                foreach ($this->collAudioFiles as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcFiless instanceof PropelCollection) {
            $this->collCcFiless->clearIterator();
        }
        $this->collCcFiless = null;
        if ($this->collAudioFiles instanceof PropelCollection) {
            $this->collAudioFiles->clearIterator();
        }
        $this->collAudioFiles = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcMusicDirsPeer::DEFAULT_STRING_FORMAT);
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
