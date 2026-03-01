<?php


/**
 * Base class that represents a row from the 'podcast_episodes' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BasePodcastEpisodes extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'PodcastEpisodesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PodcastEpisodesPeer
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
     * The value for the podcast_id field.
     * @var        int
     */
    protected $podcast_id;

    /**
     * The value for the publication_date field.
     * @var        string
     */
    protected $publication_date;

    /**
     * The value for the download_url field.
     * @var        string
     */
    protected $download_url;

    /**
     * The value for the episode_guid field.
     * @var        string
     */
    protected $episode_guid;

    /**
     * The value for the episode_title field.
     * @var        string
     */
    protected $episode_title;

    /**
     * The value for the episode_description field.
     * @var        string
     */
    protected $episode_description;

    /**
     * @var        CcFiles
     */
    protected $aCcFiles;

    /**
     * @var        Podcast
     */
    protected $aPodcast;

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
     * Get the [podcast_id] column value.
     *
     * @return int
     */
    public function getDbPodcastId()
    {

        return $this->podcast_id;
    }

    /**
     * Get the [optionally formatted] temporal [publication_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbPublicationDate($format = 'Y-m-d H:i:s')
    {
        if ($this->publication_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->publication_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->publication_date, true), $x);
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
     * Get the [download_url] column value.
     *
     * @return string
     */
    public function getDbDownloadUrl()
    {

        return $this->download_url;
    }

    /**
     * Get the [episode_guid] column value.
     *
     * @return string
     */
    public function getDbEpisodeGuid()
    {

        return $this->episode_guid;
    }

    /**
     * Get the [episode_title] column value.
     *
     * @return string
     */
    public function getDbEpisodeTitle()
    {

        return $this->episode_title;
    }

    /**
     * Get the [episode_description] column value.
     *
     * @return string
     */
    public function getDbEpisodeDescription()
    {

        return $this->episode_description;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [file_id] column.
     *
     * @param  int $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbFileId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->file_id !== $v) {
            $this->file_id = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::FILE_ID;
        }

        if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
            $this->aCcFiles = null;
        }


        return $this;
    } // setDbFileId()

    /**
     * Set the value of [podcast_id] column.
     *
     * @param  int $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbPodcastId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->podcast_id !== $v) {
            $this->podcast_id = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::PODCAST_ID;
        }

        if ($this->aPodcast !== null && $this->aPodcast->getDbId() !== $v) {
            $this->aPodcast = null;
        }


        return $this;
    } // setDbPodcastId()

    /**
     * Sets the value of [publication_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbPublicationDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->publication_date !== null || $dt !== null) {
            $currentDateAsString = ($this->publication_date !== null && $tmpDt = new DateTime($this->publication_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->publication_date = $newDateAsString;
                $this->modifiedColumns[] = PodcastEpisodesPeer::PUBLICATION_DATE;
            }
        } // if either are not null


        return $this;
    } // setDbPublicationDate()

    /**
     * Set the value of [download_url] column.
     *
     * @param  string $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbDownloadUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->download_url !== $v) {
            $this->download_url = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::DOWNLOAD_URL;
        }


        return $this;
    } // setDbDownloadUrl()

    /**
     * Set the value of [episode_guid] column.
     *
     * @param  string $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbEpisodeGuid($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->episode_guid !== $v) {
            $this->episode_guid = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::EPISODE_GUID;
        }


        return $this;
    } // setDbEpisodeGuid()

    /**
     * Set the value of [episode_title] column.
     *
     * @param  string $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbEpisodeTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->episode_title !== $v) {
            $this->episode_title = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::EPISODE_TITLE;
        }


        return $this;
    } // setDbEpisodeTitle()

    /**
     * Set the value of [episode_description] column.
     *
     * @param  string $v new value
     * @return PodcastEpisodes The current object (for fluent API support)
     */
    public function setDbEpisodeDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->episode_description !== $v) {
            $this->episode_description = $v;
            $this->modifiedColumns[] = PodcastEpisodesPeer::EPISODE_DESCRIPTION;
        }


        return $this;
    } // setDbEpisodeDescription()

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
            $this->podcast_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->publication_date = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->download_url = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->episode_guid = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->episode_title = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->episode_description = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = PodcastEpisodesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating PodcastEpisodes object", $e);
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
        if ($this->aPodcast !== null && $this->podcast_id !== $this->aPodcast->getDbId()) {
            $this->aPodcast = null;
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
            $con = Propel::getConnection(PodcastEpisodesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PodcastEpisodesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcFiles = null;
            $this->aPodcast = null;
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
            $con = Propel::getConnection(PodcastEpisodesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PodcastEpisodesQuery::create()
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
            $con = Propel::getConnection(PodcastEpisodesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PodcastEpisodesPeer::addInstanceToPool($this);
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

            if ($this->aPodcast !== null) {
                if ($this->aPodcast->isModified() || $this->aPodcast->isNew()) {
                    $affectedRows += $this->aPodcast->save($con);
                }
                $this->setPodcast($this->aPodcast);
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

        $this->modifiedColumns[] = PodcastEpisodesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PodcastEpisodesPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('podcast_episodes_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PodcastEpisodesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::FILE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"file_id"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::PODCAST_ID)) {
            $modifiedColumns[':p' . $index++]  = '"podcast_id"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::PUBLICATION_DATE)) {
            $modifiedColumns[':p' . $index++]  = '"publication_date"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::DOWNLOAD_URL)) {
            $modifiedColumns[':p' . $index++]  = '"download_url"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_GUID)) {
            $modifiedColumns[':p' . $index++]  = '"episode_guid"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"episode_title"';
        }
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"episode_description"';
        }

        $sql = sprintf(
            'INSERT INTO "podcast_episodes" (%s) VALUES (%s)',
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
                    case '"podcast_id"':
                        $stmt->bindValue($identifier, $this->podcast_id, PDO::PARAM_INT);
                        break;
                    case '"publication_date"':
                        $stmt->bindValue($identifier, $this->publication_date, PDO::PARAM_STR);
                        break;
                    case '"download_url"':
                        $stmt->bindValue($identifier, $this->download_url, PDO::PARAM_STR);
                        break;
                    case '"episode_guid"':
                        $stmt->bindValue($identifier, $this->episode_guid, PDO::PARAM_STR);
                        break;
                    case '"episode_title"':
                        $stmt->bindValue($identifier, $this->episode_title, PDO::PARAM_STR);
                        break;
                    case '"episode_description"':
                        $stmt->bindValue($identifier, $this->episode_description, PDO::PARAM_STR);
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

            if ($this->aPodcast !== null) {
                if (!$this->aPodcast->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPodcast->getValidationFailures());
                }
            }


            if (($retval = PodcastEpisodesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = PodcastEpisodesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbPodcastId();
                break;
            case 3:
                return $this->getDbPublicationDate();
                break;
            case 4:
                return $this->getDbDownloadUrl();
                break;
            case 5:
                return $this->getDbEpisodeGuid();
                break;
            case 6:
                return $this->getDbEpisodeTitle();
                break;
            case 7:
                return $this->getDbEpisodeDescription();
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
        if (isset($alreadyDumpedObjects['PodcastEpisodes'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['PodcastEpisodes'][$this->getPrimaryKey()] = true;
        $keys = PodcastEpisodesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbFileId(),
            $keys[2] => $this->getDbPodcastId(),
            $keys[3] => $this->getDbPublicationDate(),
            $keys[4] => $this->getDbDownloadUrl(),
            $keys[5] => $this->getDbEpisodeGuid(),
            $keys[6] => $this->getDbEpisodeTitle(),
            $keys[7] => $this->getDbEpisodeDescription(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcFiles) {
                $result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPodcast) {
                $result['Podcast'] = $this->aPodcast->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = PodcastEpisodesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbPodcastId($value);
                break;
            case 3:
                $this->setDbPublicationDate($value);
                break;
            case 4:
                $this->setDbDownloadUrl($value);
                break;
            case 5:
                $this->setDbEpisodeGuid($value);
                break;
            case 6:
                $this->setDbEpisodeTitle($value);
                break;
            case 7:
                $this->setDbEpisodeDescription($value);
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
        $keys = PodcastEpisodesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbFileId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbPodcastId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbPublicationDate($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbDownloadUrl($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbEpisodeGuid($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbEpisodeTitle($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbEpisodeDescription($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PodcastEpisodesPeer::DATABASE_NAME);

        if ($this->isColumnModified(PodcastEpisodesPeer::ID)) $criteria->add(PodcastEpisodesPeer::ID, $this->id);
        if ($this->isColumnModified(PodcastEpisodesPeer::FILE_ID)) $criteria->add(PodcastEpisodesPeer::FILE_ID, $this->file_id);
        if ($this->isColumnModified(PodcastEpisodesPeer::PODCAST_ID)) $criteria->add(PodcastEpisodesPeer::PODCAST_ID, $this->podcast_id);
        if ($this->isColumnModified(PodcastEpisodesPeer::PUBLICATION_DATE)) $criteria->add(PodcastEpisodesPeer::PUBLICATION_DATE, $this->publication_date);
        if ($this->isColumnModified(PodcastEpisodesPeer::DOWNLOAD_URL)) $criteria->add(PodcastEpisodesPeer::DOWNLOAD_URL, $this->download_url);
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_GUID)) $criteria->add(PodcastEpisodesPeer::EPISODE_GUID, $this->episode_guid);
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_TITLE)) $criteria->add(PodcastEpisodesPeer::EPISODE_TITLE, $this->episode_title);
        if ($this->isColumnModified(PodcastEpisodesPeer::EPISODE_DESCRIPTION)) $criteria->add(PodcastEpisodesPeer::EPISODE_DESCRIPTION, $this->episode_description);

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
        $criteria = new Criteria(PodcastEpisodesPeer::DATABASE_NAME);
        $criteria->add(PodcastEpisodesPeer::ID, $this->id);

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
     * @param object $copyObj An object of PodcastEpisodes (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbFileId($this->getDbFileId());
        $copyObj->setDbPodcastId($this->getDbPodcastId());
        $copyObj->setDbPublicationDate($this->getDbPublicationDate());
        $copyObj->setDbDownloadUrl($this->getDbDownloadUrl());
        $copyObj->setDbEpisodeGuid($this->getDbEpisodeGuid());
        $copyObj->setDbEpisodeTitle($this->getDbEpisodeTitle());
        $copyObj->setDbEpisodeDescription($this->getDbEpisodeDescription());

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
     * @return PodcastEpisodes Clone of current object.
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
     * @return PodcastEpisodesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PodcastEpisodesPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcFiles object.
     *
     * @param                  CcFiles $v
     * @return PodcastEpisodes The current object (for fluent API support)
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
            $v->addPodcastEpisodes($this);
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
                $this->aCcFiles->addPodcastEpisodess($this);
             */
        }

        return $this->aCcFiles;
    }

    /**
     * Declares an association between this object and a Podcast object.
     *
     * @param                  Podcast $v
     * @return PodcastEpisodes The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPodcast(Podcast $v = null)
    {
        if ($v === null) {
            $this->setDbPodcastId(NULL);
        } else {
            $this->setDbPodcastId($v->getDbId());
        }

        $this->aPodcast = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Podcast object, it will not be re-added.
        if ($v !== null) {
            $v->addPodcastEpisodes($this);
        }


        return $this;
    }


    /**
     * Get the associated Podcast object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Podcast The associated Podcast object.
     * @throws PropelException
     */
    public function getPodcast(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPodcast === null && ($this->podcast_id !== null) && $doQuery) {
            $this->aPodcast = PodcastQuery::create()->findPk($this->podcast_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPodcast->addPodcastEpisodess($this);
             */
        }

        return $this->aPodcast;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->file_id = null;
        $this->podcast_id = null;
        $this->publication_date = null;
        $this->download_url = null;
        $this->episode_guid = null;
        $this->episode_title = null;
        $this->episode_description = null;
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
            if ($this->aCcFiles instanceof Persistent) {
              $this->aCcFiles->clearAllReferences($deep);
            }
            if ($this->aPodcast instanceof Persistent) {
              $this->aPodcast->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aCcFiles = null;
        $this->aPodcast = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PodcastEpisodesPeer::DEFAULT_STRING_FORMAT);
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
