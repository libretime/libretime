<?php


/**
 * Base class that represents a row from the 'cc_playout_history_template_field' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryTemplateTag extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcPlayoutHistoryTemplateTagPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcPlayoutHistoryTemplateTagPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the template_id field.
	 * @var        int
	 */
	protected $template_id;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the type field.
	 * @var        string
	 */
	protected $type;

	/**
	 * The value for the is_file_md field.
	 * Note: this column has a database default value of: false
	 * @var        boolean
	 */
	protected $is_file_md;

	/**
	 * The value for the position field.
	 * @var        int
	 */
	protected $position;

	/**
	 * @var        CcPlayoutHistoryTemplate
	 */
	protected $aCcPlayoutHistoryTemplate;

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
		$this->is_file_md = false;
	}

	/**
	 * Initializes internal state of BaseCcPlayoutHistoryTemplateTag object.
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
	 * Get the [template_id] column value.
	 *
	 * @return     int
	 */
	public function getDbTemplateId()
	{
		return $this->template_id;
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
	 * Get the [type] column value.
	 *
	 * @return     string
	 */
	public function getDbType()
	{
		return $this->type;
	}

	/**
	 * Get the [is_file_md] column value.
	 *
	 * @return     boolean
	 */
	public function getDbIsFileMD()
	{
		return $this->is_file_md;
	}

	/**
	 * Get the [position] column value.
	 *
	 * @return     int
	 */
	public function getDbTagPosition()
	{
		return $this->position;
	}

	/**
	 * Set the value of [id] column.
	 *
	 * @param      int $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [template_id] column.
	 *
	 * @param      int $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbTemplateId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->template_id !== $v) {
			$this->template_id = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID;
		}

		if ($this->aCcPlayoutHistoryTemplate !== null && $this->aCcPlayoutHistoryTemplate->getDbId() !== $v) {
			$this->aCcPlayoutHistoryTemplate = null;
		}

		return $this;
	} // setDbTemplateId()

	/**
	 * Set the value of [name] column.
	 *
	 * @param      string $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::NAME;
		}

		return $this;
	} // setDbName()

	/**
	 * Set the value of [type] column.
	 *
	 * @param      string $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::TYPE;
		}

		return $this;
	} // setDbType()

	/**
	 * Set the value of [is_file_md] column.
	 *
	 * @param      boolean $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbIsFileMD($v)
	{
		if ($v !== null) {
			$v = (boolean) $v;
		}

		if ($this->is_file_md !== $v || $this->isNew()) {
			$this->is_file_md = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::IS_FILE_MD;
		}

		return $this;
	} // setDbIsFileMD()

	/**
	 * Set the value of [position] column.
	 *
	 * @param      int $v new value
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 */
	public function setDbTagPosition($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->position !== $v) {
			$this->position = $v;
			$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::POSITION;
		}

		return $this;
	} // setDbTagPosition()

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
			if ($this->is_file_md !== false) {
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
			$this->template_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->is_file_md = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
			$this->position = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 6; // 6 = CcPlayoutHistoryTemplateTagPeer::NUM_COLUMNS - CcPlayoutHistoryTemplateTagPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcPlayoutHistoryTemplateTag object", $e);
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

		if ($this->aCcPlayoutHistoryTemplate !== null && $this->template_id !== $this->aCcPlayoutHistoryTemplate->getDbId()) {
			$this->aCcPlayoutHistoryTemplate = null;
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
			$con = Propel::getConnection(CcPlayoutHistoryTemplateTagPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcPlayoutHistoryTemplateTagPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcPlayoutHistoryTemplate = null;
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
			$con = Propel::getConnection(CcPlayoutHistoryTemplateTagPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcPlayoutHistoryTemplateTagQuery::create()
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
			$con = Propel::getConnection(CcPlayoutHistoryTemplateTagPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcPlayoutHistoryTemplateTagPeer::addInstanceToPool($this);
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

			if ($this->aCcPlayoutHistoryTemplate !== null) {
				if ($this->aCcPlayoutHistoryTemplate->isModified() || $this->aCcPlayoutHistoryTemplate->isNew()) {
					$affectedRows += $this->aCcPlayoutHistoryTemplate->save($con);
				}
				$this->setCcPlayoutHistoryTemplate($this->aCcPlayoutHistoryTemplate);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcPlayoutHistoryTemplateTagPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcPlayoutHistoryTemplateTagPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcPlayoutHistoryTemplateTagPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcPlayoutHistoryTemplateTagPeer::doUpdate($this, $con);
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

			if ($this->aCcPlayoutHistoryTemplate !== null) {
				if (!$this->aCcPlayoutHistoryTemplate->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcPlayoutHistoryTemplate->getValidationFailures());
				}
			}


			if (($retval = CcPlayoutHistoryTemplateTagPeer::doValidate($this, $columns)) !== true) {
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
		$pos = CcPlayoutHistoryTemplateTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDbTemplateId();
				break;
			case 2:
				return $this->getDbName();
				break;
			case 3:
				return $this->getDbType();
				break;
			case 4:
				return $this->getDbIsFileMD();
				break;
			case 5:
				return $this->getDbTagPosition();
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
		$keys = CcPlayoutHistoryTemplateTagPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbTemplateId(),
			$keys[2] => $this->getDbName(),
			$keys[3] => $this->getDbType(),
			$keys[4] => $this->getDbIsFileMD(),
			$keys[5] => $this->getDbTagPosition(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcPlayoutHistoryTemplate) {
				$result['CcPlayoutHistoryTemplate'] = $this->aCcPlayoutHistoryTemplate->toArray($keyType, $includeLazyLoadColumns, true);
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
		$pos = CcPlayoutHistoryTemplateTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDbTemplateId($value);
				break;
			case 2:
				$this->setDbName($value);
				break;
			case 3:
				$this->setDbType($value);
				break;
			case 4:
				$this->setDbIsFileMD($value);
				break;
			case 5:
				$this->setDbTagPosition($value);
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
		$keys = CcPlayoutHistoryTemplateTagPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbTemplateId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbType($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbIsFileMD($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbTagPosition($arr[$keys[5]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcPlayoutHistoryTemplateTagPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::ID)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::ID, $this->id);
		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID, $this->template_id);
		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::NAME)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::NAME, $this->name);
		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::TYPE)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::TYPE, $this->type);
		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::IS_FILE_MD)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::IS_FILE_MD, $this->is_file_md);
		if ($this->isColumnModified(CcPlayoutHistoryTemplateTagPeer::POSITION)) $criteria->add(CcPlayoutHistoryTemplateTagPeer::POSITION, $this->position);

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
		$criteria = new Criteria(CcPlayoutHistoryTemplateTagPeer::DATABASE_NAME);
		$criteria->add(CcPlayoutHistoryTemplateTagPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcPlayoutHistoryTemplateTag (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbTemplateId($this->template_id);
		$copyObj->setDbName($this->name);
		$copyObj->setDbType($this->type);
		$copyObj->setDbIsFileMD($this->is_file_md);
		$copyObj->setDbTagPosition($this->position);

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
	 * @return     CcPlayoutHistoryTemplateTag Clone of current object.
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
	 * @return     CcPlayoutHistoryTemplateTagPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcPlayoutHistoryTemplateTagPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcPlayoutHistoryTemplate object.
	 *
	 * @param      CcPlayoutHistoryTemplate $v
	 * @return     CcPlayoutHistoryTemplateTag The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcPlayoutHistoryTemplate(CcPlayoutHistoryTemplate $v = null)
	{
		if ($v === null) {
			$this->setDbTemplateId(NULL);
		} else {
			$this->setDbTemplateId($v->getDbId());
		}

		$this->aCcPlayoutHistoryTemplate = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcPlayoutHistoryTemplate object, it will not be re-added.
		if ($v !== null) {
			$v->addCcPlayoutHistoryTemplateTag($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcPlayoutHistoryTemplate object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcPlayoutHistoryTemplate The associated CcPlayoutHistoryTemplate object.
	 * @throws     PropelException
	 */
	public function getCcPlayoutHistoryTemplate(PropelPDO $con = null)
	{
		if ($this->aCcPlayoutHistoryTemplate === null && ($this->template_id !== null)) {
			$this->aCcPlayoutHistoryTemplate = CcPlayoutHistoryTemplateQuery::create()->findPk($this->template_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcPlayoutHistoryTemplate->addCcPlayoutHistoryTemplateTags($this);
			 */
		}
		return $this->aCcPlayoutHistoryTemplate;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->template_id = null;
		$this->name = null;
		$this->type = null;
		$this->is_file_md = null;
		$this->position = null;
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

		$this->aCcPlayoutHistoryTemplate = null;
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

} // BaseCcPlayoutHistoryTemplateTag
