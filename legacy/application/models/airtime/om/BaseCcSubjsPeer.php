<?php


/**
 * Base static class for performing query and update operations on the 'cc_subjs' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseCcSubjsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'cc_subjs';

    /** the related Propel class for this table */
    const OM_CLASS = 'CcSubjs';

    /** the related TableMap class for this table */
    const TM_CLASS = 'CcSubjsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 14;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 14;

    /** the column name for the id field */
    const ID = 'cc_subjs.id';

    /** the column name for the login field */
    const LOGIN = 'cc_subjs.login';

    /** the column name for the pass field */
    const PASS = 'cc_subjs.pass';

    /** the column name for the type field */
    const TYPE = 'cc_subjs.type';

    /** the column name for the is_active field */
    const IS_ACTIVE = 'cc_subjs.is_active';

    /** the column name for the first_name field */
    const FIRST_NAME = 'cc_subjs.first_name';

    /** the column name for the last_name field */
    const LAST_NAME = 'cc_subjs.last_name';

    /** the column name for the lastlogin field */
    const LASTLOGIN = 'cc_subjs.lastlogin';

    /** the column name for the lastfail field */
    const LASTFAIL = 'cc_subjs.lastfail';

    /** the column name for the skype_contact field */
    const SKYPE_CONTACT = 'cc_subjs.skype_contact';

    /** the column name for the jabber_contact field */
    const JABBER_CONTACT = 'cc_subjs.jabber_contact';

    /** the column name for the email field */
    const EMAIL = 'cc_subjs.email';

    /** the column name for the cell_phone field */
    const CELL_PHONE = 'cc_subjs.cell_phone';

    /** the column name for the login_attempts field */
    const LOGIN_ATTEMPTS = 'cc_subjs.login_attempts';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of CcSubjs objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array CcSubjs[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CcSubjsPeer::$fieldNames[CcSubjsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbLogin', 'DbPass', 'DbType', 'DbIsActive', 'DbFirstName', 'DbLastName', 'DbLastlogin', 'DbLastfail', 'DbSkypeContact', 'DbJabberContact', 'DbEmail', 'DbCellPhone', 'DbLoginAttempts', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbLogin', 'dbPass', 'dbType', 'dbIsActive', 'dbFirstName', 'dbLastName', 'dbLastlogin', 'dbLastfail', 'dbSkypeContact', 'dbJabberContact', 'dbEmail', 'dbCellPhone', 'dbLoginAttempts', ),
        BasePeer::TYPE_COLNAME => array (CcSubjsPeer::ID, CcSubjsPeer::LOGIN, CcSubjsPeer::PASS, CcSubjsPeer::TYPE, CcSubjsPeer::IS_ACTIVE, CcSubjsPeer::FIRST_NAME, CcSubjsPeer::LAST_NAME, CcSubjsPeer::LASTLOGIN, CcSubjsPeer::LASTFAIL, CcSubjsPeer::SKYPE_CONTACT, CcSubjsPeer::JABBER_CONTACT, CcSubjsPeer::EMAIL, CcSubjsPeer::CELL_PHONE, CcSubjsPeer::LOGIN_ATTEMPTS, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'LOGIN', 'PASS', 'TYPE', 'IS_ACTIVE', 'FIRST_NAME', 'LAST_NAME', 'LASTLOGIN', 'LASTFAIL', 'SKYPE_CONTACT', 'JABBER_CONTACT', 'EMAIL', 'CELL_PHONE', 'LOGIN_ATTEMPTS', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'login', 'pass', 'type', 'is_active', 'first_name', 'last_name', 'lastlogin', 'lastfail', 'skype_contact', 'jabber_contact', 'email', 'cell_phone', 'login_attempts', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CcSubjsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbLogin' => 1, 'DbPass' => 2, 'DbType' => 3, 'DbIsActive' => 4, 'DbFirstName' => 5, 'DbLastName' => 6, 'DbLastlogin' => 7, 'DbLastfail' => 8, 'DbSkypeContact' => 9, 'DbJabberContact' => 10, 'DbEmail' => 11, 'DbCellPhone' => 12, 'DbLoginAttempts' => 13, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbLogin' => 1, 'dbPass' => 2, 'dbType' => 3, 'dbIsActive' => 4, 'dbFirstName' => 5, 'dbLastName' => 6, 'dbLastlogin' => 7, 'dbLastfail' => 8, 'dbSkypeContact' => 9, 'dbJabberContact' => 10, 'dbEmail' => 11, 'dbCellPhone' => 12, 'dbLoginAttempts' => 13, ),
        BasePeer::TYPE_COLNAME => array (CcSubjsPeer::ID => 0, CcSubjsPeer::LOGIN => 1, CcSubjsPeer::PASS => 2, CcSubjsPeer::TYPE => 3, CcSubjsPeer::IS_ACTIVE => 4, CcSubjsPeer::FIRST_NAME => 5, CcSubjsPeer::LAST_NAME => 6, CcSubjsPeer::LASTLOGIN => 7, CcSubjsPeer::LASTFAIL => 8, CcSubjsPeer::SKYPE_CONTACT => 9, CcSubjsPeer::JABBER_CONTACT => 10, CcSubjsPeer::EMAIL => 11, CcSubjsPeer::CELL_PHONE => 12, CcSubjsPeer::LOGIN_ATTEMPTS => 13, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'LOGIN' => 1, 'PASS' => 2, 'TYPE' => 3, 'IS_ACTIVE' => 4, 'FIRST_NAME' => 5, 'LAST_NAME' => 6, 'LASTLOGIN' => 7, 'LASTFAIL' => 8, 'SKYPE_CONTACT' => 9, 'JABBER_CONTACT' => 10, 'EMAIL' => 11, 'CELL_PHONE' => 12, 'LOGIN_ATTEMPTS' => 13, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'login' => 1, 'pass' => 2, 'type' => 3, 'is_active' => 4, 'first_name' => 5, 'last_name' => 6, 'lastlogin' => 7, 'lastfail' => 8, 'skype_contact' => 9, 'jabber_contact' => 10, 'email' => 11, 'cell_phone' => 12, 'login_attempts' => 13, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
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
        $toNames = CcSubjsPeer::getFieldNames($toType);
        $key = isset(CcSubjsPeer::$fieldKeys[$fromType][$name]) ? CcSubjsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CcSubjsPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, CcSubjsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CcSubjsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. CcSubjsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CcSubjsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(CcSubjsPeer::ID);
            $criteria->addSelectColumn(CcSubjsPeer::LOGIN);
            $criteria->addSelectColumn(CcSubjsPeer::PASS);
            $criteria->addSelectColumn(CcSubjsPeer::TYPE);
            $criteria->addSelectColumn(CcSubjsPeer::IS_ACTIVE);
            $criteria->addSelectColumn(CcSubjsPeer::FIRST_NAME);
            $criteria->addSelectColumn(CcSubjsPeer::LAST_NAME);
            $criteria->addSelectColumn(CcSubjsPeer::LASTLOGIN);
            $criteria->addSelectColumn(CcSubjsPeer::LASTFAIL);
            $criteria->addSelectColumn(CcSubjsPeer::SKYPE_CONTACT);
            $criteria->addSelectColumn(CcSubjsPeer::JABBER_CONTACT);
            $criteria->addSelectColumn(CcSubjsPeer::EMAIL);
            $criteria->addSelectColumn(CcSubjsPeer::CELL_PHONE);
            $criteria->addSelectColumn(CcSubjsPeer::LOGIN_ATTEMPTS);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.login');
            $criteria->addSelectColumn($alias . '.pass');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.is_active');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.last_name');
            $criteria->addSelectColumn($alias . '.lastlogin');
            $criteria->addSelectColumn($alias . '.lastfail');
            $criteria->addSelectColumn($alias . '.skype_contact');
            $criteria->addSelectColumn($alias . '.jabber_contact');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.cell_phone');
            $criteria->addSelectColumn($alias . '.login_attempts');
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
        $criteria->setPrimaryTableName(CcSubjsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcSubjsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CcSubjsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return CcSubjs
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CcSubjsPeer::doSelect($critcopy, $con);
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
        return CcSubjsPeer::populateObjects(CcSubjsPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CcSubjsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CcSubjsPeer::DATABASE_NAME);

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
     * @param CcSubjs $obj A CcSubjs object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            CcSubjsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A CcSubjs object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof CcSubjs) {
                $key = (string) $value->getDbId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcSubjs object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(CcSubjsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return CcSubjs Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CcSubjsPeer::$instances[$key])) {
                return CcSubjsPeer::$instances[$key];
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
        foreach (CcSubjsPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CcSubjsPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to cc_subjs
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in CcShowHostsPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcShowHostsPeer::clearInstancePool();
        // Invalidate objects in CcPlaylistPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcPlaylistPeer::clearInstancePool();
        // Invalidate objects in CcBlockPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcBlockPeer::clearInstancePool();
        // Invalidate objects in CcPrefPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcPrefPeer::clearInstancePool();
        // Invalidate objects in CcSubjsTokenPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcSubjsTokenPeer::clearInstancePool();
        // Invalidate objects in PodcastPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        PodcastPeer::clearInstancePool();
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
        $cls = CcSubjsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = CcSubjsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = CcSubjsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CcSubjsPeer::addInstanceToPool($obj, $key);
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
     * @return array (CcSubjs object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CcSubjsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CcSubjsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CcSubjsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            CcSubjsPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
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
        return Propel::getDatabaseMap(CcSubjsPeer::DATABASE_NAME)->getTable(CcSubjsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCcSubjsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCcSubjsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \CcSubjsTableMap());
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
        return CcSubjsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a CcSubjs or Criteria object.
     *
     * @param      mixed $values Criteria or CcSubjs object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from CcSubjs object
        }

        if ($criteria->containsKey(CcSubjsPeer::ID) && $criteria->keyContainsValue(CcSubjsPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcSubjsPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CcSubjsPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a CcSubjs or Criteria object.
     *
     * @param      mixed $values Criteria or CcSubjs object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CcSubjsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(CcSubjsPeer::ID);
            $value = $criteria->remove(CcSubjsPeer::ID);
            if ($value) {
                $selectCriteria->add(CcSubjsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(CcSubjsPeer::TABLE_NAME);
            }

        } else { // $values is CcSubjs object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(CcSubjsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the cc_subjs table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(CcSubjsPeer::TABLE_NAME, $con, CcSubjsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CcSubjsPeer::clearInstancePool();
            CcSubjsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a CcSubjs or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CcSubjs object or primary key or array of primary keys
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
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            CcSubjsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof CcSubjs) { // it's a model object
            // invalidate the cache for this single object
            CcSubjsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CcSubjsPeer::DATABASE_NAME);
            $criteria->add(CcSubjsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CcSubjsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CcSubjsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CcSubjsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given CcSubjs object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param CcSubjs $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CcSubjsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CcSubjsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(CcSubjsPeer::DATABASE_NAME, CcSubjsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return CcSubjs
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = CcSubjsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(CcSubjsPeer::DATABASE_NAME);
        $criteria->add(CcSubjsPeer::ID, $pk);

        $v = CcSubjsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return CcSubjs[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcSubjsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(CcSubjsPeer::DATABASE_NAME);
            $criteria->add(CcSubjsPeer::ID, $pks, Criteria::IN);
            $objs = CcSubjsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseCcSubjsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcSubjsPeer::buildTableMap();
