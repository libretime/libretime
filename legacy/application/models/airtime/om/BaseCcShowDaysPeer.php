<?php


/**
 * Base static class for performing query and update operations on the 'cc_show_days' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseCcShowDaysPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'cc_show_days';

    /** the related Propel class for this table */
    const OM_CLASS = 'CcShowDays';

    /** the related TableMap class for this table */
    const TM_CLASS = 'CcShowDaysTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 11;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 11;

    /** the column name for the id field */
    const ID = 'cc_show_days.id';

    /** the column name for the first_show field */
    const FIRST_SHOW = 'cc_show_days.first_show';

    /** the column name for the last_show field */
    const LAST_SHOW = 'cc_show_days.last_show';

    /** the column name for the start_time field */
    const START_TIME = 'cc_show_days.start_time';

    /** the column name for the timezone field */
    const TIMEZONE = 'cc_show_days.timezone';

    /** the column name for the duration field */
    const DURATION = 'cc_show_days.duration';

    /** the column name for the day field */
    const DAY = 'cc_show_days.day';

    /** the column name for the repeat_type field */
    const REPEAT_TYPE = 'cc_show_days.repeat_type';

    /** the column name for the next_pop_date field */
    const NEXT_POP_DATE = 'cc_show_days.next_pop_date';

    /** the column name for the show_id field */
    const SHOW_ID = 'cc_show_days.show_id';

    /** the column name for the record field */
    const RECORD = 'cc_show_days.record';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of CcShowDays objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array CcShowDays[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CcShowDaysPeer::$fieldNames[CcShowDaysPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbFirstShow', 'DbLastShow', 'DbStartTime', 'DbTimezone', 'DbDuration', 'DbDay', 'DbRepeatType', 'DbNextPopDate', 'DbShowId', 'DbRecord', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbFirstShow', 'dbLastShow', 'dbStartTime', 'dbTimezone', 'dbDuration', 'dbDay', 'dbRepeatType', 'dbNextPopDate', 'dbShowId', 'dbRecord', ),
        BasePeer::TYPE_COLNAME => array (CcShowDaysPeer::ID, CcShowDaysPeer::FIRST_SHOW, CcShowDaysPeer::LAST_SHOW, CcShowDaysPeer::START_TIME, CcShowDaysPeer::TIMEZONE, CcShowDaysPeer::DURATION, CcShowDaysPeer::DAY, CcShowDaysPeer::REPEAT_TYPE, CcShowDaysPeer::NEXT_POP_DATE, CcShowDaysPeer::SHOW_ID, CcShowDaysPeer::RECORD, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'FIRST_SHOW', 'LAST_SHOW', 'START_TIME', 'TIMEZONE', 'DURATION', 'DAY', 'REPEAT_TYPE', 'NEXT_POP_DATE', 'SHOW_ID', 'RECORD', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'first_show', 'last_show', 'start_time', 'timezone', 'duration', 'day', 'repeat_type', 'next_pop_date', 'show_id', 'record', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CcShowDaysPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbFirstShow' => 1, 'DbLastShow' => 2, 'DbStartTime' => 3, 'DbTimezone' => 4, 'DbDuration' => 5, 'DbDay' => 6, 'DbRepeatType' => 7, 'DbNextPopDate' => 8, 'DbShowId' => 9, 'DbRecord' => 10, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbFirstShow' => 1, 'dbLastShow' => 2, 'dbStartTime' => 3, 'dbTimezone' => 4, 'dbDuration' => 5, 'dbDay' => 6, 'dbRepeatType' => 7, 'dbNextPopDate' => 8, 'dbShowId' => 9, 'dbRecord' => 10, ),
        BasePeer::TYPE_COLNAME => array (CcShowDaysPeer::ID => 0, CcShowDaysPeer::FIRST_SHOW => 1, CcShowDaysPeer::LAST_SHOW => 2, CcShowDaysPeer::START_TIME => 3, CcShowDaysPeer::TIMEZONE => 4, CcShowDaysPeer::DURATION => 5, CcShowDaysPeer::DAY => 6, CcShowDaysPeer::REPEAT_TYPE => 7, CcShowDaysPeer::NEXT_POP_DATE => 8, CcShowDaysPeer::SHOW_ID => 9, CcShowDaysPeer::RECORD => 10, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'FIRST_SHOW' => 1, 'LAST_SHOW' => 2, 'START_TIME' => 3, 'TIMEZONE' => 4, 'DURATION' => 5, 'DAY' => 6, 'REPEAT_TYPE' => 7, 'NEXT_POP_DATE' => 8, 'SHOW_ID' => 9, 'RECORD' => 10, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'first_show' => 1, 'last_show' => 2, 'start_time' => 3, 'timezone' => 4, 'duration' => 5, 'day' => 6, 'repeat_type' => 7, 'next_pop_date' => 8, 'show_id' => 9, 'record' => 10, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = CcShowDaysPeer::getFieldNames($toType);
        $key = isset(CcShowDaysPeer::$fieldKeys[$fromType][$name]) ? CcShowDaysPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CcShowDaysPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, CcShowDaysPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CcShowDaysPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. CcShowDaysPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CcShowDaysPeer::TABLE_NAME.'.', $alias.'.', $column);
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CcShowDaysPeer::ID);
            $criteria->addSelectColumn(CcShowDaysPeer::FIRST_SHOW);
            $criteria->addSelectColumn(CcShowDaysPeer::LAST_SHOW);
            $criteria->addSelectColumn(CcShowDaysPeer::START_TIME);
            $criteria->addSelectColumn(CcShowDaysPeer::TIMEZONE);
            $criteria->addSelectColumn(CcShowDaysPeer::DURATION);
            $criteria->addSelectColumn(CcShowDaysPeer::DAY);
            $criteria->addSelectColumn(CcShowDaysPeer::REPEAT_TYPE);
            $criteria->addSelectColumn(CcShowDaysPeer::NEXT_POP_DATE);
            $criteria->addSelectColumn(CcShowDaysPeer::SHOW_ID);
            $criteria->addSelectColumn(CcShowDaysPeer::RECORD);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.first_show');
            $criteria->addSelectColumn($alias . '.last_show');
            $criteria->addSelectColumn($alias . '.start_time');
            $criteria->addSelectColumn($alias . '.timezone');
            $criteria->addSelectColumn($alias . '.duration');
            $criteria->addSelectColumn($alias . '.day');
            $criteria->addSelectColumn($alias . '.repeat_type');
            $criteria->addSelectColumn($alias . '.next_pop_date');
            $criteria->addSelectColumn($alias . '.show_id');
            $criteria->addSelectColumn($alias . '.record');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcShowDaysPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcShowDaysPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return CcShowDays
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CcShowDaysPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return CcShowDaysPeer::populateObjects(CcShowDaysPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CcShowDaysPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

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
     * @param CcShowDays $obj A CcShowDays object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            CcShowDaysPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A CcShowDays object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof CcShowDays) {
                $key = (string) $value->getDbId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcShowDays object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(CcShowDaysPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return CcShowDays Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CcShowDaysPeer::$instances[$key])) {
                return CcShowDaysPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (CcShowDaysPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CcShowDaysPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to cc_show_days
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
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
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
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = CcShowDaysPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = CcShowDaysPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = CcShowDaysPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CcShowDaysPeer::addInstanceToPool($obj, $key);
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (CcShowDays object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CcShowDaysPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CcShowDaysPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CcShowDaysPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CcShowDaysPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            CcShowDaysPeer::addInstanceToPool($obj, $key);
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
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcShow(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcShowDaysPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcShowDaysPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcShowDaysPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

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
     * Selects a collection of CcShowDays objects pre-filled with their CcShow objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcShowDays objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcShow(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);
        }

        CcShowDaysPeer::addSelectColumns($criteria);
        $startcol = CcShowDaysPeer::NUM_HYDRATE_COLUMNS;
        CcShowPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcShowDaysPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcShowDaysPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcShowDaysPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcShowDaysPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcShowDaysPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcShowPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcShowPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcShowPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcShowDays) to $obj2 (CcShow)
                $obj2->addCcShowDays($obj1);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcShowDaysPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcShowDaysPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcShowDaysPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

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
     * Selects a collection of CcShowDays objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcShowDays objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);
        }

        CcShowDaysPeer::addSelectColumns($criteria);
        $startcol2 = CcShowDaysPeer::NUM_HYDRATE_COLUMNS;

        CcShowPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcShowPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcShowDaysPeer::SHOW_ID, CcShowPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcShowDaysPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcShowDaysPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcShowDaysPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcShowDaysPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcShow rows

            $key2 = CcShowPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcShowPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcShowPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcShowPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (CcShowDays) to the collection in $obj2 (CcShow)
                $obj2->addCcShowDays($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(CcShowDaysPeer::DATABASE_NAME)->getTable(CcShowDaysPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCcShowDaysPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCcShowDaysPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \CcShowDaysTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return CcShowDaysPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a CcShowDays or Criteria object.
     *
     * @param      mixed $values Criteria or CcShowDays object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from CcShowDays object
        }

        if ($criteria->containsKey(CcShowDaysPeer::ID) && $criteria->keyContainsValue(CcShowDaysPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcShowDaysPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a CcShowDays or Criteria object.
     *
     * @param      mixed $values Criteria or CcShowDays object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(CcShowDaysPeer::ID);
            $value = $criteria->remove(CcShowDaysPeer::ID);
            if ($value) {
                $selectCriteria->add(CcShowDaysPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(CcShowDaysPeer::TABLE_NAME);
            }

        } else { // $values is CcShowDays object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the cc_show_days table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(CcShowDaysPeer::TABLE_NAME, $con, CcShowDaysPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CcShowDaysPeer::clearInstancePool();
            CcShowDaysPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a CcShowDays or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CcShowDays object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            CcShowDaysPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof CcShowDays) { // it's a model object
            // invalidate the cache for this single object
            CcShowDaysPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);
            $criteria->add(CcShowDaysPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CcShowDaysPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CcShowDaysPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CcShowDaysPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given CcShowDays object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param CcShowDays $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CcShowDaysPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CcShowDaysPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(CcShowDaysPeer::DATABASE_NAME, CcShowDaysPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return CcShowDays
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = CcShowDaysPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);
        $criteria->add(CcShowDaysPeer::ID, $pk);

        $v = CcShowDaysPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return CcShowDays[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcShowDaysPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(CcShowDaysPeer::DATABASE_NAME);
            $criteria->add(CcShowDaysPeer::ID, $pks, Criteria::IN);
            $objs = CcShowDaysPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseCcShowDaysPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcShowDaysPeer::buildTableMap();
