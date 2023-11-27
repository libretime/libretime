<?php


/**
 * Base class that represents a row from the 'cc_trans' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTrans extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcTransPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcTransPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the trtok field.
	 * @var        string
	 */
	protected $trtok;

	/**
	 * The value for the direction field.
	 * @var        string
	 */
	protected $direction;

	/**
	 * The value for the state field.
	 * @var        string
	 */
	protected $state;

	/**
	 * The value for the trtype field.
	 * @var        string
	 */
	protected $trtype;

	/**
	 * The value for the lock field.
	 * Note: this column has a database default value of: 'N'
	 * @var        string
	 */
	protected $lock;

	/**
	 * The value for the target field.
	 * @var        string
	 */
	protected $target;

	/**
	 * The value for the rtrtok field.
	 * @var        string
	 */
	protected $rtrtok;

	/**
	 * The value for the mdtrtok field.
	 * @var        string
	 */
	protected $mdtrtok;

	/**
	 * The value for the gunid field.
	 * @var        string
	 */
	protected $gunid;

	/**
	 * The value for the pdtoken field.
	 * @var        string
	 */
	protected $pdtoken;

	/**
	 * The value for the url field.
	 * @var        string
	 */
	protected $url;

	/**
	 * The value for the localfile field.
	 * @var        string
	 */
	protected $localfile;

	/**
	 * The value for the fname field.
	 * @var        string
	 */
	protected $fname;

	/**
	 * The value for the title field.
	 * @var        string
	 */
	protected $title;

	/**
	 * The value for the expectedsum field.
	 * @var        string
	 */
	protected $expectedsum;

	/**
	 * The value for the realsum field.
	 * @var        string
	 */
	protected $realsum;

	/**
	 * The value for the expectedsize field.
	 * @var        int
	 */
	protected $expectedsize;

	/**
	 * The value for the realsize field.
	 * @var        int
	 */
	protected $realsize;

	/**
	 * The value for the uid field.
	 * @var        int
	 */
	protected $uid;

	/**
	 * The value for the errmsg field.
	 * @var        string
	 */
	protected $errmsg;

	/**
	 * The value for the jobpid field.
	 * @var        int
	 */
	protected $jobpid;

	/**
	 * The value for the start field.
	 * @var        string
	 */
	protected $start;

	/**
	 * The value for the ts field.
	 * @var        string
	 */
	protected $ts;

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
		$this->lock = 'N';
	}

	/**
	 * Initializes internal state of BaseCcTrans object.
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
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [trtok] column value.
	 *
	 * @return     string
	 */
	public function getTrtok()
	{
		return $this->trtok;
	}

	/**
	 * Get the [direction] column value.
	 *
	 * @return     string
	 */
	public function getDirection()
	{
		return $this->direction;
	}

	/**
	 * Get the [state] column value.
	 *
	 * @return     string
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get the [trtype] column value.
	 *
	 * @return     string
	 */
	public function getTrtype()
	{
		return $this->trtype;
	}

	/**
	 * Get the [lock] column value.
	 *
	 * @return     string
	 */
	public function getLock()
	{
		return $this->lock;
	}

	/**
	 * Get the [target] column value.
	 *
	 * @return     string
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * Get the [rtrtok] column value.
	 *
	 * @return     string
	 */
	public function getRtrtok()
	{
		return $this->rtrtok;
	}

	/**
	 * Get the [mdtrtok] column value.
	 *
	 * @return     string
	 */
	public function getMdtrtok()
	{
		return $this->mdtrtok;
	}

	/**
	 * Get the [gunid] column value.
	 *
	 * @return     string
	 */
	public function getGunid()
	{
		return $this->gunid;
	}

	/**
	 * Get the [pdtoken] column value.
	 *
	 * @return     string
	 */
	public function getPdtoken()
	{
		return $this->pdtoken;
	}

	/**
	 * Get the [url] column value.
	 *
	 * @return     string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Get the [localfile] column value.
	 *
	 * @return     string
	 */
	public function getLocalfile()
	{
		return $this->localfile;
	}

	/**
	 * Get the [fname] column value.
	 *
	 * @return     string
	 */
	public function getFname()
	{
		return $this->fname;
	}

	/**
	 * Get the [title] column value.
	 *
	 * @return     string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Get the [expectedsum] column value.
	 *
	 * @return     string
	 */
	public function getExpectedsum()
	{
		return $this->expectedsum;
	}

	/**
	 * Get the [realsum] column value.
	 *
	 * @return     string
	 */
	public function getRealsum()
	{
		return $this->realsum;
	}

	/**
	 * Get the [expectedsize] column value.
	 *
	 * @return     int
	 */
	public function getExpectedsize()
	{
		return $this->expectedsize;
	}

	/**
	 * Get the [realsize] column value.
	 *
	 * @return     int
	 */
	public function getRealsize()
	{
		return $this->realsize;
	}

	/**
	 * Get the [uid] column value.
	 *
	 * @return     int
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * Get the [errmsg] column value.
	 *
	 * @return     string
	 */
	public function getErrmsg()
	{
		return $this->errmsg;
	}

	/**
	 * Get the [jobpid] column value.
	 *
	 * @return     int
	 */
	public function getJobpid()
	{
		return $this->jobpid;
	}

	/**
	 * Get the [optionally formatted] temporal [start] column value.
	 *
	 *
	 * @param      string $format The date/time format string (date()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getStart($format = 'Y-m-d H:i:s')
	{
		if ($this->start === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->start);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			throw new PropelException('strftime format not supported anymore');
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [ts] column value.
	 *
	 *
	 * @param      string $format The date/time format string (date()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getTs($format = 'Y-m-d H:i:s')
	{
		if ($this->ts === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->ts);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->ts, true), $x);
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			throw new PropelException('strftime format not supported anymore');
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Set the value of [id] column.
	 *
	 * @param      int $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcTransPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [trtok] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setTrtok($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->trtok !== $v) {
			$this->trtok = $v;
			$this->modifiedColumns[] = CcTransPeer::TRTOK;
		}

		return $this;
	} // setTrtok()

	/**
	 * Set the value of [direction] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setDirection($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->direction !== $v) {
			$this->direction = $v;
			$this->modifiedColumns[] = CcTransPeer::DIRECTION;
		}

		return $this;
	} // setDirection()

	/**
	 * Set the value of [state] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setState($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->state !== $v) {
			$this->state = $v;
			$this->modifiedColumns[] = CcTransPeer::STATE;
		}

		return $this;
	} // setState()

	/**
	 * Set the value of [trtype] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setTrtype($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->trtype !== $v) {
			$this->trtype = $v;
			$this->modifiedColumns[] = CcTransPeer::TRTYPE;
		}

		return $this;
	} // setTrtype()

	/**
	 * Set the value of [lock] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setLock($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->lock !== $v || $this->isNew()) {
			$this->lock = $v;
			$this->modifiedColumns[] = CcTransPeer::LOCK;
		}

		return $this;
	} // setLock()

	/**
	 * Set the value of [target] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setTarget($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->target !== $v) {
			$this->target = $v;
			$this->modifiedColumns[] = CcTransPeer::TARGET;
		}

		return $this;
	} // setTarget()

	/**
	 * Set the value of [rtrtok] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setRtrtok($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->rtrtok !== $v) {
			$this->rtrtok = $v;
			$this->modifiedColumns[] = CcTransPeer::RTRTOK;
		}

		return $this;
	} // setRtrtok()

	/**
	 * Set the value of [mdtrtok] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setMdtrtok($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mdtrtok !== $v) {
			$this->mdtrtok = $v;
			$this->modifiedColumns[] = CcTransPeer::MDTRTOK;
		}

		return $this;
	} // setMdtrtok()

	/**
	 * Set the value of [gunid] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setGunid($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->gunid !== $v) {
			$this->gunid = $v;
			$this->modifiedColumns[] = CcTransPeer::GUNID;
		}

		return $this;
	} // setGunid()

	/**
	 * Set the value of [pdtoken] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setPdtoken($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->pdtoken !== $v) {
			$this->pdtoken = $v;
			$this->modifiedColumns[] = CcTransPeer::PDTOKEN;
		}

		return $this;
	} // setPdtoken()

	/**
	 * Set the value of [url] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setUrl($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url !== $v) {
			$this->url = $v;
			$this->modifiedColumns[] = CcTransPeer::URL;
		}

		return $this;
	} // setUrl()

	/**
	 * Set the value of [localfile] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setLocalfile($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->localfile !== $v) {
			$this->localfile = $v;
			$this->modifiedColumns[] = CcTransPeer::LOCALFILE;
		}

		return $this;
	} // setLocalfile()

	/**
	 * Set the value of [fname] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setFname($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fname !== $v) {
			$this->fname = $v;
			$this->modifiedColumns[] = CcTransPeer::FNAME;
		}

		return $this;
	} // setFname()

	/**
	 * Set the value of [title] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setTitle($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = CcTransPeer::TITLE;
		}

		return $this;
	} // setTitle()

	/**
	 * Set the value of [expectedsum] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setExpectedsum($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->expectedsum !== $v) {
			$this->expectedsum = $v;
			$this->modifiedColumns[] = CcTransPeer::EXPECTEDSUM;
		}

		return $this;
	} // setExpectedsum()

	/**
	 * Set the value of [realsum] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setRealsum($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->realsum !== $v) {
			$this->realsum = $v;
			$this->modifiedColumns[] = CcTransPeer::REALSUM;
		}

		return $this;
	} // setRealsum()

	/**
	 * Set the value of [expectedsize] column.
	 *
	 * @param      int $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setExpectedsize($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->expectedsize !== $v) {
			$this->expectedsize = $v;
			$this->modifiedColumns[] = CcTransPeer::EXPECTEDSIZE;
		}

		return $this;
	} // setExpectedsize()

	/**
	 * Set the value of [realsize] column.
	 *
	 * @param      int $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setRealsize($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->realsize !== $v) {
			$this->realsize = $v;
			$this->modifiedColumns[] = CcTransPeer::REALSIZE;
		}

		return $this;
	} // setRealsize()

	/**
	 * Set the value of [uid] column.
	 *
	 * @param      int $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setUid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->uid !== $v) {
			$this->uid = $v;
			$this->modifiedColumns[] = CcTransPeer::UID;
		}

		return $this;
	} // setUid()

	/**
	 * Set the value of [errmsg] column.
	 *
	 * @param      string $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setErrmsg($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->errmsg !== $v) {
			$this->errmsg = $v;
			$this->modifiedColumns[] = CcTransPeer::ERRMSG;
		}

		return $this;
	} // setErrmsg()

	/**
	 * Set the value of [jobpid] column.
	 *
	 * @param      int $v new value
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setJobpid($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->jobpid !== $v) {
			$this->jobpid = $v;
			$this->modifiedColumns[] = CcTransPeer::JOBPID;
		}

		return $this;
	} // setJobpid()

	/**
	 * Sets the value of [start] column to a normalized version of the date/time value specified.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setStart($v)
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

		if ( $this->start !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->start !== null && $tmpDt = new DateTime($this->start)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match
					)
			{
				$this->start = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcTransPeer::START;
			}
		} // if either are not null

		return $this;
	} // setStart()

	/**
	 * Sets the value of [ts] column to a normalized version of the date/time value specified.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcTrans The current object (for fluent API support)
	 */
	public function setTs($v)
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

		if ( $this->ts !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->ts !== null && $tmpDt = new DateTime($this->ts)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match
					)
			{
				$this->ts = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcTransPeer::TS;
			}
		} // if either are not null

		return $this;
	} // setTs()

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
			if ($this->lock !== 'N') {
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
			$this->trtok = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->direction = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->state = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->trtype = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->lock = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->target = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->rtrtok = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->mdtrtok = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->gunid = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->pdtoken = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->url = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->localfile = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->fname = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
			$this->title = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->expectedsum = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->realsum = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->expectedsize = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->realsize = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->uid = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->errmsg = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->jobpid = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->start = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->ts = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 24; // 24 = CcTransPeer::NUM_COLUMNS - CcTransPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcTrans object", $e);
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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcTransPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcTransQuery::create()
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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcTransPeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcTransPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcTransPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcTransPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = CcTransPeer::doUpdate($this, $con);
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


			if (($retval = CcTransPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CcTransPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getId();
				break;
			case 1:
				return $this->getTrtok();
				break;
			case 2:
				return $this->getDirection();
				break;
			case 3:
				return $this->getState();
				break;
			case 4:
				return $this->getTrtype();
				break;
			case 5:
				return $this->getLock();
				break;
			case 6:
				return $this->getTarget();
				break;
			case 7:
				return $this->getRtrtok();
				break;
			case 8:
				return $this->getMdtrtok();
				break;
			case 9:
				return $this->getGunid();
				break;
			case 10:
				return $this->getPdtoken();
				break;
			case 11:
				return $this->getUrl();
				break;
			case 12:
				return $this->getLocalfile();
				break;
			case 13:
				return $this->getFname();
				break;
			case 14:
				return $this->getTitle();
				break;
			case 15:
				return $this->getExpectedsum();
				break;
			case 16:
				return $this->getRealsum();
				break;
			case 17:
				return $this->getExpectedsize();
				break;
			case 18:
				return $this->getRealsize();
				break;
			case 19:
				return $this->getUid();
				break;
			case 20:
				return $this->getErrmsg();
				break;
			case 21:
				return $this->getJobpid();
				break;
			case 22:
				return $this->getStart();
				break;
			case 23:
				return $this->getTs();
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
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = CcTransPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getTrtok(),
			$keys[2] => $this->getDirection(),
			$keys[3] => $this->getState(),
			$keys[4] => $this->getTrtype(),
			$keys[5] => $this->getLock(),
			$keys[6] => $this->getTarget(),
			$keys[7] => $this->getRtrtok(),
			$keys[8] => $this->getMdtrtok(),
			$keys[9] => $this->getGunid(),
			$keys[10] => $this->getPdtoken(),
			$keys[11] => $this->getUrl(),
			$keys[12] => $this->getLocalfile(),
			$keys[13] => $this->getFname(),
			$keys[14] => $this->getTitle(),
			$keys[15] => $this->getExpectedsum(),
			$keys[16] => $this->getRealsum(),
			$keys[17] => $this->getExpectedsize(),
			$keys[18] => $this->getRealsize(),
			$keys[19] => $this->getUid(),
			$keys[20] => $this->getErrmsg(),
			$keys[21] => $this->getJobpid(),
			$keys[22] => $this->getStart(),
			$keys[23] => $this->getTs(),
		);
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
		$pos = CcTransPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setId($value);
				break;
			case 1:
				$this->setTrtok($value);
				break;
			case 2:
				$this->setDirection($value);
				break;
			case 3:
				$this->setState($value);
				break;
			case 4:
				$this->setTrtype($value);
				break;
			case 5:
				$this->setLock($value);
				break;
			case 6:
				$this->setTarget($value);
				break;
			case 7:
				$this->setRtrtok($value);
				break;
			case 8:
				$this->setMdtrtok($value);
				break;
			case 9:
				$this->setGunid($value);
				break;
			case 10:
				$this->setPdtoken($value);
				break;
			case 11:
				$this->setUrl($value);
				break;
			case 12:
				$this->setLocalfile($value);
				break;
			case 13:
				$this->setFname($value);
				break;
			case 14:
				$this->setTitle($value);
				break;
			case 15:
				$this->setExpectedsum($value);
				break;
			case 16:
				$this->setRealsum($value);
				break;
			case 17:
				$this->setExpectedsize($value);
				break;
			case 18:
				$this->setRealsize($value);
				break;
			case 19:
				$this->setUid($value);
				break;
			case 20:
				$this->setErrmsg($value);
				break;
			case 21:
				$this->setJobpid($value);
				break;
			case 22:
				$this->setStart($value);
				break;
			case 23:
				$this->setTs($value);
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
		$keys = CcTransPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setTrtok($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDirection($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setState($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setTrtype($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setLock($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setTarget($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setRtrtok($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setMdtrtok($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setGunid($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setPdtoken($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setUrl($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setLocalfile($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setFname($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setTitle($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setExpectedsum($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setRealsum($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setExpectedsize($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setRealsize($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setUid($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setErrmsg($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setJobpid($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setStart($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setTs($arr[$keys[23]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcTransPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcTransPeer::ID)) $criteria->add(CcTransPeer::ID, $this->id);
		if ($this->isColumnModified(CcTransPeer::TRTOK)) $criteria->add(CcTransPeer::TRTOK, $this->trtok);
		if ($this->isColumnModified(CcTransPeer::DIRECTION)) $criteria->add(CcTransPeer::DIRECTION, $this->direction);
		if ($this->isColumnModified(CcTransPeer::STATE)) $criteria->add(CcTransPeer::STATE, $this->state);
		if ($this->isColumnModified(CcTransPeer::TRTYPE)) $criteria->add(CcTransPeer::TRTYPE, $this->trtype);
		if ($this->isColumnModified(CcTransPeer::LOCK)) $criteria->add(CcTransPeer::LOCK, $this->lock);
		if ($this->isColumnModified(CcTransPeer::TARGET)) $criteria->add(CcTransPeer::TARGET, $this->target);
		if ($this->isColumnModified(CcTransPeer::RTRTOK)) $criteria->add(CcTransPeer::RTRTOK, $this->rtrtok);
		if ($this->isColumnModified(CcTransPeer::MDTRTOK)) $criteria->add(CcTransPeer::MDTRTOK, $this->mdtrtok);
		if ($this->isColumnModified(CcTransPeer::GUNID)) $criteria->add(CcTransPeer::GUNID, $this->gunid);
		if ($this->isColumnModified(CcTransPeer::PDTOKEN)) $criteria->add(CcTransPeer::PDTOKEN, $this->pdtoken);
		if ($this->isColumnModified(CcTransPeer::URL)) $criteria->add(CcTransPeer::URL, $this->url);
		if ($this->isColumnModified(CcTransPeer::LOCALFILE)) $criteria->add(CcTransPeer::LOCALFILE, $this->localfile);
		if ($this->isColumnModified(CcTransPeer::FNAME)) $criteria->add(CcTransPeer::FNAME, $this->fname);
		if ($this->isColumnModified(CcTransPeer::TITLE)) $criteria->add(CcTransPeer::TITLE, $this->title);
		if ($this->isColumnModified(CcTransPeer::EXPECTEDSUM)) $criteria->add(CcTransPeer::EXPECTEDSUM, $this->expectedsum);
		if ($this->isColumnModified(CcTransPeer::REALSUM)) $criteria->add(CcTransPeer::REALSUM, $this->realsum);
		if ($this->isColumnModified(CcTransPeer::EXPECTEDSIZE)) $criteria->add(CcTransPeer::EXPECTEDSIZE, $this->expectedsize);
		if ($this->isColumnModified(CcTransPeer::REALSIZE)) $criteria->add(CcTransPeer::REALSIZE, $this->realsize);
		if ($this->isColumnModified(CcTransPeer::UID)) $criteria->add(CcTransPeer::UID, $this->uid);
		if ($this->isColumnModified(CcTransPeer::ERRMSG)) $criteria->add(CcTransPeer::ERRMSG, $this->errmsg);
		if ($this->isColumnModified(CcTransPeer::JOBPID)) $criteria->add(CcTransPeer::JOBPID, $this->jobpid);
		if ($this->isColumnModified(CcTransPeer::START)) $criteria->add(CcTransPeer::START, $this->start);
		if ($this->isColumnModified(CcTransPeer::TS)) $criteria->add(CcTransPeer::TS, $this->ts);

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
		$criteria = new Criteria(CcTransPeer::DATABASE_NAME);
		$criteria->add(CcTransPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
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
	 * @param      object $copyObj An object of CcTrans (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setTrtok($this->trtok);
		$copyObj->setDirection($this->direction);
		$copyObj->setState($this->state);
		$copyObj->setTrtype($this->trtype);
		$copyObj->setLock($this->lock);
		$copyObj->setTarget($this->target);
		$copyObj->setRtrtok($this->rtrtok);
		$copyObj->setMdtrtok($this->mdtrtok);
		$copyObj->setGunid($this->gunid);
		$copyObj->setPdtoken($this->pdtoken);
		$copyObj->setUrl($this->url);
		$copyObj->setLocalfile($this->localfile);
		$copyObj->setFname($this->fname);
		$copyObj->setTitle($this->title);
		$copyObj->setExpectedsum($this->expectedsum);
		$copyObj->setRealsum($this->realsum);
		$copyObj->setExpectedsize($this->expectedsize);
		$copyObj->setRealsize($this->realsize);
		$copyObj->setUid($this->uid);
		$copyObj->setErrmsg($this->errmsg);
		$copyObj->setJobpid($this->jobpid);
		$copyObj->setStart($this->start);
		$copyObj->setTs($this->ts);

		$copyObj->setNew(true);
		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value
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
	 * @return     CcTrans Clone of current object.
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
	 * @return     CcTransPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcTransPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->trtok = null;
		$this->direction = null;
		$this->state = null;
		$this->trtype = null;
		$this->lock = null;
		$this->target = null;
		$this->rtrtok = null;
		$this->mdtrtok = null;
		$this->gunid = null;
		$this->pdtoken = null;
		$this->url = null;
		$this->localfile = null;
		$this->fname = null;
		$this->title = null;
		$this->expectedsum = null;
		$this->realsum = null;
		$this->expectedsize = null;
		$this->realsize = null;
		$this->uid = null;
		$this->errmsg = null;
		$this->jobpid = null;
		$this->start = null;
		$this->ts = null;
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

} // BaseCcTrans
