<?php

namespace Airtime\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use \PropelQuery;
use Airtime\CcSchedule;
use Airtime\CcScheduleQuery;
use Airtime\CcShowInstances;
use Airtime\CcShowInstancesQuery;
use Airtime\CcSubjs;
use Airtime\CcSubjsQuery;
use Airtime\MediaItem;
use Airtime\MediaItemPeer;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\MediaContent;
use Airtime\MediaItem\MediaContentQuery;
use Airtime\MediaItem\Playlist;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItem\PlaylistRule;
use Airtime\MediaItem\PlaylistRuleQuery;
use Airtime\MediaItem\Webstream;
use Airtime\MediaItem\WebstreamQuery;

/**
 * Base class that represents a row from the 'media_item' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseMediaItem extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\MediaItemPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MediaItemPeer
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
     * The value for the owner_id field.
     * @var        int
     */
    protected $owner_id;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the last_played field.
     * @var        string
     */
    protected $last_played;

    /**
     * The value for the play_count field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $play_count;

    /**
     * The value for the length field.
     * Note: this column has a database default value of: '00:00:00'
     * @var        string
     */
    protected $length;

    /**
     * The value for the mime field.
     * @var        string
     */
    protected $mime;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * The value for the descendant_class field.
     * @var        string
     */
    protected $descendant_class;

    /**
     * @var        CcSubjs
     */
    protected $aCcSubjs;

    /**
     * @var        PropelObjectCollection|CcShowInstances[] Collection to store aggregation of CcShowInstances objects.
     */
    protected $collCcShowInstancess;
    protected $collCcShowInstancessPartial;

    /**
     * @var        PropelObjectCollection|CcSchedule[] Collection to store aggregation of CcSchedule objects.
     */
    protected $collCcSchedules;
    protected $collCcSchedulesPartial;

    /**
     * @var        PropelObjectCollection|PlaylistRule[] Collection to store aggregation of PlaylistRule objects.
     */
    protected $collPlaylistRules;
    protected $collPlaylistRulesPartial;

    /**
     * @var        PropelObjectCollection|MediaContent[] Collection to store aggregation of MediaContent objects.
     */
    protected $collMediaContents;
    protected $collMediaContentsPartial;

    /**
     * @var        AudioFile one-to-one related AudioFile object
     */
    protected $singleAudioFile;

    /**
     * @var        Webstream one-to-one related Webstream object
     */
    protected $singleWebstream;

    /**
     * @var        Playlist one-to-one related Playlist object
     */
    protected $singlePlaylist;

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
    protected $ccShowInstancessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccSchedulesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $playlistRulesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $mediaContentsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->play_count = 0;
        $this->length = '00:00:00';
    }

    /**
     * Initializes internal state of BaseMediaItem object.
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [owner_id] column value.
     *
     * @return int
     */
    public function getOwnerId()
    {

        return $this->owner_id;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Get the [optionally formatted] temporal [last_played] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastPlayedTime($format = 'Y-m-d H:i:s')
    {
        if ($this->last_played === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->last_played);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->last_played, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [play_count] column value.
     *
     * @return int
     */
    public function getPlayCount()
    {

        return $this->play_count;
    }

    /**
     * Get the [length] column value.
     *
     * @return string
     */
    public function getLength()
    {

        return $this->length;
    }

    /**
     * Get the [mime] column value.
     *
     * @return string
     */
    public function getMime()
    {

        return $this->mime;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->created_at === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->updated_at === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a \DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [descendant_class] column value.
     *
     * @return string
     */
    public function getDescendantClass()
    {

        return $this->descendant_class;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = MediaItemPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = MediaItemPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [owner_id] column.
     *
     * @param  int $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setOwnerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner_id !== $v) {
            $this->owner_id = $v;
            $this->modifiedColumns[] = MediaItemPeer::OWNER_ID;
        }

        if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
            $this->aCcSubjs = null;
        }


        return $this;
    } // setOwnerId()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = MediaItemPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Sets the value of [last_played] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return MediaItem The current object (for fluent API support)
     */
    public function setLastPlayedTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->last_played !== null || $dt !== null) {
            $currentDateAsString = ($this->last_played !== null && $tmpDt = new \DateTime($this->last_played)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_played = $newDateAsString;
                $this->modifiedColumns[] = MediaItemPeer::LAST_PLAYED;
            }
        } // if either are not null


        return $this;
    } // setLastPlayedTime()

    /**
     * Set the value of [play_count] column.
     *
     * @param  int $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setPlayCount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->play_count !== $v) {
            $this->play_count = $v;
            $this->modifiedColumns[] = MediaItemPeer::PLAY_COUNT;
        }


        return $this;
    } // setPlayCount()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setLength($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = MediaItemPeer::LENGTH;
        }


        return $this;
    } // setLength()

    /**
     * Set the value of [mime] column.
     *
     * @param  string $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setMime($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->mime !== $v) {
            $this->mime = $v;
            $this->modifiedColumns[] = MediaItemPeer::MIME;
        }


        return $this;
    } // setMime()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return MediaItem The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new \DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = MediaItemPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return MediaItem The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new \DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = MediaItemPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [descendant_class] column.
     *
     * @param  string $v new value
     * @return MediaItem The current object (for fluent API support)
     */
    public function setDescendantClass($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->descendant_class !== $v) {
            $this->descendant_class = $v;
            $this->modifiedColumns[] = MediaItemPeer::DESCENDANT_CLASS;
        }


        return $this;
    } // setDescendantClass()

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
            if ($this->play_count !== 0) {
                return false;
            }

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
            $this->owner_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->description = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->last_played = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->play_count = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->length = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->mime = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->descendant_class = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = MediaItemPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating MediaItem object", $e);
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

        if ($this->aCcSubjs !== null && $this->owner_id !== $this->aCcSubjs->getDbId()) {
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
            $con = Propel::getConnection(MediaItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = MediaItemPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcSubjs = null;
            $this->collCcShowInstancess = null;

            $this->collCcSchedules = null;

            $this->collPlaylistRules = null;

            $this->collMediaContents = null;

            $this->singleAudioFile = null;

            $this->singleWebstream = null;

            $this->singlePlaylist = null;

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
            $con = Propel::getConnection(MediaItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = MediaItemQuery::create()
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
            $con = Propel::getConnection(MediaItemPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(MediaItemPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(MediaItemPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(MediaItemPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                MediaItemPeer::addInstanceToPool($this);
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

            if ($this->ccShowInstancessScheduledForDeletion !== null) {
                if (!$this->ccShowInstancessScheduledForDeletion->isEmpty()) {
                    CcShowInstancesQuery::create()
                        ->filterByPrimaryKeys($this->ccShowInstancessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowInstancessScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowInstancess !== null) {
                foreach ($this->collCcShowInstancess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

            if ($this->playlistRulesScheduledForDeletion !== null) {
                if (!$this->playlistRulesScheduledForDeletion->isEmpty()) {
                    PlaylistRuleQuery::create()
                        ->filterByPrimaryKeys($this->playlistRulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->playlistRulesScheduledForDeletion = null;
                }
            }

            if ($this->collPlaylistRules !== null) {
                foreach ($this->collPlaylistRules as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->mediaContentsScheduledForDeletion !== null) {
                if (!$this->mediaContentsScheduledForDeletion->isEmpty()) {
                    MediaContentQuery::create()
                        ->filterByPrimaryKeys($this->mediaContentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->mediaContentsScheduledForDeletion = null;
                }
            }

            if ($this->collMediaContents !== null) {
                foreach ($this->collMediaContents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleAudioFile !== null) {
                if (!$this->singleAudioFile->isDeleted() && ($this->singleAudioFile->isNew() || $this->singleAudioFile->isModified())) {
                        $affectedRows += $this->singleAudioFile->save($con);
                }
            }

            if ($this->singleWebstream !== null) {
                if (!$this->singleWebstream->isDeleted() && ($this->singleWebstream->isNew() || $this->singleWebstream->isModified())) {
                        $affectedRows += $this->singleWebstream->save($con);
                }
            }

            if ($this->singlePlaylist !== null) {
                if (!$this->singlePlaylist->isDeleted() && ($this->singlePlaylist->isNew() || $this->singlePlaylist->isModified())) {
                        $affectedRows += $this->singlePlaylist->save($con);
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

        $this->modifiedColumns[] = MediaItemPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . MediaItemPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('media_item_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(MediaItemPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(MediaItemPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(MediaItemPeer::OWNER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"owner_id"';
        }
        if ($this->isColumnModified(MediaItemPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(MediaItemPeer::LAST_PLAYED)) {
            $modifiedColumns[':p' . $index++]  = '"last_played"';
        }
        if ($this->isColumnModified(MediaItemPeer::PLAY_COUNT)) {
            $modifiedColumns[':p' . $index++]  = '"play_count"';
        }
        if ($this->isColumnModified(MediaItemPeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(MediaItemPeer::MIME)) {
            $modifiedColumns[':p' . $index++]  = '"mime"';
        }
        if ($this->isColumnModified(MediaItemPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"created_at"';
        }
        if ($this->isColumnModified(MediaItemPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"updated_at"';
        }
        if ($this->isColumnModified(MediaItemPeer::DESCENDANT_CLASS)) {
            $modifiedColumns[':p' . $index++]  = '"descendant_class"';
        }

        $sql = sprintf(
            'INSERT INTO "media_item" (%s) VALUES (%s)',
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
                    case '"owner_id"':
                        $stmt->bindValue($identifier, $this->owner_id, PDO::PARAM_INT);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"last_played"':
                        $stmt->bindValue($identifier, $this->last_played, PDO::PARAM_STR);
                        break;
                    case '"play_count"':
                        $stmt->bindValue($identifier, $this->play_count, PDO::PARAM_INT);
                        break;
                    case '"length"':
                        $stmt->bindValue($identifier, $this->length, PDO::PARAM_STR);
                        break;
                    case '"mime"':
                        $stmt->bindValue($identifier, $this->mime, PDO::PARAM_STR);
                        break;
                    case '"created_at"':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '"updated_at"':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                    case '"descendant_class"':
                        $stmt->bindValue($identifier, $this->descendant_class, PDO::PARAM_STR);
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


            if (($retval = MediaItemPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcShowInstancess !== null) {
                    foreach ($this->collCcShowInstancess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcSchedules !== null) {
                    foreach ($this->collCcSchedules as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPlaylistRules !== null) {
                    foreach ($this->collPlaylistRules as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collMediaContents !== null) {
                    foreach ($this->collMediaContents as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->singleAudioFile !== null) {
                    if (!$this->singleAudioFile->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleAudioFile->getValidationFailures());
                    }
                }

                if ($this->singleWebstream !== null) {
                    if (!$this->singleWebstream->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleWebstream->getValidationFailures());
                    }
                }

                if ($this->singlePlaylist !== null) {
                    if (!$this->singlePlaylist->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singlePlaylist->getValidationFailures());
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
        $pos = MediaItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getOwnerId();
                break;
            case 3:
                return $this->getDescription();
                break;
            case 4:
                return $this->getLastPlayedTime();
                break;
            case 5:
                return $this->getPlayCount();
                break;
            case 6:
                return $this->getLength();
                break;
            case 7:
                return $this->getMime();
                break;
            case 8:
                return $this->getCreatedAt();
                break;
            case 9:
                return $this->getUpdatedAt();
                break;
            case 10:
                return $this->getDescendantClass();
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
        if (isset($alreadyDumpedObjects['MediaItem'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['MediaItem'][$this->getPrimaryKey()] = true;
        $keys = MediaItemPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getOwnerId(),
            $keys[3] => $this->getDescription(),
            $keys[4] => $this->getLastPlayedTime(),
            $keys[5] => $this->getPlayCount(),
            $keys[6] => $this->getLength(),
            $keys[7] => $this->getMime(),
            $keys[8] => $this->getCreatedAt(),
            $keys[9] => $this->getUpdatedAt(),
            $keys[10] => $this->getDescendantClass(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcSubjs) {
                $result['CcSubjs'] = $this->aCcSubjs->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcShowInstancess) {
                $result['CcShowInstancess'] = $this->collCcShowInstancess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcSchedules) {
                $result['CcSchedules'] = $this->collCcSchedules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPlaylistRules) {
                $result['PlaylistRules'] = $this->collPlaylistRules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMediaContents) {
                $result['MediaContents'] = $this->collMediaContents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleAudioFile) {
                $result['AudioFile'] = $this->singleAudioFile->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->singleWebstream) {
                $result['Webstream'] = $this->singleWebstream->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->singlePlaylist) {
                $result['Playlist'] = $this->singlePlaylist->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
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
        $pos = MediaItemPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setName($value);
                break;
            case 2:
                $this->setOwnerId($value);
                break;
            case 3:
                $this->setDescription($value);
                break;
            case 4:
                $this->setLastPlayedTime($value);
                break;
            case 5:
                $this->setPlayCount($value);
                break;
            case 6:
                $this->setLength($value);
                break;
            case 7:
                $this->setMime($value);
                break;
            case 8:
                $this->setCreatedAt($value);
                break;
            case 9:
                $this->setUpdatedAt($value);
                break;
            case 10:
                $this->setDescendantClass($value);
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
        $keys = MediaItemPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setOwnerId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDescription($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setLastPlayedTime($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPlayCount($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setLength($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setMime($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDescendantClass($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MediaItemPeer::DATABASE_NAME);

        if ($this->isColumnModified(MediaItemPeer::ID)) $criteria->add(MediaItemPeer::ID, $this->id);
        if ($this->isColumnModified(MediaItemPeer::NAME)) $criteria->add(MediaItemPeer::NAME, $this->name);
        if ($this->isColumnModified(MediaItemPeer::OWNER_ID)) $criteria->add(MediaItemPeer::OWNER_ID, $this->owner_id);
        if ($this->isColumnModified(MediaItemPeer::DESCRIPTION)) $criteria->add(MediaItemPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(MediaItemPeer::LAST_PLAYED)) $criteria->add(MediaItemPeer::LAST_PLAYED, $this->last_played);
        if ($this->isColumnModified(MediaItemPeer::PLAY_COUNT)) $criteria->add(MediaItemPeer::PLAY_COUNT, $this->play_count);
        if ($this->isColumnModified(MediaItemPeer::LENGTH)) $criteria->add(MediaItemPeer::LENGTH, $this->length);
        if ($this->isColumnModified(MediaItemPeer::MIME)) $criteria->add(MediaItemPeer::MIME, $this->mime);
        if ($this->isColumnModified(MediaItemPeer::CREATED_AT)) $criteria->add(MediaItemPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(MediaItemPeer::UPDATED_AT)) $criteria->add(MediaItemPeer::UPDATED_AT, $this->updated_at);
        if ($this->isColumnModified(MediaItemPeer::DESCENDANT_CLASS)) $criteria->add(MediaItemPeer::DESCENDANT_CLASS, $this->descendant_class);

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
        $criteria = new Criteria(MediaItemPeer::DATABASE_NAME);
        $criteria->add(MediaItemPeer::ID, $this->id);

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
     * @param object $copyObj An object of MediaItem (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setOwnerId($this->getOwnerId());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setLastPlayedTime($this->getLastPlayedTime());
        $copyObj->setPlayCount($this->getPlayCount());
        $copyObj->setLength($this->getLength());
        $copyObj->setMime($this->getMime());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setDescendantClass($this->getDescendantClass());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcShowInstancess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowInstances($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcSchedules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcSchedule($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPlaylistRules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPlaylistRule($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMediaContents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMediaContent($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getAudioFile();
            if ($relObj) {
                $copyObj->setAudioFile($relObj->copy($deepCopy));
            }

            $relObj = $this->getWebstream();
            if ($relObj) {
                $copyObj->setWebstream($relObj->copy($deepCopy));
            }

            $relObj = $this->getPlaylist();
            if ($relObj) {
                $copyObj->setPlaylist($relObj->copy($deepCopy));
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
     * @return MediaItem Clone of current object.
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
     * @return MediaItemPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MediaItemPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return MediaItem The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcSubjs(CcSubjs $v = null)
    {
        if ($v === null) {
            $this->setOwnerId(NULL);
        } else {
            $this->setOwnerId($v->getDbId());
        }

        $this->aCcSubjs = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcSubjs object, it will not be re-added.
        if ($v !== null) {
            $v->addMediaItem($this);
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
        if ($this->aCcSubjs === null && ($this->owner_id !== null) && $doQuery) {
            $this->aCcSubjs = CcSubjsQuery::create()->findPk($this->owner_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcSubjs->addMediaItems($this);
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
        if ('CcShowInstances' == $relationName) {
            $this->initCcShowInstancess();
        }
        if ('CcSchedule' == $relationName) {
            $this->initCcSchedules();
        }
        if ('PlaylistRule' == $relationName) {
            $this->initPlaylistRules();
        }
        if ('MediaContent' == $relationName) {
            $this->initMediaContents();
        }
    }

    /**
     * Clears out the collCcShowInstancess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return MediaItem The current object (for fluent API support)
     * @see        addCcShowInstancess()
     */
    public function clearCcShowInstancess()
    {
        $this->collCcShowInstancess = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowInstancessPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowInstancess collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowInstancess($v = true)
    {
        $this->collCcShowInstancessPartial = $v;
    }

    /**
     * Initializes the collCcShowInstancess collection.
     *
     * By default this just sets the collCcShowInstancess collection to an empty array (like clearcollCcShowInstancess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowInstancess($overrideExisting = true)
    {
        if (null !== $this->collCcShowInstancess && !$overrideExisting) {
            return;
        }
        $this->collCcShowInstancess = new PropelObjectCollection();
        $this->collCcShowInstancess->setModel('CcShowInstances');
    }

    /**
     * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this MediaItem is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     * @throws PropelException
     */
    public function getCcShowInstancess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessPartial && !$this->isNew();
        if (null === $this->collCcShowInstancess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                // return empty collection
                $this->initCcShowInstancess();
            } else {
                $collCcShowInstancess = CcShowInstancesQuery::create(null, $criteria)
                    ->filterByMediaItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowInstancessPartial && count($collCcShowInstancess)) {
                      $this->initCcShowInstancess(false);

                      foreach ($collCcShowInstancess as $obj) {
                        if (false == $this->collCcShowInstancess->contains($obj)) {
                          $this->collCcShowInstancess->append($obj);
                        }
                      }

                      $this->collCcShowInstancessPartial = true;
                    }

                    $collCcShowInstancess->getInternalIterator()->rewind();

                    return $collCcShowInstancess;
                }

                if ($partial && $this->collCcShowInstancess) {
                    foreach ($this->collCcShowInstancess as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowInstancess[] = $obj;
                        }
                    }
                }

                $this->collCcShowInstancess = $collCcShowInstancess;
                $this->collCcShowInstancessPartial = false;
            }
        }

        return $this->collCcShowInstancess;
    }

    /**
     * Sets a collection of CcShowInstances objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowInstancess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return MediaItem The current object (for fluent API support)
     */
    public function setCcShowInstancess(PropelCollection $ccShowInstancess, PropelPDO $con = null)
    {
        $ccShowInstancessToDelete = $this->getCcShowInstancess(new Criteria(), $con)->diff($ccShowInstancess);


        $this->ccShowInstancessScheduledForDeletion = $ccShowInstancessToDelete;

        foreach ($ccShowInstancessToDelete as $ccShowInstancesRemoved) {
            $ccShowInstancesRemoved->setMediaItem(null);
        }

        $this->collCcShowInstancess = null;
        foreach ($ccShowInstancess as $ccShowInstances) {
            $this->addCcShowInstances($ccShowInstances);
        }

        $this->collCcShowInstancess = $ccShowInstancess;
        $this->collCcShowInstancessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowInstances objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowInstances objects.
     * @throws PropelException
     */
    public function countCcShowInstancess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowInstancessPartial && !$this->isNew();
        if (null === $this->collCcShowInstancess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowInstancess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowInstancess());
            }
            $query = CcShowInstancesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMediaItem($this)
                ->count($con);
        }

        return count($this->collCcShowInstancess);
    }

    /**
     * Method called to associate a CcShowInstances object to this object
     * through the CcShowInstances foreign key attribute.
     *
     * @param    CcShowInstances $l CcShowInstances
     * @return MediaItem The current object (for fluent API support)
     */
    public function addCcShowInstances(CcShowInstances $l)
    {
        if ($this->collCcShowInstancess === null) {
            $this->initCcShowInstancess();
            $this->collCcShowInstancessPartial = true;
        }

        if (!in_array($l, $this->collCcShowInstancess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowInstances($l);

            if ($this->ccShowInstancessScheduledForDeletion and $this->ccShowInstancessScheduledForDeletion->contains($l)) {
                $this->ccShowInstancessScheduledForDeletion->remove($this->ccShowInstancessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowInstances $ccShowInstances The ccShowInstances object to add.
     */
    protected function doAddCcShowInstances($ccShowInstances)
    {
        $this->collCcShowInstancess[]= $ccShowInstances;
        $ccShowInstances->setMediaItem($this);
    }

    /**
     * @param	CcShowInstances $ccShowInstances The ccShowInstances object to remove.
     * @return MediaItem The current object (for fluent API support)
     */
    public function removeCcShowInstances($ccShowInstances)
    {
        if ($this->getCcShowInstancess()->contains($ccShowInstances)) {
            $this->collCcShowInstancess->remove($this->collCcShowInstancess->search($ccShowInstances));
            if (null === $this->ccShowInstancessScheduledForDeletion) {
                $this->ccShowInstancessScheduledForDeletion = clone $this->collCcShowInstancess;
                $this->ccShowInstancessScheduledForDeletion->clear();
            }
            $this->ccShowInstancessScheduledForDeletion[]= $ccShowInstances;
            $ccShowInstances->setMediaItem(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessJoinCcShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcShow', $join_behavior);

        return $this->getCcShowInstancess($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessJoinCcShowInstancesRelatedByDbOriginalShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcShowInstancesRelatedByDbOriginalShow', $join_behavior);

        return $this->getCcShowInstancess($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowInstances[] List of CcShowInstances objects
     */
    public function getCcShowInstancessJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowInstancesQuery::create(null, $criteria);
        $query->joinWith('CcFiles', $join_behavior);

        return $this->getCcShowInstancess($query, $con);
    }

    /**
     * Clears out the collCcSchedules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return MediaItem The current object (for fluent API support)
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
     * If this MediaItem is new, it will return
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
                    ->filterByMediaItem($this)
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
     * @return MediaItem The current object (for fluent API support)
     */
    public function setCcSchedules(PropelCollection $ccSchedules, PropelPDO $con = null)
    {
        $ccSchedulesToDelete = $this->getCcSchedules(new Criteria(), $con)->diff($ccSchedules);


        $this->ccSchedulesScheduledForDeletion = $ccSchedulesToDelete;

        foreach ($ccSchedulesToDelete as $ccScheduleRemoved) {
            $ccScheduleRemoved->setMediaItem(null);
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
                ->filterByMediaItem($this)
                ->count($con);
        }

        return count($this->collCcSchedules);
    }

    /**
     * Method called to associate a CcSchedule object to this object
     * through the CcSchedule foreign key attribute.
     *
     * @param    CcSchedule $l CcSchedule
     * @return MediaItem The current object (for fluent API support)
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
        $ccSchedule->setMediaItem($this);
    }

    /**
     * @param	CcSchedule $ccSchedule The ccSchedule object to remove.
     * @return MediaItem The current object (for fluent API support)
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
            $ccSchedule->setMediaItem(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
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
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
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
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related CcSchedules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcSchedule[] List of CcSchedule objects
     */
    public function getCcSchedulesJoinCcWebstream($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcScheduleQuery::create(null, $criteria);
        $query->joinWith('CcWebstream', $join_behavior);

        return $this->getCcSchedules($query, $con);
    }

    /**
     * Clears out the collPlaylistRules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return MediaItem The current object (for fluent API support)
     * @see        addPlaylistRules()
     */
    public function clearPlaylistRules()
    {
        $this->collPlaylistRules = null; // important to set this to null since that means it is uninitialized
        $this->collPlaylistRulesPartial = null;

        return $this;
    }

    /**
     * reset is the collPlaylistRules collection loaded partially
     *
     * @return void
     */
    public function resetPartialPlaylistRules($v = true)
    {
        $this->collPlaylistRulesPartial = $v;
    }

    /**
     * Initializes the collPlaylistRules collection.
     *
     * By default this just sets the collPlaylistRules collection to an empty array (like clearcollPlaylistRules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPlaylistRules($overrideExisting = true)
    {
        if (null !== $this->collPlaylistRules && !$overrideExisting) {
            return;
        }
        $this->collPlaylistRules = new PropelObjectCollection();
        $this->collPlaylistRules->setModel('PlaylistRule');
    }

    /**
     * Gets an array of PlaylistRule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this MediaItem is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PlaylistRule[] List of PlaylistRule objects
     * @throws PropelException
     */
    public function getPlaylistRules($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPlaylistRulesPartial && !$this->isNew();
        if (null === $this->collPlaylistRules || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPlaylistRules) {
                // return empty collection
                $this->initPlaylistRules();
            } else {
                $collPlaylistRules = PlaylistRuleQuery::create(null, $criteria)
                    ->filterByMediaItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPlaylistRulesPartial && count($collPlaylistRules)) {
                      $this->initPlaylistRules(false);

                      foreach ($collPlaylistRules as $obj) {
                        if (false == $this->collPlaylistRules->contains($obj)) {
                          $this->collPlaylistRules->append($obj);
                        }
                      }

                      $this->collPlaylistRulesPartial = true;
                    }

                    $collPlaylistRules->getInternalIterator()->rewind();

                    return $collPlaylistRules;
                }

                if ($partial && $this->collPlaylistRules) {
                    foreach ($this->collPlaylistRules as $obj) {
                        if ($obj->isNew()) {
                            $collPlaylistRules[] = $obj;
                        }
                    }
                }

                $this->collPlaylistRules = $collPlaylistRules;
                $this->collPlaylistRulesPartial = false;
            }
        }

        return $this->collPlaylistRules;
    }

    /**
     * Sets a collection of PlaylistRule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $playlistRules A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return MediaItem The current object (for fluent API support)
     */
    public function setPlaylistRules(PropelCollection $playlistRules, PropelPDO $con = null)
    {
        $playlistRulesToDelete = $this->getPlaylistRules(new Criteria(), $con)->diff($playlistRules);


        $this->playlistRulesScheduledForDeletion = $playlistRulesToDelete;

        foreach ($playlistRulesToDelete as $playlistRuleRemoved) {
            $playlistRuleRemoved->setMediaItem(null);
        }

        $this->collPlaylistRules = null;
        foreach ($playlistRules as $playlistRule) {
            $this->addPlaylistRule($playlistRule);
        }

        $this->collPlaylistRules = $playlistRules;
        $this->collPlaylistRulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PlaylistRule objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PlaylistRule objects.
     * @throws PropelException
     */
    public function countPlaylistRules(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPlaylistRulesPartial && !$this->isNew();
        if (null === $this->collPlaylistRules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPlaylistRules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPlaylistRules());
            }
            $query = PlaylistRuleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMediaItem($this)
                ->count($con);
        }

        return count($this->collPlaylistRules);
    }

    /**
     * Method called to associate a PlaylistRule object to this object
     * through the PlaylistRule foreign key attribute.
     *
     * @param    PlaylistRule $l PlaylistRule
     * @return MediaItem The current object (for fluent API support)
     */
    public function addPlaylistRule(PlaylistRule $l)
    {
        if ($this->collPlaylistRules === null) {
            $this->initPlaylistRules();
            $this->collPlaylistRulesPartial = true;
        }

        if (!in_array($l, $this->collPlaylistRules->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPlaylistRule($l);

            if ($this->playlistRulesScheduledForDeletion and $this->playlistRulesScheduledForDeletion->contains($l)) {
                $this->playlistRulesScheduledForDeletion->remove($this->playlistRulesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PlaylistRule $playlistRule The playlistRule object to add.
     */
    protected function doAddPlaylistRule($playlistRule)
    {
        $this->collPlaylistRules[]= $playlistRule;
        $playlistRule->setMediaItem($this);
    }

    /**
     * @param	PlaylistRule $playlistRule The playlistRule object to remove.
     * @return MediaItem The current object (for fluent API support)
     */
    public function removePlaylistRule($playlistRule)
    {
        if ($this->getPlaylistRules()->contains($playlistRule)) {
            $this->collPlaylistRules->remove($this->collPlaylistRules->search($playlistRule));
            if (null === $this->playlistRulesScheduledForDeletion) {
                $this->playlistRulesScheduledForDeletion = clone $this->collPlaylistRules;
                $this->playlistRulesScheduledForDeletion->clear();
            }
            $this->playlistRulesScheduledForDeletion[]= clone $playlistRule;
            $playlistRule->setMediaItem(null);
        }

        return $this;
    }

    /**
     * Clears out the collMediaContents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return MediaItem The current object (for fluent API support)
     * @see        addMediaContents()
     */
    public function clearMediaContents()
    {
        $this->collMediaContents = null; // important to set this to null since that means it is uninitialized
        $this->collMediaContentsPartial = null;

        return $this;
    }

    /**
     * reset is the collMediaContents collection loaded partially
     *
     * @return void
     */
    public function resetPartialMediaContents($v = true)
    {
        $this->collMediaContentsPartial = $v;
    }

    /**
     * Initializes the collMediaContents collection.
     *
     * By default this just sets the collMediaContents collection to an empty array (like clearcollMediaContents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMediaContents($overrideExisting = true)
    {
        if (null !== $this->collMediaContents && !$overrideExisting) {
            return;
        }
        $this->collMediaContents = new PropelObjectCollection();
        $this->collMediaContents->setModel('MediaContent');
    }

    /**
     * Gets an array of MediaContent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this MediaItem is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|MediaContent[] List of MediaContent objects
     * @throws PropelException
     */
    public function getMediaContents($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collMediaContentsPartial && !$this->isNew();
        if (null === $this->collMediaContents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMediaContents) {
                // return empty collection
                $this->initMediaContents();
            } else {
                $collMediaContents = MediaContentQuery::create(null, $criteria)
                    ->filterByMediaItem($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collMediaContentsPartial && count($collMediaContents)) {
                      $this->initMediaContents(false);

                      foreach ($collMediaContents as $obj) {
                        if (false == $this->collMediaContents->contains($obj)) {
                          $this->collMediaContents->append($obj);
                        }
                      }

                      $this->collMediaContentsPartial = true;
                    }

                    $collMediaContents->getInternalIterator()->rewind();

                    return $collMediaContents;
                }

                if ($partial && $this->collMediaContents) {
                    foreach ($this->collMediaContents as $obj) {
                        if ($obj->isNew()) {
                            $collMediaContents[] = $obj;
                        }
                    }
                }

                $this->collMediaContents = $collMediaContents;
                $this->collMediaContentsPartial = false;
            }
        }

        return $this->collMediaContents;
    }

    /**
     * Sets a collection of MediaContent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $mediaContents A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return MediaItem The current object (for fluent API support)
     */
    public function setMediaContents(PropelCollection $mediaContents, PropelPDO $con = null)
    {
        $mediaContentsToDelete = $this->getMediaContents(new Criteria(), $con)->diff($mediaContents);


        $this->mediaContentsScheduledForDeletion = $mediaContentsToDelete;

        foreach ($mediaContentsToDelete as $mediaContentRemoved) {
            $mediaContentRemoved->setMediaItem(null);
        }

        $this->collMediaContents = null;
        foreach ($mediaContents as $mediaContent) {
            $this->addMediaContent($mediaContent);
        }

        $this->collMediaContents = $mediaContents;
        $this->collMediaContentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MediaContent objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related MediaContent objects.
     * @throws PropelException
     */
    public function countMediaContents(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collMediaContentsPartial && !$this->isNew();
        if (null === $this->collMediaContents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMediaContents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMediaContents());
            }
            $query = MediaContentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMediaItem($this)
                ->count($con);
        }

        return count($this->collMediaContents);
    }

    /**
     * Method called to associate a MediaContent object to this object
     * through the MediaContent foreign key attribute.
     *
     * @param    MediaContent $l MediaContent
     * @return MediaItem The current object (for fluent API support)
     */
    public function addMediaContent(MediaContent $l)
    {
        if ($this->collMediaContents === null) {
            $this->initMediaContents();
            $this->collMediaContentsPartial = true;
        }

        if (!in_array($l, $this->collMediaContents->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMediaContent($l);

            if ($this->mediaContentsScheduledForDeletion and $this->mediaContentsScheduledForDeletion->contains($l)) {
                $this->mediaContentsScheduledForDeletion->remove($this->mediaContentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	MediaContent $mediaContent The mediaContent object to add.
     */
    protected function doAddMediaContent($mediaContent)
    {
        $this->collMediaContents[]= $mediaContent;
        $mediaContent->setMediaItem($this);
    }

    /**
     * @param	MediaContent $mediaContent The mediaContent object to remove.
     * @return MediaItem The current object (for fluent API support)
     */
    public function removeMediaContent($mediaContent)
    {
        if ($this->getMediaContents()->contains($mediaContent)) {
            $this->collMediaContents->remove($this->collMediaContents->search($mediaContent));
            if (null === $this->mediaContentsScheduledForDeletion) {
                $this->mediaContentsScheduledForDeletion = clone $this->collMediaContents;
                $this->mediaContentsScheduledForDeletion->clear();
            }
            $this->mediaContentsScheduledForDeletion[]= $mediaContent;
            $mediaContent->setMediaItem(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this MediaItem is new, it will return
     * an empty collection; or if this MediaItem has previously
     * been saved, it will retrieve related MediaContents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in MediaItem.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|MediaContent[] List of MediaContent objects
     */
    public function getMediaContentsJoinPlaylist($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = MediaContentQuery::create(null, $criteria);
        $query->joinWith('Playlist', $join_behavior);

        return $this->getMediaContents($query, $con);
    }

    /**
     * Gets a single AudioFile object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return AudioFile
     * @throws PropelException
     */
    public function getAudioFile(PropelPDO $con = null)
    {

        if ($this->singleAudioFile === null && !$this->isNew()) {
            $this->singleAudioFile = AudioFileQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleAudioFile;
    }

    /**
     * Sets a single AudioFile object as related to this object by a one-to-one relationship.
     *
     * @param                  AudioFile $v AudioFile
     * @return MediaItem The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAudioFile(AudioFile $v = null)
    {
        $this->singleAudioFile = $v;

        // Make sure that that the passed-in AudioFile isn't already associated with this object
        if ($v !== null && $v->getMediaItem(null, false) === null) {
            $v->setMediaItem($this);
        }

        return $this;
    }

    /**
     * Gets a single Webstream object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return Webstream
     * @throws PropelException
     */
    public function getWebstream(PropelPDO $con = null)
    {

        if ($this->singleWebstream === null && !$this->isNew()) {
            $this->singleWebstream = WebstreamQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleWebstream;
    }

    /**
     * Sets a single Webstream object as related to this object by a one-to-one relationship.
     *
     * @param                  Webstream $v Webstream
     * @return MediaItem The current object (for fluent API support)
     * @throws PropelException
     */
    public function setWebstream(Webstream $v = null)
    {
        $this->singleWebstream = $v;

        // Make sure that that the passed-in Webstream isn't already associated with this object
        if ($v !== null && $v->getMediaItem(null, false) === null) {
            $v->setMediaItem($this);
        }

        return $this;
    }

    /**
     * Gets a single Playlist object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return Playlist
     * @throws PropelException
     */
    public function getPlaylist(PropelPDO $con = null)
    {

        if ($this->singlePlaylist === null && !$this->isNew()) {
            $this->singlePlaylist = PlaylistQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singlePlaylist;
    }

    /**
     * Sets a single Playlist object as related to this object by a one-to-one relationship.
     *
     * @param                  Playlist $v Playlist
     * @return MediaItem The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlaylist(Playlist $v = null)
    {
        $this->singlePlaylist = $v;

        // Make sure that that the passed-in Playlist isn't already associated with this object
        if ($v !== null && $v->getMediaItem(null, false) === null) {
            $v->setMediaItem($this);
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
        $this->owner_id = null;
        $this->description = null;
        $this->last_played = null;
        $this->play_count = null;
        $this->length = null;
        $this->mime = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->descendant_class = null;
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
            if ($this->collCcShowInstancess) {
                foreach ($this->collCcShowInstancess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcSchedules) {
                foreach ($this->collCcSchedules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPlaylistRules) {
                foreach ($this->collPlaylistRules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMediaContents) {
                foreach ($this->collMediaContents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleAudioFile) {
                $this->singleAudioFile->clearAllReferences($deep);
            }
            if ($this->singleWebstream) {
                $this->singleWebstream->clearAllReferences($deep);
            }
            if ($this->singlePlaylist) {
                $this->singlePlaylist->clearAllReferences($deep);
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcShowInstancess instanceof PropelCollection) {
            $this->collCcShowInstancess->clearIterator();
        }
        $this->collCcShowInstancess = null;
        if ($this->collCcSchedules instanceof PropelCollection) {
            $this->collCcSchedules->clearIterator();
        }
        $this->collCcSchedules = null;
        if ($this->collPlaylistRules instanceof PropelCollection) {
            $this->collPlaylistRules->clearIterator();
        }
        $this->collPlaylistRules = null;
        if ($this->collMediaContents instanceof PropelCollection) {
            $this->collMediaContents->clearIterator();
        }
        $this->collMediaContents = null;
        if ($this->singleAudioFile instanceof PropelCollection) {
            $this->singleAudioFile->clearIterator();
        }
        $this->singleAudioFile = null;
        if ($this->singleWebstream instanceof PropelCollection) {
            $this->singleWebstream->clearIterator();
        }
        $this->singleWebstream = null;
        if ($this->singlePlaylist instanceof PropelCollection) {
            $this->singlePlaylist->clearIterator();
        }
        $this->singlePlaylist = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MediaItemPeer::DEFAULT_STRING_FORMAT);
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

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     MediaItem The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = MediaItemPeer::UPDATED_AT;

        return $this;
    }

    // concrete_inheritance_parent behavior

    /**
     * Whether or not this object is the parent of a child object
     *
     * @return    bool
     */
    public function hasChildObject()
    {
        return $this->getDescendantClass() !== null;
    }

    /**
     * Get the child object of this object
     *
     * @return    mixed
     */
    public function getChildObject()
    {
        if (!$this->hasChildObject()) {
            return null;
        }
        $childObjectClass = $this->getDescendantClass();
        $childObject = PropelQuery::from($childObjectClass)->findPk($this->getPrimaryKey());

        return $childObject->hasChildObject() ? $childObject->getChildObject() : $childObject;
    }

}
