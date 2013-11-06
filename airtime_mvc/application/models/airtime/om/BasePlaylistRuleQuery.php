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
use Airtime\MediaItem\PlaylistRule;
use Airtime\MediaItem\PlaylistRulePeer;
use Airtime\MediaItem\PlaylistRuleQuery;

/**
 * Base class that represents a query for the 'media_playlist_rule' table.
 *
 *
 *
 * @method PlaylistRuleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PlaylistRuleQuery orderByCriteria($order = Criteria::ASC) Order by the criteria column
 * @method PlaylistRuleQuery orderByModifier($order = Criteria::ASC) Order by the modifier column
 * @method PlaylistRuleQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method PlaylistRuleQuery orderByExtra($order = Criteria::ASC) Order by the extra column
 * @method PlaylistRuleQuery orderByMediaId($order = Criteria::ASC) Order by the media_id column
 *
 * @method PlaylistRuleQuery groupById() Group by the id column
 * @method PlaylistRuleQuery groupByCriteria() Group by the criteria column
 * @method PlaylistRuleQuery groupByModifier() Group by the modifier column
 * @method PlaylistRuleQuery groupByValue() Group by the value column
 * @method PlaylistRuleQuery groupByExtra() Group by the extra column
 * @method PlaylistRuleQuery groupByMediaId() Group by the media_id column
 *
 * @method PlaylistRuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlaylistRuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlaylistRuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlaylistRuleQuery leftJoinMediaItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the MediaItem relation
 * @method PlaylistRuleQuery rightJoinMediaItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MediaItem relation
 * @method PlaylistRuleQuery innerJoinMediaItem($relationAlias = null) Adds a INNER JOIN clause to the query using the MediaItem relation
 *
 * @method PlaylistRule findOne(PropelPDO $con = null) Return the first PlaylistRule matching the query
 * @method PlaylistRule findOneOrCreate(PropelPDO $con = null) Return the first PlaylistRule matching the query, or a new PlaylistRule object populated from the query conditions when no match is found
 *
 * @method PlaylistRule findOneByCriteria(string $criteria) Return the first PlaylistRule filtered by the criteria column
 * @method PlaylistRule findOneByModifier(string $modifier) Return the first PlaylistRule filtered by the modifier column
 * @method PlaylistRule findOneByValue(string $value) Return the first PlaylistRule filtered by the value column
 * @method PlaylistRule findOneByExtra(string $extra) Return the first PlaylistRule filtered by the extra column
 * @method PlaylistRule findOneByMediaId(int $media_id) Return the first PlaylistRule filtered by the media_id column
 *
 * @method array findById(int $id) Return PlaylistRule objects filtered by the id column
 * @method array findByCriteria(string $criteria) Return PlaylistRule objects filtered by the criteria column
 * @method array findByModifier(string $modifier) Return PlaylistRule objects filtered by the modifier column
 * @method array findByValue(string $value) Return PlaylistRule objects filtered by the value column
 * @method array findByExtra(string $extra) Return PlaylistRule objects filtered by the extra column
 * @method array findByMediaId(int $media_id) Return PlaylistRule objects filtered by the media_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BasePlaylistRuleQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlaylistRuleQuery object.
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
            $modelName = 'Airtime\\MediaItem\\PlaylistRule';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlaylistRuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlaylistRuleQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlaylistRuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlaylistRuleQuery) {
            return $criteria;
        }
        $query = new PlaylistRuleQuery(null, null, $modelAlias);

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
     * @return   PlaylistRule|PlaylistRule[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlaylistRulePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlaylistRulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PlaylistRule A model object, or null if the key is not found
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
     * @return                 PlaylistRule A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "criteria", "modifier", "value", "extra", "media_id" FROM "media_playlist_rule" WHERE "id" = :p0';
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
            $obj = new PlaylistRule();
            $obj->hydrate($row);
            PlaylistRulePeer::addInstanceToPool($obj, (string) $key);
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
     * @return PlaylistRule|PlaylistRule[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PlaylistRule[]|mixed the list of results, formatted by the current formatter
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
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlaylistRulePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlaylistRulePeer::ID, $keys, Criteria::IN);
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
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PlaylistRulePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PlaylistRulePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the criteria column
     *
     * Example usage:
     * <code>
     * $query->filterByCriteria('fooValue');   // WHERE criteria = 'fooValue'
     * $query->filterByCriteria('%fooValue%'); // WHERE criteria LIKE '%fooValue%'
     * </code>
     *
     * @param     string $criteria The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByCriteria($criteria = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($criteria)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $criteria)) {
                $criteria = str_replace('*', '%', $criteria);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::CRITERIA, $criteria, $comparison);
    }

    /**
     * Filter the query on the modifier column
     *
     * Example usage:
     * <code>
     * $query->filterByModifier('fooValue');   // WHERE modifier = 'fooValue'
     * $query->filterByModifier('%fooValue%'); // WHERE modifier LIKE '%fooValue%'
     * </code>
     *
     * @param     string $modifier The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByModifier($modifier = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($modifier)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $modifier)) {
                $modifier = str_replace('*', '%', $modifier);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::MODIFIER, $modifier, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::VALUE, $value, $comparison);
    }

    /**
     * Filter the query on the extra column
     *
     * Example usage:
     * <code>
     * $query->filterByExtra('fooValue');   // WHERE extra = 'fooValue'
     * $query->filterByExtra('%fooValue%'); // WHERE extra LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extra The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByExtra($extra = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extra)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extra)) {
                $extra = str_replace('*', '%', $extra);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::EXTRA, $extra, $comparison);
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
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function filterByMediaId($mediaId = null, $comparison = null)
    {
        if (is_array($mediaId)) {
            $useMinMax = false;
            if (isset($mediaId['min'])) {
                $this->addUsingAlias(PlaylistRulePeer::MEDIA_ID, $mediaId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mediaId['max'])) {
                $this->addUsingAlias(PlaylistRulePeer::MEDIA_ID, $mediaId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlaylistRulePeer::MEDIA_ID, $mediaId, $comparison);
    }

    /**
     * Filter the query by a related MediaItem object
     *
     * @param   MediaItem|PropelObjectCollection $mediaItem The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlaylistRuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByMediaItem($mediaItem, $comparison = null)
    {
        if ($mediaItem instanceof MediaItem) {
            return $this
                ->addUsingAlias(PlaylistRulePeer::MEDIA_ID, $mediaItem->getId(), $comparison);
        } elseif ($mediaItem instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PlaylistRulePeer::MEDIA_ID, $mediaItem->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return PlaylistRuleQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   PlaylistRule $playlistRule Object to remove from the list of results
     *
     * @return PlaylistRuleQuery The current query, for fluid interface
     */
    public function prune($playlistRule = null)
    {
        if ($playlistRule) {
            $this->addUsingAlias(PlaylistRulePeer::ID, $playlistRule->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
