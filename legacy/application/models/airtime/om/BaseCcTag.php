<?php


/**
 * Base class that represents a row from the 'cc_tag' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTag extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcTagPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcTagPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the tag_name field.
	 * @var        string
	 */
	protected $tag_name;

	/**
	 * The value for the tag_type field.
	 * Note: this column has a database default value of: 'boolean'
	 * @var        string
	 */
	protected $tag_type;

	/**
	 * @var        array CcFileTag[] Collection to store aggregation of CcFileTag objects.
	 */
	protected $collCcFileTags;

	/**
	 * @var        array CcPlayoutHistoryMetaData[] Collection to store aggregation of CcPlayoutHistoryMetaData objects.
	 */
	protected $collCcPlayoutHistoryMetaDatas;

	/**
	 * @var        array CcPlayoutHistoryTemplateTag[] Collection to store aggregation of CcPlayoutHistoryTemplateTag objects.
	 */
	protected $collCcPlayoutHistoryTemplateTags;

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
		$this->tag_type = 'boolean';
	}

	/**
	 * Initializes internal state of BaseCcTag object.
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
	 * Get the [tag_name] column value.
	 *
	 * @return     string
	 */
	public function getDbTagName()
	{
		return $this->tag_name;
	}

	/**
	 * Get the [tag_type] column value.
	 *
	 * @return     string
	 */
	public function getDbTagType()
	{
		return $this->tag_type;
	}

	/**
	 * Set the value of [id] column.
	 *
	 * @param      int $v new value
	 * @return     CcTag The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcTagPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [tag_name] column.
	 *
	 * @param      string $v new value
	 * @return     CcTag The current object (for fluent API support)
	 */
	public function setDbTagName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tag_name !== $v) {
			$this->tag_name = $v;
			$this->modifiedColumns[] = CcTagPeer::TAG_NAME;
		}

		return $this;
	} // setDbTagName()

	/**
	 * Set the value of [tag_type] column.
	 *
	 * @param      string $v new value
	 * @return     CcTag The current object (for fluent API support)
	 */
	public function setDbTagType($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tag_type !== $v || $this->isNew()) {
			$this->tag_type = $v;
			$this->modifiedColumns[] = CcTagPeer::TAG_TYPE;
		}

		return $this;
	} // setDbTagType()

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
			if ($this->tag_type !== 'boolean') {
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
			$this->tag_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->tag_type = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 3; // 3 = CcTagPeer::NUM_COLUMNS - CcTagPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcTag object", $e);
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
			$con = Propel::getConnection(CcTagPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcTagPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collCcFileTags = null;

			$this->collCcPlayoutHistoryMetaDatas = null;

			$this->collCcPlayoutHistoryTemplateTags = null;

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
			$con = Propel::getConnection(CcTagPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcTagQuery::create()
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
			$con = Propel::getConnection(CcTagPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				CcTagPeer::addInstanceToPool($this);
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
				$this->modifiedColumns[] = CcTagPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcTagPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcTagPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows = 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows = CcTagPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collCcFileTags !== null) {
				foreach ($this->collCcFileTags as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCcPlayoutHistoryMetaDatas !== null) {
				foreach ($this->collCcPlayoutHistoryMetaDatas as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collCcPlayoutHistoryTemplateTags !== null) {
				foreach ($this->collCcPlayoutHistoryTemplateTags as $referrerFK) {
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


			if (($retval = CcTagPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collCcFileTags !== null) {
					foreach ($this->collCcFileTags as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCcPlayoutHistoryMetaDatas !== null) {
					foreach ($this->collCcPlayoutHistoryMetaDatas as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collCcPlayoutHistoryTemplateTags !== null) {
					foreach ($this->collCcPlayoutHistoryTemplateTags as $referrerFK) {
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
		$pos = CcTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getDbTagName();
				break;
			case 2:
				return $this->getDbTagType();
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
		$keys = CcTagPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbTagName(),
			$keys[2] => $this->getDbTagType(),
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
		$pos = CcTagPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setDbTagName($value);
				break;
			case 2:
				$this->setDbTagType($value);
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
		$keys = CcTagPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbTagName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbTagType($arr[$keys[2]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcTagPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcTagPeer::ID)) $criteria->add(CcTagPeer::ID, $this->id);
		if ($this->isColumnModified(CcTagPeer::TAG_NAME)) $criteria->add(CcTagPeer::TAG_NAME, $this->tag_name);
		if ($this->isColumnModified(CcTagPeer::TAG_TYPE)) $criteria->add(CcTagPeer::TAG_TYPE, $this->tag_type);

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
		$criteria = new Criteria(CcTagPeer::DATABASE_NAME);
		$criteria->add(CcTagPeer::ID, $this->id);

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
	 * @param      object $copyObj An object of CcTag (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbTagName($this->tag_name);
		$copyObj->setDbTagType($this->tag_type);

		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getCcFileTags() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcFileTag($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCcPlayoutHistoryMetaDatas() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcPlayoutHistoryMetaData($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getCcPlayoutHistoryTemplateTags() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addCcPlayoutHistoryTemplateTag($relObj->copy($deepCopy));
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
	 * @return     CcTag Clone of current object.
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
	 * @return     CcTagPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcTagPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collCcFileTags collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcFileTags()
	 */
	public function clearCcFileTags()
	{
		$this->collCcFileTags = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcFileTags collection.
	 *
	 * By default this just sets the collCcFileTags collection to an empty array (like clearcollCcFileTags());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcFileTags()
	{
		$this->collCcFileTags = new PropelObjectCollection();
		$this->collCcFileTags->setModel('CcFileTag');
	}

	/**
	 * Gets an array of CcFileTag objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcTag is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcFileTag[] List of CcFileTag objects
	 * @throws     PropelException
	 */
	public function getCcFileTags($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcFileTags || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcFileTags) {
				// return empty collection
				$this->initCcFileTags();
			} else {
				$collCcFileTags = CcFileTagQuery::create(null, $criteria)
					->filterByCcTag($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcFileTags;
				}
				$this->collCcFileTags = $collCcFileTags;
			}
		}
		return $this->collCcFileTags;
	}

	/**
	 * Returns the number of related CcFileTag objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcFileTag objects.
	 * @throws     PropelException
	 */
	public function countCcFileTags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcFileTags || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcFileTags) {
				return 0;
			} else {
				$query = CcFileTagQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcTag($this)
					->count($con);
			}
		} else {
			return count($this->collCcFileTags);
		}
	}

	/**
	 * Method called to associate a CcFileTag object to this object
	 * through the CcFileTag foreign key attribute.
	 *
	 * @param      CcFileTag $l CcFileTag
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcFileTag(CcFileTag $l)
	{
		if ($this->collCcFileTags === null) {
			$this->initCcFileTags();
		}
		if (!$this->collCcFileTags->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcFileTags[]= $l;
			$l->setCcTag($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcTag is new, it will return
	 * an empty collection; or if this CcTag has previously
	 * been saved, it will retrieve related CcFileTags from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcTag.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcFileTag[] List of CcFileTag objects
	 */
	public function getCcFileTagsJoinCcFiles($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcFileTagQuery::create(null, $criteria);
		$query->joinWith('CcFiles', $join_behavior);

		return $this->getCcFileTags($query, $con);
	}

	/**
	 * Clears out the collCcPlayoutHistoryMetaDatas collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcPlayoutHistoryMetaDatas()
	 */
	public function clearCcPlayoutHistoryMetaDatas()
	{
		$this->collCcPlayoutHistoryMetaDatas = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcPlayoutHistoryMetaDatas collection.
	 *
	 * By default this just sets the collCcPlayoutHistoryMetaDatas collection to an empty array (like clearcollCcPlayoutHistoryMetaDatas());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcPlayoutHistoryMetaDatas()
	{
		$this->collCcPlayoutHistoryMetaDatas = new PropelObjectCollection();
		$this->collCcPlayoutHistoryMetaDatas->setModel('CcPlayoutHistoryMetaData');
	}

	/**
	 * Gets an array of CcPlayoutHistoryMetaData objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcTag is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcPlayoutHistoryMetaData[] List of CcPlayoutHistoryMetaData objects
	 * @throws     PropelException
	 */
	public function getCcPlayoutHistoryMetaDatas($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistoryMetaDatas || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistoryMetaDatas) {
				// return empty collection
				$this->initCcPlayoutHistoryMetaDatas();
			} else {
				$collCcPlayoutHistoryMetaDatas = CcPlayoutHistoryMetaDataQuery::create(null, $criteria)
					->filterByCcTag($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcPlayoutHistoryMetaDatas;
				}
				$this->collCcPlayoutHistoryMetaDatas = $collCcPlayoutHistoryMetaDatas;
			}
		}
		return $this->collCcPlayoutHistoryMetaDatas;
	}

	/**
	 * Returns the number of related CcPlayoutHistoryMetaData objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcPlayoutHistoryMetaData objects.
	 * @throws     PropelException
	 */
	public function countCcPlayoutHistoryMetaDatas(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistoryMetaDatas || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistoryMetaDatas) {
				return 0;
			} else {
				$query = CcPlayoutHistoryMetaDataQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcTag($this)
					->count($con);
			}
		} else {
			return count($this->collCcPlayoutHistoryMetaDatas);
		}
	}

	/**
	 * Method called to associate a CcPlayoutHistoryMetaData object to this object
	 * through the CcPlayoutHistoryMetaData foreign key attribute.
	 *
	 * @param      CcPlayoutHistoryMetaData $l CcPlayoutHistoryMetaData
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcPlayoutHistoryMetaData(CcPlayoutHistoryMetaData $l)
	{
		if ($this->collCcPlayoutHistoryMetaDatas === null) {
			$this->initCcPlayoutHistoryMetaDatas();
		}
		if (!$this->collCcPlayoutHistoryMetaDatas->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcPlayoutHistoryMetaDatas[]= $l;
			$l->setCcTag($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcTag is new, it will return
	 * an empty collection; or if this CcTag has previously
	 * been saved, it will retrieve related CcPlayoutHistoryMetaDatas from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcTag.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcPlayoutHistoryMetaData[] List of CcPlayoutHistoryMetaData objects
	 */
	public function getCcPlayoutHistoryMetaDatasJoinCcPlayoutHistory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcPlayoutHistoryMetaDataQuery::create(null, $criteria);
		$query->joinWith('CcPlayoutHistory', $join_behavior);

		return $this->getCcPlayoutHistoryMetaDatas($query, $con);
	}

	/**
	 * Clears out the collCcPlayoutHistoryTemplateTags collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addCcPlayoutHistoryTemplateTags()
	 */
	public function clearCcPlayoutHistoryTemplateTags()
	{
		$this->collCcPlayoutHistoryTemplateTags = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collCcPlayoutHistoryTemplateTags collection.
	 *
	 * By default this just sets the collCcPlayoutHistoryTemplateTags collection to an empty array (like clearcollCcPlayoutHistoryTemplateTags());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initCcPlayoutHistoryTemplateTags()
	{
		$this->collCcPlayoutHistoryTemplateTags = new PropelObjectCollection();
		$this->collCcPlayoutHistoryTemplateTags->setModel('CcPlayoutHistoryTemplateTag');
	}

	/**
	 * Gets an array of CcPlayoutHistoryTemplateTag objects which contain a foreign key that references this object.
	 *
	 * If the $criteria is not null, it is used to always fetch the results from the database.
	 * Otherwise the results are fetched from the database the first time, then cached.
	 * Next time the same method is called without $criteria, the cached collection is returned.
	 * If this CcTag is new, it will return
	 * an empty collection or the current collection; the criteria is ignored on a new object.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @return     PropelCollection|array CcPlayoutHistoryTemplateTag[] List of CcPlayoutHistoryTemplateTag objects
	 * @throws     PropelException
	 */
	public function getCcPlayoutHistoryTemplateTags($criteria = null, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistoryTemplateTags || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistoryTemplateTags) {
				// return empty collection
				$this->initCcPlayoutHistoryTemplateTags();
			} else {
				$collCcPlayoutHistoryTemplateTags = CcPlayoutHistoryTemplateTagQuery::create(null, $criteria)
					->filterByCcTag($this)
					->find($con);
				if (null !== $criteria) {
					return $collCcPlayoutHistoryTemplateTags;
				}
				$this->collCcPlayoutHistoryTemplateTags = $collCcPlayoutHistoryTemplateTags;
			}
		}
		return $this->collCcPlayoutHistoryTemplateTags;
	}

	/**
	 * Returns the number of related CcPlayoutHistoryTemplateTag objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related CcPlayoutHistoryTemplateTag objects.
	 * @throws     PropelException
	 */
	public function countCcPlayoutHistoryTemplateTags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if(null === $this->collCcPlayoutHistoryTemplateTags || null !== $criteria) {
			if ($this->isNew() && null === $this->collCcPlayoutHistoryTemplateTags) {
				return 0;
			} else {
				$query = CcPlayoutHistoryTemplateTagQuery::create(null, $criteria);
				if($distinct) {
					$query->distinct();
				}
				return $query
					->filterByCcTag($this)
					->count($con);
			}
		} else {
			return count($this->collCcPlayoutHistoryTemplateTags);
		}
	}

	/**
	 * Method called to associate a CcPlayoutHistoryTemplateTag object to this object
	 * through the CcPlayoutHistoryTemplateTag foreign key attribute.
	 *
	 * @param      CcPlayoutHistoryTemplateTag $l CcPlayoutHistoryTemplateTag
	 * @return     void
	 * @throws     PropelException
	 */
	public function addCcPlayoutHistoryTemplateTag(CcPlayoutHistoryTemplateTag $l)
	{
		if ($this->collCcPlayoutHistoryTemplateTags === null) {
			$this->initCcPlayoutHistoryTemplateTags();
		}
		if (!$this->collCcPlayoutHistoryTemplateTags->contains($l)) { // only add it if the **same** object is not already associated
			$this->collCcPlayoutHistoryTemplateTags[]= $l;
			$l->setCcTag($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this CcTag is new, it will return
	 * an empty collection; or if this CcTag has previously
	 * been saved, it will retrieve related CcPlayoutHistoryTemplateTags from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in CcTag.
	 *
	 * @param      Criteria $criteria optional Criteria object to narrow the query
	 * @param      PropelPDO $con optional connection object
	 * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
	 * @return     PropelCollection|array CcPlayoutHistoryTemplateTag[] List of CcPlayoutHistoryTemplateTag objects
	 */
	public function getCcPlayoutHistoryTemplateTagsJoinCcPlayoutHistoryTemplate($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$query = CcPlayoutHistoryTemplateTagQuery::create(null, $criteria);
		$query->joinWith('CcPlayoutHistoryTemplate', $join_behavior);

		return $this->getCcPlayoutHistoryTemplateTags($query, $con);
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->tag_name = null;
		$this->tag_type = null;
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
			if ($this->collCcFileTags) {
				foreach ((array) $this->collCcFileTags as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCcPlayoutHistoryMetaDatas) {
				foreach ((array) $this->collCcPlayoutHistoryMetaDatas as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collCcPlayoutHistoryTemplateTags) {
				foreach ((array) $this->collCcPlayoutHistoryTemplateTags as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collCcFileTags = null;
		$this->collCcPlayoutHistoryMetaDatas = null;
		$this->collCcPlayoutHistoryTemplateTags = null;
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

} // BaseCcTag
