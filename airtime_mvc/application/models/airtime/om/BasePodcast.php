<?php


/**
 * Base class that represents a row from the 'podcast' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BasePodcast extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'PodcastPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PodcastPeer
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
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the creator field.
     * @var        string
     */
    protected $creator;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the auto_ingest field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $auto_ingest;

    /**
     * The value for the owner field.
     * @var        int
     */
    protected $owner;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $type;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjs;

    /**
     * @var        PropelObjectCollection|PodcastEpisodes[] Collection to store aggregation of PodcastEpisodes objects.
     */
    protected $collPodcastEpisodess;
    protected $collPodcastEpisodessPartial;

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
    protected $podcastEpisodessScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->auto_ingest = false;
        $this->type = 1;
    }

    /**
     * Initializes internal state of BasePodcast object.
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
     * Get the [url] column value.
     *
     * @return string
     */
    public function getDbUrl()
    {

        return $this->url;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getDbTitle()
    {

        return $this->title;
    }

    /**
     * Get the [creator] column value.
     *
     * @return string
     */
    public function getDbCreator()
    {

        return $this->creator;
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
     * Get the [auto_ingest] column value.
     *
     * @return boolean
     */
    public function getDbAutoIngest()
    {

        return $this->auto_ingest;
    }

    /**
     * Get the [owner] column value.
     *
     * @return int
     */
    public function getDbOwner()
    {

        return $this->owner;
    }

    /**
     * Get the [type] column value.
     *
     * @return int
     */
    public function getDbType()
    {

        return $this->type;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PodcastPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [url] column.
     *
     * @param  string $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbUrl($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = PodcastPeer::URL;
        }


        return $this;
    } // setDbUrl()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PodcastPeer::TITLE;
        }


        return $this;
    } // setDbTitle()

    /**
     * Set the value of [creator] column.
     *
     * @param  string $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbCreator($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->creator !== $v) {
            $this->creator = $v;
            $this->modifiedColumns[] = PodcastPeer::CREATOR;
        }


        return $this;
    } // setDbCreator()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = PodcastPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Sets the value of the [auto_ingest] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbAutoIngest($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->auto_ingest !== $v) {
            $this->auto_ingest = $v;
            $this->modifiedColumns[] = PodcastPeer::AUTO_INGEST;
        }


        return $this;
    } // setDbAutoIngest()

    /**
     * Set the value of [owner] column.
     *
     * @param  int $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbOwner($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner !== $v) {
            $this->owner = $v;
            $this->modifiedColumns[] = PodcastPeer::OWNER;
        }

        if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
            $this->aCcSubjs = null;
        }


        return $this;
    } // setDbOwner()

    /**
     * Set the value of [type] column.
     *
     * @param  int $v new value
     * @return Podcast The current object (for fluent API support)
     */
    public function setDbType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = PodcastPeer::TYPE;
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
            if ($this->auto_ingest !== false) {
                return false;
            }

            if ($this->type !== 1) {
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
            $this->url = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->title = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->creator = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->auto_ingest = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->owner = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->type = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = PodcastPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Podcast object", $e);
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

        if ($this->aCcSubjs !== null && $this->owner !== $this->aCcSubjs->getDbId()) {
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
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PodcastPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcSubjs = null;
            $this->collPodcastEpisodess = null;

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
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PodcastQuery::create()
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
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PodcastPeer::addInstanceToPool($this);
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

            if ($this->podcastEpisodessScheduledForDeletion !== null) {
                if (!$this->podcastEpisodessScheduledForDeletion->isEmpty()) {
                    PodcastEpisodesQuery::create()
                        ->filterByPrimaryKeys($this->podcastEpisodessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->podcastEpisodessScheduledForDeletion = null;
                }
            }

            if ($this->collPodcastEpisodess !== null) {
                foreach ($this->collPodcastEpisodess as $referrerFK) {
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

        $this->modifiedColumns[] = PodcastPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PodcastPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('podcast_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PodcastPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(PodcastPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = '"url"';
        }
        if ($this->isColumnModified(PodcastPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"title"';
        }
        if ($this->isColumnModified(PodcastPeer::CREATOR)) {
            $modifiedColumns[':p' . $index++]  = '"creator"';
        }
        if ($this->isColumnModified(PodcastPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(PodcastPeer::AUTO_INGEST)) {
            $modifiedColumns[':p' . $index++]  = '"auto_ingest"';
        }
        if ($this->isColumnModified(PodcastPeer::OWNER)) {
            $modifiedColumns[':p' . $index++]  = '"owner"';
        }
        if ($this->isColumnModified(PodcastPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }

        $sql = sprintf(
            'INSERT INTO "podcast" (%s) VALUES (%s)',
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
                    case '"url"':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case '"title"':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '"creator"':
                        $stmt->bindValue($identifier, $this->creator, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"auto_ingest"':
                        $stmt->bindValue($identifier, $this->auto_ingest, PDO::PARAM_BOOL);
                        break;
                    case '"owner"':
                        $stmt->bindValue($identifier, $this->owner, PDO::PARAM_INT);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
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


            if (($retval = PodcastPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPodcastEpisodess !== null) {
                    foreach ($this->collPodcastEpisodess as $referrerFK) {
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
        $pos = PodcastPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbUrl();
                break;
            case 2:
                return $this->getDbTitle();
                break;
            case 3:
                return $this->getDbCreator();
                break;
            case 4:
                return $this->getDbDescription();
                break;
            case 5:
                return $this->getDbAutoIngest();
                break;
            case 6:
                return $this->getDbOwner();
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
        if (isset($alreadyDumpedObjects['Podcast'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Podcast'][$this->getPrimaryKey()] = true;
        $keys = PodcastPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbUrl(),
            $keys[2] => $this->getDbTitle(),
            $keys[3] => $this->getDbCreator(),
            $keys[4] => $this->getDbDescription(),
            $keys[5] => $this->getDbAutoIngest(),
            $keys[6] => $this->getDbOwner(),
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
            if (null !== $this->collPodcastEpisodess) {
                $result['PodcastEpisodess'] = $this->collPodcastEpisodess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PodcastPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbUrl($value);
                break;
            case 2:
                $this->setDbTitle($value);
                break;
            case 3:
                $this->setDbCreator($value);
                break;
            case 4:
                $this->setDbDescription($value);
                break;
            case 5:
                $this->setDbAutoIngest($value);
                break;
            case 6:
                $this->setDbOwner($value);
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
        $keys = PodcastPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbUrl($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbCreator($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbAutoIngest($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbOwner($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbType($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PodcastPeer::DATABASE_NAME);

        if ($this->isColumnModified(PodcastPeer::ID)) $criteria->add(PodcastPeer::ID, $this->id);
        if ($this->isColumnModified(PodcastPeer::URL)) $criteria->add(PodcastPeer::URL, $this->url);
        if ($this->isColumnModified(PodcastPeer::TITLE)) $criteria->add(PodcastPeer::TITLE, $this->title);
        if ($this->isColumnModified(PodcastPeer::CREATOR)) $criteria->add(PodcastPeer::CREATOR, $this->creator);
        if ($this->isColumnModified(PodcastPeer::DESCRIPTION)) $criteria->add(PodcastPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(PodcastPeer::AUTO_INGEST)) $criteria->add(PodcastPeer::AUTO_INGEST, $this->auto_ingest);
        if ($this->isColumnModified(PodcastPeer::OWNER)) $criteria->add(PodcastPeer::OWNER, $this->owner);
        if ($this->isColumnModified(PodcastPeer::TYPE)) $criteria->add(PodcastPeer::TYPE, $this->type);

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
        $criteria = new Criteria(PodcastPeer::DATABASE_NAME);
        $criteria->add(PodcastPeer::ID, $this->id);

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
     * @param object $copyObj An object of Podcast (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbUrl($this->getDbUrl());
        $copyObj->setDbTitle($this->getDbTitle());
        $copyObj->setDbCreator($this->getDbCreator());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbAutoIngest($this->getDbAutoIngest());
        $copyObj->setDbOwner($this->getDbOwner());
        $copyObj->setDbType($this->getDbType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPodcastEpisodess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPodcastEpisodes($relObj->copy($deepCopy));
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
     * @return Podcast Clone of current object.
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
     * @return PodcastPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PodcastPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return Podcast The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcSubjs(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setDbOwner(NULL);
        } else {
            $this->setDbOwner($v->getDbId());
        }

        $this->aCcSubjs = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addPodcast($this);
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
        if ($this->aCcSubjs === null && ($this->owner !== null) && $doQuery) {
            $this->aCcSubjs = CcSubjsQuery::create()->findPk($this->owner, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcSubjs->addPodcasts($this);
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
        if ('PodcastEpisodes' == $relationName) {
            $this->initPodcastEpisodess();
        }
    }

    /**
     * Clears out the collPodcastEpisodess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Podcast The current object (for fluent API support)
     * @see        addPodcastEpisodess()
     */
    public function clearPodcastEpisodess()
    {
        $this->collPodcastEpisodess = null; // important to set this to null since that means it is uninitialized
        $this->collPodcastEpisodessPartial = null;

        return $this;
    }

    /**
     * reset is the collPodcastEpisodess collection loaded partially
     *
     * @return void
     */
    public function resetPartialPodcastEpisodess($v = true)
    {
        $this->collPodcastEpisodessPartial = $v;
    }

    /**
     * Initializes the collPodcastEpisodess collection.
     *
     * By default this just sets the collPodcastEpisodess collection to an empty array (like clearcollPodcastEpisodess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPodcastEpisodess($overrideExisting = true)
    {
        if (null !== $this->collPodcastEpisodess && !$overrideExisting) {
            return;
        }
        $this->collPodcastEpisodess = new PropelObjectCollection();
        $this->collPodcastEpisodess->setModel('PodcastEpisodes');
    }

    /**
     * Gets an array of PodcastEpisodes objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Podcast is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PodcastEpisodes[] List of PodcastEpisodes objects
     * @throws PropelException
     */
    public function getPodcastEpisodess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPodcastEpisodessPartial && !$this->isNew();
        if (null === $this->collPodcastEpisodess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPodcastEpisodess) {
                // return empty collection
                $this->initPodcastEpisodess();
            } else {
                $collPodcastEpisodess = PodcastEpisodesQuery::create(null, $criteria)
                    ->filterByPodcast($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPodcastEpisodessPartial && count($collPodcastEpisodess)) {
                      $this->initPodcastEpisodess(false);

                      foreach ($collPodcastEpisodess as $obj) {
                        if (false == $this->collPodcastEpisodess->contains($obj)) {
                          $this->collPodcastEpisodess->append($obj);
                        }
                      }

                      $this->collPodcastEpisodessPartial = true;
                    }

                    $collPodcastEpisodess->getInternalIterator()->rewind();

                    return $collPodcastEpisodess;
                }

                if ($partial && $this->collPodcastEpisodess) {
                    foreach ($this->collPodcastEpisodess as $obj) {
                        if ($obj->isNew()) {
                            $collPodcastEpisodess[] = $obj;
                        }
                    }
                }

                $this->collPodcastEpisodess = $collPodcastEpisodess;
                $this->collPodcastEpisodessPartial = false;
            }
        }

        return $this->collPodcastEpisodess;
    }

    /**
     * Sets a collection of PodcastEpisodes objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $podcastEpisodess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Podcast The current object (for fluent API support)
     */
    public function setPodcastEpisodess(PropelCollection $podcastEpisodess, PropelPDO $con = null)
    {
        $podcastEpisodessToDelete = $this->getPodcastEpisodess(new Criteria(), $con)->diff($podcastEpisodess);


        $this->podcastEpisodessScheduledForDeletion = $podcastEpisodessToDelete;

        foreach ($podcastEpisodessToDelete as $podcastEpisodesRemoved) {
            $podcastEpisodesRemoved->setPodcast(null);
        }

        $this->collPodcastEpisodess = null;
        foreach ($podcastEpisodess as $podcastEpisodes) {
            $this->addPodcastEpisodes($podcastEpisodes);
        }

        $this->collPodcastEpisodess = $podcastEpisodess;
        $this->collPodcastEpisodessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PodcastEpisodes objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PodcastEpisodes objects.
     * @throws PropelException
     */
    public function countPodcastEpisodess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPodcastEpisodessPartial && !$this->isNew();
        if (null === $this->collPodcastEpisodess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPodcastEpisodess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPodcastEpisodess());
            }
            $query = PodcastEpisodesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPodcast($this)
                ->count($con);
        }

        return count($this->collPodcastEpisodess);
    }

    /**
     * Method called to associate a PodcastEpisodes object to this object
     * through the PodcastEpisodes foreign key attribute.
     *
     * @param    PodcastEpisodes $l PodcastEpisodes
     * @return Podcast The current object (for fluent API support)
     */
    public function addPodcastEpisodes(PodcastEpisodes $l)
    {
        if ($this->collPodcastEpisodess === null) {
            $this->initPodcastEpisodess();
            $this->collPodcastEpisodessPartial = true;
        }

        if (!in_array($l, $this->collPodcastEpisodess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPodcastEpisodes($l);

            if ($this->podcastEpisodessScheduledForDeletion and $this->podcastEpisodessScheduledForDeletion->contains($l)) {
                $this->podcastEpisodessScheduledForDeletion->remove($this->podcastEpisodessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PodcastEpisodes $podcastEpisodes The podcastEpisodes object to add.
     */
    protected function doAddPodcastEpisodes($podcastEpisodes)
    {
        $this->collPodcastEpisodess[]= $podcastEpisodes;
        $podcastEpisodes->setPodcast($this);
    }

    /**
     * @param	PodcastEpisodes $podcastEpisodes The podcastEpisodes object to remove.
     * @return Podcast The current object (for fluent API support)
     */
    public function removePodcastEpisodes($podcastEpisodes)
    {
        if ($this->getPodcastEpisodess()->contains($podcastEpisodes)) {
            $this->collPodcastEpisodess->remove($this->collPodcastEpisodess->search($podcastEpisodes));
            if (null === $this->podcastEpisodessScheduledForDeletion) {
                $this->podcastEpisodessScheduledForDeletion = clone $this->collPodcastEpisodess;
                $this->podcastEpisodessScheduledForDeletion->clear();
            }
            $this->podcastEpisodessScheduledForDeletion[]= clone $podcastEpisodes;
            $podcastEpisodes->setPodcast(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Podcast is new, it will return
     * an empty collection; or if this Podcast has previously
     * been saved, it will retrieve related PodcastEpisodess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Podcast.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PodcastEpisodes[] List of PodcastEpisodes objects
     */
    public function getPodcastEpisodessJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PodcastEpisodesQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getPodcastEpisodess($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->url = null;
        $this->title = null;
        $this->creator = null;
        $this->description = null;
        $this->auto_ingest = null;
        $this->owner = null;
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
            if ($this->collPodcastEpisodess) {
                foreach ($this->collPodcastEpisodess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collPodcastEpisodess instanceof PropelCollection) {
            $this->collPodcastEpisodess->clearIterator();
        }
        $this->collPodcastEpisodess = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PodcastPeer::DEFAULT_STRING_FORMAT);
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
