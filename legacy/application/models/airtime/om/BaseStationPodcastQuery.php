<?php


/**
 * Base class that represents a query for the 'station_podcast' table.
 *
 *
 *
 * @method StationPodcastQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method StationPodcastQuery orderByDbPodcastId($order = Criteria::ASC) Order by the podcast_id column
 *
 * @method StationPodcastQuery groupByDbId() Group by the id column
 * @method StationPodcastQuery groupByDbPodcastId() Group by the podcast_id column
 *
 * @method StationPodcastQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StationPodcastQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StationPodcastQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StationPodcastQuery leftJoinPodcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the Podcast relation
 * @method StationPodcastQuery rightJoinPodcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Podcast relation
 * @method StationPodcastQuery innerJoinPodcast($relationAlias = null) Adds a INNER JOIN clause to the query using the Podcast relation
 *
 * @method StationPodcast findOne(PropelPDO $con = null) Return the first StationPodcast matching the query
 * @method StationPodcast findOneOrCreate(PropelPDO $con = null) Return the first StationPodcast matching the query, or a new StationPodcast object populated from the query conditions when no match is found
 *
 * @method StationPodcast findOneByDbPodcastId(int $podcast_id) Return the first StationPodcast filtered by the podcast_id column
 *
 * @method array findByDbId(int $id) Return StationPodcast objects filtered by the id column
 * @method array findByDbPodcastId(int $podcast_id) Return StationPodcast objects filtered by the podcast_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseStationPodcastQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseStationPodcastQuery object.
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
            $modelName = 'StationPodcast';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new StationPodcastQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   StationPodcastQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StationPodcastQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StationPodcastQuery) {
            return $criteria;
        }
        $query = new StationPodcastQuery(null, null, $modelAlias);

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
     * @return   StationPodcast|StationPodcast[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StationPodcastPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StationPodcastPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 StationPodcast A model object, or null if the key is not found
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
     * @return                 StationPodcast A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "podcast_id" FROM "station_podcast" WHERE "id" = :p0';
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
            $obj = new StationPodcast();
            $obj->hydrate($row);
            StationPodcastPeer::addInstanceToPool($obj, (string) $key);
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
     * @return StationPodcast|StationPodcast[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|StationPodcast[]|mixed the list of results, formatted by the current formatter
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
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StationPodcastPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StationPodcastPeer::ID, $keys, Criteria::IN);
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
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(StationPodcastPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(StationPodcastPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::ID, $dbId, $comparison);
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
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function filterByDbPodcastId($dbPodcastId = null, $comparison = null)
    {
        if (is_array($dbPodcastId)) {
            $useMinMax = false;
            if (isset($dbPodcastId['min'])) {
                $this->addUsingAlias(StationPodcastPeer::PODCAST_ID, $dbPodcastId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPodcastId['max'])) {
                $this->addUsingAlias(StationPodcastPeer::PODCAST_ID, $dbPodcastId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StationPodcastPeer::PODCAST_ID, $dbPodcastId, $comparison);
    }

    /**
     * Filter the query by a related Podcast object
     *
     * @param   Podcast|PropelObjectCollection $podcast The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StationPodcastQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPodcast($podcast, $comparison = null)
    {
        if ($podcast instanceof Podcast) {
            return $this
                ->addUsingAlias(StationPodcastPeer::PODCAST_ID, $podcast->getDbId(), $comparison);
        } elseif ($podcast instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StationPodcastPeer::PODCAST_ID, $podcast->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return StationPodcastQuery The current query, for fluid interface
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
     * @param   StationPodcast $stationPodcast Object to remove from the list of results
     *
     * @return StationPodcastQuery The current query, for fluid interface
     */
    public function prune($stationPodcast = null)
    {
        if ($stationPodcast) {
            $this->addUsingAlias(StationPodcastPeer::ID, $stationPodcast->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
