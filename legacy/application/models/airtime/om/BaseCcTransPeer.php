<?php


/**
 * Base static class for performing query and update operations on the 'cc_trans' table.
 *
 *
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTransPeer {

	/** the default database name for this class */
	const DATABASE_NAME = 'airtime';

	/** the table name for this class */
	const TABLE_NAME = 'cc_trans';

	/** the related Propel class for this table */
	const OM_CLASS = 'CcTrans';

	/** A class that can be returned by this peer. */
	const CLASS_DEFAULT = 'airtime.CcTrans';

	/** the related TableMap class for this table */
	const TM_CLASS = 'CcTransTableMap';

	/** The total number of columns. */
	const NUM_COLUMNS = 24;

	/** The number of lazy-loaded columns. */
	const NUM_LAZY_LOAD_COLUMNS = 0;

	/** the column name for the ID field */
	const ID = 'cc_trans.ID';

	/** the column name for the TRTOK field */
	const TRTOK = 'cc_trans.TRTOK';

	/** the column name for the DIRECTION field */
	const DIRECTION = 'cc_trans.DIRECTION';

	/** the column name for the STATE field */
	const STATE = 'cc_trans.STATE';

	/** the column name for the TRTYPE field */
	const TRTYPE = 'cc_trans.TRTYPE';

	/** the column name for the LOCK field */
	const LOCK = 'cc_trans.LOCK';

	/** the column name for the TARGET field */
	const TARGET = 'cc_trans.TARGET';

	/** the column name for the RTRTOK field */
	const RTRTOK = 'cc_trans.RTRTOK';

	/** the column name for the MDTRTOK field */
	const MDTRTOK = 'cc_trans.MDTRTOK';

	/** the column name for the GUNID field */
	const GUNID = 'cc_trans.GUNID';

	/** the column name for the PDTOKEN field */
	const PDTOKEN = 'cc_trans.PDTOKEN';

	/** the column name for the URL field */
	const URL = 'cc_trans.URL';

	/** the column name for the LOCALFILE field */
	const LOCALFILE = 'cc_trans.LOCALFILE';

	/** the column name for the FNAME field */
	const FNAME = 'cc_trans.FNAME';

	/** the column name for the TITLE field */
	const TITLE = 'cc_trans.TITLE';

	/** the column name for the EXPECTEDSUM field */
	const EXPECTEDSUM = 'cc_trans.EXPECTEDSUM';

	/** the column name for the REALSUM field */
	const REALSUM = 'cc_trans.REALSUM';

	/** the column name for the EXPECTEDSIZE field */
	const EXPECTEDSIZE = 'cc_trans.EXPECTEDSIZE';

	/** the column name for the REALSIZE field */
	const REALSIZE = 'cc_trans.REALSIZE';

	/** the column name for the UID field */
	const UID = 'cc_trans.UID';

	/** the column name for the ERRMSG field */
	const ERRMSG = 'cc_trans.ERRMSG';

	/** the column name for the JOBPID field */
	const JOBPID = 'cc_trans.JOBPID';

	/** the column name for the START field */
	const START = 'cc_trans.START';

	/** the column name for the TS field */
	const TS = 'cc_trans.TS';

	/**
	 * An identiy map to hold any loaded instances of CcTrans objects.
	 * This must be public so that other peer classes can access this when hydrating from JOIN
	 * queries.
	 * @var        array CcTrans[]
	 */
	public static $instances = array();


	/**
	 * holds an array of fieldnames
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
	 */
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('Id', 'Trtok', 'Direction', 'State', 'Trtype', 'Lock', 'Target', 'Rtrtok', 'Mdtrtok', 'Gunid', 'Pdtoken', 'Url', 'Localfile', 'Fname', 'Title', 'Expectedsum', 'Realsum', 'Expectedsize', 'Realsize', 'Uid', 'Errmsg', 'Jobpid', 'Start', 'Ts', ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'trtok', 'direction', 'state', 'trtype', 'lock', 'target', 'rtrtok', 'mdtrtok', 'gunid', 'pdtoken', 'url', 'localfile', 'fname', 'title', 'expectedsum', 'realsum', 'expectedsize', 'realsize', 'uid', 'errmsg', 'jobpid', 'start', 'ts', ),
		BasePeer::TYPE_COLNAME => array (self::ID, self::TRTOK, self::DIRECTION, self::STATE, self::TRTYPE, self::LOCK, self::TARGET, self::RTRTOK, self::MDTRTOK, self::GUNID, self::PDTOKEN, self::URL, self::LOCALFILE, self::FNAME, self::TITLE, self::EXPECTEDSUM, self::REALSUM, self::EXPECTEDSIZE, self::REALSIZE, self::UID, self::ERRMSG, self::JOBPID, self::START, self::TS, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID', 'TRTOK', 'DIRECTION', 'STATE', 'TRTYPE', 'LOCK', 'TARGET', 'RTRTOK', 'MDTRTOK', 'GUNID', 'PDTOKEN', 'URL', 'LOCALFILE', 'FNAME', 'TITLE', 'EXPECTEDSUM', 'REALSUM', 'EXPECTEDSIZE', 'REALSIZE', 'UID', 'ERRMSG', 'JOBPID', 'START', 'TS', ),
		BasePeer::TYPE_FIELDNAME => array ('id', 'trtok', 'direction', 'state', 'trtype', 'lock', 'target', 'rtrtok', 'mdtrtok', 'gunid', 'pdtoken', 'url', 'localfile', 'fname', 'title', 'expectedsum', 'realsum', 'expectedsize', 'realsize', 'uid', 'errmsg', 'jobpid', 'start', 'ts', ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
	);

	/**
	 * holds an array of keys for quick access to the fieldnames array
	 *
	 * first dimension keys are the type constants
	 * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
	 */
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Trtok' => 1, 'Direction' => 2, 'State' => 3, 'Trtype' => 4, 'Lock' => 5, 'Target' => 6, 'Rtrtok' => 7, 'Mdtrtok' => 8, 'Gunid' => 9, 'Pdtoken' => 10, 'Url' => 11, 'Localfile' => 12, 'Fname' => 13, 'Title' => 14, 'Expectedsum' => 15, 'Realsum' => 16, 'Expectedsize' => 17, 'Realsize' => 18, 'Uid' => 19, 'Errmsg' => 20, 'Jobpid' => 21, 'Start' => 22, 'Ts' => 23, ),
		BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'trtok' => 1, 'direction' => 2, 'state' => 3, 'trtype' => 4, 'lock' => 5, 'target' => 6, 'rtrtok' => 7, 'mdtrtok' => 8, 'gunid' => 9, 'pdtoken' => 10, 'url' => 11, 'localfile' => 12, 'fname' => 13, 'title' => 14, 'expectedsum' => 15, 'realsum' => 16, 'expectedsize' => 17, 'realsize' => 18, 'uid' => 19, 'errmsg' => 20, 'jobpid' => 21, 'start' => 22, 'ts' => 23, ),
		BasePeer::TYPE_COLNAME => array (self::ID => 0, self::TRTOK => 1, self::DIRECTION => 2, self::STATE => 3, self::TRTYPE => 4, self::LOCK => 5, self::TARGET => 6, self::RTRTOK => 7, self::MDTRTOK => 8, self::GUNID => 9, self::PDTOKEN => 10, self::URL => 11, self::LOCALFILE => 12, self::FNAME => 13, self::TITLE => 14, self::EXPECTEDSUM => 15, self::REALSUM => 16, self::EXPECTEDSIZE => 17, self::REALSIZE => 18, self::UID => 19, self::ERRMSG => 20, self::JOBPID => 21, self::START => 22, self::TS => 23, ),
		BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'TRTOK' => 1, 'DIRECTION' => 2, 'STATE' => 3, 'TRTYPE' => 4, 'LOCK' => 5, 'TARGET' => 6, 'RTRTOK' => 7, 'MDTRTOK' => 8, 'GUNID' => 9, 'PDTOKEN' => 10, 'URL' => 11, 'LOCALFILE' => 12, 'FNAME' => 13, 'TITLE' => 14, 'EXPECTEDSUM' => 15, 'REALSUM' => 16, 'EXPECTEDSIZE' => 17, 'REALSIZE' => 18, 'UID' => 19, 'ERRMSG' => 20, 'JOBPID' => 21, 'START' => 22, 'TS' => 23, ),
		BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'trtok' => 1, 'direction' => 2, 'state' => 3, 'trtype' => 4, 'lock' => 5, 'target' => 6, 'rtrtok' => 7, 'mdtrtok' => 8, 'gunid' => 9, 'pdtoken' => 10, 'url' => 11, 'localfile' => 12, 'fname' => 13, 'title' => 14, 'expectedsum' => 15, 'realsum' => 16, 'expectedsize' => 17, 'realsize' => 18, 'uid' => 19, 'errmsg' => 20, 'jobpid' => 21, 'start' => 22, 'ts' => 23, ),
		BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, )
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
	 * @param      string $column The column name for current table. (i.e. CcTransPeer::COLUMN_NAME).
	 * @return     string
	 */
	public static function alias($alias, $column)
	{
		return str_replace(CcTransPeer::TABLE_NAME.'.', $alias.'.', $column);
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
			$criteria->addSelectColumn(CcTransPeer::ID);
			$criteria->addSelectColumn(CcTransPeer::TRTOK);
			$criteria->addSelectColumn(CcTransPeer::DIRECTION);
			$criteria->addSelectColumn(CcTransPeer::STATE);
			$criteria->addSelectColumn(CcTransPeer::TRTYPE);
			$criteria->addSelectColumn(CcTransPeer::LOCK);
			$criteria->addSelectColumn(CcTransPeer::TARGET);
			$criteria->addSelectColumn(CcTransPeer::RTRTOK);
			$criteria->addSelectColumn(CcTransPeer::MDTRTOK);
			$criteria->addSelectColumn(CcTransPeer::GUNID);
			$criteria->addSelectColumn(CcTransPeer::PDTOKEN);
			$criteria->addSelectColumn(CcTransPeer::URL);
			$criteria->addSelectColumn(CcTransPeer::LOCALFILE);
			$criteria->addSelectColumn(CcTransPeer::FNAME);
			$criteria->addSelectColumn(CcTransPeer::TITLE);
			$criteria->addSelectColumn(CcTransPeer::EXPECTEDSUM);
			$criteria->addSelectColumn(CcTransPeer::REALSUM);
			$criteria->addSelectColumn(CcTransPeer::EXPECTEDSIZE);
			$criteria->addSelectColumn(CcTransPeer::REALSIZE);
			$criteria->addSelectColumn(CcTransPeer::UID);
			$criteria->addSelectColumn(CcTransPeer::ERRMSG);
			$criteria->addSelectColumn(CcTransPeer::JOBPID);
			$criteria->addSelectColumn(CcTransPeer::START);
			$criteria->addSelectColumn(CcTransPeer::TS);
		} else {
			$criteria->addSelectColumn($alias . '.ID');
			$criteria->addSelectColumn($alias . '.TRTOK');
			$criteria->addSelectColumn($alias . '.DIRECTION');
			$criteria->addSelectColumn($alias . '.STATE');
			$criteria->addSelectColumn($alias . '.TRTYPE');
			$criteria->addSelectColumn($alias . '.LOCK');
			$criteria->addSelectColumn($alias . '.TARGET');
			$criteria->addSelectColumn($alias . '.RTRTOK');
			$criteria->addSelectColumn($alias . '.MDTRTOK');
			$criteria->addSelectColumn($alias . '.GUNID');
			$criteria->addSelectColumn($alias . '.PDTOKEN');
			$criteria->addSelectColumn($alias . '.URL');
			$criteria->addSelectColumn($alias . '.LOCALFILE');
			$criteria->addSelectColumn($alias . '.FNAME');
			$criteria->addSelectColumn($alias . '.TITLE');
			$criteria->addSelectColumn($alias . '.EXPECTEDSUM');
			$criteria->addSelectColumn($alias . '.REALSUM');
			$criteria->addSelectColumn($alias . '.EXPECTEDSIZE');
			$criteria->addSelectColumn($alias . '.REALSIZE');
			$criteria->addSelectColumn($alias . '.UID');
			$criteria->addSelectColumn($alias . '.ERRMSG');
			$criteria->addSelectColumn($alias . '.JOBPID');
			$criteria->addSelectColumn($alias . '.START');
			$criteria->addSelectColumn($alias . '.TS');
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
		$criteria->setPrimaryTableName(CcTransPeer::TABLE_NAME);

		if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->setDistinct();
		}

		if (!$criteria->hasSelectClause()) {
			CcTransPeer::addSelectColumns($criteria);
		}

		$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if ($con === null) {
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return     CcTrans
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = CcTransPeer::doSelect($critcopy, $con);
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
		return CcTransPeer::populateObjects(CcTransPeer::doSelectStmt($criteria, $con));
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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		if (!$criteria->hasSelectClause()) {
			$criteria = clone $criteria;
			CcTransPeer::addSelectColumns($criteria);
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
	 * @param      CcTrans $value A CcTrans object.
	 * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
	 */
	public static function addInstanceToPool(CcTrans $obj, $key = null)
	{
		if (Propel::isInstancePoolingEnabled()) {
			if ($key === null) {
				$key = (string) $obj->getId();
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
	 * @param      mixed $value A CcTrans object or a primary key value.
	 */
	public static function removeInstanceFromPool($value)
	{
		if (Propel::isInstancePoolingEnabled() && $value !== null) {
			if (is_object($value) && $value instanceof CcTrans) {
				$key = (string) $value->getId();
			} elseif (is_scalar($value)) {
				// assume we've been passed a primary key
				$key = (string) $value;
			} else {
				$e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcTrans object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
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
	 * @return     CcTrans Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
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
	 * Method to invalidate the instance pool of all tables related to cc_trans
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
		$cls = CcTransPeer::getOMClass(false);
		// populate the object(s)
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$key = CcTransPeer::getPrimaryKeyHashFromRow($row, 0);
			if (null !== ($obj = CcTransPeer::getInstanceFromPool($key))) {
				// We no longer rehydrate the object, since this can cause data loss.
				// See http://www.propelorm.org/ticket/509
				// $obj->hydrate($row, 0, true); // rehydrate
				$results[] = $obj;
			} else {
				$obj = new $cls();
				$obj->hydrate($row);
				$results[] = $obj;
				CcTransPeer::addInstanceToPool($obj, $key);
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
	 * @return     array (CcTrans object, last column rank)
	 */
	public static function populateObject($row, $startcol = 0)
	{
		$key = CcTransPeer::getPrimaryKeyHashFromRow($row, $startcol);
		if (null !== ($obj = CcTransPeer::getInstanceFromPool($key))) {
			// We no longer rehydrate the object, since this can cause data loss.
			// See http://www.propelorm.org/ticket/509
			// $obj->hydrate($row, $startcol, true); // rehydrate
			$col = $startcol + CcTransPeer::NUM_COLUMNS;
		} else {
			$cls = CcTransPeer::OM_CLASS;
			$obj = new $cls();
			$col = $obj->hydrate($row, $startcol);
			CcTransPeer::addInstanceToPool($obj, $key);
		}
		return array($obj, $col);
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
	  $dbMap = Propel::getDatabaseMap(BaseCcTransPeer::DATABASE_NAME);
	  if (!$dbMap->hasTable(BaseCcTransPeer::TABLE_NAME))
	  {
	    $dbMap->addTableObject(new CcTransTableMap());
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
		return $withPrefix ? CcTransPeer::CLASS_DEFAULT : CcTransPeer::OM_CLASS;
	}

	/**
	 * Method perform an INSERT on the database, given a CcTrans or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcTrans object containing data that is used to create the INSERT statement.
	 * @param      PropelPDO $con the PropelPDO connection to use
	 * @return     mixed The new primary key.
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doInsert($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity
		} else {
			$criteria = $values->buildCriteria(); // build Criteria from CcTrans object
		}

		if ($criteria->containsKey(CcTransPeer::ID) && $criteria->keyContainsValue(CcTransPeer::ID) ) {
			throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcTransPeer::ID.')');
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
	 * Method perform an UPDATE on the database, given a CcTrans or Criteria object.
	 *
	 * @param      mixed $values Criteria or CcTrans object containing data that is used to create the UPDATE statement.
	 * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function doUpdate($values, PropelPDO $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; // rename for clarity

			$comparison = $criteria->getComparison(CcTransPeer::ID);
			$value = $criteria->remove(CcTransPeer::ID);
			if ($value) {
				$selectCriteria->add(CcTransPeer::ID, $value, $comparison);
			} else {
				$selectCriteria->setPrimaryTableName(CcTransPeer::TABLE_NAME);
			}

		} else { // $values is CcTrans object
			$criteria = $values->buildCriteria(); // gets full criteria
			$selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
		}

		// set the correct dbName
		$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	/**
	 * Method to DELETE all rows from the cc_trans table.
	 *
	 * @return     int The number of affected rows (if supported by underlying database driver).
	 */
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		$affectedRows = 0; // initialize var to track total num of affected rows
		try {
			// use transaction because $criteria could contain info
			// for more than one table or we could emulating ON DELETE CASCADE, etc.
			$con->beginTransaction();
			$affectedRows += BasePeer::doDeleteAll(CcTransPeer::TABLE_NAME, $con, CcTransPeer::DATABASE_NAME);
			// Because this db requires some delete cascade/set null emulation, we have to
			// clear the cached instance *after* the emulation has happened (since
			// instances get re-added by the select statement contained therein).
			CcTransPeer::clearInstancePool();
			CcTransPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Method perform a DELETE on the database, given a CcTrans or Criteria object OR a primary key value.
	 *
	 * @param      mixed $values Criteria or CcTrans object or primary key or array of primary keys
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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}

		if ($values instanceof Criteria) {
			// invalidate the cache for all objects of this type, since we have no
			// way of knowing (without running a query) what objects should be invalidated
			// from the cache based on this Criteria.
			CcTransPeer::clearInstancePool();
			// rename for clarity
			$criteria = clone $values;
		} elseif ($values instanceof CcTrans) { // it's a model object
			// invalidate the cache for this single object
			CcTransPeer::removeInstanceFromPool($values);
			// create criteria based on pk values
			$criteria = $values->buildPkeyCriteria();
		} else { // it's a primary key, or an array of pks
			$criteria = new Criteria(self::DATABASE_NAME);
			$criteria->add(CcTransPeer::ID, (array) $values, Criteria::IN);
			// invalidate the cache for this object(s)
			foreach ((array) $values as $singleval) {
				CcTransPeer::removeInstanceFromPool($singleval);
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
			CcTransPeer::clearRelatedInstancePool();
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Validates all modified columns of given CcTrans object.
	 * If parameter $columns is either a single column name or an array of column names
	 * than only those columns are validated.
	 *
	 * NOTICE: This does not apply to primary or foreign keys for now.
	 *
	 * @param      CcTrans $obj The object to validate.
	 * @param      mixed $cols Column name or array of column names.
	 *
	 * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
	 */
	public static function doValidate(CcTrans $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(CcTransPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(CcTransPeer::TABLE_NAME);

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

		return BasePeer::doValidate(CcTransPeer::DATABASE_NAME, CcTransPeer::TABLE_NAME, $columns);
	}

	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     CcTrans
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{

		if (null !== ($obj = CcTransPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		if ($con === null) {
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$criteria = new Criteria(CcTransPeer::DATABASE_NAME);
		$criteria->add(CcTransPeer::ID, $pk);

		$v = CcTransPeer::doSelect($criteria, $con);

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
			$con = Propel::getConnection(CcTransPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = new Criteria(CcTransPeer::DATABASE_NAME);
			$criteria->add(CcTransPeer::ID, $pks, Criteria::IN);
			$objs = CcTransPeer::doSelect($criteria, $con);
		}
		return $objs;
	}

} // BaseCcTransPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcTransPeer::buildTableMap();
