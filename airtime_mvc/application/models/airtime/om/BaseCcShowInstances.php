<?php


/**
 * Base class that represents a row from the 'cc_show_instances' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowInstances extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcShowInstancesPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcShowInstancesPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the starts field.
	 * @var        string
	 */
	protected $starts;

	/**
	 * The value for the ends field.
	 * @var        string
	 */
	protected $ends;

	/**
	 * The value for the show_id field.
	 * @var        int
	 */
	protected $show_id;

	/**
	 * The value for the record field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $record;

	/**
	 * The value for the rebroadcast field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $rebroadcast;

	/**
	 * The value for the instance_id field.
	 * @var        int
	 */
	protected $instance_id;

	/**
	 * The value for the file_id field.
	 * @var        int
	 */
	protected $file_id;

	/**
	 * The value for the time_filled field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $time_filled;

	/**
	 * The value for the created field.
	 * @var        string
	 */
	protected $created;

	/**
	 * The value for the last_scheduled field.
	 * @var        string
	 */
	protected $last_scheduled;

	/**
	 * The value for the modified_instance field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $modified_instance;

	/**
	 * @var        CcShow
	 */
	protected $aCcShow;

	/**
	 * @var        CcShowInstances
	 */
	protected $aCcShowInstancesRelatedByDbOriginalShow;

	/**
	 * @var        CcFiles
	 */
	protected $aCcFiles;

	/**
	 * @var        array CcShowInstances[] Collection to store aggregation of CcShowInstances objects.
	 */
	protected $collCcShowInstancessRelatedByDbId;

	/**
	 * @var        array CcSchedule[] Collection to store aggregation of CcSchedule objects.
	 */
	protected $collCcSchedules;

	/**
	 * @var        array CcPlayoutHistory[] Collection to store aggregation of CcPlayoutHistory objects.
	 */
	protected $collCcPlayoutHistorys;

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
		$this->record = 0;
		$this->rebroadcast = 0;
		$this->time_filled = '00:00:00';
		$this->modified_instance = false;
	}

	/**
	 * Initializes internal state of BaseCcShowInstances object.
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
	 * Get the [optionally formatted] temporal [starts] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbStarts($format = 'Y-m-d H:i:s')
	{
		if ($this->starts === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->starts);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->starts, true), $x);
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
	 * Get the [optionally formatted] temporal [ends] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbEnds($format = 'Y-m-d H:i:s')
	{
		if ($this->ends === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->ends);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ends, true), $x);
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
	 * Get the [show_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbShowId()
	{
		return $this->show_id;
	}

	/**
	 * Get the [record] column value.
	 * 
	 * @return     int
	 */
	public function getDbRecord()
	{
		return $this->record;
	}

	/**
	 * Get the [rebroadcast] column value.
	 * 
	 * @return     int
	 */
	public function getDbRebroadcast()
	{
		return $this->rebroadcast;
	}

	/**
	 * Get the [instance_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbOriginalShow()
	{
		return $this->instance_id;
	}

	/**
	 * Get the [file_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbRecordedFile()
	{
		return $this->file_id;
	}

	/**
	 * Get the [time_filled] column value.
	 * 
	 * @return     string
	 */
	public function getDbTimeFilled()
	{
		return $this->time_filled;
	}

	/**
	 * Get the [optionally formatted] temporal [created] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCreated($format = 'Y-m-d H:i:s')
	{
		if ($this->created === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->created);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created, true), $x);
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
	 * Get the [optionally formatted] temporal [last_scheduled] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbLastScheduled($format = 'Y-m-d H:i:s')
	{
		if ($this->last_scheduled === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->last_scheduled);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_scheduled, true), $x);
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
	 * Get the [modified_instance] column value.
	 * 
	 * @return     boolean
	 */
	public function getDbModifiedInstance()
	{
		return $this->modified_instance;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Sets the value of [starts] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbStarts($v)
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

		if ( $this->starts !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->starts !== null && $tmpDt = new DateTime($this->starts)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->starts = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcShowInstancesPeer::STARTS;
			}
		} // if either are not null

		return $this;
	} // setDbStarts()

	/**
	 * Sets the value of [ends] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbEnds($v)
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

		if ( $this->ends !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->ends !== null && $tmpDt = new DateTime($this->ends)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->ends = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcShowInstancesPeer::ENDS;
			}
		} // if either are not null

		return $this;
	} // setDbEnds()

	/**
	 * Set the value of [show_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbShowId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->show_id !== $v) {
			$this->show_id = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::SHOW_ID;
		}

		if ($this->aCcShow !== null && $this->aCcShow->getDbId() !== $v) {
			$this->aCcShow = null;
		}

		return $this;
	} // setDbShowId()

	/**
	 * Set the value of [record] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbRecord($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->record !== $v || $this->isNew()) {
			$this->record = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::RECORD;
		}

		return $this;
	} // setDbRecord()

	/**
	 * Set the value of [rebroadcast] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbRebroadcast($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rebroadcast !== $v || $this->isNew()) {
			$this->rebroadcast = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::REBROADCAST;
		}

		return $this;
	} // setDbRebroadcast()

	/**
	 * Set the value of [instance_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbOriginalShow($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->instance_id !== $v) {
			$this->instance_id = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::INSTANCE_ID;
		}

		if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null && $this->aCcShowInstancesRelatedByDbOriginalShow->getDbId() !== $v) {
			$this->aCcShowInstancesRelatedByDbOriginalShow = null;
		}

		return $this;
	} // setDbOriginalShow()

	/**
	 * Set the value of [file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbRecordedFile($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_id !== $v) {
			$this->file_id = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::FILE_ID;
		}

		if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
			$this->aCcFiles = null;
		}

		return $this;
	} // setDbRecordedFile()

	/**
	 * Set the value of [time_filled] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbTimeFilled($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->time_filled !== $v || $this->isNew()) {
			$this->time_filled = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::TIME_FILLED;
		}

		return $this;
	} // setDbTimeFilled()

	/**
	 * Sets the value of [created] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbCreated($v)
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

		if ( $this->created !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->created !== null && $tmpDt = new DateTime($this->created)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->created = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcShowInstancesPeer::CREATED;
			}
		} // if either are not null

		return $this;
	} // setDbCreated()

	/**
	 * Sets the value of [last_scheduled] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbLastScheduled($v)
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

		if ( $this->last_scheduled !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->last_scheduled !== null && $tmpDt = new DateTime($this->last_scheduled)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->last_scheduled = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcShowInstancesPeer::LAST_SCHEDULED;
			}
		} // if either are not null

		return $this;
	} // setDbLastScheduled()

	/**
	 * Set the value of [modified_instance] column.
	 * 
	 * @param      boolean $v new value
	 * @return     CcShowInstances The current object (for fluent API support)
	 */
	public function setDbModifiedInstance($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->modified_instance !== $v || $this->isNew()) {
			$this->modified_instance = $v;
			$this->modifiedColumns[] = CcShowInstancesPeer::MODIFIED_INSTANCE;
		}

		return $this;
	} // setDbModifiedInstance()

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
			if ($this->record !== 0) {
				return false;
			}

			if ($this->rebroadcast !== 0) {
				return false;
			}

			if ($this->time_filled !== '00:00:00') {
				return false;
			}

			if ($this->modified_instance !== false) {
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
			$this->starts = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->ends = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->show_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->record = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->rebroadcast = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->instance_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->file_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->time_filled = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->created = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->last_scheduled = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->modified_instance = ($row[$startcol + 11] !== null) ? (boolean) $row[$startcol + 11] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 12; // 12 = CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcShowInstances object", $e);
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

		if ($this->aCcShow !== null && $this->show_id !== $this->aCcShow->getDbId()) {
			$this->aCcShow = null;
		}
		if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null && $this->instance_id !== $this->aCcShowInstancesRelatedByDbOriginalShow->getDbId()) {
			$this->aCcShowInstancesRelatedByDbOriginalShow = null;
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
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcShowInstancesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcShow = null;
			$this->aCcShowInstancesRelatedByDbOriginalShow = null;
			$this->aCcFiles = null;
			$this->collCcShowInstancessRelatedByDbId = null;

			$this->collCcSchedules = null;

			$this->collCcPlayoutHistorys = null;

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
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcShowInstancesQuery::create()
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
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcShowInstancesPeer::addInstanceToPool($this);
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

			if ($this->aCcShow !== null) {
				if ($this->aCcShow->isModified() || $this->aCcShow->isNew()) {
					$affectedRows += $this->aCcShow->save($con);
				}
				$this->setCcShow($this->aCcShow);
			}

			if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null) {
				if ($this->aCcShowInstancesRelatedByDbOriginalShow->isModified() || $this->aCcShowInstancesRelatedByDbOriginalShow->isNew()) {
					$affectedRows += $this->aCcShowInstancesRelatedByDbOriginalShow->save($con);
				}
				$this->setCcShowInstancesRelatedByDbOriginalShow($this->aCcShowInstancesRelatedByDbOriginalShow);
			}

			if ($this->aCcFiles !== null) {
				if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
					$affectedRows += $this->aCcFiles->save($con);
				}
				$this->setCcFiles($this->aCcFiles);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcShowInstancesPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcShowInstancesPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcShowInstancesPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcShowInstancesPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCcShowInstancessRelatedByDbId !== null) {
				foreach ($this->collCcShowInstancessRelatedByDbId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCcSchedules !== null) {
				foreach ($this->collCcSchedules as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCcPlayoutHistorys !== null) {
				foreach ($this->collCcPlayoutHistorys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
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

			if ($this->aCcShow !== null) {
				if (!$this->aCcShow->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcShow->getValidationFailures());
				}
			}

			if ($this->aCcShowInstancesRelatedByDbOriginalShow !== null) {
				if (!$this->aCcShowInstancesRelatedByDbOriginalShow->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcShowInstancesRelatedByDbOriginalShow->getValidationFailures());
				}
			}

			if ($this->aCcFiles !== null) {
				if (!$this->aCcFiles->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
				}
			}


			if (($retval = CcShowInstancesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCcShowInstancessRelatedByDbId !== null) {
					foreach ($this->collCcShowInstancessRelatedByDbId as $referrerFK) {
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

				if ($this->collCcPlayoutHistorys !== null) {
					foreach ($this->collCcPlayoutHistorys as $referrerFK) {
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
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CcShowInstancesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDbStarts();
				break;
			case 2:
				return $this->getDbEnds();
				break;
			case 3:
				return $this->getDbShowId();
				break;
			case 4:
				return $this->getDbRecord();
				break;
			case 5:
				return $this->getDbRebroadcast();
				break;
			case 6:
				return $this->getDbOriginalShow();
				break;
			case 7:
				return $this->getDbRecordedFile();
				break;
			case 8:
				return $this->getDbTimeFilled();
				break;
			case 9:
				return $this->getDbCreated();
				break;
			case 10:
				return $this->getDbLastScheduled();
				break;
			case 11:
				return $this->getDbModifiedInstance();
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
		$keys = CcShowInstancesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbStarts(),
			$keys[2] => $this->getDbEnds(),
			$keys[3] => $this->getDbShowId(),
			$keys[4] => $this->getDbRecord(),
			$keys[5] => $this->getDbRebroadcast(),
			$keys[6] => $this->getDbOriginalShow(),
			$keys[7] => $this->getDbRecordedFile(),
			$keys[8] => $this->getDbTimeFilled(),
			$keys[9] => $this->getDbCreated(),
			$keys[10] => $this->getDbLastScheduled(),
			$keys[11] => $this->getDbModifiedInstance(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcShow) {
				$result['CcShow'] = $this->aCcShow->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcShowInstancesRelatedByDbOriginalShow) {
				$result['CcShowInstancesRelatedByDbOriginalShow'] = $this->aCcShowInstancesRelatedByDbOriginalShow->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcFiles) {
				$result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = CcShowInstancesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDbStarts($value);
				break;
			case 2:
				$this->setDbEnds($value);
				break;
			case 3:
				$this->setDbShowId($value);
				break;
			case 4:
				$this->setDbRecord($value);
				break;
			case 5:
				$this->setDbRebroadcast($value);
				break;
			case 6:
				$this->setDbOriginalShow($value);
				break;
			case 7:
				$this->setDbRecordedFile($value);
				break;
			case 8:
				$this->setDbTimeFilled($value);
				break;
			case 9:
				$this->setDbCreated($value);
				break;
			case 10:
				$this->setDbLastScheduled($value);
				break;
			case 11:
				$this->setDbModifiedInstance($value);
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
		$keys = CcShowInstancesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbStarts($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbEnds($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbShowId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbRecord($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbRebroadcast($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDbOriginalShow($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDbRecordedFile($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDbTimeFilled($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDbCreated($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDbLastScheduled($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDbModifiedInstance($arr[$keys[11]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcShowInstancesPeer::ID)) $criteria->add(CcShowInstancesPeer::ID, $this->id);
		if ($this->isColumnModified(CcShowInstancesPeer::STARTS)) $criteria->add(CcShowInstancesPeer::STARTS, $this->starts);
		if ($this->isColumnModified(CcShowInstancesPeer::ENDS)) $criteria->add(CcShowInstancesPeer::ENDS, $this->ends);
		if ($this->isColumnModified(CcShowInstancesPeer::SHOW_ID)) $criteria->add(CcShowInstancesPeer::SHOW_ID, $this->show_id);
		if ($this->isColumnModified(CcShowInstancesPeer::RECORD)) $criteria->add(CcShowInstancesPeer::RECORD, $this->record);
		if ($this->isColumnModified(CcShowInstancesPeer::REBROADCAST)) $criteria->add(CcShowInstancesPeer::REBROADCAST, $this->rebroadcast);
		if ($this->isColumnModified(CcShowInstancesPeer::INSTANCE_ID)) $criteria->add(CcShowInstancesPeer::INSTANCE_ID, $this->instance_id);
		if ($this->isColumnModified(CcShowInstancesPeer::FILE_ID)) $criteria->add(CcShowInstancesPeer::FILE_ID, $this->file_id);
		if ($this->isColumnModified(CcShowInstancesPeer::TIME_FILLED)) $criteria->add(CcShowInstancesPeer::TIME_FILLED, $this->time_filled);
		if ($this->isColumnModified(CcShowInstancesPeer::CREATED)) $criteria->add(CcShowInstancesPeer::CREATED, $this->created);
		if ($this->isColumnModified(CcShowInstancesPeer::LAST_SCHEDULED)) $criteria->add(CcShowInstancesPeer::LAST_SCHEDULED, $this->last_scheduled);
		if ($this->isColumnModified(CcShowInstancesPeer::MODIFIED_INSTANCE)) $criteria->add(CcShowInstancesPeer::MODIFIED_INSTANCE, $this->modified_instance);

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
		$criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);
		$criteria->add(CcShowInstancesPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcShowInstances (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbStarts($this->starts);
		$copyObj->setDbEnds($this->ends);
		$copyObj->setDbShowId($this->show_id);
		$copyObj->setDbRecord($this->record);
		$copyObj->setDbRebroadcast($this->rebroadcast);
		$copyObj->setDbOriginalShow($this->instance_id);
		$copyObj->setDbRecordedFile($this->file_id);
		$copyObj->setDbTimeFilled($this->time_filled);
		$copyObj->setDbCreated($this->created);
		$copyObj->setDbLastScheduled($this->last_scheduled);
		$copyObj->setDbModifiedInstance($this->modified_instance);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCcShowInstancessRelatedByDbId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcShowInstancesRelatedByDbId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCcSchedules() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcSchedule($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCcPlayoutHistorys() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcPlayoutHistory($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


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
	 * @return     CcShowInstances Clone of current object.
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
	 * @return     CcShowInstancesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcShowInstancesPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcShow object.
	 *
	 * @param      CcShow $v
	 * @return     CcShowInstances The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcShow(CcShow $v = null)
	{
		if ($v === null) {
			$this->setDbShowId(NULL);
		} else {
			$this->setDbShowId($v->getDbId());
		}

		$this->aCcShow = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcShow object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowInstances($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcShow object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcShow The associated CcShow object.
	 * @throws     PropelException
	 */
	public function getCcShow(PropelPDO $con = null)
	{
		if ($this->aCcShow === null && ($this->show_id !== null)) {
			$this->aCcShow = CcShowQuery::create()->findPk($this->show_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcShow->addCcShowInstancess($this);
			 */
		}
		return $this->aCcShow;
	}

	/**
	 * Declares an association between this object and a CcShowInstances object.
	 *
	 * @param      CcShowInstances $v
	 * @return     CcShowInstances The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcShowInstancesRelatedByDbOriginalShow(CcShowInstances $v = null)
	{
		if ($v === null) {
			$this->setDbOriginalShow(NULL);
		} else {
			$this->setDbOriginalShow($v->getDbId());
		}

		$this->aCcShowInstancesRelatedByDbOriginalShow = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcShowInstances object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowInstancesRelatedByDbId($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcShowInstances object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcShowInstances The associated CcShowInstances object.
	 * @throws     PropelException
	 */
	public function getCcShowInstancesRelatedByDbOriginalShow(PropelPDO $con = null)
	{
		if ($this->aCcShowInstancesRelatedByDbOriginalShow === null && ($this->instance_id !== null)) {
			$this->aCcShowInstancesRelatedByDbOriginalShow = CcShowInstancesQuery::create()->findPk($this->instance_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcShowInstancesRelatedByDbOriginalShow->addCcShowInstancessRelatedByDbId($this);
			 */
		}
		return $this->aCcShowInstancesRelatedByDbOriginalShow;
	}

	/**
	 * Declares an association between this object and a CcFiles object.
	 *
	 * @param      CcFiles $v
	 * @return     CcShowInstances The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcFiles(CcFiles $v = null)
	{
		if ($v === null) {
			$this->setDbRecordedFile(NULL);
		} else {
			$this->setDbRecordedFile($v->getDbId());
		}

		$this->aCcFiles = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcFiles object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowInstances($this);
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
			   $this->aCcFiles->addCcShowInstancess($this);
			 */
		}
		return $this->aCcFiles;
	}

	/**
	 * Clears out the collCcShowInstancessRelatedByDbId collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcShowInstancessRelatedByDbId()
	 */
	public function clearCcShowInstancessRelatedByDbId()
	{
		$this->collCcShowInstancessRelatedByDbId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcShowInstancessRelatedByDbId collection.
	 *
	 * By default this just sets the collCcShowInstancessRelatedByDbId collection to an empty array (like clearcollCcShowInstancessRelatedByDbId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcShowInstancessRelatedByDbId()
	{
		$this->collCcShowInstancessRelatedByDbId = new PropelObjectCollection();
		$this->collCcShowInstancessRelatedByDbId->setModel('CcShowInstances');
	}

	/**
	 * Gets an array of CcShowInstances objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcShowInstances is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcShowInstances[] List of CcShowInstances objects
	 * @throws     PropelException
	 */
	public function getCcShowInstancessRelatedByDbId($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcShowInstancessRelatedByDbId || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcShowInstancessRelatedByDbId) {
				// return empty collection
				$this->initCcShowInstancessRelatedByDbId();
			} else {
				$collCcShowInstancessRelatedByDbId = CcShowInstancesQuery::create(null, $criteria)
					->filterByCcShowInstancesRelatedByDbOriginalShow($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcShowInstancessRelatedByDbId;
				}
				$this->collCcShowInstancessRelatedByDbId = $collCcShowInstancessRelatedByDbId;
			}
		}
		return $this->collCcShowInstancessRelatedByDbId;
	}

	/**
	 * Returns the number of related CcShowInstances objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcShowInstances objects.
	 * @throws     PropelException
	 */
	public function countCcShowInstancessRelatedByDbId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcShowInstancessRelatedByDbId || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcShowInstancessRelatedByDbId) {
				return 0;
			} else {
				$query = CcShowInstancesQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcShowInstancesRelatedByDbOriginalShow($this)
					->count($con);
			}
		} else {
			return count($this->collCcShowInstancessRelatedByDbId);
		}
	}

	/**
	 * Method called to associate a CcShowInstances object to this object
	 * through the CcShowInstances foreign key attribute.
	 *
	 * @param      CcShowInstances $l CcShowInstances
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcShowInstancesRelatedByDbId(CcShowInstances $l)
	{
		if ($this->collCcShowInstancessRelatedByDbId === null) {
			$this->initCcShowInstancessRelatedByDbId();
		}
		if (!$this->collCcShowInstancessRelatedByDbId->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcShowInstancessRelatedByDbId[]= $l;
			$l->setCcShowInstancesRelatedByDbOriginalShow($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcShowInstances is new, it will return
	 * an empty collection; or if this CcShowInstances has previously
	 * been saved, it will retrieve related CcShowInstancessRelatedByDbId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcShowInstances.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcShowInstances[] List of CcShowInstances objects
	 */
	public function getCcShowInstancessRelatedByDbIdJoinCcShow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcShowInstancesQuery::create(null, $criteria);
		$query->joinWith('CcShow', $join_behavior);

		return $this->getCcShowInstancessRelatedByDbId($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcShowInstances is new, it will return
	 * an empty collection; or if this CcShowInstances has previously
	 * been saved, it will retrieve related CcShowInstancessRelatedByDbId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcShowInstances.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcShowInstances[] List of CcShowInstances objects
	 */
	public function getCcShowInstancessRelatedByDbIdJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcShowInstancesQuery::create(null, $criteria);
		$query->joinWith('CcFiles', $join_behavior);

		return $this->getCcShowInstancessRelatedByDbId($query, $con);
	}

	/**
	 * Clears out the collCcSchedules collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcSchedules()
	 */
	public function clearCcSchedules()
	{
		$this->collCcSchedules = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcSchedules collection.
	 *
	 * By default this just sets the collCcSchedules collection to an empty array (like clearcollCcSchedules());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcSchedules()
	{
		$this->collCcSchedules = new PropelObjectCollection();
		$this->collCcSchedules->setModel('CcSchedule');
	}

	/**
	 * Gets an array of CcSchedule objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcShowInstances is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcSchedule[] List of CcSchedule objects
	 * @throws     PropelException
	 */
	public function getCcSchedules($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcSchedules || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcSchedules) {
				// return empty collection
				$this->initCcSchedules();
			} else {
				$collCcSchedules = CcScheduleQuery::create(null, $criteria)
					->filterByCcShowInstances($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcSchedules;
				}
				$this->collCcSchedules = $collCcSchedules;
			}
		}
		return $this->collCcSchedules;
	}

	/**
	 * Returns the number of related CcSchedule objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcSchedule objects.
	 * @throws     PropelException
	 */
	public function countCcSchedules(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcSchedules || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcSchedules) {
				return 0;
			} else {
				$query = CcScheduleQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcShowInstances($this)
					->count($con);
			}
		} else {
			return count($this->collCcSchedules);
		}
	}

	/**
	 * Method called to associate a CcSchedule object to this object
	 * through the CcSchedule foreign key attribute.
	 *
	 * @param      CcSchedule $l CcSchedule
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcSchedule(CcSchedule $l)
	{
		if ($this->collCcSchedules === null) {
			$this->initCcSchedules();
		}
		if (!$this->collCcSchedules->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcSchedules[]= $l;
			$l->setCcShowInstances($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcShowInstances is new, it will return
	 * an empty collection; or if this CcShowInstances has previously
	 * been saved, it will retrieve related CcSchedules from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcShowInstances.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcSchedule[] List of CcSchedule objects
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
	 * Otherwise if this CcShowInstances is new, it will return
	 * an empty collection; or if this CcShowInstances has previously
	 * been saved, it will retrieve related CcSchedules from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcShowInstances.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcSchedule[] List of CcSchedule objects
	 */
	public function getCcSchedulesJoinCcWebstream($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcScheduleQuery::create(null, $criteria);
		$query->joinWith('CcWebstream', $join_behavior);

		return $this->getCcSchedules($query, $con);
	}

	/**
	 * Clears out the collCcPlayoutHistorys collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcPlayoutHistorys()
	 */
	public function clearCcPlayoutHistorys()
	{
		$this->collCcPlayoutHistorys = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcPlayoutHistorys collection.
	 *
	 * By default this just sets the collCcPlayoutHistorys collection to an empty array (like clearcollCcPlayoutHistorys());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcPlayoutHistorys()
	{
		$this->collCcPlayoutHistorys = new PropelObjectCollection();
		$this->collCcPlayoutHistorys->setModel('CcPlayoutHistory');
	}

	/**
	 * Gets an array of CcPlayoutHistory objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcShowInstances is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcPlayoutHistory[] List of CcPlayoutHistory objects
	 * @throws     PropelException
	 */
	public function getCcPlayoutHistorys($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistorys || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
				// return empty collection
				$this->initCcPlayoutHistorys();
			} else {
				$collCcPlayoutHistorys = CcPlayoutHistoryQuery::create(null, $criteria)
					->filterByCcShowInstances($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcPlayoutHistorys;
				}
				$this->collCcPlayoutHistorys = $collCcPlayoutHistorys;
			}
		}
		return $this->collCcPlayoutHistorys;
	}

	/**
	 * Returns the number of related CcPlayoutHistory objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcPlayoutHistory objects.
	 * @throws     PropelException
	 */
	public function countCcPlayoutHistorys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistorys || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistorys) {
				return 0;
			} else {
				$query = CcPlayoutHistoryQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcShowInstances($this)
					->count($con);
			}
		} else {
			return count($this->collCcPlayoutHistorys);
		}
	}

	/**
	 * Method called to associate a CcPlayoutHistory object to this object
	 * through the CcPlayoutHistory foreign key attribute.
	 *
	 * @param      CcPlayoutHistory $l CcPlayoutHistory
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcPlayoutHistory(CcPlayoutHistory $l)
	{
		if ($this->collCcPlayoutHistorys === null) {
			$this->initCcPlayoutHistorys();
		}
		if (!$this->collCcPlayoutHistorys->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcPlayoutHistorys[]= $l;
			$l->setCcShowInstances($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcShowInstances is new, it will return
	 * an empty collection; or if this CcShowInstances has previously
	 * been saved, it will retrieve related CcPlayoutHistorys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcShowInstances.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcPlayoutHistory[] List of CcPlayoutHistory objects
	 */
	public function getCcPlayoutHistorysJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcPlayoutHistoryQuery::create(null, $criteria);
		$query->joinWith('CcFiles', $join_behavior);

		return $this->getCcPlayoutHistorys($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->starts = null;
		$this->ends = null;
		$this->show_id = null;
		$this->record = null;
		$this->rebroadcast = null;
		$this->instance_id = null;
		$this->file_id = null;
		$this->time_filled = null;
		$this->created = null;
		$this->last_scheduled = null;
		$this->modified_instance = null;
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
			if ($this->collCcShowInstancessRelatedByDbId) {
				foreach ((array) $this->collCcShowInstancessRelatedByDbId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCcSchedules) {
				foreach ((array) $this->collCcSchedules as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCcPlayoutHistorys) {
				foreach ((array) $this->collCcPlayoutHistorys as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCcShowInstancessRelatedByDbId = null;
		$this->collCcSchedules = null;
		$this->collCcPlayoutHistorys = null;
		$this->aCcShow = null;
		$this->aCcShowInstancesRelatedByDbOriginalShow = null;
		$this->aCcFiles = null;
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

} // BaseCcShowInstances
