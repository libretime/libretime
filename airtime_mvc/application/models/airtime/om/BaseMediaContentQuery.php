<?php

namespace Airtime\MediaItem\om;

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
use Airtime\MediaItem;
use Airtime\MediaItem\MediaContent;
use Airtime\MediaItem\MediaContentPeer;
use Airtime\MediaItem\MediaContentQuery;
use Airtime\MediaItem\Playlist;

/**
 * Base class that represents a query for the 'media_content' table.
 *
 *
 *
 * @method MediaContentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method MediaContentQuery orderByPlaylistId($order = Criteria::ASC) Order by the playlist_id column
 * @method MediaContentQuery orderByMediaId($order = Criteria::ASC) Order by the media_id column
 * @method MediaContentQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method MediaContentQuery orderByTrackOffset($order = Criteria::ASC) Order by the trackoffset column
 * @method MediaContentQuery orderByCliplength($order = Criteria::ASC) Order by the cliplength column
 * @method MediaContentQuery orderByCuein($order = Criteria::ASC) Order by the cuein column
 * @method MediaContentQuery orderByCueout($order = Criteria::ASC) Order by the cueout column
 * @method MediaContentQuery orderByFadein($order = Criteria::ASC) Order by the fadein column
 * @method MediaContentQuery orderByFadeout($order = Criteria::ASC) Order by the fadeout column
 *
 * @method MediaContentQuery groupById() Group by the id column
 * @method MediaContentQuery groupByPlaylistId() Group by the playlist_id column
 * @method MediaContentQuery groupByMediaId() Group by the media_id column
 * @method MediaContentQuery groupByPosition() Group by the position column
 * @method MediaContentQuery groupByTrackOffset() Group by the trackoffset column
 * @method MediaContentQuery groupByCliplength() Group by the cliplength column
 * @method MediaContentQuery groupByCuein() Group by the cuein column
 * @method MediaContentQuery groupByCueout() Group by the cueout column
 * @method MediaContentQuery groupByFadein() Group by the fadein column
 * @method MediaContentQuery groupByFadeout() Group by the fadeout column
 *
 * @method MediaContentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method MediaContentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method MediaContentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method MediaContentQuery leftJoinPlaylist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Playlist relation
 * @method MediaContentQuery rightJoinPlaylist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Playlist relation
 * @method MediaContentQuery innerJoinPlaylist($relationAlias = null) Adds a INNER JOIN clause to the query using the Playlist relation
 *
 * @method MediaContentQuery leftJoinMediaItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the MediaItem relation
 * @method MediaContentQuery rightJoinMediaItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MediaItem relation
 * @method MediaContentQuery innerJoinMediaItem($relationAlias = null) Adds a INNER JOIN clause to the query using the MediaItem relation
 *
 * @method MediaContent findOne(PropelPDO $con = null) Return the first MediaContent matching the query
 * @method MediaContent findOneOrCreate(PropelPDO $con = null) Return the first MediaContent matching the query, or a new MediaContent object populated from the query conditions when no match is found
 *
 * @method MediaContent findOneByPlaylistId(int $playlist_id) Return the first MediaContent filtered by the playlist_id column
 * @method MediaContent findOneByMediaId(int $media_id) Return the first MediaContent filtered by the media_id column
 * @method MediaContent findOneByPosition(int $position) Return the first MediaContent filtered by the position column
 * @method MediaContent findOneByTrackOffset(double $trackoffset) Return the first MediaContent filtered by the trackoffset column
 * @method MediaContent findOneByCliplength(string $cliplength) Return the first MediaContent filtered by the cliplength column
 * @method MediaContent findOneByCuein(string $cuein) Return the first MediaContent filtered by the cuein column
 * @method MediaContent findOneByCueout(string $cueout) Return the first MediaContent filtered by the cueout column
 * @method MediaContent findOneByFadein(string $fadein) Return the first MediaContent filtered by the fadein column
 * @method MediaContent findOneByFadeout(string $fadeout) Return the first MediaContent filtered by the fadeout column
 *
 * @method array findById(int $id) Return MediaContent objects filtered by the id column
 * @method array findByPlaylistId(int $playlist_id) Return MediaContent objects filtered by the playlist_id column
 * @method array findByMediaId(int $media_id) Return MediaContent objects filtered by the media_id column
 * @method array findByPosition(int $position) Return MediaContent objects filtered by the position column
 * @method array findByTrackOffset(double $trackoffset) Return MediaContent objects filtered by the trackoffset column
 * @method array findByCliplength(string $cliplength) Return MediaContent objects filtered by the cliplength column
 * @method array findByCuein(string $cuein) Return MediaContent objects filtered by the cuein column
 * @method array findByCueout(string $cueout) Return MediaContent objects filtered by the cueout column
 * @method array findByFadein(string $fadein) Return MediaContent objects filtered by the fadein column
 * @method array findByFadeout(string $fadeout) Return MediaContent objects filtered by the fadeout column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseMediaContentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseMediaContentQuery object.
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
            $modelName = 'Airtime\\MediaItem\\MediaContent';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new MediaContentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   MediaContentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return MediaContentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof MediaContentQuery) {
            return $criteria;
        }
        $query = new MediaContentQuery(null, null, $modelAlias);

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
     * @return   MediaContent|MediaContent[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MediaContentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(MediaContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 MediaContent A model object, or null if the key is not found
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
     * @return                 MediaContent A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "playlist_id", "media_id", "position", "trackoffset", "cliplength", "cuein", "cueout", "fadein", "fadeout" FROM "media_content" WHERE "id" = :p0';
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
            $obj = new MediaContent();
            $obj->hydrate($row);
            MediaContentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return MediaContent|MediaContent[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|MediaContent[]|mixed the list of results, formatted by the current formatter
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
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MediaContentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MediaContentPeer::ID, $keys, Criteria::IN);
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
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MediaContentPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MediaContentPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the playlist_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlaylistId(1234); // WHERE playlist_id = 1234
     * $query->filterByPlaylistId(array(12, 34)); // WHERE playlist_id IN (12, 34)
     * $query->filterByPlaylistId(array('min' => 12)); // WHERE playlist_id >= 12
     * $query->filterByPlaylistId(array('max' => 12)); // WHERE playlist_id <= 12
     * </code>
     *
     * @see       filterByPlaylist()
     *
     * @param     mixed $playlistId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByPlaylistId($playlistId = null, $comparison = null)
    {
        if (is_array($playlistId)) {
            $useMinMax = false;
            if (isset($playlistId['min'])) {
                $this->addUsingAlias(MediaContentPeer::PLAYLIST_ID, $playlistId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playlistId['max'])) {
                $this->addUsingAlias(MediaContentPeer::PLAYLIST_ID, $playlistId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::PLAYLIST_ID, $playlistId, $comparison);
    }

    /**
     * Filter the query on the media_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMediaId(1234); // WHERE media_id = 1234
     * $query->filterByMediaId(array(12, 34)); // WHERE media_id IN (12, 34)
     * $query->filterByMediaId(array('min' => 12)); // WHERE media_id >= 12
     * $query->filterByMediaId(array('max' => 12)); // WHERE media_id <= 12
     * </code>
     *
     * @see       filterByMediaItem()
     *
     * @param     mixed $mediaId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByMediaId($mediaId = null, $comparison = null)
    {
        if (is_array($mediaId)) {
            $useMinMax = false;
            if (isset($mediaId['min'])) {
                $this->addUsingAlias(MediaContentPeer::MEDIA_ID, $mediaId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mediaId['max'])) {
                $this->addUsingAlias(MediaContentPeer::MEDIA_ID, $mediaId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::MEDIA_ID, $mediaId, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position >= 12
     * $query->filterByPosition(array('max' => 12)); // WHERE position <= 12
     * </code>
     *
     * @param     mixed $position The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(MediaContentPeer::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(MediaContentPeer::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the trackoffset column
     *
     * Example usage:
     * <code>
     * $query->filterByTrackOffset(1234); // WHERE trackoffset = 1234
     * $query->filterByTrackOffset(array(12, 34)); // WHERE trackoffset IN (12, 34)
     * $query->filterByTrackOffset(array('min' => 12)); // WHERE trackoffset >= 12
     * $query->filterByTrackOffset(array('max' => 12)); // WHERE trackoffset <= 12
     * </code>
     *
     * @param     mixed $trackOffset The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByTrackOffset($trackOffset = null, $comparison = null)
    {
        if (is_array($trackOffset)) {
            $useMinMax = false;
            if (isset($trackOffset['min'])) {
                $this->addUsingAlias(MediaContentPeer::TRACKOFFSET, $trackOffset['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($trackOffset['max'])) {
                $this->addUsingAlias(MediaContentPeer::TRACKOFFSET, $trackOffset['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::TRACKOFFSET, $trackOffset, $comparison);
    }

    /**
     * Filter the query on the cliplength column
     *
     * Example usage:
     * <code>
     * $query->filterByCliplength('fooValue');   // WHERE cliplength = 'fooValue'
     * $query->filterByCliplength('%fooValue%'); // WHERE cliplength LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cliplength The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByCliplength($cliplength = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cliplength)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cliplength)) {
                $cliplength = str_replace('*', '%', $cliplength);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::CLIPLENGTH, $cliplength, $comparison);
    }

    /**
     * Filter the query on the cuein column
     *
     * Example usage:
     * <code>
     * $query->filterByCuein('fooValue');   // WHERE cuein = 'fooValue'
     * $query->filterByCuein('%fooValue%'); // WHERE cuein LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cuein The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByCuein($cuein = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cuein)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cuein)) {
                $cuein = str_replace('*', '%', $cuein);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::CUEIN, $cuein, $comparison);
    }

    /**
     * Filter the query on the cueout column
     *
     * Example usage:
     * <code>
     * $query->filterByCueout('fooValue');   // WHERE cueout = 'fooValue'
     * $query->filterByCueout('%fooValue%'); // WHERE cueout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cueout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByCueout($cueout = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cueout)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cueout)) {
                $cueout = str_replace('*', '%', $cueout);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::CUEOUT, $cueout, $comparison);
    }

    /**
     * Filter the query on the fadein column
     *
     * Example usage:
     * <code>
     * $query->filterByFadein(1234); // WHERE fadein = 1234
     * $query->filterByFadein(array(12, 34)); // WHERE fadein IN (12, 34)
     * $query->filterByFadein(array('min' => 12)); // WHERE fadein >= 12
     * $query->filterByFadein(array('max' => 12)); // WHERE fadein <= 12
     * </code>
     *
     * @param     mixed $fadein The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByFadein($fadein = null, $comparison = null)
    {
        if (is_array($fadein)) {
            $useMinMax = false;
            if (isset($fadein['min'])) {
                $this->addUsingAlias(MediaContentPeer::FADEIN, $fadein['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fadein['max'])) {
                $this->addUsingAlias(MediaContentPeer::FADEIN, $fadein['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::FADEIN, $fadein, $comparison);
    }

    /**
     * Filter the query on the fadeout column
     *
     * Example usage:
     * <code>
     * $query->filterByFadeout(1234); // WHERE fadeout = 1234
     * $query->filterByFadeout(array(12, 34)); // WHERE fadeout IN (12, 34)
     * $query->filterByFadeout(array('min' => 12)); // WHERE fadeout >= 12
     * $query->filterByFadeout(array('max' => 12)); // WHERE fadeout <= 12
     * </code>
     *
     * @param     mixed $fadeout The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function filterByFadeout($fadeout = null, $comparison = null)
    {
        if (is_array($fadeout)) {
            $useMinMax = false;
            if (isset($fadeout['min'])) {
                $this->addUsingAlias(MediaContentPeer::FADEOUT, $fadeout['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fadeout['max'])) {
                $this->addUsingAlias(MediaContentPeer::FADEOUT, $fadeout['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MediaContentPeer::FADEOUT, $fadeout, $comparison);
    }

    /**
     * Filter the query by a related Playlist object
     *
     * @param   Playlist|PropelObjectCollection $playlist The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaContentQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlaylist($playlist, $comparison = null)
    {
        if ($playlist instanceof Playlist) {
            return $this
                ->addUsingAlias(MediaContentPeer::PLAYLIST_ID, $playlist->getId(), $comparison);
        } elseif ($playlist instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MediaContentPeer::PLAYLIST_ID, $playlist->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function joinPlaylist($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function usePlaylistQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPlaylist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Playlist', '\Airtime\MediaItem\PlaylistQuery');
    }

    /**
     * Filter the query by a related MediaItem object
     *
     * @param   MediaItem|PropelObjectCollection $mediaItem The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 MediaContentQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMediaItem($mediaItem, $comparison = null)
    {
        if ($mediaItem instanceof MediaItem) {
            return $this
                ->addUsingAlias(MediaContentPeer::MEDIA_ID, $mediaItem->getId(), $comparison);
        } elseif ($mediaItem instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MediaContentPeer::MEDIA_ID, $mediaItem->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function joinMediaItem($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useMediaItemQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMediaItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MediaItem', '\Airtime\MediaItemQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   MediaContent $mediaContent Object to remove from the list of results
     *
     * @return MediaContentQuery The current query, for fluid interface
     */
    public function prune($mediaContent = null)
    {
        if ($mediaContent) {
            $this->addUsingAlias(MediaContentPeer::ID, $mediaContent->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
