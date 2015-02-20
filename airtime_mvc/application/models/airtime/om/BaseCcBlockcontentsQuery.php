<?php


/**
 * Base class that represents a query for the 'cc_blockcontents' table.
 *
 *
 *
 * @method CcBlockcontentsQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcBlockcontentsQuery orderByDbBlockId($order = Criteria::ASC) Order by the block_id column
 * @method CcBlockcontentsQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method CcBlockcontentsQuery orderByDbPosition($order = Criteria::ASC) Order by the position column
 * @method CcBlockcontentsQuery orderByDbTrackOffset($order = Criteria::ASC) Order by the trackoffset column
 * @method CcBlockcontentsQuery orderByDbCliplength($order = Criteria::ASC) Order by the cliplength column
 * @method CcBlockcontentsQuery orderByDbCuein($order = Criteria::ASC) Order by the cuein column
 * @method CcBlockcontentsQuery orderByDbCueout($order = Criteria::ASC) Order by the cueout column
 * @method CcBlockcontentsQuery orderByDbFadein($order = Criteria::ASC) Order by the fadein column
 * @method CcBlockcontentsQuery orderByDbFadeout($order = Criteria::ASC) Order by the fadeout column
 *
 * @method CcBlockcontentsQuery groupByDbId() Group by the id column
 * @method CcBlockcontentsQuery groupByDbBlockId() Group by the block_id column
 * @method CcBlockcontentsQuery groupByDbFileId() Group by the file_id column
 * @method CcBlockcontentsQuery groupByDbPosition() Group by the position column
 * @method CcBlockcontentsQuery groupByDbTrackOffset() Group by the trackoffset column
 * @method CcBlockcontentsQuery groupByDbCliplength() Group by the cliplength column
 * @method CcBlockcontentsQuery groupByDbCuein() Group by the cuein column
 * @method CcBlockcontentsQuery groupByDbCueout() Group by the cueout column
 * @method CcBlockcontentsQuery groupByDbFadein() Group by the fadein column
 * @method CcBlockcontentsQuery groupByDbFadeout() Group by the fadeout column
 *
 * @method CcBlockcontentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcBlockcontentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcBlockcontentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcBlockcontentsQuery leftJoinCcFiles($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method CcBlockcontentsQuery rightJoinCcFiles($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method CcBlockcontentsQuery innerJoinCcFiles($relationAlias = null) Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method CcBlockcontentsQuery leftJoinCcBlock($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcBlock relation
 * @method CcBlockcontentsQuery rightJoinCcBlock($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcBlock relation
 * @method CcBlockcontentsQuery innerJoinCcBlock($relationAlias = null) Adds a INNER JOIN clause to the query using the CcBlock relation
 *
 * @method CcBlockcontents findOne(PropelPDO $con = null) Return the first CcBlockcontents matching the query
 * @method CcBlockcontents findOneOrCreate(PropelPDO $con = null) Return the first CcBlockcontents matching the query, or a new CcBlockcontents object populated from the query conditions when no match is found
 *
 * @method CcBlockcontents findOneByDbBlockId(int $block_id) Return the first CcBlockcontents filtered by the block_id column
 * @method CcBlockcontents findOneByDbFileId(int $file_id) Return the first CcBlockcontents filtered by the file_id column
 * @method CcBlockcontents findOneByDbPosition(int $position) Return the first CcBlockcontents filtered by the position column
 * @method CcBlockcontents findOneByDbTrackOffset(double $trackoffset) Return the first CcBlockcontents filtered by the trackoffset column
 * @method CcBlockcontents findOneByDbCliplength(string $cliplength) Return the first CcBlockcontents filtered by the cliplength column
 * @method CcBlockcontents findOneByDbCuein(string $cuein) Return the first CcBlockcontents filtered by the cuein column
 * @method CcBlockcontents findOneByDbCueout(string $cueout) Return the first CcBlockcontents filtered by the cueout column
 * @method CcBlockcontents findOneByDbFadein(string $fadein) Return the first CcBlockcontents filtered by the fadein column
 * @method CcBlockcontents findOneByDbFadeout(string $fadeout) Return the first CcBlockcontents filtered by the fadeout column
 *
 * @method array findByDbId(int $id) Return CcBlockcontents objects filtered by the id column
 * @method array findByDbBlockId(int $block_id) Return CcBlockcontents objects filtered by the block_id column
 * @method array findByDbFileId(int $file_id) Return CcBlockcontents objects filtered by the file_id column
 * @method array findByDbPosition(int $position) Return CcBlockcontents objects filtered by the position column
 * @method array findByDbTrackOffset(double $trackoffset) Return CcBlockcontents objects filtered by the trackoffset column
 * @method array findByDbCliplength(string $cliplength) Return CcBlockcontents objects filtered by the cliplength column
 * @method array findByDbCuein(string $cuein) Return CcBlockcontents objects filtered by the cuein column
 * @method array findByDbCueout(string $cueout) Return CcBlockcontents objects filtered by the cueout column
 * @method array findByDbFadein(string $fadein) Return CcBlockcontents objects filtered by the fadein column
 * @method array findByDbFadeout(string $fadeout) Return CcBlockcontents objects filtered by the fadeout column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcBlockcontentsQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcBlockcontentsQuery object.
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
            $modelName = 'CcBlockcontents';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcBlockcontentsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcBlockcontentsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcBlockcontentsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcBlockcontentsQuery) {
            return $criteria;
        }
        $query = new CcBlockcontentsQuery(null, null, $modelAlias);

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
     * @return   CcBlockcontents|CcBlockcontents[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcBlockcontentsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcBlockcontentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcBlockcontents A model object, or null if the key is not found
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
     * @return                 CcBlockcontents A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "block_id", "file_id", "position", "trackoffset", "cliplength", "cuein", "cueout", "fadein", "fadeout" FROM "cc_blockcontents" WHERE "id" = :p0';
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
            $obj = new CcBlockcontents();
            $obj->hydrate($row);
            CcBlockcontentsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcBlockcontents|CcBlockcontents[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcBlockcontents[]|mixed the list of results, formatted by the current formatter
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
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcBlockcontentsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcBlockcontentsPeer::ID, $keys, Criteria::IN);
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
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the block_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBlockId(1234); // WHERE block_id = 1234
     * $query->filterByDbBlockId(array(12, 34)); // WHERE block_id IN (12, 34)
     * $query->filterByDbBlockId(array('min' => 12)); // WHERE block_id >= 12
     * $query->filterByDbBlockId(array('max' => 12)); // WHERE block_id <= 12
     * </code>
     *
     * @see       filterByCcBlock()
     *
     * @param     mixed $dbBlockId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbBlockId($dbBlockId = null, $comparison = null)
    {
        if (is_array($dbBlockId)) {
            $useMinMax = false;
            if (isset($dbBlockId['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbBlockId['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $dbBlockId, $comparison);
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFileId(1234); // WHERE file_id = 1234
     * $query->filterByDbFileId(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByDbFileId(array('min' => 12)); // WHERE file_id >= 12
     * $query->filterByDbFileId(array('max' => 12)); // WHERE file_id <= 12
     * </code>
     *
     * @see       filterByCcFiles()
     *
     * @param     mixed $dbFileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbFileId($dbFileId = null, $comparison = null)
    {
        if (is_array($dbFileId)) {
            $useMinMax = false;
            if (isset($dbFileId['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFileId['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $dbFileId, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByDbPosition(1234); // WHERE position = 1234
     * $query->filterByDbPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByDbPosition(array('min' => 12)); // WHERE position >= 12
     * $query->filterByDbPosition(array('max' => 12)); // WHERE position <= 12
     * </code>
     *
     * @param     mixed $dbPosition The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbPosition($dbPosition = null, $comparison = null)
    {
        if (is_array($dbPosition)) {
            $useMinMax = false;
            if (isset($dbPosition['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbPosition['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::POSITION, $dbPosition, $comparison);
    }

    /**
     * Filter the query on the trackoffset column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTrackOffset(1234); // WHERE trackoffset = 1234
     * $query->filterByDbTrackOffset(array(12, 34)); // WHERE trackoffset IN (12, 34)
     * $query->filterByDbTrackOffset(array('min' => 12)); // WHERE trackoffset >= 12
     * $query->filterByDbTrackOffset(array('max' => 12)); // WHERE trackoffset <= 12
     * </code>
     *
     * @param     mixed $dbTrackOffset The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbTrackOffset($dbTrackOffset = null, $comparison = null)
    {
        if (is_array($dbTrackOffset)) {
            $useMinMax = false;
            if (isset($dbTrackOffset['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbTrackOffset['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::TRACKOFFSET, $dbTrackOffset, $comparison);
    }

    /**
     * Filter the query on the cliplength column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCliplength('fooValue');   // WHERE cliplength = 'fooValue'
     * $query->filterByDbCliplength('%fooValue%'); // WHERE cliplength LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCliplength The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbCliplength($dbCliplength = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCliplength)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCliplength)) {
                $dbCliplength = str_replace('*', '%', $dbCliplength);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::CLIPLENGTH, $dbCliplength, $comparison);
    }

    /**
     * Filter the query on the cuein column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCuein('fooValue');   // WHERE cuein = 'fooValue'
     * $query->filterByDbCuein('%fooValue%'); // WHERE cuein LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCuein The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbCuein($dbCuein = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCuein)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCuein)) {
                $dbCuein = str_replace('*', '%', $dbCuein);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::CUEIN, $dbCuein, $comparison);
    }

    /**
     * Filter the query on the cueout column
     *
     * Example usage:
     * <code>
     * $query->filterByDbCueout('fooValue');   // WHERE cueout = 'fooValue'
     * $query->filterByDbCueout('%fooValue%'); // WHERE cueout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbCueout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbCueout($dbCueout = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbCueout)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbCueout)) {
                $dbCueout = str_replace('*', '%', $dbCueout);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::CUEOUT, $dbCueout, $comparison);
    }

    /**
     * Filter the query on the fadein column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFadein('2011-03-14'); // WHERE fadein = '2011-03-14'
     * $query->filterByDbFadein('now'); // WHERE fadein = '2011-03-14'
     * $query->filterByDbFadein(array('max' => 'yesterday')); // WHERE fadein < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbFadein The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbFadein($dbFadein = null, $comparison = null)
    {
        if (is_array($dbFadein)) {
            $useMinMax = false;
            if (isset($dbFadein['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFadein['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::FADEIN, $dbFadein, $comparison);
    }

    /**
     * Filter the query on the fadeout column
     *
     * Example usage:
     * <code>
     * $query->filterByDbFadeout('2011-03-14'); // WHERE fadeout = '2011-03-14'
     * $query->filterByDbFadeout('now'); // WHERE fadeout = '2011-03-14'
     * $query->filterByDbFadeout(array('max' => 'yesterday')); // WHERE fadeout < '2011-03-13'
     * </code>
     *
     * @param     mixed $dbFadeout The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function filterByDbFadeout($dbFadeout = null, $comparison = null)
    {
        if (is_array($dbFadeout)) {
            $useMinMax = false;
            if (isset($dbFadeout['min'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbFadeout['max'])) {
                $this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcBlockcontentsPeer::FADEOUT, $dbFadeout, $comparison);
    }

    /**
     * Filter the query by a related CcFiles object
     *
     * @param   CcFiles|PropelObjectCollection $ccFiles The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockcontentsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcFiles($ccFiles, $comparison = null)
    {
        if ($ccFiles instanceof CcFiles) {
            return $this
                ->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
        } elseif ($ccFiles instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcBlockcontentsPeer::FILE_ID, $ccFiles->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return CcBlockcontentsQuery The current query, for fluid interface
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
     * Filter the query by a related CcBlock object
     *
     * @param   CcBlock|PropelObjectCollection $ccBlock The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcBlockcontentsQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcBlock($ccBlock, $comparison = null)
    {
        if ($ccBlock instanceof CcBlock) {
            return $this
                ->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $ccBlock->getDbId(), $comparison);
        } elseif ($ccBlock instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcBlockcontentsPeer::BLOCK_ID, $ccBlock->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcBlock() only accepts arguments of type CcBlock or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcBlock relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function joinCcBlock($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcBlock');

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
            $this->addJoinObject($join, 'CcBlock');
        }

        return $this;
    }

    /**
     * Use the CcBlock relation CcBlock object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcBlockQuery A secondary query class using the current class as primary query
     */
    public function useCcBlockQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcBlock($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcBlock', 'CcBlockQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcBlockcontents $ccBlockcontents Object to remove from the list of results
     *
     * @return CcBlockcontentsQuery The current query, for fluid interface
     */
    public function prune($ccBlockcontents = null)
    {
        if ($ccBlockcontents) {
            $this->addUsingAlias(CcBlockcontentsPeer::ID, $ccBlockcontents->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Code to execute before every DELETE statement
     *
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePreDelete(PropelPDO $con)
    {
        // aggregate_column_relation behavior
        $this->findRelatedCcBlocks($con);

        return $this->preDelete($con);
    }

    /**
     * Code to execute after every DELETE statement
     *
     * @param     int $affectedRows the number of deleted rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostDelete($affectedRows, PropelPDO $con)
    {
        // aggregate_column_relation behavior
        $this->updateRelatedCcBlocks($con);

        return $this->postDelete($affectedRows, $con);
    }

    /**
     * Code to execute before every UPDATE statement
     *
     * @param     array $values The associative array of columns and values for the update
     * @param     PropelPDO $con The connection object used by the query
     * @param     boolean $forceIndividualSaves If false (default), the resulting call is a BasePeer::doUpdate(), otherwise it is a series of save() calls on all the found objects
     */
    protected function basePreUpdate(&$values, PropelPDO $con, $forceIndividualSaves = false)
    {
        // aggregate_column_relation behavior
        $this->findRelatedCcBlocks($con);

        return $this->preUpdate($values, $con, $forceIndividualSaves);
    }

    /**
     * Code to execute after every UPDATE statement
     *
     * @param     int $affectedRows the number of updated rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostUpdate($affectedRows, PropelPDO $con)
    {
        // aggregate_column_relation behavior
        $this->updateRelatedCcBlocks($con);

        return $this->postUpdate($affectedRows, $con);
    }

    // aggregate_column_relation behavior

    /**
     * Finds the related CcBlock objects and keep them for later
     *
     * @param PropelPDO $con A connection object
     */
    protected function findRelatedCcBlocks($con)
    {
        $criteria = clone $this;
        if ($this->useAliasInSQL) {
            $alias = $this->getModelAlias();
            $criteria->removeAlias($alias);
        } else {
            $alias = '';
        }
        $this->ccBlocks = CcBlockQuery::create()
            ->joinCcBlockcontents($alias)
            ->mergeWith($criteria)
            ->find($con);
    }

    protected function updateRelatedCcBlocks($con)
    {
        foreach ($this->ccBlocks as $ccBlock) {
            $ccBlock->updateDbLength($con);
        }
        $this->ccBlocks = array();
    }

}
