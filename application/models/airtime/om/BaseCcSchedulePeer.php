<?php


/**
 * Base static class for performing query and update operations on the 'cc_schedule' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcSchedulePeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_schedule';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcSchedule';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcSchedule';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcScheduleTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 14;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_schedule.ID';

	/** the column name for the PLAYLIST_ID field */
	const PLAYLIST_ID = 'cc_schedule.PLAYLIST_ID';

	/** the column name for the STARTS field */
	const STARTS = 'cc_schedule.STARTS';

	/** the column name for the ENDS field */
	const ENDS = 'cc_schedule.ENDS';

	/** the column name for the GROUP_ID field */
	const GROUP_ID = 'cc_schedule.GROUP_ID';

	/** the column name for the FILE_ID field */
	const FILE_ID = 'cc_schedule.FILE_ID';

	/** the column name for the CLIP_LENGTH field */
	const CLIP_LENGTH = 'cc_schedule.CLIP_LENGTH';

	/** the column name for the FADE_IN field */
	const FADE_IN = 'cc_schedule.FADE_IN';

	/** the column name for the FADE_OUT field */
	const FADE_OUT = 'cc_schedule.FADE_OUT';

	/** the column name for the CUE_IN field */
	const CUE_IN = 'cc_schedule.CUE_IN';

	/** the column name for the CUE_OUT field */
	const CUE_OUT = 'cc_schedule.CUE_OUT';

	/** the column name for the SCHEDULE_GROUP_PLAYED field */
	const SCHEDULE_GROUP_PLAYED = 'cc_schedule.SCHEDULE_GROUP_PLAYED';

	/** the column name for the MEDIA_ITEM_PLAYED field */
	const MEDIA_ITEM_PLAYED = 'cc_schedule.MEDIA_ITEM_PLAYED';

	/** the column name for the INSTANCE_ID field */
	const INSTANCE_ID = 'cc_schedule.INSTANCE_ID';

	/**
	 * An identiy map to hold any loaded instances of CcSchedule objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcSchedule[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DbId', 'DbPlaylistId', 'DbStarts', 'DbEnds', 'DbGroupId', 'DbFileId', 'DbClipLength', 'DbFadeIn', 'DbFadeOut', 'DbCueIn', 'DbCueOut', 'DbScheduleGroupPlayed', 'DbMediaItemPlayed', 'DbInstanceId', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbPlaylistId', 'dbStarts', 'dbEnds', 'dbGroupId', 'dbFileId', 'dbClipLength', 'dbFadeIn', 'dbFadeOut', 'dbCueIn', 'dbCueOut', 'dbScheduleGroupPlayed', 'dbMediaItemPlayed', 'dbInstanceId', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::PLAYLIST_ID, self::STARTS, self::ENDS, self::GROUP_ID, self::FILE_ID, self::CLIP_LENGTH, self::FADE_IN, self::FADE_OUT, self::CUE_IN, self::CUE_OUT, self::SCHEDULE_GROUP_PLAYED, self::MEDIA_ITEM_PLAYED, self::INSTANCE_ID, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'PLAYLIST_ID', 'STARTS', 'ENDS', 'GROUP_ID', 'FILE_ID', 'CLIP_LENGTH', 'FADE_IN', 'FADE_OUT', 'CUE_IN', 'CUE_OUT', 'SCHEDULE_GROUP_PLAYED', 'MEDIA_ITEM_PLAYED', 'INSTANCE_ID', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'playlist_id', 'starts', 'ends', 'group_id', 'file_id', 'clip_length', 'fade_in', 'fade_out', 'cue_in', 'cue_out', 'schedule_group_played', 'media_item_played', 'instance_id', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbPlaylistId' => 1, 'DbStarts' => 2, 'DbEnds' => 3, 'DbGroupId' => 4, 'DbFileId' => 5, 'DbClipLength' => 6, 'DbFadeIn' => 7, 'DbFadeOut' => 8, 'DbCueIn' => 9, 'DbCueOut' => 10, 'DbScheduleGroupPlayed' => 11, 'DbMediaItemPlayed' => 12, 'DbInstanceId' => 13, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbPlaylistId' => 1, 'dbStarts' => 2, 'dbEnds' => 3, 'dbGroupId' => 4, 'dbFileId' => 5, 'dbClipLength' => 6, 'dbFadeIn' => 7, 'dbFadeOut' => 8, 'dbCueIn' => 9, 'dbCueOut' => 10, 'dbScheduleGroupPlayed' => 11, 'dbMediaItemPlayed' => 12, 'dbInstanceId' => 13, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::PLAYLIST_ID => 1, self::STARTS => 2, self::ENDS => 3, self::GROUP_ID => 4, self::FILE_ID => 5, self::CLIP_LENGTH => 6, self::FADE_IN => 7, self::FADE_OUT => 8, self::CUE_IN => 9, self::CUE_OUT => 10, self::SCHEDULE_GROUP_PLAYED => 11, self::MEDIA_ITEM_PLAYED => 12, self::INSTANCE_ID => 13, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'PLAYLIST_ID' => 1, 'STARTS' => 2, 'ENDS' => 3, 'GROUP_ID' => 4, 'FILE_ID' => 5, 'CLIP_LENGTH' => 6, 'FADE_IN' => 7, 'FADE_OUT' => 8, 'CUE_IN' => 9, 'CUE_OUT' => 10, 'SCHEDULE_GROUP_PLAYED' => 11, 'MEDIA_ITEM_PLAYED' => 12, 'INSTANCE_ID' => 13, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'playlist_id' => 1, 'starts' => 2, 'ends' => 3, 'group_id' => 4, 'file_id' => 5, 'clip_length' => 6, 'fade_in' => 7, 'fade_out' => 8, 'cue_in' => 9, 'cue_out' => 10, 'schedule_group_played' => 11, 'media_item_played' => 12, 'instance_id' => 13, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
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
	 * @param      string $column The column name for current table. (i.e. CcSchedulePeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CcSchedulePeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(CcSchedulePeer::ID);
			$criteria->addSelectColumn(CcSchedulePeer::PLAYLIST_ID);
			$criteria->addSelectColumn(CcSchedulePeer::STARTS);
			$criteria->addSelectColumn(CcSchedulePeer::ENDS);
			$criteria->addSelectColumn(CcSchedulePeer::GROUP_ID);
			$criteria->addSelectColumn(CcSchedulePeer::FILE_ID);
			$criteria->addSelectColumn(CcSchedulePeer::CLIP_LENGTH);
			$criteria->addSelectColumn(CcSchedulePeer::FADE_IN);
			$criteria->addSelectColumn(CcSchedulePeer::FADE_OUT);
			$criteria->addSelectColumn(CcSchedulePeer::CUE_IN);
			$criteria->addSelectColumn(CcSchedulePeer::CUE_OUT);
			$criteria->addSelectColumn(CcSchedulePeer::SCHEDULE_GROUP_PLAYED);
			$criteria->addSelectColumn(CcSchedulePeer::MEDIA_ITEM_PLAYED);
			$criteria->addSelectColumn(CcSchedulePeer::INSTANCE_ID);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.PLAYLIST_ID');
			$criteria->addSelectColumn($alias . '.STARTS');
			$criteria->addSelectColumn($alias . '.ENDS');
			$criteria->addSelectColumn($alias . '.GROUP_ID');
			$criteria->addSelectColumn($alias . '.FILE_ID');
			$criteria->addSelectColumn($alias . '.CLIP_LENGTH');
			$criteria->addSelectColumn($alias . '.FADE_IN');
			$criteria->addSelectColumn($alias . '.FADE_OUT');
			$criteria->addSelectColumn($alias . '.CUE_IN');
			$criteria->addSelectColumn($alias . '.CUE_OUT');
			$criteria->addSelectColumn($alias . '.SCHEDULE_GROUP_PLAYED');
			$criteria->addSelectColumn($alias . '.MEDIA_ITEM_PLAYED');
			$criteria->addSelectColumn($alias . '.INSTANCE_ID');
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
		$criteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcSchedulePeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     CcSchedule
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CcSchedulePeer::doSelect($critcopy, $con);
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
		return CcSchedulePeer::populateObjects(CcSchedulePeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CcSchedulePeer::addSelectColumns($criteria);
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
	 * @param      CcSchedule $value A CcSchedule object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcSchedule $obj, $key = null)
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
	 * @param      mixed $value A CcSchedule object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CcSchedule) {
				$key = (string) $value->getDbId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcSchedule object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     CcSchedule Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to cc_schedule
	 * by a foreign key with ON DELETE CASCADE
	 */
	public static function clearRelatedInstancePool()
	{
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
		return (string) $row[$startcol];
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
		$cls = CcSchedulePeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CcSchedulePeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CcSchedulePeer::addInstanceToPool($obj, $key);
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
	 * @return     array (CcSchedule object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcSchedulePeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcSchedulePeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcSchedulePeer::NUM_COLUMNS;
		} else {
			$cls = CcSchedulePeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcSchedulePeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
	}

	/**
	 * Returns the number of rows matching criteria, joining the related CcShowInstances table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcShowInstances(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcSchedulePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

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
	 * Selects a collection of CcSchedule objects pre-filled with their CcShowInstances objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcSchedule objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcShowInstances(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcSchedulePeer::addSelectColumns($criteria);
		$startcol = (CcSchedulePeer::NUM_COLUMNS - CcSchedulePeer::NUM_LAZY_LOAD_COLUMNS);
		CcShowInstancesPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcSchedulePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcSchedulePeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcShowInstancesPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcShowInstancesPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcSchedule) to $obj2 (CcShowInstances)
				$obj2->addCcSchedule($obj1);

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
		$criteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcSchedulePeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

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
	 * Selects a collection of CcSchedule objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcSchedule objects.
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

		CcSchedulePeer::addSelectColumns($criteria);
		$startcol2 = (CcSchedulePeer::NUM_COLUMNS - CcSchedulePeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcSchedulePeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcSchedulePeer::addInstanceToPool($obj1, $key1);
			} // if obj1 already loaded

			// Add objects for joined CcShowInstances rows

			$key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
			if ($key2 !== null) {
				$obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcShowInstancesPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol2);
					CcShowInstancesPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 loaded

				// Add the $obj1 (CcSchedule) to the collection in $obj2 (CcShowInstances)
				$obj2->addCcSchedule($obj1);
			} // if joined row not null

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
	  $dbMap = Propel::getDatabaseMap(BaseCcSchedulePeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcSchedulePeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcScheduleTableMap());
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
		return $withPrefix ? CcSchedulePeer::CLASS_DEFAULT : CcSchedulePeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcSchedule or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcSchedule object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CcSchedule object
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
	 * Method perform an UPDATE on the database, given a CcSchedule or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcSchedule object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CcSchedulePeer::ID);
			$value = $criteria->remove(CcSchedulePeer::ID);
			if ($value) {
				$selectCriteria->add(CcSchedulePeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);
			}

		} else { // $values is CcSchedule object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_schedule table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CcSchedulePeer::TABLE_NAME, $con, CcSchedulePeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CcSchedulePeer::clearInstancePool();
			CcSchedulePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcSchedule or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcSchedule object or primary key or array of primary keys
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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CcSchedulePeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CcSchedule) { // it's a model object
			// invalidate the cache for this single object
			CcSchedulePeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcSchedulePeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcSchedulePeer::removeInstanceFromPool($singleval);
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
			CcSchedulePeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CcSchedule object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CcSchedule $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcSchedule $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcSchedulePeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcSchedulePeer::TABLE_NAME);

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

		return BasePeer::doValidate(CcSchedulePeer::DATABASE_NAME, CcSchedulePeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcSchedule
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CcSchedulePeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);
		$criteria->add(CcSchedulePeer::ID, $pk);

		$v = CcSchedulePeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);
			$criteria->add(CcSchedulePeer::ID, $pks, Criteria::IN);
			$objs = CcSchedulePeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCcSchedulePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcSchedulePeer::buildTableMap();

