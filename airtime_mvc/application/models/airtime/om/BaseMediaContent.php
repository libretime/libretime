<?php

namespace Airtime\MediaItem\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelException;
use \PropelPDO;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\MediaContent;
use Airtime\MediaItem\MediaContentPeer;
use Airtime\MediaItem\MediaContentQuery;
use Airtime\MediaItem\Playlist;
use Airtime\MediaItem\PlaylistQuery;

/**
 * Base class that represents a row from the 'media_content' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseMediaContent extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\MediaItem\\MediaContentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MediaContentPeer
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
     * The value for the playlist_id field.
     * @var        int
     */
    protected $playlist_id;

    /**
     * The value for the media_id field.
     * @var        int
     */
    protected $media_id;

    /**
     * The value for the position field.
     * @var        int
     */
    protected $position;

    /**
     * The value for the trackoffset field.
     * Note: this column has a database default value of: 0
     * @var        double
     */
    protected $trackoffset;

    /**
     * The value for the cliplength field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $cliplength;

    /**
     * The value for the cuein field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $cuein;

    /**
     * The value for the cueout field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $cueout;

    /**
     * The value for the fadein field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $fadein;

    /**
     * The value for the fadeout field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $fadeout;

    /**
     * @var        Playlist
     */
    protected $aPlaylist;

    /**
     * @var        MediaItem
     */
    protected $aMediaItem;

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
        $this->trackoffset = 0;
        $this->cliplength = '00:00:00';
        $this->cuein = '00:00:00';
        $this->cueout = '00:00:00';
        $this->fadein = '0';
        $this->fadeout = '0';
    }

    /**
     * Initializes internal state of BaseMediaContent object.
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
     * Get the [playlist_id] column value.
     *
     * @return int
     */
    public function getPlaylistId()
    {

        return $this->playlist_id;
    }

    /**
     * Get the [media_id] column value.
     *
     * @return int
     */
    public function getMediaId()
    {

        return $this->media_id;
    }

    /**
     * Get the [position] column value.
     *
     * @return int
     */
    public function getPosition()
    {

        return $this->position;
    }

    /**
     * Get the [trackoffset] column value.
     *
     * @return double
     */
    public function getTrackOffset()
    {

        return $this->trackoffset;
    }

    /**
     * Get the [cliplength] column value.
     *
     * @return string
     */
    public function getCliplength()
    {

        return $this->cliplength;
    }

    /**
     * Get the [cuein] column value.
     *
     * @return string
     */
    public function getCuein()
    {

        return $this->cuein;
    }

    /**
     * Get the [cueout] column value.
     *
     * @return string
     */
    public function getCueout()
    {

        return $this->cueout;
    }

    /**
     * Get the [fadein] column value.
     *
     * @return string
     */
    public function getFadein()
    {

        return $this->fadein;
    }

    /**
     * Get the [fadeout] column value.
     *
     * @return string
     */
    public function getFadeout()
    {

        return $this->fadeout;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = MediaContentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [playlist_id] column.
     *
     * @param  int $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setPlaylistId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->playlist_id !== $v) {
            $this->playlist_id = $v;
            $this->modifiedColumns[] = MediaContentPeer::PLAYLIST_ID;
        }

        if ($this->aPlaylist !== null && $this->aPlaylist->getId() !== $v) {
            $this->aPlaylist = null;
        }


        return $this;
    } // setPlaylistId()

    /**
     * Set the value of [media_id] column.
     *
     * @param  int $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setMediaId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->media_id !== $v) {
            $this->media_id = $v;
            $this->modifiedColumns[] = MediaContentPeer::MEDIA_ID;
        }

        if ($this->aMediaItem !== null && $this->aMediaItem->getId() !== $v) {
            $this->aMediaItem = null;
        }


        return $this;
    } // setMediaId()

    /**
     * Set the value of [position] column.
     *
     * @param  int $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setPosition($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[] = MediaContentPeer::POSITION;
        }


        return $this;
    } // setPosition()

    /**
     * Set the value of [trackoffset] column.
     *
     * @param  double $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setTrackOffset($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (double) $v;
        }

        if ($this->trackoffset !== $v) {
            $this->trackoffset = $v;
            $this->modifiedColumns[] = MediaContentPeer::TRACKOFFSET;
        }


        return $this;
    } // setTrackOffset()

    /**
     * Set the value of [cliplength] column.
     *
     * @param  string $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setCliplength($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cliplength !== $v) {
            $this->cliplength = $v;
            $this->modifiedColumns[] = MediaContentPeer::CLIPLENGTH;
        }


        return $this;
    } // setCliplength()

    /**
     * Set the value of [cuein] column.
     *
     * @param  string $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setCuein($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cuein !== $v) {
            $this->cuein = $v;
            $this->modifiedColumns[] = MediaContentPeer::CUEIN;
        }


        return $this;
    } // setCuein()

    /**
     * Set the value of [cueout] column.
     *
     * @param  string $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setCueout($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cueout !== $v) {
            $this->cueout = $v;
            $this->modifiedColumns[] = MediaContentPeer::CUEOUT;
        }


        return $this;
    } // setCueout()

    /**
     * Set the value of [fadein] column.
     *
     * @param  string $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setFadein($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->fadein !== $v) {
            $this->fadein = $v;
            $this->modifiedColumns[] = MediaContentPeer::FADEIN;
        }


        return $this;
    } // setFadein()

    /**
     * Set the value of [fadeout] column.
     *
     * @param  string $v new value
     * @return MediaContent The current object (for fluent API support)
     */
    public function setFadeout($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->fadeout !== $v) {
            $this->fadeout = $v;
            $this->modifiedColumns[] = MediaContentPeer::FADEOUT;
        }


        return $this;
    } // setFadeout()

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
            if ($this->trackoffset !== 0) {
                return false;
            }

            if ($this->cliplength !== '00:00:00') {
                return false;
            }

            if ($this->cuein !== '00:00:00') {
                return false;
            }

            if ($this->cueout !== '00:00:00') {
                return false;
            }

            if ($this->fadein !== '0') {
                return false;
            }

            if ($this->fadeout !== '0') {
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
            $this->playlist_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->media_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->position = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->trackoffset = ($row[$startcol + 4] !== null) ? (double) $row[$startcol + 4] : null;
            $this->cliplength = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->cuein = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->cueout = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->fadein = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->fadeout = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 10; // 10 = MediaContentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating MediaContent object", $e);
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

        if ($this->aPlaylist !== null && $this->playlist_id !== $this->aPlaylist->getId()) {
            $this->aPlaylist = null;
        }
        if ($this->aMediaItem !== null && $this->media_id !== $this->aMediaItem->getId()) {
            $this->aMediaItem = null;
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
            $con = Propel::getConnection(MediaContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = MediaContentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPlaylist = null;
            $this->aMediaItem = null;
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
            $con = Propel::getConnection(MediaContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = MediaContentQuery::create()
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
            $con = Propel::getConnection(MediaContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                MediaContentPeer::addInstanceToPool($this);
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

            if ($this->aPlaylist !== null) {
                if ($this->aPlaylist->isModified() || $this->aPlaylist->isNew()) {
                    $affectedRows += $this->aPlaylist->save($con);
                }
                $this->setPlaylist($this->aPlaylist);
            }

            if ($this->aMediaItem !== null) {
                if ($this->aMediaItem->isModified() || $this->aMediaItem->isNew()) {
                    $affectedRows += $this->aMediaItem->save($con);
                }
                $this->setMediaItem($this->aMediaItem);
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

        $this->modifiedColumns[] = MediaContentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . MediaContentPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('media_content_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(MediaContentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(MediaContentPeer::PLAYLIST_ID)) {
            $modifiedColumns[':p' . $index++]  = '"playlist_id"';
        }
        if ($this->isColumnModified(MediaContentPeer::MEDIA_ID)) {
            $modifiedColumns[':p' . $index++]  = '"media_id"';
        }
        if ($this->isColumnModified(MediaContentPeer::POSITION)) {
            $modifiedColumns[':p' . $index++]  = '"position"';
        }
        if ($this->isColumnModified(MediaContentPeer::TRACKOFFSET)) {
            $modifiedColumns[':p' . $index++]  = '"trackoffset"';
        }
        if ($this->isColumnModified(MediaContentPeer::CLIPLENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"cliplength"';
        }
        if ($this->isColumnModified(MediaContentPeer::CUEIN)) {
            $modifiedColumns[':p' . $index++]  = '"cuein"';
        }
        if ($this->isColumnModified(MediaContentPeer::CUEOUT)) {
            $modifiedColumns[':p' . $index++]  = '"cueout"';
        }
        if ($this->isColumnModified(MediaContentPeer::FADEIN)) {
            $modifiedColumns[':p' . $index++]  = '"fadein"';
        }
        if ($this->isColumnModified(MediaContentPeer::FADEOUT)) {
            $modifiedColumns[':p' . $index++]  = '"fadeout"';
        }

        $sql = sprintf(
            'INSERT INTO "media_content" (%s) VALUES (%s)',
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
                    case '"playlist_id"':
                        $stmt->bindValue($identifier, $this->playlist_id, PDO::PARAM_INT);
                        break;
                    case '"media_id"':
                        $stmt->bindValue($identifier, $this->media_id, PDO::PARAM_INT);
                        break;
                    case '"position"':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
                        break;
                    case '"trackoffset"':
                        $stmt->bindValue($identifier, $this->trackoffset, PDO::PARAM_STR);
                        break;
                    case '"cliplength"':
                        $stmt->bindValue($identifier, $this->cliplength, PDO::PARAM_STR);
                        break;
                    case '"cuein"':
                        $stmt->bindValue($identifier, $this->cuein, PDO::PARAM_STR);
                        break;
                    case '"cueout"':
                        $stmt->bindValue($identifier, $this->cueout, PDO::PARAM_STR);
                        break;
                    case '"fadein"':
                        $stmt->bindValue($identifier, $this->fadein, PDO::PARAM_STR);
                        break;
                    case '"fadeout"':
                        $stmt->bindValue($identifier, $this->fadeout, PDO::PARAM_STR);
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

            if ($this->aPlaylist !== null) {
                if (!$this->aPlaylist->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlaylist->getValidationFailures());
                }
            }

            if ($this->aMediaItem !== null) {
                if (!$this->aMediaItem->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aMediaItem->getValidationFailures());
                }
            }


            if (($retval = MediaContentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = MediaContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPlaylistId();
                break;
            case 2:
                return $this->getMediaId();
                break;
            case 3:
                return $this->getPosition();
                break;
            case 4:
                return $this->getTrackOffset();
                break;
            case 5:
                return $this->getCliplength();
                break;
            case 6:
                return $this->getCuein();
                break;
            case 7:
                return $this->getCueout();
                break;
            case 8:
                return $this->getFadein();
                break;
            case 9:
                return $this->getFadeout();
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
        if (isset($alreadyDumpedObjects['MediaContent'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['MediaContent'][$this->getPrimaryKey()] = true;
        $keys = MediaContentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPlaylistId(),
            $keys[2] => $this->getMediaId(),
            $keys[3] => $this->getPosition(),
            $keys[4] => $this->getTrackOffset(),
            $keys[5] => $this->getCliplength(),
            $keys[6] => $this->getCuein(),
            $keys[7] => $this->getCueout(),
            $keys[8] => $this->getFadein(),
            $keys[9] => $this->getFadeout(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aPlaylist) {
                $result['Playlist'] = $this->aPlaylist->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aMediaItem) {
                $result['MediaItem'] = $this->aMediaItem->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = MediaContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPlaylistId($value);
                break;
            case 2:
                $this->setMediaId($value);
                break;
            case 3:
                $this->setPosition($value);
                break;
            case 4:
                $this->setTrackOffset($value);
                break;
            case 5:
                $this->setCliplength($value);
                break;
            case 6:
                $this->setCuein($value);
                break;
            case 7:
                $this->setCueout($value);
                break;
            case 8:
                $this->setFadein($value);
                break;
            case 9:
                $this->setFadeout($value);
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
        $keys = MediaContentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPlaylistId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setMediaId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPosition($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setTrackOffset($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCliplength($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCuein($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCueout($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setFadein($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setFadeout($arr[$keys[9]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MediaContentPeer::DATABASE_NAME);

        if ($this->isColumnModified(MediaContentPeer::ID)) $criteria->add(MediaContentPeer::ID, $this->id);
        if ($this->isColumnModified(MediaContentPeer::PLAYLIST_ID)) $criteria->add(MediaContentPeer::PLAYLIST_ID, $this->playlist_id);
        if ($this->isColumnModified(MediaContentPeer::MEDIA_ID)) $criteria->add(MediaContentPeer::MEDIA_ID, $this->media_id);
        if ($this->isColumnModified(MediaContentPeer::POSITION)) $criteria->add(MediaContentPeer::POSITION, $this->position);
        if ($this->isColumnModified(MediaContentPeer::TRACKOFFSET)) $criteria->add(MediaContentPeer::TRACKOFFSET, $this->trackoffset);
        if ($this->isColumnModified(MediaContentPeer::CLIPLENGTH)) $criteria->add(MediaContentPeer::CLIPLENGTH, $this->cliplength);
        if ($this->isColumnModified(MediaContentPeer::CUEIN)) $criteria->add(MediaContentPeer::CUEIN, $this->cuein);
        if ($this->isColumnModified(MediaContentPeer::CUEOUT)) $criteria->add(MediaContentPeer::CUEOUT, $this->cueout);
        if ($this->isColumnModified(MediaContentPeer::FADEIN)) $criteria->add(MediaContentPeer::FADEIN, $this->fadein);
        if ($this->isColumnModified(MediaContentPeer::FADEOUT)) $criteria->add(MediaContentPeer::FADEOUT, $this->fadeout);

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
        $criteria = new Criteria(MediaContentPeer::DATABASE_NAME);
        $criteria->add(MediaContentPeer::ID, $this->id);

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
     * @param object $copyObj An object of MediaContent (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPlaylistId($this->getPlaylistId());
        $copyObj->setMediaId($this->getMediaId());
        $copyObj->setPosition($this->getPosition());
        $copyObj->setTrackOffset($this->getTrackOffset());
        $copyObj->setCliplength($this->getCliplength());
        $copyObj->setCuein($this->getCuein());
        $copyObj->setCueout($this->getCueout());
        $copyObj->setFadein($this->getFadein());
        $copyObj->setFadeout($this->getFadeout());

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
     * @return MediaContent Clone of current object.
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
     * @return MediaContentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MediaContentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Playlist object.
     *
     * @param                  Playlist $v
     * @return MediaContent The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlaylist(Playlist $v = null)
    {
        if ($v === null) {
            $this->setPlaylistId(NULL);
        } else {
            $this->setPlaylistId($v->getId());
        }

        $this->aPlaylist = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Playlist object, it will not be re-added.
        if ($v !== null) {
            $v->addMediaContent($this);
        }


        return $this;
    }


    /**
     * Get the associated Playlist object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Playlist The associated Playlist object.
     * @throws PropelException
     */
    public function getPlaylist(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlaylist === null && ($this->playlist_id !== null) && $doQuery) {
            $this->aPlaylist = PlaylistQuery::create()->findPk($this->playlist_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlaylist->addMediaContents($this);
             */
        }

        return $this->aPlaylist;
    }

    /**
     * Declares an association between this object and a MediaItem object.
     *
     * @param                  MediaItem $v
     * @return MediaContent The current object (for fluent API support)
     * @throws PropelException
     */
    public function setMediaItem(MediaItem $v = null)
    {
        if ($v === null) {
            $this->setMediaId(NULL);
        } else {
            $this->setMediaId($v->getId());
        }

        $this->aMediaItem = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the MediaItem object, it will not be re-added.
        if ($v !== null) {
            $v->addMediaContent($this);
        }


        return $this;
    }


    /**
     * Get the associated MediaItem object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return MediaItem The associated MediaItem object.
     * @throws PropelException
     */
    public function getMediaItem(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aMediaItem === null && ($this->media_id !== null) && $doQuery) {
            $this->aMediaItem = MediaItemQuery::create()->findPk($this->media_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aMediaItem->addMediaContents($this);
             */
        }

        return $this->aMediaItem;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->playlist_id = null;
        $this->media_id = null;
        $this->position = null;
        $this->trackoffset = null;
        $this->cliplength = null;
        $this->cuein = null;
        $this->cueout = null;
        $this->fadein = null;
        $this->fadeout = null;
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
            if ($this->aPlaylist instanceof Persistent) {
              $this->aPlaylist->clearAllReferences($deep);
            }
            if ($this->aMediaItem instanceof Persistent) {
              $this->aMediaItem->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aPlaylist = null;
        $this->aMediaItem = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MediaContentPeer::DEFAULT_STRING_FORMAT);
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
