<?php

namespace Airtime\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Airtime\CcSchedule;
use Airtime\CcShowInstances;
use Airtime\CcSubjs;
use Airtime\MediaItem;
use Airtime\MediaItemPeer;
use Airtime\MediaItemQuery;
use Airtime\MediaItem\AudioFile;
use Airtime\MediaItem\MediaContent;
use Airtime\MediaItem\Playlist;
use Airtime\MediaItem\PlaylistRule;
use Airtime\MediaItem\Webstream;

/**
 * Base class that represents a query for the 'media_item' table.
 *
 *
 *
 * @method MediaItemQuery orderById($order = Criteria::ASC) Order by the id column
 * @method MediaItemQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method MediaItemQuery orderByOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method MediaItemQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method MediaItemQuery orderByLastPlayedTime($order = Criteria::ASC) Order by the last_played column
 * @method MediaItemQuery orderByPlayCount($order = Criteria::ASC) Order by the play_count column
 * @method MediaItemQuery orderByLength($order = Criteria::ASC) Order by the length column
 * @method MediaItemQuery orderByMime($order = Criteria::ASC) Order by the mime column
 * @method MediaItemQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method MediaItemQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method MediaItemQuery orderByDescendantClass($order = Criteria::ASC) Order by the descendant_class column
 *
 * @method MediaItemQuery groupById() Group by the id column
 * @method MediaItemQuery groupByName() Group by the name column
 * @method MediaItemQuery groupByOwnerId() Group by the owner_id column
 * @method MediaItemQuery groupByDescription() Group by the description column
 * @method MediaItemQuery groupByLastPlayedTime() Group by the last_played column
 * @method MediaItemQuery groupByPlayCount() Group by the play_count column
 * @method MediaItemQuery groupByLength() Group by the length column
 * @method MediaItemQuery groupByMime() Group by the mime column
 * @method MediaItemQuery groupByCreatedAt() Group by the created_at column
 * @method MediaItemQuery groupByUpdatedAt() Group by the updated_at column
 * @method MediaItemQuery groupByDescendantClass() Group by the descendant_class column
 *
 * @method MediaItemQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method MediaItemQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method MediaItemQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method MediaItemQuery leftJoinCcSubjs($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSubjs relation
 * @method MediaItemQuery rightJoinCcSubjs($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSubjs relation
 * @method MediaItemQuery innerJoinCcSubjs($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSubjs relation
 *
 * @method MediaItemQuery leftJoinCcShowInstances($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method MediaItemQuery rightJoinCcShowInstances($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method MediaItemQuery innerJoinCcShowInstances($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method MediaItemQuery leftJoinCcSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcSchedule relation
 * @method MediaItemQuery rightJoinCcSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcSchedule relation
 * @method MediaItemQuery innerJoinCcSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the CcSchedule relation
 *
 * @method MediaItemQuery leftJoinPlaylistRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlaylistRule relation
 * @method MediaItemQuery rightJoinPlaylistRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlaylistRule relation
 * @method MediaItemQuery innerJoinPlaylistRule($relationAlias = null) Adds a INNER JOIN clause to the query using the PlaylistRule relation
 *
 * @method MediaItemQuery leftJoinMediaContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the MediaContent relation
 * @method MediaItemQuery rightJoinMediaContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MediaContent relation
 * @method MediaItemQuery innerJoinMediaContent($relationAlias = null) Adds a INNER JOIN clause to the query using the MediaContent relation
 *
 * @method MediaItemQuery leftJoinAudioFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the AudioFile relation
 * @method MediaItemQuery rightJoinAudioFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AudioFile relation
 * @method MediaItemQuery innerJoinAudioFile($relationAlias = null) Adds a INNER JOIN clause to the query using the AudioFile relation
 *
 * @method MediaItemQuery leftJoinWebstream($relationAlias = null) Adds a LEFT JOIN clause to the query using the Webstream relation
 * @method MediaItemQuery rightJoinWebstream($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Webstream relation
 * @method MediaItemQuery innerJoinWebstream($relationAlias = null) Adds a INNER JOIN clause to the query using the Webstream relation
 *
 * @method MediaItemQuery leftJoinPlaylist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Playlist relation
 * @method MediaItemQuery rightJoinPlaylist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Playlist relation
 * @method MediaItemQuery innerJoinPlaylist($relationAlias = null) Adds a INNER JOIN clause to the query using the Playlist relation
 *
 * @method MediaItem findOne(PropelPDO $con = null) Return the first MediaItem matching the query
 * @method MediaItem findOneOrCreate(PropelPDO $con = null) Return the first MediaItem matching the query, or a new MediaItem object populated from the query conditions when no match is found
 *
 * @method MediaItem findOneByName(string $name) Return the first MediaItem filtered by the name column
 * @method MediaItem findOneByOwnerId(int $owner_id) Return the first MediaItem filtered by the owner_id column
 * @method MediaItem findOneByDescription(string $description) Return the first MediaItem filtered by the description column
 * @method MediaItem findOneByLastPlayedTime(string $last_played) Return the first MediaItem filtered by the last_played column
 * @method MediaItem findOneByPlayCount(int $play_count) Return the first MediaItem filtered by the play_count column
 * @method MediaItem findOneByLength(string $length) Return the first MediaItem filtered by the length column
 * @method MediaItem findOneByMime(string $mime) Return the first MediaItem filtered by the mime column
 * @method MediaItem findOneByCreatedAt(string $created_at) Return the first MediaItem filtered by the created_at column
 * @method MediaItem findOneByUpdatedAt(string $updated_at) Return the first MediaItem filtered by the updated_at column
 * @method MediaItem findOneByDescendantClass(string $descendant_class) Return the first MediaItem filtered by the descendant_class column
 *
 * @method array findById(int $id) Return MediaItem objects filtered by the id column
 * @method array findByName(string $name) Return MediaItem objects filtered by the name column
 * @method array findByOwnerId(int $owner_id) Return MediaItem objects filtered by the owner_id column
 * @method array findByDescription(string $description) Return MediaItem objects filtered by the description column
 * @method array findByLastPlayedTime(string $last_played) Return MediaItem objects filtered by the last_played column
 * @method array findByPlayCount(int $play_count) Return MediaItem objects filtered by the play_count column
 * @method array findByLength(string $length) Return MediaItem objects filtered by the length column
 * @method array findByMime(string $mime) Return MediaItem objects filtered by the mime column
 * @method array findByCreatedAt(string $created_at) Return MediaItem objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return MediaItem objects filtered by the updated_at column
 * @method array findByDescendantClass(string $descendant_class) Return MediaItem objects filtered by the descendant_class column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseMediaItemQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseMediaItemQuery object.
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
            $modelName = 'Airtime\\MediaItem';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new MediaItemQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   MediaItemQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return MediaItemQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof MediaItemQuery) {
            return $criteria;
        }
        $query = new MediaItemQuery(null, null, $modelAlias);

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
     * @return   MediaItem|MediaItem[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MediaItemPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(MediaItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 MediaItem A model object, or null if the key is not found
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
     * @return                 MediaItem A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "owner_id", "description", "last_played", "play_count", "length", "mime", "created_at", "updated_at", "descendant_class" FROM "media_item" WHERE "id" = :p0';
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
            $obj = new MediaItem();
            $obj->hydrate($row);
            MediaItemPeer::addInstanceToPool($obj, (string) $key);
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
     * @return MediaItem|MediaItem[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|MediaItem[]|mixed the list of results, formatted by the current formatter
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MediaItemPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MediaItemPeer::ID, $keys, Criteria::IN);
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
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MediaItemPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MediaItemPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::ID, $id, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
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

        return $this->addUsingAlias(MediaItemPeer::NAME, $name, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByOwnerId($ownerId = null, $comparison = null)
    {
        if (is_array($ownerId)) {
            $useMinMax = false;
            if (isset($ownerId['min'])) {
                $this->addUsingAlias(MediaItemPeer::OWNER_ID, $ownerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ownerId['max'])) {
                $this->addUsingAlias(MediaItemPeer::OWNER_ID, $ownerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::OWNER_ID, $ownerId, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
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

        return $this->addUsingAlias(MediaItemPeer::DESCRIPTION, $description, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByLastPlayedTime($lastPlayedTime = null, $comparison = null)
    {
        if (is_array($lastPlayedTime)) {
            $useMinMax = false;
            if (isset($lastPlayedTime['min'])) {
                $this->addUsingAlias(MediaItemPeer::LAST_PLAYED, $lastPlayedTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastPlayedTime['max'])) {
                $this->addUsingAlias(MediaItemPeer::LAST_PLAYED, $lastPlayedTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::LAST_PLAYED, $lastPlayedTime, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByPlayCount($playCount = null, $comparison = null)
    {
        if (is_array($playCount)) {
            $useMinMax = false;
            if (isset($playCount['min'])) {
                $this->addUsingAlias(MediaItemPeer::PLAY_COUNT, $playCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playCount['max'])) {
                $this->addUsingAlias(MediaItemPeer::PLAY_COUNT, $playCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::PLAY_COUNT, $playCount, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
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

        return $this->addUsingAlias(MediaItemPeer::LENGTH, $length, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
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

        return $this->addUsingAlias(MediaItemPeer::MIME, $mime, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(MediaItemPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(MediaItemPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(MediaItemPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(MediaItemPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the descendant_class column
     *
     * Example usage:
     * <code>
     * $query->filterByDescendantClass('fooValue');   // WHERE descendant_class = 'fooValue'
     * $query->filterByDescendantClass('%fooValue%'); // WHERE descendant_class LIKE '%fooValue%'
     * </code>
     *
     * @param     string $descendantClass The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function filterByDescendantClass($descendantClass = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($descendantClass)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $descendantClass)) {
                $descendantClass = str_replace('*', '%', $descendantClass);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MediaItemPeer::DESCENDANT_CLASS, $descendantClass, $comparison);
    }

    /**
     * Filter the query by a related CcSubjs object
     *
     * @param   CcSubjs|PropelObjectCollection $ccSubjs The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSubjs($ccSubjs, $comparison = null)
    {
        if ($ccSubjs instanceof CcSubjs) {
            return $this
                ->addUsingAlias(MediaItemPeer::OWNER_ID, $ccSubjs->getDbId(), $comparison);
        } elseif ($ccSubjs instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MediaItemPeer::OWNER_ID, $ccSubjs->toKeyValue('PrimaryKey', 'DbId'), $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
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
     * Filter the query by a related CcShowInstances object
     *
     * @param   CcShowInstances|PropelObjectCollection $ccShowInstances  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowInstances($ccShowInstances, $comparison = null)
    {
        if ($ccShowInstances instanceof CcShowInstances) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $ccShowInstances->getDbRecordedMediaItem(), $comparison);
        } elseif ($ccShowInstances instanceof PropelObjectCollection) {
            return $this
                ->useCcShowInstancesQuery()
                ->filterByPrimaryKeys($ccShowInstances->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowInstances() only accepts arguments of type CcShowInstances or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowInstances relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinCcShowInstances($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowInstances');

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
            $this->addJoinObject($join, 'CcShowInstances');
        }

        return $this;
    }

    /**
     * Use the CcShowInstances relation CcShowInstances object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\CcShowInstancesQuery A secondary query class using the current class as primary query
     */
    public function useCcShowInstancesQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcShowInstances($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', '\Airtime\CcShowInstancesQuery');
    }

    /**
     * Filter the query by a related CcSchedule object
     *
     * @param   CcSchedule|PropelObjectCollection $ccSchedule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcSchedule($ccSchedule, $comparison = null)
    {
        if ($ccSchedule instanceof CcSchedule) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $ccSchedule->getDbMediaId(), $comparison);
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
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinCcSchedule($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
     * @return   \Airtime\CcScheduleQuery A secondary query class using the current class as primary query
     */
    public function useCcScheduleQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcSchedule', '\Airtime\CcScheduleQuery');
    }

    /**
     * Filter the query by a related PlaylistRule object
     *
     * @param   PlaylistRule|PropelObjectCollection $playlistRule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlaylistRule($playlistRule, $comparison = null)
    {
        if ($playlistRule instanceof PlaylistRule) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $playlistRule->getMediaId(), $comparison);
        } elseif ($playlistRule instanceof PropelObjectCollection) {
            return $this
                ->usePlaylistRuleQuery()
                ->filterByPrimaryKeys($playlistRule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPlaylistRule() only accepts arguments of type PlaylistRule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlaylistRule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinPlaylistRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlaylistRule');

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
            $this->addJoinObject($join, 'PlaylistRule');
        }

        return $this;
    }

    /**
     * Use the PlaylistRule relation PlaylistRule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\PlaylistRuleQuery A secondary query class using the current class as primary query
     */
    public function usePlaylistRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlaylistRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlaylistRule', '\Airtime\MediaItem\PlaylistRuleQuery');
    }

    /**
     * Filter the query by a related MediaContent object
     *
     * @param   MediaContent|PropelObjectCollection $mediaContent  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMediaContent($mediaContent, $comparison = null)
    {
        if ($mediaContent instanceof MediaContent) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $mediaContent->getMediaId(), $comparison);
        } elseif ($mediaContent instanceof PropelObjectCollection) {
            return $this
                ->useMediaContentQuery()
                ->filterByPrimaryKeys($mediaContent->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMediaContent() only accepts arguments of type MediaContent or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MediaContent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinMediaContent($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MediaContent');

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
            $this->addJoinObject($join, 'MediaContent');
        }

        return $this;
    }

    /**
     * Use the MediaContent relation MediaContent object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\MediaContentQuery A secondary query class using the current class as primary query
     */
    public function useMediaContentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMediaContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MediaContent', '\Airtime\MediaItem\MediaContentQuery');
    }

    /**
     * Filter the query by a related AudioFile object
     *
     * @param   AudioFile|PropelObjectCollection $audioFile  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAudioFile($audioFile, $comparison = null)
    {
        if ($audioFile instanceof AudioFile) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $audioFile->getId(), $comparison);
        } elseif ($audioFile instanceof PropelObjectCollection) {
            return $this
                ->useAudioFileQuery()
                ->filterByPrimaryKeys($audioFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAudioFile() only accepts arguments of type AudioFile or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AudioFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinAudioFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AudioFile');

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
            $this->addJoinObject($join, 'AudioFile');
        }

        return $this;
    }

    /**
     * Use the AudioFile relation AudioFile object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\AudioFileQuery A secondary query class using the current class as primary query
     */
    public function useAudioFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAudioFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AudioFile', '\Airtime\MediaItem\AudioFileQuery');
    }

    /**
     * Filter the query by a related Webstream object
     *
     * @param   Webstream|PropelObjectCollection $webstream  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWebstream($webstream, $comparison = null)
    {
        if ($webstream instanceof Webstream) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $webstream->getId(), $comparison);
        } elseif ($webstream instanceof PropelObjectCollection) {
            return $this
                ->useWebstreamQuery()
                ->filterByPrimaryKeys($webstream->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByWebstream() only accepts arguments of type Webstream or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Webstream relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinWebstream($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Webstream');

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
            $this->addJoinObject($join, 'Webstream');
        }

        return $this;
    }

    /**
     * Use the Webstream relation Webstream object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\WebstreamQuery A secondary query class using the current class as primary query
     */
    public function useWebstreamQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWebstream($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Webstream', '\Airtime\MediaItem\WebstreamQuery');
    }

    /**
     * Filter the query by a related Playlist object
     *
     * @param   Playlist|PropelObjectCollection $playlist  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaItemQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlaylist($playlist, $comparison = null)
    {
        if ($playlist instanceof Playlist) {
            return $this
                ->addUsingAlias(MediaItemPeer::ID, $playlist->getId(), $comparison);
        } elseif ($playlist instanceof PropelObjectCollection) {
            return $this
                ->usePlaylistQuery()
                ->filterByPrimaryKeys($playlist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPlaylist() only accepts arguments of type Playlist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Playlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function joinPlaylist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Playlist');

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
            $this->addJoinObject($join, 'Playlist');
        }

        return $this;
    }

    /**
     * Use the Playlist relation Playlist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Airtime\MediaItem\PlaylistQuery A secondary query class using the current class as primary query
     */
    public function usePlaylistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlaylist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Playlist', '\Airtime\MediaItem\PlaylistQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   MediaItem $mediaItem Object to remove from the list of results
     *
     * @return MediaItemQuery The current query, for fluid interface
     */
    public function prune($mediaItem = null)
    {
        if ($mediaItem) {
            $this->addUsingAlias(MediaItemPeer::ID, $mediaItem->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(MediaItemPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(MediaItemPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(MediaItemPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(MediaItemPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(MediaItemPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     MediaItemQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(MediaItemPeer::CREATED_AT);
    }
}
