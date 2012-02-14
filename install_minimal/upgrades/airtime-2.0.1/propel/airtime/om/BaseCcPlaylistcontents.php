<?php


/**
 * Base class that represents a row from the 'cc_playlistcontents' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlaylistcontents extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcPlaylistcontentsPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcPlaylistcontentsPeer
	 */
	protected static $peer;

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
	 * The value for the file_id field.
	 * @var        int
	 */
	protected $file_id;

	/**
	 * The value for the position field.
	 * @var        int
	 */
	protected $position;

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
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fadein;

	/**
	 * The value for the fadeout field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fadeout;

	/**
	 * @var        CcFiles
	 */
	protected $aCcFiles;

	/**
	 * @var        CcPlaylist
	 */
	protected $aCcPlaylist;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->cliplength = '00:00:00';
		$this->cuein = '00:00:00';
		$this->cueout = '00:00:00';
		$this->fadein = '00:00:00';
		$this->fadeout = '00:00:00';
	}

	/**
	 * Initializes internal state of BaseCcPlaylistcontents object.
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
	 * @return     int
	 */
	public function getDbId()
	{
		return $this->id;
	}

	/**
	 * Get the [playlist_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbPlaylistId()
	{
		return $this->playlist_id;
	}

	/**
	 * Get the [file_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbFileId()
	{
		return $this->file_id;
	}

	/**
	 * Get the [position] column value.
	 * 
	 * @return     int
	 */
	public function getDbPosition()
	{
		return $this->position;
	}

	/**
	 * Get the [optionally formatted] temporal [cliplength] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCliplength($format = '%X')
	{
		if ($this->cliplength === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->cliplength);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->cliplength, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [cuein] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCuein($format = '%X')
	{
		if ($this->cuein === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->cuein);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->cuein, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [cueout] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCueout($format = '%X')
	{
		if ($this->cueout === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->cueout);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->cueout, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [fadein] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbFadein($format = '%X')
	{
		if ($this->fadein === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fadein);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fadein, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [fadeout] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbFadeout($format = '%X')
	{
		if ($this->fadeout === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fadeout);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fadeout, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcPlaylistcontentsPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [playlist_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbPlaylistId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->playlist_id !== $v) {
			$this->playlist_id = $v;
			$this->modifiedColumns[] = CcPlaylistcontentsPeer::PLAYLIST_ID;
		}

		if ($this->aCcPlaylist !== null && $this->aCcPlaylist->getDbId() !== $v) {
			$this->aCcPlaylist = null;
		}

		return $this;
	} // setDbPlaylistId()

	/**
	 * Set the value of [file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbFileId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_id !== $v) {
			$this->file_id = $v;
			$this->modifiedColumns[] = CcPlaylistcontentsPeer::FILE_ID;
		}

		if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
			$this->aCcFiles = null;
		}

		return $this;
	} // setDbFileId()

	/**
	 * Set the value of [position] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbPosition($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->position !== $v) {
			$this->position = $v;
			$this->modifiedColumns[] = CcPlaylistcontentsPeer::POSITION;
		}

		return $this;
	} // setDbPosition()

	/**
	 * Sets the value of [cliplength] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbCliplength($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->cliplength !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->cliplength !== null && $tmpDt = new DateTime($this->cliplength)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->cliplength = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::CLIPLENGTH;
			}
		} // if either are not null

		return $this;
	} // setDbCliplength()

	/**
	 * Sets the value of [cuein] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbCuein($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->cuein !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->cuein !== null && $tmpDt = new DateTime($this->cuein)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->cuein = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEIN;
			}
		} // if either are not null

		return $this;
	} // setDbCuein()

	/**
	 * Sets the value of [cueout] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbCueout($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->cueout !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->cueout !== null && $tmpDt = new DateTime($this->cueout)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->cueout = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::CUEOUT;
			}
		} // if either are not null

		return $this;
	} // setDbCueout()

	/**
	 * Sets the value of [fadein] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbFadein($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->fadein !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fadein !== null && $tmpDt = new DateTime($this->fadein)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->fadein = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEIN;
			}
		} // if either are not null

		return $this;
	} // setDbFadein()

	/**
	 * Sets the value of [fadeout] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 */
	public function setDbFadeout($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->fadeout !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fadeout !== null && $tmpDt = new DateTime($this->fadeout)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->fadeout = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::FADEOUT;
			}
		} // if either are not null

		return $this;
	} // setDbFadeout()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->cliplength !== '00:00:00') {
				return false;
			}

			if ($this->cuein !== '00:00:00') {
				return false;
			}

			if ($this->cueout !== '00:00:00') {
				return false;
			}

			if ($this->fadein !== '00:00:00') {
				return false;
			}

			if ($this->fadeout !== '00:00:00') {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
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
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->playlist_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->file_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->position = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->cliplength = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->cuein = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->cueout = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->fadein = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->fadeout = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 9; // 9 = CcPlaylistcontentsPeer::NUM_COLUMNS - CcPlaylistcontentsPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcPlaylistcontents object", $e);
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
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aCcPlaylist !== null && $this->playlist_id !== $this->aCcPlaylist->getDbId()) {
			$this->aCcPlaylist = null;
		}
		if ($this->aCcFiles !== null && $this->file_id !== $this->aCcFiles->getDbId()) {
			$this->aCcFiles = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
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
			$con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcPlaylistcontentsPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcFiles = null;
			$this->aCcPlaylist = null;
		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcPlaylistcontentsQuery::create()
					->filterByPrimaryKey($this->getPrimaryKey())
					->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcPlaylistcontentsPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
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
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aCcFiles !== null) {
				if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
					$affectedRows += $this->aCcFiles->save($con);
				}
				$this->setCcFiles($this->aCcFiles);
			}

			if ($this->aCcPlaylist !== null) {
				if ($this->aCcPlaylist->isModified() || $this->aCcPlaylist->isNew()) {
					$affectedRows += $this->aCcPlaylist->save($con);
				}
				$this->setCcPlaylist($this->aCcPlaylist);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcPlaylistcontentsPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcPlaylistcontentsPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcPlaylistcontentsPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcPlaylistcontentsPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
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
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aCcFiles !== null) {
				if (!$this->aCcFiles->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
				}
			}

			if ($this->aCcPlaylist !== null) {
				if (!$this->aCcPlaylist->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcPlaylist->getValidationFailures());
				}
			}


			if (($retval = CcPlaylistcontentsPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CcPlaylistcontentsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getDbId();
				break;
			case 1:
				return $this->getDbPlaylistId();
				break;
			case 2:
				return $this->getDbFileId();
				break;
			case 3:
				return $this->getDbPosition();
				break;
			case 4:
				return $this->getDbCliplength();
				break;
			case 5:
				return $this->getDbCuein();
				break;
			case 6:
				return $this->getDbCueout();
				break;
			case 7:
				return $this->getDbFadein();
				break;
			case 8:
				return $this->getDbFadeout();
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
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = CcPlaylistcontentsPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbPlaylistId(),
			$keys[2] => $this->getDbFileId(),
			$keys[3] => $this->getDbPosition(),
			$keys[4] => $this->getDbCliplength(),
			$keys[5] => $this->getDbCuein(),
			$keys[6] => $this->getDbCueout(),
			$keys[7] => $this->getDbFadein(),
			$keys[8] => $this->getDbFadeout(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcFiles) {
				$result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcPlaylist) {
				$result['CcPlaylist'] = $this->aCcPlaylist->toArray($keyType, $includeLazyLoadColumns, true);
			}
		}
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CcPlaylistcontentsPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setDbId($value);
				break;
			case 1:
				$this->setDbPlaylistId($value);
				break;
			case 2:
				$this->setDbFileId($value);
				break;
			case 3:
				$this->setDbPosition($value);
				break;
			case 4:
				$this->setDbCliplength($value);
				break;
			case 5:
				$this->setDbCuein($value);
				break;
			case 6:
				$this->setDbCueout($value);
				break;
			case 7:
				$this->setDbFadein($value);
				break;
			case 8:
				$this->setDbFadeout($value);
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
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = CcPlaylistcontentsPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbPlaylistId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbFileId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbPosition($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbCliplength($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbCuein($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDbCueout($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDbFadein($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDbFadeout($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcPlaylistcontentsPeer::ID)) $criteria->add(CcPlaylistcontentsPeer::ID, $this->id);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::PLAYLIST_ID)) $criteria->add(CcPlaylistcontentsPeer::PLAYLIST_ID, $this->playlist_id);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::FILE_ID)) $criteria->add(CcPlaylistcontentsPeer::FILE_ID, $this->file_id);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::POSITION)) $criteria->add(CcPlaylistcontentsPeer::POSITION, $this->position);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::CLIPLENGTH)) $criteria->add(CcPlaylistcontentsPeer::CLIPLENGTH, $this->cliplength);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::CUEIN)) $criteria->add(CcPlaylistcontentsPeer::CUEIN, $this->cuein);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::CUEOUT)) $criteria->add(CcPlaylistcontentsPeer::CUEOUT, $this->cueout);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::FADEIN)) $criteria->add(CcPlaylistcontentsPeer::FADEIN, $this->fadein);
		if ($this->isColumnModified(CcPlaylistcontentsPeer::FADEOUT)) $criteria->add(CcPlaylistcontentsPeer::FADEOUT, $this->fadeout);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);
		$criteria->add(CcPlaylistcontentsPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getDbId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setDbId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
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
	 * @param      object $copyObj An object of CcPlaylistcontents (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbPlaylistId($this->playlist_id);
		$copyObj->setDbFileId($this->file_id);
		$copyObj->setDbPosition($this->position);
		$copyObj->setDbCliplength($this->cliplength);
		$copyObj->setDbCuein($this->cuein);
		$copyObj->setDbCueout($this->cueout);
		$copyObj->setDbFadein($this->fadein);
		$copyObj->setDbFadeout($this->fadeout);

		$copyObj->setNew(true);
		$copyObj->setDbId(NULL); // this is a auto-increment column, so set to default value
	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     CcPlaylistcontents Clone of current object.
	 * @throws     PropelException
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
	 * @return     CcPlaylistcontentsPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcPlaylistcontentsPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcFiles object.
	 *
	 * @param      CcFiles $v
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 * @throws     PropelException
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
			$v->addCcPlaylistcontents($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcFiles object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcFiles The associated CcFiles object.
	 * @throws     PropelException
	 */
	public function getCcFiles(PropelPDO $con = null)
	{
		if ($this->aCcFiles === null && ($this->file_id !== null)) {
			$this->aCcFiles = CcFilesQuery::create()->findPk($this->file_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcFiles->addCcPlaylistcontentss($this);
			 */
		}
		return $this->aCcFiles;
	}

	/**
	 * Declares an association between this object and a CcPlaylist object.
	 *
	 * @param      CcPlaylist $v
	 * @return     CcPlaylistcontents The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcPlaylist(CcPlaylist $v = null)
	{
		if ($v === null) {
			$this->setDbPlaylistId(NULL);
		} else {
			$this->setDbPlaylistId($v->getDbId());
		}

		$this->aCcPlaylist = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcPlaylist object, it will not be re-added.
		if ($v !== null) {
			$v->addCcPlaylistcontents($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcPlaylist object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcPlaylist The associated CcPlaylist object.
	 * @throws     PropelException
	 */
	public function getCcPlaylist(PropelPDO $con = null)
	{
		if ($this->aCcPlaylist === null && ($this->playlist_id !== null)) {
			$this->aCcPlaylist = CcPlaylistQuery::create()->findPk($this->playlist_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcPlaylist->addCcPlaylistcontentss($this);
			 */
		}
		return $this->aCcPlaylist;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->playlist_id = null;
		$this->file_id = null;
		$this->position = null;
		$this->cliplength = null;
		$this->cuein = null;
		$this->cueout = null;
		$this->fadein = null;
		$this->fadeout = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->applyDefaultValues();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

		$this->aCcFiles = null;
		$this->aCcPlaylist = null;
	}

	/**
	 * Catches calls to virtual methods
	 */
	public function __call($name, $params)
	{
		if (preg_match('/get(\w+)/', $name, $matches)) {
			$virtualColumn = $matches[1];
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
			// no lcfirst in php<5.3...
			$virtualColumn[0] = strtolower($virtualColumn[0]);
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
		}
		throw new PropelException('Call to undefined method: ' . $name);
	}

} // BaseCcPlaylistcontents
