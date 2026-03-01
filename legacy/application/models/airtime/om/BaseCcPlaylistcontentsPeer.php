<?php


/**
 * Base static class for performing query and update operations on the 'cc_playlistcontents' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseCcPlaylistcontentsPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'cc_playlistcontents';

    /** the related Propel class for this table */
    const OM_CLASS = 'CcPlaylistcontents';

    /** the related TableMap class for this table */
    const TM_CLASS = 'CcPlaylistcontentsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 13;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 13;

    /** the column name for the id field */
    const ID = 'cc_playlistcontents.id';

    /** the column name for the playlist_id field */
    const PLAYLIST_ID = 'cc_playlistcontents.playlist_id';

    /** the column name for the file_id field */
    const FILE_ID = 'cc_playlistcontents.file_id';

    /** the column name for the block_id field */
    const BLOCK_ID = 'cc_playlistcontents.block_id';

    /** the column name for the stream_id field */
    const STREAM_ID = 'cc_playlistcontents.stream_id';

    /** the column name for the type field */
    const TYPE = 'cc_playlistcontents.type';

    /** the column name for the position field */
    const POSITION = 'cc_playlistcontents.position';

    /** the column name for the trackoffset field */
    const TRACKOFFSET = 'cc_playlistcontents.trackoffset';

    /** the column name for the cliplength field */
    const CLIPLENGTH = 'cc_playlistcontents.cliplength';

    /** the column name for the cuein field */
    const CUEIN = 'cc_playlistcontents.cuein';

    /** the column name for the cueout field */
    const CUEOUT = 'cc_playlistcontents.cueout';

    /** the column name for the fadein field */
    const FADEIN = 'cc_playlistcontents.fadein';

    /** the column name for the fadeout field */
    const FADEOUT = 'cc_playlistcontents.fadeout';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of CcPlaylistcontents objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array CcPlaylistcontents[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CcPlaylistcontentsPeer::$fieldNames[CcPlaylistcontentsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbPlaylistId', 'DbFileId', 'DbBlockId', 'DbStreamId', 'DbType', 'DbPosition', 'DbTrackOffset', 'DbCliplength', 'DbCuein', 'DbCueout', 'DbFadein', 'DbFadeout', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbPlaylistId', 'dbFileId', 'dbBlockId', 'dbStreamId', 'dbType', 'dbPosition', 'dbTrackOffset', 'dbCliplength', 'dbCuein', 'dbCueout', 'dbFadein', 'dbFadeout', ),
        BasePeer::TYPE_COLNAME => array (CcPlaylistcontentsPeer::ID, CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistcontentsPeer::FILE_ID, CcPlaylistcontentsPeer::BLOCK_ID, CcPlaylistcontentsPeer::STREAM_ID, CcPlaylistcontentsPeer::TYPE, CcPlaylistcontentsPeer::POSITION, CcPlaylistcontentsPeer::TRACKOFFSET, CcPlaylistcontentsPeer::CLIPLENGTH, CcPlaylistcontentsPeer::CUEIN, CcPlaylistcontentsPeer::CUEOUT, CcPlaylistcontentsPeer::FADEIN, CcPlaylistcontentsPeer::FADEOUT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'PLAYLIST_ID', 'FILE_ID', 'BLOCK_ID', 'STREAM_ID', 'TYPE', 'POSITION', 'TRACKOFFSET', 'CLIPLENGTH', 'CUEIN', 'CUEOUT', 'FADEIN', 'FADEOUT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'playlist_id', 'file_id', 'block_id', 'stream_id', 'type', 'position', 'trackoffset', 'cliplength', 'cuein', 'cueout', 'fadein', 'fadeout', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CcPlaylistcontentsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbPlaylistId' => 1, 'DbFileId' => 2, 'DbBlockId' => 3, 'DbStreamId' => 4, 'DbType' => 5, 'DbPosition' => 6, 'DbTrackOffset' => 7, 'DbCliplength' => 8, 'DbCuein' => 9, 'DbCueout' => 10, 'DbFadein' => 11, 'DbFadeout' => 12, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbPlaylistId' => 1, 'dbFileId' => 2, 'dbBlockId' => 3, 'dbStreamId' => 4, 'dbType' => 5, 'dbPosition' => 6, 'dbTrackOffset' => 7, 'dbCliplength' => 8, 'dbCuein' => 9, 'dbCueout' => 10, 'dbFadein' => 11, 'dbFadeout' => 12, ),
        BasePeer::TYPE_COLNAME => array (CcPlaylistcontentsPeer::ID => 0, CcPlaylistcontentsPeer::PLAYLIST_ID => 1, CcPlaylistcontentsPeer::FILE_ID => 2, CcPlaylistcontentsPeer::BLOCK_ID => 3, CcPlaylistcontentsPeer::STREAM_ID => 4, CcPlaylistcontentsPeer::TYPE => 5, CcPlaylistcontentsPeer::POSITION => 6, CcPlaylistcontentsPeer::TRACKOFFSET => 7, CcPlaylistcontentsPeer::CLIPLENGTH => 8, CcPlaylistcontentsPeer::CUEIN => 9, CcPlaylistcontentsPeer::CUEOUT => 10, CcPlaylistcontentsPeer::FADEIN => 11, CcPlaylistcontentsPeer::FADEOUT => 12, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'PLAYLIST_ID' => 1, 'FILE_ID' => 2, 'BLOCK_ID' => 3, 'STREAM_ID' => 4, 'TYPE' => 5, 'POSITION' => 6, 'TRACKOFFSET' => 7, 'CLIPLENGTH' => 8, 'CUEIN' => 9, 'CUEOUT' => 10, 'FADEIN' => 11, 'FADEOUT' => 12, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'playlist_id' => 1, 'file_id' => 2, 'block_id' => 3, 'stream_id' => 4, 'type' => 5, 'position' => 6, 'trackoffset' => 7, 'cliplength' => 8, 'cuein' => 9, 'cueout' => 10, 'fadein' => 11, 'fadeout' => 12, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
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
        $toNames = CcPlaylistcontentsPeer::getFieldNames($toType);
        $key = isset(CcPlaylistcontentsPeer::$fieldKeys[$fromType][$name]) ? CcPlaylistcontentsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CcPlaylistcontentsPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, CcPlaylistcontentsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CcPlaylistcontentsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. CcPlaylistcontentsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(CcPlaylistcontentsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::ID);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::PLAYLIST_ID);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::FILE_ID);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::BLOCK_ID);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::STREAM_ID);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::TYPE);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::POSITION);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::TRACKOFFSET);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::CLIPLENGTH);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::CUEIN);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::CUEOUT);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::FADEIN);
            $criteria->addSelectColumn(CcPlaylistcontentsPeer::FADEOUT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.playlist_id');
            $criteria->addSelectColumn($alias . '.file_id');
            $criteria->addSelectColumn($alias . '.block_id');
            $criteria->addSelectColumn($alias . '.stream_id');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.position');
            $criteria->addSelectColumn($alias . '.trackoffset');
            $criteria->addSelectColumn($alias . '.cliplength');
            $criteria->addSelectColumn($alias . '.cuein');
            $criteria->addSelectColumn($alias . '.cueout');
            $criteria->addSelectColumn($alias . '.fadein');
            $criteria->addSelectColumn($alias . '.fadeout');
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
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return CcPlaylistcontents
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = CcPlaylistcontentsPeer::doSelect($critcopy, $con);
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
        return CcPlaylistcontentsPeer::populateObjects(CcPlaylistcontentsPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

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
     * @param CcPlaylistcontents $obj A CcPlaylistcontents object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            CcPlaylistcontentsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A CcPlaylistcontents object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof CcPlaylistcontents) {
                $key = (string) $value->getDbId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or CcPlaylistcontents object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(CcPlaylistcontentsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return CcPlaylistcontents Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CcPlaylistcontentsPeer::$instances[$key])) {
                return CcPlaylistcontentsPeer::$instances[$key];
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
        foreach (CcPlaylistcontentsPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CcPlaylistcontentsPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to cc_playlistcontents
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
        $cls = CcPlaylistcontentsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = CcPlaylistcontentsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CcPlaylistcontentsPeer::addInstanceToPool($obj, $key);
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
     * @return array (CcPlaylistcontents object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CcPlaylistcontentsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CcPlaylistcontentsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            CcPlaylistcontentsPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcFiles table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcFiles(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcBlock(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcPlaylist(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
     * Selects a collection of CcPlaylistcontents objects pre-filled with their CcFiles objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcFiles(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;
        CcFilesPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcFilesPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcFilesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcFilesPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to $obj2 (CcFiles)
                $obj2->addCcPlaylistcontents($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcPlaylistcontents objects pre-filled with their CcBlock objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcBlock(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;
        CcBlockPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcBlockPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcBlockPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcBlockPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to $obj2 (CcBlock)
                $obj2->addCcPlaylistcontents($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcPlaylistcontents objects pre-filled with their CcPlaylist objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcPlaylist(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;
        CcPlaylistPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcPlaylistPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcPlaylistPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcPlaylistPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to $obj2 (CcPlaylist)
                $obj2->addCcPlaylistcontents($obj1);

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
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
     * Selects a collection of CcPlaylistcontents objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol2 = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcBlockPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcBlockPeer::NUM_HYDRATE_COLUMNS;

        CcPlaylistPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CcPlaylistPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcFiles rows

            $key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcFilesPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcFilesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcFilesPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj2 (CcFiles)
                $obj2->addCcPlaylistcontents($obj1);
            } // if joined row not null

            // Add objects for joined CcBlock rows

            $key3 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CcBlockPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CcBlockPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcBlockPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj3 (CcBlock)
                $obj3->addCcPlaylistcontents($obj1);
            } // if joined row not null

            // Add objects for joined CcPlaylist rows

            $key4 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = CcPlaylistPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = CcPlaylistPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CcPlaylistPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj4 (CcPlaylist)
                $obj4->addCcPlaylistcontents($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcFiles table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcFiles(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcBlock(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcPlaylist(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcPlaylistcontentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

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
     * Selects a collection of CcPlaylistcontents objects pre-filled with all related objects except CcFiles.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcFiles(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol2 = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;

        CcBlockPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcBlockPeer::NUM_HYDRATE_COLUMNS;

        CcPlaylistPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcPlaylistPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcBlock rows

                $key2 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcBlockPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcBlockPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcBlockPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj2 (CcBlock)
                $obj2->addCcPlaylistcontents($obj1);

            } // if joined row is not null

                // Add objects for joined CcPlaylist rows

                $key3 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcPlaylistPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcPlaylistPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcPlaylistPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj3 (CcPlaylist)
                $obj3->addCcPlaylistcontents($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcPlaylistcontents objects pre-filled with all related objects except CcBlock.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcBlock(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol2 = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcPlaylistPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcPlaylistPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::PLAYLIST_ID, CcPlaylistPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcFiles rows

                $key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcFilesPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcFilesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcFilesPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj2 (CcFiles)
                $obj2->addCcPlaylistcontents($obj1);

            } // if joined row is not null

                // Add objects for joined CcPlaylist rows

                $key3 = CcPlaylistPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcPlaylistPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcPlaylistPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcPlaylistPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj3 (CcPlaylist)
                $obj3->addCcPlaylistcontents($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcPlaylistcontents objects pre-filled with all related objects except CcPlaylist.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcPlaylistcontents objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcPlaylist(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);
        }

        CcPlaylistcontentsPeer::addSelectColumns($criteria);
        $startcol2 = CcPlaylistcontentsPeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcBlockPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcBlockPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcPlaylistcontentsPeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcPlaylistcontentsPeer::BLOCK_ID, CcBlockPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcPlaylistcontentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcPlaylistcontentsPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcPlaylistcontentsPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcPlaylistcontentsPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcFiles rows

                $key2 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcFilesPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcFilesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcFilesPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj2 (CcFiles)
                $obj2->addCcPlaylistcontents($obj1);

            } // if joined row is not null

                // Add objects for joined CcBlock rows

                $key3 = CcBlockPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcBlockPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcBlockPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcBlockPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcPlaylistcontents) to the collection in $obj3 (CcBlock)
                $obj3->addCcPlaylistcontents($obj1);

            } // if joined row is not null

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
        return Propel::getDatabaseMap(CcPlaylistcontentsPeer::DATABASE_NAME)->getTable(CcPlaylistcontentsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCcPlaylistcontentsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCcPlaylistcontentsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \CcPlaylistcontentsTableMap());
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
        return CcPlaylistcontentsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a CcPlaylistcontents or Criteria object.
     *
     * @param      mixed $values Criteria or CcPlaylistcontents object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from CcPlaylistcontents object
        }

        if ($criteria->containsKey(CcPlaylistcontentsPeer::ID) && $criteria->keyContainsValue(CcPlaylistcontentsPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcPlaylistcontentsPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a CcPlaylistcontents or Criteria object.
     *
     * @param      mixed $values Criteria or CcPlaylistcontents object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(CcPlaylistcontentsPeer::ID);
            $value = $criteria->remove(CcPlaylistcontentsPeer::ID);
            if ($value) {
                $selectCriteria->add(CcPlaylistcontentsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(CcPlaylistcontentsPeer::TABLE_NAME);
            }

        } else { // $values is CcPlaylistcontents object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the cc_playlistcontents table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(CcPlaylistcontentsPeer::TABLE_NAME, $con, CcPlaylistcontentsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CcPlaylistcontentsPeer::clearInstancePool();
            CcPlaylistcontentsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a CcPlaylistcontents or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CcPlaylistcontents object or primary key or array of primary keys
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
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            CcPlaylistcontentsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof CcPlaylistcontents) { // it's a model object
            // invalidate the cache for this single object
            CcPlaylistcontentsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);
            $criteria->add(CcPlaylistcontentsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CcPlaylistcontentsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CcPlaylistcontentsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CcPlaylistcontentsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given CcPlaylistcontents object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param CcPlaylistcontents $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CcPlaylistcontentsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CcPlaylistcontentsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(CcPlaylistcontentsPeer::DATABASE_NAME, CcPlaylistcontentsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return CcPlaylistcontents
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = CcPlaylistcontentsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);
        $criteria->add(CcPlaylistcontentsPeer::ID, $pk);

        $v = CcPlaylistcontentsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return CcPlaylistcontents[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcPlaylistcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(CcPlaylistcontentsPeer::DATABASE_NAME);
            $criteria->add(CcPlaylistcontentsPeer::ID, $pks, Criteria::IN);
            $objs = CcPlaylistcontentsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseCcPlaylistcontentsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseCcPlaylistcontentsPeer::buildTableMap();
