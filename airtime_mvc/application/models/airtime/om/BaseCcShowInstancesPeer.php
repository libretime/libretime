<?php


/**
 * Base static class for performing query and update operations on the 'cc_show_instances' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowInstancesPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_show_instances';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcShowInstances';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcShowInstances';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcShowInstancesTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 12;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_show_instances.ID';

	/** the column name for the STARTS field */
	const STARTS = 'cc_show_instances.STARTS';

	/** the column name for the ENDS field */
	const ENDS = 'cc_show_instances.ENDS';

	/** the column name for the SHOW_ID field */
	const SHOW_ID = 'cc_show_instances.SHOW_ID';

	/** the column name for the RECORD field */
	const RECORD = 'cc_show_instances.RECORD';

	/** the column name for the REBROADCAST field */
	const REBROADCAST = 'cc_show_instances.REBROADCAST';

	/** the column name for the INSTANCE_ID field */
	const INSTANCE_ID = 'cc_show_instances.INSTANCE_ID';

	/** the column name for the FILE_ID field */
	const FILE_ID = 'cc_show_instances.FILE_ID';

	/** the column name for the TIME_FILLED field */
	const TIME_FILLED = 'cc_show_instances.TIME_FILLED';

	/** the column name for the CREATED field */
	const CREATED = 'cc_show_instances.CREATED';

	/** the column name for the LAST_SCHEDULED field */
	const LAST_SCHEDULED = 'cc_show_instances.LAST_SCHEDULED';

	/** the column name for the MODIFIED_INSTANCE field */
	const MODIFIED_INSTANCE = 'cc_show_instances.MODIFIED_INSTANCE';

	/**
	 * An identiy map to hold any loaded instances of CcShowInstances objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcShowInstances[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DbId', 'DbStarts', 'DbEnds', 'DbShowId', 'DbRecord', 'DbRebroadcast', 'DbOriginalShow', 'DbRecordedFile', 'DbTimeFilled', 'DbCreated', 'DbLastScheduled', 'DbModifiedInstance', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbStarts', 'dbEnds', 'dbShowId', 'dbRecord', 'dbRebroadcast', 'dbOriginalShow', 'dbRecordedFile', 'dbTimeFilled', 'dbCreated', 'dbLastScheduled', 'dbModifiedInstance', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::STARTS, self::ENDS, self::SHOW_ID, self::RECORD, self::REBROADCAST, self::INSTANCE_ID, self::FILE_ID, self::TIME_FILLED, self::CREATED, self::LAST_SCHEDULED, self::MODIFIED_INSTANCE, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'STARTS', 'ENDS', 'SHOW_ID', 'RECORD', 'REBROADCAST', 'INSTANCE_ID', 'FILE_ID', 'TIME_FILLED', 'CREATED', 'LAST_SCHEDULED', 'MODIFIED_INSTANCE', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'starts', 'ends', 'show_id', 'record', 'rebroadcast', 'instance_id', 'file_id', 'time_filled', 'created', 'last_scheduled', 'modified_instance', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbStarts' => 1, 'DbEnds' => 2, 'DbShowId' => 3, 'DbRecord' => 4, 'DbRebroadcast' => 5, 'DbOriginalShow' => 6, 'DbRecordedFile' => 7, 'DbTimeFilled' => 8, 'DbCreated' => 9, 'DbLastScheduled' => 10, 'DbModifiedInstance' => 11, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbStarts' => 1, 'dbEnds' => 2, 'dbShowId' => 3, 'dbRecord' => 4, 'dbRebroadcast' => 5, 'dbOriginalShow' => 6, 'dbRecordedFile' => 7, 'dbTimeFilled' => 8, 'dbCreated' => 9, 'dbLastScheduled' => 10, 'dbModifiedInstance' => 11, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::STARTS => 1, self::ENDS => 2, self::SHOW_ID => 3, self::RECORD => 4, self::REBROADCAST => 5, self::INSTANCE_ID => 6, self::FILE_ID => 7, self::TIME_FILLED => 8, self::CREATED => 9, self::LAST_SCHEDULED => 10, self::MODIFIED_INSTANCE => 11, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'STARTS' => 1, 'ENDS' => 2, 'SHOW_ID' => 3, 'RECORD' => 4, 'REBROADCAST' => 5, 'INSTANCE_ID' => 6, 'FILE_ID' => 7, 'TIME_FILLED' => 8, 'CREATED' => 9, 'LAST_SCHEDULED' => 10, 'MODIFIED_INSTANCE' => 11, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'starts' => 1, 'ends' => 2, 'show_id' => 3, 'record' => 4, 'rebroadcast' => 5, 'instance_id' => 6, 'file_id' => 7, 'time_filled' => 8, 'created' => 9, 'last_scheduled' => 10, 'modified_instance' => 11, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
	);

	/**
	 * Translates a fieldname to another type
	 *
	 * @param      string $name field name
	 * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param      string $toType   One of the class type constants
	 * @return     string translated name of the field.
	 * @throws     PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	/**
	 * Returns an array of field names.
	 *
	 * @param      string $type The type of fieldnames to return:
	 *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     array A list of field names
	 */

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	/**
	 * Convenience method which changes table.column to alias.column.
	 *
	 * Using this method you can maintain SQL abstraction while using column aliases.
	 * <code>
	 *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
	 *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
	 * </code>
	 * @param      string $alias The alias for the current table.
	 * @param      string $column The column name for current table. (i.e. CcShowInstancesPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CcShowInstancesPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	/**
	 * Add all the columns needed to create a new object.
	 *
	 * Note: any columns that were marked with lazyLoad="true" in the
	 * XML schema will not be added to the select list and only loaded
	 * on demand.
	 *
	 * @param      Criteria $criteria object containing the columns to add.
	 * @param      string   $alias    optional table alias
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function addSelectColumns(Criteria $criteria, $alias = null)
	{
		if (null === $alias) {
			$criteria->addSelectColumn(CcShowInstancesPeer::ID);
			$criteria->addSelectColumn(CcShowInstancesPeer::STARTS);
			$criteria->addSelectColumn(CcShowInstancesPeer::ENDS);
			$criteria->addSelectColumn(CcShowInstancesPeer::SHOW_ID);
			$criteria->addSelectColumn(CcShowInstancesPeer::RECORD);
			$criteria->addSelectColumn(CcShowInstancesPeer::REBROADCAST);
			$criteria->addSelectColumn(CcShowInstancesPeer::INSTANCE_ID);
			$criteria->addSelectColumn(CcShowInstancesPeer::FILE_ID);
			$criteria->addSelectColumn(CcShowInstancesPeer::TIME_FILLED);
			$criteria->addSelectColumn(CcShowInstancesPeer::CREATED);
			$criteria->addSelectColumn(CcShowInstancesPeer::LAST_SCHEDULED);
			$criteria->addSelectColumn(CcShowInstancesPeer::MODIFIED_INSTANCE);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.STARTS');
			$criteria->addSelectColumn($alias . '.ENDS');
			$criteria->addSelectColumn($alias . '.SHOW_ID');
			$criteria->addSelectColumn($alias . '.RECORD');
			$criteria->addSelectColumn($alias . '.REBROADCAST');
			$criteria->addSelectColumn($alias . '.INSTANCE_ID');
			$criteria->addSelectColumn($alias . '.FILE_ID');
			$criteria->addSelectColumn($alias . '.TIME_FILLED');
			$criteria->addSelectColumn($alias . '.CREATED');
			$criteria->addSelectColumn($alias . '.LAST_SCHEDULED');
			$criteria->addSelectColumn($alias . '.MODIFIED_INSTANCE');
		}
	}

	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		// we may modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		// BasePeer returns a PDOStatement
		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}
	/**
	 * Method to select one object from the DB.
	 *
	 * @param      Criteria $criteria object used to create the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     CcShowInstances
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CcShowInstancesPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	/**
	 * Method to do selects.
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con
	 * @return     array Array of selected Objects
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		return CcShowInstancesPeer::populateObjects(CcShowInstancesPeer::doSelectStmt($criteria, $con));
	}
	/**
	 * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
	 *
	 * Use this method directly if you want to work with an executed statement durirectly (for example
	 * to perform your own object hydration).
	 *
	 * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
	 * @param      PropelPDO $con The connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     PDOStatement The executed PDOStatement object.
	 * @see        BasePeer::doSelect()
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CcShowInstancesPeer::addSelectColumns($criteria);
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		// BasePeer returns a PDOStatement
		return BasePeer::doSelect($criteria, $con);
	}
	/**
	 * Adds an object to the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doSelect*()
	 * methods in your stub classes -- you may need to explicitly add objects
	 * to the cache in order to ensure that the same objects are always returned by doSelect*()
	 * and retrieveByPK*() calls.
	 *
	 * @param      CcShowInstances $value A CcShowInstances object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcShowInstances $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getDbId();
			} // if key === null
			self::$instances[$key] = $obj;
		}
	}

	/**
	 * Removes an object from the instance pool.
	 *
	 * Propel keeps cached copies of objects in an instance pool when they are retrieved
	 * from the database.  In some cases -- especially when you override doDelete
	 * methods in your stub classes -- you may need to explicitly remove objects
	 * from the cache in order to prevent returning objects that no longer exist.
	 *
	 * @param      mixed $value A CcShowInstances object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CcShowInstances) {
				$key = (string) $value->getDbId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcShowInstances object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
				throw $e;
			}

			unset(self::$instances[$key]);
		}
	} // removeInstanceFromPool()

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
	 * @return     CcShowInstances Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
	 * @see        getPrimaryKeyHash()
	 */
	public static function getInstanceFromPool($key)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if (isset(self::$instances[$key])) {
				return self::$instances[$key];
			}
		}
		return null; // just to be explicit
	}
	
	/**
	 * Clear the instance pool.
	 *
	 * @return     void
	 */
	public static function clearInstancePool()
	{
		self::$instances = array();
	}
	
	/**
	 * Method to invalidate the instance pool of all tables related to cc_show_instances
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
		// Invalidate objects in CcShowInstancesPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcShowInstancesPeer::clearInstancePool();
		// Invalidate objects in CcSchedulePeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcSchedulePeer::clearInstancePool();
		// Invalidate objects in CcPlayoutHistoryPeer instance pool, 
		// since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
		CcPlayoutHistoryPeer::clearInstancePool();
	}

	/**
	 * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
	 *
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, a serialize()d version of the primary key will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     string A string version of PK or NULL if the components of primary key in result array are all null.
	 */
	public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
	{
		// If the PK cannot be derived from the row, return NULL.
		if ($row[$startcol] === null) {
			return null;
		}
		return (string) $row[$startcol];
	}

	/**
	 * Retrieves the primary key from the DB resultset row 
	 * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
	 * a multi-column primary key, an array of the primary key columns will be returned.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @return     mixed The primary key of the row
	 */
	public static function getPrimaryKeyFromRow($row, $startcol = 0)
	{
		return (int) $row[$startcol];
	}
	
	/**
	 * The returned array will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function populateObjects(PDOStatement $stmt)
	{
		$results = array();
	
		// set the class once to avoid overhead in the loop
		$cls = CcShowInstancesPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CcShowInstancesPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CcShowInstancesPeer::addInstanceToPool($obj, $key);
			} // if key exists
		}
		$stmt->closeCursor();
		return $results;
	}
	/**
	 * Populates an object of the default type or an object that inherit from the default.
	 *
	 * @param      array $row PropelPDO resultset row.
	 * @param      int $startcol The 0-based offset for reading from the resultset row.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 * @return     array (CcShowInstances object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcShowInstancesPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcShowInstancesPeer::NUM_COLUMNS;
		} else {
			$cls = CcShowInstancesPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcShowInstancesPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related CcShow table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcShow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcFiles table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcFiles(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of CcShowInstances objects pre-filled with their CcShow objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcShow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcShowPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcShowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcShowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcShowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcShowInstances) to $obj2 (CcShow)
				$obj2->addCcShowInstances($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowInstances objects pre-filled with their CcFiles objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcFiles(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);
		CcFilesPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcFilesPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcFilesPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcFilesPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcShowInstances) to $obj2 (CcFiles)
				$obj2->addCcShowInstances($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}

	/**
	 * Selects a collection of CcShowInstances objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined CcShow rows

			$key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CcShowPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcShowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcShowPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj2 (CcShow)
				$obj2->addCcShowInstances($obj1);
			} // if joined row not null

			// Add objects for joined CcFiles rows

			$key3 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = CcFilesPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = CcFilesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcFilesPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj3 (CcFiles)
				$obj3->addCcShowInstances($obj1);
			} // if joined row not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcShow table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcShow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcShowInstancesRelatedByDbOriginalShow table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcShowInstancesRelatedByDbOriginalShow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Returns the number of rows matching criteria, joining the related CcFiles table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcFiles(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowInstancesPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$stmt = BasePeer::doCount($criteria, $con);

		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$count = (int) $row[0];
		} else {
			$count = 0; // no rows returned; we infer that means 0 matches.
		}
		$stmt->closeCursor();
		return $count;
	}


	/**
	 * Selects a collection of CcShowInstances objects pre-filled with all related objects except CcShow.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcShow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcFiles rows

				$key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcFilesPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcFilesPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcFilesPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj2 (CcFiles)
				$obj2->addCcShowInstances($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowInstances objects pre-filled with all related objects except CcShowInstancesRelatedByDbOriginalShow.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcShowInstancesRelatedByDbOriginalShow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowInstancesPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcShow rows

				$key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcShowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcShowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcShowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj2 (CcShow)
				$obj2->addCcShowInstances($obj1);

			} // if joined row is not null

				// Add objects for joined CcFiles rows

				$key3 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcFilesPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcFilesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcFilesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj3 (CcFiles)
				$obj3->addCcShowInstances($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowInstances objects pre-filled with all related objects except CcFiles.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowInstances objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcFiles(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowInstancesPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowInstancesPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowInstancesPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowInstancesPeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

				// Add objects for joined CcShow rows

				$key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
				if ($key2 !== null) {
					$obj2 = CcShowPeer::getInstanceFromPool($key2);
					if (!$obj2) {
	
						$cls = CcShowPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcShowPeer::addInstanceToPool($obj2, $key2);
				} // if $obj2 already loaded

				// Add the $obj1 (CcShowInstances) to the collection in $obj2 (CcShow)
				$obj2->addCcShowInstances($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * Returns the TableMap related to this peer.
	 * This method is not needed for general use but a specific application could have a need.
	 * @return     TableMap
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	/**
	 * Add a TableMap instance to the database for this peer class.
	 */
	public static function buildTableMap()
	{
	  $dbMap = Propel::getDatabaseMap(BaseCcShowInstancesPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcShowInstancesPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcShowInstancesTableMap());
	  }
	}

	/**
	 * The class that the Peer will make instances of.
	 *
	 * If $withPrefix is true, the returned path
	 * uses a dot-path notation which is tranalted into a path
	 * relative to a location on the PHP include_path.
	 * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
	 *
	 * @param      boolean $withPrefix Whether or not to return the path with the class name
	 * @return     string path.to.ClassName
	 */
	public static function getOMClass($withPrefix = true)
	{
		return $withPrefix ? CcShowInstancesPeer::CLASS_DEFAULT : CcShowInstancesPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcShowInstances or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcShowInstances object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CcShowInstances object
		}

		if ($criteria->containsKey(CcShowInstancesPeer::ID) && $criteria->keyContainsValue(CcShowInstancesPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcShowInstancesPeer::ID.')');
		}


		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		try {
			// use transaction because $criteria could contain info
			// for more than one table (I guess, conceivably)
			$con->beginTransaction();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollBack();
			throw $e;
		}

		return $pk;
	}

	/**
	 * Method perform an UPDATE on the database, given a CcShowInstances or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcShowInstances object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CcShowInstancesPeer::ID);
			$value = $criteria->remove(CcShowInstancesPeer::ID);
			if ($value) {
				$selectCriteria->add(CcShowInstancesPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CcShowInstancesPeer::TABLE_NAME);
			}

		} else { // $values is CcShowInstances object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_show_instances table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CcShowInstancesPeer::TABLE_NAME, $con, CcShowInstancesPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CcShowInstancesPeer::clearInstancePool();
			CcShowInstancesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcShowInstances or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcShowInstances object or primary key or array of primary keys
	 *              which is used to create the DELETE statement
	 * @param      PropelPDO $con the connection to use
	 * @return     int 	The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
	 *				if supported by native driver or if emulated using Propel.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	 public static function doDelete($values, PropelPDO $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CcShowInstancesPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CcShowInstances) { // it's a model object
			// invalidate the cache for this single object
			CcShowInstancesPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcShowInstancesPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcShowInstancesPeer::removeInstanceFromPool($singleval);
			}
		}

		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; // initialize var to track total num of affected rows

		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			CcShowInstancesPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CcShowInstances object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CcShowInstances $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcShowInstances $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcShowInstancesPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcShowInstancesPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach ($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		return BasePeer::doValidate(CcShowInstancesPeer::DATABASE_NAME, CcShowInstancesPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcShowInstances
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CcShowInstancesPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);
		$criteria->add(CcShowInstancesPeer::ID, $pk);

		$v = CcShowInstancesPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}

	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CcShowInstancesPeer::DATABASE_NAME);
			$criteria->add(CcShowInstancesPeer::ID, $pks, Criteria::IN);
			$objs = CcShowInstancesPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCcShowInstancesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcShowInstancesPeer::buildTableMap();

