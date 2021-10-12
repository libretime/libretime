<?php


/**
 * Base class that represents a query for the 'cc_playout_history_template_field' table.
 *
 *
 *
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbId($order = Criteria::ASC) Order by the id column
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbTemplateId($order = Criteria::ASC) Order by the template_id column
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbName($order = Criteria::ASC) Order by the name column
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbType($order = Criteria::ASC) Order by the type column
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbIsFileMD($order = Criteria::ASC) Order by the is_file_md column
 * @method     CcPlayoutHistoryTemplateTagQuery orderByDbTagPosition($order = Criteria::ASC) Order by the position column
 *
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbId() Group by the id column
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbTemplateId() Group by the template_id column
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbName() Group by the name column
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbType() Group by the type column
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbIsFileMD() Group by the is_file_md column
 * @method     CcPlayoutHistoryTemplateTagQuery groupByDbTagPosition() Group by the position column
 *
 * @method     CcPlayoutHistoryTemplateTagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateTagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     CcPlayoutHistoryTemplateTagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     CcPlayoutHistoryTemplateTagQuery leftJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a LEFT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method     CcPlayoutHistoryTemplateTagQuery rightJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a RIGHT JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 * @method     CcPlayoutHistoryTemplateTagQuery innerJoinCcPlayoutHistoryTemplate($relationAlias = '') Adds a INNER JOIN clause to the query using the CcPlayoutHistoryTemplate relation
 *
 * @method     CcPlayoutHistoryTemplateTag findOne(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateTag matching the query
 * @method     CcPlayoutHistoryTemplateTag findOneOrCreate(PropelPDO $con = null) Return the first CcPlayoutHistoryTemplateTag matching the query, or a new CcPlayoutHistoryTemplateTag object populated from the query conditions when no match is found
 *
 * @method     CcPlayoutHistoryTemplateTag findOneByDbId(int $id) Return the first CcPlayoutHistoryTemplateTag filtered by the id column
 * @method     CcPlayoutHistoryTemplateTag findOneByDbTemplateId(int $template_id) Return the first CcPlayoutHistoryTemplateTag filtered by the template_id column
 * @method     CcPlayoutHistoryTemplateTag findOneByDbName(string $name) Return the first CcPlayoutHistoryTemplateTag filtered by the name column
 * @method     CcPlayoutHistoryTemplateTag findOneByDbType(string $type) Return the first CcPlayoutHistoryTemplateTag filtered by the type column
 * @method     CcPlayoutHistoryTemplateTag findOneByDbIsFileMD(boolean $is_file_md) Return the first CcPlayoutHistoryTemplateTag filtered by the is_file_md column
 * @method     CcPlayoutHistoryTemplateTag findOneByDbTagPosition(int $position) Return the first CcPlayoutHistoryTemplateTag filtered by the position column
 *
 * @method     array findByDbId(int $id) Return CcPlayoutHistoryTemplateTag objects filtered by the id column
 * @method     array findByDbTemplateId(int $template_id) Return CcPlayoutHistoryTemplateTag objects filtered by the template_id column
 * @method     array findByDbName(string $name) Return CcPlayoutHistoryTemplateTag objects filtered by the name column
 * @method     array findByDbType(string $type) Return CcPlayoutHistoryTemplateTag objects filtered by the type column
 * @method     array findByDbIsFileMD(boolean $is_file_md) Return CcPlayoutHistoryTemplateTag objects filtered by the is_file_md column
 * @method     array findByDbTagPosition(int $position) Return CcPlayoutHistoryTemplateTag objects filtered by the position column
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcPlayoutHistoryTemplateTagQuery extends ModelCriteria
{

	/**
	 * Initializes internal state of BaseCcPlayoutHistoryTemplateTagQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'airtime', $modelName = 'CcPlayoutHistoryTemplateTag', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new CcPlayoutHistoryTemplateTagQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof CcPlayoutHistoryTemplateTagQuery) {
			return $criteria;
		}
		$query = new CcPlayoutHistoryTemplateTagQuery();
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
	 * @return    CcPlayoutHistoryTemplateTag|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ((null !== ($obj = CcPlayoutHistoryTemplateTagPeer::getInstanceFromPool((string) $key))) && $this->getFormatter()->isObjectFormatter()) {
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
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the id column
	 *
	 * @param     int|array $dbId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByDbId($dbId = null, $comparison = null)
	{
		if (is_array($dbId) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::ID, $dbId, $comparison);
	}

	/**
	 * Filter the query on the template_id column
	 *
	 * @param     int|array $dbTemplateId The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByDbTemplateId($dbTemplateId = null, $comparison = null)
	{
		if (is_array($dbTemplateId)) {
			$useMinMax = false;
			if (isset($dbTemplateId['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID, $dbTemplateId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTemplateId['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID, $dbTemplateId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID, $dbTemplateId, $comparison);
	}

	/**
	 * Filter the query on the name column
	 *
	 * @param     string $dbName The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
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
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::NAME, $dbName, $comparison);
	}

	/**
	 * Filter the query on the type column
	 *
	 * @param     string $dbType The value to use as filter.
	 *            Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByDbType($dbType = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($dbType)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $dbType)) {
				$dbType = str_replace('*', '%', $dbType);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::TYPE, $dbType, $comparison);
	}

	/**
	 * Filter the query on the is_file_md column
	 *
	 * @param     boolean|string $dbIsFileMD The value to use as filter.
	 *            Accepts strings ('false', 'off', '-', 'no', 'n', and '0' are false, the rest is true)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByDbIsFileMD($dbIsFileMD = null, $comparison = null)
	{
		if (is_string($dbIsFileMD)) {
			$is_file_md = in_array(strtolower($dbIsFileMD), array('false', 'off', '-', 'no', 'n', '0')) ? false : true;
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::IS_FILE_MD, $dbIsFileMD, $comparison);
	}

	/**
	 * Filter the query on the position column
	 *
	 * @param     int|array $dbTagPosition The value to use as filter.
	 *            Accepts an associative array('min' => $minValue, 'max' => $maxValue)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByDbTagPosition($dbTagPosition = null, $comparison = null)
	{
		if (is_array($dbTagPosition)) {
			$useMinMax = false;
			if (isset($dbTagPosition['min'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::POSITION, $dbTagPosition['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($dbTagPosition['max'])) {
				$this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::POSITION, $dbTagPosition['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::POSITION, $dbTagPosition, $comparison);
	}

	/**
	 * Filter the query by a related CcPlayoutHistoryTemplate object
	 *
	 * @param     CcPlayoutHistoryTemplate $ccPlayoutHistoryTemplate  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function filterByCcPlayoutHistoryTemplate($ccPlayoutHistoryTemplate, $comparison = null)
	{
		return $this
			->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::TEMPLATE_ID, $ccPlayoutHistoryTemplate->getDbId(), $comparison);
	}

	/**
	 * Adds a JOIN clause to the query using the CcPlayoutHistoryTemplate relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function joinCcPlayoutHistoryTemplate($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('CcPlayoutHistoryTemplate');

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
			$this->addJoinObject($join, 'CcPlayoutHistoryTemplate');
		}

		return $this;
	}

	/**
	 * Use the CcPlayoutHistoryTemplate relation CcPlayoutHistoryTemplate object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    CcPlayoutHistoryTemplateQuery A secondary query class using the current class as primary query
	 */
	public function useCcPlayoutHistoryTemplateQuery($relationAlias = '', $joinType = Criteria::INNER_JOIN)
	{
		return $this
			->joinCcPlayoutHistoryTemplate($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'CcPlayoutHistoryTemplate', 'CcPlayoutHistoryTemplateQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     CcPlayoutHistoryTemplateTag $ccPlayoutHistoryTemplateTag Object to remove from the list of results
	 *
	 * @return    CcPlayoutHistoryTemplateTagQuery The current query, for fluid interface
	 */
	public function prune($ccPlayoutHistoryTemplateTag = null)
	{
		if ($ccPlayoutHistoryTemplateTag) {
			$this->addUsingAlias(CcPlayoutHistoryTemplateTagPeer::ID, $ccPlayoutHistoryTemplateTag->getDbId(), Criteria::NOT_EQUAL);
	  }

		return $this;
	}

} // BaseCcPlayoutHistoryTemplateTagQuery
