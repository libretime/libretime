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
use Airtime\CcBlock;
use Airtime\CcBlockQuery;
use Airtime\CcFiles;
use Airtime\CcFilesQuery;
use Airtime\CcPlaylist;
use Airtime\CcPlaylistQuery;
use Airtime\CcPref;
use Airtime\CcPrefQuery;
use Airtime\CcShowHosts;
use Airtime\CcShowHostsQuery;
use Airtime\CcSubjs;
use Airtime\CcSubjsPeer;
use Airtime\CcSubjsQuery;
use Airtime\CcSubjsToken;
use Airtime\CcSubjsTokenQuery;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\AudioFileQuery;
use Airtime\MediaItem\Playlist;
use Airtime\MediaItem\PlaylistQuery;
use Airtime\MediaItem\Webstream;
use Airtime\MediaItem\WebstreamQuery;

/**
 * Base class that represents a row from the 'cc_subjs' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSubjs extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Airtime\\CcSubjsPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcSubjsPeer
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
     * The value for the login field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $login;

    /**
     * The value for the pass field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $pass;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'U'
     * @var        string
     */
    protected $type;

    /**
     * The value for the first_name field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $last_name;

    /**
     * The value for the lastlogin field.
     * @var        string
     */
    protected $lastlogin;

    /**
     * The value for the lastfail field.
     * @var        string
     */
    protected $lastfail;

    /**
     * The value for the skype_contact field.
     * @var        string
     */
    protected $skype_contact;

    /**
     * The value for the jabber_contact field.
     * @var        string
     */
    protected $jabber_contact;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the cell_phone field.
     * @var        string
     */
    protected $cell_phone;

    /**
     * The value for the login_attempts field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $login_attempts;

    /**
     * @var        PropelObjectCollection|CcFiles[] Collection to store aggregation of CcFiles objects.
     */
    protected $collCcFilessRelatedByDbOwnerId;
    protected $collCcFilessRelatedByDbOwnerIdPartial;

    /**
     * @var        PropelObjectCollection|CcFiles[] Collection to store aggregation of CcFiles objects.
     */
    protected $collCcFilessRelatedByDbEditedby;
    protected $collCcFilessRelatedByDbEditedbyPartial;

    /**
     * @var        PropelObjectCollection|CcShowHosts[] Collection to store aggregation of CcShowHosts objects.
     */
    protected $collCcShowHostss;
    protected $collCcShowHostssPartial;

    /**
     * @var        PropelObjectCollection|CcPlaylist[] Collection to store aggregation of CcPlaylist objects.
     */
    protected $collCcPlaylists;
    protected $collCcPlaylistsPartial;

    /**
     * @var        PropelObjectCollection|CcBlock[] Collection to store aggregation of CcBlock objects.
     */
    protected $collCcBlocks;
    protected $collCcBlocksPartial;

    /**
     * @var        PropelObjectCollection|CcPref[] Collection to store aggregation of CcPref objects.
     */
    protected $collCcPrefs;
    protected $collCcPrefsPartial;

    /**
     * @var        PropelObjectCollection|CcSubjsToken[] Collection to store aggregation of CcSubjsToken objects.
     */
    protected $collCcSubjsTokens;
    protected $collCcSubjsTokensPartial;

    /**
     * @var        PropelObjectCollection|MediaItem[] Collection to store aggregation of MediaItem objects.
     */
    protected $collMediaItems;
    protected $collMediaItemsPartial;

    /**
     * @var        PropelObjectCollection|AudioFile[] Collection to store aggregation of AudioFile objects.
     */
    protected $collAudioFiles;
    protected $collAudioFilesPartial;

    /**
     * @var        PropelObjectCollection|Webstream[] Collection to store aggregation of Webstream objects.
     */
    protected $collWebstreams;
    protected $collWebstreamsPartial;

    /**
     * @var        PropelObjectCollection|Playlist[] Collection to store aggregation of Playlist objects.
     */
    protected $collPlaylists;
    protected $collPlaylistsPartial;

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
    protected $ccFilessRelatedByDbOwnerIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccFilessRelatedByDbEditedbyScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccShowHostssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccPlaylistsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccBlocksScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccPrefsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccSubjsTokensScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $mediaItemsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $audioFilesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $webstreamsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $playlistsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->login = '';
        $this->pass = '';
        $this->type = 'U';
        $this->first_name = '';
        $this->last_name = '';
        $this->login_attempts = 0;
    }

    /**
     * Initializes internal state of BaseCcSubjs object.
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
     * Get the [login] column value.
     *
     * @return string
     */
    public function getDbLogin()
    {

        return $this->login;
    }

    /**
     * Get the [pass] column value.
     *
     * @return string
     */
    public function getDbPass()
    {

        return $this->pass;
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
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getDbFirstName()
    {

        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getDbLastName()
    {

        return $this->last_name;
    }

    /**
     * Get the [optionally formatted] temporal [lastlogin] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLastlogin($format = 'Y-m-d H:i:s')
    {
        if ($this->lastlogin === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->lastlogin);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->lastlogin, true), $x);
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
     * Get the [optionally formatted] temporal [lastfail] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or \DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDbLastfail($format = 'Y-m-d H:i:s')
    {
        if ($this->lastfail === null) {
            return null;
        }


        try {
            $dt = new \DateTime($this->lastfail);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to \DateTime: " . var_export($this->lastfail, true), $x);
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
     * Get the [skype_contact] column value.
     *
     * @return string
     */
    public function getDbSkypeContact()
    {

        return $this->skype_contact;
    }

    /**
     * Get the [jabber_contact] column value.
     *
     * @return string
     */
    public function getDbJabberContact()
    {

        return $this->jabber_contact;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getDbEmail()
    {

        return $this->email;
    }

    /**
     * Get the [cell_phone] column value.
     *
     * @return string
     */
    public function getDbCellPhone()
    {

        return $this->cell_phone;
    }

    /**
     * Get the [login_attempts] column value.
     *
     * @return int
     */
    public function getDbLoginAttempts()
    {

        return $this->login_attempts;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcSubjsPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [login] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbLogin($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->login !== $v) {
            $this->login = $v;
            $this->modifiedColumns[] = CcSubjsPeer::LOGIN;
        }


        return $this;
    } // setDbLogin()

    /**
     * Set the value of [pass] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbPass($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->pass !== $v) {
            $this->pass = $v;
            $this->modifiedColumns[] = CcSubjsPeer::PASS;
        }


        return $this;
    } // setDbPass()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbType($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CcSubjsPeer::TYPE;
        }


        return $this;
    } // setDbType()

    /**
     * Set the value of [first_name] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbFirstName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[] = CcSubjsPeer::FIRST_NAME;
        }


        return $this;
    } // setDbFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbLastName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[] = CcSubjsPeer::LAST_NAME;
        }


        return $this;
    } // setDbLastName()

    /**
     * Sets the value of [lastlogin] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbLastlogin($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->lastlogin !== null || $dt !== null) {
            $currentDateAsString = ($this->lastlogin !== null && $tmpDt = new \DateTime($this->lastlogin)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->lastlogin = $newDateAsString;
                $this->modifiedColumns[] = CcSubjsPeer::LASTLOGIN;
            }
        } // if either are not null


        return $this;
    } // setDbLastlogin()

    /**
     * Sets the value of [lastfail] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbLastfail($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->lastfail !== null || $dt !== null) {
            $currentDateAsString = ($this->lastfail !== null && $tmpDt = new \DateTime($this->lastfail)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->lastfail = $newDateAsString;
                $this->modifiedColumns[] = CcSubjsPeer::LASTFAIL;
            }
        } // if either are not null


        return $this;
    } // setDbLastfail()

    /**
     * Set the value of [skype_contact] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbSkypeContact($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->skype_contact !== $v) {
            $this->skype_contact = $v;
            $this->modifiedColumns[] = CcSubjsPeer::SKYPE_CONTACT;
        }


        return $this;
    } // setDbSkypeContact()

    /**
     * Set the value of [jabber_contact] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbJabberContact($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->jabber_contact !== $v) {
            $this->jabber_contact = $v;
            $this->modifiedColumns[] = CcSubjsPeer::JABBER_CONTACT;
        }


        return $this;
    } // setDbJabberContact()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbEmail($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = CcSubjsPeer::EMAIL;
        }


        return $this;
    } // setDbEmail()

    /**
     * Set the value of [cell_phone] column.
     *
     * @param  string $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbCellPhone($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->cell_phone !== $v) {
            $this->cell_phone = $v;
            $this->modifiedColumns[] = CcSubjsPeer::CELL_PHONE;
        }


        return $this;
    } // setDbCellPhone()

    /**
     * Set the value of [login_attempts] column.
     *
     * @param  int $v new value
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setDbLoginAttempts($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->login_attempts !== $v) {
            $this->login_attempts = $v;
            $this->modifiedColumns[] = CcSubjsPeer::LOGIN_ATTEMPTS;
        }


        return $this;
    } // setDbLoginAttempts()

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
            if ($this->login !== '') {
                return false;
            }

            if ($this->pass !== '') {
                return false;
            }

            if ($this->type !== 'U') {
                return false;
            }

            if ($this->first_name !== '') {
                return false;
            }

            if ($this->last_name !== '') {
                return false;
            }

            if ($this->login_attempts !== 0) {
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
            $this->login = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->pass = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->first_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->last_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->lastlogin = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->lastfail = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->skype_contact = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->jabber_contact = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->email = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->cell_phone = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->login_attempts = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 13; // 13 = CcSubjsPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcSubjs object", $e);
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
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcSubjsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCcFilessRelatedByDbOwnerId = null;

            $this->collCcFilessRelatedByDbEditedby = null;

            $this->collCcShowHostss = null;

            $this->collCcPlaylists = null;

            $this->collCcBlocks = null;

            $this->collCcPrefs = null;

            $this->collCcSubjsTokens = null;

            $this->collMediaItems = null;

            $this->collAudioFiles = null;

            $this->collWebstreams = null;

            $this->collPlaylists = null;

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
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcSubjsQuery::create()
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
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcSubjsPeer::addInstanceToPool($this);
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

            if ($this->ccFilessRelatedByDbOwnerIdScheduledForDeletion !== null) {
                if (!$this->ccFilessRelatedByDbOwnerIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->ccFilessRelatedByDbOwnerIdScheduledForDeletion as $ccFilesRelatedByDbOwnerId) {
                        // need to save related object because we set the relation to null
                        $ccFilesRelatedByDbOwnerId->save($con);
                    }
                    $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion = null;
                }
            }

            if ($this->collCcFilessRelatedByDbOwnerId !== null) {
                foreach ($this->collCcFilessRelatedByDbOwnerId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccFilessRelatedByDbEditedbyScheduledForDeletion !== null) {
                if (!$this->ccFilessRelatedByDbEditedbyScheduledForDeletion->isEmpty()) {
                    foreach ($this->ccFilessRelatedByDbEditedbyScheduledForDeletion as $ccFilesRelatedByDbEditedby) {
                        // need to save related object because we set the relation to null
                        $ccFilesRelatedByDbEditedby->save($con);
                    }
                    $this->ccFilessRelatedByDbEditedbyScheduledForDeletion = null;
                }
            }

            if ($this->collCcFilessRelatedByDbEditedby !== null) {
                foreach ($this->collCcFilessRelatedByDbEditedby as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccShowHostssScheduledForDeletion !== null) {
                if (!$this->ccShowHostssScheduledForDeletion->isEmpty()) {
                    CcShowHostsQuery::create()
                        ->filterByPrimaryKeys($this->ccShowHostssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowHostssScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowHostss !== null) {
                foreach ($this->collCcShowHostss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccPlaylistsScheduledForDeletion !== null) {
                if (!$this->ccPlaylistsScheduledForDeletion->isEmpty()) {
                    CcPlaylistQuery::create()
                        ->filterByPrimaryKeys($this->ccPlaylistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPlaylistsScheduledForDeletion = null;
                }
            }

            if ($this->collCcPlaylists !== null) {
                foreach ($this->collCcPlaylists as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccBlocksScheduledForDeletion !== null) {
                if (!$this->ccBlocksScheduledForDeletion->isEmpty()) {
                    CcBlockQuery::create()
                        ->filterByPrimaryKeys($this->ccBlocksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccBlocksScheduledForDeletion = null;
                }
            }

            if ($this->collCcBlocks !== null) {
                foreach ($this->collCcBlocks as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccPrefsScheduledForDeletion !== null) {
                if (!$this->ccPrefsScheduledForDeletion->isEmpty()) {
                    CcPrefQuery::create()
                        ->filterByPrimaryKeys($this->ccPrefsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccPrefsScheduledForDeletion = null;
                }
            }

            if ($this->collCcPrefs !== null) {
                foreach ($this->collCcPrefs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccSubjsTokensScheduledForDeletion !== null) {
                if (!$this->ccSubjsTokensScheduledForDeletion->isEmpty()) {
                    CcSubjsTokenQuery::create()
                        ->filterByPrimaryKeys($this->ccSubjsTokensScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccSubjsTokensScheduledForDeletion = null;
                }
            }

            if ($this->collCcSubjsTokens !== null) {
                foreach ($this->collCcSubjsTokens as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->mediaItemsScheduledForDeletion !== null) {
                if (!$this->mediaItemsScheduledForDeletion->isEmpty()) {
                    foreach ($this->mediaItemsScheduledForDeletion as $mediaItem) {
                        // need to save related object because we set the relation to null
                        $mediaItem->save($con);
                    }
                    $this->mediaItemsScheduledForDeletion = null;
                }
            }

            if ($this->collMediaItems !== null) {
                foreach ($this->collMediaItems as $referrerFK) {
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

            if ($this->webstreamsScheduledForDeletion !== null) {
                if (!$this->webstreamsScheduledForDeletion->isEmpty()) {
                    foreach ($this->webstreamsScheduledForDeletion as $webstream) {
                        // need to save related object because we set the relation to null
                        $webstream->save($con);
                    }
                    $this->webstreamsScheduledForDeletion = null;
                }
            }

            if ($this->collWebstreams !== null) {
                foreach ($this->collWebstreams as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->playlistsScheduledForDeletion !== null) {
                if (!$this->playlistsScheduledForDeletion->isEmpty()) {
                    foreach ($this->playlistsScheduledForDeletion as $playlist) {
                        // need to save related object because we set the relation to null
                        $playlist->save($con);
                    }
                    $this->playlistsScheduledForDeletion = null;
                }
            }

            if ($this->collPlaylists !== null) {
                foreach ($this->collPlaylists as $referrerFK) {
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

        $this->modifiedColumns[] = CcSubjsPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcSubjsPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_subjs_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcSubjsPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcSubjsPeer::LOGIN)) {
            $modifiedColumns[':p' . $index++]  = '"login"';
        }
        if ($this->isColumnModified(CcSubjsPeer::PASS)) {
            $modifiedColumns[':p' . $index++]  = '"pass"';
        }
        if ($this->isColumnModified(CcSubjsPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '"type"';
        }
        if ($this->isColumnModified(CcSubjsPeer::FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"first_name"';
        }
        if ($this->isColumnModified(CcSubjsPeer::LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '"last_name"';
        }
        if ($this->isColumnModified(CcSubjsPeer::LASTLOGIN)) {
            $modifiedColumns[':p' . $index++]  = '"lastlogin"';
        }
        if ($this->isColumnModified(CcSubjsPeer::LASTFAIL)) {
            $modifiedColumns[':p' . $index++]  = '"lastfail"';
        }
        if ($this->isColumnModified(CcSubjsPeer::SKYPE_CONTACT)) {
            $modifiedColumns[':p' . $index++]  = '"skype_contact"';
        }
        if ($this->isColumnModified(CcSubjsPeer::JABBER_CONTACT)) {
            $modifiedColumns[':p' . $index++]  = '"jabber_contact"';
        }
        if ($this->isColumnModified(CcSubjsPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '"email"';
        }
        if ($this->isColumnModified(CcSubjsPeer::CELL_PHONE)) {
            $modifiedColumns[':p' . $index++]  = '"cell_phone"';
        }
        if ($this->isColumnModified(CcSubjsPeer::LOGIN_ATTEMPTS)) {
            $modifiedColumns[':p' . $index++]  = '"login_attempts"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_subjs" (%s) VALUES (%s)',
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
                    case '"login"':
                        $stmt->bindValue($identifier, $this->login, PDO::PARAM_STR);
                        break;
                    case '"pass"':
                        $stmt->bindValue($identifier, $this->pass, PDO::PARAM_STR);
                        break;
                    case '"type"':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case '"first_name"':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case '"last_name"':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case '"lastlogin"':
                        $stmt->bindValue($identifier, $this->lastlogin, PDO::PARAM_STR);
                        break;
                    case '"lastfail"':
                        $stmt->bindValue($identifier, $this->lastfail, PDO::PARAM_STR);
                        break;
                    case '"skype_contact"':
                        $stmt->bindValue($identifier, $this->skype_contact, PDO::PARAM_STR);
                        break;
                    case '"jabber_contact"':
                        $stmt->bindValue($identifier, $this->jabber_contact, PDO::PARAM_STR);
                        break;
                    case '"email"':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '"cell_phone"':
                        $stmt->bindValue($identifier, $this->cell_phone, PDO::PARAM_STR);
                        break;
                    case '"login_attempts"':
                        $stmt->bindValue($identifier, $this->login_attempts, PDO::PARAM_INT);
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


            if (($retval = CcSubjsPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcFilessRelatedByDbOwnerId !== null) {
                    foreach ($this->collCcFilessRelatedByDbOwnerId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcFilessRelatedByDbEditedby !== null) {
                    foreach ($this->collCcFilessRelatedByDbEditedby as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcShowHostss !== null) {
                    foreach ($this->collCcShowHostss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcPlaylists !== null) {
                    foreach ($this->collCcPlaylists as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcBlocks !== null) {
                    foreach ($this->collCcBlocks as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcPrefs !== null) {
                    foreach ($this->collCcPrefs as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcSubjsTokens !== null) {
                    foreach ($this->collCcSubjsTokens as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collMediaItems !== null) {
                    foreach ($this->collMediaItems as $referrerFK) {
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

                if ($this->collWebstreams !== null) {
                    foreach ($this->collWebstreams as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPlaylists !== null) {
                    foreach ($this->collPlaylists as $referrerFK) {
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
        $pos = CcSubjsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbLogin();
                break;
            case 2:
                return $this->getDbPass();
                break;
            case 3:
                return $this->getDbType();
                break;
            case 4:
                return $this->getDbFirstName();
                break;
            case 5:
                return $this->getDbLastName();
                break;
            case 6:
                return $this->getDbLastlogin();
                break;
            case 7:
                return $this->getDbLastfail();
                break;
            case 8:
                return $this->getDbSkypeContact();
                break;
            case 9:
                return $this->getDbJabberContact();
                break;
            case 10:
                return $this->getDbEmail();
                break;
            case 11:
                return $this->getDbCellPhone();
                break;
            case 12:
                return $this->getDbLoginAttempts();
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
        if (isset($alreadyDumpedObjects['CcSubjs'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcSubjs'][$this->getPrimaryKey()] = true;
        $keys = CcSubjsPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbLogin(),
            $keys[2] => $this->getDbPass(),
            $keys[3] => $this->getDbType(),
            $keys[4] => $this->getDbFirstName(),
            $keys[5] => $this->getDbLastName(),
            $keys[6] => $this->getDbLastlogin(),
            $keys[7] => $this->getDbLastfail(),
            $keys[8] => $this->getDbSkypeContact(),
            $keys[9] => $this->getDbJabberContact(),
            $keys[10] => $this->getDbEmail(),
            $keys[11] => $this->getDbCellPhone(),
            $keys[12] => $this->getDbLoginAttempts(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCcFilessRelatedByDbOwnerId) {
                $result['CcFilessRelatedByDbOwnerId'] = $this->collCcFilessRelatedByDbOwnerId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcFilessRelatedByDbEditedby) {
                $result['CcFilessRelatedByDbEditedby'] = $this->collCcFilessRelatedByDbEditedby->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcShowHostss) {
                $result['CcShowHostss'] = $this->collCcShowHostss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcPlaylists) {
                $result['CcPlaylists'] = $this->collCcPlaylists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcBlocks) {
                $result['CcBlocks'] = $this->collCcBlocks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcPrefs) {
                $result['CcPrefs'] = $this->collCcPrefs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcSubjsTokens) {
                $result['CcSubjsTokens'] = $this->collCcSubjsTokens->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMediaItems) {
                $result['MediaItems'] = $this->collMediaItems->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAudioFiles) {
                $result['AudioFiles'] = $this->collAudioFiles->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWebstreams) {
                $result['Webstreams'] = $this->collWebstreams->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPlaylists) {
                $result['Playlists'] = $this->collPlaylists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcSubjsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbLogin($value);
                break;
            case 2:
                $this->setDbPass($value);
                break;
            case 3:
                $this->setDbType($value);
                break;
            case 4:
                $this->setDbFirstName($value);
                break;
            case 5:
                $this->setDbLastName($value);
                break;
            case 6:
                $this->setDbLastlogin($value);
                break;
            case 7:
                $this->setDbLastfail($value);
                break;
            case 8:
                $this->setDbSkypeContact($value);
                break;
            case 9:
                $this->setDbJabberContact($value);
                break;
            case 10:
                $this->setDbEmail($value);
                break;
            case 11:
                $this->setDbCellPhone($value);
                break;
            case 12:
                $this->setDbLoginAttempts($value);
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
        $keys = CcSubjsPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbLogin($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbPass($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbFirstName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbLastName($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbLastlogin($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbLastfail($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbSkypeContact($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbJabberContact($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbEmail($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbCellPhone($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbLoginAttempts($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcSubjsPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcSubjsPeer::ID)) $criteria->add(CcSubjsPeer::ID, $this->id);
        if ($this->isColumnModified(CcSubjsPeer::LOGIN)) $criteria->add(CcSubjsPeer::LOGIN, $this->login);
        if ($this->isColumnModified(CcSubjsPeer::PASS)) $criteria->add(CcSubjsPeer::PASS, $this->pass);
        if ($this->isColumnModified(CcSubjsPeer::TYPE)) $criteria->add(CcSubjsPeer::TYPE, $this->type);
        if ($this->isColumnModified(CcSubjsPeer::FIRST_NAME)) $criteria->add(CcSubjsPeer::FIRST_NAME, $this->first_name);
        if ($this->isColumnModified(CcSubjsPeer::LAST_NAME)) $criteria->add(CcSubjsPeer::LAST_NAME, $this->last_name);
        if ($this->isColumnModified(CcSubjsPeer::LASTLOGIN)) $criteria->add(CcSubjsPeer::LASTLOGIN, $this->lastlogin);
        if ($this->isColumnModified(CcSubjsPeer::LASTFAIL)) $criteria->add(CcSubjsPeer::LASTFAIL, $this->lastfail);
        if ($this->isColumnModified(CcSubjsPeer::SKYPE_CONTACT)) $criteria->add(CcSubjsPeer::SKYPE_CONTACT, $this->skype_contact);
        if ($this->isColumnModified(CcSubjsPeer::JABBER_CONTACT)) $criteria->add(CcSubjsPeer::JABBER_CONTACT, $this->jabber_contact);
        if ($this->isColumnModified(CcSubjsPeer::EMAIL)) $criteria->add(CcSubjsPeer::EMAIL, $this->email);
        if ($this->isColumnModified(CcSubjsPeer::CELL_PHONE)) $criteria->add(CcSubjsPeer::CELL_PHONE, $this->cell_phone);
        if ($this->isColumnModified(CcSubjsPeer::LOGIN_ATTEMPTS)) $criteria->add(CcSubjsPeer::LOGIN_ATTEMPTS, $this->login_attempts);

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
        $criteria = new Criteria(CcSubjsPeer::DATABASE_NAME);
        $criteria->add(CcSubjsPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcSubjs (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbLogin($this->getDbLogin());
        $copyObj->setDbPass($this->getDbPass());
        $copyObj->setDbType($this->getDbType());
        $copyObj->setDbFirstName($this->getDbFirstName());
        $copyObj->setDbLastName($this->getDbLastName());
        $copyObj->setDbLastlogin($this->getDbLastlogin());
        $copyObj->setDbLastfail($this->getDbLastfail());
        $copyObj->setDbSkypeContact($this->getDbSkypeContact());
        $copyObj->setDbJabberContact($this->getDbJabberContact());
        $copyObj->setDbEmail($this->getDbEmail());
        $copyObj->setDbCellPhone($this->getDbCellPhone());
        $copyObj->setDbLoginAttempts($this->getDbLoginAttempts());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCcFilessRelatedByDbOwnerId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcFilesRelatedByDbOwnerId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcFilessRelatedByDbEditedby() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcFilesRelatedByDbEditedby($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcShowHostss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowHosts($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcPlaylists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPlaylist($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcBlocks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcBlock($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcPrefs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcPref($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcSubjsTokens() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcSubjsToken($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMediaItems() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMediaItem($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAudioFiles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAudioFile($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWebstreams() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWebstream($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPlaylists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPlaylist($relObj->copy($deepCopy));
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
     * @return CcSubjs Clone of current object.
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
     * @return CcSubjsPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcSubjsPeer();
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
        if ('CcFilesRelatedByDbOwnerId' == $relationName) {
            $this->initCcFilessRelatedByDbOwnerId();
        }
        if ('CcFilesRelatedByDbEditedby' == $relationName) {
            $this->initCcFilessRelatedByDbEditedby();
        }
        if ('CcShowHosts' == $relationName) {
            $this->initCcShowHostss();
        }
        if ('CcPlaylist' == $relationName) {
            $this->initCcPlaylists();
        }
        if ('CcBlock' == $relationName) {
            $this->initCcBlocks();
        }
        if ('CcPref' == $relationName) {
            $this->initCcPrefs();
        }
        if ('CcSubjsToken' == $relationName) {
            $this->initCcSubjsTokens();
        }
        if ('MediaItem' == $relationName) {
            $this->initMediaItems();
        }
        if ('AudioFile' == $relationName) {
            $this->initAudioFiles();
        }
        if ('Webstream' == $relationName) {
            $this->initWebstreams();
        }
        if ('Playlist' == $relationName) {
            $this->initPlaylists();
        }
    }

    /**
     * Clears out the collCcFilessRelatedByDbOwnerId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcFilessRelatedByDbOwnerId()
     */
    public function clearCcFilessRelatedByDbOwnerId()
    {
        $this->collCcFilessRelatedByDbOwnerId = null; // important to set this to null since that means it is uninitialized
        $this->collCcFilessRelatedByDbOwnerIdPartial = null;

        return $this;
    }

    /**
     * reset is the collCcFilessRelatedByDbOwnerId collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcFilessRelatedByDbOwnerId($v = true)
    {
        $this->collCcFilessRelatedByDbOwnerIdPartial = $v;
    }

    /**
     * Initializes the collCcFilessRelatedByDbOwnerId collection.
     *
     * By default this just sets the collCcFilessRelatedByDbOwnerId collection to an empty array (like clearcollCcFilessRelatedByDbOwnerId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcFilessRelatedByDbOwnerId($overrideExisting = true)
    {
        if (null !== $this->collCcFilessRelatedByDbOwnerId && !$overrideExisting) {
            return;
        }
        $this->collCcFilessRelatedByDbOwnerId = new PropelObjectCollection();
        $this->collCcFilessRelatedByDbOwnerId->setModel('CcFiles');
    }

    /**
     * Gets an array of CcFiles objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     * @throws PropelException
     */
    public function getCcFilessRelatedByDbOwnerId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessRelatedByDbOwnerIdPartial && !$this->isNew();
        if (null === $this->collCcFilessRelatedByDbOwnerId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcFilessRelatedByDbOwnerId) {
                // return empty collection
                $this->initCcFilessRelatedByDbOwnerId();
            } else {
                $collCcFilessRelatedByDbOwnerId = CcFilesQuery::create(null, $criteria)
                    ->filterByFkOwner($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcFilessRelatedByDbOwnerIdPartial && count($collCcFilessRelatedByDbOwnerId)) {
                      $this->initCcFilessRelatedByDbOwnerId(false);

                      foreach ($collCcFilessRelatedByDbOwnerId as $obj) {
                        if (false == $this->collCcFilessRelatedByDbOwnerId->contains($obj)) {
                          $this->collCcFilessRelatedByDbOwnerId->append($obj);
                        }
                      }

                      $this->collCcFilessRelatedByDbOwnerIdPartial = true;
                    }

                    $collCcFilessRelatedByDbOwnerId->getInternalIterator()->rewind();

                    return $collCcFilessRelatedByDbOwnerId;
                }

                if ($partial && $this->collCcFilessRelatedByDbOwnerId) {
                    foreach ($this->collCcFilessRelatedByDbOwnerId as $obj) {
                        if ($obj->isNew()) {
                            $collCcFilessRelatedByDbOwnerId[] = $obj;
                        }
                    }
                }

                $this->collCcFilessRelatedByDbOwnerId = $collCcFilessRelatedByDbOwnerId;
                $this->collCcFilessRelatedByDbOwnerIdPartial = false;
            }
        }

        return $this->collCcFilessRelatedByDbOwnerId;
    }

    /**
     * Sets a collection of CcFilesRelatedByDbOwnerId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccFilessRelatedByDbOwnerId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcFilessRelatedByDbOwnerId(PropelCollection $ccFilessRelatedByDbOwnerId, PropelPDO $con = null)
    {
        $ccFilessRelatedByDbOwnerIdToDelete = $this->getCcFilessRelatedByDbOwnerId(new Criteria(), $con)->diff($ccFilessRelatedByDbOwnerId);


        $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion = $ccFilessRelatedByDbOwnerIdToDelete;

        foreach ($ccFilessRelatedByDbOwnerIdToDelete as $ccFilesRelatedByDbOwnerIdRemoved) {
            $ccFilesRelatedByDbOwnerIdRemoved->setFkOwner(null);
        }

        $this->collCcFilessRelatedByDbOwnerId = null;
        foreach ($ccFilessRelatedByDbOwnerId as $ccFilesRelatedByDbOwnerId) {
            $this->addCcFilesRelatedByDbOwnerId($ccFilesRelatedByDbOwnerId);
        }

        $this->collCcFilessRelatedByDbOwnerId = $ccFilessRelatedByDbOwnerId;
        $this->collCcFilessRelatedByDbOwnerIdPartial = false;

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
    public function countCcFilessRelatedByDbOwnerId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessRelatedByDbOwnerIdPartial && !$this->isNew();
        if (null === $this->collCcFilessRelatedByDbOwnerId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcFilessRelatedByDbOwnerId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcFilessRelatedByDbOwnerId());
            }
            $query = CcFilesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFkOwner($this)
                ->count($con);
        }

        return count($this->collCcFilessRelatedByDbOwnerId);
    }

    /**
     * Method called to associate a CcFiles object to this object
     * through the CcFiles foreign key attribute.
     *
     * @param    CcFiles $l CcFiles
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcFilesRelatedByDbOwnerId(CcFiles $l)
    {
        if ($this->collCcFilessRelatedByDbOwnerId === null) {
            $this->initCcFilessRelatedByDbOwnerId();
            $this->collCcFilessRelatedByDbOwnerIdPartial = true;
        }

        if (!in_array($l, $this->collCcFilessRelatedByDbOwnerId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcFilesRelatedByDbOwnerId($l);

            if ($this->ccFilessRelatedByDbOwnerIdScheduledForDeletion and $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion->contains($l)) {
                $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion->remove($this->ccFilessRelatedByDbOwnerIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcFilesRelatedByDbOwnerId $ccFilesRelatedByDbOwnerId The ccFilesRelatedByDbOwnerId object to add.
     */
    protected function doAddCcFilesRelatedByDbOwnerId($ccFilesRelatedByDbOwnerId)
    {
        $this->collCcFilessRelatedByDbOwnerId[]= $ccFilesRelatedByDbOwnerId;
        $ccFilesRelatedByDbOwnerId->setFkOwner($this);
    }

    /**
     * @param	CcFilesRelatedByDbOwnerId $ccFilesRelatedByDbOwnerId The ccFilesRelatedByDbOwnerId object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcFilesRelatedByDbOwnerId($ccFilesRelatedByDbOwnerId)
    {
        if ($this->getCcFilessRelatedByDbOwnerId()->contains($ccFilesRelatedByDbOwnerId)) {
            $this->collCcFilessRelatedByDbOwnerId->remove($this->collCcFilessRelatedByDbOwnerId->search($ccFilesRelatedByDbOwnerId));
            if (null === $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion) {
                $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion = clone $this->collCcFilessRelatedByDbOwnerId;
                $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion->clear();
            }
            $this->ccFilessRelatedByDbOwnerIdScheduledForDeletion[]= $ccFilesRelatedByDbOwnerId;
            $ccFilesRelatedByDbOwnerId->setFkOwner(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related CcFilessRelatedByDbOwnerId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     */
    public function getCcFilessRelatedByDbOwnerIdJoinCcMusicDirs($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcFilesQuery::create(null, $criteria);
        $query->joinWith('CcMusicDirs', $join_behavior);

        return $this->getCcFilessRelatedByDbOwnerId($query, $con);
    }

    /**
     * Clears out the collCcFilessRelatedByDbEditedby collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcFilessRelatedByDbEditedby()
     */
    public function clearCcFilessRelatedByDbEditedby()
    {
        $this->collCcFilessRelatedByDbEditedby = null; // important to set this to null since that means it is uninitialized
        $this->collCcFilessRelatedByDbEditedbyPartial = null;

        return $this;
    }

    /**
     * reset is the collCcFilessRelatedByDbEditedby collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcFilessRelatedByDbEditedby($v = true)
    {
        $this->collCcFilessRelatedByDbEditedbyPartial = $v;
    }

    /**
     * Initializes the collCcFilessRelatedByDbEditedby collection.
     *
     * By default this just sets the collCcFilessRelatedByDbEditedby collection to an empty array (like clearcollCcFilessRelatedByDbEditedby());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcFilessRelatedByDbEditedby($overrideExisting = true)
    {
        if (null !== $this->collCcFilessRelatedByDbEditedby && !$overrideExisting) {
            return;
        }
        $this->collCcFilessRelatedByDbEditedby = new PropelObjectCollection();
        $this->collCcFilessRelatedByDbEditedby->setModel('CcFiles');
    }

    /**
     * Gets an array of CcFiles objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     * @throws PropelException
     */
    public function getCcFilessRelatedByDbEditedby($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessRelatedByDbEditedbyPartial && !$this->isNew();
        if (null === $this->collCcFilessRelatedByDbEditedby || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcFilessRelatedByDbEditedby) {
                // return empty collection
                $this->initCcFilessRelatedByDbEditedby();
            } else {
                $collCcFilessRelatedByDbEditedby = CcFilesQuery::create(null, $criteria)
                    ->filterByCcSubjsRelatedByDbEditedby($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcFilessRelatedByDbEditedbyPartial && count($collCcFilessRelatedByDbEditedby)) {
                      $this->initCcFilessRelatedByDbEditedby(false);

                      foreach ($collCcFilessRelatedByDbEditedby as $obj) {
                        if (false == $this->collCcFilessRelatedByDbEditedby->contains($obj)) {
                          $this->collCcFilessRelatedByDbEditedby->append($obj);
                        }
                      }

                      $this->collCcFilessRelatedByDbEditedbyPartial = true;
                    }

                    $collCcFilessRelatedByDbEditedby->getInternalIterator()->rewind();

                    return $collCcFilessRelatedByDbEditedby;
                }

                if ($partial && $this->collCcFilessRelatedByDbEditedby) {
                    foreach ($this->collCcFilessRelatedByDbEditedby as $obj) {
                        if ($obj->isNew()) {
                            $collCcFilessRelatedByDbEditedby[] = $obj;
                        }
                    }
                }

                $this->collCcFilessRelatedByDbEditedby = $collCcFilessRelatedByDbEditedby;
                $this->collCcFilessRelatedByDbEditedbyPartial = false;
            }
        }

        return $this->collCcFilessRelatedByDbEditedby;
    }

    /**
     * Sets a collection of CcFilesRelatedByDbEditedby objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccFilessRelatedByDbEditedby A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcFilessRelatedByDbEditedby(PropelCollection $ccFilessRelatedByDbEditedby, PropelPDO $con = null)
    {
        $ccFilessRelatedByDbEditedbyToDelete = $this->getCcFilessRelatedByDbEditedby(new Criteria(), $con)->diff($ccFilessRelatedByDbEditedby);


        $this->ccFilessRelatedByDbEditedbyScheduledForDeletion = $ccFilessRelatedByDbEditedbyToDelete;

        foreach ($ccFilessRelatedByDbEditedbyToDelete as $ccFilesRelatedByDbEditedbyRemoved) {
            $ccFilesRelatedByDbEditedbyRemoved->setCcSubjsRelatedByDbEditedby(null);
        }

        $this->collCcFilessRelatedByDbEditedby = null;
        foreach ($ccFilessRelatedByDbEditedby as $ccFilesRelatedByDbEditedby) {
            $this->addCcFilesRelatedByDbEditedby($ccFilesRelatedByDbEditedby);
        }

        $this->collCcFilessRelatedByDbEditedby = $ccFilessRelatedByDbEditedby;
        $this->collCcFilessRelatedByDbEditedbyPartial = false;

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
    public function countCcFilessRelatedByDbEditedby(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcFilessRelatedByDbEditedbyPartial && !$this->isNew();
        if (null === $this->collCcFilessRelatedByDbEditedby || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcFilessRelatedByDbEditedby) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcFilessRelatedByDbEditedby());
            }
            $query = CcFilesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjsRelatedByDbEditedby($this)
                ->count($con);
        }

        return count($this->collCcFilessRelatedByDbEditedby);
    }

    /**
     * Method called to associate a CcFiles object to this object
     * through the CcFiles foreign key attribute.
     *
     * @param    CcFiles $l CcFiles
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcFilesRelatedByDbEditedby(CcFiles $l)
    {
        if ($this->collCcFilessRelatedByDbEditedby === null) {
            $this->initCcFilessRelatedByDbEditedby();
            $this->collCcFilessRelatedByDbEditedbyPartial = true;
        }

        if (!in_array($l, $this->collCcFilessRelatedByDbEditedby->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcFilesRelatedByDbEditedby($l);

            if ($this->ccFilessRelatedByDbEditedbyScheduledForDeletion and $this->ccFilessRelatedByDbEditedbyScheduledForDeletion->contains($l)) {
                $this->ccFilessRelatedByDbEditedbyScheduledForDeletion->remove($this->ccFilessRelatedByDbEditedbyScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcFilesRelatedByDbEditedby $ccFilesRelatedByDbEditedby The ccFilesRelatedByDbEditedby object to add.
     */
    protected function doAddCcFilesRelatedByDbEditedby($ccFilesRelatedByDbEditedby)
    {
        $this->collCcFilessRelatedByDbEditedby[]= $ccFilesRelatedByDbEditedby;
        $ccFilesRelatedByDbEditedby->setCcSubjsRelatedByDbEditedby($this);
    }

    /**
     * @param	CcFilesRelatedByDbEditedby $ccFilesRelatedByDbEditedby The ccFilesRelatedByDbEditedby object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcFilesRelatedByDbEditedby($ccFilesRelatedByDbEditedby)
    {
        if ($this->getCcFilessRelatedByDbEditedby()->contains($ccFilesRelatedByDbEditedby)) {
            $this->collCcFilessRelatedByDbEditedby->remove($this->collCcFilessRelatedByDbEditedby->search($ccFilesRelatedByDbEditedby));
            if (null === $this->ccFilessRelatedByDbEditedbyScheduledForDeletion) {
                $this->ccFilessRelatedByDbEditedbyScheduledForDeletion = clone $this->collCcFilessRelatedByDbEditedby;
                $this->ccFilessRelatedByDbEditedbyScheduledForDeletion->clear();
            }
            $this->ccFilessRelatedByDbEditedbyScheduledForDeletion[]= $ccFilesRelatedByDbEditedby;
            $ccFilesRelatedByDbEditedby->setCcSubjsRelatedByDbEditedby(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related CcFilessRelatedByDbEditedby from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcFiles[] List of CcFiles objects
     */
    public function getCcFilessRelatedByDbEditedbyJoinCcMusicDirs($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcFilesQuery::create(null, $criteria);
        $query->joinWith('CcMusicDirs', $join_behavior);

        return $this->getCcFilessRelatedByDbEditedby($query, $con);
    }

    /**
     * Clears out the collCcShowHostss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcShowHostss()
     */
    public function clearCcShowHostss()
    {
        $this->collCcShowHostss = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowHostssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowHostss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowHostss($v = true)
    {
        $this->collCcShowHostssPartial = $v;
    }

    /**
     * Initializes the collCcShowHostss collection.
     *
     * By default this just sets the collCcShowHostss collection to an empty array (like clearcollCcShowHostss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowHostss($overrideExisting = true)
    {
        if (null !== $this->collCcShowHostss && !$overrideExisting) {
            return;
        }
        $this->collCcShowHostss = new PropelObjectCollection();
        $this->collCcShowHostss->setModel('CcShowHosts');
    }

    /**
     * Gets an array of CcShowHosts objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowHosts[] List of CcShowHosts objects
     * @throws PropelException
     */
    public function getCcShowHostss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowHostssPartial && !$this->isNew();
        if (null === $this->collCcShowHostss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowHostss) {
                // return empty collection
                $this->initCcShowHostss();
            } else {
                $collCcShowHostss = CcShowHostsQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowHostssPartial && count($collCcShowHostss)) {
                      $this->initCcShowHostss(false);

                      foreach ($collCcShowHostss as $obj) {
                        if (false == $this->collCcShowHostss->contains($obj)) {
                          $this->collCcShowHostss->append($obj);
                        }
                      }

                      $this->collCcShowHostssPartial = true;
                    }

                    $collCcShowHostss->getInternalIterator()->rewind();

                    return $collCcShowHostss;
                }

                if ($partial && $this->collCcShowHostss) {
                    foreach ($this->collCcShowHostss as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowHostss[] = $obj;
                        }
                    }
                }

                $this->collCcShowHostss = $collCcShowHostss;
                $this->collCcShowHostssPartial = false;
            }
        }

        return $this->collCcShowHostss;
    }

    /**
     * Sets a collection of CcShowHosts objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowHostss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcShowHostss(PropelCollection $ccShowHostss, PropelPDO $con = null)
    {
        $ccShowHostssToDelete = $this->getCcShowHostss(new Criteria(), $con)->diff($ccShowHostss);


        $this->ccShowHostssScheduledForDeletion = $ccShowHostssToDelete;

        foreach ($ccShowHostssToDelete as $ccShowHostsRemoved) {
            $ccShowHostsRemoved->setCcSubjs(null);
        }

        $this->collCcShowHostss = null;
        foreach ($ccShowHostss as $ccShowHosts) {
            $this->addCcShowHosts($ccShowHosts);
        }

        $this->collCcShowHostss = $ccShowHostss;
        $this->collCcShowHostssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowHosts objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowHosts objects.
     * @throws PropelException
     */
    public function countCcShowHostss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowHostssPartial && !$this->isNew();
        if (null === $this->collCcShowHostss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowHostss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowHostss());
            }
            $query = CcShowHostsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collCcShowHostss);
    }

    /**
     * Method called to associate a CcShowHosts object to this object
     * through the CcShowHosts foreign key attribute.
     *
     * @param    CcShowHosts $l CcShowHosts
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcShowHosts(CcShowHosts $l)
    {
        if ($this->collCcShowHostss === null) {
            $this->initCcShowHostss();
            $this->collCcShowHostssPartial = true;
        }

        if (!in_array($l, $this->collCcShowHostss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowHosts($l);

            if ($this->ccShowHostssScheduledForDeletion and $this->ccShowHostssScheduledForDeletion->contains($l)) {
                $this->ccShowHostssScheduledForDeletion->remove($this->ccShowHostssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowHosts $ccShowHosts The ccShowHosts object to add.
     */
    protected function doAddCcShowHosts($ccShowHosts)
    {
        $this->collCcShowHostss[]= $ccShowHosts;
        $ccShowHosts->setCcSubjs($this);
    }

    /**
     * @param	CcShowHosts $ccShowHosts The ccShowHosts object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcShowHosts($ccShowHosts)
    {
        if ($this->getCcShowHostss()->contains($ccShowHosts)) {
            $this->collCcShowHostss->remove($this->collCcShowHostss->search($ccShowHosts));
            if (null === $this->ccShowHostssScheduledForDeletion) {
                $this->ccShowHostssScheduledForDeletion = clone $this->collCcShowHostss;
                $this->ccShowHostssScheduledForDeletion->clear();
            }
            $this->ccShowHostssScheduledForDeletion[]= clone $ccShowHosts;
            $ccShowHosts->setCcSubjs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related CcShowHostss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowHosts[] List of CcShowHosts objects
     */
    public function getCcShowHostssJoinCcShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowHostsQuery::create(null, $criteria);
        $query->joinWith('CcShow', $join_behavior);

        return $this->getCcShowHostss($query, $con);
    }

    /**
     * Clears out the collCcPlaylists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcPlaylists()
     */
    public function clearCcPlaylists()
    {
        $this->collCcPlaylists = null; // important to set this to null since that means it is uninitialized
        $this->collCcPlaylistsPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPlaylists collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPlaylists($v = true)
    {
        $this->collCcPlaylistsPartial = $v;
    }

    /**
     * Initializes the collCcPlaylists collection.
     *
     * By default this just sets the collCcPlaylists collection to an empty array (like clearcollCcPlaylists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPlaylists($overrideExisting = true)
    {
        if (null !== $this->collCcPlaylists && !$overrideExisting) {
            return;
        }
        $this->collCcPlaylists = new PropelObjectCollection();
        $this->collCcPlaylists->setModel('CcPlaylist');
    }

    /**
     * Gets an array of CcPlaylist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPlaylist[] List of CcPlaylist objects
     * @throws PropelException
     */
    public function getCcPlaylists($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistsPartial && !$this->isNew();
        if (null === $this->collCcPlaylists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylists) {
                // return empty collection
                $this->initCcPlaylists();
            } else {
                $collCcPlaylists = CcPlaylistQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPlaylistsPartial && count($collCcPlaylists)) {
                      $this->initCcPlaylists(false);

                      foreach ($collCcPlaylists as $obj) {
                        if (false == $this->collCcPlaylists->contains($obj)) {
                          $this->collCcPlaylists->append($obj);
                        }
                      }

                      $this->collCcPlaylistsPartial = true;
                    }

                    $collCcPlaylists->getInternalIterator()->rewind();

                    return $collCcPlaylists;
                }

                if ($partial && $this->collCcPlaylists) {
                    foreach ($this->collCcPlaylists as $obj) {
                        if ($obj->isNew()) {
                            $collCcPlaylists[] = $obj;
                        }
                    }
                }

                $this->collCcPlaylists = $collCcPlaylists;
                $this->collCcPlaylistsPartial = false;
            }
        }

        return $this->collCcPlaylists;
    }

    /**
     * Sets a collection of CcPlaylist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPlaylists A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcPlaylists(PropelCollection $ccPlaylists, PropelPDO $con = null)
    {
        $ccPlaylistsToDelete = $this->getCcPlaylists(new Criteria(), $con)->diff($ccPlaylists);


        $this->ccPlaylistsScheduledForDeletion = $ccPlaylistsToDelete;

        foreach ($ccPlaylistsToDelete as $ccPlaylistRemoved) {
            $ccPlaylistRemoved->setCcSubjs(null);
        }

        $this->collCcPlaylists = null;
        foreach ($ccPlaylists as $ccPlaylist) {
            $this->addCcPlaylist($ccPlaylist);
        }

        $this->collCcPlaylists = $ccPlaylists;
        $this->collCcPlaylistsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPlaylist objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPlaylist objects.
     * @throws PropelException
     */
    public function countCcPlaylists(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPlaylistsPartial && !$this->isNew();
        if (null === $this->collCcPlaylists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPlaylists) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPlaylists());
            }
            $query = CcPlaylistQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collCcPlaylists);
    }

    /**
     * Method called to associate a CcPlaylist object to this object
     * through the CcPlaylist foreign key attribute.
     *
     * @param    CcPlaylist $l CcPlaylist
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcPlaylist(CcPlaylist $l)
    {
        if ($this->collCcPlaylists === null) {
            $this->initCcPlaylists();
            $this->collCcPlaylistsPartial = true;
        }

        if (!in_array($l, $this->collCcPlaylists->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPlaylist($l);

            if ($this->ccPlaylistsScheduledForDeletion and $this->ccPlaylistsScheduledForDeletion->contains($l)) {
                $this->ccPlaylistsScheduledForDeletion->remove($this->ccPlaylistsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPlaylist $ccPlaylist The ccPlaylist object to add.
     */
    protected function doAddCcPlaylist($ccPlaylist)
    {
        $this->collCcPlaylists[]= $ccPlaylist;
        $ccPlaylist->setCcSubjs($this);
    }

    /**
     * @param	CcPlaylist $ccPlaylist The ccPlaylist object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcPlaylist($ccPlaylist)
    {
        if ($this->getCcPlaylists()->contains($ccPlaylist)) {
            $this->collCcPlaylists->remove($this->collCcPlaylists->search($ccPlaylist));
            if (null === $this->ccPlaylistsScheduledForDeletion) {
                $this->ccPlaylistsScheduledForDeletion = clone $this->collCcPlaylists;
                $this->ccPlaylistsScheduledForDeletion->clear();
            }
            $this->ccPlaylistsScheduledForDeletion[]= $ccPlaylist;
            $ccPlaylist->setCcSubjs(null);
        }

        return $this;
    }

    /**
     * Clears out the collCcBlocks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcBlocks()
     */
    public function clearCcBlocks()
    {
        $this->collCcBlocks = null; // important to set this to null since that means it is uninitialized
        $this->collCcBlocksPartial = null;

        return $this;
    }

    /**
     * reset is the collCcBlocks collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcBlocks($v = true)
    {
        $this->collCcBlocksPartial = $v;
    }

    /**
     * Initializes the collCcBlocks collection.
     *
     * By default this just sets the collCcBlocks collection to an empty array (like clearcollCcBlocks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcBlocks($overrideExisting = true)
    {
        if (null !== $this->collCcBlocks && !$overrideExisting) {
            return;
        }
        $this->collCcBlocks = new PropelObjectCollection();
        $this->collCcBlocks->setModel('CcBlock');
    }

    /**
     * Gets an array of CcBlock objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcBlock[] List of CcBlock objects
     * @throws PropelException
     */
    public function getCcBlocks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcBlocksPartial && !$this->isNew();
        if (null === $this->collCcBlocks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcBlocks) {
                // return empty collection
                $this->initCcBlocks();
            } else {
                $collCcBlocks = CcBlockQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcBlocksPartial && count($collCcBlocks)) {
                      $this->initCcBlocks(false);

                      foreach ($collCcBlocks as $obj) {
                        if (false == $this->collCcBlocks->contains($obj)) {
                          $this->collCcBlocks->append($obj);
                        }
                      }

                      $this->collCcBlocksPartial = true;
                    }

                    $collCcBlocks->getInternalIterator()->rewind();

                    return $collCcBlocks;
                }

                if ($partial && $this->collCcBlocks) {
                    foreach ($this->collCcBlocks as $obj) {
                        if ($obj->isNew()) {
                            $collCcBlocks[] = $obj;
                        }
                    }
                }

                $this->collCcBlocks = $collCcBlocks;
                $this->collCcBlocksPartial = false;
            }
        }

        return $this->collCcBlocks;
    }

    /**
     * Sets a collection of CcBlock objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccBlocks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcBlocks(PropelCollection $ccBlocks, PropelPDO $con = null)
    {
        $ccBlocksToDelete = $this->getCcBlocks(new Criteria(), $con)->diff($ccBlocks);


        $this->ccBlocksScheduledForDeletion = $ccBlocksToDelete;

        foreach ($ccBlocksToDelete as $ccBlockRemoved) {
            $ccBlockRemoved->setCcSubjs(null);
        }

        $this->collCcBlocks = null;
        foreach ($ccBlocks as $ccBlock) {
            $this->addCcBlock($ccBlock);
        }

        $this->collCcBlocks = $ccBlocks;
        $this->collCcBlocksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcBlock objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcBlock objects.
     * @throws PropelException
     */
    public function countCcBlocks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcBlocksPartial && !$this->isNew();
        if (null === $this->collCcBlocks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcBlocks) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcBlocks());
            }
            $query = CcBlockQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collCcBlocks);
    }

    /**
     * Method called to associate a CcBlock object to this object
     * through the CcBlock foreign key attribute.
     *
     * @param    CcBlock $l CcBlock
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcBlock(CcBlock $l)
    {
        if ($this->collCcBlocks === null) {
            $this->initCcBlocks();
            $this->collCcBlocksPartial = true;
        }

        if (!in_array($l, $this->collCcBlocks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcBlock($l);

            if ($this->ccBlocksScheduledForDeletion and $this->ccBlocksScheduledForDeletion->contains($l)) {
                $this->ccBlocksScheduledForDeletion->remove($this->ccBlocksScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcBlock $ccBlock The ccBlock object to add.
     */
    protected function doAddCcBlock($ccBlock)
    {
        $this->collCcBlocks[]= $ccBlock;
        $ccBlock->setCcSubjs($this);
    }

    /**
     * @param	CcBlock $ccBlock The ccBlock object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcBlock($ccBlock)
    {
        if ($this->getCcBlocks()->contains($ccBlock)) {
            $this->collCcBlocks->remove($this->collCcBlocks->search($ccBlock));
            if (null === $this->ccBlocksScheduledForDeletion) {
                $this->ccBlocksScheduledForDeletion = clone $this->collCcBlocks;
                $this->ccBlocksScheduledForDeletion->clear();
            }
            $this->ccBlocksScheduledForDeletion[]= $ccBlock;
            $ccBlock->setCcSubjs(null);
        }

        return $this;
    }

    /**
     * Clears out the collCcPrefs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcPrefs()
     */
    public function clearCcPrefs()
    {
        $this->collCcPrefs = null; // important to set this to null since that means it is uninitialized
        $this->collCcPrefsPartial = null;

        return $this;
    }

    /**
     * reset is the collCcPrefs collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcPrefs($v = true)
    {
        $this->collCcPrefsPartial = $v;
    }

    /**
     * Initializes the collCcPrefs collection.
     *
     * By default this just sets the collCcPrefs collection to an empty array (like clearcollCcPrefs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcPrefs($overrideExisting = true)
    {
        if (null !== $this->collCcPrefs && !$overrideExisting) {
            return;
        }
        $this->collCcPrefs = new PropelObjectCollection();
        $this->collCcPrefs->setModel('CcPref');
    }

    /**
     * Gets an array of CcPref objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcPref[] List of CcPref objects
     * @throws PropelException
     */
    public function getCcPrefs($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcPrefsPartial && !$this->isNew();
        if (null === $this->collCcPrefs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcPrefs) {
                // return empty collection
                $this->initCcPrefs();
            } else {
                $collCcPrefs = CcPrefQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcPrefsPartial && count($collCcPrefs)) {
                      $this->initCcPrefs(false);

                      foreach ($collCcPrefs as $obj) {
                        if (false == $this->collCcPrefs->contains($obj)) {
                          $this->collCcPrefs->append($obj);
                        }
                      }

                      $this->collCcPrefsPartial = true;
                    }

                    $collCcPrefs->getInternalIterator()->rewind();

                    return $collCcPrefs;
                }

                if ($partial && $this->collCcPrefs) {
                    foreach ($this->collCcPrefs as $obj) {
                        if ($obj->isNew()) {
                            $collCcPrefs[] = $obj;
                        }
                    }
                }

                $this->collCcPrefs = $collCcPrefs;
                $this->collCcPrefsPartial = false;
            }
        }

        return $this->collCcPrefs;
    }

    /**
     * Sets a collection of CcPref objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccPrefs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcPrefs(PropelCollection $ccPrefs, PropelPDO $con = null)
    {
        $ccPrefsToDelete = $this->getCcPrefs(new Criteria(), $con)->diff($ccPrefs);


        $this->ccPrefsScheduledForDeletion = $ccPrefsToDelete;

        foreach ($ccPrefsToDelete as $ccPrefRemoved) {
            $ccPrefRemoved->setCcSubjs(null);
        }

        $this->collCcPrefs = null;
        foreach ($ccPrefs as $ccPref) {
            $this->addCcPref($ccPref);
        }

        $this->collCcPrefs = $ccPrefs;
        $this->collCcPrefsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcPref objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcPref objects.
     * @throws PropelException
     */
    public function countCcPrefs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcPrefsPartial && !$this->isNew();
        if (null === $this->collCcPrefs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcPrefs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcPrefs());
            }
            $query = CcPrefQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collCcPrefs);
    }

    /**
     * Method called to associate a CcPref object to this object
     * through the CcPref foreign key attribute.
     *
     * @param    CcPref $l CcPref
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcPref(CcPref $l)
    {
        if ($this->collCcPrefs === null) {
            $this->initCcPrefs();
            $this->collCcPrefsPartial = true;
        }

        if (!in_array($l, $this->collCcPrefs->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcPref($l);

            if ($this->ccPrefsScheduledForDeletion and $this->ccPrefsScheduledForDeletion->contains($l)) {
                $this->ccPrefsScheduledForDeletion->remove($this->ccPrefsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcPref $ccPref The ccPref object to add.
     */
    protected function doAddCcPref($ccPref)
    {
        $this->collCcPrefs[]= $ccPref;
        $ccPref->setCcSubjs($this);
    }

    /**
     * @param	CcPref $ccPref The ccPref object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcPref($ccPref)
    {
        if ($this->getCcPrefs()->contains($ccPref)) {
            $this->collCcPrefs->remove($this->collCcPrefs->search($ccPref));
            if (null === $this->ccPrefsScheduledForDeletion) {
                $this->ccPrefsScheduledForDeletion = clone $this->collCcPrefs;
                $this->ccPrefsScheduledForDeletion->clear();
            }
            $this->ccPrefsScheduledForDeletion[]= $ccPref;
            $ccPref->setCcSubjs(null);
        }

        return $this;
    }

    /**
     * Clears out the collCcSubjsTokens collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addCcSubjsTokens()
     */
    public function clearCcSubjsTokens()
    {
        $this->collCcSubjsTokens = null; // important to set this to null since that means it is uninitialized
        $this->collCcSubjsTokensPartial = null;

        return $this;
    }

    /**
     * reset is the collCcSubjsTokens collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcSubjsTokens($v = true)
    {
        $this->collCcSubjsTokensPartial = $v;
    }

    /**
     * Initializes the collCcSubjsTokens collection.
     *
     * By default this just sets the collCcSubjsTokens collection to an empty array (like clearcollCcSubjsTokens());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcSubjsTokens($overrideExisting = true)
    {
        if (null !== $this->collCcSubjsTokens && !$overrideExisting) {
            return;
        }
        $this->collCcSubjsTokens = new PropelObjectCollection();
        $this->collCcSubjsTokens->setModel('CcSubjsToken');
    }

    /**
     * Gets an array of CcSubjsToken objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcSubjsToken[] List of CcSubjsToken objects
     * @throws PropelException
     */
    public function getCcSubjsTokens($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcSubjsTokensPartial && !$this->isNew();
        if (null === $this->collCcSubjsTokens || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcSubjsTokens) {
                // return empty collection
                $this->initCcSubjsTokens();
            } else {
                $collCcSubjsTokens = CcSubjsTokenQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcSubjsTokensPartial && count($collCcSubjsTokens)) {
                      $this->initCcSubjsTokens(false);

                      foreach ($collCcSubjsTokens as $obj) {
                        if (false == $this->collCcSubjsTokens->contains($obj)) {
                          $this->collCcSubjsTokens->append($obj);
                        }
                      }

                      $this->collCcSubjsTokensPartial = true;
                    }

                    $collCcSubjsTokens->getInternalIterator()->rewind();

                    return $collCcSubjsTokens;
                }

                if ($partial && $this->collCcSubjsTokens) {
                    foreach ($this->collCcSubjsTokens as $obj) {
                        if ($obj->isNew()) {
                            $collCcSubjsTokens[] = $obj;
                        }
                    }
                }

                $this->collCcSubjsTokens = $collCcSubjsTokens;
                $this->collCcSubjsTokensPartial = false;
            }
        }

        return $this->collCcSubjsTokens;
    }

    /**
     * Sets a collection of CcSubjsToken objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccSubjsTokens A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setCcSubjsTokens(PropelCollection $ccSubjsTokens, PropelPDO $con = null)
    {
        $ccSubjsTokensToDelete = $this->getCcSubjsTokens(new Criteria(), $con)->diff($ccSubjsTokens);


        $this->ccSubjsTokensScheduledForDeletion = $ccSubjsTokensToDelete;

        foreach ($ccSubjsTokensToDelete as $ccSubjsTokenRemoved) {
            $ccSubjsTokenRemoved->setCcSubjs(null);
        }

        $this->collCcSubjsTokens = null;
        foreach ($ccSubjsTokens as $ccSubjsToken) {
            $this->addCcSubjsToken($ccSubjsToken);
        }

        $this->collCcSubjsTokens = $ccSubjsTokens;
        $this->collCcSubjsTokensPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcSubjsToken objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcSubjsToken objects.
     * @throws PropelException
     */
    public function countCcSubjsTokens(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcSubjsTokensPartial && !$this->isNew();
        if (null === $this->collCcSubjsTokens || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcSubjsTokens) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcSubjsTokens());
            }
            $query = CcSubjsTokenQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collCcSubjsTokens);
    }

    /**
     * Method called to associate a CcSubjsToken object to this object
     * through the CcSubjsToken foreign key attribute.
     *
     * @param    CcSubjsToken $l CcSubjsToken
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addCcSubjsToken(CcSubjsToken $l)
    {
        if ($this->collCcSubjsTokens === null) {
            $this->initCcSubjsTokens();
            $this->collCcSubjsTokensPartial = true;
        }

        if (!in_array($l, $this->collCcSubjsTokens->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcSubjsToken($l);

            if ($this->ccSubjsTokensScheduledForDeletion and $this->ccSubjsTokensScheduledForDeletion->contains($l)) {
                $this->ccSubjsTokensScheduledForDeletion->remove($this->ccSubjsTokensScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcSubjsToken $ccSubjsToken The ccSubjsToken object to add.
     */
    protected function doAddCcSubjsToken($ccSubjsToken)
    {
        $this->collCcSubjsTokens[]= $ccSubjsToken;
        $ccSubjsToken->setCcSubjs($this);
    }

    /**
     * @param	CcSubjsToken $ccSubjsToken The ccSubjsToken object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeCcSubjsToken($ccSubjsToken)
    {
        if ($this->getCcSubjsTokens()->contains($ccSubjsToken)) {
            $this->collCcSubjsTokens->remove($this->collCcSubjsTokens->search($ccSubjsToken));
            if (null === $this->ccSubjsTokensScheduledForDeletion) {
                $this->ccSubjsTokensScheduledForDeletion = clone $this->collCcSubjsTokens;
                $this->ccSubjsTokensScheduledForDeletion->clear();
            }
            $this->ccSubjsTokensScheduledForDeletion[]= clone $ccSubjsToken;
            $ccSubjsToken->setCcSubjs(null);
        }

        return $this;
    }

    /**
     * Clears out the collMediaItems collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addMediaItems()
     */
    public function clearMediaItems()
    {
        $this->collMediaItems = null; // important to set this to null since that means it is uninitialized
        $this->collMediaItemsPartial = null;

        return $this;
    }

    /**
     * reset is the collMediaItems collection loaded partially
     *
     * @return void
     */
    public function resetPartialMediaItems($v = true)
    {
        $this->collMediaItemsPartial = $v;
    }

    /**
     * Initializes the collMediaItems collection.
     *
     * By default this just sets the collMediaItems collection to an empty array (like clearcollMediaItems());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMediaItems($overrideExisting = true)
    {
        if (null !== $this->collMediaItems && !$overrideExisting) {
            return;
        }
        $this->collMediaItems = new PropelObjectCollection();
        $this->collMediaItems->setModel('MediaItem');
    }

    /**
     * Gets an array of MediaItem objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|MediaItem[] List of MediaItem objects
     * @throws PropelException
     */
    public function getMediaItems($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collMediaItemsPartial && !$this->isNew();
        if (null === $this->collMediaItems || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMediaItems) {
                // return empty collection
                $this->initMediaItems();
            } else {
                $collMediaItems = MediaItemQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collMediaItemsPartial && count($collMediaItems)) {
                      $this->initMediaItems(false);

                      foreach ($collMediaItems as $obj) {
                        if (false == $this->collMediaItems->contains($obj)) {
                          $this->collMediaItems->append($obj);
                        }
                      }

                      $this->collMediaItemsPartial = true;
                    }

                    $collMediaItems->getInternalIterator()->rewind();

                    return $collMediaItems;
                }

                if ($partial && $this->collMediaItems) {
                    foreach ($this->collMediaItems as $obj) {
                        if ($obj->isNew()) {
                            $collMediaItems[] = $obj;
                        }
                    }
                }

                $this->collMediaItems = $collMediaItems;
                $this->collMediaItemsPartial = false;
            }
        }

        return $this->collMediaItems;
    }

    /**
     * Sets a collection of MediaItem objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $mediaItems A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setMediaItems(PropelCollection $mediaItems, PropelPDO $con = null)
    {
        $mediaItemsToDelete = $this->getMediaItems(new Criteria(), $con)->diff($mediaItems);


        $this->mediaItemsScheduledForDeletion = $mediaItemsToDelete;

        foreach ($mediaItemsToDelete as $mediaItemRemoved) {
            $mediaItemRemoved->setCcSubjs(null);
        }

        $this->collMediaItems = null;
        foreach ($mediaItems as $mediaItem) {
            $this->addMediaItem($mediaItem);
        }

        $this->collMediaItems = $mediaItems;
        $this->collMediaItemsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MediaItem objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related MediaItem objects.
     * @throws PropelException
     */
    public function countMediaItems(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collMediaItemsPartial && !$this->isNew();
        if (null === $this->collMediaItems || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMediaItems) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMediaItems());
            }
            $query = MediaItemQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collMediaItems);
    }

    /**
     * Method called to associate a MediaItem object to this object
     * through the MediaItem foreign key attribute.
     *
     * @param    MediaItem $l MediaItem
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addMediaItem(MediaItem $l)
    {
        if ($this->collMediaItems === null) {
            $this->initMediaItems();
            $this->collMediaItemsPartial = true;
        }

        if (!in_array($l, $this->collMediaItems->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMediaItem($l);

            if ($this->mediaItemsScheduledForDeletion and $this->mediaItemsScheduledForDeletion->contains($l)) {
                $this->mediaItemsScheduledForDeletion->remove($this->mediaItemsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	MediaItem $mediaItem The mediaItem object to add.
     */
    protected function doAddMediaItem($mediaItem)
    {
        $this->collMediaItems[]= $mediaItem;
        $mediaItem->setCcSubjs($this);
    }

    /**
     * @param	MediaItem $mediaItem The mediaItem object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeMediaItem($mediaItem)
    {
        if ($this->getMediaItems()->contains($mediaItem)) {
            $this->collMediaItems->remove($this->collMediaItems->search($mediaItem));
            if (null === $this->mediaItemsScheduledForDeletion) {
                $this->mediaItemsScheduledForDeletion = clone $this->collMediaItems;
                $this->mediaItemsScheduledForDeletion->clear();
            }
            $this->mediaItemsScheduledForDeletion[]= $mediaItem;
            $mediaItem->setCcSubjs(null);
        }

        return $this;
    }

    /**
     * Clears out the collAudioFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
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
     * If this CcSubjs is new, it will return
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
                    ->filterByCcSubjs($this)
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
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setAudioFiles(PropelCollection $audioFiles, PropelPDO $con = null)
    {
        $audioFilesToDelete = $this->getAudioFiles(new Criteria(), $con)->diff($audioFiles);


        $this->audioFilesScheduledForDeletion = $audioFilesToDelete;

        foreach ($audioFilesToDelete as $audioFileRemoved) {
            $audioFileRemoved->setCcSubjs(null);
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
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collAudioFiles);
    }

    /**
     * Method called to associate a AudioFile object to this object
     * through the AudioFile foreign key attribute.
     *
     * @param    AudioFile $l AudioFile
     * @return CcSubjs The current object (for fluent API support)
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
        $audioFile->setCcSubjs($this);
    }

    /**
     * @param	AudioFile $audioFile The audioFile object to remove.
     * @return CcSubjs The current object (for fluent API support)
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
            $audioFile->setCcSubjs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related AudioFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AudioFile[] List of AudioFile objects
     */
    public function getAudioFilesJoinCcMusicDirs($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AudioFileQuery::create(null, $criteria);
        $query->joinWith('CcMusicDirs', $join_behavior);

        return $this->getAudioFiles($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related AudioFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
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
     * Clears out the collWebstreams collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addWebstreams()
     */
    public function clearWebstreams()
    {
        $this->collWebstreams = null; // important to set this to null since that means it is uninitialized
        $this->collWebstreamsPartial = null;

        return $this;
    }

    /**
     * reset is the collWebstreams collection loaded partially
     *
     * @return void
     */
    public function resetPartialWebstreams($v = true)
    {
        $this->collWebstreamsPartial = $v;
    }

    /**
     * Initializes the collWebstreams collection.
     *
     * By default this just sets the collWebstreams collection to an empty array (like clearcollWebstreams());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWebstreams($overrideExisting = true)
    {
        if (null !== $this->collWebstreams && !$overrideExisting) {
            return;
        }
        $this->collWebstreams = new PropelObjectCollection();
        $this->collWebstreams->setModel('Webstream');
    }

    /**
     * Gets an array of Webstream objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Webstream[] List of Webstream objects
     * @throws PropelException
     */
    public function getWebstreams($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWebstreamsPartial && !$this->isNew();
        if (null === $this->collWebstreams || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWebstreams) {
                // return empty collection
                $this->initWebstreams();
            } else {
                $collWebstreams = WebstreamQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWebstreamsPartial && count($collWebstreams)) {
                      $this->initWebstreams(false);

                      foreach ($collWebstreams as $obj) {
                        if (false == $this->collWebstreams->contains($obj)) {
                          $this->collWebstreams->append($obj);
                        }
                      }

                      $this->collWebstreamsPartial = true;
                    }

                    $collWebstreams->getInternalIterator()->rewind();

                    return $collWebstreams;
                }

                if ($partial && $this->collWebstreams) {
                    foreach ($this->collWebstreams as $obj) {
                        if ($obj->isNew()) {
                            $collWebstreams[] = $obj;
                        }
                    }
                }

                $this->collWebstreams = $collWebstreams;
                $this->collWebstreamsPartial = false;
            }
        }

        return $this->collWebstreams;
    }

    /**
     * Sets a collection of Webstream objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $webstreams A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setWebstreams(PropelCollection $webstreams, PropelPDO $con = null)
    {
        $webstreamsToDelete = $this->getWebstreams(new Criteria(), $con)->diff($webstreams);


        $this->webstreamsScheduledForDeletion = $webstreamsToDelete;

        foreach ($webstreamsToDelete as $webstreamRemoved) {
            $webstreamRemoved->setCcSubjs(null);
        }

        $this->collWebstreams = null;
        foreach ($webstreams as $webstream) {
            $this->addWebstream($webstream);
        }

        $this->collWebstreams = $webstreams;
        $this->collWebstreamsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Webstream objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Webstream objects.
     * @throws PropelException
     */
    public function countWebstreams(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWebstreamsPartial && !$this->isNew();
        if (null === $this->collWebstreams || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWebstreams) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWebstreams());
            }
            $query = WebstreamQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collWebstreams);
    }

    /**
     * Method called to associate a Webstream object to this object
     * through the Webstream foreign key attribute.
     *
     * @param    Webstream $l Webstream
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addWebstream(Webstream $l)
    {
        if ($this->collWebstreams === null) {
            $this->initWebstreams();
            $this->collWebstreamsPartial = true;
        }

        if (!in_array($l, $this->collWebstreams->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWebstream($l);

            if ($this->webstreamsScheduledForDeletion and $this->webstreamsScheduledForDeletion->contains($l)) {
                $this->webstreamsScheduledForDeletion->remove($this->webstreamsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Webstream $webstream The webstream object to add.
     */
    protected function doAddWebstream($webstream)
    {
        $this->collWebstreams[]= $webstream;
        $webstream->setCcSubjs($this);
    }

    /**
     * @param	Webstream $webstream The webstream object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removeWebstream($webstream)
    {
        if ($this->getWebstreams()->contains($webstream)) {
            $this->collWebstreams->remove($this->collWebstreams->search($webstream));
            if (null === $this->webstreamsScheduledForDeletion) {
                $this->webstreamsScheduledForDeletion = clone $this->collWebstreams;
                $this->webstreamsScheduledForDeletion->clear();
            }
            $this->webstreamsScheduledForDeletion[]= $webstream;
            $webstream->setCcSubjs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related Webstreams from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Webstream[] List of Webstream objects
     */
    public function getWebstreamsJoinMediaItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WebstreamQuery::create(null, $criteria);
        $query->joinWith('MediaItem', $join_behavior);

        return $this->getWebstreams($query, $con);
    }

    /**
     * Clears out the collPlaylists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcSubjs The current object (for fluent API support)
     * @see        addPlaylists()
     */
    public function clearPlaylists()
    {
        $this->collPlaylists = null; // important to set this to null since that means it is uninitialized
        $this->collPlaylistsPartial = null;

        return $this;
    }

    /**
     * reset is the collPlaylists collection loaded partially
     *
     * @return void
     */
    public function resetPartialPlaylists($v = true)
    {
        $this->collPlaylistsPartial = $v;
    }

    /**
     * Initializes the collPlaylists collection.
     *
     * By default this just sets the collPlaylists collection to an empty array (like clearcollPlaylists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPlaylists($overrideExisting = true)
    {
        if (null !== $this->collPlaylists && !$overrideExisting) {
            return;
        }
        $this->collPlaylists = new PropelObjectCollection();
        $this->collPlaylists->setModel('Playlist');
    }

    /**
     * Gets an array of Playlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcSubjs is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Playlist[] List of Playlist objects
     * @throws PropelException
     */
    public function getPlaylists($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPlaylistsPartial && !$this->isNew();
        if (null === $this->collPlaylists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPlaylists) {
                // return empty collection
                $this->initPlaylists();
            } else {
                $collPlaylists = PlaylistQuery::create(null, $criteria)
                    ->filterByCcSubjs($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPlaylistsPartial && count($collPlaylists)) {
                      $this->initPlaylists(false);

                      foreach ($collPlaylists as $obj) {
                        if (false == $this->collPlaylists->contains($obj)) {
                          $this->collPlaylists->append($obj);
                        }
                      }

                      $this->collPlaylistsPartial = true;
                    }

                    $collPlaylists->getInternalIterator()->rewind();

                    return $collPlaylists;
                }

                if ($partial && $this->collPlaylists) {
                    foreach ($this->collPlaylists as $obj) {
                        if ($obj->isNew()) {
                            $collPlaylists[] = $obj;
                        }
                    }
                }

                $this->collPlaylists = $collPlaylists;
                $this->collPlaylistsPartial = false;
            }
        }

        return $this->collPlaylists;
    }

    /**
     * Sets a collection of Playlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $playlists A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcSubjs The current object (for fluent API support)
     */
    public function setPlaylists(PropelCollection $playlists, PropelPDO $con = null)
    {
        $playlistsToDelete = $this->getPlaylists(new Criteria(), $con)->diff($playlists);


        $this->playlistsScheduledForDeletion = $playlistsToDelete;

        foreach ($playlistsToDelete as $playlistRemoved) {
            $playlistRemoved->setCcSubjs(null);
        }

        $this->collPlaylists = null;
        foreach ($playlists as $playlist) {
            $this->addPlaylist($playlist);
        }

        $this->collPlaylists = $playlists;
        $this->collPlaylistsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Playlist objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Playlist objects.
     * @throws PropelException
     */
    public function countPlaylists(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPlaylistsPartial && !$this->isNew();
        if (null === $this->collPlaylists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPlaylists) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPlaylists());
            }
            $query = PlaylistQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcSubjs($this)
                ->count($con);
        }

        return count($this->collPlaylists);
    }

    /**
     * Method called to associate a Playlist object to this object
     * through the Playlist foreign key attribute.
     *
     * @param    Playlist $l Playlist
     * @return CcSubjs The current object (for fluent API support)
     */
    public function addPlaylist(Playlist $l)
    {
        if ($this->collPlaylists === null) {
            $this->initPlaylists();
            $this->collPlaylistsPartial = true;
        }

        if (!in_array($l, $this->collPlaylists->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPlaylist($l);

            if ($this->playlistsScheduledForDeletion and $this->playlistsScheduledForDeletion->contains($l)) {
                $this->playlistsScheduledForDeletion->remove($this->playlistsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Playlist $playlist The playlist object to add.
     */
    protected function doAddPlaylist($playlist)
    {
        $this->collPlaylists[]= $playlist;
        $playlist->setCcSubjs($this);
    }

    /**
     * @param	Playlist $playlist The playlist object to remove.
     * @return CcSubjs The current object (for fluent API support)
     */
    public function removePlaylist($playlist)
    {
        if ($this->getPlaylists()->contains($playlist)) {
            $this->collPlaylists->remove($this->collPlaylists->search($playlist));
            if (null === $this->playlistsScheduledForDeletion) {
                $this->playlistsScheduledForDeletion = clone $this->collPlaylists;
                $this->playlistsScheduledForDeletion->clear();
            }
            $this->playlistsScheduledForDeletion[]= $playlist;
            $playlist->setCcSubjs(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcSubjs is new, it will return
     * an empty collection; or if this CcSubjs has previously
     * been saved, it will retrieve related Playlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcSubjs.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Playlist[] List of Playlist objects
     */
    public function getPlaylistsJoinMediaItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PlaylistQuery::create(null, $criteria);
        $query->joinWith('MediaItem', $join_behavior);

        return $this->getPlaylists($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->login = null;
        $this->pass = null;
        $this->type = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->lastlogin = null;
        $this->lastfail = null;
        $this->skype_contact = null;
        $this->jabber_contact = null;
        $this->email = null;
        $this->cell_phone = null;
        $this->login_attempts = null;
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
            if ($this->collCcFilessRelatedByDbOwnerId) {
                foreach ($this->collCcFilessRelatedByDbOwnerId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcFilessRelatedByDbEditedby) {
                foreach ($this->collCcFilessRelatedByDbEditedby as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcShowHostss) {
                foreach ($this->collCcShowHostss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcPlaylists) {
                foreach ($this->collCcPlaylists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcBlocks) {
                foreach ($this->collCcBlocks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcPrefs) {
                foreach ($this->collCcPrefs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcSubjsTokens) {
                foreach ($this->collCcSubjsTokens as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMediaItems) {
                foreach ($this->collMediaItems as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAudioFiles) {
                foreach ($this->collAudioFiles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWebstreams) {
                foreach ($this->collWebstreams as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPlaylists) {
                foreach ($this->collPlaylists as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcFilessRelatedByDbOwnerId instanceof PropelCollection) {
            $this->collCcFilessRelatedByDbOwnerId->clearIterator();
        }
        $this->collCcFilessRelatedByDbOwnerId = null;
        if ($this->collCcFilessRelatedByDbEditedby instanceof PropelCollection) {
            $this->collCcFilessRelatedByDbEditedby->clearIterator();
        }
        $this->collCcFilessRelatedByDbEditedby = null;
        if ($this->collCcShowHostss instanceof PropelCollection) {
            $this->collCcShowHostss->clearIterator();
        }
        $this->collCcShowHostss = null;
        if ($this->collCcPlaylists instanceof PropelCollection) {
            $this->collCcPlaylists->clearIterator();
        }
        $this->collCcPlaylists = null;
        if ($this->collCcBlocks instanceof PropelCollection) {
            $this->collCcBlocks->clearIterator();
        }
        $this->collCcBlocks = null;
        if ($this->collCcPrefs instanceof PropelCollection) {
            $this->collCcPrefs->clearIterator();
        }
        $this->collCcPrefs = null;
        if ($this->collCcSubjsTokens instanceof PropelCollection) {
            $this->collCcSubjsTokens->clearIterator();
        }
        $this->collCcSubjsTokens = null;
        if ($this->collMediaItems instanceof PropelCollection) {
            $this->collMediaItems->clearIterator();
        }
        $this->collMediaItems = null;
        if ($this->collAudioFiles instanceof PropelCollection) {
            $this->collAudioFiles->clearIterator();
        }
        $this->collAudioFiles = null;
        if ($this->collWebstreams instanceof PropelCollection) {
            $this->collWebstreams->clearIterator();
        }
        $this->collWebstreams = null;
        if ($this->collPlaylists instanceof PropelCollection) {
            $this->collPlaylists->clearIterator();
        }
        $this->collPlaylists = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcSubjsPeer::DEFAULT_STRING_FORMAT);
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
