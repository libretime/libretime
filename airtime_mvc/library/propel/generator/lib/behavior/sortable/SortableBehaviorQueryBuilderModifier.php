<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Behavior to add sortable query methods
 *
 * @author     François Zaninotto
 * @package    propel.generator.behavior.sortable
 */
class SortableBehaviorQueryBuilderModifier
{
    /**
     * @var SortableBehavior
     */
    protected $behavior;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var OMBuilder
     */
    protected $builder;

    /**
     * @var String
     */
    protected $objectClassname;

    /**
     * @var String
     */
    protected $peerClassname;

    public function __construct($behavior)
    {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    protected function getParameter($key)
    {
        return $this->behavior->getParameter($key);
    }

    protected function getColumn($name)
    {
        return $this->behavior->getColumnForParameter($name);
    }

    protected function setBuilder($builder)
    {
        $this->builder = $builder;
        $this->objectClassname = $builder->getStubObjectBuilder()->getClassname();
        $this->queryClassname = $builder->getStubQueryBuilder()->getClassname();
        $this->peerClassname = $builder->getStubPeerBuilder()->getClassname();
    }

    public function queryMethods($builder)
    {
        $this->setBuilder($builder);
        $script = '';

        // select filters
        if ($this->behavior->useScope()) {
            $this->addInList($script);
        }
        if ($this->getParameter('rank_column') != 'rank') {
            $this->addFilterByRank($script);
            $this->addOrderByRank($script);
        }

        // select termination methods
        if ($this->getParameter('rank_column') != 'rank' || $this->behavior->useScope()) {
            $this->addFindOneByRank($script);
        }
        $this->addFindList($script);

        // utilities
        $this->addGetMaxRank($script);
        $this->addGetMaxRankArray($script);
        $this->addReorder($script);

        return $script;
    }

    protected function addInList(&$script)
    {
        list($methodSignature, $paramsDoc, $buildScope) = $this->behavior->generateScopePhp();

        $script .= "
/**
 * Returns the objects in a certain list, from the list scope
 *
$paramsDoc
 *
 * @return {$this->queryClassname} The current query, for fluid interface
 */
public function inList($methodSignature)
{
    $buildScope
    {$this->peerClassname}::sortableApplyScopeCriteria(\$this, \$scope, 'addUsingAlias');

    return \$this;
}
";
    }

    protected function addFilterByRank(&$script)
    {
        $useScope = $this->behavior->useScope();
        $peerClassname = $this->peerClassname;

        if ($useScope) {
            list($methodSignature, $paramsDoc, $buildScope) = $this->behavior->generateScopePhp();
        }

        $script .= "
/**
 * Filter the query based on a rank in the list
 *
 * @param     integer   \$rank rank";
        if ($useScope) {
            $script .= "
$paramsDoc
";
        }
        $script .= "
 *
 * @return    " . $this->queryClassname . " The current query, for fluid interface
 */
public function filterByRank(\$rank" . ($useScope ? ", $methodSignature" : "") . ")
{
";
        if ($useScope) {
            $methodSignature = str_replace(' = null', '', $methodSignature);
        }

        $script .= "

    return \$this";
        if ($useScope) {
            $script .= "
        ->inList($methodSignature)";
        }
        $script .= "
        ->addUsingAlias($peerClassname::RANK_COL, \$rank, Criteria::EQUAL);
}
";
    }

    protected function addOrderByRank(&$script)
    {
        $script .= "
/**
 * Order the query based on the rank in the list.
 * Using the default \$order, returns the item with the lowest rank first
 *
 * @param     string \$order either Criteria::ASC (default) or Criteria::DESC
 *
 * @return    " . $this->queryClassname . " The current query, for fluid interface
 */
public function orderByRank(\$order = Criteria::ASC)
{
    \$order = strtoupper(\$order);
    switch (\$order) {
        case Criteria::ASC:
            return \$this->addAscendingOrderByColumn(\$this->getAliasedColName(" . $this->peerClassname . "::RANK_COL));
            break;
        case Criteria::DESC:
            return \$this->addDescendingOrderByColumn(\$this->getAliasedColName(" . $this->peerClassname . "::RANK_COL));
            break;
        default:
            throw new PropelException('" . $this->queryClassname . "::orderBy() only accepts \"asc\" or \"desc\" as argument');
    }
}
";
    }

    protected function addFindOneByRank(&$script)
    {
        $useScope = $this->behavior->useScope();

        if ($useScope) {
            list($methodSignature, $paramsDoc, $buildScope) = $this->behavior->generateScopePhp();
        }

        $script .= "
/**
 * Get an item from the list based on its rank
 *
 * @param     integer   \$rank rank";
        if ($useScope) {
            $script .= "
$paramsDoc";
        }
        $script .= "
 * @param     PropelPDO \$con optional connection
 *
 * @return    {$this->objectClassname}
 */
public function findOneByRank(\$rank, " . ($useScope ? "$methodSignature, " : "") . "PropelPDO \$con = null)
{";

        if ($useScope) {
            $methodSignature = str_replace(' = null', '', $methodSignature);
        }

        $script .= "

    return \$this
        ->filterByRank(\$rank" . ($useScope ? ", $methodSignature" : "") . ")
        ->findOne(\$con);
}
";
    }

    protected function addFindList(&$script)
    {
        $useScope = $this->behavior->useScope();

        if ($useScope) {
            list($methodSignature, $paramsDoc, $buildScope) = $this->behavior->generateScopePhp();
        }

        $script .= "
/**
 * Returns " . ($useScope ? 'a' : 'the') ." list of objects
 *";
         if ($useScope) {
             $script .= "
$paramsDoc
";
         }
        $script .= "
 * @param      PropelPDO \$con	Connection to use.
 *
 * @return     mixed the list of results, formatted by the current formatter
 */
public function findList(" . ($useScope ? "$methodSignature, " : "") . "\$con = null)
{
";

        if ($useScope) {
            $methodSignature = str_replace(' = null', '', $methodSignature);
        }

        $script .= "

    return \$this";
        if ($useScope) {
            $script .= "
        ->inList($methodSignature)";
        }
        $script .= "
        ->orderByRank()
        ->find(\$con);
}
";
    }

    protected function addGetMaxRank(&$script)
    {
        $this->builder->declareClasses('Propel');
        $useScope = $this->behavior->useScope();

        if ($useScope) {
            list($methodSignature, $paramsDoc, $buildScope) = $this->behavior->generateScopePhp();
        }

        $script .= "
/**
 * Get the highest rank
 * ";
        if ($useScope) {
            $script .= "
$paramsDoc
";
        }
        $script .= "
 * @param     PropelPDO optional connection
 *
 * @return    integer highest position
 */
public function getMaxRank(" . ($useScope ? "$methodSignature, " : "") . "PropelPDO \$con = null)
{
    if (\$con === null) {
        \$con = Propel::getConnection({$this->peerClassname}::DATABASE_NAME);
    }
    // shift the objects with a position lower than the one of object
    \$this->addSelectColumn('MAX(' . {$this->peerClassname}::RANK_COL . ')');";
        if ($useScope) {
        $script .= "
        $buildScope
    {$this->peerClassname}::sortableApplyScopeCriteria(\$this, \$scope);";
        }
        $script .= "
    \$stmt = \$this->doSelect(\$con);

    return \$stmt->fetchColumn();
}
";
    }

    protected function addGetMaxRankArray(&$script)
    {
        $this->builder->declareClasses('Propel');
        $useScope = $this->behavior->useScope();

        $script .= "
/**
 * Get the highest rank by a scope with a array format.
 * ";
        if ($useScope) {
            $script .= "
 * @param     int \$scope		The scope value as scalar type or array(\$value1, ...).
";
        }
        $script .= "
 * @param     PropelPDO optional connection
 *
 * @return    integer highest position
 */
public function getMaxRankArray(" . ($useScope ? "\$scope, " : "") . "PropelPDO \$con = null)
{
    if (\$con === null) {
        \$con = Propel::getConnection({$this->peerClassname}::DATABASE_NAME);
    }
    // shift the objects with a position lower than the one of object
    \$this->addSelectColumn('MAX(' . {$this->peerClassname}::RANK_COL . ')');";
        if ($useScope) {
        $script .= "
    {$this->peerClassname}::sortableApplyScopeCriteria(\$this, \$scope);";
        }
        $script .= "
    \$stmt = \$this->doSelect(\$con);

    return \$stmt->fetchColumn();
}
";
    }

    protected function addReorder(&$script)
    {
        $this->builder->declareClasses('Propel');
        $peerClassname = $this->peerClassname;
        $columnGetter = 'get' . $this->behavior->getColumnForParameter('rank_column')->getPhpName();
        $columnSetter = 'set' . $this->behavior->getColumnForParameter('rank_column')->getPhpName();
        $script .= "
/**
 * Reorder a set of sortable objects based on a list of id/position
 * Beware that there is no check made on the positions passed
 * So incoherent positions will result in an incoherent list
 *
 * @param     array     \$order id => rank pairs
 * @param     PropelPDO \$con   optional connection
 *
 * @return    boolean true if the reordering took place, false if a database problem prevented it
 */
public function reorder(array \$order, PropelPDO \$con = null)
{
    if (\$con === null) {
        \$con = Propel::getConnection($peerClassname::DATABASE_NAME);
    }

    \$con->beginTransaction();
    try {
        \$ids = array_keys(\$order);
        \$objects = \$this->findPks(\$ids, \$con);
        foreach (\$objects as \$object) {
            \$pk = \$object->getPrimaryKey();
            if (\$object->$columnGetter() != \$order[\$pk]) {
                \$object->$columnSetter(\$order[\$pk]);
                \$object->save(\$con);
            }
        }
        \$con->commit();

        return true;
    } catch (Exception \$e) {
        \$con->rollback();
        throw \$e;
    }
}
";
    }

}
