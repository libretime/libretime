<?php


/**
 * Base class that represents a row from the 'cc_access' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcAccess extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcAccessPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcAccessPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the gunid field.
	 * @var        string
	 */
	protected $gunid;

	/**
	 * The value for the token field.
	 * @var        string
	 */
	protected $token;

	/**
	 * The value for the chsum field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $chsum;

	/**
	 * The value for the ext field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $ext;

	/**
	 * The value for the type field.
	 * Note: this column has a database default value of: ''
	 * @var        string
	 */
	protected $type;

	/**
	 * The value for the parent field.
	 * @var        string
	 */
	protected $parent;

	/**
	 * The value for the owner field.
	 * @var        int
	 */
	protected $owner;

	/**
	 * The value for the ts field.
	 * @var        string
	 */
	protected $ts;

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
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->chsum = '';
		$this->ext = '';
		$this->type = '';
	}

	/**
	 * Initializes internal state of BaseCcAccess object.
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
	 * Get the [gunid] column value.
	 *
	 * @return     string
	 */
	public function getGunid()
	{
		return $this->gunid;
	}

	/**
	 * Get the [token] column value.
	 *
	 * @return     string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Get the [chsum] column value.
	 *
	 * @return     string
	 */
	public function getChsum()
	{
		return $this->chsum;
	}

	/**
	 * Get the [ext] column value.
	 *
	 * @return     string
	 */
	public function getExt()
	{
		return $this->ext;
	}

	/**
	 * Get the [type] column value.
	 *
	 * @return     string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [parent] column value.
	 *
	 * @return     string
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Get the [owner] column value.
	 *
	 * @return     int
	 */
	public function getOwner()
	{
		return $this->owner;
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
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcAccessPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [gunid] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setGunid($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->gunid !== $v) {
			$this->gunid = $v;
			$this->modifiedColumns[] = CcAccessPeer::GUNID;
		}

		return $this;
	} // setGunid()

	/**
	 * Set the value of [token] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setToken($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->token !== $v) {
			$this->token = $v;
			$this->modifiedColumns[] = CcAccessPeer::TOKEN;
		}

		return $this;
	} // setToken()

	/**
	 * Set the value of [chsum] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setChsum($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->chsum !== $v || $this->isNew()) {
			$this->chsum = $v;
			$this->modifiedColumns[] = CcAccessPeer::CHSUM;
		}

		return $this;
	} // setChsum()

	/**
	 * Set the value of [ext] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setExt($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->ext !== $v || $this->isNew()) {
			$this->ext = $v;
			$this->modifiedColumns[] = CcAccessPeer::EXT;
		}

		return $this;
	} // setExt()

	/**
	 * Set the value of [type] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type !== $v || $this->isNew()) {
			$this->type = $v;
			$this->modifiedColumns[] = CcAccessPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [parent] column.
	 *
	 * @param      string $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setParent($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->parent !== $v) {
			$this->parent = $v;
			$this->modifiedColumns[] = CcAccessPeer::PARENT;
		}

		return $this;
	} // setParent()

	/**
	 * Set the value of [owner] column.
	 *
	 * @param      int $v new value
	 * @return     CcAccess The current object (for fluent API support)
	 */
	public function setOwner($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->owner !== $v) {
			$this->owner = $v;
			$this->modifiedColumns[] = CcAccessPeer::OWNER;
		}

		if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
			$this->aCcSubjs = null;
		}

		return $this;
	} // setOwner()

	/**
	 * Sets the value of [ts] column to a normalized version of the date/time value specified.
	 *
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcAccess The current object (for fluent API support)
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
				$this->modifiedColumns[] = CcAccessPeer::TS;
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
			if ($this->chsum !== '') {
				return false;
			}

			if ($this->ext !== '') {
				return false;
			}

			if ($this->type !== '') {
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
			$this->gunid = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->token = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->chsum = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->ext = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->type = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->parent = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->owner = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->ts = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 9; // 9 = CcAccessPeer::NUM_COLUMNS - CcAccessPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcAccess object", $e);
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

		if ($this->aCcSubjs !== null && $this->owner !== $this->aCcSubjs->getDbId()) {
			$this->aCcSubjs = null;
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
			$con = Propel::getConnection(CcAccessPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcAccessPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcSubjs = null;
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
			$con = Propel::getConnection(CcAccessPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcAccessQuery::create()
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
			$con = Propel::getConnection(CcAccessPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcAccessPeer::addInstanceToPool($this);
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

			if ($this->aCcSubjs !== null) {
				if ($this->aCcSubjs->isModified() || $this->aCcSubjs->isNew()) {
					$affectedRows += $this->aCcSubjs->save($con);
				}
				$this->setCcSubjs($this->aCcSubjs);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcAccessPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcAccessPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcAccessPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcAccessPeer::doUpdate($this, $con);
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

			if ($this->aCcSubjs !== null) {
				if (!$this->aCcSubjs->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
				}
			}


			if (($retval = CcAccessPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CcAccessPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getGunid();
				break;
			case 2:
				return $this->getToken();
				break;
			case 3:
				return $this->getChsum();
				break;
			case 4:
				return $this->getExt();
				break;
			case 5:
				return $this->getType();
				break;
			case 6:
				return $this->getParent();
				break;
			case 7:
				return $this->getOwner();
				break;
			case 8:
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
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = CcAccessPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getGunid(),
			$keys[2] => $this->getToken(),
			$keys[3] => $this->getChsum(),
			$keys[4] => $this->getExt(),
			$keys[5] => $this->getType(),
			$keys[6] => $this->getParent(),
			$keys[7] => $this->getOwner(),
			$keys[8] => $this->getTs(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcSubjs) {
				$result['CcSubjs'] = $this->aCcSubjs->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = CcAccessPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setGunid($value);
				break;
			case 2:
				$this->setToken($value);
				break;
			case 3:
				$this->setChsum($value);
				break;
			case 4:
				$this->setExt($value);
				break;
			case 5:
				$this->setType($value);
				break;
			case 6:
				$this->setParent($value);
				break;
			case 7:
				$this->setOwner($value);
				break;
			case 8:
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
		$keys = CcAccessPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setGunid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setToken($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setChsum($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setExt($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setType($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setParent($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setOwner($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setTs($arr[$keys[8]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcAccessPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcAccessPeer::ID)) $criteria->add(CcAccessPeer::ID, $this->id);
		if ($this->isColumnModified(CcAccessPeer::GUNID)) $criteria->add(CcAccessPeer::GUNID, $this->gunid);
		if ($this->isColumnModified(CcAccessPeer::TOKEN)) $criteria->add(CcAccessPeer::TOKEN, $this->token);
		if ($this->isColumnModified(CcAccessPeer::CHSUM)) $criteria->add(CcAccessPeer::CHSUM, $this->chsum);
		if ($this->isColumnModified(CcAccessPeer::EXT)) $criteria->add(CcAccessPeer::EXT, $this->ext);
		if ($this->isColumnModified(CcAccessPeer::TYPE)) $criteria->add(CcAccessPeer::TYPE, $this->type);
		if ($this->isColumnModified(CcAccessPeer::PARENT)) $criteria->add(CcAccessPeer::PARENT, $this->parent);
		if ($this->isColumnModified(CcAccessPeer::OWNER)) $criteria->add(CcAccessPeer::OWNER, $this->owner);
		if ($this->isColumnModified(CcAccessPeer::TS)) $criteria->add(CcAccessPeer::TS, $this->ts);

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
		$criteria = new Criteria(CcAccessPeer::DATABASE_NAME);
		$criteria->add(CcAccessPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcAccess (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setGunid($this->gunid);
		$copyObj->setToken($this->token);
		$copyObj->setChsum($this->chsum);
		$copyObj->setExt($this->ext);
		$copyObj->setType($this->type);
		$copyObj->setParent($this->parent);
		$copyObj->setOwner($this->owner);
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
	 * @return     CcAccess Clone of current object.
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
	 * @return     CcAccessPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcAccessPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcSubjs object.
	 *
	 * @param      CcSubjs $v
	 * @return     CcAccess The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcSubjs(CcSubjs $v = null)
	{
		if ($v === null) {
			$this->setOwner(NULL);
		} else {
			$this->setOwner($v->getDbId());
		}

		$this->aCcSubjs = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcSubjs object, it will not be re-added.
		if ($v !== null) {
			$v->addCcAccess($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcSubjs object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcSubjs The associated CcSubjs object.
	 * @throws     PropelException
	 */
	public function getCcSubjs(PropelPDO $con = null)
	{
		if ($this->aCcSubjs === null && ($this->owner !== null)) {
			$this->aCcSubjs = CcSubjsQuery::create()->findPk($this->owner, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcSubjs->addCcAccesss($this);
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
		$this->gunid = null;
		$this->token = null;
		$this->chsum = null;
		$this->ext = null;
		$this->type = null;
		$this->parent = null;
		$this->owner = null;
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

		$this->aCcSubjs = null;
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

} // BaseCcAccess
