<?php


/**
 * Base class that represents a row from the 'cc_show' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShow extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'CcShowPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CcShowPeer
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
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $name;

    /**
     * The value for the url field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $url;

    /**
     * The value for the genre field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $genre;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the color field.
     * @var        string
     */
    protected $color;

    /**
     * The value for the background_color field.
     * @var        string
     */
    protected $background_color;

    /**
     * The value for the live_stream_using_airtime_auth field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $live_stream_using_airtime_auth;

    /**
     * The value for the live_stream_using_custom_auth field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $live_stream_using_custom_auth;

    /**
     * The value for the live_stream_user field.
     * @var        string
     */
    protected $live_stream_user;

    /**
     * The value for the live_stream_pass field.
     * @var        string
     */
    protected $live_stream_pass;

    /**
     * The value for the linked field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $linked;

    /**
     * The value for the is_linkable field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $is_linkable;

    /**
     * The value for the image_path field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $image_path;

    /**
     * The value for the has_autoplaylist field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $has_autoplaylist;

    /**
     * The value for the autoplaylist_id field.
     * @var        int
     */
    protected $autoplaylist_id;

    /**
     * The value for the autoplaylist_repeat field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $autoplaylist_repeat;

    /**
     * The value for the intro_playlist_id field.
     * @var        int
     */
    protected $intro_playlist_id;

    /**
     * The value for the outro_playlist_id field.
     * @var        int
     */
    protected $outro_playlist_id;

    /**
     * @var        CcPlaylist
     */
    protected $aCcPlaylistRelatedByDbAutoPlaylistId;

    /**
     * @var        CcPlaylist
     */
    protected $aCcPlaylistRelatedByDbIntroPlaylistId;

    /**
     * @var        CcPlaylist
     */
    protected $aCcPlaylistRelatedByDbOutroPlaylistId;

    /**
     * @var        PropelObjectCollection|CcShowInstances[] Collection to store aggregation of CcShowInstances objects.
     */
    protected $collCcShowInstancess;
    protected $collCcShowInstancessPartial;

    /**
     * @var        PropelObjectCollection|CcShowDays[] Collection to store aggregation of CcShowDays objects.
     */
    protected $collCcShowDayss;
    protected $collCcShowDayssPartial;

    /**
     * @var        PropelObjectCollection|CcShowRebroadcast[] Collection to store aggregation of CcShowRebroadcast objects.
     */
    protected $collCcShowRebroadcasts;
    protected $collCcShowRebroadcastsPartial;

    /**
     * @var        PropelObjectCollection|CcShowHosts[] Collection to store aggregation of CcShowHosts objects.
     */
    protected $collCcShowHostss;
    protected $collCcShowHostssPartial;

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
    protected $ccShowDayssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccShowRebroadcastsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $ccShowHostssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->name = '';
        $this->url = '';
        $this->genre = '';
        $this->live_stream_using_airtime_auth = false;
        $this->live_stream_using_custom_auth = false;
        $this->linked = false;
        $this->is_linkable = true;
        $this->image_path = '';
        $this->has_autoplaylist = false;
        $this->autoplaylist_repeat = false;
    }

    /**
     * Initializes internal state of BaseCcShow object.
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getDbName()
    {

        return $this->name;
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
     * Get the [genre] column value.
     *
     * @return string
     */
    public function getDbGenre()
    {

        return $this->genre;
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
     * Get the [color] column value.
     *
     * @return string
     */
    public function getDbColor()
    {

        return $this->color;
    }

    /**
     * Get the [background_color] column value.
     *
     * @return string
     */
    public function getDbBackgroundColor()
    {

        return $this->background_color;
    }

    /**
     * Get the [live_stream_using_airtime_auth] column value.
     *
     * @return boolean
     */
    public function getDbLiveStreamUsingAirtimeAuth()
    {

        return $this->live_stream_using_airtime_auth;
    }

    /**
     * Get the [live_stream_using_custom_auth] column value.
     *
     * @return boolean
     */
    public function getDbLiveStreamUsingCustomAuth()
    {

        return $this->live_stream_using_custom_auth;
    }

    /**
     * Get the [live_stream_user] column value.
     *
     * @return string
     */
    public function getDbLiveStreamUser()
    {

        return $this->live_stream_user;
    }

    /**
     * Get the [live_stream_pass] column value.
     *
     * @return string
     */
    public function getDbLiveStreamPass()
    {

        return $this->live_stream_pass;
    }

    /**
     * Get the [linked] column value.
     *
     * @return boolean
     */
    public function getDbLinked()
    {

        return $this->linked;
    }

    /**
     * Get the [is_linkable] column value.
     *
     * @return boolean
     */
    public function getDbIsLinkable()
    {

        return $this->is_linkable;
    }

    /**
     * Get the [image_path] column value.
     *
     * @return string
     */
    public function getDbImagePath()
    {

        return $this->image_path;
    }

    /**
     * Get the [has_autoplaylist] column value.
     *
     * @return boolean
     */
    public function getDbHasAutoPlaylist()
    {

        return $this->has_autoplaylist;
    }

    /**
     * Get the [autoplaylist_id] column value.
     *
     * @return int
     */
    public function getDbAutoPlaylistId()
    {

        return $this->autoplaylist_id;
    }

    /**
     * Get the [autoplaylist_repeat] column value.
     *
     * @return boolean
     */
    public function getDbAutoPlaylistRepeat()
    {

        return $this->autoplaylist_repeat;
    }

    /**
     * Get the [intro_playlist_id] column value.
     *
     * @return int
     */
    public function getDbIntroPlaylistId()
    {

        return $this->intro_playlist_id;
    }

    /**
     * Get the [outro_playlist_id] column value.
     *
     * @return int
     */
    public function getDbOutroPlaylistId()
    {

        return $this->outro_playlist_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CcShowPeer::ID;
        }


        return $this;
    } // setDbId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CcShowPeer::NAME;
        }


        return $this;
    } // setDbName()

    /**
     * Set the value of [url] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = CcShowPeer::URL;
        }


        return $this;
    } // setDbUrl()

    /**
     * Set the value of [genre] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbGenre($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->genre !== $v) {
            $this->genre = $v;
            $this->modifiedColumns[] = CcShowPeer::GENRE;
        }


        return $this;
    } // setDbGenre()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CcShowPeer::DESCRIPTION;
        }


        return $this;
    } // setDbDescription()

    /**
     * Set the value of [color] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = CcShowPeer::COLOR;
        }


        return $this;
    } // setDbColor()

    /**
     * Set the value of [background_color] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbBackgroundColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->background_color !== $v) {
            $this->background_color = $v;
            $this->modifiedColumns[] = CcShowPeer::BACKGROUND_COLOR;
        }


        return $this;
    } // setDbBackgroundColor()

    /**
     * Sets the value of the [live_stream_using_airtime_auth] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbLiveStreamUsingAirtimeAuth($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->live_stream_using_airtime_auth !== $v) {
            $this->live_stream_using_airtime_auth = $v;
            $this->modifiedColumns[] = CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH;
        }


        return $this;
    } // setDbLiveStreamUsingAirtimeAuth()

    /**
     * Sets the value of the [live_stream_using_custom_auth] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbLiveStreamUsingCustomAuth($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->live_stream_using_custom_auth !== $v) {
            $this->live_stream_using_custom_auth = $v;
            $this->modifiedColumns[] = CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH;
        }


        return $this;
    } // setDbLiveStreamUsingCustomAuth()

    /**
     * Set the value of [live_stream_user] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbLiveStreamUser($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->live_stream_user !== $v) {
            $this->live_stream_user = $v;
            $this->modifiedColumns[] = CcShowPeer::LIVE_STREAM_USER;
        }


        return $this;
    } // setDbLiveStreamUser()

    /**
     * Set the value of [live_stream_pass] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbLiveStreamPass($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->live_stream_pass !== $v) {
            $this->live_stream_pass = $v;
            $this->modifiedColumns[] = CcShowPeer::LIVE_STREAM_PASS;
        }


        return $this;
    } // setDbLiveStreamPass()

    /**
     * Sets the value of the [linked] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbLinked($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->linked !== $v) {
            $this->linked = $v;
            $this->modifiedColumns[] = CcShowPeer::LINKED;
        }


        return $this;
    } // setDbLinked()

    /**
     * Sets the value of the [is_linkable] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbIsLinkable($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_linkable !== $v) {
            $this->is_linkable = $v;
            $this->modifiedColumns[] = CcShowPeer::IS_LINKABLE;
        }


        return $this;
    } // setDbIsLinkable()

    /**
     * Set the value of [image_path] column.
     *
     * @param  string $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbImagePath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->image_path !== $v) {
            $this->image_path = $v;
            $this->modifiedColumns[] = CcShowPeer::IMAGE_PATH;
        }


        return $this;
    } // setDbImagePath()

    /**
     * Sets the value of the [has_autoplaylist] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbHasAutoPlaylist($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->has_autoplaylist !== $v) {
            $this->has_autoplaylist = $v;
            $this->modifiedColumns[] = CcShowPeer::HAS_AUTOPLAYLIST;
        }


        return $this;
    } // setDbHasAutoPlaylist()

    /**
     * Set the value of [autoplaylist_id] column.
     *
     * @param  int $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbAutoPlaylistId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->autoplaylist_id !== $v) {
            $this->autoplaylist_id = $v;
            $this->modifiedColumns[] = CcShowPeer::AUTOPLAYLIST_ID;
        }

        if ($this->aCcPlaylistRelatedByDbAutoPlaylistId !== null && $this->aCcPlaylistRelatedByDbAutoPlaylistId->getDbId() !== $v) {
            $this->aCcPlaylistRelatedByDbAutoPlaylistId = null;
        }


        return $this;
    } // setDbAutoPlaylistId()

    /**
     * Sets the value of the [autoplaylist_repeat] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbAutoPlaylistRepeat($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->autoplaylist_repeat !== $v) {
            $this->autoplaylist_repeat = $v;
            $this->modifiedColumns[] = CcShowPeer::AUTOPLAYLIST_REPEAT;
        }


        return $this;
    } // setDbAutoPlaylistRepeat()

    /**
     * Set the value of [intro_playlist_id] column.
     *
     * @param  int $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbIntroPlaylistId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->intro_playlist_id !== $v) {
            $this->intro_playlist_id = $v;
            $this->modifiedColumns[] = CcShowPeer::INTRO_PLAYLIST_ID;
        }

        if ($this->aCcPlaylistRelatedByDbIntroPlaylistId !== null && $this->aCcPlaylistRelatedByDbIntroPlaylistId->getDbId() !== $v) {
            $this->aCcPlaylistRelatedByDbIntroPlaylistId = null;
        }


        return $this;
    } // setDbIntroPlaylistId()

    /**
     * Set the value of [outro_playlist_id] column.
     *
     * @param  int $v new value
     * @return CcShow The current object (for fluent API support)
     */
    public function setDbOutroPlaylistId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->outro_playlist_id !== $v) {
            $this->outro_playlist_id = $v;
            $this->modifiedColumns[] = CcShowPeer::OUTRO_PLAYLIST_ID;
        }

        if ($this->aCcPlaylistRelatedByDbOutroPlaylistId !== null && $this->aCcPlaylistRelatedByDbOutroPlaylistId->getDbId() !== $v) {
            $this->aCcPlaylistRelatedByDbOutroPlaylistId = null;
        }


        return $this;
    } // setDbOutroPlaylistId()

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
            if ($this->name !== '') {
                return false;
            }

            if ($this->url !== '') {
                return false;
            }

            if ($this->genre !== '') {
                return false;
            }

            if ($this->live_stream_using_airtime_auth !== false) {
                return false;
            }

            if ($this->live_stream_using_custom_auth !== false) {
                return false;
            }

            if ($this->linked !== false) {
                return false;
            }

            if ($this->is_linkable !== true) {
                return false;
            }

            if ($this->image_path !== '') {
                return false;
            }

            if ($this->has_autoplaylist !== false) {
                return false;
            }

            if ($this->autoplaylist_repeat !== false) {
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
            $this->url = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->genre = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->color = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->background_color = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->live_stream_using_airtime_auth = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->live_stream_using_custom_auth = ($row[$startcol + 8] !== null) ? (boolean) $row[$startcol + 8] : null;
            $this->live_stream_user = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->live_stream_pass = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->linked = ($row[$startcol + 11] !== null) ? (boolean) $row[$startcol + 11] : null;
            $this->is_linkable = ($row[$startcol + 12] !== null) ? (boolean) $row[$startcol + 12] : null;
            $this->image_path = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->has_autoplaylist = ($row[$startcol + 14] !== null) ? (boolean) $row[$startcol + 14] : null;
            $this->autoplaylist_id = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
            $this->autoplaylist_repeat = ($row[$startcol + 16] !== null) ? (boolean) $row[$startcol + 16] : null;
            $this->intro_playlist_id = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
            $this->outro_playlist_id = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 19; // 19 = CcShowPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CcShow object", $e);
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

        if ($this->aCcPlaylistRelatedByDbAutoPlaylistId !== null && $this->autoplaylist_id !== $this->aCcPlaylistRelatedByDbAutoPlaylistId->getDbId()) {
            $this->aCcPlaylistRelatedByDbAutoPlaylistId = null;
        }
        if ($this->aCcPlaylistRelatedByDbIntroPlaylistId !== null && $this->intro_playlist_id !== $this->aCcPlaylistRelatedByDbIntroPlaylistId->getDbId()) {
            $this->aCcPlaylistRelatedByDbIntroPlaylistId = null;
        }
        if ($this->aCcPlaylistRelatedByDbOutroPlaylistId !== null && $this->outro_playlist_id !== $this->aCcPlaylistRelatedByDbOutroPlaylistId->getDbId()) {
            $this->aCcPlaylistRelatedByDbOutroPlaylistId = null;
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
            $con = Propel::getConnection(CcShowPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CcShowPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCcPlaylistRelatedByDbAutoPlaylistId = null;
            $this->aCcPlaylistRelatedByDbIntroPlaylistId = null;
            $this->aCcPlaylistRelatedByDbOutroPlaylistId = null;
            $this->collCcShowInstancess = null;

            $this->collCcShowDayss = null;

            $this->collCcShowRebroadcasts = null;

            $this->collCcShowHostss = null;

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
            $con = Propel::getConnection(CcShowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CcShowQuery::create()
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
            $con = Propel::getConnection(CcShowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CcShowPeer::addInstanceToPool($this);
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

            if ($this->aCcPlaylistRelatedByDbAutoPlaylistId !== null) {
                if ($this->aCcPlaylistRelatedByDbAutoPlaylistId->isModified() || $this->aCcPlaylistRelatedByDbAutoPlaylistId->isNew()) {
                    $affectedRows += $this->aCcPlaylistRelatedByDbAutoPlaylistId->save($con);
                }
                $this->setCcPlaylistRelatedByDbAutoPlaylistId($this->aCcPlaylistRelatedByDbAutoPlaylistId);
            }

            if ($this->aCcPlaylistRelatedByDbIntroPlaylistId !== null) {
                if ($this->aCcPlaylistRelatedByDbIntroPlaylistId->isModified() || $this->aCcPlaylistRelatedByDbIntroPlaylistId->isNew()) {
                    $affectedRows += $this->aCcPlaylistRelatedByDbIntroPlaylistId->save($con);
                }
                $this->setCcPlaylistRelatedByDbIntroPlaylistId($this->aCcPlaylistRelatedByDbIntroPlaylistId);
            }

            if ($this->aCcPlaylistRelatedByDbOutroPlaylistId !== null) {
                if ($this->aCcPlaylistRelatedByDbOutroPlaylistId->isModified() || $this->aCcPlaylistRelatedByDbOutroPlaylistId->isNew()) {
                    $affectedRows += $this->aCcPlaylistRelatedByDbOutroPlaylistId->save($con);
                }
                $this->setCcPlaylistRelatedByDbOutroPlaylistId($this->aCcPlaylistRelatedByDbOutroPlaylistId);
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

            if ($this->ccShowDayssScheduledForDeletion !== null) {
                if (!$this->ccShowDayssScheduledForDeletion->isEmpty()) {
                    CcShowDaysQuery::create()
                        ->filterByPrimaryKeys($this->ccShowDayssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowDayssScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowDayss !== null) {
                foreach ($this->collCcShowDayss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->ccShowRebroadcastsScheduledForDeletion !== null) {
                if (!$this->ccShowRebroadcastsScheduledForDeletion->isEmpty()) {
                    CcShowRebroadcastQuery::create()
                        ->filterByPrimaryKeys($this->ccShowRebroadcastsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ccShowRebroadcastsScheduledForDeletion = null;
                }
            }

            if ($this->collCcShowRebroadcasts !== null) {
                foreach ($this->collCcShowRebroadcasts as $referrerFK) {
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

        $this->modifiedColumns[] = CcShowPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CcShowPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('cc_show_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CcShowPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CcShowPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(CcShowPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = '"url"';
        }
        if ($this->isColumnModified(CcShowPeer::GENRE)) {
            $modifiedColumns[':p' . $index++]  = '"genre"';
        }
        if ($this->isColumnModified(CcShowPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CcShowPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '"color"';
        }
        if ($this->isColumnModified(CcShowPeer::BACKGROUND_COLOR)) {
            $modifiedColumns[':p' . $index++]  = '"background_color"';
        }
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH)) {
            $modifiedColumns[':p' . $index++]  = '"live_stream_using_airtime_auth"';
        }
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH)) {
            $modifiedColumns[':p' . $index++]  = '"live_stream_using_custom_auth"';
        }
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USER)) {
            $modifiedColumns[':p' . $index++]  = '"live_stream_user"';
        }
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_PASS)) {
            $modifiedColumns[':p' . $index++]  = '"live_stream_pass"';
        }
        if ($this->isColumnModified(CcShowPeer::LINKED)) {
            $modifiedColumns[':p' . $index++]  = '"linked"';
        }
        if ($this->isColumnModified(CcShowPeer::IS_LINKABLE)) {
            $modifiedColumns[':p' . $index++]  = '"is_linkable"';
        }
        if ($this->isColumnModified(CcShowPeer::IMAGE_PATH)) {
            $modifiedColumns[':p' . $index++]  = '"image_path"';
        }
        if ($this->isColumnModified(CcShowPeer::HAS_AUTOPLAYLIST)) {
            $modifiedColumns[':p' . $index++]  = '"has_autoplaylist"';
        }
        if ($this->isColumnModified(CcShowPeer::AUTOPLAYLIST_ID)) {
            $modifiedColumns[':p' . $index++]  = '"autoplaylist_id"';
        }
        if ($this->isColumnModified(CcShowPeer::AUTOPLAYLIST_REPEAT)) {
            $modifiedColumns[':p' . $index++]  = '"autoplaylist_repeat"';
        }
        if ($this->isColumnModified(CcShowPeer::INTRO_PLAYLIST_ID)) {
            $modifiedColumns[':p' . $index++]  = '"intro_playlist_id"';
        }
        if ($this->isColumnModified(CcShowPeer::OUTRO_PLAYLIST_ID)) {
            $modifiedColumns[':p' . $index++]  = '"outro_playlist_id"';
        }

        $sql = sprintf(
            'INSERT INTO "cc_show" (%s) VALUES (%s)',
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
                    case '"url"':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case '"genre"':
                        $stmt->bindValue($identifier, $this->genre, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"color"':
                        $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
                        break;
                    case '"background_color"':
                        $stmt->bindValue($identifier, $this->background_color, PDO::PARAM_STR);
                        break;
                    case '"live_stream_using_airtime_auth"':
                        $stmt->bindValue($identifier, $this->live_stream_using_airtime_auth, PDO::PARAM_BOOL);
                        break;
                    case '"live_stream_using_custom_auth"':
                        $stmt->bindValue($identifier, $this->live_stream_using_custom_auth, PDO::PARAM_BOOL);
                        break;
                    case '"live_stream_user"':
                        $stmt->bindValue($identifier, $this->live_stream_user, PDO::PARAM_STR);
                        break;
                    case '"live_stream_pass"':
                        $stmt->bindValue($identifier, $this->live_stream_pass, PDO::PARAM_STR);
                        break;
                    case '"linked"':
                        $stmt->bindValue($identifier, $this->linked, PDO::PARAM_BOOL);
                        break;
                    case '"is_linkable"':
                        $stmt->bindValue($identifier, $this->is_linkable, PDO::PARAM_BOOL);
                        break;
                    case '"image_path"':
                        $stmt->bindValue($identifier, $this->image_path, PDO::PARAM_STR);
                        break;
                    case '"has_autoplaylist"':
                        $stmt->bindValue($identifier, $this->has_autoplaylist, PDO::PARAM_BOOL);
                        break;
                    case '"autoplaylist_id"':
                        $stmt->bindValue($identifier, $this->autoplaylist_id, PDO::PARAM_INT);
                        break;
                    case '"autoplaylist_repeat"':
                        $stmt->bindValue($identifier, $this->autoplaylist_repeat, PDO::PARAM_BOOL);
                        break;
                    case '"intro_playlist_id"':
                        $stmt->bindValue($identifier, $this->intro_playlist_id, PDO::PARAM_INT);
                        break;
                    case '"outro_playlist_id"':
                        $stmt->bindValue($identifier, $this->outro_playlist_id, PDO::PARAM_INT);
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

            if ($this->aCcPlaylistRelatedByDbAutoPlaylistId !== null) {
                if (!$this->aCcPlaylistRelatedByDbAutoPlaylistId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcPlaylistRelatedByDbAutoPlaylistId->getValidationFailures());
                }
            }

            if ($this->aCcPlaylistRelatedByDbIntroPlaylistId !== null) {
                if (!$this->aCcPlaylistRelatedByDbIntroPlaylistId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcPlaylistRelatedByDbIntroPlaylistId->getValidationFailures());
                }
            }

            if ($this->aCcPlaylistRelatedByDbOutroPlaylistId !== null) {
                if (!$this->aCcPlaylistRelatedByDbOutroPlaylistId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCcPlaylistRelatedByDbOutroPlaylistId->getValidationFailures());
                }
            }


            if (($retval = CcShowPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCcShowInstancess !== null) {
                    foreach ($this->collCcShowInstancess as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcShowDayss !== null) {
                    foreach ($this->collCcShowDayss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCcShowRebroadcasts !== null) {
                    foreach ($this->collCcShowRebroadcasts as $referrerFK) {
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
        $pos = CcShowPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDbName();
                break;
            case 2:
                return $this->getDbUrl();
                break;
            case 3:
                return $this->getDbGenre();
                break;
            case 4:
                return $this->getDbDescription();
                break;
            case 5:
                return $this->getDbColor();
                break;
            case 6:
                return $this->getDbBackgroundColor();
                break;
            case 7:
                return $this->getDbLiveStreamUsingAirtimeAuth();
                break;
            case 8:
                return $this->getDbLiveStreamUsingCustomAuth();
                break;
            case 9:
                return $this->getDbLiveStreamUser();
                break;
            case 10:
                return $this->getDbLiveStreamPass();
                break;
            case 11:
                return $this->getDbLinked();
                break;
            case 12:
                return $this->getDbIsLinkable();
                break;
            case 13:
                return $this->getDbImagePath();
                break;
            case 14:
                return $this->getDbHasAutoPlaylist();
                break;
            case 15:
                return $this->getDbAutoPlaylistId();
                break;
            case 16:
                return $this->getDbAutoPlaylistRepeat();
                break;
            case 17:
                return $this->getDbIntroPlaylistId();
                break;
            case 18:
                return $this->getDbOutroPlaylistId();
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
        if (isset($alreadyDumpedObjects['CcShow'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CcShow'][$this->getPrimaryKey()] = true;
        $keys = CcShowPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDbId(),
            $keys[1] => $this->getDbName(),
            $keys[2] => $this->getDbUrl(),
            $keys[3] => $this->getDbGenre(),
            $keys[4] => $this->getDbDescription(),
            $keys[5] => $this->getDbColor(),
            $keys[6] => $this->getDbBackgroundColor(),
            $keys[7] => $this->getDbLiveStreamUsingAirtimeAuth(),
            $keys[8] => $this->getDbLiveStreamUsingCustomAuth(),
            $keys[9] => $this->getDbLiveStreamUser(),
            $keys[10] => $this->getDbLiveStreamPass(),
            $keys[11] => $this->getDbLinked(),
            $keys[12] => $this->getDbIsLinkable(),
            $keys[13] => $this->getDbImagePath(),
            $keys[14] => $this->getDbHasAutoPlaylist(),
            $keys[15] => $this->getDbAutoPlaylistId(),
            $keys[16] => $this->getDbAutoPlaylistRepeat(),
            $keys[17] => $this->getDbIntroPlaylistId(),
            $keys[18] => $this->getDbOutroPlaylistId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCcPlaylistRelatedByDbAutoPlaylistId) {
                $result['CcPlaylistRelatedByDbAutoPlaylistId'] = $this->aCcPlaylistRelatedByDbAutoPlaylistId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcPlaylistRelatedByDbIntroPlaylistId) {
                $result['CcPlaylistRelatedByDbIntroPlaylistId'] = $this->aCcPlaylistRelatedByDbIntroPlaylistId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCcPlaylistRelatedByDbOutroPlaylistId) {
                $result['CcPlaylistRelatedByDbOutroPlaylistId'] = $this->aCcPlaylistRelatedByDbOutroPlaylistId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCcShowInstancess) {
                $result['CcShowInstancess'] = $this->collCcShowInstancess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcShowDayss) {
                $result['CcShowDayss'] = $this->collCcShowDayss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcShowRebroadcasts) {
                $result['CcShowRebroadcasts'] = $this->collCcShowRebroadcasts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCcShowHostss) {
                $result['CcShowHostss'] = $this->collCcShowHostss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CcShowPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDbName($value);
                break;
            case 2:
                $this->setDbUrl($value);
                break;
            case 3:
                $this->setDbGenre($value);
                break;
            case 4:
                $this->setDbDescription($value);
                break;
            case 5:
                $this->setDbColor($value);
                break;
            case 6:
                $this->setDbBackgroundColor($value);
                break;
            case 7:
                $this->setDbLiveStreamUsingAirtimeAuth($value);
                break;
            case 8:
                $this->setDbLiveStreamUsingCustomAuth($value);
                break;
            case 9:
                $this->setDbLiveStreamUser($value);
                break;
            case 10:
                $this->setDbLiveStreamPass($value);
                break;
            case 11:
                $this->setDbLinked($value);
                break;
            case 12:
                $this->setDbIsLinkable($value);
                break;
            case 13:
                $this->setDbImagePath($value);
                break;
            case 14:
                $this->setDbHasAutoPlaylist($value);
                break;
            case 15:
                $this->setDbAutoPlaylistId($value);
                break;
            case 16:
                $this->setDbAutoPlaylistRepeat($value);
                break;
            case 17:
                $this->setDbIntroPlaylistId($value);
                break;
            case 18:
                $this->setDbOutroPlaylistId($value);
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
        $keys = CcShowPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDbName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDbUrl($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDbGenre($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDbDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDbColor($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDbBackgroundColor($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDbLiveStreamUsingAirtimeAuth($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDbLiveStreamUsingCustomAuth($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDbLiveStreamUser($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDbLiveStreamPass($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDbLinked($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDbIsLinkable($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setDbImagePath($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setDbHasAutoPlaylist($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setDbAutoPlaylistId($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setDbAutoPlaylistRepeat($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setDbIntroPlaylistId($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setDbOutroPlaylistId($arr[$keys[18]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CcShowPeer::DATABASE_NAME);

        if ($this->isColumnModified(CcShowPeer::ID)) $criteria->add(CcShowPeer::ID, $this->id);
        if ($this->isColumnModified(CcShowPeer::NAME)) $criteria->add(CcShowPeer::NAME, $this->name);
        if ($this->isColumnModified(CcShowPeer::URL)) $criteria->add(CcShowPeer::URL, $this->url);
        if ($this->isColumnModified(CcShowPeer::GENRE)) $criteria->add(CcShowPeer::GENRE, $this->genre);
        if ($this->isColumnModified(CcShowPeer::DESCRIPTION)) $criteria->add(CcShowPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CcShowPeer::COLOR)) $criteria->add(CcShowPeer::COLOR, $this->color);
        if ($this->isColumnModified(CcShowPeer::BACKGROUND_COLOR)) $criteria->add(CcShowPeer::BACKGROUND_COLOR, $this->background_color);
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH)) $criteria->add(CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH, $this->live_stream_using_airtime_auth);
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH)) $criteria->add(CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH, $this->live_stream_using_custom_auth);
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_USER)) $criteria->add(CcShowPeer::LIVE_STREAM_USER, $this->live_stream_user);
        if ($this->isColumnModified(CcShowPeer::LIVE_STREAM_PASS)) $criteria->add(CcShowPeer::LIVE_STREAM_PASS, $this->live_stream_pass);
        if ($this->isColumnModified(CcShowPeer::LINKED)) $criteria->add(CcShowPeer::LINKED, $this->linked);
        if ($this->isColumnModified(CcShowPeer::IS_LINKABLE)) $criteria->add(CcShowPeer::IS_LINKABLE, $this->is_linkable);
        if ($this->isColumnModified(CcShowPeer::IMAGE_PATH)) $criteria->add(CcShowPeer::IMAGE_PATH, $this->image_path);
        if ($this->isColumnModified(CcShowPeer::HAS_AUTOPLAYLIST)) $criteria->add(CcShowPeer::HAS_AUTOPLAYLIST, $this->has_autoplaylist);
        if ($this->isColumnModified(CcShowPeer::AUTOPLAYLIST_ID)) $criteria->add(CcShowPeer::AUTOPLAYLIST_ID, $this->autoplaylist_id);
        if ($this->isColumnModified(CcShowPeer::AUTOPLAYLIST_REPEAT)) $criteria->add(CcShowPeer::AUTOPLAYLIST_REPEAT, $this->autoplaylist_repeat);
        if ($this->isColumnModified(CcShowPeer::INTRO_PLAYLIST_ID)) $criteria->add(CcShowPeer::INTRO_PLAYLIST_ID, $this->intro_playlist_id);
        if ($this->isColumnModified(CcShowPeer::OUTRO_PLAYLIST_ID)) $criteria->add(CcShowPeer::OUTRO_PLAYLIST_ID, $this->outro_playlist_id);

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
        $criteria = new Criteria(CcShowPeer::DATABASE_NAME);
        $criteria->add(CcShowPeer::ID, $this->id);

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
     * @param object $copyObj An object of CcShow (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDbName($this->getDbName());
        $copyObj->setDbUrl($this->getDbUrl());
        $copyObj->setDbGenre($this->getDbGenre());
        $copyObj->setDbDescription($this->getDbDescription());
        $copyObj->setDbColor($this->getDbColor());
        $copyObj->setDbBackgroundColor($this->getDbBackgroundColor());
        $copyObj->setDbLiveStreamUsingAirtimeAuth($this->getDbLiveStreamUsingAirtimeAuth());
        $copyObj->setDbLiveStreamUsingCustomAuth($this->getDbLiveStreamUsingCustomAuth());
        $copyObj->setDbLiveStreamUser($this->getDbLiveStreamUser());
        $copyObj->setDbLiveStreamPass($this->getDbLiveStreamPass());
        $copyObj->setDbLinked($this->getDbLinked());
        $copyObj->setDbIsLinkable($this->getDbIsLinkable());
        $copyObj->setDbImagePath($this->getDbImagePath());
        $copyObj->setDbHasAutoPlaylist($this->getDbHasAutoPlaylist());
        $copyObj->setDbAutoPlaylistId($this->getDbAutoPlaylistId());
        $copyObj->setDbAutoPlaylistRepeat($this->getDbAutoPlaylistRepeat());
        $copyObj->setDbIntroPlaylistId($this->getDbIntroPlaylistId());
        $copyObj->setDbOutroPlaylistId($this->getDbOutroPlaylistId());

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

            foreach ($this->getCcShowDayss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowDays($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcShowRebroadcasts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowRebroadcast($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCcShowHostss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCcShowHosts($relObj->copy($deepCopy));
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
     * @return CcShow Clone of current object.
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
     * @return CcShowPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CcShowPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CcPlaylist object.
     *
     * @param                  CcPlaylist $v
     * @return CcShow The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcPlaylistRelatedByDbAutoPlaylistId(CcPlaylist $v = null)
    {
        if ($v === null) {
            $this->setDbAutoPlaylistId(NULL);
        } else {
            $this->setDbAutoPlaylistId($v->getDbId());
        }

        $this->aCcPlaylistRelatedByDbAutoPlaylistId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcPlaylist object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowRelatedByDbAutoPlaylistId($this);
        }


        return $this;
    }


    /**
     * Get the associated CcPlaylist object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcPlaylist The associated CcPlaylist object.
     * @throws PropelException
     */
    public function getCcPlaylistRelatedByDbAutoPlaylistId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcPlaylistRelatedByDbAutoPlaylistId === null && ($this->autoplaylist_id !== null) && $doQuery) {
            $this->aCcPlaylistRelatedByDbAutoPlaylistId = CcPlaylistQuery::create()->findPk($this->autoplaylist_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcPlaylistRelatedByDbAutoPlaylistId->addCcShowsRelatedByDbAutoPlaylistId($this);
             */
        }

        return $this->aCcPlaylistRelatedByDbAutoPlaylistId;
    }

    /**
     * Declares an association between this object and a CcPlaylist object.
     *
     * @param                  CcPlaylist $v
     * @return CcShow The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcPlaylistRelatedByDbIntroPlaylistId(CcPlaylist $v = null)
    {
        if ($v === null) {
            $this->setDbIntroPlaylistId(NULL);
        } else {
            $this->setDbIntroPlaylistId($v->getDbId());
        }

        $this->aCcPlaylistRelatedByDbIntroPlaylistId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcPlaylist object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowRelatedByDbIntroPlaylistId($this);
        }


        return $this;
    }


    /**
     * Get the associated CcPlaylist object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcPlaylist The associated CcPlaylist object.
     * @throws PropelException
     */
    public function getCcPlaylistRelatedByDbIntroPlaylistId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcPlaylistRelatedByDbIntroPlaylistId === null && ($this->intro_playlist_id !== null) && $doQuery) {
            $this->aCcPlaylistRelatedByDbIntroPlaylistId = CcPlaylistQuery::create()->findPk($this->intro_playlist_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcPlaylistRelatedByDbIntroPlaylistId->addCcShowsRelatedByDbIntroPlaylistId($this);
             */
        }

        return $this->aCcPlaylistRelatedByDbIntroPlaylistId;
    }

    /**
     * Declares an association between this object and a CcPlaylist object.
     *
     * @param                  CcPlaylist $v
     * @return CcShow The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCcPlaylistRelatedByDbOutroPlaylistId(CcPlaylist $v = null)
    {
        if ($v === null) {
            $this->setDbOutroPlaylistId(NULL);
        } else {
            $this->setDbOutroPlaylistId($v->getDbId());
        }

        $this->aCcPlaylistRelatedByDbOutroPlaylistId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CcPlaylist object, it will not be re-added.
        if ($v !== null) {
            $v->addCcShowRelatedByDbOutroPlaylistId($this);
        }


        return $this;
    }


    /**
     * Get the associated CcPlaylist object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CcPlaylist The associated CcPlaylist object.
     * @throws PropelException
     */
    public function getCcPlaylistRelatedByDbOutroPlaylistId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCcPlaylistRelatedByDbOutroPlaylistId === null && ($this->outro_playlist_id !== null) && $doQuery) {
            $this->aCcPlaylistRelatedByDbOutroPlaylistId = CcPlaylistQuery::create()->findPk($this->outro_playlist_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCcPlaylistRelatedByDbOutroPlaylistId->addCcShowsRelatedByDbOutroPlaylistId($this);
             */
        }

        return $this->aCcPlaylistRelatedByDbOutroPlaylistId;
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
        if ('CcShowDays' == $relationName) {
            $this->initCcShowDayss();
        }
        if ('CcShowRebroadcast' == $relationName) {
            $this->initCcShowRebroadcasts();
        }
        if ('CcShowHosts' == $relationName) {
            $this->initCcShowHostss();
        }
    }

    /**
     * Clears out the collCcShowInstancess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShow The current object (for fluent API support)
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
     * If this CcShow is new, it will return
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
                    ->filterByCcShow($this)
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
     * @return CcShow The current object (for fluent API support)
     */
    public function setCcShowInstancess(PropelCollection $ccShowInstancess, PropelPDO $con = null)
    {
        $ccShowInstancessToDelete = $this->getCcShowInstancess(new Criteria(), $con)->diff($ccShowInstancess);


        $this->ccShowInstancessScheduledForDeletion = $ccShowInstancessToDelete;

        foreach ($ccShowInstancessToDelete as $ccShowInstancesRemoved) {
            $ccShowInstancesRemoved->setCcShow(null);
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
                ->filterByCcShow($this)
                ->count($con);
        }

        return count($this->collCcShowInstancess);
    }

    /**
     * Method called to associate a CcShowInstances object to this object
     * through the CcShowInstances foreign key attribute.
     *
     * @param    CcShowInstances $l CcShowInstances
     * @return CcShow The current object (for fluent API support)
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
        $ccShowInstances->setCcShow($this);
    }

    /**
     * @param	CcShowInstances $ccShowInstances The ccShowInstances object to remove.
     * @return CcShow The current object (for fluent API support)
     */
    public function removeCcShowInstances($ccShowInstances)
    {
        if ($this->getCcShowInstancess()->contains($ccShowInstances)) {
            $this->collCcShowInstancess->remove($this->collCcShowInstancess->search($ccShowInstances));
            if (null === $this->ccShowInstancessScheduledForDeletion) {
                $this->ccShowInstancessScheduledForDeletion = clone $this->collCcShowInstancess;
                $this->ccShowInstancessScheduledForDeletion->clear();
            }
            $this->ccShowInstancessScheduledForDeletion[]= clone $ccShowInstances;
            $ccShowInstances->setCcShow(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShow is new, it will return
     * an empty collection; or if this CcShow has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShow.
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
     * Otherwise if this CcShow is new, it will return
     * an empty collection; or if this CcShow has previously
     * been saved, it will retrieve related CcShowInstancess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShow.
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
     * Clears out the collCcShowDayss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShow The current object (for fluent API support)
     * @see        addCcShowDayss()
     */
    public function clearCcShowDayss()
    {
        $this->collCcShowDayss = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowDayssPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowDayss collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowDayss($v = true)
    {
        $this->collCcShowDayssPartial = $v;
    }

    /**
     * Initializes the collCcShowDayss collection.
     *
     * By default this just sets the collCcShowDayss collection to an empty array (like clearcollCcShowDayss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowDayss($overrideExisting = true)
    {
        if (null !== $this->collCcShowDayss && !$overrideExisting) {
            return;
        }
        $this->collCcShowDayss = new PropelObjectCollection();
        $this->collCcShowDayss->setModel('CcShowDays');
    }

    /**
     * Gets an array of CcShowDays objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShow is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowDays[] List of CcShowDays objects
     * @throws PropelException
     */
    public function getCcShowDayss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowDayssPartial && !$this->isNew();
        if (null === $this->collCcShowDayss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowDayss) {
                // return empty collection
                $this->initCcShowDayss();
            } else {
                $collCcShowDayss = CcShowDaysQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowDayssPartial && count($collCcShowDayss)) {
                      $this->initCcShowDayss(false);

                      foreach ($collCcShowDayss as $obj) {
                        if (false == $this->collCcShowDayss->contains($obj)) {
                          $this->collCcShowDayss->append($obj);
                        }
                      }

                      $this->collCcShowDayssPartial = true;
                    }

                    $collCcShowDayss->getInternalIterator()->rewind();

                    return $collCcShowDayss;
                }

                if ($partial && $this->collCcShowDayss) {
                    foreach ($this->collCcShowDayss as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowDayss[] = $obj;
                        }
                    }
                }

                $this->collCcShowDayss = $collCcShowDayss;
                $this->collCcShowDayssPartial = false;
            }
        }

        return $this->collCcShowDayss;
    }

    /**
     * Sets a collection of CcShowDays objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowDayss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcShow The current object (for fluent API support)
     */
    public function setCcShowDayss(PropelCollection $ccShowDayss, PropelPDO $con = null)
    {
        $ccShowDayssToDelete = $this->getCcShowDayss(new Criteria(), $con)->diff($ccShowDayss);


        $this->ccShowDayssScheduledForDeletion = $ccShowDayssToDelete;

        foreach ($ccShowDayssToDelete as $ccShowDaysRemoved) {
            $ccShowDaysRemoved->setCcShow(null);
        }

        $this->collCcShowDayss = null;
        foreach ($ccShowDayss as $ccShowDays) {
            $this->addCcShowDays($ccShowDays);
        }

        $this->collCcShowDayss = $ccShowDayss;
        $this->collCcShowDayssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowDays objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowDays objects.
     * @throws PropelException
     */
    public function countCcShowDayss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowDayssPartial && !$this->isNew();
        if (null === $this->collCcShowDayss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowDayss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowDayss());
            }
            $query = CcShowDaysQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcShow($this)
                ->count($con);
        }

        return count($this->collCcShowDayss);
    }

    /**
     * Method called to associate a CcShowDays object to this object
     * through the CcShowDays foreign key attribute.
     *
     * @param    CcShowDays $l CcShowDays
     * @return CcShow The current object (for fluent API support)
     */
    public function addCcShowDays(CcShowDays $l)
    {
        if ($this->collCcShowDayss === null) {
            $this->initCcShowDayss();
            $this->collCcShowDayssPartial = true;
        }

        if (!in_array($l, $this->collCcShowDayss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowDays($l);

            if ($this->ccShowDayssScheduledForDeletion and $this->ccShowDayssScheduledForDeletion->contains($l)) {
                $this->ccShowDayssScheduledForDeletion->remove($this->ccShowDayssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowDays $ccShowDays The ccShowDays object to add.
     */
    protected function doAddCcShowDays($ccShowDays)
    {
        $this->collCcShowDayss[]= $ccShowDays;
        $ccShowDays->setCcShow($this);
    }

    /**
     * @param	CcShowDays $ccShowDays The ccShowDays object to remove.
     * @return CcShow The current object (for fluent API support)
     */
    public function removeCcShowDays($ccShowDays)
    {
        if ($this->getCcShowDayss()->contains($ccShowDays)) {
            $this->collCcShowDayss->remove($this->collCcShowDayss->search($ccShowDays));
            if (null === $this->ccShowDayssScheduledForDeletion) {
                $this->ccShowDayssScheduledForDeletion = clone $this->collCcShowDayss;
                $this->ccShowDayssScheduledForDeletion->clear();
            }
            $this->ccShowDayssScheduledForDeletion[]= clone $ccShowDays;
            $ccShowDays->setCcShow(null);
        }

        return $this;
    }

    /**
     * Clears out the collCcShowRebroadcasts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShow The current object (for fluent API support)
     * @see        addCcShowRebroadcasts()
     */
    public function clearCcShowRebroadcasts()
    {
        $this->collCcShowRebroadcasts = null; // important to set this to null since that means it is uninitialized
        $this->collCcShowRebroadcastsPartial = null;

        return $this;
    }

    /**
     * reset is the collCcShowRebroadcasts collection loaded partially
     *
     * @return void
     */
    public function resetPartialCcShowRebroadcasts($v = true)
    {
        $this->collCcShowRebroadcastsPartial = $v;
    }

    /**
     * Initializes the collCcShowRebroadcasts collection.
     *
     * By default this just sets the collCcShowRebroadcasts collection to an empty array (like clearcollCcShowRebroadcasts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCcShowRebroadcasts($overrideExisting = true)
    {
        if (null !== $this->collCcShowRebroadcasts && !$overrideExisting) {
            return;
        }
        $this->collCcShowRebroadcasts = new PropelObjectCollection();
        $this->collCcShowRebroadcasts->setModel('CcShowRebroadcast');
    }

    /**
     * Gets an array of CcShowRebroadcast objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CcShow is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CcShowRebroadcast[] List of CcShowRebroadcast objects
     * @throws PropelException
     */
    public function getCcShowRebroadcasts($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCcShowRebroadcastsPartial && !$this->isNew();
        if (null === $this->collCcShowRebroadcasts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCcShowRebroadcasts) {
                // return empty collection
                $this->initCcShowRebroadcasts();
            } else {
                $collCcShowRebroadcasts = CcShowRebroadcastQuery::create(null, $criteria)
                    ->filterByCcShow($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCcShowRebroadcastsPartial && count($collCcShowRebroadcasts)) {
                      $this->initCcShowRebroadcasts(false);

                      foreach ($collCcShowRebroadcasts as $obj) {
                        if (false == $this->collCcShowRebroadcasts->contains($obj)) {
                          $this->collCcShowRebroadcasts->append($obj);
                        }
                      }

                      $this->collCcShowRebroadcastsPartial = true;
                    }

                    $collCcShowRebroadcasts->getInternalIterator()->rewind();

                    return $collCcShowRebroadcasts;
                }

                if ($partial && $this->collCcShowRebroadcasts) {
                    foreach ($this->collCcShowRebroadcasts as $obj) {
                        if ($obj->isNew()) {
                            $collCcShowRebroadcasts[] = $obj;
                        }
                    }
                }

                $this->collCcShowRebroadcasts = $collCcShowRebroadcasts;
                $this->collCcShowRebroadcastsPartial = false;
            }
        }

        return $this->collCcShowRebroadcasts;
    }

    /**
     * Sets a collection of CcShowRebroadcast objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ccShowRebroadcasts A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CcShow The current object (for fluent API support)
     */
    public function setCcShowRebroadcasts(PropelCollection $ccShowRebroadcasts, PropelPDO $con = null)
    {
        $ccShowRebroadcastsToDelete = $this->getCcShowRebroadcasts(new Criteria(), $con)->diff($ccShowRebroadcasts);


        $this->ccShowRebroadcastsScheduledForDeletion = $ccShowRebroadcastsToDelete;

        foreach ($ccShowRebroadcastsToDelete as $ccShowRebroadcastRemoved) {
            $ccShowRebroadcastRemoved->setCcShow(null);
        }

        $this->collCcShowRebroadcasts = null;
        foreach ($ccShowRebroadcasts as $ccShowRebroadcast) {
            $this->addCcShowRebroadcast($ccShowRebroadcast);
        }

        $this->collCcShowRebroadcasts = $ccShowRebroadcasts;
        $this->collCcShowRebroadcastsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CcShowRebroadcast objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CcShowRebroadcast objects.
     * @throws PropelException
     */
    public function countCcShowRebroadcasts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCcShowRebroadcastsPartial && !$this->isNew();
        if (null === $this->collCcShowRebroadcasts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCcShowRebroadcasts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCcShowRebroadcasts());
            }
            $query = CcShowRebroadcastQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCcShow($this)
                ->count($con);
        }

        return count($this->collCcShowRebroadcasts);
    }

    /**
     * Method called to associate a CcShowRebroadcast object to this object
     * through the CcShowRebroadcast foreign key attribute.
     *
     * @param    CcShowRebroadcast $l CcShowRebroadcast
     * @return CcShow The current object (for fluent API support)
     */
    public function addCcShowRebroadcast(CcShowRebroadcast $l)
    {
        if ($this->collCcShowRebroadcasts === null) {
            $this->initCcShowRebroadcasts();
            $this->collCcShowRebroadcastsPartial = true;
        }

        if (!in_array($l, $this->collCcShowRebroadcasts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCcShowRebroadcast($l);

            if ($this->ccShowRebroadcastsScheduledForDeletion and $this->ccShowRebroadcastsScheduledForDeletion->contains($l)) {
                $this->ccShowRebroadcastsScheduledForDeletion->remove($this->ccShowRebroadcastsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CcShowRebroadcast $ccShowRebroadcast The ccShowRebroadcast object to add.
     */
    protected function doAddCcShowRebroadcast($ccShowRebroadcast)
    {
        $this->collCcShowRebroadcasts[]= $ccShowRebroadcast;
        $ccShowRebroadcast->setCcShow($this);
    }

    /**
     * @param	CcShowRebroadcast $ccShowRebroadcast The ccShowRebroadcast object to remove.
     * @return CcShow The current object (for fluent API support)
     */
    public function removeCcShowRebroadcast($ccShowRebroadcast)
    {
        if ($this->getCcShowRebroadcasts()->contains($ccShowRebroadcast)) {
            $this->collCcShowRebroadcasts->remove($this->collCcShowRebroadcasts->search($ccShowRebroadcast));
            if (null === $this->ccShowRebroadcastsScheduledForDeletion) {
                $this->ccShowRebroadcastsScheduledForDeletion = clone $this->collCcShowRebroadcasts;
                $this->ccShowRebroadcastsScheduledForDeletion->clear();
            }
            $this->ccShowRebroadcastsScheduledForDeletion[]= clone $ccShowRebroadcast;
            $ccShowRebroadcast->setCcShow(null);
        }

        return $this;
    }

    /**
     * Clears out the collCcShowHostss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CcShow The current object (for fluent API support)
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
     * If this CcShow is new, it will return
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
                    ->filterByCcShow($this)
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
     * @return CcShow The current object (for fluent API support)
     */
    public function setCcShowHostss(PropelCollection $ccShowHostss, PropelPDO $con = null)
    {
        $ccShowHostssToDelete = $this->getCcShowHostss(new Criteria(), $con)->diff($ccShowHostss);


        $this->ccShowHostssScheduledForDeletion = $ccShowHostssToDelete;

        foreach ($ccShowHostssToDelete as $ccShowHostsRemoved) {
            $ccShowHostsRemoved->setCcShow(null);
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
                ->filterByCcShow($this)
                ->count($con);
        }

        return count($this->collCcShowHostss);
    }

    /**
     * Method called to associate a CcShowHosts object to this object
     * through the CcShowHosts foreign key attribute.
     *
     * @param    CcShowHosts $l CcShowHosts
     * @return CcShow The current object (for fluent API support)
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
        $ccShowHosts->setCcShow($this);
    }

    /**
     * @param	CcShowHosts $ccShowHosts The ccShowHosts object to remove.
     * @return CcShow The current object (for fluent API support)
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
            $ccShowHosts->setCcShow(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CcShow is new, it will return
     * an empty collection; or if this CcShow has previously
     * been saved, it will retrieve related CcShowHostss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CcShow.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CcShowHosts[] List of CcShowHosts objects
     */
    public function getCcShowHostssJoinCcSubjs($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CcShowHostsQuery::create(null, $criteria);
        $query->joinWith('CcSubjs', $join_behavior);

        return $this->getCcShowHostss($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->url = null;
        $this->genre = null;
        $this->description = null;
        $this->color = null;
        $this->background_color = null;
        $this->live_stream_using_airtime_auth = null;
        $this->live_stream_using_custom_auth = null;
        $this->live_stream_user = null;
        $this->live_stream_pass = null;
        $this->linked = null;
        $this->is_linkable = null;
        $this->image_path = null;
        $this->has_autoplaylist = null;
        $this->autoplaylist_id = null;
        $this->autoplaylist_repeat = null;
        $this->intro_playlist_id = null;
        $this->outro_playlist_id = null;
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
            if ($this->collCcShowDayss) {
                foreach ($this->collCcShowDayss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcShowRebroadcasts) {
                foreach ($this->collCcShowRebroadcasts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCcShowHostss) {
                foreach ($this->collCcShowHostss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCcPlaylistRelatedByDbAutoPlaylistId instanceof Persistent) {
              $this->aCcPlaylistRelatedByDbAutoPlaylistId->clearAllReferences($deep);
            }
            if ($this->aCcPlaylistRelatedByDbIntroPlaylistId instanceof Persistent) {
              $this->aCcPlaylistRelatedByDbIntroPlaylistId->clearAllReferences($deep);
            }
            if ($this->aCcPlaylistRelatedByDbOutroPlaylistId instanceof Persistent) {
              $this->aCcPlaylistRelatedByDbOutroPlaylistId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCcShowInstancess instanceof PropelCollection) {
            $this->collCcShowInstancess->clearIterator();
        }
        $this->collCcShowInstancess = null;
        if ($this->collCcShowDayss instanceof PropelCollection) {
            $this->collCcShowDayss->clearIterator();
        }
        $this->collCcShowDayss = null;
        if ($this->collCcShowRebroadcasts instanceof PropelCollection) {
            $this->collCcShowRebroadcasts->clearIterator();
        }
        $this->collCcShowRebroadcasts = null;
        if ($this->collCcShowHostss instanceof PropelCollection) {
            $this->collCcShowHostss->clearIterator();
        }
        $this->collCcShowHostss = null;
        $this->aCcPlaylistRelatedByDbAutoPlaylistId = null;
        $this->aCcPlaylistRelatedByDbIntroPlaylistId = null;
        $this->aCcPlaylistRelatedByDbOutroPlaylistId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CcShowPeer::DEFAULT_STRING_FORMAT);
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
