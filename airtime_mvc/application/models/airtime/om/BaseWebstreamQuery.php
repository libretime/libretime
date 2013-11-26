<?php

namespace Airtime\MediaItem\om;

use \Criteria;
use \Exception;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Airtime\CcSubjs;
use Airtime\MediaItem;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\Webstream;
use Airtime\MediaItem\WebstreamPeer;
use Airtime\MediaItem\WebstreamQuery;

/**
 * Base class that represents a query for the 'media_webstream' table.
 *
 *
 *
 * @method WebstreamQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method WebstreamQuery orderById($order = Criteria::ASC) Order by the id column
 * @method WebstreamQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method WebstreamQuery orderByOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method WebstreamQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method WebstreamQuery orderByLastPlayedTime($order = Criteria::ASC) Order by the last_played column
 * @method WebstreamQuery orderByPlayCount($order = Criteria::ASC) Order by the play_count column
 * @method WebstreamQuery orderByLength($order = Criteria::ASC) Order by the length column
 * @method WebstreamQuery orderByMime($order = Criteria::ASC) Order by the mime column
 * @method WebstreamQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method WebstreamQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method WebstreamQuery groupByUrl() Group by the url column
 * @method WebstreamQuery groupById() Group by the id column
 * @method WebstreamQuery groupByName() Group by the name column
 * @method WebstreamQuery groupByOwnerId() Group by the owner_id column
 * @method WebstreamQuery groupByDescription() Group by the description column
 * @method WebstreamQuery groupByLastPlayedTime() Group by the last_played column
 * @method WebstreamQuery groupByPlayCount() Group by the play_count column
 * @method WebstreamQuery groupByLength() Group by the length column
 * @method WebstreamQuery groupByMime() Group by the mime column
 * @method WebstreamQuery groupByCreatedAt() Group by the created_at column
 * @method WebstreamQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method WebstreamQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method WebstreamQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method WebstreamQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method WebstreamQuery leftJoinMediaItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the MediaItem relation
 * @method WebstreamQuery rightJoinMediaItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MediaItem relation
 * @method WebstreamQuery innerJoinMediaItem($relationAlias = null) Adds a INNER JOIN clause to the query using the MediaItem relation
 *
 * @method WebstreamQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method WebstreamQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method WebstreamQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method Webstream findOne(PropelPDO $con = null) Return the first Webstream matching the query
 * @method Webstream findOneOrCreate(PropelPDO $con = null) Return the first Webstream matching the query, or a new Webstream object populated from the query conditions when no match is found
 *
 * @method Webstream findOneByUrl(string $url) Return the first Webstream filtered by the url column
 * @method Webstream findOneByName(string $name) Return the first Webstream filtered by the name column
 * @method Webstream findOneByOwnerId(int $owner_id) Return the first Webstream filtered by the owner_id column
 * @method Webstream findOneByDescription(string $description) Return the first Webstream filtered by the description column
 * @method Webstream findOneByLastPlayedTime(string $last_played) Return the first Webstream filtered by the last_played column
 * @method Webstream findOneByPlayCount(int $play_count) Return the first Webstream filtered by the play_count column
 * @method Webstream findOneByLength(string $length) Return the first Webstream filtered by the length column
 * @method Webstream findOneByMime(string $mime) Return the first Webstream filtered by the mime column
 * @method Webstream findOneByCreatedAt(string $created_at) Return the first Webstream filtered by the created_at column
 * @method Webstream findOneByUpdatedAt(string $updated_at) Return the first Webstream filtered by the updated_at column
 *
 * @method array findByUrl(string $url) Return Webstream objects filtered by the url column
 * @method array findById(int $id) Return Webstream objects filtered by the id column
 * @method array findByName(string $name) Return Webstream objects filtered by the name column
 * @method array findByOwnerId(int $owner_id) Return Webstream objects filtered by the owner_id column
 * @method array findByDescription(string $description) Return Webstream objects filtered by the description column
 * @method array findByLastPlayedTime(string $last_played) Return Webstream objects filtered by the last_played column
 * @method array findByPlayCount(int $play_count) Return Webstream objects filtered by the play_count column
 * @method array findByLength(string $length) Return Webstream objects filtered by the length column
 * @method array findByMime(string $mime) Return Webstream objects filtered by the mime column
 * @method array findByCreatedAt(string $created_at) Return Webstream objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Webstream objects filtered by the updated_at column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseWebstreamQuery extends MediaItemQuery
{
    /**
     * Initializes internal state of BaseWebstreamQuery object.
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
            $modelName = 'Airtime\\MediaItem\\Webstream';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new WebstreamQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   WebstreamQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return WebstreamQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof WebstreamQuery) {
            return $criteria;
        }
        $query = new WebstreamQuery(null, null, $modelAlias);

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
     * @return   Webstream|Webstream[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = WebstreamPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(WebstreamPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Webstream A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
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
     * @return                 Webstream A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "url", "id", "name", "owner_id", "description", "last_played", "play_count", "length", "mime", "created_at", "updated_at" FROM "media_webstream" WHERE "id" = :p0';
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
            $obj = new Webstream();
            $obj->hydrate($row);
            WebstreamPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Webstream|Webstream[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Webstream[]|mixed the list of results, formatted by the current formatter
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
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(WebstreamPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(WebstreamPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::URL, $url, $comparison);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @see       filterByMediaItem()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(WebstreamPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(WebstreamPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the owner_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOwnerId(1234); // WHERE owner_id = 1234
     * $query->filterByOwnerId(array(12, 34)); // WHERE owner_id IN (12, 34)
     * $query->filterByOwnerId(array('min' => 12)); // WHERE owner_id >= 12
     * $query->filterByOwnerId(array('max' => 12)); // WHERE owner_id <= 12
     * </code>
     *
     * @see       filterByCcSubjs()
     *
     * @param     mixed $ownerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByOwnerId($ownerId = null, $comparison = null)
    {
        if (is_array($ownerId)) {
            $useMinMax = false;
            if (isset($ownerId['min'])) {
                $this->addUsingAlias(WebstreamPeer::OWNER_ID, $ownerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ownerId['max'])) {
                $this->addUsingAlias(WebstreamPeer::OWNER_ID, $ownerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::OWNER_ID, $ownerId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the last_played column
     *
     * Example usage:
     * <code>
     * $query->filterByLastPlayedTime('2011-03-14'); // WHERE last_played = '2011-03-14'
     * $query->filterByLastPlayedTime('now'); // WHERE last_played = '2011-03-14'
     * $query->filterByLastPlayedTime(array('max' => 'yesterday')); // WHERE last_played < '2011-03-13'
     * </code>
     *
     * @param     mixed $lastPlayedTime The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByLastPlayedTime($lastPlayedTime = null, $comparison = null)
    {
        if (is_array($lastPlayedTime)) {
            $useMinMax = false;
            if (isset($lastPlayedTime['min'])) {
                $this->addUsingAlias(WebstreamPeer::LAST_PLAYED, $lastPlayedTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastPlayedTime['max'])) {
                $this->addUsingAlias(WebstreamPeer::LAST_PLAYED, $lastPlayedTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::LAST_PLAYED, $lastPlayedTime, $comparison);
    }

    /**
     * Filter the query on the play_count column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayCount(1234); // WHERE play_count = 1234
     * $query->filterByPlayCount(array(12, 34)); // WHERE play_count IN (12, 34)
     * $query->filterByPlayCount(array('min' => 12)); // WHERE play_count >= 12
     * $query->filterByPlayCount(array('max' => 12)); // WHERE play_count <= 12
     * </code>
     *
     * @param     mixed $playCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByPlayCount($playCount = null, $comparison = null)
    {
        if (is_array($playCount)) {
            $useMinMax = false;
            if (isset($playCount['min'])) {
                $this->addUsingAlias(WebstreamPeer::PLAY_COUNT, $playCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playCount['max'])) {
                $this->addUsingAlias(WebstreamPeer::PLAY_COUNT, $playCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::PLAY_COUNT, $playCount, $comparison);
    }

    /**
     * Filter the query on the length column
     *
     * Example usage:
     * <code>
     * $query->filterByLength('fooValue');   // WHERE length = 'fooValue'
     * $query->filterByLength('%fooValue%'); // WHERE length LIKE '%fooValue%'
     * </code>
     *
     * @param     string $length The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByLength($length = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($length)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $length)) {
                $length = str_replace('*', '%', $length);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::LENGTH, $length, $comparison);
    }

    /**
     * Filter the query on the mime column
     *
     * Example usage:
     * <code>
     * $query->filterByMime('fooValue');   // WHERE mime = 'fooValue'
     * $query->filterByMime('%fooValue%'); // WHERE mime LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mime The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByMime($mime = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mime)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mime)) {
                $mime = str_replace('*', '%', $mime);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::MIME, $mime, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(WebstreamPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(WebstreamPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(WebstreamPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(WebstreamPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WebstreamPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related MediaItem object
     *
     * @param   MediaItem|PropelObjectCollection $mediaItem The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WebstreamQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMediaItem($mediaItem, $comparison = null)
    {
        if ($mediaItem instanceof MediaItem) {
            return $this
                ->addUsingAlias(WebstreamPeer::ID, $mediaItem->getId(), $comparison);
        } elseif ($mediaItem instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WebstreamPeer::ID, $mediaItem->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMediaItem() only accepts arguments of type MediaItem or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MediaItem relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function joinMediaItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MediaItem');

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
            $this->addJoinObject($join, 'MediaItem');
        }

        return $this;
    }

    /**
     * Use the MediaItem relation MediaItem object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItemQuery A secondary query class using the current class as primary query
     */
    public function useMediaItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMediaItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MediaItem', '\Airtime\MediaItemQuery');
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WebstreamQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(WebstreamPeer::OWNER_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WebstreamPeer::OWNER_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcSubjs() only accepts arguments of type CcSubjs or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcSubjs relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function joinCcSubjs($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcSubjs');

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
            $this->addJoinObject($join, 'CcSubjs');
        }

        return $this;
    }

    /**
     * Use the CcSubjs relation CcSubjs object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\CcSubjsQuery A secondary query class using the current class as primary query
     */
    public function useCcSubjsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSubjs($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSubjs', '\Airtime\CcSubjsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Webstream $webstream Object to remove from the list of results
     *
     * @return WebstreamQuery The current query, for fluid interface
     */
    public function prune($webstream = null)
    {
        if ($webstream) {
            $this->addUsingAlias(WebstreamPeer::ID, $webstream->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(WebstreamPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(WebstreamPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(WebstreamPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(WebstreamPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(WebstreamPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     WebstreamQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(WebstreamPeer::CREATED_AT);
    }
}
