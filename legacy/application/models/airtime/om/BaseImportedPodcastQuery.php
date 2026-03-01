<?php


/**
 * Base class that represents a query for the 'imported_podcast' table.
 *
 *
 *
 * @method ImportedPodcastQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method ImportedPodcastQuery orderByDbAutoIngest($order = Criteria::ASC) Order by the auto_ingest column
 * @method ImportedPodcastQuery orderByDbAutoIngestTimestamp($order = Criteria::ASC) Order by the auto_ingest_timestamp column
 * @method ImportedPodcastQuery orderByDbAlbumOverride($order = Criteria::ASC) Order by the album_override column
 * @method ImportedPodcastQuery orderByDbPodcastId($order = Criteria::ASC) Order by the podcast_id column
 *
 * @method ImportedPodcastQuery groupByDbId() Group by the id column
 * @method ImportedPodcastQuery groupByDbAutoIngest() Group by the auto_ingest column
 * @method ImportedPodcastQuery groupByDbAutoIngestTimestamp() Group by the auto_ingest_timestamp column
 * @method ImportedPodcastQuery groupByDbAlbumOverride() Group by the album_override column
 * @method ImportedPodcastQuery groupByDbPodcastId() Group by the podcast_id column
 *
 * @method ImportedPodcastQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ImportedPodcastQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ImportedPodcastQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ImportedPodcastQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method ImportedPodcastQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method ImportedPodcastQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method ImportedPodcast findOne(PropelPDO $con = null) Return the first ImportedPodcast matching the query
 * @method ImportedPodcast findOneOrCreate(PropelPDO $con = null) Return the first ImportedPodcast matching the query, or a new ImportedPodcast object populated from the query conditions when no match is found
 *
 * @method ImportedPodcast findOneByDbAutoIngest(boolean $auto_ingest) Return the first ImportedPodcast filtered by the auto_ingest column
 * @method ImportedPodcast findOneByDbAutoIngestTimestamp(string $auto_ingest_timestamp) Return the first ImportedPodcast filtered by the auto_ingest_timestamp column
 * @method ImportedPodcast findOneByDbAlbumOverride(boolean $album_override) Return the first ImportedPodcast filtered by the album_override column
 * @method ImportedPodcast findOneByDbPodcastId(int $podcast_id) Return the first ImportedPodcast filtered by the podcast_id column
 *
 * @method array findByDbId(int $id) Return ImportedPodcast objects filtered by the id column
 * @method array findByDbAutoIngest(boolean $auto_ingest) Return ImportedPodcast objects filtered by the auto_ingest column
 * @method array findByDbAutoIngestTimestamp(string $auto_ingest_timestamp) Return ImportedPodcast objects filtered by the auto_ingest_timestamp column
 * @method array findByDbAlbumOverride(boolean $album_override) Return ImportedPodcast objects filtered by the album_override column
 * @method array findByDbPodcastId(int $podcast_id) Return ImportedPodcast objects filtered by the podcast_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseImportedPodcastQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseImportedPodcastQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'airtime';
        }
        if (null === $modelName) {
            $modelName = 'ImportedPodcast';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ImportedPodcastQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ImportedPodcastQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ImportedPodcastQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ImportedPodcastQuery) {
            return $criteria;
        }
        $query = new ImportedPodcastQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   ImportedPodcast|ImportedPodcast[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ImportedPodcastPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ImportedPodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 ImportedPodcast A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByDbId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 ImportedPodcast A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "auto_ingest", "auto_ingest_timestamp", "album_override", "podcast_id" FROM "imported_podcast" WHERE "id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new ImportedPodcast();
            $obj->hydrate($row);
            ImportedPodcastPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return ImportedPodcast|ImportedPodcast[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|ImportedPodcast[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbId(1234); // WHERE id = 1234
     * $query->filterByDbId(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterByDbId(array('min' => 12)); // WHERE id >= 12
     * $query->filterByDbId(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $dbId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the auto_ingest column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoIngest(true); // WHERE auto_ingest = true
     * $query->filterByDbAutoIngest('yes'); // WHERE auto_ingest = true
     * </code>
     *
     * @param     boolean|string $dbAutoIngest The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbAutoIngest($dbAutoIngest = null, $comparison = null)
    {
        if (is_string($dbAutoIngest)) {
            $dbAutoIngest = in_array(strtolower($dbAutoIngest), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ImportedPodcastPeer::AUTO_INGEST, $dbAutoIngest, $comparison);
    }

    /**
     * Filter the query on the auto_ingest_timestamp column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoIngestTimestamp('2011-03-14'); // WHERE auto_ingest_timestamp = '2011-03-14'
     * $query->filterByDbAutoIngestTimestamp('now'); // WHERE auto_ingest_timestamp = '2011-03-14'
     * $query->filterByDbAutoIngestTimestamp(array('max' => 'yesterday')); // WHERE auto_ingest_timestamp < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbAutoIngestTimestamp The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbAutoIngestTimestamp($dbAutoIngestTimestamp = null, $comparison = null)
    {
        if (is_array($dbAutoIngestTimestamp)) {
            $useMinMax = false;
            if (isset($dbAutoIngestTimestamp['min'])) {
                $this->addUsingAlias(ImportedPodcastPeer::AUTO_INGEST_TIMESTAMP, $dbAutoIngestTimestamp['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbAutoIngestTimestamp['max'])) {
                $this->addUsingAlias(ImportedPodcastPeer::AUTO_INGEST_TIMESTAMP, $dbAutoIngestTimestamp['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::AUTO_INGEST_TIMESTAMP, $dbAutoIngestTimestamp, $comparison);
    }

    /**
     * Filter the query on the album_override column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAlbumOverride(true); // WHERE album_override = true
     * $query->filterByDbAlbumOverride('yes'); // WHERE album_override = true
     * </code>
     *
     * @param     boolean|string $dbAlbumOverride The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbAlbumOverride($dbAlbumOverride = null, $comparison = null)
    {
        if (is_string($dbAlbumOverride)) {
            $dbAlbumOverride = in_array(strtolower($dbAlbumOverride), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ImportedPodcastPeer::ALBUM_OVERRIDE, $dbAlbumOverride, $comparison);
    }

    /**
     * Filter the query on the podcast_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPodcastId(1234); // WHERE podcast_id = 1234
     * $query->filterByDbPodcastId(array(12, 34)); // WHERE podcast_id IN (12, 34)
     * $query->filterByDbPodcastId(array('min' => 12)); // WHERE podcast_id >= 12
     * $query->filterByDbPodcastId(array('max' => 12)); // WHERE podcast_id <= 12
     * </code>
     *
     * @see       filterByPodcast()
     *
     * @param     mixed $dbPodcastId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function filterByDbPodcastId($dbPodcastId = null, $comparison = null)
    {
        if (is_array($dbPodcastId)) {
            $useMinMax = false;
            if (isset($dbPodcastId['min'])) {
                $this->addUsingAlias(ImportedPodcastPeer::PODCAST_ID, $dbPodcastId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPodcastId['max'])) {
                $this->addUsingAlias(ImportedPodcastPeer::PODCAST_ID, $dbPodcastId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImportedPodcastPeer::PODCAST_ID, $dbPodcastId, $comparison);
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ImportedPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(ImportedPodcastPeer::PODCAST_ID, $podcast->getDbId(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImportedPodcastPeer::PODCAST_ID, $podcast->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByPodcast() only accepts arguments of type Podcast or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Podcast relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function joinPodcast($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Podcast');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Podcast');
        }

        return $this;
    }

    /**
     * Use the Podcast relation Podcast object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PodcastQuery A secondary query class using the current class as primary query
     */
    public function usePodcastQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPodcast($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Podcast', 'PodcastQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ImportedPodcast $importedPodcast Object to remove from the list of results
     *
     * @return ImportedPodcastQuery The current query, for fluid interface
     */
    public function prune($importedPodcast = null)
    {
        if ($importedPodcast) {
            $this->addUsingAlias(ImportedPodcastPeer::ID, $importedPodcast->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
