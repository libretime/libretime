<?php


/**
 * Base class that represents a query for the 'cc_show' table.
 *
 *
 *
 * @method CcShowQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method CcShowQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method CcShowQuery orderByDbUrl($order = Criteria::ASC) Order by the url column
 * @method CcShowQuery orderByDbGenre($order = Criteria::ASC) Order by the genre column
 * @method CcShowQuery orderByDbDescription($order = Criteria::ASC) Order by the description column
 * @method CcShowQuery orderByDbColor($order = Criteria::ASC) Order by the color column
 * @method CcShowQuery orderByDbBackgroundColor($order = Criteria::ASC) Order by the background_color column
 * @method CcShowQuery orderByDbLiveStreamUsingAirtimeAuth($order = Criteria::ASC) Order by the live_stream_using_airtime_auth column
 * @method CcShowQuery orderByDbLiveStreamUsingCustomAuth($order = Criteria::ASC) Order by the live_stream_using_custom_auth column
 * @method CcShowQuery orderByDbLiveStreamUser($order = Criteria::ASC) Order by the live_stream_user column
 * @method CcShowQuery orderByDbLiveStreamPass($order = Criteria::ASC) Order by the live_stream_pass column
 * @method CcShowQuery orderByDbLinked($order = Criteria::ASC) Order by the linked column
 * @method CcShowQuery orderByDbIsLinkable($order = Criteria::ASC) Order by the is_linkable column
 * @method CcShowQuery orderByDbImagePath($order = Criteria::ASC) Order by the image_path column
 * @method CcShowQuery orderByDbHasAutoPlaylist($order = Criteria::ASC) Order by the has_autoplaylist column
 * @method CcShowQuery orderByDbAutoPlaylistId($order = Criteria::ASC) Order by the autoplaylist_id column
 * @method CcShowQuery orderByDbAutoPlaylistRepeat($order = Criteria::ASC) Order by the autoplaylist_repeat column
 * @method CcShowQuery orderByDbOverrideIntroPlaylist($order = Criteria::ASC) Order by the override_intro_playlist column
 * @method CcShowQuery orderByDbIntroPlaylistId($order = Criteria::ASC) Order by the intro_playlist_id column
 * @method CcShowQuery orderByDbOverrideOutroPlaylist($order = Criteria::ASC) Order by the override_outro_playlist column
 * @method CcShowQuery orderByDbOutroPlaylistId($order = Criteria::ASC) Order by the outro_playlist_id column
 *
 * @method CcShowQuery groupByDbId() Group by the id column
 * @method CcShowQuery groupByDbName() Group by the name column
 * @method CcShowQuery groupByDbUrl() Group by the url column
 * @method CcShowQuery groupByDbGenre() Group by the genre column
 * @method CcShowQuery groupByDbDescription() Group by the description column
 * @method CcShowQuery groupByDbColor() Group by the color column
 * @method CcShowQuery groupByDbBackgroundColor() Group by the background_color column
 * @method CcShowQuery groupByDbLiveStreamUsingAirtimeAuth() Group by the live_stream_using_airtime_auth column
 * @method CcShowQuery groupByDbLiveStreamUsingCustomAuth() Group by the live_stream_using_custom_auth column
 * @method CcShowQuery groupByDbLiveStreamUser() Group by the live_stream_user column
 * @method CcShowQuery groupByDbLiveStreamPass() Group by the live_stream_pass column
 * @method CcShowQuery groupByDbLinked() Group by the linked column
 * @method CcShowQuery groupByDbIsLinkable() Group by the is_linkable column
 * @method CcShowQuery groupByDbImagePath() Group by the image_path column
 * @method CcShowQuery groupByDbHasAutoPlaylist() Group by the has_autoplaylist column
 * @method CcShowQuery groupByDbAutoPlaylistId() Group by the autoplaylist_id column
 * @method CcShowQuery groupByDbAutoPlaylistRepeat() Group by the autoplaylist_repeat column
 * @method CcShowQuery groupByDbOverrideIntroPlaylist() Group by the override_intro_playlist column
 * @method CcShowQuery groupByDbIntroPlaylistId() Group by the intro_playlist_id column
 * @method CcShowQuery groupByDbOverrideOutroPlaylist() Group by the override_outro_playlist column
 * @method CcShowQuery groupByDbOutroPlaylistId() Group by the outro_playlist_id column
 *
 * @method CcShowQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CcShowQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CcShowQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CcShowQuery leftJoinCcPlaylistRelatedByDbAutoPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistRelatedByDbAutoPlaylistId relation
 * @method CcShowQuery rightJoinCcPlaylistRelatedByDbAutoPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistRelatedByDbAutoPlaylistId relation
 * @method CcShowQuery innerJoinCcPlaylistRelatedByDbAutoPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistRelatedByDbAutoPlaylistId relation
 *
 * @method CcShowQuery leftJoinCcPlaylistRelatedByDbIntroPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistRelatedByDbIntroPlaylistId relation
 * @method CcShowQuery rightJoinCcPlaylistRelatedByDbIntroPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistRelatedByDbIntroPlaylistId relation
 * @method CcShowQuery innerJoinCcPlaylistRelatedByDbIntroPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistRelatedByDbIntroPlaylistId relation
 *
 * @method CcShowQuery leftJoinCcPlaylistRelatedByDbOutroPlaylistId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcPlaylistRelatedByDbOutroPlaylistId relation
 * @method CcShowQuery rightJoinCcPlaylistRelatedByDbOutroPlaylistId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcPlaylistRelatedByDbOutroPlaylistId relation
 * @method CcShowQuery innerJoinCcPlaylistRelatedByDbOutroPlaylistId($relationAlias = null) Adds a INNER JOIN clause to the query using the CcPlaylistRelatedByDbOutroPlaylistId relation
 *
 * @method CcShowQuery leftJoinCcShowInstances($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowInstances relation
 * @method CcShowQuery rightJoinCcShowInstances($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowInstances relation
 * @method CcShowQuery innerJoinCcShowInstances($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowInstances relation
 *
 * @method CcShowQuery leftJoinCcShowDays($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowDays relation
 * @method CcShowQuery rightJoinCcShowDays($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowDays relation
 * @method CcShowQuery innerJoinCcShowDays($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowDays relation
 *
 * @method CcShowQuery leftJoinCcShowRebroadcast($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowRebroadcast relation
 * @method CcShowQuery rightJoinCcShowRebroadcast($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowRebroadcast relation
 * @method CcShowQuery innerJoinCcShowRebroadcast($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowRebroadcast relation
 *
 * @method CcShowQuery leftJoinCcShowHosts($relationAlias = null) Adds a LEFT JOIN clause to the query using the CcShowHosts relation
 * @method CcShowQuery rightJoinCcShowHosts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CcShowHosts relation
 * @method CcShowQuery innerJoinCcShowHosts($relationAlias = null) Adds a INNER JOIN clause to the query using the CcShowHosts relation
 *
 * @method CcShow findOne(PropelPDO $con = null) Return the first CcShow matching the query
 * @method CcShow findOneOrCreate(PropelPDO $con = null) Return the first CcShow matching the query, or a new CcShow object populated from the query conditions when no match is found
 *
 * @method CcShow findOneByDbName(string $name) Return the first CcShow filtered by the name column
 * @method CcShow findOneByDbUrl(string $url) Return the first CcShow filtered by the url column
 * @method CcShow findOneByDbGenre(string $genre) Return the first CcShow filtered by the genre column
 * @method CcShow findOneByDbDescription(string $description) Return the first CcShow filtered by the description column
 * @method CcShow findOneByDbColor(string $color) Return the first CcShow filtered by the color column
 * @method CcShow findOneByDbBackgroundColor(string $background_color) Return the first CcShow filtered by the background_color column
 * @method CcShow findOneByDbLiveStreamUsingAirtimeAuth(boolean $live_stream_using_airtime_auth) Return the first CcShow filtered by the live_stream_using_airtime_auth column
 * @method CcShow findOneByDbLiveStreamUsingCustomAuth(boolean $live_stream_using_custom_auth) Return the first CcShow filtered by the live_stream_using_custom_auth column
 * @method CcShow findOneByDbLiveStreamUser(string $live_stream_user) Return the first CcShow filtered by the live_stream_user column
 * @method CcShow findOneByDbLiveStreamPass(string $live_stream_pass) Return the first CcShow filtered by the live_stream_pass column
 * @method CcShow findOneByDbLinked(boolean $linked) Return the first CcShow filtered by the linked column
 * @method CcShow findOneByDbIsLinkable(boolean $is_linkable) Return the first CcShow filtered by the is_linkable column
 * @method CcShow findOneByDbImagePath(string $image_path) Return the first CcShow filtered by the image_path column
 * @method CcShow findOneByDbHasAutoPlaylist(boolean $has_autoplaylist) Return the first CcShow filtered by the has_autoplaylist column
 * @method CcShow findOneByDbAutoPlaylistId(int $autoplaylist_id) Return the first CcShow filtered by the autoplaylist_id column
 * @method CcShow findOneByDbAutoPlaylistRepeat(boolean $autoplaylist_repeat) Return the first CcShow filtered by the autoplaylist_repeat column
 * @method CcShow findOneByDbOverrideIntroPlaylist(boolean $override_intro_playlist) Return the first CcShow filtered by the override_intro_playlist column
 * @method CcShow findOneByDbIntroPlaylistId(int $intro_playlist_id) Return the first CcShow filtered by the intro_playlist_id column
 * @method CcShow findOneByDbOverrideOutroPlaylist(boolean $override_outro_playlist) Return the first CcShow filtered by the override_outro_playlist column
 * @method CcShow findOneByDbOutroPlaylistId(int $outro_playlist_id) Return the first CcShow filtered by the outro_playlist_id column
 *
 * @method array findByDbId(int $id) Return CcShow objects filtered by the id column
 * @method array findByDbName(string $name) Return CcShow objects filtered by the name column
 * @method array findByDbUrl(string $url) Return CcShow objects filtered by the url column
 * @method array findByDbGenre(string $genre) Return CcShow objects filtered by the genre column
 * @method array findByDbDescription(string $description) Return CcShow objects filtered by the description column
 * @method array findByDbColor(string $color) Return CcShow objects filtered by the color column
 * @method array findByDbBackgroundColor(string $background_color) Return CcShow objects filtered by the background_color column
 * @method array findByDbLiveStreamUsingAirtimeAuth(boolean $live_stream_using_airtime_auth) Return CcShow objects filtered by the live_stream_using_airtime_auth column
 * @method array findByDbLiveStreamUsingCustomAuth(boolean $live_stream_using_custom_auth) Return CcShow objects filtered by the live_stream_using_custom_auth column
 * @method array findByDbLiveStreamUser(string $live_stream_user) Return CcShow objects filtered by the live_stream_user column
 * @method array findByDbLiveStreamPass(string $live_stream_pass) Return CcShow objects filtered by the live_stream_pass column
 * @method array findByDbLinked(boolean $linked) Return CcShow objects filtered by the linked column
 * @method array findByDbIsLinkable(boolean $is_linkable) Return CcShow objects filtered by the is_linkable column
 * @method array findByDbImagePath(string $image_path) Return CcShow objects filtered by the image_path column
 * @method array findByDbHasAutoPlaylist(boolean $has_autoplaylist) Return CcShow objects filtered by the has_autoplaylist column
 * @method array findByDbAutoPlaylistId(int $autoplaylist_id) Return CcShow objects filtered by the autoplaylist_id column
 * @method array findByDbAutoPlaylistRepeat(boolean $autoplaylist_repeat) Return CcShow objects filtered by the autoplaylist_repeat column
 * @method array findByDbOverrideIntroPlaylist(boolean $override_intro_playlist) Return CcShow objects filtered by the override_intro_playlist column
 * @method array findByDbIntroPlaylistId(int $intro_playlist_id) Return CcShow objects filtered by the intro_playlist_id column
 * @method array findByDbOverrideOutroPlaylist(boolean $override_outro_playlist) Return CcShow objects filtered by the override_outro_playlist column
 * @method array findByDbOutroPlaylistId(int $outro_playlist_id) Return CcShow objects filtered by the outro_playlist_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCcShowQuery object.
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
            $modelName = 'CcShow';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CcShowQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CcShowQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CcShowQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CcShowQuery) {
            return $criteria;
        }
        $query = new CcShowQuery(null, null, $modelAlias);

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
     * @return   CcShow|CcShow[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CcShowPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CcShowPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CcShow A model object, or null if the key is not found
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
     * @return                 CcShow A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "name", "url", "genre", "description", "color", "background_color", "live_stream_using_airtime_auth", "live_stream_using_custom_auth", "live_stream_user", "live_stream_pass", "linked", "is_linkable", "image_path", "has_autoplaylist", "autoplaylist_id", "autoplaylist_repeat", "override_intro_playlist", "intro_playlist_id", "override_outro_playlist", "outro_playlist_id" FROM "cc_show" WHERE "id" = :p0';
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
            $obj = new CcShow();
            $obj->hydrate($row);
            CcShowPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CcShow|CcShow[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CcShow[]|mixed the list of results, formatted by the current formatter
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
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CcShowPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CcShowPeer::ID, $keys, Criteria::IN);
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
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbId($dbId = null, $comparison = null)
    {
        if (is_array($dbId)) {
            $useMinMax = false;
            if (isset($dbId['min'])) {
                $this->addUsingAlias(CcShowPeer::ID, $dbId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbId['max'])) {
                $this->addUsingAlias(CcShowPeer::ID, $dbId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowPeer::ID, $dbId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByDbName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByDbName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbName($dbName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbName)) {
                $dbName = str_replace('*', '%', $dbName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::NAME, $dbName, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByDbUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByDbUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbUrl($dbUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbUrl)) {
                $dbUrl = str_replace('*', '%', $dbUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::URL, $dbUrl, $comparison);
    }

    /**
     * Filter the query on the genre column
     *
     * Example usage:
     * <code>
     * $query->filterByDbGenre('fooValue');   // WHERE genre = 'fooValue'
     * $query->filterByDbGenre('%fooValue%'); // WHERE genre LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbGenre The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbGenre($dbGenre = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbGenre)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbGenre)) {
                $dbGenre = str_replace('*', '%', $dbGenre);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::GENRE, $dbGenre, $comparison);
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
     * @return CcShowQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CcShowPeer::DESCRIPTION, $dbDescription, $comparison);
    }

    /**
     * Filter the query on the color column
     *
     * Example usage:
     * <code>
     * $query->filterByDbColor('fooValue');   // WHERE color = 'fooValue'
     * $query->filterByDbColor('%fooValue%'); // WHERE color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbColor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbColor($dbColor = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbColor)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbColor)) {
                $dbColor = str_replace('*', '%', $dbColor);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::COLOR, $dbColor, $comparison);
    }

    /**
     * Filter the query on the background_color column
     *
     * Example usage:
     * <code>
     * $query->filterByDbBackgroundColor('fooValue');   // WHERE background_color = 'fooValue'
     * $query->filterByDbBackgroundColor('%fooValue%'); // WHERE background_color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbBackgroundColor The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbBackgroundColor($dbBackgroundColor = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbBackgroundColor)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbBackgroundColor)) {
                $dbBackgroundColor = str_replace('*', '%', $dbBackgroundColor);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::BACKGROUND_COLOR, $dbBackgroundColor, $comparison);
    }

    /**
     * Filter the query on the live_stream_using_airtime_auth column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLiveStreamUsingAirtimeAuth(true); // WHERE live_stream_using_airtime_auth = true
     * $query->filterByDbLiveStreamUsingAirtimeAuth('yes'); // WHERE live_stream_using_airtime_auth = true
     * </code>
     *
     * @param     boolean|string $dbLiveStreamUsingAirtimeAuth The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbLiveStreamUsingAirtimeAuth($dbLiveStreamUsingAirtimeAuth = null, $comparison = null)
    {
        if (is_string($dbLiveStreamUsingAirtimeAuth)) {
            $dbLiveStreamUsingAirtimeAuth = in_array(strtolower($dbLiveStreamUsingAirtimeAuth), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USING_AIRTIME_AUTH, $dbLiveStreamUsingAirtimeAuth, $comparison);
    }

    /**
     * Filter the query on the live_stream_using_custom_auth column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLiveStreamUsingCustomAuth(true); // WHERE live_stream_using_custom_auth = true
     * $query->filterByDbLiveStreamUsingCustomAuth('yes'); // WHERE live_stream_using_custom_auth = true
     * </code>
     *
     * @param     boolean|string $dbLiveStreamUsingCustomAuth The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbLiveStreamUsingCustomAuth($dbLiveStreamUsingCustomAuth = null, $comparison = null)
    {
        if (is_string($dbLiveStreamUsingCustomAuth)) {
            $dbLiveStreamUsingCustomAuth = in_array(strtolower($dbLiveStreamUsingCustomAuth), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USING_CUSTOM_AUTH, $dbLiveStreamUsingCustomAuth, $comparison);
    }

    /**
     * Filter the query on the live_stream_user column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLiveStreamUser('fooValue');   // WHERE live_stream_user = 'fooValue'
     * $query->filterByDbLiveStreamUser('%fooValue%'); // WHERE live_stream_user LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLiveStreamUser The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbLiveStreamUser($dbLiveStreamUser = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLiveStreamUser)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLiveStreamUser)) {
                $dbLiveStreamUser = str_replace('*', '%', $dbLiveStreamUser);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_USER, $dbLiveStreamUser, $comparison);
    }

    /**
     * Filter the query on the live_stream_pass column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLiveStreamPass('fooValue');   // WHERE live_stream_pass = 'fooValue'
     * $query->filterByDbLiveStreamPass('%fooValue%'); // WHERE live_stream_pass LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbLiveStreamPass The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbLiveStreamPass($dbLiveStreamPass = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbLiveStreamPass)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbLiveStreamPass)) {
                $dbLiveStreamPass = str_replace('*', '%', $dbLiveStreamPass);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::LIVE_STREAM_PASS, $dbLiveStreamPass, $comparison);
    }

    /**
     * Filter the query on the linked column
     *
     * Example usage:
     * <code>
     * $query->filterByDbLinked(true); // WHERE linked = true
     * $query->filterByDbLinked('yes'); // WHERE linked = true
     * </code>
     *
     * @param     boolean|string $dbLinked The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbLinked($dbLinked = null, $comparison = null)
    {
        if (is_string($dbLinked)) {
            $dbLinked = in_array(strtolower($dbLinked), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::LINKED, $dbLinked, $comparison);
    }

    /**
     * Filter the query on the is_linkable column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIsLinkable(true); // WHERE is_linkable = true
     * $query->filterByDbIsLinkable('yes'); // WHERE is_linkable = true
     * </code>
     *
     * @param     boolean|string $dbIsLinkable The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbIsLinkable($dbIsLinkable = null, $comparison = null)
    {
        if (is_string($dbIsLinkable)) {
            $dbIsLinkable = in_array(strtolower($dbIsLinkable), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::IS_LINKABLE, $dbIsLinkable, $comparison);
    }

    /**
     * Filter the query on the image_path column
     *
     * Example usage:
     * <code>
     * $query->filterByDbImagePath('fooValue');   // WHERE image_path = 'fooValue'
     * $query->filterByDbImagePath('%fooValue%'); // WHERE image_path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbImagePath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbImagePath($dbImagePath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbImagePath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbImagePath)) {
                $dbImagePath = str_replace('*', '%', $dbImagePath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CcShowPeer::IMAGE_PATH, $dbImagePath, $comparison);
    }

    /**
     * Filter the query on the has_autoplaylist column
     *
     * Example usage:
     * <code>
     * $query->filterByDbHasAutoPlaylist(true); // WHERE has_autoplaylist = true
     * $query->filterByDbHasAutoPlaylist('yes'); // WHERE has_autoplaylist = true
     * </code>
     *
     * @param     boolean|string $dbHasAutoPlaylist The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbHasAutoPlaylist($dbHasAutoPlaylist = null, $comparison = null)
    {
        if (is_string($dbHasAutoPlaylist)) {
            $dbHasAutoPlaylist = in_array(strtolower($dbHasAutoPlaylist), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::HAS_AUTOPLAYLIST, $dbHasAutoPlaylist, $comparison);
    }

    /**
     * Filter the query on the autoplaylist_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoPlaylistId(1234); // WHERE autoplaylist_id = 1234
     * $query->filterByDbAutoPlaylistId(array(12, 34)); // WHERE autoplaylist_id IN (12, 34)
     * $query->filterByDbAutoPlaylistId(array('min' => 12)); // WHERE autoplaylist_id >= 12
     * $query->filterByDbAutoPlaylistId(array('max' => 12)); // WHERE autoplaylist_id <= 12
     * </code>
     *
     * @see       filterByCcPlaylistRelatedByDbAutoPlaylistId()
     *
     * @param     mixed $dbAutoPlaylistId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbAutoPlaylistId($dbAutoPlaylistId = null, $comparison = null)
    {
        if (is_array($dbAutoPlaylistId)) {
            $useMinMax = false;
            if (isset($dbAutoPlaylistId['min'])) {
                $this->addUsingAlias(CcShowPeer::AUTOPLAYLIST_ID, $dbAutoPlaylistId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbAutoPlaylistId['max'])) {
                $this->addUsingAlias(CcShowPeer::AUTOPLAYLIST_ID, $dbAutoPlaylistId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowPeer::AUTOPLAYLIST_ID, $dbAutoPlaylistId, $comparison);
    }

    /**
     * Filter the query on the autoplaylist_repeat column
     *
     * Example usage:
     * <code>
     * $query->filterByDbAutoPlaylistRepeat(true); // WHERE autoplaylist_repeat = true
     * $query->filterByDbAutoPlaylistRepeat('yes'); // WHERE autoplaylist_repeat = true
     * </code>
     *
     * @param     boolean|string $dbAutoPlaylistRepeat The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbAutoPlaylistRepeat($dbAutoPlaylistRepeat = null, $comparison = null)
    {
        if (is_string($dbAutoPlaylistRepeat)) {
            $dbAutoPlaylistRepeat = in_array(strtolower($dbAutoPlaylistRepeat), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::AUTOPLAYLIST_REPEAT, $dbAutoPlaylistRepeat, $comparison);
    }

    /**
     * Filter the query on the override_intro_playlist column
     *
     * Example usage:
     * <code>
     * $query->filterByDbOverrideIntroPlaylist(true); // WHERE override_intro_playlist = true
     * $query->filterByDbOverrideIntroPlaylist('yes'); // WHERE override_intro_playlist = true
     * </code>
     *
     * @param     boolean|string $dbOverrideIntroPlaylist The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbOverrideIntroPlaylist($dbOverrideIntroPlaylist = null, $comparison = null)
    {
        if (is_string($dbOverrideIntroPlaylist)) {
            $dbOverrideIntroPlaylist = in_array(strtolower($dbOverrideIntroPlaylist), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::OVERRIDE_INTRO_PLAYLIST, $dbOverrideIntroPlaylist, $comparison);
    }

    /**
     * Filter the query on the intro_playlist_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbIntroPlaylistId(1234); // WHERE intro_playlist_id = 1234
     * $query->filterByDbIntroPlaylistId(array(12, 34)); // WHERE intro_playlist_id IN (12, 34)
     * $query->filterByDbIntroPlaylistId(array('min' => 12)); // WHERE intro_playlist_id >= 12
     * $query->filterByDbIntroPlaylistId(array('max' => 12)); // WHERE intro_playlist_id <= 12
     * </code>
     *
     * @see       filterByCcPlaylistRelatedByDbIntroPlaylistId()
     *
     * @param     mixed $dbIntroPlaylistId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbIntroPlaylistId($dbIntroPlaylistId = null, $comparison = null)
    {
        if (is_array($dbIntroPlaylistId)) {
            $useMinMax = false;
            if (isset($dbIntroPlaylistId['min'])) {
                $this->addUsingAlias(CcShowPeer::INTRO_PLAYLIST_ID, $dbIntroPlaylistId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbIntroPlaylistId['max'])) {
                $this->addUsingAlias(CcShowPeer::INTRO_PLAYLIST_ID, $dbIntroPlaylistId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowPeer::INTRO_PLAYLIST_ID, $dbIntroPlaylistId, $comparison);
    }

    /**
     * Filter the query on the override_outro_playlist column
     *
     * Example usage:
     * <code>
     * $query->filterByDbOverrideOutroPlaylist(true); // WHERE override_outro_playlist = true
     * $query->filterByDbOverrideOutroPlaylist('yes'); // WHERE override_outro_playlist = true
     * </code>
     *
     * @param     boolean|string $dbOverrideOutroPlaylist The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbOverrideOutroPlaylist($dbOverrideOutroPlaylist = null, $comparison = null)
    {
        if (is_string($dbOverrideOutroPlaylist)) {
            $dbOverrideOutroPlaylist = in_array(strtolower($dbOverrideOutroPlaylist), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CcShowPeer::OVERRIDE_OUTRO_PLAYLIST, $dbOverrideOutroPlaylist, $comparison);
    }

    /**
     * Filter the query on the outro_playlist_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDbOutroPlaylistId(1234); // WHERE outro_playlist_id = 1234
     * $query->filterByDbOutroPlaylistId(array(12, 34)); // WHERE outro_playlist_id IN (12, 34)
     * $query->filterByDbOutroPlaylistId(array('min' => 12)); // WHERE outro_playlist_id >= 12
     * $query->filterByDbOutroPlaylistId(array('max' => 12)); // WHERE outro_playlist_id <= 12
     * </code>
     *
     * @see       filterByCcPlaylistRelatedByDbOutroPlaylistId()
     *
     * @param     mixed $dbOutroPlaylistId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function filterByDbOutroPlaylistId($dbOutroPlaylistId = null, $comparison = null)
    {
        if (is_array($dbOutroPlaylistId)) {
            $useMinMax = false;
            if (isset($dbOutroPlaylistId['min'])) {
                $this->addUsingAlias(CcShowPeer::OUTRO_PLAYLIST_ID, $dbOutroPlaylistId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dbOutroPlaylistId['max'])) {
                $this->addUsingAlias(CcShowPeer::OUTRO_PLAYLIST_ID, $dbOutroPlaylistId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CcShowPeer::OUTRO_PLAYLIST_ID, $dbOutroPlaylistId, $comparison);
    }

    /**
     * Filter the query by a related CcPlaylist object
     *
     * @param   CcPlaylist|PropelObjectCollection $ccPlaylist The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistRelatedByDbAutoPlaylistId($ccPlaylist, $comparison = null)
    {
        if ($ccPlaylist instanceof CcPlaylist) {
            return $this
                ->addUsingAlias(CcShowPeer::AUTOPLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
        } elseif ($ccPlaylist instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowPeer::AUTOPLAYLIST_ID, $ccPlaylist->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlaylistRelatedByDbAutoPlaylistId() only accepts arguments of type CcPlaylist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylistRelatedByDbAutoPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcPlaylistRelatedByDbAutoPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlaylistRelatedByDbAutoPlaylistId');

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
            $this->addJoinObject($join, 'CcPlaylistRelatedByDbAutoPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcPlaylistRelatedByDbAutoPlaylistId relation CcPlaylist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlaylistQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistRelatedByDbAutoPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylistRelatedByDbAutoPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylistRelatedByDbAutoPlaylistId', 'CcPlaylistQuery');
    }

    /**
     * Filter the query by a related CcPlaylist object
     *
     * @param   CcPlaylist|PropelObjectCollection $ccPlaylist The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistRelatedByDbIntroPlaylistId($ccPlaylist, $comparison = null)
    {
        if ($ccPlaylist instanceof CcPlaylist) {
            return $this
                ->addUsingAlias(CcShowPeer::INTRO_PLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
        } elseif ($ccPlaylist instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowPeer::INTRO_PLAYLIST_ID, $ccPlaylist->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlaylistRelatedByDbIntroPlaylistId() only accepts arguments of type CcPlaylist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylistRelatedByDbIntroPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcPlaylistRelatedByDbIntroPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlaylistRelatedByDbIntroPlaylistId');

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
            $this->addJoinObject($join, 'CcPlaylistRelatedByDbIntroPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcPlaylistRelatedByDbIntroPlaylistId relation CcPlaylist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlaylistQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistRelatedByDbIntroPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylistRelatedByDbIntroPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylistRelatedByDbIntroPlaylistId', 'CcPlaylistQuery');
    }

    /**
     * Filter the query by a related CcPlaylist object
     *
     * @param   CcPlaylist|PropelObjectCollection $ccPlaylist The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcPlaylistRelatedByDbOutroPlaylistId($ccPlaylist, $comparison = null)
    {
        if ($ccPlaylist instanceof CcPlaylist) {
            return $this
                ->addUsingAlias(CcShowPeer::OUTRO_PLAYLIST_ID, $ccPlaylist->getDbId(), $comparison);
        } elseif ($ccPlaylist instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CcShowPeer::OUTRO_PLAYLIST_ID, $ccPlaylist->toKeyValue('PrimaryKey', 'DbId'), $comparison);
        } else {
            throw new PropelException('filterByCcPlaylistRelatedByDbOutroPlaylistId() only accepts arguments of type CcPlaylist or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcPlaylistRelatedByDbOutroPlaylistId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcPlaylistRelatedByDbOutroPlaylistId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcPlaylistRelatedByDbOutroPlaylistId');

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
            $this->addJoinObject($join, 'CcPlaylistRelatedByDbOutroPlaylistId');
        }

        return $this;
    }

    /**
     * Use the CcPlaylistRelatedByDbOutroPlaylistId relation CcPlaylist object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcPlaylistQuery A secondary query class using the current class as primary query
     */
    public function useCcPlaylistRelatedByDbOutroPlaylistIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCcPlaylistRelatedByDbOutroPlaylistId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcPlaylistRelatedByDbOutroPlaylistId', 'CcPlaylistQuery');
    }

    /**
     * Filter the query by a related CcShowInstances object
     *
     * @param   CcShowInstances|PropelObjectCollection $ccShowInstances  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowInstances($ccShowInstances, $comparison = null)
    {
        if ($ccShowInstances instanceof CcShowInstances) {
            return $this
                ->addUsingAlias(CcShowPeer::ID, $ccShowInstances->getDbShowId(), $comparison);
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
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcShowInstances($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
     * @return   CcShowInstancesQuery A secondary query class using the current class as primary query
     */
    public function useCcShowInstancesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShowInstances($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowInstances', 'CcShowInstancesQuery');
    }

    /**
     * Filter the query by a related CcShowDays object
     *
     * @param   CcShowDays|PropelObjectCollection $ccShowDays  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowDays($ccShowDays, $comparison = null)
    {
        if ($ccShowDays instanceof CcShowDays) {
            return $this
                ->addUsingAlias(CcShowPeer::ID, $ccShowDays->getDbShowId(), $comparison);
        } elseif ($ccShowDays instanceof PropelObjectCollection) {
            return $this
                ->useCcShowDaysQuery()
                ->filterByPrimaryKeys($ccShowDays->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowDays() only accepts arguments of type CcShowDays or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowDays relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcShowDays($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowDays');

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
            $this->addJoinObject($join, 'CcShowDays');
        }

        return $this;
    }

    /**
     * Use the CcShowDays relation CcShowDays object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowDaysQuery A secondary query class using the current class as primary query
     */
    public function useCcShowDaysQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShowDays($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowDays', 'CcShowDaysQuery');
    }

    /**
     * Filter the query by a related CcShowRebroadcast object
     *
     * @param   CcShowRebroadcast|PropelObjectCollection $ccShowRebroadcast  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowRebroadcast($ccShowRebroadcast, $comparison = null)
    {
        if ($ccShowRebroadcast instanceof CcShowRebroadcast) {
            return $this
                ->addUsingAlias(CcShowPeer::ID, $ccShowRebroadcast->getDbShowId(), $comparison);
        } elseif ($ccShowRebroadcast instanceof PropelObjectCollection) {
            return $this
                ->useCcShowRebroadcastQuery()
                ->filterByPrimaryKeys($ccShowRebroadcast->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowRebroadcast() only accepts arguments of type CcShowRebroadcast or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowRebroadcast relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcShowRebroadcast($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowRebroadcast');

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
            $this->addJoinObject($join, 'CcShowRebroadcast');
        }

        return $this;
    }

    /**
     * Use the CcShowRebroadcast relation CcShowRebroadcast object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowRebroadcastQuery A secondary query class using the current class as primary query
     */
    public function useCcShowRebroadcastQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShowRebroadcast($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowRebroadcast', 'CcShowRebroadcastQuery');
    }

    /**
     * Filter the query by a related CcShowHosts object
     *
     * @param   CcShowHosts|PropelObjectCollection $ccShowHosts  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CcShowQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCcShowHosts($ccShowHosts, $comparison = null)
    {
        if ($ccShowHosts instanceof CcShowHosts) {
            return $this
                ->addUsingAlias(CcShowPeer::ID, $ccShowHosts->getDbShow(), $comparison);
        } elseif ($ccShowHosts instanceof PropelObjectCollection) {
            return $this
                ->useCcShowHostsQuery()
                ->filterByPrimaryKeys($ccShowHosts->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCcShowHosts() only accepts arguments of type CcShowHosts or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CcShowHosts relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function joinCcShowHosts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CcShowHosts');

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
            $this->addJoinObject($join, 'CcShowHosts');
        }

        return $this;
    }

    /**
     * Use the CcShowHosts relation CcShowHosts object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   CcShowHostsQuery A secondary query class using the current class as primary query
     */
    public function useCcShowHostsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCcShowHosts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CcShowHosts', 'CcShowHostsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CcShow $ccShow Object to remove from the list of results
     *
     * @return CcShowQuery The current query, for fluid interface
     */
    public function prune($ccShow = null)
    {
        if ($ccShow) {
            $this->addUsingAlias(CcShowPeer::ID, $ccShow->getDbId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
