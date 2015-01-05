<?php


/**
 * Base class that represents a row from the 'cc_playlist' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlaylist extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcPlaylistPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcPlaylistPeer
	 */
	protected static $peer;

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
	 * The value for the mtime field.
	 * @var        string
	 */
	protected $mtime;

	/**
	 * The value for the utime field.
	 * @var        string
	 */
	protected $utime;

	/**
	 * The value for the creator_id field.
	 * @var        int
	 */
	protected $creator_id;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the length field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $length;

	/**
	 * @var        CcSubjs
	 */
	protected $aCcSubjs;

	/**
	 * @var        array CcPlaylistcontents[] Collection to store aggregation of CcPlaylistcontents objects.
	 */
	protected $collCcPlaylistcontentss;

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
		$this->name = '';
		$this->length = '00:00:00';
	}

	/**
	 * Initializes internal state of BaseCcPlaylist object.
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
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getDbName()
	{
		return $this->name;
	}

	/**
	 * Get the [optionally formatted] temporal [mtime] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbMtime($format = 'Y-m-d H:i:s')
	{
		if ($this->mtime === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->mtime);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->mtime, true), $x);
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
	 * Get the [optionally formatted] temporal [utime] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDbUtime($format = 'Y-m-d H:i:s')
	{
		if ($this->utime === null) {
			return null;
		}



		try {
			$dt = new DateTime($this->utime);
		} catch (Exception $x) {
			throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->utime, true), $x);
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
	 * Get the [creator_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbCreatorId()
	{
		return $this->creator_id;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDbDescription()
	{
		return $this->description;
	}

	/**
	 * Get the [length] column value.
	 * 
	 * @return     string
	 */
	public function getDbLength()
	{
		return $this->length;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcPlaylistPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v || $this->isNew()) {
			$this->name = $v;
			$this->modifiedColumns[] = CcPlaylistPeer::NAME;
		}

		return $this;
	} // setDbName()

	/**
	 * Sets the value of [mtime] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbMtime($v)
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

		if ( $this->mtime !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->mtime !== null && $tmpDt = new DateTime($this->mtime)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->mtime = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcPlaylistPeer::MTIME;
			}
		} // if either are not null

		return $this;
	} // setDbMtime()

	/**
	 * Sets the value of [utime] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbUtime($v)
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

		if ( $this->utime !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->utime !== null && $tmpDt = new DateTime($this->utime)) ? $tmpDt->format('Y-m-d\\TH:i:sO') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d\\TH:i:sO') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->utime = ($dt ? $dt->format('Y-m-d\\TH:i:sO') : null);
				$this->modifiedColumns[] = CcPlaylistPeer::UTIME;
			}
		} // if either are not null

		return $this;
	} // setDbUtime()

	/**
	 * Set the value of [creator_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbCreatorId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->creator_id !== $v) {
			$this->creator_id = $v;
			$this->modifiedColumns[] = CcPlaylistPeer::CREATOR_ID;
		}

		if ($this->aCcSubjs !== null && $this->aCcSubjs->getDbId() !== $v) {
			$this->aCcSubjs = null;
		}

		return $this;
	} // setDbCreatorId()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbDescription($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = CcPlaylistPeer::DESCRIPTION;
		}

		return $this;
	} // setDbDescription()

	/**
	 * Set the value of [length] column.
	 * 
	 * @param      string $v new value
	 * @return     CcPlaylist The current object (for fluent API support)
	 */
	public function setDbLength($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->length !== $v || $this->isNew()) {
			$this->length = $v;
			$this->modifiedColumns[] = CcPlaylistPeer::LENGTH;
		}

		return $this;
	} // setDbLength()

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
			if ($this->name !== '') {
				return false;
			}

			if ($this->length !== '00:00:00') {
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
			$this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->mtime = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->utime = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->creator_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->description = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->length = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 7; // 7 = CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcPlaylist object", $e);
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

		if ($this->aCcSubjs !== null && $this->creator_id !== $this->aCcSubjs->getDbId()) {
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
			$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcPlaylistPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcSubjs = null;
			$this->collCcPlaylistcontentss = null;

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
			$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcPlaylistQuery::create()
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
			$con = Propel::getConnection(CcPlaylistPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcPlaylistPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = CcPlaylistPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcPlaylistPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcPlaylistPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcPlaylistPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCcPlaylistcontentss !== null) {
				foreach ($this->collCcPlaylistcontentss as $referrerFK) {
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

			if ($this->aCcSubjs !== null) {
				if (!$this->aCcSubjs->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcSubjs->getValidationFailures());
				}
			}


			if (($retval = CcPlaylistPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCcPlaylistcontentss !== null) {
					foreach ($this->collCcPlaylistcontentss as $referrerFK) {
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
		$pos = CcPlaylistPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDbName();
				break;
			case 2:
				return $this->getDbMtime();
				break;
			case 3:
				return $this->getDbUtime();
				break;
			case 4:
				return $this->getDbCreatorId();
				break;
			case 5:
				return $this->getDbDescription();
				break;
			case 6:
				return $this->getDbLength();
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
		$keys = CcPlaylistPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbName(),
			$keys[2] => $this->getDbMtime(),
			$keys[3] => $this->getDbUtime(),
			$keys[4] => $this->getDbCreatorId(),
			$keys[5] => $this->getDbDescription(),
			$keys[6] => $this->getDbLength(),
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
		$pos = CcPlaylistPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDbName($value);
				break;
			case 2:
				$this->setDbMtime($value);
				break;
			case 3:
				$this->setDbUtime($value);
				break;
			case 4:
				$this->setDbCreatorId($value);
				break;
			case 5:
				$this->setDbDescription($value);
				break;
			case 6:
				$this->setDbLength($value);
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
		$keys = CcPlaylistPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbMtime($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbUtime($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbCreatorId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbDescription($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDbLength($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcPlaylistPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcPlaylistPeer::ID)) $criteria->add(CcPlaylistPeer::ID, $this->id);
		if ($this->isColumnModified(CcPlaylistPeer::NAME)) $criteria->add(CcPlaylistPeer::NAME, $this->name);
		if ($this->isColumnModified(CcPlaylistPeer::MTIME)) $criteria->add(CcPlaylistPeer::MTIME, $this->mtime);
		if ($this->isColumnModified(CcPlaylistPeer::UTIME)) $criteria->add(CcPlaylistPeer::UTIME, $this->utime);
		if ($this->isColumnModified(CcPlaylistPeer::CREATOR_ID)) $criteria->add(CcPlaylistPeer::CREATOR_ID, $this->creator_id);
		if ($this->isColumnModified(CcPlaylistPeer::DESCRIPTION)) $criteria->add(CcPlaylistPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(CcPlaylistPeer::LENGTH)) $criteria->add(CcPlaylistPeer::LENGTH, $this->length);

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
		$criteria = new Criteria(CcPlaylistPeer::DATABASE_NAME);
		$criteria->add(CcPlaylistPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcPlaylist (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbName($this->name);
		$copyObj->setDbMtime($this->mtime);
		$copyObj->setDbUtime($this->utime);
		$copyObj->setDbCreatorId($this->creator_id);
		$copyObj->setDbDescription($this->description);
		$copyObj->setDbLength($this->length);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCcPlaylistcontentss() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcPlaylistcontents($relObj->copy($deepCopy));
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
	 * @return     CcPlaylist Clone of current object.
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
	 * @return     CcPlaylistPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcPlaylistPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcSubjs object.
	 *
	 * @param      CcSubjs $v
	 * @return     CcPlaylist The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcSubjs(CcSubjs $v = null)
	{
		if ($v === null) {
			$this->setDbCreatorId(NULL);
		} else {
			$this->setDbCreatorId($v->getDbId());
		}

		$this->aCcSubjs = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcSubjs object, it will not be re-added.
		if ($v !== null) {
			$v->addCcPlaylist($this);
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
		if ($this->aCcSubjs === null && ($this->creator_id !== null)) {
			$this->aCcSubjs = CcSubjsQuery::create()->findPk($this->creator_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcSubjs->addCcPlaylists($this);
			 */
		}
		return $this->aCcSubjs;
	}

	/**
	 * Clears out the collCcPlaylistcontentss collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcPlaylistcontentss()
	 */
	public function clearCcPlaylistcontentss()
	{
		$this->collCcPlaylistcontentss = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcPlaylistcontentss collection.
	 *
	 * By default this just sets the collCcPlaylistcontentss collection to an empty array (like clearcollCcPlaylistcontentss());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcPlaylistcontentss()
	{
		$this->collCcPlaylistcontentss = new PropelObjectCollection();
		$this->collCcPlaylistcontentss->setModel('CcPlaylistcontents');
	}

	/**
	 * Gets an array of CcPlaylistcontents objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcPlaylist is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcPlaylistcontents[] List of CcPlaylistcontents objects
	 * @throws     PropelException
	 */
	public function getCcPlaylistcontentss($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcPlaylistcontentss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
				// return empty collection
				$this->initCcPlaylistcontentss();
			} else {
				$collCcPlaylistcontentss = CcPlaylistcontentsQuery::create(null, $criteria)
					->filterByCcPlaylist($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcPlaylistcontentss;
				}
				$this->collCcPlaylistcontentss = $collCcPlaylistcontentss;
			}
		}
		return $this->collCcPlaylistcontentss;
	}

	/**
	 * Returns the number of related CcPlaylistcontents objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcPlaylistcontents objects.
	 * @throws     PropelException
	 */
	public function countCcPlaylistcontentss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcPlaylistcontentss || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlaylistcontentss) {
				return 0;
			} else {
				$query = CcPlaylistcontentsQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcPlaylist($this)
					->count($con);
			}
		} else {
			return count($this->collCcPlaylistcontentss);
		}
	}

	/**
	 * Method called to associate a CcPlaylistcontents object to this object
	 * through the CcPlaylistcontents foreign key attribute.
	 *
	 * @param      CcPlaylistcontents $l CcPlaylistcontents
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcPlaylistcontents(CcPlaylistcontents $l)
	{
		if ($this->collCcPlaylistcontentss === null) {
			$this->initCcPlaylistcontentss();
		}
		if (!$this->collCcPlaylistcontentss->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcPlaylistcontentss[]= $l;
			$l->setCcPlaylist($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcPlaylist is new, it will return
	 * an empty collection; or if this CcPlaylist has previously
	 * been saved, it will retrieve related CcPlaylistcontentss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcPlaylist.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcPlaylistcontents[] List of CcPlaylistcontents objects
	 */
	public function getCcPlaylistcontentssJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcPlaylistcontentsQuery::create(null, $criteria);
		$query->joinWith('CcFiles', $join_behavior);

		return $this->getCcPlaylistcontentss($query, $con);
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcPlaylist is new, it will return
	 * an empty collection; or if this CcPlaylist has previously
	 * been saved, it will retrieve related CcPlaylistcontentss from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcPlaylist.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcPlaylistcontents[] List of CcPlaylistcontents objects
	 */
	public function getCcPlaylistcontentssJoinCcBlock($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcPlaylistcontentsQuery::create(null, $criteria);
		$query->joinWith('CcBlock', $join_behavior);

		return $this->getCcPlaylistcontentss($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->name = null;
		$this->mtime = null;
		$this->utime = null;
		$this->creator_id = null;
		$this->description = null;
		$this->length = null;
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
			if ($this->collCcPlaylistcontentss) {
				foreach ((array) $this->collCcPlaylistcontentss as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCcPlaylistcontentss = null;
		$this->aCcSubjs = null;
	}

	// aggregate_column behavior
	
	/**
	 * Computes the value of the aggregate column length 
	 *
	 * @param PropelPDO $con A connection object
	 *
	 * @return mixed The scalar result from the aggregate query
	 */
	public function computeDbLength(PropelPDO $con)
	{
		$stmt = $con->prepare('SELECT SUM(cliplength) FROM "cc_playlistcontents" WHERE cc_playlistcontents.PLAYLIST_ID = :p1');
	  $stmt->bindValue(':p1', $this->getDbId());
		$stmt->execute();
		return $stmt->fetchColumn();
	}
	
	/**
	 * Updates the aggregate column length 
	 *
	 * @param PropelPDO $con A connection object
	 */
	public function updateDbLength(PropelPDO $con)
	{
		$this->setDbLength($this->computeDbLength($con));
		$this->save($con);
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

} // BaseCcPlaylist
