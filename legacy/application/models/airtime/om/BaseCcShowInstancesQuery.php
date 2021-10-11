<?php


/**
 * Base class that represents a query for the 'cc_show_instances' table.
 *
 *
 *
 * @method CcShowInstancesQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcShowInstancesQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcShowInstancesQuery orderByDbStarts($order = Criteria::ASC) Order by the starts column
 * @method CcShowInstancesQuery orderByDbEnds($order = Criteria::ASC) Order by the ends column
 * @method CcShowInstancesQuery orderByDbShowId($order = Criteria::ASC) Order by the show_id column
 * @method CcShowInstancesQuery orderByDbRecord($order = Criteria::ASC) Order by the record column
 * @method CcShowInstancesQuery orderByDbRebroadcast($order = Criteria::ASC) Order by the rebroadcast column
 * @method CcShowInstancesQuery orderByDbOriginalShow($order = Criteria::ASC) Order by the instance_id column
 * @method CcShowInstancesQuery orderByDbRecordedFile($order = Criteria::ASC) Order by the file_id column
 * @method CcShowInstancesQuery orderByDbTimeFilled($order = Criteria::ASC) Order by the time_filled column
 * @method CcShowInstancesQuery orderByDbCreated($order = Criteria::ASC) Order by the created column
 * @method CcShowInstancesQuery orderByDbLastScheduled($order = Criteria::ASC) Order by the last_scheduled column
 * @method CcShowInstancesQuery orderByDbModifiedInstance($order = Criteria::ASC) Order by the modified_instance column
 * @method CcShowInstancesQuery orderByDbAutoPlaylistBuilt($order = Criteria::ASC) Order by the autoplaylist_built column
 *
 * @method CcShowInstancesQuery groupByDbId() Group by the id column
 * @method CcShowInstancesQuery groupByDbDescription() Group by the description column
 * @method CcShowInstancesQuery groupByDbStarts() Group by the starts column
 * @method CcShowInstancesQuery groupByDbEnds() Group by the ends column
 * @method CcShowInstancesQuery groupByDbShowId() Group by the show_id column
 * @method CcShowInstancesQuery groupByDbRecord() Group by the record column
 * @method CcShowInstancesQuery groupByDbRebroadcast() Group by the rebroadcast column
 * @method CcShowInstancesQuery groupByDbOriginalShow() Group by the instance_id column
 * @method CcShowInstancesQuery groupByDbRecordedFile() Group by the file_id column
 * @method CcShowInstancesQuery groupByDbTimeFilled() Group by the time_filled column
 * @method CcShowInstancesQuery groupByDbCreated() Group by the created column
 * @method CcShowInstancesQuery groupByDbLastScheduled() Group by the last_scheduled column
 * @method CcShowInstancesQuery groupByDbModifiedInstance() Group by the modified_instance column
 * @method CcShowInstancesQuery groupByDbAutoPlaylistBuilt() Group by the autoplaylist_built column
 *
 * @method CcShowInstancesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcShowInstancesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcShowInstancesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcShowInstancesQuery leftJoinCcShow($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShow relation
 * @method CcShowInstancesQuery rightJoinCcShow($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShow relation
 * @method CcShowInstancesQuery innerJoinCcShow($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShow relation
 *
 * @method CcShowInstancesQuery leftJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 * @method CcShowInstancesQuery rightJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 * @method CcShowInstancesQuery innerJoinCcShowInstancesRelatedByDbOriginalShow($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
 *
 * @method CcShowInstancesQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method CcShowInstancesQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method CcShowInstancesQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method CcShowInstancesQuery leftJoinCcShowInstancesRelatedByDbId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 * @method CcShowInstancesQuery rightJoinCcShowInstancesRelatedByDbId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 * @method CcShowInstancesQuery innerJoinCcShowInstancesRelatedByDbId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
 *
 * @method CcShowInstancesQuery leftJoinCcSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method CcShowInstancesQuery rightJoinCcSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method CcShowInstancesQuery innerJoinCcSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method CcShowInstancesQuery leftJoinCcPlayoutHistory($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcShowInstancesQuery rightJoinCcPlayoutHistory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlayoutHistory relation
 * @method CcShowInstancesQuery innerJoinCcPlayoutHistory($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlayoutHistory relation
 *
 * @method CcShowInstances findOne(PropelPDO $con = null) Return the first CcShowInstances matching the query
 * @method CcShowInstances findOneOrCreate(PropelPDO $con = null) Return the first CcShowInstances matching the query, or a new CcShowInstances object populated from the query conditions when no match is found
 *
 * @method CcShowInstances findOneByDbDescription(string $description) Return the first CcShowInstances filtered by the description column
 * @method CcShowInstances findOneByDbStarts(string $starts) Return the first CcShowInstances filtered by the starts column
 * @method CcShowInstances findOneByDbEnds(string $ends) Return the first CcShowInstances filtered by the ends column
 * @method CcShowInstances findOneByDbShowId(int $show_id) Return the first CcShowInstances filtered by the show_id column
 * @method CcShowInstances findOneByDbRecord(int $record) Return the first CcShowInstances filtered by the record column
 * @method CcShowInstances findOneByDbRebroadcast(int $rebroadcast) Return the first CcShowInstances filtered by the rebroadcast column
 * @method CcShowInstances findOneByDbOriginalShow(int $instance_id) Return the first CcShowInstances filtered by the instance_id column
 * @method CcShowInstances findOneByDbRecordedFile(int $file_id) Return the first CcShowInstances filtered by the file_id column
 * @method CcShowInstances findOneByDbTimeFilled(string $time_filled) Return the first CcShowInstances filtered by the time_filled column
 * @method CcShowInstances findOneByDbCreated(string $created) Return the first CcShowInstances filtered by the created column
 * @method CcShowInstances findOneByDbLastScheduled(string $last_scheduled) Return the first CcShowInstances filtered by the last_scheduled column
 * @method CcShowInstances findOneByDbModifiedInstance(boolean $modified_instance) Return the first CcShowInstances filtered by the modified_instance column
 * @method CcShowInstances findOneByDbAutoPlaylistBuilt(boolean $autoplaylist_built) Return the first CcShowInstances filtered by the autoplaylist_built column
 *
 * @method array findByDbId(int $id) Return CcShowInstances objects filtered by the id column
 * @method array findByDbDescription(string $description) Return CcShowInstances objects filtered by the description column
 * @method array findByDbStarts(string $starts) Return CcShowInstances objects filtered by the starts column
 * @method array findByDbEnds(string $ends) Return CcShowInstances objects filtered by the ends column
 * @method array findByDbShowId(int $show_id) Return CcShowInstances objects filtered by the show_id column
 * @method array findByDbRecord(int $record) Return CcShowInstances objects filtered by the record column
 * @method array findByDbRebroadcast(int $rebroadcast) Return CcShowInstances objects filtered by the rebroadcast column
 * @method array findByDbOriginalShow(int $instance_id) Return CcShowInstances objects filtered by the instance_id column
 * @method array findByDbRecordedFile(int $file_id) Return CcShowInstances objects filtered by the file_id column
 * @method array findByDbTimeFilled(string $time_filled) Return CcShowInstances objects filtered by the time_filled column
 * @method array findByDbCreated(string $created) Return CcShowInstances objects filtered by the created column
 * @method array findByDbLastScheduled(string $last_scheduled) Return CcShowInstances objects filtered by the last_scheduled column
 * @method array findByDbModifiedInstance(boolean $modified_instance) Return CcShowInstances objects filtered by the modified_instance column
 * @method array findByDbAutoPlaylistBuilt(boolean $autoplaylist_built) Return CcShowInstances objects filtered by the autoplaylist_built column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowInstancesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcShowInstancesQuery object.
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
            $modelName = 'CcShowInstances';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcShowInstancesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcShowInstancesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcShowInstancesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcShowInstancesQuery) {
            return $criteria;
        }
        $query = new CcShowInstancesQuery(null, null, $modelAlias);

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
     * @return   CcShowInstances|CcShowInstances[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcShowInstancesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcShowInstancesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcShowInstances A model object, or null if the key is not found
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
     * @return                 CcShowInstances A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "description", "starts", "ends", "show_id", "record", "rebroadcast", "instance_id", "file_id", "time_filled", "created", "last_scheduled", "modified_instance", "autoplaylist_built" FROM "cc_show_instances" WHERE "id" = :p0';
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
            $obj = new CcShowInstances();
            $obj->hydrate($row);
            CcShowInstancesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcShowInstances|CcShowInstances[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcShowInstances[]|mixed the list of results, formatted by the current formatter
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
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcShowInstancesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcShowInstancesPeer::ID, $keys, Criteria::IN);
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
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDbDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDbDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbDescription($dbDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbDescription)) {
                $dbDescription = str_replace('*', '%', $dbDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the starts column
     *
     * Example usage:
     * <code>
     * $query->filterByDbStarts('2011-03-14'); // WHERE starts = '2011-03-14'
     * $query->filterByDbStarts('now'); // WHERE starts = '2011-03-14'
     * $query->filterByDbStarts(array('max' => 'yesterday')); // WHERE starts < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbStarts The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbStarts($dbStarts = null, $comparison = null)
    {
        if (is_array($dbStarts)) {
            $useMinMax = false;
            if (isset($dbStarts['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbStarts['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::STARTS, $dbStarts, $comparison);
    }

    /**
     * Filter the query on the ends column
     *
     * Example usage:
     * <code>
     * $query->filterByDbEnds('2011-03-14'); // WHERE ends = '2011-03-14'
     * $query->filterByDbEnds('now'); // WHERE ends = '2011-03-14'
     * $query->filterByDbEnds(array('max' => 'yesterday')); // WHERE ends < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbEnds The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbEnds($dbEnds = null, $comparison = null)
    {
        if (is_array($dbEnds)) {
            $useMinMax = false;
            if (isset($dbEnds['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbEnds['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::ENDS, $dbEnds, $comparison);
    }

    /**
     * Filter the query on the show_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbShowId(1234); // WHERE show_id = 1234
     * $query->filterByDbShowId(array(12, 34)); // WHERE show_id IN (12, 34)
     * $query->filterByDbShowId(array('min' => 12)); // WHERE show_id >= 12
     * $query->filterByDbShowId(array('max' => 12)); // WHERE show_id <= 12
     * </code>
     *
     * @see       filterByCcShow()
     *
     * @param     mixed $dbShowId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbShowId($dbShowId = null, $comparison = null)
    {
        if (is_array($dbShowId)) {
            $useMinMax = false;
            if (isset($dbShowId['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbShowId['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $dbShowId, $comparison);
    }

    /**
     * Filter the query on the record column
     *
     * Example usage:
     * <code>
     * $query->filterByDbRecord(1234); // WHERE record = 1234
     * $query->filterByDbRecord(array(12, 34)); // WHERE record IN (12, 34)
     * $query->filterByDbRecord(array('min' => 12)); // WHERE record >= 12
     * $query->filterByDbRecord(array('max' => 12)); // WHERE record <= 12
     * </code>
     *
     * @param     mixed $dbRecord The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbRecord($dbRecord = null, $comparison = null)
    {
        if (is_array($dbRecord)) {
            $useMinMax = false;
            if (isset($dbRecord['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbRecord['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::RECORD, $dbRecord, $comparison);
    }

    /**
     * Filter the query on the rebroadcast column
     *
     * Example usage:
     * <code>
     * $query->filterByDbRebroadcast(1234); // WHERE rebroadcast = 1234
     * $query->filterByDbRebroadcast(array(12, 34)); // WHERE rebroadcast IN (12, 34)
     * $query->filterByDbRebroadcast(array('min' => 12)); // WHERE rebroadcast >= 12
     * $query->filterByDbRebroadcast(array('max' => 12)); // WHERE rebroadcast <= 12
     * </code>
     *
     * @param     mixed $dbRebroadcast The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbRebroadcast($dbRebroadcast = null, $comparison = null)
    {
        if (is_array($dbRebroadcast)) {
            $useMinMax = false;
            if (isset($dbRebroadcast['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbRebroadcast['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::REBROADCAST, $dbRebroadcast, $comparison);
    }

    /**
     * Filter the query on the instance_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbOriginalShow(1234); // WHERE instance_id = 1234
     * $query->filterByDbOriginalShow(array(12, 34)); // WHERE instance_id IN (12, 34)
     * $query->filterByDbOriginalShow(array('min' => 12)); // WHERE instance_id >= 12
     * $query->filterByDbOriginalShow(array('max' => 12)); // WHERE instance_id <= 12
     * </code>
     *
     * @see       filterByCcShowInstancesRelatedByDbOriginalShow()
     *
     * @param     mixed $dbOriginalShow The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbOriginalShow($dbOriginalShow = null, $comparison = null)
    {
        if (is_array($dbOriginalShow)) {
            $useMinMax = false;
            if (isset($dbOriginalShow['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbOriginalShow['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $dbOriginalShow, $comparison);
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbRecordedFile(1234); // WHERE file_id = 1234
     * $query->filterByDbRecordedFile(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByDbRecordedFile(array('min' => 12)); // WHERE file_id >= 12
     * $query->filterByDbRecordedFile(array('max' => 12)); // WHERE file_id <= 12
     * </code>
     *
     * @see       filterByCcFiles()
     *
     * @param     mixed $dbRecordedFile The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbRecordedFile($dbRecordedFile = null, $comparison = null)
    {
        if (is_array($dbRecordedFile)) {
            $useMinMax = false;
            if (isset($dbRecordedFile['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbRecordedFile['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::FILE_ID, $dbRecordedFile, $comparison);
    }

    /**
     * Filter the query on the time_filled column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTimeFilled('fooValue');   // WHERE time_filled = 'fooValue'
     * $query->filterByDbTimeFilled('%fooValue%'); // WHERE time_filled LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTimeFilled The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbTimeFilled($dbTimeFilled = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTimeFilled)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTimeFilled)) {
                $dbTimeFilled = str_replace('*', '%', $dbTimeFilled);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::TIME_FILLED, $dbTimeFilled, $comparison);
    }

    /**
     * Filter the query on the created column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCreated('2011-03-14'); // WHERE created = '2011-03-14'
     * $query->filterByDbCreated('now'); // WHERE created = '2011-03-14'
     * $query->filterByDbCreated(array('max' => 'yesterday')); // WHERE created < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbCreated The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbCreated($dbCreated = null, $comparison = null)
    {
        if (is_array($dbCreated)) {
            $useMinMax = false;
            if (isset($dbCreated['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbCreated['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::CREATED, $dbCreated, $comparison);
    }

    /**
     * Filter the query on the last_scheduled column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLastScheduled('2011-03-14'); // WHERE last_scheduled = '2011-03-14'
     * $query->filterByDbLastScheduled('now'); // WHERE last_scheduled = '2011-03-14'
     * $query->filterByDbLastScheduled(array('max' => 'yesterday')); // WHERE last_scheduled < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbLastScheduled The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbLastScheduled($dbLastScheduled = null, $comparison = null)
    {
        if (is_array($dbLastScheduled)) {
            $useMinMax = false;
            if (isset($dbLastScheduled['min'])) {
                $this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbLastScheduled['max'])) {
                $this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowInstancesPeer::LAST_SCHEDULED, $dbLastScheduled, $comparison);
    }

    /**
     * Filter the query on the modified_instance column
     *
     * Example usage:
     * <code>
     * $query->filterByDbModifiedInstance(true); // WHERE modified_instance = true
     * $query->filterByDbModifiedInstance('yes'); // WHERE modified_instance = true
     * </code>
     *
     * @param     boolean|string $dbModifiedInstance The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbModifiedInstance($dbModifiedInstance = null, $comparison = null)
    {
        if (is_string($dbModifiedInstance)) {
            $dbModifiedInstance = in_array(strtolower($dbModifiedInstance), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowInstancesPeer::MODIFIED_INSTANCE, $dbModifiedInstance, $comparison);
    }

    /**
     * Filter the query on the autoplaylist_built column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoPlaylistBuilt(true); // WHERE autoplaylist_built = true
     * $query->filterByDbAutoPlaylistBuilt('yes'); // WHERE autoplaylist_built = true
     * </code>
     *
     * @param     boolean|string $dbAutoPlaylistBuilt The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function filterByDbAutoPlaylistBuilt($dbAutoPlaylistBuilt = null, $comparison = null)
    {
        if (is_string($dbAutoPlaylistBuilt)) {
            $dbAutoPlaylistBuilt = in_array(strtolower($dbAutoPlaylistBuilt), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowInstancesPeer::AUTOPLAYLIST_BUILT, $dbAutoPlaylistBuilt, $comparison);
    }

    /**
     * Filter the query by a related CcShow object
     *
     * @param   CcShow|PropelObjectCollection $ccShow The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShow($ccShow, $comparison = null)
    {
        if ($ccShow instanceof CcShow) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $ccShow->getDbId(), $comparison);
        } elseif ($ccShow instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowInstancesPeer::SHOW_ID, $ccShow->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcShow() only accepts arguments of type CcShow or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShow relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcShow($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShow');

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
            $this->addJoinObject($join, 'CcShow');
        }

        return $this;
    }

    /**
     * Use the CcShow relation CcShow object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowQuery A secondary query class using the current class as primary query
     */
    public function useCcShowQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShow($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShow', 'CcShowQuery');
    }

    /**
     * Filter the query by a related CcShowInstances object
     *
     * @param   CcShowInstances|PropelObjectCollection $ccShowInstances The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowInstancesRelatedByDbOriginalShow($ccShowInstances, $comparison = null)
    {
        if ($ccShowInstances instanceof CcShowInstances) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $ccShowInstances->getDbId(), $comparison);
        } elseif ($ccShowInstances instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowInstancesPeer::INSTANCE_ID, $ccShowInstances->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcShowInstancesRelatedByDbOriginalShow() only accepts arguments of type CcShowInstances or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowInstancesRelatedByDbOriginalShow relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcShowInstancesRelatedByDbOriginalShow($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowInstancesRelatedByDbOriginalShow');

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
            $this->addJoinObject($join, 'CcShowInstancesRelatedByDbOriginalShow');
        }

        return $this;
    }

    /**
     * Use the CcShowInstancesRelatedByDbOriginalShow relation CcShowInstances object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowInstancesQuery A secondary query class using the current class as primary query
     */
    public function useCcShowInstancesRelatedByDbOriginalShowQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowInstancesRelatedByDbOriginalShow($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowInstancesRelatedByDbOriginalShow', 'CcShowInstancesQuery');
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowInstancesPeer::FILE_ID, $ccFiles->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcFiles() only accepts arguments of type CcFiles or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcFiles relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcFiles($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcFiles');

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
            $this->addJoinObject($join, 'CcFiles');
        }

        return $this;
    }

    /**
     * Use the CcFiles relation CcFiles object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcFilesQuery A secondary query class using the current class as primary query
     */
    public function useCcFilesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcFiles($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
    }

    /**
     * Filter the query by a related CcShowInstances object
     *
     * @param   CcShowInstances|PropelObjectCollection $ccShowInstances  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowInstancesRelatedByDbId($ccShowInstances, $comparison = null)
    {
        if ($ccShowInstances instanceof CcShowInstances) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::ID, $ccShowInstances->getDbOriginalShow(), $comparison);
        } elseif ($ccShowInstances instanceof PropelObjectCollection) {
            return $this
                ->useCcShowInstancesRelatedByDbIdQuery()
                ->filterByPrimaryKeys($ccShowInstances->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowInstancesRelatedByDbId() only accepts arguments of type CcShowInstances or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowInstancesRelatedByDbId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcShowInstancesRelatedByDbId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowInstancesRelatedByDbId');

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
            $this->addJoinObject($join, 'CcShowInstancesRelatedByDbId');
        }

        return $this;
    }

    /**
     * Use the CcShowInstancesRelatedByDbId relation CcShowInstances object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowInstancesQuery A secondary query class using the current class as primary query
     */
    public function useCcShowInstancesRelatedByDbIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowInstancesRelatedByDbId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowInstancesRelatedByDbId', 'CcShowInstancesQuery');
    }

    /**
     * Filter the query by a related CcSchedule object
     *
     * @param   CcSchedule|PropelObjectCollection $ccSchedule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSchedule($ccSchedule, $comparison = null)
    {
        if ($ccSchedule instanceof CcSchedule) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::ID, $ccSchedule->getDbInstanceId(), $comparison);
        } elseif ($ccSchedule instanceof PropelObjectCollection) {
            return $this
                ->useCcScheduleQuery()
                ->filterByPrimaryKeys($ccSchedule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcSchedule() only accepts arguments of type CcSchedule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSchedule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcSchedule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcSchedule');

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
            $this->addJoinObject($join, 'CcSchedule');
        }

        return $this;
    }

    /**
     * Use the CcSchedule relation CcSchedule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcScheduleQuery A secondary query class using the current class as primary query
     */
    public function useCcScheduleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSchedule', 'CcScheduleQuery');
    }

    /**
     * Filter the query by a related CcPlayoutHistory object
     *
     * @param   CcPlayoutHistory|PropelObjectCollection $ccPlayoutHistory  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowInstancesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlayoutHistory($ccPlayoutHistory, $comparison = null)
    {
        if ($ccPlayoutHistory instanceof CcPlayoutHistory) {
            return $this
                ->addUsingAlias(CcShowInstancesPeer::ID, $ccPlayoutHistory->getDbInstanceId(), $comparison);
        } elseif ($ccPlayoutHistory instanceof PropelObjectCollection) {
            return $this
                ->useCcPlayoutHistoryQuery()
                ->filterByPrimaryKeys($ccPlayoutHistory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcPlayoutHistory() only accepts arguments of type CcPlayoutHistory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlayoutHistory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function joinCcPlayoutHistory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlayoutHistory');

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
            $this->addJoinObject($join, 'CcPlayoutHistory');
        }

        return $this;
    }

    /**
     * Use the CcPlayoutHistory relation CcPlayoutHistory object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlayoutHistoryQuery A secondary query class using the current class as primary query
     */
    public function useCcPlayoutHistoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlayoutHistory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistory', 'CcPlayoutHistoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcShowInstances $ccShowInstances Object to remove from the list of results
     *
     * @return CcShowInstancesQuery The current query, for fluid interface
     */
    public function prune($ccShowInstances = null)
    {
        if ($ccShowInstances) {
            $this->addUsingAlias(CcShowInstancesPeer::ID, $ccShowInstances->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
