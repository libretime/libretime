<?php


/**
 * Base class that represents a query for the 'cc_tag' table.
 *
 *
 *
 * @method     CcTagQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcTagQuery orderByDbTagName($order = Criteria::ASC) Order by the tag_name column
 * @method     CcTagQuery orderByDbTagType($order = Criteria::ASC) Order by the tag_type column
 *
 * @method     CcTagQuery groupByDbId() Group by the id column
 * @method     CcTagQuery groupByDbTagName() Group by the tag_name column
 * @method     CcTagQuery groupByDbTagType() Group by the tag_type column
 *
 * @method     CcTagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcTagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcTagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcTagQuery leftJoinCcFileTag($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFileTag relation
 * @method     CcTagQuery rightJoinCcFileTag($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFileTag relation
 * @method     CcTagQuery innerJoinCcFileTag($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFileTag relation
 *
 * @method     CcTagQuery leftJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 * @method     CcTagQuery rightJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 * @method     CcTagQuery innerJoinCcPlayoutHistoryMetaData($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryMetaData relation
 *
 * @method     CcTagQuery leftJoinCcPlayoutHistoryTemplateTag($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplateTag relation
 * @method     CcTagQuery rightJoinCcPlayoutHistoryTemplateTag($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplateTag relation
 * @method     CcTagQuery innerJoinCcPlayoutHistoryTemplateTag($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplateTag relation
 *
 * @method     CcTag findOne(PropelPDO $con = null) Return the first CcTag matching the query
 * @method     CcTag findOneOrCreate(PropelPDO $con = null) Return the first CcTag matching the query, or a new CcTag object populated from the query conditions when no match is found
 *
 * @method     CcTag findOneByDbId(int $id) Return the first CcTag filtered by the id column
 * @method     CcTag findOneByDbTagName(string $tag_name) Return the first CcTag filtered by the tag_name column
 * @method     CcTag findOneByDbTagType(string $tag_type) Return the first CcTag filtered by the tag_type column
 *
 * @method     array findByDbId(int $id) Return CcTag objects filtered by the id column
 * @method     array findByDbTagName(string $tag_name) Return CcTag objects filtered by the tag_name column
 * @method     array findByDbTagType(string $tag_type) Return CcTag objects filtered by the tag_type column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcTagQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcTagQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcTag', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcTagQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcTagQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcTagQuery) {
			return $criteria;
		}
		$query = new CcTagQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key
	 * Use instance pooling to avoid a database query if the object exists
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    CcTag|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcTagPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
			// the object is alredy in the instance pool
			return $obj;
		} else {
			// the object has not been requested yet, or the formatter is not an object formatter
			$criteria = $this->isKeepQuery() ? clone $this : $this;
			$stmt = $criteria
				->filterByPrimaryKey($key)
				->getSelectStatement($con);
			return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
		}
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		return $this
			->filterByPrimaryKeys($keys)
			->find($con);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcTagPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcTagPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcTagPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the tag_name column
	 *
	 * @param     string $dbTagName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByDbTagName($dbTagName = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbTagName)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbTagName)) {
				$dbTagName = str_replace('*', '%', $dbTagName);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTagPeer::TAG_NAME, $dbTagName, $comparison);
	}

	/**
	 * Filter the query on the tag_type column
	 *
	 * @param     string $dbTagType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByDbTagType($dbTagType = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbTagType)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbTagType)) {
				$dbTagType = str_replace('*', '%', $dbTagType);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcTagPeer::TAG_TYPE, $dbTagType, $comparison);
	}

	/**
	 * Filter the query by a related CcFileTag object
	 *
	 * @param     CcFileTag $ccFileTag  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByCcFileTag($ccFileTag, $comparison = null)
	{
		return $this
			->addUsingAlias(CcTagPeer::ID, $ccFileTag->getDbTagId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFileTag relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function joinCcFileTag($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcFileTag');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcFileTag');
		}

		return $this;
	}

	/**
	 * Use the CcFileTag relation CcFileTag object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFileTagQuery A secondary query class using the current class as primary query
	 */
	public function useCcFileTagQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcFileTag($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFileTag', 'CcFileTagQuery');
	}

	/**
	 * Filter the query by a related CcPlayoutHistoryMetaData object
	 *
	 * @param     CcPlayoutHistoryMetaData $ccPlayoutHistoryMetaData  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryMetaData($ccPlayoutHistoryMetaData, $comparison = null)
	{
		return $this
			->addUsingAlias(CcTagPeer::ID, $ccPlayoutHistoryMetaData->getDbTagId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryMetaData relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryMetaData($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistoryMetaData');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcPlayoutHistoryMetaData');
		}

		return $this;
	}

	/**
	 * Use the CcPlayoutHistoryMetaData relation CcPlayoutHistoryMetaData object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryMetaDataQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryMetaDataQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryMetaData($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryMetaData', 'CcPlayoutHistoryMetaDataQuery');
	}

	/**
	 * Filter the query by a related CcPlayoutHistoryTemplateTag object
	 *
	 * @param     CcPlayoutHistoryTemplateTag $ccPlayoutHistoryTemplateTag  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryTemplateTag($ccPlayoutHistoryTemplateTag, $comparison = null)
	{
		return $this
			->addUsingAlias(CcTagPeer::ID, $ccPlayoutHistoryTemplateTag->getDbTagId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplateTag relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryTemplateTag($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplateTag');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'CcPlayoutHistoryTemplateTag');
		}

		return $this;
	}

	/**
	 * Use the CcPlayoutHistoryTemplateTag relation CcPlayoutHistoryTemplateTag object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryTemplateTagQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryTemplateTag($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplateTag', 'CcPlayoutHistoryTemplateTagQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcTag $ccTag Object to remove from the list of results
	 *
	 * @return    CcTagQuery The current query, for fluid interface
	 */
	public function prune($ccTag = null)
	{
		if ($ccTag) {
			$this->addUsingAlias(CcTagPeer::ID, $ccTag->getDbId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcTagQuery
