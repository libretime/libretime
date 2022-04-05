<?php


/**
 * Base static class for performing query and update operations on the 'podcast' table.
 *
 *
 *
 * @package propel.generator.airtime.om
 */
abstract class BasePodcastPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'airtime';

    /** the table name for this class */
    const TABLE_NAME = 'podcast';

    /** the related Propel class for this table */
    const OM_CLASS = 'Podcast';

    /** the related TableMap class for this table */
    const TM_CLASS = 'PodcastTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 15;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 15;

    /** the column name for the id field */
    const ID = 'podcast.id';

    /** the column name for the url field */
    const URL = 'podcast.url';

    /** the column name for the title field */
    const TITLE = 'podcast.title';

    /** the column name for the creator field */
    const CREATOR = 'podcast.creator';

    /** the column name for the description field */
    const DESCRIPTION = 'podcast.description';

    /** the column name for the language field */
    const LANGUAGE = 'podcast.language';

    /** the column name for the copyright field */
    const COPYRIGHT = 'podcast.copyright';

    /** the column name for the link field */
    const LINK = 'podcast.link';

    /** the column name for the itunes_author field */
    const ITUNES_AUTHOR = 'podcast.itunes_author';

    /** the column name for the itunes_keywords field */
    const ITUNES_KEYWORDS = 'podcast.itunes_keywords';

    /** the column name for the itunes_summary field */
    const ITUNES_SUMMARY = 'podcast.itunes_summary';

    /** the column name for the itunes_subtitle field */
    const ITUNES_SUBTITLE = 'podcast.itunes_subtitle';

    /** the column name for the itunes_category field */
    const ITUNES_CATEGORY = 'podcast.itunes_category';

    /** the column name for the itunes_explicit field */
    const ITUNES_EXPLICIT = 'podcast.itunes_explicit';

    /** the column name for the owner field */
    const OWNER = 'podcast.owner';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Podcast objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Podcast[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. PodcastPeer::$fieldNames[PodcastPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DbId', 'DbUrl', 'DbTitle', 'DbCreator', 'DbDescription', 'DbLanguage', 'DbCopyright', 'DbLink', 'DbItunesAuthor', 'DbItunesKeywords', 'DbItunesSummary', 'DbItunesSubtitle', 'DbItunesCategory', 'DbItunesExplicit', 'DbOwner', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId', 'dbUrl', 'dbTitle', 'dbCreator', 'dbDescription', 'dbLanguage', 'dbCopyright', 'dbLink', 'dbItunesAuthor', 'dbItunesKeywords', 'dbItunesSummary', 'dbItunesSubtitle', 'dbItunesCategory', 'dbItunesExplicit', 'dbOwner', ),
        BasePeer::TYPE_COLNAME => array (PodcastPeer::ID, PodcastPeer::URL, PodcastPeer::TITLE, PodcastPeer::CREATOR, PodcastPeer::DESCRIPTION, PodcastPeer::LANGUAGE, PodcastPeer::COPYRIGHT, PodcastPeer::LINK, PodcastPeer::ITUNES_AUTHOR, PodcastPeer::ITUNES_KEYWORDS, PodcastPeer::ITUNES_SUMMARY, PodcastPeer::ITUNES_SUBTITLE, PodcastPeer::ITUNES_CATEGORY, PodcastPeer::ITUNES_EXPLICIT, PodcastPeer::OWNER, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'URL', 'TITLE', 'CREATOR', 'DESCRIPTION', 'LANGUAGE', 'COPYRIGHT', 'LINK', 'ITUNES_AUTHOR', 'ITUNES_KEYWORDS', 'ITUNES_SUMMARY', 'ITUNES_SUBTITLE', 'ITUNES_CATEGORY', 'ITUNES_EXPLICIT', 'OWNER', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'url', 'title', 'creator', 'description', 'language', 'copyright', 'link', 'itunes_author', 'itunes_keywords', 'itunes_summary', 'itunes_subtitle', 'itunes_category', 'itunes_explicit', 'owner', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. PodcastPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DbId' => 0, 'DbUrl' => 1, 'DbTitle' => 2, 'DbCreator' => 3, 'DbDescription' => 4, 'DbLanguage' => 5, 'DbCopyright' => 6, 'DbLink' => 7, 'DbItunesAuthor' => 8, 'DbItunesKeywords' => 9, 'DbItunesSummary' => 10, 'DbItunesSubtitle' => 11, 'DbItunesCategory' => 12, 'DbItunesExplicit' => 13, 'DbOwner' => 14, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('dbId' => 0, 'dbUrl' => 1, 'dbTitle' => 2, 'dbCreator' => 3, 'dbDescription' => 4, 'dbLanguage' => 5, 'dbCopyright' => 6, 'dbLink' => 7, 'dbItunesAuthor' => 8, 'dbItunesKeywords' => 9, 'dbItunesSummary' => 10, 'dbItunesSubtitle' => 11, 'dbItunesCategory' => 12, 'dbItunesExplicit' => 13, 'dbOwner' => 14, ),
        BasePeer::TYPE_COLNAME => array (PodcastPeer::ID => 0, PodcastPeer::URL => 1, PodcastPeer::TITLE => 2, PodcastPeer::CREATOR => 3, PodcastPeer::DESCRIPTION => 4, PodcastPeer::LANGUAGE => 5, PodcastPeer::COPYRIGHT => 6, PodcastPeer::LINK => 7, PodcastPeer::ITUNES_AUTHOR => 8, PodcastPeer::ITUNES_KEYWORDS => 9, PodcastPeer::ITUNES_SUMMARY => 10, PodcastPeer::ITUNES_SUBTITLE => 11, PodcastPeer::ITUNES_CATEGORY => 12, PodcastPeer::ITUNES_EXPLICIT => 13, PodcastPeer::OWNER => 14, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'URL' => 1, 'TITLE' => 2, 'CREATOR' => 3, 'DESCRIPTION' => 4, 'LANGUAGE' => 5, 'COPYRIGHT' => 6, 'LINK' => 7, 'ITUNES_AUTHOR' => 8, 'ITUNES_KEYWORDS' => 9, 'ITUNES_SUMMARY' => 10, 'ITUNES_SUBTITLE' => 11, 'ITUNES_CATEGORY' => 12, 'ITUNES_EXPLICIT' => 13, 'OWNER' => 14, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'url' => 1, 'title' => 2, 'creator' => 3, 'description' => 4, 'language' => 5, 'copyright' => 6, 'link' => 7, 'itunes_author' => 8, 'itunes_keywords' => 9, 'itunes_summary' => 10, 'itunes_subtitle' => 11, 'itunes_category' => 12, 'itunes_explicit' => 13, 'owner' => 14, ),
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
        $toNames = PodcastPeer::getFieldNames($toType);
        $key = isset(PodcastPeer::$fieldKeys[$fromType][$name]) ? PodcastPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(PodcastPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, PodcastPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return PodcastPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. PodcastPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(PodcastPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(PodcastPeer::ID);
            $criteria->addSelectColumn(PodcastPeer::URL);
            $criteria->addSelectColumn(PodcastPeer::TITLE);
            $criteria->addSelectColumn(PodcastPeer::CREATOR);
            $criteria->addSelectColumn(PodcastPeer::DESCRIPTION);
            $criteria->addSelectColumn(PodcastPeer::LANGUAGE);
            $criteria->addSelectColumn(PodcastPeer::COPYRIGHT);
            $criteria->addSelectColumn(PodcastPeer::LINK);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_AUTHOR);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_KEYWORDS);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_SUMMARY);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_SUBTITLE);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_CATEGORY);
            $criteria->addSelectColumn(PodcastPeer::ITUNES_EXPLICIT);
            $criteria->addSelectColumn(PodcastPeer::OWNER);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.url');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.creator');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.language');
            $criteria->addSelectColumn($alias . '.copyright');
            $criteria->addSelectColumn($alias . '.link');
            $criteria->addSelectColumn($alias . '.itunes_author');
            $criteria->addSelectColumn($alias . '.itunes_keywords');
            $criteria->addSelectColumn($alias . '.itunes_summary');
            $criteria->addSelectColumn($alias . '.itunes_subtitle');
            $criteria->addSelectColumn($alias . '.itunes_category');
            $criteria->addSelectColumn($alias . '.itunes_explicit');
            $criteria->addSelectColumn($alias . '.owner');
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
        $criteria->setPrimaryTableName(PodcastPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PodcastPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(PodcastPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return Podcast
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = PodcastPeer::doSelect($critcopy, $con);
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
        return PodcastPeer::populateObjects(PodcastPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            PodcastPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

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
     * @param Podcast $obj A Podcast object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getDbId();
            } // if key === null
            PodcastPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Podcast object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Podcast) {
                $key = (string) $value->getDbId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Podcast object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(PodcastPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Podcast Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(PodcastPeer::$instances[$key])) {
                return PodcastPeer::$instances[$key];
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
        foreach (PodcastPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        PodcastPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to podcast
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in StationPodcastPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        StationPodcastPeer::clearInstancePool();
        // Invalidate objects in ImportedPodcastPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ImportedPodcastPeer::clearInstancePool();
        // Invalidate objects in PodcastEpisodesPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        PodcastEpisodesPeer::clearInstancePool();
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
        $cls = PodcastPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = PodcastPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = PodcastPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PodcastPeer::addInstanceToPool($obj, $key);
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
     * @return array (Podcast object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = PodcastPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = PodcastPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + PodcastPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PodcastPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            PodcastPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related CcSubjs table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCcSubjs(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PodcastPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PodcastPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PodcastPeer::OWNER, CcSubjsPeer::ID, $join_behavior);

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
     * Selects a collection of Podcast objects pre-filled with their CcSubjs objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Podcast objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCcSubjs(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PodcastPeer::DATABASE_NAME);
        }

        PodcastPeer::addSelectColumns($criteria);
        $startcol = PodcastPeer::NUM_HYDRATE_COLUMNS;
        CcSubjsPeer::addSelectColumns($criteria);

        $criteria->addJoin(PodcastPeer::OWNER, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PodcastPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PodcastPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = PodcastPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PodcastPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CcSubjsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CcSubjsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Podcast) to $obj2 (CcSubjs)
                $obj2->addPodcast($obj1);

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
        $criteria->setPrimaryTableName(PodcastPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PodcastPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PodcastPeer::OWNER, CcSubjsPeer::ID, $join_behavior);

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
     * Selects a collection of Podcast objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Podcast objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PodcastPeer::DATABASE_NAME);
        }

        PodcastPeer::addSelectColumns($criteria);
        $startcol2 = PodcastPeer::NUM_HYDRATE_COLUMNS;

        CcSubjsPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CcSubjsPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(PodcastPeer::OWNER, CcSubjsPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PodcastPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PodcastPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = PodcastPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PodcastPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined CcSubjs rows

            $key2 = CcSubjsPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CcSubjsPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CcSubjsPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CcSubjsPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Podcast) to the collection in $obj2 (CcSubjs)
                $obj2->addPodcast($obj1);
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
        return Propel::getDatabaseMap(PodcastPeer::DATABASE_NAME)->getTable(PodcastPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BasePodcastPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BasePodcastPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \PodcastTableMap());
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
        return PodcastPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Podcast or Criteria object.
     *
     * @param      mixed $values Criteria or Podcast object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Podcast object
        }

        if ($criteria->containsKey(PodcastPeer::ID) && $criteria->keyContainsValue(PodcastPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PodcastPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Podcast or Criteria object.
     *
     * @param      mixed $values Criteria or Podcast object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(PodcastPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(PodcastPeer::ID);
            $value = $criteria->remove(PodcastPeer::ID);
            if ($value) {
                $selectCriteria->add(PodcastPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(PodcastPeer::TABLE_NAME);
            }

        } else { // $values is Podcast object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the podcast table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(PodcastPeer::TABLE_NAME, $con, PodcastPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PodcastPeer::clearInstancePool();
            PodcastPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Podcast or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Podcast object or primary key or array of primary keys
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
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            PodcastPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Podcast) { // it's a model object
            // invalidate the cache for this single object
            PodcastPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PodcastPeer::DATABASE_NAME);
            $criteria->add(PodcastPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                PodcastPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(PodcastPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            PodcastPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Podcast object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Podcast $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(PodcastPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(PodcastPeer::TABLE_NAME);

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

        return BasePeer::doValidate(PodcastPeer::DATABASE_NAME, PodcastPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Podcast
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = PodcastPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(PodcastPeer::DATABASE_NAME);
        $criteria->add(PodcastPeer::ID, $pk);

        $v = PodcastPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Podcast[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(PodcastPeer::DATABASE_NAME);
            $criteria->add(PodcastPeer::ID, $pks, Criteria::IN);
            $objs = PodcastPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BasePodcastPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasePodcastPeer::buildTableMap();
