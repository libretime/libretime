<?php


/**
 * Base class that represents a query for the 'cc_file_tag' table.
 *
 *
 *
 * @method     CcFileTagQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcFileTagQuery orderByDbFileId($order = Criteria::ASC) Order by the file_id column
 * @method     CcFileTagQuery orderByDbTagId($order = Criteria::ASC) Order by the tag_id column
 *
 * @method     CcFileTagQuery groupByDbId() Group by the id column
 * @method     CcFileTagQuery groupByDbFileId() Group by the file_id column
 * @method     CcFileTagQuery groupByDbTagId() Group by the tag_id column
 *
 * @method     CcFileTagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcFileTagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcFileTagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcFileTagQuery leftJoinCcFiles($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcFiles relation
 * @method     CcFileTagQuery rightJoinCcFiles($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcFiles relation
 * @method     CcFileTagQuery innerJoinCcFiles($relationAlias = '') Adds a INNER JOIN clause to the query using the CcFiles relation
 *
 * @method     CcFileTagQuery leftJoinCcTag($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcTag relation
 * @method     CcFileTagQuery rightJoinCcTag($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcTag relation
 * @method     CcFileTagQuery innerJoinCcTag($relationAlias = '') Adds a INNER JOIN clause to the query using the CcTag relation
 *
 * @method     CcFileTag findOne(PropelPDO $con = null) Return the first CcFileTag matching the query
 * @method     CcFileTag findOneOrCreate(PropelPDO $con = null) Return the first CcFileTag matching the query, or a new CcFileTag object populated from the query conditions when no match is found
 *
 * @method     CcFileTag findOneByDbId(int $id) Return the first CcFileTag filtered by the id column
 * @method     CcFileTag findOneByDbFileId(int $file_id) Return the first CcFileTag filtered by the file_id column
 * @method     CcFileTag findOneByDbTagId(int $tag_id) Return the first CcFileTag filtered by the tag_id column
 *
 * @method     array findByDbId(int $id) Return CcFileTag objects filtered by the id column
 * @method     array findByDbFileId(int $file_id) Return CcFileTag objects filtered by the file_id column
 * @method     array findByDbTagId(int $tag_id) Return CcFileTag objects filtered by the tag_id column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcFileTagQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcFileTagQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcFileTag', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcFileTagQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcFileTagQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcFileTagQuery) {
			return $criteria;
		}
		$query = new CcFileTagQuery();
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
	 * @return    CcFileTag|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcFileTagPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcFileTagPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcFileTagPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcFileTagPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the file_id column
	 *
	 * @param     int|array $dbFileId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByDbFileId($dbFileId = null, $comparison = null)
	{
		if (is_array($dbFileId)) {
			$useMinMax = false;
			if (isset($dbFileId['min'])) {
				$this->addUsingAlias(CcFileTagPeer::FILE_ID, $dbFileId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbFileId['max'])) {
				$this->addUsingAlias(CcFileTagPeer::FILE_ID, $dbFileId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFileTagPeer::FILE_ID, $dbFileId, $comparison);
	}

	/**
	 * Filter the query on the tag_id column
	 *
	 * @param     int|array $dbTagId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByDbTagId($dbTagId = null, $comparison = null)
	{
		if (is_array($dbTagId)) {
			$useMinMax = false;
			if (isset($dbTagId['min'])) {
				$this->addUsingAlias(CcFileTagPeer::TAG_ID, $dbTagId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTagId['max'])) {
				$this->addUsingAlias(CcFileTagPeer::TAG_ID, $dbTagId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcFileTagPeer::TAG_ID, $dbTagId, $comparison);
	}

	/**
	 * Filter the query by a related CcFiles object
	 *
	 * @param     CcFiles $ccFiles  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByCcFiles($ccFiles, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFileTagPeer::FILE_ID, $ccFiles->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcFiles relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function joinCcFiles($relationAlias = '', $joinType = Criteria::INNER_JOIN)
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
		if($relationAlias) {
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
	 * @return    CcFilesQuery A secondary query class using the current class as primary query
	 */
	public function useCcFilesQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcFiles($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcFiles', 'CcFilesQuery');
	}

	/**
	 * Filter the query by a related CcTag object
	 *
	 * @param     CcTag $ccTag  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function filterByCcTag($ccTag, $comparison = null)
	{
		return $this
			->addUsingAlias(CcFileTagPeer::TAG_ID, $ccTag->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcTag relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function joinCcTag($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcTag');

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
			$this->addJoinObject($join, 'CcTag');
		}

		return $this;
	}

	/**
	 * Use the CcTag relation CcTag object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcTagQuery A secondary query class using the current class as primary query
	 */
	public function useCcTagQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcTag($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcTag', 'CcTagQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcFileTag $ccFileTag Object to remove from the list of results
	 *
	 * @return    CcFileTagQuery The current query, for fluid interface
	 */
	public function prune($ccFileTag = null)
	{
		if ($ccFileTag) {
			$this->addUsingAlias(CcFileTagPeer::ID, $ccFileTag->getDbId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcFileTagQuery
