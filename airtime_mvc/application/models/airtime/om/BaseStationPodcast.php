<?php


/**
 * Base class that represents a row from the 'station_podcast' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseStationPodcast extends Podcast implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'StationPodcastPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        StationPodcastPeer
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
     * The value for the language field.
     * @var        string
     */
    protected $language;

    /**
     * The value for the copyright field.
     * @var        string
     */
    protected $copyright;

    /**
     * The value for the link field.
     * @var        string
     */
    protected $link;

    /**
     * The value for the itunes_author field.
     * @var        string
     */
    protected $itunes_author;

    /**
     * The value for the itunes_keywords field.
     * @var        string
     */
    protected $itunes_keywords;

    /**
     * The value for the itunes_summary field.
     * @var        string
     */
    protected $itunes_summary;

    /**
     * The value for the itunes_subtitle field.
     * @var        string
     */
    protected $itunes_subtitle;

    /**
     * The value for the itunes_category field.
     * @var        string
     */
    protected $itunes_category;

    /**
     * The value for the itunes_explicit field.
     * @var        string
     */
    protected $itunes_explicit;

    /**
     * The value for the owner field.
     * @var        int
     */
    protected $owner;

    /**
     * @var        Podcast
     */
    protected $aPodcast;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjs;

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
     * Get the [language] column value.
     *
     * @return string
     */
    public function getDbLanguage()
    {

        return $this->language;
    }

    /**
     * Get the [copyright] column value.
     *
     * @return string
     */
    public function getDbCopyright()
    {

        return $this->copyright;
    }

    /**
     * Get the [link] column value.
     *
     * @return string
     */
    public function getDbLink()
    {

        return $this->link;
    }

    /**
     * Get the [itunes_author] column value.
     *
     * @return string
     */
    public function getDbItunesAuthor()
    {

        return $this->itunes_author;
    }

    /**
     * Get the [itunes_keywords] column value.
     *
     * @return string
     */
    public function getDbItunesKeywords()
    {

        return $this->itunes_keywords;
    }

    /**
     * Get the [itunes_summary] column value.
     *
     * @return string
     */
    public function getDbItunesSummary()
    {

        return $this->itunes_summary;
    }

    /**
     * Get the [itunes_subtitle] column value.
     *
     * @return string
     */
    public function getDbItunesSubtitle()
    {

        return $this->itunes_subtitle;
    }

    /**
     * Get the [itunes_category] column value.
     *
     * @return string
     */
    public function getDbItunesCategory()
    {

        return $this->itunes_category;
    }

    /**
     * Get the [itunes_explicit] column value.
     *
     * @return string
     */
    public function getDbItunesExplicit()
    {

        return $this->itunes_explicit;
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
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ID;
        }

        if ($this->aPodcast !== null && $this->aPodcast->getDbId() !== $v) {
            $this->aPodcast = null;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = StationPodcastPeer::TITLE;
        }


        return $this;
    } // setDbTitle()

    /**
     * Set the value of [creator] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbCreator($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->creator !== $v) {
            $this->creator = $v;
            $this->modifiedColumns[] = StationPodcastPeer::CREATOR;
        }


        return $this;
    } // setDbCreator()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = StationPodcastPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Set the value of [language] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbLanguage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->language !== $v) {
            $this->language = $v;
            $this->modifiedColumns[] = StationPodcastPeer::LANGUAGE;
        }


        return $this;
    } // setDbLanguage()

    /**
     * Set the value of [copyright] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbCopyright($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->copyright !== $v) {
            $this->copyright = $v;
            $this->modifiedColumns[] = StationPodcastPeer::COPYRIGHT;
        }


        return $this;
    } // setDbCopyright()

    /**
     * Set the value of [link] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbLink($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->link !== $v) {
            $this->link = $v;
            $this->modifiedColumns[] = StationPodcastPeer::LINK;
        }


        return $this;
    } // setDbLink()

    /**
     * Set the value of [itunes_author] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesAuthor($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_author !== $v) {
            $this->itunes_author = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_AUTHOR;
        }


        return $this;
    } // setDbItunesAuthor()

    /**
     * Set the value of [itunes_keywords] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesKeywords($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_keywords !== $v) {
            $this->itunes_keywords = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_KEYWORDS;
        }


        return $this;
    } // setDbItunesKeywords()

    /**
     * Set the value of [itunes_summary] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesSummary($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_summary !== $v) {
            $this->itunes_summary = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_SUMMARY;
        }


        return $this;
    } // setDbItunesSummary()

    /**
     * Set the value of [itunes_subtitle] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesSubtitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_subtitle !== $v) {
            $this->itunes_subtitle = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_SUBTITLE;
        }


        return $this;
    } // setDbItunesSubtitle()

    /**
     * Set the value of [itunes_category] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesCategory($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_category !== $v) {
            $this->itunes_category = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_CATEGORY;
        }


        return $this;
    } // setDbItunesCategory()

    /**
     * Set the value of [itunes_explicit] column.
     *
     * @param  string $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbItunesExplicit($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->itunes_explicit !== $v) {
            $this->itunes_explicit = $v;
            $this->modifiedColumns[] = StationPodcastPeer::ITUNES_EXPLICIT;
        }


        return $this;
    } // setDbItunesExplicit()

    /**
     * Set the value of [owner] column.
     *
     * @param  int $v new value
     * @return StationPodcast The current object (for fluent API support)
     */
    public function setDbOwner($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner !== $v) {
            $this->owner = $v;
            $this->modifiedColumns[] = StationPodcastPeer::OWNER;
        }

        if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
            $this->aCcSubjs = null;
        }


        return $this;
    } // setDbOwner()

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
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->creator = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->description = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->language = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->copyright = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->link = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->itunes_author = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->itunes_keywords = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->itunes_summary = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->itunes_subtitle = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->itunes_category = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->itunes_explicit = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->owner = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 14; // 14 = StationPodcastPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating StationPodcast object", $e);
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

        if ($this->aPodcast !== null && $this->id !== $this->aPodcast->getDbId()) {
            $this->aPodcast = null;
        }
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
            $con = Propel::getConnection(StationPodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = StationPodcastPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPodcast = null;
            $this->aCcSubjs = null;
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
            $con = Propel::getConnection(StationPodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = StationPodcastQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                // concrete_inheritance behavior
                $this->getParentOrCreate($con)->delete($con);

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
            $con = Propel::getConnection(StationPodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // concrete_inheritance behavior
            $parent = $this->getSyncParent($con);
            $parent->save($con);
            $this->setPrimaryKey($parent->getPrimaryKey());

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
                StationPodcastPeer::addInstanceToPool($this);
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

            if ($this->aPodcast !== null) {
                if ($this->aPodcast->isModified() || $this->aPodcast->isNew()) {
                    $affectedRows += $this->aPodcast->save($con);
                }
                $this->setPodcast($this->aPodcast);
            }

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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(StationPodcastPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(StationPodcastPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '"title"';
        }
        if ($this->isColumnModified(StationPodcastPeer::CREATOR)) {
            $modifiedColumns[':p' . $index++]  = '"creator"';
        }
        if ($this->isColumnModified(StationPodcastPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(StationPodcastPeer::LANGUAGE)) {
            $modifiedColumns[':p' . $index++]  = '"language"';
        }
        if ($this->isColumnModified(StationPodcastPeer::COPYRIGHT)) {
            $modifiedColumns[':p' . $index++]  = '"copyright"';
        }
        if ($this->isColumnModified(StationPodcastPeer::LINK)) {
            $modifiedColumns[':p' . $index++]  = '"link"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_AUTHOR)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_author"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_KEYWORDS)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_keywords"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_SUMMARY)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_summary"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_SUBTITLE)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_subtitle"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_CATEGORY)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_category"';
        }
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_EXPLICIT)) {
            $modifiedColumns[':p' . $index++]  = '"itunes_explicit"';
        }
        if ($this->isColumnModified(StationPodcastPeer::OWNER)) {
            $modifiedColumns[':p' . $index++]  = '"owner"';
        }

        $sql = sprintf(
            'INSERT INTO "station_podcast" (%s) VALUES (%s)',
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
                    case '"title"':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '"creator"':
                        $stmt->bindValue($identifier, $this->creator, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"language"':
                        $stmt->bindValue($identifier, $this->language, PDO::PARAM_STR);
                        break;
                    case '"copyright"':
                        $stmt->bindValue($identifier, $this->copyright, PDO::PARAM_STR);
                        break;
                    case '"link"':
                        $stmt->bindValue($identifier, $this->link, PDO::PARAM_STR);
                        break;
                    case '"itunes_author"':
                        $stmt->bindValue($identifier, $this->itunes_author, PDO::PARAM_STR);
                        break;
                    case '"itunes_keywords"':
                        $stmt->bindValue($identifier, $this->itunes_keywords, PDO::PARAM_STR);
                        break;
                    case '"itunes_summary"':
                        $stmt->bindValue($identifier, $this->itunes_summary, PDO::PARAM_STR);
                        break;
                    case '"itunes_subtitle"':
                        $stmt->bindValue($identifier, $this->itunes_subtitle, PDO::PARAM_STR);
                        break;
                    case '"itunes_category"':
                        $stmt->bindValue($identifier, $this->itunes_category, PDO::PARAM_STR);
                        break;
                    case '"itunes_explicit"':
                        $stmt->bindValue($identifier, $this->itunes_explicit, PDO::PARAM_STR);
                        break;
                    case '"owner"':
                        $stmt->bindValue($identifier, $this->owner, PDO::PARAM_INT);
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

            if ($this->aPodcast !== null) {
                if (!$this->aPodcast->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPodcast->getValidationFailures());
                }
            }

            if ($this->aCcSubjs !== null) {
                if (!$this->aCcSubjs->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
                }
            }


            if (($retval = StationPodcastPeer::doValidate($this, $columns)) !== true) {
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
        $pos = StationPodcastPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbTitle();
                break;
            case 2:
                return $this->getDbCreator();
                break;
            case 3:
                return $this->getDbDescription();
                break;
            case 4:
                return $this->getDbLanguage();
                break;
            case 5:
                return $this->getDbCopyright();
                break;
            case 6:
                return $this->getDbLink();
                break;
            case 7:
                return $this->getDbItunesAuthor();
                break;
            case 8:
                return $this->getDbItunesKeywords();
                break;
            case 9:
                return $this->getDbItunesSummary();
                break;
            case 10:
                return $this->getDbItunesSubtitle();
                break;
            case 11:
                return $this->getDbItunesCategory();
                break;
            case 12:
                return $this->getDbItunesExplicit();
                break;
            case 13:
                return $this->getDbOwner();
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
        if (isset($alreadyDumpedObjects['StationPodcast'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['StationPodcast'][$this->getPrimaryKey()] = true;
        $keys = StationPodcastPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbTitle(),
            $keys[2] => $this->getDbCreator(),
            $keys[3] => $this->getDbDescription(),
            $keys[4] => $this->getDbLanguage(),
            $keys[5] => $this->getDbCopyright(),
            $keys[6] => $this->getDbLink(),
            $keys[7] => $this->getDbItunesAuthor(),
            $keys[8] => $this->getDbItunesKeywords(),
            $keys[9] => $this->getDbItunesSummary(),
            $keys[10] => $this->getDbItunesSubtitle(),
            $keys[11] => $this->getDbItunesCategory(),
            $keys[12] => $this->getDbItunesExplicit(),
            $keys[13] => $this->getDbOwner(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aPodcast) {
                $result['Podcast'] = $this->aPodcast->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcSubjs) {
                $result['CcSubjs'] = $this->aCcSubjs->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = StationPodcastPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbTitle($value);
                break;
            case 2:
                $this->setDbCreator($value);
                break;
            case 3:
                $this->setDbDescription($value);
                break;
            case 4:
                $this->setDbLanguage($value);
                break;
            case 5:
                $this->setDbCopyright($value);
                break;
            case 6:
                $this->setDbLink($value);
                break;
            case 7:
                $this->setDbItunesAuthor($value);
                break;
            case 8:
                $this->setDbItunesKeywords($value);
                break;
            case 9:
                $this->setDbItunesSummary($value);
                break;
            case 10:
                $this->setDbItunesSubtitle($value);
                break;
            case 11:
                $this->setDbItunesCategory($value);
                break;
            case 12:
                $this->setDbItunesExplicit($value);
                break;
            case 13:
                $this->setDbOwner($value);
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
        $keys = StationPodcastPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbCreator($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbDescription($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbLanguage($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbCopyright($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbLink($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbItunesAuthor($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbItunesKeywords($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbItunesSummary($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbItunesSubtitle($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbItunesCategory($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbItunesExplicit($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDbOwner($arr[$keys[13]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(StationPodcastPeer::DATABASE_NAME);

        if ($this->isColumnModified(StationPodcastPeer::ID)) $criteria->add(StationPodcastPeer::ID, $this->id);
        if ($this->isColumnModified(StationPodcastPeer::TITLE)) $criteria->add(StationPodcastPeer::TITLE, $this->title);
        if ($this->isColumnModified(StationPodcastPeer::CREATOR)) $criteria->add(StationPodcastPeer::CREATOR, $this->creator);
        if ($this->isColumnModified(StationPodcastPeer::DESCRIPTION)) $criteria->add(StationPodcastPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(StationPodcastPeer::LANGUAGE)) $criteria->add(StationPodcastPeer::LANGUAGE, $this->language);
        if ($this->isColumnModified(StationPodcastPeer::COPYRIGHT)) $criteria->add(StationPodcastPeer::COPYRIGHT, $this->copyright);
        if ($this->isColumnModified(StationPodcastPeer::LINK)) $criteria->add(StationPodcastPeer::LINK, $this->link);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_AUTHOR)) $criteria->add(StationPodcastPeer::ITUNES_AUTHOR, $this->itunes_author);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_KEYWORDS)) $criteria->add(StationPodcastPeer::ITUNES_KEYWORDS, $this->itunes_keywords);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_SUMMARY)) $criteria->add(StationPodcastPeer::ITUNES_SUMMARY, $this->itunes_summary);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_SUBTITLE)) $criteria->add(StationPodcastPeer::ITUNES_SUBTITLE, $this->itunes_subtitle);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_CATEGORY)) $criteria->add(StationPodcastPeer::ITUNES_CATEGORY, $this->itunes_category);
        if ($this->isColumnModified(StationPodcastPeer::ITUNES_EXPLICIT)) $criteria->add(StationPodcastPeer::ITUNES_EXPLICIT, $this->itunes_explicit);
        if ($this->isColumnModified(StationPodcastPeer::OWNER)) $criteria->add(StationPodcastPeer::OWNER, $this->owner);

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
        $criteria = new Criteria(StationPodcastPeer::DATABASE_NAME);
        $criteria->add(StationPodcastPeer::ID, $this->id);

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
     * @param object $copyObj An object of StationPodcast (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbTitle($this->getDbTitle());
        $copyObj->setDbCreator($this->getDbCreator());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbLanguage($this->getDbLanguage());
        $copyObj->setDbCopyright($this->getDbCopyright());
        $copyObj->setDbLink($this->getDbLink());
        $copyObj->setDbItunesAuthor($this->getDbItunesAuthor());
        $copyObj->setDbItunesKeywords($this->getDbItunesKeywords());
        $copyObj->setDbItunesSummary($this->getDbItunesSummary());
        $copyObj->setDbItunesSubtitle($this->getDbItunesSubtitle());
        $copyObj->setDbItunesCategory($this->getDbItunesCategory());
        $copyObj->setDbItunesExplicit($this->getDbItunesExplicit());
        $copyObj->setDbOwner($this->getDbOwner());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            $relObj = $this->getPodcast();
            if ($relObj) {
                $copyObj->setPodcast($relObj->copy($deepCopy));
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
     * @return StationPodcast Clone of current object.
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
     * @return StationPodcastPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new StationPodcastPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Podcast object.
     *
     * @param                  Podcast $v
     * @return StationPodcast The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPodcast(Podcast $v = null)
    {
        if ($v === null) {
            $this->setDbId(NULL);
        } else {
            $this->setDbId($v->getDbId());
        }

        $this->aPodcast = $v;

        // Add binding for other direction of this 1:1 relationship.
        if ($v !== null) {
            $v->setStationPodcast($this);
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
        if ($this->aPodcast === null && ($this->id !== null) && $doQuery) {
            $this->aPodcast = PodcastQuery::create()->findPk($this->id, $con);
            // Because this foreign key represents a one-to-one relationship, we will create a bi-directional association.
            $this->aPodcast->setStationPodcast($this);
        }

        return $this->aPodcast;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return StationPodcast The current object (for fluent API support)
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
            $v->addStationPodcast($this);
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
                $this->aCcSubjs->addStationPodcasts($this);
             */
        }

        return $this->aCcSubjs;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->creator = null;
        $this->description = null;
        $this->language = null;
        $this->copyright = null;
        $this->link = null;
        $this->itunes_author = null;
        $this->itunes_keywords = null;
        $this->itunes_summary = null;
        $this->itunes_subtitle = null;
        $this->itunes_category = null;
        $this->itunes_explicit = null;
        $this->owner = null;
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
            if ($this->aPodcast instanceof Persistent) {
              $this->aPodcast->clearAllReferences($deep);
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aPodcast = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(StationPodcastPeer::DEFAULT_STRING_FORMAT);
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

    // concrete_inheritance behavior

    /**
     * Get or Create the parent Podcast object of the current object
     *
     * @return    Podcast The parent object
     */
    public function getParentOrCreate($con = null)
    {
        if ($this->isNew()) {
            if ($this->isPrimaryKeyNull()) {
                //this prevent issue with deep copy & save parent object
                if (null === ($parent = $this->getPodcast($con))) {
                    $parent = new Podcast();
                }
                $parent->setDescendantClass('StationPodcast');

                return $parent;
            } else {
                $parent = PodcastQuery::create()->findPk($this->getPrimaryKey(), $con);
                if (null === $parent || null !== $parent->getDescendantClass()) {
                    $parent = new Podcast();
                    $parent->setPrimaryKey($this->getPrimaryKey());
                    $parent->setDescendantClass('StationPodcast');
                }

                return $parent;
            }
        }

        return PodcastQuery::create()->findPk($this->getPrimaryKey(), $con);
    }

    /**
     * Create or Update the parent Podcast object
     * And return its primary key
     *
     * @return    int The primary key of the parent object
     */
    public function getSyncParent($con = null)
    {
        $parent = $this->getParentOrCreate($con);
        $parent->setDbTitle($this->getDbTitle());
        $parent->setDbCreator($this->getDbCreator());
        $parent->setDbDescription($this->getDbDescription());
        $parent->setDbLanguage($this->getDbLanguage());
        $parent->setDbCopyright($this->getDbCopyright());
        $parent->setDbLink($this->getDbLink());
        $parent->setDbItunesAuthor($this->getDbItunesAuthor());
        $parent->setDbItunesKeywords($this->getDbItunesKeywords());
        $parent->setDbItunesSummary($this->getDbItunesSummary());
        $parent->setDbItunesSubtitle($this->getDbItunesSubtitle());
        $parent->setDbItunesCategory($this->getDbItunesCategory());
        $parent->setDbItunesExplicit($this->getDbItunesExplicit());
        $parent->setDbOwner($this->getDbOwner());
        if ($this->getCcSubjs() && $this->getCcSubjs()->isNew()) {
            $parent->setCcSubjs($this->getCcSubjs());
        }

        return $parent;
    }

}
