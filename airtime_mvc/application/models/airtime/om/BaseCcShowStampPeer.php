<?php


/**
 * Base static class for performing query and update operations on the 'cc_show_stamp' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowStampPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_show_stamp';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcShowStamp';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcShowStamp';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcShowStampTableMap';
	
	/** The total number of columns. */
	const NUM_COLUMNS = 13;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_show_stamp.ID';

	/** the column name for the SHOW_ID field */
	const SHOW_ID = 'cc_show_stamp.SHOW_ID';

	/** the column name for the INSTANCE_ID field */
	const INSTANCE_ID = 'cc_show_stamp.INSTANCE_ID';

	/** the column name for the FILE_ID field */
	const FILE_ID = 'cc_show_stamp.FILE_ID';

	/** the column name for the STREAM_ID field */
	const STREAM_ID = 'cc_show_stamp.STREAM_ID';

	/** the column name for the BLOCK_ID field */
	const BLOCK_ID = 'cc_show_stamp.BLOCK_ID';

	/** the column name for the PLAYLIST_ID field */
	const PLAYLIST_ID = 'cc_show_stamp.PLAYLIST_ID';

	/** the column name for the POSITION field */
	const POSITION = 'cc_show_stamp.POSITION';

	/** the column name for the CLIP_LENGTH field */
	const CLIP_LENGTH = 'cc_show_stamp.CLIP_LENGTH';

	/** the column name for the CUE_IN field */
	const CUE_IN = 'cc_show_stamp.CUE_IN';

	/** the column name for the CUE_OUT field */
	const CUE_OUT = 'cc_show_stamp.CUE_OUT';

	/** the column name for the FADE_IN field */
	const FADE_IN = 'cc_show_stamp.FADE_IN';

	/** the column name for the FADE_OUT field */
	const FADE_OUT = 'cc_show_stamp.FADE_OUT';

	/**
	 * An identiy map to hold any loaded instances of CcShowStamp objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcShowStamp[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('DbId', 'DbShowId', 'DbInstanceId', 'DbFileId', 'DbStreamId', 'DbBlockId', 'DbPlaylistId', 'DbPosition', 'DbClipLength', 'DbCueIn', 'DbCueOut', 'DbFadeIn', 'DbFadeOut', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbShowId', 'dbInstanceId', 'dbFileId', 'dbStreamId', 'dbBlockId', 'dbPlaylistId', 'dbPosition', 'dbClipLength', 'dbCueIn', 'dbCueOut', 'dbFadeIn', 'dbFadeOut', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::SHOW_ID, self::INSTANCE_ID, self::FILE_ID, self::STREAM_ID, self::BLOCK_ID, self::PLAYLIST_ID, self::POSITION, self::CLIP_LENGTH, self::CUE_IN, self::CUE_OUT, self::FADE_IN, self::FADE_OUT, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'SHOW_ID', 'INSTANCE_ID', 'FILE_ID', 'STREAM_ID', 'BLOCK_ID', 'PLAYLIST_ID', 'POSITION', 'CLIP_LENGTH', 'CUE_IN', 'CUE_OUT', 'FADE_IN', 'FADE_OUT', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'show_id', 'instance_id', 'file_id', 'stream_id', 'block_id', 'playlist_id', 'position', 'clip_length', 'cue_in', 'cue_out', 'fade_in', 'fade_out', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbShowId' => 1, 'DbInstanceId' => 2, 'DbFileId' => 3, 'DbStreamId' => 4, 'DbBlockId' => 5, 'DbPlaylistId' => 6, 'DbPosition' => 7, 'DbClipLength' => 8, 'DbCueIn' => 9, 'DbCueOut' => 10, 'DbFadeIn' => 11, 'DbFadeOut' => 12, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbShowId' => 1, 'dbInstanceId' => 2, 'dbFileId' => 3, 'dbStreamId' => 4, 'dbBlockId' => 5, 'dbPlaylistId' => 6, 'dbPosition' => 7, 'dbClipLength' => 8, 'dbCueIn' => 9, 'dbCueOut' => 10, 'dbFadeIn' => 11, 'dbFadeOut' => 12, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::SHOW_ID => 1, self::INSTANCE_ID => 2, self::FILE_ID => 3, self::STREAM_ID => 4, self::BLOCK_ID => 5, self::PLAYLIST_ID => 6, self::POSITION => 7, self::CLIP_LENGTH => 8, self::CUE_IN => 9, self::CUE_OUT => 10, self::FADE_IN => 11, self::FADE_OUT => 12, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'SHOW_ID' => 1, 'INSTANCE_ID' => 2, 'FILE_ID' => 3, 'STREAM_ID' => 4, 'BLOCK_ID' => 5, 'PLAYLIST_ID' => 6, 'POSITION' => 7, 'CLIP_LENGTH' => 8, 'CUE_IN' => 9, 'CUE_OUT' => 10, 'FADE_IN' => 11, 'FADE_OUT' => 12, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'show_id' => 1, 'instance_id' => 2, 'file_id' => 3, 'stream_id' => 4, 'block_id' => 5, 'playlist_id' => 6, 'position' => 7, 'clip_length' => 8, 'cue_in' => 9, 'cue_out' => 10, 'fade_in' => 11, 'fade_out' => 12, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
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
	 * @param      string $column The column name for current table. (i.e. CcShowStampPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CcShowStampPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(CcShowStampPeer::ID);
			$criteria->addSelectColumn(CcShowStampPeer::SHOW_ID);
			$criteria->addSelectColumn(CcShowStampPeer::INSTANCE_ID);
			$criteria->addSelectColumn(CcShowStampPeer::FILE_ID);
			$criteria->addSelectColumn(CcShowStampPeer::STREAM_ID);
			$criteria->addSelectColumn(CcShowStampPeer::BLOCK_ID);
			$criteria->addSelectColumn(CcShowStampPeer::PLAYLIST_ID);
			$criteria->addSelectColumn(CcShowStampPeer::POSITION);
			$criteria->addSelectColumn(CcShowStampPeer::CLIP_LENGTH);
			$criteria->addSelectColumn(CcShowStampPeer::CUE_IN);
			$criteria->addSelectColumn(CcShowStampPeer::CUE_OUT);
			$criteria->addSelectColumn(CcShowStampPeer::FADE_IN);
			$criteria->addSelectColumn(CcShowStampPeer::FADE_OUT);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.SHOW_ID');
			$criteria->addSelectColumn($alias . '.INSTANCE_ID');
			$criteria->addSelectColumn($alias . '.FILE_ID');
			$criteria->addSelectColumn($alias . '.STREAM_ID');
			$criteria->addSelectColumn($alias . '.BLOCK_ID');
			$criteria->addSelectColumn($alias . '.PLAYLIST_ID');
			$criteria->addSelectColumn($alias . '.POSITION');
			$criteria->addSelectColumn($alias . '.CLIP_LENGTH');
			$criteria->addSelectColumn($alias . '.CUE_IN');
			$criteria->addSelectColumn($alias . '.CUE_OUT');
			$criteria->addSelectColumn($alias . '.FADE_IN');
			$criteria->addSelectColumn($alias . '.FADE_OUT');
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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     CcShowStamp
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CcShowStampPeer::doSelect($critcopy, $con);
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
		return CcShowStampPeer::populateObjects(CcShowStampPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CcShowStampPeer::addSelectColumns($criteria);
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
	 * @param      CcShowStamp $value A CcShowStamp object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcShowStamp $obj, $key = null)
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
	 * @param      mixed $value A CcShowStamp object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CcShowStamp) {
				$key = (string) $value->getDbId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcShowStamp object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     CcShowStamp Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to cc_show_stamp
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
		$cls = CcShowStampPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CcShowStampPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CcShowStampPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (CcShowStamp object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcShowStampPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcShowStampPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcShowStampPeer::NUM_COLUMNS;
		} else {
			$cls = CcShowStampPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcShowStampPeer::addInstanceToPool($obj, $key);
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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcWebstream table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcWebstream(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcBlock table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcBlock(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcPlaylist table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinCcPlaylist(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Selects a collection of CcShowStamp objects pre-filled with their CcShow objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcShowPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with their CcShowInstances objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcShowInstancesPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to $obj2 (CcShowInstances)
				$obj2->addCcShowStamp($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with their CcFiles objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcFilesPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to $obj2 (CcFiles)
				$obj2->addCcShowStamp($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with their CcWebstream objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcWebstream(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcWebstreamPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcWebstreamPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcWebstreamPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcWebstreamPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcShowStamp) to $obj2 (CcWebstream)
				$obj2->addCcShowStamp($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with their CcBlock objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcBlock(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcBlockPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcBlockPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcBlockPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcBlockPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcShowStamp) to $obj2 (CcBlock)
				$obj2->addCcShowStamp($obj1);

			} // if joined row was not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with their CcPlaylist objects.
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinCcPlaylist(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);
		CcPlaylistPeer::addSelectColumns($criteria);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {

				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
			} // if $obj1 already loaded

			$key2 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol);
			if ($key2 !== null) {
				$obj2 = CcPlaylistPeer::getInstanceFromPool($key2);
				if (!$obj2) {

					$cls = CcPlaylistPeer::getOMClass(false);

					$obj2 = new $cls();
					$obj2->hydrate($row, $startcol);
					CcPlaylistPeer::addInstanceToPool($obj2, $key2);
				} // if obj2 already loaded

				// Add the $obj1 (CcShowStamp) to $obj2 (CcPlaylist)
				$obj2->addCcShowStamp($obj1);

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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol8 = $startcol7 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);
			} // if joined row not null

			// Add objects for joined CcShowInstances rows

			$key3 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
			if ($key3 !== null) {
				$obj3 = CcShowInstancesPeer::getInstanceFromPool($key3);
				if (!$obj3) {

					$cls = CcShowInstancesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcShowInstancesPeer::addInstanceToPool($obj3, $key3);
				} // if obj3 loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcShowInstances)
				$obj3->addCcShowStamp($obj1);
			} // if joined row not null

			// Add objects for joined CcFiles rows

			$key4 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
			if ($key4 !== null) {
				$obj4 = CcFilesPeer::getInstanceFromPool($key4);
				if (!$obj4) {

					$cls = CcFilesPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcFilesPeer::addInstanceToPool($obj4, $key4);
				} // if obj4 loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcFiles)
				$obj4->addCcShowStamp($obj1);
			} // if joined row not null

			// Add objects for joined CcWebstream rows

			$key5 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol5);
			if ($key5 !== null) {
				$obj5 = CcWebstreamPeer::getInstanceFromPool($key5);
				if (!$obj5) {

					$cls = CcWebstreamPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcWebstreamPeer::addInstanceToPool($obj5, $key5);
				} // if obj5 loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcWebstream)
				$obj5->addCcShowStamp($obj1);
			} // if joined row not null

			// Add objects for joined CcBlock rows

			$key6 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol6);
			if ($key6 !== null) {
				$obj6 = CcBlockPeer::getInstanceFromPool($key6);
				if (!$obj6) {

					$cls = CcBlockPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcBlockPeer::addInstanceToPool($obj6, $key6);
				} // if obj6 loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcBlock)
				$obj6->addCcShowStamp($obj1);
			} // if joined row not null

			// Add objects for joined CcPlaylist rows

			$key7 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol7);
			if ($key7 !== null) {
				$obj7 = CcPlaylistPeer::getInstanceFromPool($key7);
				if (!$obj7) {

					$cls = CcPlaylistPeer::getOMClass(false);

					$obj7 = new $cls();
					$obj7->hydrate($row, $startcol7);
					CcPlaylistPeer::addInstanceToPool($obj7, $key7);
				} // if obj7 loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj7 (CcPlaylist)
				$obj7->addCcShowStamp($obj1);
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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcShowInstances table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcShowInstances(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcWebstream table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcWebstream(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcBlock table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcBlock(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
	 * Returns the number of rows matching criteria, joining the related CcPlaylist table
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExceptCcPlaylist(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		// we're going to modify criteria, so copy it first
		$criteria = clone $criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		$criteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
		
		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcShowStampPeer::addSelectColumns($criteria);
		}
		
		$criteria->clearOrderByColumns(); // ORDER BY should not affect count
		
		// Set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	
		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

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
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcShow.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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
				} // if $obj2 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShowInstances)
				$obj2->addCcShowStamp($obj1);

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

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcFiles)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcWebstream rows

				$key4 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcWebstreamPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcWebstreamPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcWebstreamPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcWebstream)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcBlock rows

				$key5 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcBlockPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcBlockPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcBlockPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcBlock)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcPlaylist rows

				$key6 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcPlaylistPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcPlaylistPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcPlaylistPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcPlaylist)
				$obj6->addCcShowStamp($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcShowInstances.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcShowInstances(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

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

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcFiles)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcWebstream rows

				$key4 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcWebstreamPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcWebstreamPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcWebstreamPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcWebstream)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcBlock rows

				$key5 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcBlockPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcBlockPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcBlockPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcBlock)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcPlaylist rows

				$key6 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcPlaylistPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcPlaylistPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcPlaylistPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcPlaylist)
				$obj6->addCcShowStamp($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcFiles.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
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

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcShowInstances rows

				$key3 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcShowInstancesPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcShowInstancesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcShowInstancesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcShowInstances)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcWebstream rows

				$key4 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcWebstreamPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcWebstreamPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcWebstreamPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcWebstream)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcBlock rows

				$key5 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcBlockPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcBlockPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcBlockPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcBlock)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcPlaylist rows

				$key6 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcPlaylistPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcPlaylistPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcPlaylistPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcPlaylist)
				$obj6->addCcShowStamp($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcWebstream.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcWebstream(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcShowInstances rows

				$key3 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcShowInstancesPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcShowInstancesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcShowInstancesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcShowInstances)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcFiles rows

				$key4 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcFilesPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcFilesPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcFilesPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcFiles)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcBlock rows

				$key5 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcBlockPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcBlockPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcBlockPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcBlock)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcPlaylist rows

				$key6 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcPlaylistPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcPlaylistPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcPlaylistPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcPlaylist)
				$obj6->addCcShowStamp($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcBlock.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcBlock(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcPlaylistPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcPlaylistPeer::NUM_COLUMNS - CcPlaylistPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcShowInstances rows

				$key3 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcShowInstancesPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcShowInstancesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcShowInstancesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcShowInstances)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcFiles rows

				$key4 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcFilesPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcFilesPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcFilesPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcFiles)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcWebstream rows

				$key5 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcWebstreamPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcWebstreamPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcWebstreamPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcWebstream)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcPlaylist rows

				$key6 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcPlaylistPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcPlaylistPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcPlaylistPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcPlaylist)
				$obj6->addCcShowStamp($obj1);

			} // if joined row is not null

			$results[] = $obj1;
		}
		$stmt->closeCursor();
		return $results;
	}


	/**
	 * Selects a collection of CcShowStamp objects pre-filled with all related objects except CcPlaylist.
	 *
	 * @param      Criteria  $criteria
	 * @param      PropelPDO $con
	 * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
	 * @return     array Array of CcShowStamp objects.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectJoinAllExceptCcPlaylist(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$criteria = clone $criteria;

		// Set the correct dbName if it has not been overridden
		// $criteria->getDbName() will return the same object if not set to another value
		// so == check is okay and faster
		if ($criteria->getDbName() == Propel::getDefaultDB()) {
			$criteria->setDbName(self::DATABASE_NAME);
		}

		CcShowStampPeer::addSelectColumns($criteria);
		$startcol2 = (CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowPeer::addSelectColumns($criteria);
		$startcol3 = $startcol2 + (CcShowPeer::NUM_COLUMNS - CcShowPeer::NUM_LAZY_LOAD_COLUMNS);

		CcShowInstancesPeer::addSelectColumns($criteria);
		$startcol4 = $startcol3 + (CcShowInstancesPeer::NUM_COLUMNS - CcShowInstancesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcFilesPeer::addSelectColumns($criteria);
		$startcol5 = $startcol4 + (CcFilesPeer::NUM_COLUMNS - CcFilesPeer::NUM_LAZY_LOAD_COLUMNS);

		CcWebstreamPeer::addSelectColumns($criteria);
		$startcol6 = $startcol5 + (CcWebstreamPeer::NUM_COLUMNS - CcWebstreamPeer::NUM_LAZY_LOAD_COLUMNS);

		CcBlockPeer::addSelectColumns($criteria);
		$startcol7 = $startcol6 + (CcBlockPeer::NUM_COLUMNS - CcBlockPeer::NUM_LAZY_LOAD_COLUMNS);

		$criteria->addJoin(CcShowStampPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

		$criteria->addJoin(CcShowStampPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);


		$stmt = BasePeer::doSelect($criteria, $con);
		$results = array();

		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key1 = CcShowStampPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj1 = CcShowStampPeer::getInstanceFromPool($key1))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj1->hydrate($row, 0, true); // rehydrate
			} else {
				$cls = CcShowStampPeer::getOMClass(false);

				$obj1 = new $cls();
				$obj1->hydrate($row);
				CcShowStampPeer::addInstanceToPool($obj1, $key1);
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

				// Add the $obj1 (CcShowStamp) to the collection in $obj2 (CcShow)
				$obj2->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcShowInstances rows

				$key3 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
				if ($key3 !== null) {
					$obj3 = CcShowInstancesPeer::getInstanceFromPool($key3);
					if (!$obj3) {
	
						$cls = CcShowInstancesPeer::getOMClass(false);

					$obj3 = new $cls();
					$obj3->hydrate($row, $startcol3);
					CcShowInstancesPeer::addInstanceToPool($obj3, $key3);
				} // if $obj3 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj3 (CcShowInstances)
				$obj3->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcFiles rows

				$key4 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol4);
				if ($key4 !== null) {
					$obj4 = CcFilesPeer::getInstanceFromPool($key4);
					if (!$obj4) {
	
						$cls = CcFilesPeer::getOMClass(false);

					$obj4 = new $cls();
					$obj4->hydrate($row, $startcol4);
					CcFilesPeer::addInstanceToPool($obj4, $key4);
				} // if $obj4 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj4 (CcFiles)
				$obj4->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcWebstream rows

				$key5 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol5);
				if ($key5 !== null) {
					$obj5 = CcWebstreamPeer::getInstanceFromPool($key5);
					if (!$obj5) {
	
						$cls = CcWebstreamPeer::getOMClass(false);

					$obj5 = new $cls();
					$obj5->hydrate($row, $startcol5);
					CcWebstreamPeer::addInstanceToPool($obj5, $key5);
				} // if $obj5 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj5 (CcWebstream)
				$obj5->addCcShowStamp($obj1);

			} // if joined row is not null

				// Add objects for joined CcBlock rows

				$key6 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol6);
				if ($key6 !== null) {
					$obj6 = CcBlockPeer::getInstanceFromPool($key6);
					if (!$obj6) {
	
						$cls = CcBlockPeer::getOMClass(false);

					$obj6 = new $cls();
					$obj6->hydrate($row, $startcol6);
					CcBlockPeer::addInstanceToPool($obj6, $key6);
				} // if $obj6 already loaded

				// Add the $obj1 (CcShowStamp) to the collection in $obj6 (CcBlock)
				$obj6->addCcShowStamp($obj1);

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
	  $dbMap = Propel::getDatabaseMap(BaseCcShowStampPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcShowStampPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcShowStampTableMap());
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
		return $withPrefix ? CcShowStampPeer::CLASS_DEFAULT : CcShowStampPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcShowStamp or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcShowStamp object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CcShowStamp object
		}

		if ($criteria->containsKey(CcShowStampPeer::ID) && $criteria->keyContainsValue(CcShowStampPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcShowStampPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a CcShowStamp or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcShowStamp object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CcShowStampPeer::ID);
			$value = $criteria->remove(CcShowStampPeer::ID);
			if ($value) {
				$selectCriteria->add(CcShowStampPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CcShowStampPeer::TABLE_NAME);
			}

		} else { // $values is CcShowStamp object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_show_stamp table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CcShowStampPeer::TABLE_NAME, $con, CcShowStampPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CcShowStampPeer::clearInstancePool();
			CcShowStampPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcShowStamp or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcShowStamp object or primary key or array of primary keys
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
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CcShowStampPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CcShowStamp) { // it's a model object
			// invalidate the cache for this single object
			CcShowStampPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcShowStampPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcShowStampPeer::removeInstanceFromPool($singleval);
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
			CcShowStampPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CcShowStamp object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CcShowStamp $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcShowStamp $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcShowStampPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcShowStampPeer::TABLE_NAME);

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

		return BasePeer::doValidate(CcShowStampPeer::DATABASE_NAME, CcShowStampPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcShowStamp
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CcShowStampPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CcShowStampPeer::DATABASE_NAME);
		$criteria->add(CcShowStampPeer::ID, $pk);

		$v = CcShowStampPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CcShowStampPeer::DATABASE_NAME);
			$criteria->add(CcShowStampPeer::ID, $pks, Criteria::IN);
			$objs = CcShowStampPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCcShowStampPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcShowStampPeer::buildTableMap();

