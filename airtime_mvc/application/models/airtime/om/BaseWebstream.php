<?php

namespace Airtime\MediaItem\om;

use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use Airtime\CcSubjs;
use Airtime\CcSubjsQuery;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\Webstream;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\WebstreamQuery;

/**
 * Base class that represents a row from the 'media_webstream' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseWebstream extends MediaItem implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\MediaItem\\WebstreamPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        WebstreamPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the url field.
     * @var        string
     */
    protected $url;

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
     * @var        MediaItem
     */
    protected $aMediaItem;

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
     * Initializes internal state of BaseWebstream object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [url] column value.
     *
     * @return string
     */
    public function getUrl()
    {

        return $this->url;
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
     * Set the value of [url] column.
     *
     * @param  string $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = WebstreamPeer::URL;
        }


        return $this;
    } // setUrl()

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = WebstreamPeer::ID;
        }

        if ($this->aMediaItem !== null && $this->aMediaItem->getId() !== $v) {
            $this->aMediaItem = null;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = WebstreamPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [owner_id] column.
     *
     * @param  int $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setOwnerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->owner_id !== $v) {
            $this->owner_id = $v;
            $this->modifiedColumns[] = WebstreamPeer::OWNER_ID;
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
     * @return Webstream The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = WebstreamPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Sets the value of [last_played] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Webstream The current object (for fluent API support)
     */
    public function setLastPlayedTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->last_played !== null || $dt !== null) {
            $currentDateAsString = ($this->last_played !== null && $tmpDt = new \DateTime($this->last_played)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_played = $newDateAsString;
                $this->modifiedColumns[] = WebstreamPeer::LAST_PLAYED;
            }
        } // if either are not null


        return $this;
    } // setLastPlayedTime()

    /**
     * Set the value of [play_count] column.
     *
     * @param  int $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setPlayCount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->play_count !== $v) {
            $this->play_count = $v;
            $this->modifiedColumns[] = WebstreamPeer::PLAY_COUNT;
        }


        return $this;
    } // setPlayCount()

    /**
     * Set the value of [length] column.
     *
     * @param  string $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setLength($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->length !== $v) {
            $this->length = $v;
            $this->modifiedColumns[] = WebstreamPeer::LENGTH;
        }


        return $this;
    } // setLength()

    /**
     * Set the value of [mime] column.
     *
     * @param  string $v new value
     * @return Webstream The current object (for fluent API support)
     */
    public function setMime($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->mime !== $v) {
            $this->mime = $v;
            $this->modifiedColumns[] = WebstreamPeer::MIME;
        }


        return $this;
    } // setMime()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Webstream The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new \DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = WebstreamPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Webstream The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new \DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = WebstreamPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

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

            $this->url = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
            $this->id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->owner_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->last_played = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->play_count = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->length = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->mime = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->created_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->updated_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = WebstreamPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Webstream object", $e);
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

        if ($this->aMediaItem !== null && $this->id !== $this->aMediaItem->getId()) {
            $this->aMediaItem = null;
        }
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
            $con = Propel::getConnection(WebstreamPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = WebstreamPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aMediaItem = null;
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
            $con = Propel::getConnection(WebstreamPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = WebstreamQuery::create()
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
            $con = Propel::getConnection(WebstreamPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                // timestampable behavior
                if (!$this->isColumnModified(WebstreamPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(WebstreamPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(WebstreamPeer::UPDATED_AT)) {
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
                WebstreamPeer::addInstanceToPool($this);
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

            if ($this->aMediaItem !== null) {
                if ($this->aMediaItem->isModified() || $this->aMediaItem->isNew()) {
                    $affectedRows += $this->aMediaItem->save($con);
                }
                $this->setMediaItem($this->aMediaItem);
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
        if ($this->isColumnModified(WebstreamPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = '"url"';
        }
        if ($this->isColumnModified(WebstreamPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(WebstreamPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(WebstreamPeer::OWNER_ID)) {
            $modifiedColumns[':p' . $index++]  = '"owner_id"';
        }
        if ($this->isColumnModified(WebstreamPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(WebstreamPeer::LAST_PLAYED)) {
            $modifiedColumns[':p' . $index++]  = '"last_played"';
        }
        if ($this->isColumnModified(WebstreamPeer::PLAY_COUNT)) {
            $modifiedColumns[':p' . $index++]  = '"play_count"';
        }
        if ($this->isColumnModified(WebstreamPeer::LENGTH)) {
            $modifiedColumns[':p' . $index++]  = '"length"';
        }
        if ($this->isColumnModified(WebstreamPeer::MIME)) {
            $modifiedColumns[':p' . $index++]  = '"mime"';
        }
        if ($this->isColumnModified(WebstreamPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"created_at"';
        }
        if ($this->isColumnModified(WebstreamPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '"updated_at"';
        }

        $sql = sprintf(
            'INSERT INTO "media_webstream" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"url"':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
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

            if ($this->aMediaItem !== null) {
                if (!$this->aMediaItem->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aMediaItem->getValidationFailures());
                }
            }

            if ($this->aCcSubjs !== null) {
                if (!$this->aCcSubjs->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
                }
            }


            if (($retval = WebstreamPeer::doValidate($this, $columns)) !== true) {
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
        $pos = WebstreamPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUrl();
                break;
            case 1:
                return $this->getId();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getOwnerId();
                break;
            case 4:
                return $this->getDescription();
                break;
            case 5:
                return $this->getLastPlayedTime();
                break;
            case 6:
                return $this->getPlayCount();
                break;
            case 7:
                return $this->getLength();
                break;
            case 8:
                return $this->getMime();
                break;
            case 9:
                return $this->getCreatedAt();
                break;
            case 10:
                return $this->getUpdatedAt();
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
        if (isset($alreadyDumpedObjects['Webstream'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Webstream'][$this->getPrimaryKey()] = true;
        $keys = WebstreamPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getUrl(),
            $keys[1] => $this->getId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getOwnerId(),
            $keys[4] => $this->getDescription(),
            $keys[5] => $this->getLastPlayedTime(),
            $keys[6] => $this->getPlayCount(),
            $keys[7] => $this->getLength(),
            $keys[8] => $this->getMime(),
            $keys[9] => $this->getCreatedAt(),
            $keys[10] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aMediaItem) {
                $result['MediaItem'] = $this->aMediaItem->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = WebstreamPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setUrl($value);
                break;
            case 1:
                $this->setId($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setOwnerId($value);
                break;
            case 4:
                $this->setDescription($value);
                break;
            case 5:
                $this->setLastPlayedTime($value);
                break;
            case 6:
                $this->setPlayCount($value);
                break;
            case 7:
                $this->setLength($value);
                break;
            case 8:
                $this->setMime($value);
                break;
            case 9:
                $this->setCreatedAt($value);
                break;
            case 10:
                $this->setUpdatedAt($value);
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
        $keys = WebstreamPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setUrl($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setOwnerId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLastPlayedTime($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPlayCount($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setLength($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setMime($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(WebstreamPeer::DATABASE_NAME);

        if ($this->isColumnModified(WebstreamPeer::URL)) $criteria->add(WebstreamPeer::URL, $this->url);
        if ($this->isColumnModified(WebstreamPeer::ID)) $criteria->add(WebstreamPeer::ID, $this->id);
        if ($this->isColumnModified(WebstreamPeer::NAME)) $criteria->add(WebstreamPeer::NAME, $this->name);
        if ($this->isColumnModified(WebstreamPeer::OWNER_ID)) $criteria->add(WebstreamPeer::OWNER_ID, $this->owner_id);
        if ($this->isColumnModified(WebstreamPeer::DESCRIPTION)) $criteria->add(WebstreamPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(WebstreamPeer::LAST_PLAYED)) $criteria->add(WebstreamPeer::LAST_PLAYED, $this->last_played);
        if ($this->isColumnModified(WebstreamPeer::PLAY_COUNT)) $criteria->add(WebstreamPeer::PLAY_COUNT, $this->play_count);
        if ($this->isColumnModified(WebstreamPeer::LENGTH)) $criteria->add(WebstreamPeer::LENGTH, $this->length);
        if ($this->isColumnModified(WebstreamPeer::MIME)) $criteria->add(WebstreamPeer::MIME, $this->mime);
        if ($this->isColumnModified(WebstreamPeer::CREATED_AT)) $criteria->add(WebstreamPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(WebstreamPeer::UPDATED_AT)) $criteria->add(WebstreamPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(WebstreamPeer::DATABASE_NAME);
        $criteria->add(WebstreamPeer::ID, $this->id);

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
     * @param object $copyObj An object of Webstream (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUrl($this->getUrl());
        $copyObj->setName($this->getName());
        $copyObj->setOwnerId($this->getOwnerId());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setLastPlayedTime($this->getLastPlayedTime());
        $copyObj->setPlayCount($this->getPlayCount());
        $copyObj->setLength($this->getLength());
        $copyObj->setMime($this->getMime());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            $relObj = $this->getMediaItem();
            if ($relObj) {
                $copyObj->setMediaItem($relObj->copy($deepCopy));
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
     * @return Webstream Clone of current object.
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
     * @return WebstreamPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new WebstreamPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a MediaItem object.
     *
     * @param                  MediaItem $v
     * @return Webstream The current object (for fluent API support)
     * @throws PropelException
     */
    public function setMediaItem(MediaItem $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aMediaItem = $v;

        // Add binding for other direction of this 1:1 relationship.
        if ($v !== null) {
            $v->setWebstream($this);
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
        if ($this->aMediaItem === null && ($this->id !== null) && $doQuery) {
            $this->aMediaItem = MediaItemQuery::create()->findPk($this->id, $con);
            // Because this foreign key represents a one-to-one relationship, we will create a bi-directional association.
            $this->aMediaItem->setWebstream($this);
        }

        return $this->aMediaItem;
    }

    /**
     * Declares an association between this object and a CcSubjs object.
     *
     * @param                  CcSubjs $v
     * @return Webstream The current object (for fluent API support)
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
            $v->addWebstream($this);
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
                $this->aCcSubjs->addWebstreams($this);
             */
        }

        return $this->aCcSubjs;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->url = null;
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
            if ($this->aMediaItem instanceof Persistent) {
              $this->aMediaItem->clearAllReferences($deep);
            }
            if ($this->aCcSubjs instanceof Persistent) {
              $this->aCcSubjs->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aMediaItem = null;
        $this->aCcSubjs = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(WebstreamPeer::DEFAULT_STRING_FORMAT);
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
     * Get or Create the parent MediaItem object of the current object
     *
     * @return    MediaItem The parent object
     */
    public function getParentOrCreate($con = null)
    {
        if ($this->isNew()) {
            if ($this->isPrimaryKeyNull()) {
                //this prevent issue with deep copy & save parent object
                if (null === ($parent = $this->getMediaItem($con))) {
                    $parent = new MediaItem();
                }
                $parent->setDescendantClass('Airtime\MediaItem\Webstream');

                return $parent;
            } else {
                $parent = MediaItemQuery::create()->findPk($this->getPrimaryKey(), $con);
                if (null === $parent || null !== $parent->getDescendantClass()) {
                    $parent = new MediaItem();
                    $parent->setPrimaryKey($this->getPrimaryKey());
                    $parent->setDescendantClass('Airtime\MediaItem\Webstream');
                }

                return $parent;
            }
        }

        return MediaItemQuery::create()->findPk($this->getPrimaryKey(), $con);
    }

    /**
     * Create or Update the parent MediaItem object
     * And return its primary key
     *
     * @return    int The primary key of the parent object
     */
    public function getSyncParent($con = null)
    {
        $parent = $this->getParentOrCreate($con);
        $parent->setName($this->getName());
        $parent->setOwnerId($this->getOwnerId());
        $parent->setDescription($this->getDescription());
        $parent->setLastPlayedTime($this->getLastPlayedTime());
        $parent->setPlayCount($this->getPlayCount());
        $parent->setLength($this->getLength());
        $parent->setMime($this->getMime());
        $parent->setCreatedAt($this->getCreatedAt());
        $parent->setUpdatedAt($this->getUpdatedAt());
        if ($this->getCcSubjs() && $this->getCcSubjs()->isNew()) {
            $parent->setCcSubjs($this->getCcSubjs());
        }

        return $parent;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Webstream The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = WebstreamPeer::UPDATED_AT;

        return $this;
    }

}
