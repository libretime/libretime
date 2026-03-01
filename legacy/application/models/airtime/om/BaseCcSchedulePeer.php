<?php


/**
 * Base static class for performing query and update operations on the 'cc_schedule' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BaseCcSchedulePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'cc_schedule';

    /** the related Propel class for this table */
    const OM_CLASS = 'CcSchedule';

    /** the related TableMap class for this table */
    const TM_CLASS = 'CcScheduleTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 15;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 15;

    /** the column name for the id field */
    const ID = 'cc_schedule.id';

    /** the column name for the starts field */
    const STARTS = 'cc_schedule.starts';

    /** the column name for the ends field */
    const ENDS = 'cc_schedule.ends';

    /** the column name for the file_id field */
    const FILE_ID = 'cc_schedule.file_id';

    /** the column name for the stream_id field */
    const STREAM_ID = 'cc_schedule.stream_id';

    /** the column name for the clip_length field */
    const CLIP_LENGTH = 'cc_schedule.clip_length';

    /** the column name for the fade_in field */
    const FADE_IN = 'cc_schedule.fade_in';

    /** the column name for the fade_out field */
    const FADE_OUT = 'cc_schedule.fade_out';

    /** the column name for the cue_in field */
    const CUE_IN = 'cc_schedule.cue_in';

    /** the column name for the cue_out field */
    const CUE_OUT = 'cc_schedule.cue_out';

    /** the column name for the media_item_played field */
    const MEDIA_ITEM_PLAYED = 'cc_schedule.media_item_played';

    /** the column name for the instance_id field */
    const INSTANCE_ID = 'cc_schedule.instance_id';

    /** the column name for the playout_status field */
    const PLAYOUT_STATUS = 'cc_schedule.playout_status';

    /** the column name for the broadcasted field */
    const BROADCASTED = 'cc_schedule.broadcasted';

    /** the column name for the position field */
    const POSITION = 'cc_schedule.position';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of CcSchedule objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array CcSchedule[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. CcSchedulePeer::$fieldNames[CcSchedulePeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbStarts', 'DbEnds', 'DbFileId', 'DbStreamId', 'DbClipLength', 'DbFadeIn', 'DbFadeOut', 'DbCueIn', 'DbCueOut', 'DbMediaItemPlayed', 'DbInstanceId', 'DbPlayoutStatus', 'DbBroadcasted', 'DbPosition', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbStarts', 'dbEnds', 'dbFileId', 'dbStreamId', 'dbClipLength', 'dbFadeIn', 'dbFadeOut', 'dbCueIn', 'dbCueOut', 'dbMediaItemPlayed', 'dbInstanceId', 'dbPlayoutStatus', 'dbBroadcasted', 'dbPosition', ),
        BasePeer::TYPE_COLNAME => array (CcSchedulePeer::ID, CcSchedulePeer::STARTS, CcSchedulePeer::ENDS, CcSchedulePeer::FILE_ID, CcSchedulePeer::STREAM_ID, CcSchedulePeer::CLIP_LENGTH, CcSchedulePeer::FADE_IN, CcSchedulePeer::FADE_OUT, CcSchedulePeer::CUE_IN, CcSchedulePeer::CUE_OUT, CcSchedulePeer::MEDIA_ITEM_PLAYED, CcSchedulePeer::INSTANCE_ID, CcSchedulePeer::PLAYOUT_STATUS, CcSchedulePeer::BROADCASTED, CcSchedulePeer::POSITION, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'STARTS', 'ENDS', 'FILE_ID', 'STREAM_ID', 'CLIP_LENGTH', 'FADE_IN', 'FADE_OUT', 'CUE_IN', 'CUE_OUT', 'MEDIA_ITEM_PLAYED', 'INSTANCE_ID', 'PLAYOUT_STATUS', 'BROADCASTED', 'POSITION', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'starts', 'ends', 'file_id', 'stream_id', 'clip_length', 'fade_in', 'fade_out', 'cue_in', 'cue_out', 'media_item_played', 'instance_id', 'playout_status', 'broadcasted', 'position', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. CcSchedulePeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbStarts' => 1, 'DbEnds' => 2, 'DbFileId' => 3, 'DbStreamId' => 4, 'DbClipLength' => 5, 'DbFadeIn' => 6, 'DbFadeOut' => 7, 'DbCueIn' => 8, 'DbCueOut' => 9, 'DbMediaItemPlayed' => 10, 'DbInstanceId' => 11, 'DbPlayoutStatus' => 12, 'DbBroadcasted' => 13, 'DbPosition' => 14, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbStarts' => 1, 'dbEnds' => 2, 'dbFileId' => 3, 'dbStreamId' => 4, 'dbClipLength' => 5, 'dbFadeIn' => 6, 'dbFadeOut' => 7, 'dbCueIn' => 8, 'dbCueOut' => 9, 'dbMediaItemPlayed' => 10, 'dbInstanceId' => 11, 'dbPlayoutStatus' => 12, 'dbBroadcasted' => 13, 'dbPosition' => 14, ),
        BasePeer::TYPE_COLNAME => array (CcSchedulePeer::ID => 0, CcSchedulePeer::STARTS => 1, CcSchedulePeer::ENDS => 2, CcSchedulePeer::FILE_ID => 3, CcSchedulePeer::STREAM_ID => 4, CcSchedulePeer::CLIP_LENGTH => 5, CcSchedulePeer::FADE_IN => 6, CcSchedulePeer::FADE_OUT => 7, CcSchedulePeer::CUE_IN => 8, CcSchedulePeer::CUE_OUT => 9, CcSchedulePeer::MEDIA_ITEM_PLAYED => 10, CcSchedulePeer::INSTANCE_ID => 11, CcSchedulePeer::PLAYOUT_STATUS => 12, CcSchedulePeer::BROADCASTED => 13, CcSchedulePeer::POSITION => 14, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'STARTS' => 1, 'ENDS' => 2, 'FILE_ID' => 3, 'STREAM_ID' => 4, 'CLIP_LENGTH' => 5, 'FADE_IN' => 6, 'FADE_OUT' => 7, 'CUE_IN' => 8, 'CUE_OUT' => 9, 'MEDIA_ITEM_PLAYED' => 10, 'INSTANCE_ID' => 11, 'PLAYOUT_STATUS' => 12, 'BROADCASTED' => 13, 'POSITION' => 14, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'starts' => 1, 'ends' => 2, 'file_id' => 3, 'stream_id' => 4, 'clip_length' => 5, 'fade_in' => 6, 'fade_out' => 7, 'cue_in' => 8, 'cue_out' => 9, 'media_item_played' => 10, 'instance_id' => 11, 'playout_status' => 12, 'broadcasted' => 13, 'position' => 14, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
        $toNames = CcSchedulePeer::getFieldNames($toType);
        $key = isset(CcSchedulePeer::$fieldKeys[$fromType][$name]) ? CcSchedulePeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(CcSchedulePeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, CcSchedulePeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return CcSchedulePeer::$fieldNames[$type];
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
     * @return string
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CcSchedulePeer::ID);
            $criteria->addSelectColumn(CcSchedulePeer::STARTS);
            $criteria->addSelectColumn(CcSchedulePeer::ENDS);
            $criteria->addSelectColumn(CcSchedulePeer::FILE_ID);
            $criteria->addSelectColumn(CcSchedulePeer::STREAM_ID);
            $criteria->addSelectColumn(CcSchedulePeer::CLIP_LENGTH);
            $criteria->addSelectColumn(CcSchedulePeer::FADE_IN);
            $criteria->addSelectColumn(CcSchedulePeer::FADE_OUT);
            $criteria->addSelectColumn(CcSchedulePeer::CUE_IN);
            $criteria->addSelectColumn(CcSchedulePeer::CUE_OUT);
            $criteria->addSelectColumn(CcSchedulePeer::MEDIA_ITEM_PLAYED);
            $criteria->addSelectColumn(CcSchedulePeer::INSTANCE_ID);
            $criteria->addSelectColumn(CcSchedulePeer::PLAYOUT_STATUS);
            $criteria->addSelectColumn(CcSchedulePeer::BROADCASTED);
            $criteria->addSelectColumn(CcSchedulePeer::POSITION);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.starts');
            $criteria->addSelectColumn($alias . '.ends');
            $criteria->addSelectColumn($alias . '.file_id');
            $criteria->addSelectColumn($alias . '.stream_id');
            $criteria->addSelectColumn($alias . '.clip_length');
            $criteria->addSelectColumn($alias . '.fade_in');
            $criteria->addSelectColumn($alias . '.fade_out');
            $criteria->addSelectColumn($alias . '.cue_in');
            $criteria->addSelectColumn($alias . '.cue_out');
            $criteria->addSelectColumn($alias . '.media_item_played');
            $criteria->addSelectColumn($alias . '.instance_id');
            $criteria->addSelectColumn($alias . '.playout_status');
            $criteria->addSelectColumn($alias . '.broadcasted');
            $criteria->addSelectColumn($alias . '.position');
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
        $criteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcSchedulePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME); // Set the correct dbName

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
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return CcSchedule
     * @throws PropelException Any exceptions caught during processing will be
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
        return CcSchedulePeer::populateObjects(CcSchedulePeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            CcSchedulePeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

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
     * @param CcSchedule $obj A CcSchedule object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            CcSchedulePeer::$instances[$key] = $obj;
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
     *
     * @return void
     * @throws PropelException - if the value is invalid.
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

            unset(CcSchedulePeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return CcSchedule Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(CcSchedulePeer::$instances[$key])) {
                return CcSchedulePeer::$instances[$key];
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
        foreach (CcSchedulePeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        CcSchedulePeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to cc_schedule
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in CcWebstreamMetadataPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        CcWebstreamMetadataPeer::clearInstancePool();
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
        $cls = CcSchedulePeer::getOMClass();
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
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (CcSchedule object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = CcSchedulePeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = CcSchedulePeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + CcSchedulePeer::NUM_HYDRATE_COLUMNS;
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
     * @return int Number of matching rows.
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
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

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
        $criteria->setPrimaryTableName(CcSchedulePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            CcSchedulePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcWebstream(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

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
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcShowInstances(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol = CcSchedulePeer::NUM_HYDRATE_COLUMNS;
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

                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcShowInstancesPeer::getOMClass();

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
     * Selects a collection of CcSchedule objects pre-filled with their CcFiles objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcFiles(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol = CcSchedulePeer::NUM_HYDRATE_COLUMNS;
        CcFilesPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (CcSchedule) to $obj2 (CcFiles)
                $obj2->addCcSchedule($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcSchedule objects pre-filled with their CcWebstream objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcWebstream(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol = CcSchedulePeer::NUM_HYDRATE_COLUMNS;
        CcWebstreamPeer::addSelectColumns($criteria);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcWebstreamPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcWebstreamPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcWebstreamPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (CcSchedule) to $obj2 (CcWebstream)
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
     * @return int Number of matching rows.
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
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

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
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol2 = CcSchedulePeer::NUM_HYDRATE_COLUMNS;

        CcShowInstancesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcShowInstancesPeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcWebstreamPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CcWebstreamPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcShowInstances rows

            $key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcShowInstancesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcShowInstancesPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj2 (CcShowInstances)
                $obj2->addCcSchedule($obj1);
            } // if joined row not null

            // Add objects for joined CcFiles rows

            $key3 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CcFilesPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CcFilesPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcFilesPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj3 (CcFiles)
                $obj3->addCcSchedule($obj1);
            } // if joined row not null

            // Add objects for joined CcWebstream rows

            $key4 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = CcWebstreamPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = CcWebstreamPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CcWebstreamPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj4 (CcWebstream)
                $obj4->addCcSchedule($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcShowInstances table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcShowInstances(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcFiles(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);

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
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCcWebstream(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
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

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

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
     * Selects a collection of CcSchedule objects pre-filled with all related objects except CcShowInstances.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcShowInstances(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol2 = CcSchedulePeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        CcWebstreamPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcWebstreamPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
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

                // Add the $obj1 (CcSchedule) to the collection in $obj2 (CcFiles)
                $obj2->addCcSchedule($obj1);

            } // if joined row is not null

                // Add objects for joined CcWebstream rows

                $key3 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcWebstreamPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcWebstreamPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcWebstreamPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj3 (CcWebstream)
                $obj3->addCcSchedule($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcSchedule objects pre-filled with all related objects except CcFiles.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcSchedule objects.
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
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol2 = CcSchedulePeer::NUM_HYDRATE_COLUMNS;

        CcShowInstancesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcShowInstancesPeer::NUM_HYDRATE_COLUMNS;

        CcWebstreamPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcWebstreamPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::STREAM_ID, CcWebstreamPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcShowInstances rows

                $key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcShowInstancesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcShowInstancesPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj2 (CcShowInstances)
                $obj2->addCcSchedule($obj1);

            } // if joined row is not null

                // Add objects for joined CcWebstream rows

                $key3 = CcWebstreamPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcWebstreamPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcWebstreamPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcWebstreamPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj3 (CcWebstream)
                $obj3->addCcSchedule($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of CcSchedule objects pre-filled with all related objects except CcWebstream.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of CcSchedule objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCcWebstream(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);
        }

        CcSchedulePeer::addSelectColumns($criteria);
        $startcol2 = CcSchedulePeer::NUM_HYDRATE_COLUMNS;

        CcShowInstancesPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcShowInstancesPeer::NUM_HYDRATE_COLUMNS;

        CcFilesPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CcFilesPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(CcSchedulePeer::INSTANCE_ID, CcShowInstancesPeer::ID, $join_behavior);

        $criteria->addJoin(CcSchedulePeer::FILE_ID, CcFilesPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = CcSchedulePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = CcSchedulePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = CcSchedulePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                CcSchedulePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined CcShowInstances rows

                $key2 = CcShowInstancesPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CcShowInstancesPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CcShowInstancesPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcShowInstancesPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj2 (CcShowInstances)
                $obj2->addCcSchedule($obj1);

            } // if joined row is not null

                // Add objects for joined CcFiles rows

                $key3 = CcFilesPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CcFilesPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CcFilesPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CcFilesPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (CcSchedule) to the collection in $obj3 (CcFiles)
                $obj3->addCcSchedule($obj1);

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
        return Propel::getDatabaseMap(CcSchedulePeer::DATABASE_NAME)->getTable(CcSchedulePeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseCcSchedulePeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseCcSchedulePeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \CcScheduleTableMap());
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
        return CcSchedulePeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a CcSchedule or Criteria object.
     *
     * @param      mixed $values Criteria or CcSchedule object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
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

        if ($criteria->containsKey(CcSchedulePeer::ID) && $criteria->keyContainsValue(CcSchedulePeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcSchedulePeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a CcSchedule or Criteria object.
     *
     * @param      mixed $values Criteria or CcSchedule object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(CcSchedulePeer::DATABASE_NAME);

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
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the cc_schedule table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
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
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a CcSchedule or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or CcSchedule object or primary key or array of primary keys
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
            $criteria = new Criteria(CcSchedulePeer::DATABASE_NAME);
            $criteria->add(CcSchedulePeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                CcSchedulePeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(CcSchedulePeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            CcSchedulePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
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
     * @param CcSchedule $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(CcSchedulePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(CcSchedulePeer::TABLE_NAME);

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

        return BasePeer::doValidate(CcSchedulePeer::DATABASE_NAME, CcSchedulePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return CcSchedule
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
     * @return CcSchedule[]
     * @throws PropelException Any exceptions caught during processing will be
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
