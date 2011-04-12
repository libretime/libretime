<?php


/**
 * Base class that represents a row from the 'cc_schedule' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSchedule extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcSchedulePeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcSchedulePeer
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
	 * The value for the group_id field.
	 * @var        int
	 */
	protected $group_id;

	/**
	 * The value for the file_id field.
	 * @var        int
	 */
	protected $file_id;

	/**
	 * The value for the clip_length field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $clip_length;

	/**
	 * The value for the fade_in field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fade_in;

	/**
	 * The value for the fade_out field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fade_out;

	/**
	 * The value for the cue_in field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $cue_in;

	/**
	 * The value for the cue_out field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $cue_out;

	/**
	 * The value for the schedule_group_played field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $schedule_group_played;

	/**
	 * The value for the media_item_played field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $media_item_played;

	/**
	 * The value for the instance_id field.
	 * @var        int
	 */
	protected $instance_id;

	/**
	 * @var        CcShowInstances
	 */
	protected $aCcShowInstances;

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

	// aggregate_column_relation behavior
	protected $oldCcShowInstances;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->clip_length = '00:00:00';
		$this->fade_in = '00:00:00';
		$this->fade_out = '00:00:00';
		$this->cue_in = '00:00:00';
		$this->cue_out = '00:00:00';
		$this->schedule_group_played = false;
		$this->media_item_played = false;
	}

	/**
	 * Initializes internal state of BaseCcSchedule object.
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
	 * Get the [group_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbGroupId()
	{
		return $this->group_id;
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
	 * Get the [optionally formatted] temporal [clip_length] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbClipLength($format = '%X')
	{
		if ($this->clip_length === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->clip_length);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->clip_length, true), $x);
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
	 * Get the [optionally formatted] temporal [fade_in] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbFadeIn($format = '%X')
	{
		if ($this->fade_in === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fade_in);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fade_in, true), $x);
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
	 * Get the [optionally formatted] temporal [fade_out] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbFadeOut($format = '%X')
	{
		if ($this->fade_out === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->fade_out);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->fade_out, true), $x);
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
	 * Get the [optionally formatted] temporal [cue_in] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCueIn($format = '%X')
	{
		if ($this->cue_in === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->cue_in);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->cue_in, true), $x);
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
	 * Get the [optionally formatted] temporal [cue_out] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbCueOut($format = '%X')
	{
		if ($this->cue_out === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->cue_out);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->cue_out, true), $x);
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
	 * Get the [schedule_group_played] column value.
	 * 
	 * @return     boolean
	 */
	public function getDbScheduleGroupPlayed()
	{
		return $this->schedule_group_played;
	}

	/**
	 * Get the [media_item_played] column value.
	 * 
	 * @return     boolean
	 */
	public function getDbMediaItemPlayed()
	{
		return $this->media_item_played;
	}

	/**
	 * Get the [instance_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbInstanceId()
	{
		return $this->instance_id;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcSchedulePeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [playlist_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbPlaylistId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->playlist_id !== $v) {
			$this->playlist_id = $v;
			$this->modifiedColumns[] = CcSchedulePeer::PLAYLIST_ID;
		}

		return $this;
	} // setDbPlaylistId()

	/**
	 * Sets the value of [starts] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
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
				$this->modifiedColumns[] = CcSchedulePeer::STARTS;
			}
		} // if either are not null

		return $this;
	} // setDbStarts()

	/**
	 * Sets the value of [ends] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
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
				$this->modifiedColumns[] = CcSchedulePeer::ENDS;
			}
		} // if either are not null

		return $this;
	} // setDbEnds()

	/**
	 * Set the value of [group_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbGroupId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->group_id !== $v) {
			$this->group_id = $v;
			$this->modifiedColumns[] = CcSchedulePeer::GROUP_ID;
		}

		return $this;
	} // setDbGroupId()

	/**
	 * Set the value of [file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbFileId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_id !== $v) {
			$this->file_id = $v;
			$this->modifiedColumns[] = CcSchedulePeer::FILE_ID;
		}

		return $this;
	} // setDbFileId()

	/**
	 * Sets the value of [clip_length] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbClipLength($v)
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

		if ( $this->clip_length !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->clip_length !== null && $tmpDt = new DateTime($this->clip_length)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->clip_length = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcSchedulePeer::CLIP_LENGTH;
			}
		} // if either are not null

		return $this;
	} // setDbClipLength()

	/**
	 * Sets the value of [fade_in] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbFadeIn($v)
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

		if ( $this->fade_in !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fade_in !== null && $tmpDt = new DateTime($this->fade_in)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->fade_in = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcSchedulePeer::FADE_IN;
			}
		} // if either are not null

		return $this;
	} // setDbFadeIn()

	/**
	 * Sets the value of [fade_out] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbFadeOut($v)
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

		if ( $this->fade_out !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->fade_out !== null && $tmpDt = new DateTime($this->fade_out)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->fade_out = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcSchedulePeer::FADE_OUT;
			}
		} // if either are not null

		return $this;
	} // setDbFadeOut()

	/**
	 * Sets the value of [cue_in] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbCueIn($v)
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

		if ( $this->cue_in !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->cue_in !== null && $tmpDt = new DateTime($this->cue_in)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->cue_in = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcSchedulePeer::CUE_IN;
			}
		} // if either are not null

		return $this;
	} // setDbCueIn()

	/**
	 * Sets the value of [cue_out] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbCueOut($v)
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

		if ( $this->cue_out !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->cue_out !== null && $tmpDt = new DateTime($this->cue_out)) ? $tmpDt->format('H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					|| ($dt->format('H:i:s') === '00:00:00') // or the entered value matches the default
					)
			{
				$this->cue_out = ($dt ? $dt->format('H:i:s') : null);
				$this->modifiedColumns[] = CcSchedulePeer::CUE_OUT;
			}
		} // if either are not null

		return $this;
	} // setDbCueOut()

	/**
	 * Set the value of [schedule_group_played] column.
	 * 
	 * @param      boolean $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbScheduleGroupPlayed($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->schedule_group_played !== $v || $this->isNew()) {
			$this->schedule_group_played = $v;
			$this->modifiedColumns[] = CcSchedulePeer::SCHEDULE_GROUP_PLAYED;
		}

		return $this;
	} // setDbScheduleGroupPlayed()

	/**
	 * Set the value of [media_item_played] column.
	 * 
	 * @param      boolean $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbMediaItemPlayed($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->media_item_played !== $v || $this->isNew()) {
			$this->media_item_played = $v;
			$this->modifiedColumns[] = CcSchedulePeer::MEDIA_ITEM_PLAYED;
		}

		return $this;
	} // setDbMediaItemPlayed()

	/**
	 * Set the value of [instance_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcSchedule The current object (for fluent API support)
	 */
	public function setDbInstanceId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->instance_id !== $v) {
			$this->instance_id = $v;
			$this->modifiedColumns[] = CcSchedulePeer::INSTANCE_ID;
		}

		if ($this->aCcShowInstances !== null && $this->aCcShowInstances->getDbId() !== $v) {
			$this->aCcShowInstances = null;
		}

		return $this;
	} // setDbInstanceId()

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
			if ($this->clip_length !== '00:00:00') {
				return false;
			}

			if ($this->fade_in !== '00:00:00') {
				return false;
			}

			if ($this->fade_out !== '00:00:00') {
				return false;
			}

			if ($this->cue_in !== '00:00:00') {
				return false;
			}

			if ($this->cue_out !== '00:00:00') {
				return false;
			}

			if ($this->schedule_group_played !== false) {
				return false;
			}

			if ($this->media_item_played !== false) {
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
			$this->starts = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->ends = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->group_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->file_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->clip_length = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->fade_in = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->fade_out = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->cue_in = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->cue_out = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->schedule_group_played = ($row[$startcol + 11] !== null) ? (boolean) $row[$startcol + 11] : null;
			$this->media_item_played = ($row[$startcol + 12] !== null) ? (boolean) $row[$startcol + 12] : null;
			$this->instance_id = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 14; // 14 = CcSchedulePeer::NUM_COLUMNS - CcSchedulePeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcSchedule object", $e);
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

		if ($this->aCcShowInstances !== null && $this->instance_id !== $this->aCcShowInstances->getDbId()) {
			$this->aCcShowInstances = null;
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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcSchedulePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcShowInstances = null;
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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcScheduleQuery::create()
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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				// aggregate_column_relation behavior
				$this->updateRelatedCcShowInstances($con);
				CcSchedulePeer::addInstanceToPool($this);
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

			if ($this->aCcShowInstances !== null) {
				if ($this->aCcShowInstances->isModified() || $this->aCcShowInstances->isNew()) {
					$affectedRows += $this->aCcShowInstances->save($con);
				}
				$this->setCcShowInstances($this->aCcShowInstances);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcSchedulePeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcSchedulePeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcSchedulePeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcSchedulePeer::doUpdate($this, $con);
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

			if ($this->aCcShowInstances !== null) {
				if (!$this->aCcShowInstances->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcShowInstances->getValidationFailures());
				}
			}


			if (($retval = CcSchedulePeer::doValidate($this, $columns)) !== true) {
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
		$pos = CcSchedulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDbStarts();
				break;
			case 3:
				return $this->getDbEnds();
				break;
			case 4:
				return $this->getDbGroupId();
				break;
			case 5:
				return $this->getDbFileId();
				break;
			case 6:
				return $this->getDbClipLength();
				break;
			case 7:
				return $this->getDbFadeIn();
				break;
			case 8:
				return $this->getDbFadeOut();
				break;
			case 9:
				return $this->getDbCueIn();
				break;
			case 10:
				return $this->getDbCueOut();
				break;
			case 11:
				return $this->getDbScheduleGroupPlayed();
				break;
			case 12:
				return $this->getDbMediaItemPlayed();
				break;
			case 13:
				return $this->getDbInstanceId();
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
		$keys = CcSchedulePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbPlaylistId(),
			$keys[2] => $this->getDbStarts(),
			$keys[3] => $this->getDbEnds(),
			$keys[4] => $this->getDbGroupId(),
			$keys[5] => $this->getDbFileId(),
			$keys[6] => $this->getDbClipLength(),
			$keys[7] => $this->getDbFadeIn(),
			$keys[8] => $this->getDbFadeOut(),
			$keys[9] => $this->getDbCueIn(),
			$keys[10] => $this->getDbCueOut(),
			$keys[11] => $this->getDbScheduleGroupPlayed(),
			$keys[12] => $this->getDbMediaItemPlayed(),
			$keys[13] => $this->getDbInstanceId(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcShowInstances) {
				$result['CcShowInstances'] = $this->aCcShowInstances->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = CcSchedulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDbStarts($value);
				break;
			case 3:
				$this->setDbEnds($value);
				break;
			case 4:
				$this->setDbGroupId($value);
				break;
			case 5:
				$this->setDbFileId($value);
				break;
			case 6:
				$this->setDbClipLength($value);
				break;
			case 7:
				$this->setDbFadeIn($value);
				break;
			case 8:
				$this->setDbFadeOut($value);
				break;
			case 9:
				$this->setDbCueIn($value);
				break;
			case 10:
				$this->setDbCueOut($value);
				break;
			case 11:
				$this->setDbScheduleGroupPlayed($value);
				break;
			case 12:
				$this->setDbMediaItemPlayed($value);
				break;
			case 13:
				$this->setDbInstanceId($value);
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
		$keys = CcSchedulePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbPlaylistId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbStarts($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbEnds($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbGroupId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbFileId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDbClipLength($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDbFadeIn($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDbFadeOut($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDbCueIn($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDbCueOut($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDbScheduleGroupPlayed($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDbMediaItemPlayed($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setDbInstanceId($arr[$keys[13]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);

		if ($this->isColumnModified(CcSchedulePeer::ID)) $criteria->add(CcSchedulePeer::ID, $this->id);
		if ($this->isColumnModified(CcSchedulePeer::PLAYLIST_ID)) $criteria->add(CcSchedulePeer::PLAYLIST_ID, $this->playlist_id);
		if ($this->isColumnModified(CcSchedulePeer::STARTS)) $criteria->add(CcSchedulePeer::STARTS, $this->starts);
		if ($this->isColumnModified(CcSchedulePeer::ENDS)) $criteria->add(CcSchedulePeer::ENDS, $this->ends);
		if ($this->isColumnModified(CcSchedulePeer::GROUP_ID)) $criteria->add(CcSchedulePeer::GROUP_ID, $this->group_id);
		if ($this->isColumnModified(CcSchedulePeer::FILE_ID)) $criteria->add(CcSchedulePeer::FILE_ID, $this->file_id);
		if ($this->isColumnModified(CcSchedulePeer::CLIP_LENGTH)) $criteria->add(CcSchedulePeer::CLIP_LENGTH, $this->clip_length);
		if ($this->isColumnModified(CcSchedulePeer::FADE_IN)) $criteria->add(CcSchedulePeer::FADE_IN, $this->fade_in);
		if ($this->isColumnModified(CcSchedulePeer::FADE_OUT)) $criteria->add(CcSchedulePeer::FADE_OUT, $this->fade_out);
		if ($this->isColumnModified(CcSchedulePeer::CUE_IN)) $criteria->add(CcSchedulePeer::CUE_IN, $this->cue_in);
		if ($this->isColumnModified(CcSchedulePeer::CUE_OUT)) $criteria->add(CcSchedulePeer::CUE_OUT, $this->cue_out);
		if ($this->isColumnModified(CcSchedulePeer::SCHEDULE_GROUP_PLAYED)) $criteria->add(CcSchedulePeer::SCHEDULE_GROUP_PLAYED, $this->schedule_group_played);
		if ($this->isColumnModified(CcSchedulePeer::MEDIA_ITEM_PLAYED)) $criteria->add(CcSchedulePeer::MEDIA_ITEM_PLAYED, $this->media_item_played);
		if ($this->isColumnModified(CcSchedulePeer::INSTANCE_ID)) $criteria->add(CcSchedulePeer::INSTANCE_ID, $this->instance_id);

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
		$criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);
		$criteria->add(CcSchedulePeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcSchedule (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbPlaylistId($this->playlist_id);
		$copyObj->setDbStarts($this->starts);
		$copyObj->setDbEnds($this->ends);
		$copyObj->setDbGroupId($this->group_id);
		$copyObj->setDbFileId($this->file_id);
		$copyObj->setDbClipLength($this->clip_length);
		$copyObj->setDbFadeIn($this->fade_in);
		$copyObj->setDbFadeOut($this->fade_out);
		$copyObj->setDbCueIn($this->cue_in);
		$copyObj->setDbCueOut($this->cue_out);
		$copyObj->setDbScheduleGroupPlayed($this->schedule_group_played);
		$copyObj->setDbMediaItemPlayed($this->media_item_played);
		$copyObj->setDbInstanceId($this->instance_id);

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
	 * @return     CcSchedule Clone of current object.
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
	 * @return     CcSchedulePeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcSchedulePeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcShowInstances object.
	 *
	 * @param      CcShowInstances $v
	 * @return     CcSchedule The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcShowInstances(CcShowInstances $v = null)
	{
		// aggregate_column_relation behavior
		if (null !== $this->aCcShowInstances && $v !== $this->aCcShowInstances) {
			$this->oldCcShowInstances = $this->aCcShowInstances;
		}
		if ($v === null) {
			$this->setDbInstanceId(NULL);
		} else {
			$this->setDbInstanceId($v->getDbId());
		}

		$this->aCcShowInstances = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcShowInstances object, it will not be re-added.
		if ($v !== null) {
			$v->addCcSchedule($this);
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
	public function getCcShowInstances(PropelPDO $con = null)
	{
		if ($this->aCcShowInstances === null && ($this->instance_id !== null)) {
			$this->aCcShowInstances = CcShowInstancesQuery::create()->findPk($this->instance_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcShowInstances->addCcSchedules($this);
			 */
		}
		return $this->aCcShowInstances;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->playlist_id = null;
		$this->starts = null;
		$this->ends = null;
		$this->group_id = null;
		$this->file_id = null;
		$this->clip_length = null;
		$this->fade_in = null;
		$this->fade_out = null;
		$this->cue_in = null;
		$this->cue_out = null;
		$this->schedule_group_played = null;
		$this->media_item_played = null;
		$this->instance_id = null;
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

		$this->aCcShowInstances = null;
	}

	// aggregate_column_relation behavior
	
	/**
	 * Update the aggregate column in the related CcShowInstances object
	 *
	 * @param PropelPDO $con A connection object
	 */
	protected function updateRelatedCcShowInstances(PropelPDO $con)
	{
		if ($ccShowInstances = $this->getCcShowInstances()) {
			$ccShowInstances->updateDbTimeFilled($con);
		}
		if ($this->oldCcShowInstances) {
			$this->oldCcShowInstances->updateDbTimeFilled($con);
			$this->oldCcShowInstances = null;
		}
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

} // BaseCcSchedule
